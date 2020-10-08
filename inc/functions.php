<?php

include_once 'GoogleAuthenticator.php';
include_once 'kv.php';

class Functions {

  /**
   * Grabs Database Instance
   * @return PDO
   */
  public static function GetDB() : PDO {
    $Pdo = new PDO("mysql:host=localhost;dbname=tampered", "TamperedLive", "<redacted>");
    $Pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $Pdo;
  }

  /*
   * Starts Secure Session
   */
  public static function SecStart(){
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../403.php");
        exit();
    }
    $Cookie = session_get_cookie_params();
    session_set_cookie_params($Cookie["lifetime"], $Cookie["path"], $Cookie["domain"], FALSE, TRUE);
    session_name("TamperedLive");
    session_start();
    session_regenerate_id();
  }

  /**
   * Checks Email and Password for User and Starts Secure Login
   * @param $Email
   * @param $Password
   * @return bool
   */
  public static function Login(string $Email, string $Password, string $TFA, bool $Remember) : bool {
    if (Functions::CheckIPBan()){
      return false;
    }
    //get pdo instance
    $Pdo = Functions::GetDB();

    //prepare statement
    $S_Email = $Pdo->quote($Email);
    $Stmt = $Pdo->query("SELECT * FROM `logins` WHERE `email` = $S_Email LIMIT 1");

    if($Stmt->rowCount() != 1) {
      Functions::LogToDB("login", json_encode(array('email'=>$Email, 'ip'=>Functions::GetIP(), 'error'=>'bad_email')));
      return false;
    }
      
    //fetch result
    $Result = $Stmt->fetch();
    
    //check enabled
    if($Result['enabled'] != 1){
      Functions::LogToDB("login", json_encode(array('id'=>$Result['id'], 'ip'=>Functions::GetIP(), 'error'=>'not_registered')));
      Functions::Mail($Email, 'TamperedLive Registration', '<h1>You have not finished registering!</h1><br>Please visit <a href="http://<redacted>/twofactor.php?a='.hash('sha1', $Result['secret'].$Result['username']).'">this link</a> to complete your registration!</b>');
      return false;
    }
      
    //then check bruting
    if (Functions::CheckBrute($Result['id']) == true) {
      Functions::LogToDB("login", json_encode(array('id'=>$Result['id'], 'ip'=>Functions::GetIP(), 'error'=>'brute')));
      $Reset = $Pdo->quote(sprintf('%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535)));
      $ID = $Pdo->quote($Result['id']);
      $Pdo->exec("UPDATE `logins` SET `reset_code`= $Reset WHERE `id` = $ID");
      Functions::Mail($Email, 'TamperedLive Bruteforce', '<h1>Someone was trying to get into your account!</h1><br>Below is a reset link to reset bruteforce attempts<br><b>Please click <form method="post" action = "http://<redacted>/inc/handler.php"><input hidden name="func" value="reset"/><input hidden name="u" value="'.$ID.'"/><input hidden name="r" value="'.$Reset.'"><a type="submit">HERE</a></form> to complete this action!</b>');
      return false;
    }
      
    $ga = new GoogleAuthenticator();
    if(!$ga->verifyCode($Result['secret'], $TFA, 2)){
      //serialize
      $Now = $Pdo->quote(time());
      $IP = $Pdo->quote(Functions::GetIP());
      $ID = $Pdo->quote($Result['id']);
      //execute
      $Pdo->exec("INSERT INTO login_attempts(`uid`, `ip`, `time`, `type`) VALUES ($ID, $IP, $Now, '2fa')");
      Functions::LogToDB("login", json_encode(array('id'=>$Result['id'], 'ip'=>Functions::GetIP(), 'error'=>'2fa')));
      return false; //"2FA Failed"
    }
    
    //hash password and salt
    $Password = hash('sha512', $Password . $Result['salt']);
    if ($Result['password'] != $Password) {
      //serialize
      $Now = $Pdo->quote(time());
      $IP = $Pdo->quote(Functions::GetIP());
      $ID = $Pdo->quote($Result['id']);
      //execute
      $Pdo->exec("INSERT INTO login_attempts(`uid`, `ip`, `time`, `type`) VALUES ($ID, $IP, $Now, 'password')");
      Functions::LogToDB("login", json_encode(array('id'=>$Result['id'], 'ip'=>Functions::GetIP(), 'error'=>'bad_pass')));
      return false; //"The specified credentials are invalid. Please try again."
    }
    
    //define time of hash
    $CookieTime = time();
    $CookieExpire = $CookieTime+604800;
    
    //set session headers
    $_SESSION['id'] = preg_replace("/[^0-9]+/", "", $Result['id']);
    $_SESSION['name'] = preg_replace("/[^ \w]+/", "", $Result['username']);
    $_SESSION['data'] = hash('sha512', $Password . $_SERVER['HTTP_USER_AGENT'] . $CookieTime);
    
    //hash for cookie
    $CookieHash = substr(hash('sha384', $_SESSION['data'] . $_SERVER['HTTP_USER_AGENT'] . $CookieTime), 0, 20)."-".($Result['id']+13);
    
    //set cookie
    if($Remember) setcookie("GFq4EuyLG5Prme5g", $CookieHash, $CookieExpire, "/");
            
    //serialize variables
    $IP = $Pdo->quote(Functions::GetIP());
    $ID = $Pdo->quote($Result['id']);
    $CT = $Pdo->quote($CookieTime);
    $CE = $Pdo->quote($CookieExpire);
    
    //execute
    $Pdo->exec("UPDATE `logins` SET `ip`= $IP, `reset_code` = '', `login_time` = $CT, `cookie_expire` = $CE WHERE `id` = $ID");
    Functions::LogToDB("login", json_encode(array('id'=>$Result['id'], 'ip'=>Functions::GetIP(), 'error'=>'login')));
    return true; //"success"
  }
  
  public static function Get2FASecret(string $Hash) : string {
    $Pdo = Functions::GetDB();
    $Stmt = $Pdo->query("SELECT `username`, `secret`, `id` FROM `logins` WHERE 1");
    if ($Stmt->rowCount() > 0) {
      $Results = $Stmt->fetchAll();
      $Arr = array();
      //loop all logins
      foreach ($Results as $Result) {
        $ID = $Result['id'];
        $Name = $Result['username'];
        $Secret = $Result['secret'];
        $Hash1 = hash('sha1', $Secret.$Name);
        if($Hash == $Hash1){
          return $Secret."~".$Name."~".$ID;
        }
      }
    }
    return "";
  }

  /**
   * Checks to see if the current user is logged in
   * @return bool
   */
  public static function IsLoggedIn() : bool {
    if (isset($_SESSION['id'], $_SESSION['name'], $_SESSION['data'])) {
      $Pdo = Functions::GetDB();
      $ID = $Pdo->quote($_SESSION['id']);
      $LoginHash = $_SESSION['data'];
      $BrowserAgent = $_SERVER['HTTP_USER_AGENT'];
      $Stmt = $Pdo->query("SELECT `password`, `login_time` FROM `logins` WHERE `id` = $ID LIMIT 1");
      if ($Stmt->rowCount() == 1) {
        $Result = $Stmt->fetch();
        $LoginCheck = hash('sha512', $Result['password'] . $BrowserAgent . $Result['login_time']);
        if ($LoginCheck == $LoginHash) {
          return true;
        }
      }
    }
    if (isset($_COOKIE['GFq4EuyLG5Prme5g'])){
      //cookie is set
      $Pdo = Functions::GetDB();
      //split cookie
      $Cookie = explode('-', $_COOKIE['GFq4EuyLG5Prme5g']);
      //parse values
      $ID = ($Cookie[1]-13);
      $Hash = $Cookie[0];
      //get id
      $ID = $Pdo->quote($ID);
      $Stmt = $Pdo->query("SELECT `password`, `login_time`, `id`, `username`, `cookie_expire` FROM `logins` WHERE `id` = $ID LIMIT 1");
      if ($Stmt->rowCount() == 1) {
        //id exists
        $Result = $Stmt->fetch();
        //check if cookie has already expired
        if(time() > $Result['cookie_expire']){
          //logout and clear session/cookie
          $_SESSION = array();
          $params = session_get_cookie_params();
          setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
          session_destroy();
          setcookie("GFq4EuyLG5Prme5g", "", 1);
          return false;
        }
        //cookie time is valid
        $Prehash = hash('sha512', $Result['password'] . $_SERVER['HTTP_USER_AGENT'] . $Result['login_time']);
        $CookieHash = substr(hash('sha384', $Prehash . $_SERVER['HTTP_USER_AGENT'] . $Result['login_time']), 0, 20);
        //check server hash against clients
        if($CookieHash == $Hash){
          //set a new cookie time
          $CookieTime = time();
          //set session params
          $_SESSION['id'] = preg_replace("/[^0-9]+/", "", $Result['id']);
          $_SESSION['name'] = preg_replace("/[^ \w]+/", "", $Result['username']);
          $_SESSION['data'] = hash('sha512', $Result['password'] . $_SERVER['HTTP_USER_AGENT'] . $CookieTime);
          //update cookie
          $CookieHash = substr(hash('sha384', $_SESSION['data'] . $_SERVER['HTTP_USER_AGENT'] . $CookieTime), 0, 20)."-".($Result['id']+13);
          setcookie("GFq4EuyLG5Prme5g", $CookieHash, $Result['cookie_expire'], "/");
          //update db of new cookie time
          $ID = $Pdo->quote($Result['id']);
          $CookieTime = $Pdo->quote($CookieTime);
          $Pdo->exec("UPDATE `logins` SET `login_time`= $CookieTime WHERE `id` = $ID");
          Functions::LogToDB("login", json_encode(array('id'=>$Result['id'], 'ip'=>Functions::GetIP(), 'error'=>'cookie')));
          return true;
        }
      }
    }
    return false;
  }

  /**
   * Log action to database
   * @param $Type
   * @param $Text
   */
  public static function LogToDB(string $Type, string $Text){
    $Pdo = Functions::GetDB();

    $Time = $Pdo->quote(time());
    $Type = $Pdo->quote($Type);
    $Text = $Pdo->quote($Text);
    $Pdo->exec("INSERT INTO `logs`(`category`, `text`, `time`) VALUES ($Type,$Text,$Time)");
  }

  /**
   * Checks bruteforcing for ID
   * @param $ID
   * @return bool
   */
  public static function CheckBrute($ID) : bool {
    $Pdo = Functions::GetDB();
    $Now = time();
    $Valid = $Pdo->quote($Now - (60 * 60)); //1 hour
    $ID = $Pdo->quote($ID);
    $Stmt = $Pdo->query("SELECT `time` FROM `login_attempts` WHERE `id` = $ID AND `time` > $Valid");
    if($Stmt->rowCount() >= 5) { //5 failed logins
      return true;
    } else {
      return false;
    }
  }

  /**
   * Checks if users ip is banned
   * @return bool
   */
  public static function CheckIPBan() : bool {
    $ips = array("Invalid"=>"0.0.0.0");
    foreach($ips as $Name => $IP){
      if (Functions::GetIP() == $IP){
        Functions::LogToDB("ipban", json_encode(array('ip'=>Functions::GetIP())));
        return true;
      }
    }
    return false;
  }

  /**
   * Gets current users IP
   * @return string
   */
  public static function GetIP() : string {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  /**
   * Resets Users Stored IP and Login Attempts
   * @param $ID
   * @param $Reset
   * @return bool
   */
  public static function ResetUser(string $Reset) : bool {
    //get db
    $Pdo = Functions::GetDB();
    //serialize
    $Reset = $Pdo->quote($Reset);
    //query
    $Stmt = $Pdo->query("SELECT `id` FROM `logins` WHERE `reset_code` = $Reset LIMIT 1");
    //check count
    if($Stmt->rowCount() != 1){
      Functions::LogToDB("login", json_encode(array('code'=>$Reset, 'ip'=>Functions::GetIP(), 'error'=>'reset_bad_code')));
      return true; //"The specified credentials are invalid. Please try again."
    }
    $Result = $Stmt->fetch();
    $ID = $Pdo->quote($Result['id']);
    //all good
    $Pdo->exec("UPDATE `logins` SET `reset_code` = '' WHERE `id` = $ID");
    $Pdo->exec("DELETE FROM `login_attempts` WHERE `id` = $ID");
    Functions::LogToDB("login", json_encode(array('id'=>$Result['id'], 'ip'=>Functions::GetIP(), 'error'=>'reset_success')));
    return false; //"success"
  }

  /**
   * Sends mail to the specified Email using Linux Send Mail
   * @param $To
   * @param string $Subject
   * @param string $Message
   */
  public static function Mail(string $To, string $Subject = '(No subject)', string $Message = ''){
    $Headers   = array();
    $Headers[] = "MIME-Version: 1.0";
    $Headers[] = "Content-type: text/html; charset=iso-8859-1";
    $Headers[] = "From: TamperedLive <mailer@<redacted>>";
    $Headers[] = "Reply-To: TamperedLive <mailer@<redacted>>";
    $Headers[] = "Subject: ".$Subject;
    $Headers[] = "X-Mailer: PHP/".phpversion();
    mail($To, $Subject, $Message, implode("\r\n", $Headers));
  }

  /**
   * Escapes URL to return current file
   * @param $url
   * @return string
   */
  public static function EscapeUrl(string $url) : string {
    if ('' == $url) {
      return $url;
    }
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
    $strip = array(
        '%0d',
        '%0a',
        '%0D',
        '%0A'
    );
    $url   = (string) $url;
    $count = 1;
    while ($count) {
      $url = str_replace($strip, '', $url, $count);
    }
    $url = str_replace(';//', '://', $url);
    $url = htmlentities($url);
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
    if ($url[0] !== '/') {
      return '';
    } else {
      return $url;
    }
  }

  /**
   * Gets current logged-in users level
   * @return int
   */
  public static function GetLoginLevel() : int {
    if(isset($_SESSION['id'])){
      $Pdo = Functions::GetDB();
      $ID =  $Pdo->quote($_SESSION['id']);
      $Stmt = $Pdo->query("SELECT `level` FROM `logins` WHERE `id` = $ID LIMIT 1");
      if($Stmt->rowCount() == 1){
        $Result = $Stmt->fetch();
        return $Result['level'];
      } else {
        return -1; //no user exists
      }
    }
    //no login
    return -1;
  }
  
  /**
   * Gets current avatar of logged in user
   * @return string
   */
  public static function GetAvatar() : string {
    if(isset($_SESSION['id'])){
      $Pdo = Functions::GetDB();
      $ID =  $Pdo->quote($_SESSION['id']);
      $Stmt = $Pdo->query("SELECT `avatar` FROM `logins` WHERE `id` = $ID LIMIT 1");
      if($Stmt->rowCount() == 1){
        $Result = $Stmt->fetch();
        return $Result['avatar'];
      } else {
        return "img/default-avatar.png"; //no user exists
      }
    }
    //no login
    return "img/default-avatar.png";
  }

  /**
   * Gets the overall count of clients in the database
   * @return int
   */
  public static function GetOverallClientCount() : int {
    $Pdo = Functions::GetDB();
    $Stmt = $Pdo->query("SELECT max(`id`) AS 'max' FROM `clients`");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return $Result['max'];
    } else {
      return 0;
    }
  }

  /**
   * Gets the current number of clients in the database
   * @return int
   */
  public static function GetCurrentClientCount(string $Query = "") : int {
    $Pdo = Functions::GetDB();
    $Stmt = $Pdo->query("SELECT count(*) AS 'count' FROM `clients` $Query");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return $Result['count'];
    } else {
      return 0;
    }
  }

  /**
   * Gets the current number of games in the database
   * @return int
   */
  public static function GetGameCount() : int {
    $Pdo = Functions::GetDB();
    $Stmt = $Pdo->query("SELECT count(*) AS 'count' FROM `titleids`");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return $Result['count'];
    } else {
      return 0;
    }
  }

  public static function GetTokenCount(string $Query) : int {
    $Pdo = Functions::GetDB();
    $Stmt = $Pdo->query("SELECT count(*) AS 'count' FROM `tokens` WHERE $Query");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return $Result['count'];
    } else {
      return 0;
    }
  }


  public static function GetOption(string $Option) : string {
    $Pdo = Functions::GetDB();
    $Option = $Pdo->quote($Option);
    $Stmt = $Pdo->query("SELECT `value` FROM `options` WHERE `object` = $Option LIMIT 1");
    if ($Stmt->rowCount() == 1) {
      $Result = $Stmt->fetch();
      return $Result['value'];
    }
    return "";
  }
  
  public static function GetSortClient(string $CPUKey, string $Name, string $Gamertag, string $IP, int $Sort, string $Version) : string {
    $where = "";
    $order = "ORDER BY CASE WHEN `version` > '".$Version."' THEN 1 WHEN `version` = '".$Version."' THEN 2 ELSE 3 END ASC, `cpukey` ASC";
    if ($CPUKey != null && strlen($CPUKey) > 0){
      $where .= ((strlen($where) > 0) ? " AND `cpukey` LIKE '%$CPUKey%'" : "`cpukey` LIKE '%$CPUKey%'");
    }
    if ($Name != null && strlen($Name) > 0){
      $where .= ((strlen($where) > 0) ? " AND `name` LIKE '%$Name%'" : "`name` LIKE '%$Name%'");
    }
    if ($IP != null && strlen($IP) > 0){
      $where .= ((strlen($where) > 0) ? " AND `ip` LIKE '%$IP%'" : "`ip` LIKE '%$IP%'");
    }
    if ($Gamertag != null && strlen($Gamertag) > 0){
      $where .= ((strlen($where) > 0) ? " AND `gamertag` LIKE '%$Gamertag%'" : "`gamertag` LIKE '%$Gamertag%'");
    }
    if ($Sort == 1){
      $where .= ((strlen($where) > 0) ? " AND `blacklisted` = 1" : "`blacklisted` = 1");
    }
    else if ($Sort == 2){
      $where .= ((strlen($where) > 0) ? " AND `developer` = 1" : "`developer` = 1");
    }
    else if ($Sort == 3){
      $where .= ((strlen($where) > 0) ? " AND `lifetime` = 1" : "`lifetime` = 1");
    }
    else if ($Sort == 4){
      $where .= ((strlen($where) > 0) ? " AND `fails` > 0" : "`fails` > 0");
      $order = "ORDER BY `fails` DESC, CASE WHEN `version` > '".$Version."' THEN 1 WHEN `version` = '".$Version."' THEN 2 ELSE 3 END ASC, `cpukey` ASC";
    } 
    else if ($Sort == 5){
      $where = "`remaining` > '".time()."' AND `lifetime` != 1";
      $order = "ORDER BY `remaining` DESC, `cpukey` ASC";
    }
    $where = ((strlen($where) > 0) ? "WHERE ".$where : $where);
    return $where." ".$order;
  }

  /*public static function BuildClientQuery(string $CPUKey, string $Name, string $IP) : string {
    $Query = "";
    if ($CPUKey != null && strlen($CPUKey) > 0){
      $Query .= "`cpukey` LIKE '%$CPUKey%'";
    }
    if ($Name != null && strlen($Name) > 0){
      $Query .= ((strlen($Query) > 0) ? " AND `name` LIKE '%$Name%'" : "`name` LIKE '%$Name%'");
    }
    if ($IP != null && strlen($IP) > 0){
      $Query .= ((strlen($Query) > 0) ? " AND `ip` LIKE '%$IP%'" : "`ip` LIKE '%$IP%'");
    }
    return ((strlen($Query) > 0) ? "WHERE ".$Query : $Query);
  }
  
  public static function GetSort(int $Sort) : string {
    if($Sort == 0){
      return "0";
    } 
    else if ($Sort == 1){
      return "`blacklisted` = 1";
    }
    else if ($Sort == 2){
      return "`developer` = 1";
    }
    else if ($Sort == 3){
      return "`lifetime` = 1";
    }
    else if ($Sort == 4){
      return "`fails` > 0";
    }
    else {
      return "0";
    }
  }*/

  public static function GetClients(int $Page, string $CPUKey = "", string $Name = "", string $Gamertag = "", string $IP = "", int $Sort = 0) : string {
    $Pdo = Functions::GetDB();
    $PageStart = $Page * 15;
    $Version = Functions::GetOption("version");
    $Query = Functions::GetSortClient($CPUKey, $Name, $Gamertag, $IP, $Sort, $Version);
    $Stmt = $Pdo->query("SELECT * FROM `clients` $Query LIMIT $PageStart,15");
    if ($Stmt->rowCount() > 0) {
      $Results = $Stmt->fetchAll();
      $Arr = array();
      //get this before looping
      $Index = ($Page*15)+1;
      foreach ($Results as $Result) {
        if(Functions::GetLoginLevel() >= 2){
          //blacklist
          $CCPUKey = '<span class="cpu'. (($Result['blacklisted'] == 1) ? 'bad' : 'good').'">'.$Result['cpukey'].'</span>';
          //version
          $CVersion = '<span class="version'. (($Result['version'] == $Version) ? 'good' : (($Result['version'] > $Version) ? "dev" : "bad")).'">'.$Result['version'].'</span>';
          //dev name
          $CName = (($Result['developer'] == 1) ? '<span class="dev-name">'.$Result['name'].'</span>' : (($Result['lifetime'] == 1) ? '<span class="unlimited-access">'.$Result['name'].'</span>' : $Result['name']));
          //time remaining
          $date1 = new DateTime();
          $date1->setTimestamp($Result['remaining']);
          $FExpire = (($Result['lifetime'] == 1) ? "<span class='lifetime'>Unlimited Access</span>" : (($Result['remaining'] > time()) ? $date1->format("M j, Y @ g:i a") : "<span class='expired'>Expired</span>"));
          //fails
          $Fails = (($Result['fails'] > 0) ? '<span class="fails">'.$Result['fails'].'</span>' : $Result['fails']);
          //fill array
          $Arr[] = array('index'=>$Index,
              'cpukey'=>$CCPUKey,
              'version'=>$CVersion,
              'cname'=>$CName,
              'fails'=>$Fails,
              'fexpire'=>$FExpire,
              'id'=>$Result['id']);
        } else if (Functions::GetLoginLevel() >= 1) {
          //blacklist
          $CCPUKey = '<span class="cpu'. (($Result['blacklisted'] == 1) ? 'bad' : 'good').'">'.$Result['cpukey'].'</span>';
          //version
          $CVersion = '<span class="version'. (($Result['version'] == $Version) ? 'good' : (($Result['version'] > $Version) ? "dev" : "bad")).'">'.$Result['version'].'</span>';
          //dev name
          $CName = (($Result['developer'] == 1) ? '<span class="dev-awesomeness">'.$Result['name'].'</span>' : (($Result['lifetime'] == 1) ? '<span class="unlimited-access">'.$Result['name'].'</span>' : $Result['name']));
          //time remaining
          $date1 = new DateTime();
          $date1->setTimestamp($Result['remaining']);
          $FExpire = (($Result['remaining'] > time()) ? $date1->format("M j, Y @ g:i a") : "Expired");
          //fill array
          $Arr[] = array('index'=>$Index,
              'cpukey'=>$CCPUKey,
              'version'=>$CVersion,
              'cname'=>$Result['name'],
              'fexpire'=>$FExpire);
        }
        $Index++;
      }
      $Next = -1;
      $NumClients = Functions::GetCurrentClientCount($Query);
      if (($Page * 15) + 15 < $NumClients){
        $Next = $Page+1;
      }
      return json_encode(array('p'=>$Page-1,'n'=>$Next,'c'=>ceil($NumClients/15),'data'=>$Arr));
    }
    return json_encode(array('p'=>-1,'n'=>-1,'c'=>1,'s'=>"No Clients in DB."));
  }

  public static function GetHashTableHeader() : string {
    return '(<span class="hashheader">'. ((Functions::GetOption("xexhash") != "0") ? Functions::GetOption("xexhash") : 'Protection Disabled') .'</span>)';
  }
  
  public static function GetSortToken(int $Sort, string $UName, string $CPUKey, string $Token, string $GenBy, string $Buyer) : string {
    //define stuff
    $where = "";
    $order = "ORDER BY `redeemed` ASC, `generated_date` ASC";
    //do sortint
    if($Sort == 0){
      $where = "`display` = 1 && `generated_by` NOT LIKE '%Auto-buy%'"; //everyone
    } 
    else if ($Sort == 1){
      $where = "`display` = 1 && `generated_by` = ".$UName; //just mine
    }
    else if ($Sort == 2){
      $where = "`display` = 1 && (`generated_by` LIKE '%Auto-buy%' OR `whofor` LIKE '%Auto-buy%')"; //auto
      $order = "ORDER BY `redeemed` ASC, `days` ASC, `reserve_days` ASC, `generated_date` ASC";
    }
    else if ($Sort == 3){
      $where = "`display` = 0"; //hidden
      $order = "ORDER BY `redeemed` ASC, `generated_date` ASC";
    }
    else if($Sort == 4){
      $where = "1";
    }
    else if($Sort == 5){
      $where = "`enabled` = 0";
    }
    else {
      $where = "`display` = 1";
    }
    if ($CPUKey != null && strlen($CPUKey) > 0){
      $where .= " AND `redeemed_by` LIKE '%$CPUKey%'";
    }
    if ($Token != null && strlen($Token) > 0){
      $where .= " AND `token` LIKE '%$Token%'";
    }
    if ($GenBy != null && strlen($GenBy) > 0){
      $where .= " AND `generated_by` LIKE '%$GenBy%'";
    }
    if ($Buyer != null && strlen($Buyer) > 0){
      $where .= " AND `whofor` LIKE '%$Buyer%'";
    }
    return $where." ".$order;
  }

  public static function GetTokens(int $Page, int $Sort = 1, string $CPUKey = "", string $Token = "", string $GenBy = "", string $Buyer = "") : string {
    $Pdo = Functions::GetDB();
    $PageStart = $Page * 15;
    $UName = $Pdo->quote(Functions::GetUsername($_SESSION['id']));
    $Sort = ((Functions::GetLoginLevel() == 1) ? 1 : $Sort);
    $Query = Functions::GetSortToken($Sort, $UName, $CPUKey, $Token, $GenBy, $Buyer);
    $Stmt = $Pdo->query("SELECT * FROM `tokens` WHERE $Query LIMIT $PageStart,15");
    if($Stmt->rowCount() > 0){
      $Results = $Stmt->fetchAll();
      $Arr = array();
      $Index = ($Page*15)+1;
      foreach($Results as $Result){
        $TEnabled = '<span class="token'.(($Result['enabled'] == 0) ? ((Functions::GetLoginLevel() >= 2) ? 'used"><a href="javascript:void(0)" style="color:red;" onclick="approve('.$Result['id'].')">Unapproved</a>' : 'used">Unapproved') : 'good">Approved').'</span>';
        $TStatus = '<span class="token'.(($Result['redeemed'] == 1) ? 'used">Used' : 'good">Unused').'</span>';
        $date1 = new DateTime();
        $date1->setTimestamp($Result['generated_date']);
        $TGen = $date1->format("m/d/y g:i a");
        $TRed = "";
        if($Result['redeemed_date'] > 0){
          $date1 = new DateTime();
          $date1->setTimestamp($Result['redeemed_date']);
          $TRed = $date1->format("m/d/y g:i a");
        }
        $Days = (($Result['days'] >= 0) ? $Result['days']." Days " : "").(($Result['reserve_days'] > 0) ? $Result['reserve_days']." Reserve" : "");
        $Generated = $Result['generated_by']." - ".$TGen;
        $Redeemed = (($Result['redeemed_by'] == "") ? "" : explode(" - ", $Result['redeemed_by'])[1]." - ".$TRed);
        $Buyer = (($Result['whofor'] == "") ? "" : $Result['whofor']);
        $Buyer .= (($Result['paid'] == "") ? "" : (($Buyer == "") ? htmlentities($Result['paid'], ENT_COMPAT | ENT_HTML5, 'ISO-8859-1') : " - ".htmlentities($Result['paid'], ENT_COMPAT | ENT_HTML5, 'ISO-8859-1')));
        $Trial = (($Result['trial'] == 0) ? "<span class='tokenused'>No</span>" : "<span class='tokengood'>Yes</span>");
        $Arr[] = array('index'=>$Index,
            'token'=>$Result['token'],
            'enabled'=>$TEnabled,
            'status'=>$TStatus,
            'days'=>$Days,
            'generated'=>$Generated,
            'redeemed'=>$Redeemed,
            'buyer'=>$Buyer,
            'trial'=>$Trial,
            'id'=>$Result['id']);
        $Index++;
      }
      $Next = -1;
      $NumTokens = Functions::GetTokenCount($Query);
      if (($Page * 15) + 15 < $NumTokens){
        $Next = $Page+1;
      }
      return json_encode(array('p'=>$Page-1,'n'=>$Next,'c'=>ceil($NumTokens/15),'data'=>$Arr));
    }
    return json_encode(array('p'=>-1,'n'=>-1,'c'=>1,'s'=>"No Tokens in DB."));
  }
  
  public static function GetKVCount(string $Query = "") : int {
    $Query = str_replace("ORDER BY `usage` DESC", "", str_replace("GROUP BY `kvserial`", "", $Query));
    $Pdo = Functions::GetDB();
    $Stmt = $Pdo->query("SELECT COUNT(DISTINCT `kvserial`) AS `count` FROM `clients` WHERE $Query");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return $Result['count'];
    } else {
      return 0;
    }
  }
  
  public static function GetSortKeyvault(string $CPUKey, string $Serial, int $Sort) : string {
    $where = "`kvserial` != ''";
    $order = "GROUP BY `kvserial` ORDER BY `kvstart` ASC, `kvserial` ASC";
    if ($CPUKey != null && strlen($CPUKey) > 0){
      $where .= ((strlen($where) > 0) ? " AND `cpukey` LIKE '%$CPUKey%'" : "`cpukey` LIKE '%$CPUKey%'");
    }
    if ($Serial != null && strlen($Serial) > 0){
      $where .= ((strlen($where) > 0) ? " AND `kvserial` LIKE '%$Serial%'" : "`kvserial` LIKE '%$Serial%'");
    }
    if ($Sort == 1){
      $order = "GROUP BY `kvserial` ORDER BY `kvserial` ASC";
    }
    else if ($Sort == 2){
      $order = "GROUP BY `kvserial` ORDER BY `usage` DESC";
    }
    else if ($Sort == 3){
      $where .= ((strlen($where) > 0) ? " AND `kvstatus` = 1" : "`kvstatus` = 1");
    }
    else if ($Sort == 4){
      $where .= ((strlen($where) > 0) ? " AND `kvstatus` = 2" : "`kvstatus` = 2");
    }
    return $where." ".$order;
  }
  
  public static function GetKeyvaultUsage(string $KVSerial) : int {
    $Pdo = Functions::GetDB();
    $KVSerial = $Pdo->quote($KVSerial);
    $Stmt = $Pdo->query("SELECT COUNT(*) AS 'count' FROM `clients` WHERE `kvserial` = $KVSerial");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return $Result['count'];
    } else {
      return 0;
    }
  }
  
  public static function GetKeyvaultStart(string $KVSerial) : int {
    $Pdo = Functions::GetDB();
    $KVSerial = $Pdo->quote($KVSerial);
    $Stmt = $Pdo->query("SELECT MIN(`kvstart`) AS `minkvstart` FROM `clients` WHERE `kvserial` = $KVSerial");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return $Result['minkvstart'];
    } else {
      return 0;
    }
  }
  
  public static function GetKeyvaults(int $Page, int $Sort = 0, string $CPUKey = "", string $Serial = "") : string {
    $Pdo = Functions::GetDB();
    $PageStart = $Page * 15;
    $Query = Functions::GetSortKeyvault($CPUKey, $Serial, $Sort);
    $Stmt = $Pdo->query("SELECT *, COUNT(DISTINCT `cpukey`) AS `usage` FROM `clients` WHERE $Query LIMIT $PageStart,15");
    if ($Stmt->rowCount() > 0) {
      $Results = $Stmt->fetchAll();
      $Arr = array();
      //get this before looping
      $Index = ($Page*15)+1;
      foreach ($Results as $Result) {
        //blacklist
        $Serial1 = '<span class="cpugood">'.$Result['kvserial'].'</span>';
        //usage
        $Consoles = '<span class="white">'.$Result['usage']. ' Consoles</span>';
        //start time
        $minstart = Functions::GetKeyvaultStart($Result['kvserial']);
        $date1 = new DateTime();
        $date1->setTimestamp($minstart);
        $StartDate = $date1->format("M j, Y @ g:i a");
        //elapsed
        $tmpmonths = floor((time() - $minstart) / 2592000);
        $tmpdays = floor(((time() - $minstart) % 2592000)/86400);
        $Elapsed = (($tmpmonths > 0) ? $tmpmonths . ' Month(s) ' : '') . (($tmpdays > 0) ? $tmpdays . ' Days(s) ' : '');
        //status
        if($Result['kvstatus'] == 1) $Status = '<span class="hashbad">Banned</span>';
        else if($Result['kvstatus'] == 2) $Status = '<span class="hashgood">Unbanned</span>';
        else if($Result['kvstatus'] == 3) $Status = '<span class="hashbad">Invalid</span>';
        else $Status = '<span class="hashbad">Error</span>';
        //fill array
        $Arr[] = array('index'=>$Index,
          'serial'=>$Serial1,
          'usage'=>$Consoles,
          'sdate'=>$StartDate,
          'elapsed'=>$Elapsed,
          'status'=>$Status,
          'id'=>$Result['id']);
         
        $Index++;
      }
      $Next = -1;
      $NumKeyvaults = Functions::GetKVCount($Query);
      if (($Page * 15) + 15 < $NumKeyvaults){
        $Next = $Page+1;
      }
      return json_encode(array('p'=>$Page-1,'n'=>$Next,'c'=>ceil($NumKeyvaults/15),'data'=>$Arr));
    }
    return json_encode(array('p'=>-1,'n'=>-1,'c'=>1,'s'=>"No Keyvaults in DB."));
  }
  
  public static function ParseClientLog(int $LID, stdClass $Json) : string {
    $Pdo = Functions::GetDB();
    //get user who edited
    $ID = $Pdo->quote($Json->id);
    $Stmt = $Pdo->query("SELECT * FROM `logins` WHERE `id` = $ID LIMIT 1");
    $User = $Stmt->fetch();
    //serialize client id
    $CID = $Pdo->quote($Json->cid);
    //edit client
    if($Json->error == "edit_client"){
      $Stmt = $Pdo->query("SELECT * FROM `clients` WHERE `id` = $CID");
      if ($Stmt->rowCount() != 1) {
        $Stmt = $Pdo->query("SELECT * FROM `clients_restore` WHERE `id` = $CID");
        if ($Stmt->rowCount() != 1) return '<span style="color:#3ddcf7">'.$User['username']."</span> edited unknown client with ID #".$Json->cid.".";
      }
      $Result = $Stmt->fetch();
      $Modal = '<div id="EditClient-'.$LID.'" class="modal-demo">' .
               '<button type="button" class="close" onclick="Custombox.close();">' . 
               '<span>&times;</span><span class="sr-only">Close</span></button>' .
               '<h4 class="custom-modal-title">'.$Result['name'].'\'s Differences</h4>' .
               '<div class="custom-modal-text"><form class="form-horizontal">';
      $Options = $Json->options;
      foreach($Options as $Key => $Value){
        $RowPre = '<div class="row"><div class="col-md-6"><div class="form-group>' .
                  '<label class="control-label">'.$Key.' Before</label>';
        $RowMid = '</div></div><div class="col-md-6"><div class="form-group>' . 
                  '<label class="control-label">'.$Key.' After</label>';
        $RowPost = '</div></div></div>';
        if($Key == "Notes"){
          $ValueOne = '<textarea class="form-control" rows="5">'.$Value[0].'</textarea>';
          $ValueTwo = '<textarea class="form-control" rows="5">'.$Value[1].'</textarea>';  
        } else if ($Key == "Expire Time") {
          $A = new DateTime();
          $A->setTimezone(new DateTimeZone('America/New_York'));
          $A->setTimestamp($Value[0]);
          $B = new DateTime();
          $B->setTimezone(new DateTimeZone('America/New_York'));
          $B->setTimestamp($Value[1]);
          $ValueOne = '<input type="text" class="form-control" value="'.$A->format("m/d/Y h:i A").'">';
          $ValueTwo = '<input type="text" class="form-control" value="'.$B->format("m/d/Y h:i A").'">';
        } else if ($Key == "Developer" || $Key == "Blacklist" || $Key == "Lifetime") {
          $ValueOne = '<input type="text" class="form-control" value="'.(($Value[0] == 1) ? "On" : "Off").'">';
          $ValueTwo = '<input type="text" class="form-control" value="'.(($Value[1] == 1) ? "On" : "Off").'">';
        } else {
          $ValueOne = '<input type="text" class="form-control" value="'.$Value[0].'">';
          $ValueTwo = '<input type="text" class="form-control" value="'.$Value[1].'">';
        }
        $Modal .= $RowPre.$ValueOne.$RowMid.$ValueTwo.$RowPost;
      }
      $Modal .= '</form></div></div>';
      $CPUKey = '<a href="editclient.php?id='.$Result['id'].'"><span class="cpu'. (($Result['blacklisted'] == 1) ? 'bad' : 'good').'">'.$Result['cpukey'].'</span></a>';
      $Message = '<span style="color:#3ddcf7">'.$User['username'].'</span> edited client with CPUKey '.$CPUKey.' and set ' .
                 '<a href="logs.php" onclick="Custombox.open({ target: \'#EditClient-'.$LID.'\', effect: \'push\' }); return false;"><span class="white">values</span></a>.';
      
      return $Message . $Modal;
    }
    //remove client
    else if ($Json->error == "remove_client"){
      $Stmt = $Pdo->query("SELECT * FROM `clients_restore` WHERE `id` = $CID");
      if ($Stmt->rowCount() != 1) return '<span style="color:#3ddcf7">'.$User['username']."</span> deleted unknown client with ID #".$Json->cid.".";
      $Result = $Stmt->fetch();
      $CPUKey = '<span class="cpu'. (($Result['blacklisted'] == 1) ? 'bad' : 'good').'">'.$Result['cpukey'].'</span>';
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> deleted client with CPUKey '.$CPUKey.'.';
    }
    //add client
    else if ($Json->error == "add_client"){
      $Stmt = $Pdo->query("SELECT * FROM `clients` WHERE `id` = $CID");
      if ($Stmt->rowCount() != 1) {
        $Stmt = $Pdo->query("SELECT * FROM `clients_restore` WHERE `id` = $CID");
        if ($Stmt->rowCount() != 1) return '<span style="color:#3ddcf7">'.$User['username']."</span> added unknown client with ID #".$Json->cid.".";
      }
      $Result = $Stmt->fetch();
      $Modal = '<div id="AddClient-'.$LID.'" class="modal-demo">' .
               '<button type="button" class="close" onclick="Custombox.close();">' . 
               '<span>&times;</span><span class="sr-only">Close</span></button>' .
               '<h4 class="custom-modal-title">'.$Result['name'].'\'s Values</h4>' .
               '<div class="custom-modal-text"><form class="form-horizontal">';
      $Options = $Json->options;
      foreach($Options as $Key => $Value){
        $RowPre = '<div class="form-group"><label class="col-sm-3 control-label">'.$Key.'</label><div class="col-sm-9">';
        $RowPost = '</div></div>';
        if($Key == "Notes"){
          $ValueOne = '<textarea class="form-control" rows="5">'.$Value.'</textarea>';
        } else if ($Key == "Expire Time") {
          $A = new DateTime();
          $A->setTimezone(new DateTimeZone('America/New_York'));
          $A->setTimestamp($Value);
          $ValueOne = '<input type="text" class="form-control" value="'.$A->format("m/d/Y h:i A").'">';
        } else if ($Key == "Developer" || $Key == "Blacklist" || $Key == "Lifetime") {
          $ValueOne = '<input type="text" class="form-control" value="'.(($Value == 1) ? "On" : "Off").'">';
        } else {
          $ValueOne = '<input type="text" class="form-control" value="'.$Value.'">';
        }
        $Modal .= $RowPre.$ValueOne.$RowPost;
      }
      $Modal .= '</form></div></div>';
      $CPUKey = '<a href="editclient.php?id='.$Result['id'].'"><span class="cpu'. (($Result['blacklisted'] == 1) ? 'bad' : 'good').'">'.$Result['cpukey'].'</span></a>';
      $Message = '<span style="color:#3ddcf7">'.$User['username'].'</span> add client with CPUKey '.$CPUKey.' and ' .
                 '<a href="logs.php" onclick="Custombox.open({ target: \'#AddClient-'.$LID.'\', effect: \'push\' }); return false;"><span class="white">values</span></a>.';
      
      return $Message . $Modal;
    }
    //unknown
    else {
      error_log('Unknown Action: '.$Json->error.' with CID: '.$Json->cid.PHP_EOL);
      return "";
    }
  }
  
  public static function ParseTokenLog(int $LID, stdClass $Json) : string {
    $Pdo = Functions::GetDB();
    //get user who edited
    if($Json->id < 0) {
      $Username = $Json->user;
    } else {
      $ID = $Pdo->quote($Json->id);
      $Stmt = $Pdo->query("SELECT * FROM `logins` WHERE `id` = $ID LIMIT 1");
      $User = $Stmt->fetch();
      $Username = $User['username'];
    }
    //serialize token id
    $TID = $Pdo->quote(str_replace('"', "", str_replace("'", "", $Json->tid)));
    //edit token
    if($Json->error == "edit_token"){
      $Stmt = $Pdo->query("SELECT * FROM `tokens` WHERE `id` = $TID");
      if ($Stmt->rowCount() != 1) {
        $Stmt = $Pdo->query("SELECT * FROM `tokens_restore` WHERE `id` = $TID");
        if ($Stmt->rowCount() != 1) return '<span style="color:#3ddcf7">'.$Username."</span> edited unknown token with ID #".$Json->tid.".";
      }
      $Result = $Stmt->fetch();
      $Modal = '<div id="EditToken-'.$LID.'" class="modal-demo">' .
               '<button type="button" class="close" onclick="Custombox.close();">' . 
               '<span>&times;</span><span class="sr-only">Close</span></button>' .
               '<h4 class="custom-modal-title">Differences</h4>' .
               '<div class="custom-modal-text"><form class="form-horizontal">';
      $Options = $Json->options;
      foreach($Options as $Key => $Value){
        $RowPre = '<div class="row"><div class="col-md-6"><div class="form-group>' .
                  '<label class="control-label">'.$Key.' Before</label>';
        $RowMid = '</div></div><div class="col-md-6"><div class="form-group>' . 
                  '<label class="control-label">'.$Key.' After</label>';
        $RowPost = '</div></div></div>';
        if ($Key == "Redeemed Date") {
          $A = new DateTime();
          $A->setTimezone(new DateTimeZone('America/New_York'));
          $A->setTimestamp($Value[0]);
          $B = new DateTime();
          $B->setTimezone(new DateTimeZone('America/New_York'));
          $B->setTimestamp($Value[1]);
          $ValueOne = '<input type="text" class="form-control" value="'.$A->format("m/d/Y h:i A").'">';
          $ValueTwo = '<input type="text" class="form-control" value="'.$B->format("m/d/Y h:i A").'">';
        } else if ($Key == "Display" || $Key == "Enabled" || $Key == "Trial" || $Key == "Redeemed") {
          $ValueOne = '<input type="text" class="form-control" value="'.(($Value[0] == 1) ? "Yes" : "No").'">';
          $ValueTwo = '<input type="text" class="form-control" value="'.(($Value[1] == 1) ? "Yes" : "No").'">';
        } else {
          $ValueOne = '<input type="text" class="form-control" value="'.$Value[0].'">';
          $ValueTwo = '<input type="text" class="form-control" value="'.$Value[1].'">';
        }
        $Modal .= $RowPre.$ValueOne.$RowMid.$ValueTwo.$RowPost;
      }
      $Modal .= '</form></div></div>';
      $Token = '<a href="edittoken.php?id='.$Result['id'].'"><span class="tokenblue">'.$Result['token'].'</span></a>';
      $Message = '<span style="color:#3ddcf7">'.$Username.'</span> edited Token '.$Token.' and set ' .
                 '<a href="logs.php" onclick="Custombox.open({ target: \'#EditToken-'.$LID.'\', effect: \'push\' }); return false;"><span class="white">values</span></a>.';
      
      return $Message . $Modal;
    }
    //remove token
    else if ($Json->error == "remove_token"){
      $Stmt = $Pdo->query("SELECT * FROM `tokens_restore` WHERE `id` = $TID");
      if ($Stmt->rowCount() != 1) return '<span style="color:#3ddcf7">'.$Username."</span> deleted unknown token with ID #".$Json->tid.".";
      $Result = $Stmt->fetch();
      return '<span style="color:#3ddcf7">'.$Username.'</span> deleted Token <span class="tokenblue">'.$Result['token'].'</span>.';
    }
    //add token
    else if ($Json->error == "generate_token"){
      $Stmt = $Pdo->query("SELECT * FROM `tokens` WHERE `id` = $TID");
      if ($Stmt->rowCount() != 1) {
        $Stmt = $Pdo->query("SELECT * FROM `tokens_restore` WHERE `id` = $TID");
        if ($Stmt->rowCount() != 1) return '<span style="color:#3ddcf7">'.$Username."</span> generated unknown token with ID #".$Json->tid.".";
      }
      $Result = $Stmt->fetch();
      $Modal = '<div id="AddToken-'.$LID.'" class="modal-demo">' .
               '<button type="button" class="close" onclick="Custombox.close();">' . 
               '<span>&times;</span><span class="sr-only">Close</span></button>' .
               '<h4 class="custom-modal-title">Tokens Values</h4>' .
               '<div class="custom-modal-text"><form class="form-horizontal">';
      $Options = $Json->options;
      foreach($Options as $Key => $Value){
        $RowPre = '<div class="form-group"><label class="col-sm-3 control-label">'.$Key.'</label><div class="col-sm-9">';
        $RowPost = '</div></div>';
        if ($Key == "Enabled" || $Key == "Trial") {
          $ValueOne = '<input type="text" class="form-control" value="'.(($Value == 1) ? "On" : "Off").'">';
        } else {
          $ValueOne = '<input type="text" class="form-control" value="'.$Value.'">';
        }
        $Modal .= $RowPre.$ValueOne.$RowPost;
      }
      $Modal .= '</form></div></div>';
      $Token = '<a href="edittoken.php?id='.$Result['id'].'"><span class="tokenblue">'.$Result['token'].'</span></a>';
      $Message = '<span style="color:#3ddcf7">'.$Username.'</span> generated Token '.$Token.' with ' .
                 '<a href="logs.php" onclick="Custombox.open({ target: \'#AddToken-'.$LID.'\', effect: \'push\' }); return false;"><span class="white">values</span></a>.';
      
      return $Message . $Modal;
    }
    //remove token
    else if ($Json->error == "approve_token"){
     $Stmt = $Pdo->query("SELECT * FROM `tokens` WHERE `id` = $TID");
      if ($Stmt->rowCount() != 1) {
        $Stmt = $Pdo->query("SELECT * FROM `tokens_restore` WHERE `id` = $TID");
        if ($Stmt->rowCount() != 1) return '<span style="color:#3ddcf7">'.$Username."</span> approved unknown token with ID #".$Json->tid.".";
      }
      $Result = $Stmt->fetch();
      $Token = '<a href="edittoken.php?id='.$Result['id'].'"><span class="tokenblue">'.$Result['token'].'</span></a>';
      return '<span style="color:#3ddcf7">'.$Username.'</span> approved Token '.$Token.'.';
    }
    //unknown
    else {
      error_log('Unknown Action: '.$Json->error.' with TID: '.$Json->tid.PHP_EOL);
      return "";
    }
  }
  
  public static function ParseLoginLog(stdClass $Json) : string {
    $Pdo = Functions::GetDB();
    //get user who edited
    if(isset($Json->id)) {
      $ID = $Pdo->quote($Json->id);
      $Stmt = $Pdo->query("SELECT * FROM `logins` WHERE `id` = $ID LIMIT 1");
      if ($Stmt->rowCount() != 1) return "";
      $User = $Stmt->fetch();
    }
    //bad email
    if($Json->error == "bad_email"){
      return 'Someone tried to login with non-existent Email <span class="white">'.$Json->email.'</span>.';
    }
    //not registered
    else if ($Json->error == "not_registered"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> tried to login but hasn\'t finished registering.';
    }
    //brute
    else if ($Json->error == "brute"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span>\'s account has been locked for brute-forcing.';
    }
    //2fa
    else if ($Json->error == "2fa"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> failed two-factor authentication.';
    }
    //bad pass
    else if ($Json->error == "bad_pass"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> tried to login with a bad password.';
    }
    //login
    else if ($Json->error == "login"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> logged in.';
    }
    //cookie
    else if ($Json->error == "cookie"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> logged in using cookie authentication.';
    }
    //reset bad code
    else if ($Json->error == "reset_bad_code"){
      return 'Someone tried to unlock an account with a bad reset code.';
    }
    //reset success
    else if ($Json->error == "reset_success"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> unlocked their account.';
    }
    //register success
    else if ($Json->error == "register_success"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> finished registering.';
    }
    //edit profile
    else if ($Json->error == "edit_profile"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> edited their profile.';
    }
    //change password
    else if ($Json->error == "change_password"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> changed their password.';
    }
    //recovery
    else if ($Json->error == "recovery"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> was sent a recovery email.';
    }
    //unknown
    else {
      error_log('Unknown Action: '.$Json->error.PHP_EOL);
      return "";
    }
  }
  
  public static function ParseOptionLog(stdClass $Json) : string {
    $Pdo = Functions::GetDB();
    $ID = $Pdo->quote($Json->id);
    $Stmt = $Pdo->query("SELECT * FROM `logins` WHERE `id` = $ID LIMIT 1");
    $User = $Stmt->fetch();
    //xex hashing
    if($Json->option == "xexhash"){
      $A = (($Json->values[1] == 1) ? "on" : "off");
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> turned XEX Hashing '.$A.'.';
    }
    //version
    else if($Json->option == "version"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> changed the Version from <span class="white">'.$Json->values[0].'</span> to <span class="white">'.$Json->values[1].'</span>.';
    }
    //free
    else if($Json->option == "free"){
      $A = (($Json->values[1] == 1) ? "on" : "off");
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> turned Free-Mode '.$A.'.';
    }
    //registration
    else if($Json->option == "registration"){
      $A = (($Json->values[1] == 1) ? "enabled" : "disabled");
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> '.$A.' registration.';
    }
    //registration code
    else if($Json->option == "registration_code"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> changed the Registration Code from <span class="white">'.$Json->values[0].'</span> to <span class="white">'.$Json->values[1].'</span>.';
    }
    //generate new code
    else if($Json->option == "generate_new_code"){
      $A = (($Json->values[1] == 1) ? " " : " not ");
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> set registrations to'.$A.'generate a new code.';
    }
    //socketio
    else if($Json->option == "socketio"){
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> changed the SocketIO URL from <span class="white">'.$Json->values[0].'</span> to <span class="white">'.$Json->values[1].'</span>.';
    }
    //xex hashing
    else if($Json->option == "genealogy_hash"){
      $A = (($Json->values[1] == 1) ? "on" : "off");
      return '<span style="color:#3ddcf7">'.$User['username'].'</span> turned Genealogy checking '.$A.'.';
    }
    //unknown
    else {
      error_log('Unknown Option: '.$Json->option.PHP_EOL);
      return "";
    }
  }
  
  public static function ParseIPBanLog(stdClass $Json) : string {
    return "Someone tried to access the dashboard from a banned IP.";
  }
  
  public static function ParseLog(string $Type, int $LID, stdClass $Json) : string {
    if($Type == "clients") return Functions::ParseClientLog($LID, $Json);
    if($Type == "tokens") return Functions::ParseTokenLog($LID, $Json);
    if($Type == "login") return Functions::ParseLoginLog($Json);
    if($Type == "options") return Functions::ParseOptionLog($Json);
    if($Type == "ipban") return Functions::ParseIPBanLog($Json);
  }
  
  public static function GetLogType(string $Type, string $Search) : string {
    $Pdo = Functions::GetDB();
    if($Type == 0) {
      if (strlen($Search) > 0){
        $Client = '';
        $Token = '';
        $Login = '';
        //search client
        $Stmt = $Pdo->query("SELECT `id` FROM `clients` WHERE `cpukey` LIKE '%".$Search."%' LIMIT 1");
        if($Stmt->rowCount() == 1){
          $Result = $Stmt->fetch();
          $Client = $Result['id'];
        } else { //go deeper
          $Stmt = $Pdo->query("SELECT `id` FROM `clients` WHERE `name` LIKE '%".$Search."%' LIMIT 1");
          if($Stmt->rowCount() == 1){
            $Result = $Stmt->fetch();
            $Client = $Result['id'];
          }
        }
        //search token
        $Stmt = $Pdo->query("SELECT `id` FROM `tokens` WHERE `token` LIKE '%".$Search."%' LIMIT 1");
        if($Stmt->rowCount() == 1){
          $Result = $Stmt->fetch();
          $Token = $Result['id'];
        }
        //search login
        $Stmt = $Pdo->query("SELECT `id` FROM `logins` WHERE `username` LIKE '%".$Search."%' LIMIT 1");
        if($Stmt->rowCount() == 1){
          $Result = $Stmt->fetch();
          $Login = $Result['id'];
        }
        //now do final search
        return '`text` LIKE \'%"cid":"'.$Client.'"%\' OR `text` LIKE \'%"tid":"'.$Token.'"%\' OR `text` LIKE \'%"id":"'.$Result['id'].'"%\' OR `text` LIKE \'%"option":"'.$Search.'"%\' OR `text` LIKE \'%"ip":"'.$Search.'"%\'';
      }
      return '1';
    }
    if($Type == 1) {
      if (strlen($Search) > 0){
        $Stmt = $Pdo->query("SELECT `id` FROM `clients` WHERE `cpukey` LIKE '%".$Search."%' LIMIT 1");
        if($Stmt->rowCount() == 1){
          $Result = $Stmt->fetch();
          return '`category` = \'clients\' && `text` LIKE \'%"cid":"'.$Result['id'].'"%\'';
        } else { //go deeper
          $Stmt = $Pdo->query("SELECT `id` FROM `clients` WHERE `name` LIKE '%".$Search."%' LIMIT 1");
          if($Stmt->rowCount() == 1){
            $Result = $Stmt->fetch();
            return '`category` = \'clients\' && `text` LIKE \'%"cid":"'.$Result['id'].'"%\'';
          }
        }
      }
      return '`category` = \'clients\'';
    }
    if($Type == 2) {
      if (strlen($Search) > 0){
        $Stmt = $Pdo->query("SELECT `id` FROM `tokens` WHERE `token` LIKE '%".$Search."%' LIMIT 1");
        if($Stmt->rowCount() == 1){
          $Result = $Stmt->fetch();
          return '`category` = \'tokens\' && `text` LIKE \'%"tid":"'.$Result['id'].'"%\'';
        }
      }
      return '`category` = \'tokens\'';
    }
    if($Type == 3) {
      if (strlen($Search) > 0){
        $Stmt = $Pdo->query("SELECT `id` FROM `logins` WHERE `username` LIKE '%".$Search."%' LIMIT 1");
        if($Stmt->rowCount() == 1){
          $Result = $Stmt->fetch();
          return '`category` = \'login\' && `text` LIKE \'%"id":"'.$Result['id'].'"%\'';
        }
      }
      return '`category` = \'login\'';
    }
    if($Type == 4){
      if (strlen($Search) > 0){
        return '`category` = \'options\' && `text` LIKE \'%"option":"'.$Search.'"%\'';
      }
      return '`category` = \'options\'';
    }
    if($Type == 5) {
      if (strlen($Search) > 0){
        return '`category` = \'ipban\' && `text` LIKE \'%"ip":"'.$Search.'"%\'';
      }
      return '`category` = \'ipban\'';
    }
    return '1';
  }

  public static function GetLogCount(string $Query) : int {
    $Pdo = Functions::GetDB();
    $Stmt = $Pdo->query("SELECT count(*) AS 'count' FROM `logs` WHERE ".$Query);
    if ($Stmt->rowCount() > 0) {
      $Result = $Stmt->fetch();
      return $Result['count'];
    }
    return 0;
  }

  public static function GetLogs(int $Page, int $Type, string $Search = "") : string {
    $Pdo = Functions::GetDB();
    $PageStart = $Page*15;
    $Query = Functions::GetLogType($Type, $Search);
    $Stmt = $Pdo->query("SELECT * FROM `logs` WHERE ".$Query." ORDER BY `time` DESC LIMIT $PageStart,15");
    if($Stmt->rowCount() > 0){
      $Results = $Stmt->fetchAll();
      $Arr = array();
      $Index = ($Page*15)+1;
      foreach($Results as $Result){
        $Json = json_decode($Result['text']);
        $Formatted = Functions::ParseLog($Result['category'], $Result['id'], $Json);
        if(strlen($Formatted) < 1) continue;
        $A = new DateTime();
        $A->setTimezone(new DateTimeZone('America/New_York'));
        $A->setTimestamp($Result['time']);
        $Arr[] = array('index'=>$Index, 'message'=>$Formatted, 'time'=>$A->format("m/d/Y h:i A"), 'ip'=>$Json->ip);
        $Index++;
      }
      $Next = -1;
      $NumLogs = Functions::GetLogCount($Query);
      if (($Page * 15) + 15 < $NumLogs){
        $Next = $Page+1;
      }
      return json_encode(array('p'=>$Page-1,'n'=>$Next,'c'=>ceil($NumLogs/15),'data'=>$Arr));
    }
    return json_encode(array('p'=>-1,'n'=>-1,'c'=>1,'s'=>"No logs of that type in DB."));
  }

  public static function GUID() : string {
    return sprintf('%04X-%04X-%04X', mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151));
  }

  public static function GenerateToken(int $Days, int $RDays, string $Name, string $Paid, bool $Enabled, string $User, int $Trial = 0) : string {
    $Pdo = Functions::GetDB();
    while(true){ //loop until valid token
      $Token = Functions::GUID();
      $S_Token = $Pdo->quote($Token);
      $S_Days = $Pdo->quote($Days);
      $S_RDays = $Pdo->quote($RDays);
      $S_Name = $Pdo->quote($Name);
      $S_Paid = $Pdo->quote($Paid);
      $S_User = $Pdo->quote($User);
      $S_Trial = $Pdo->quote($Trial);
      $S_Enabled = $Pdo->quote((($Enabled) ? 1 : 0));
      $Stmt = $Pdo->query("SELECT * FROM `tokens` WHERE `token` = $S_Token LIMIT 1");
      if ($Stmt->rowCount() == 0){ //token is valid. insert directly into vagina
        $S_Time = $Pdo->quote(time());
        if($Pdo->exec("INSERT INTO `tokens`(`token`, `enabled`, `days`, `reserve_days`, `generated_by`, `generated_date`, `whofor`, `paid`, `trial`) VALUES ($S_Token,$S_Enabled,$S_Days,$S_RDays,$S_User,$S_Time,$S_Name,$S_Paid,$S_Trial)")){
          $Stmt = $Pdo->query("SELECT `id` FROM `tokens` WHERE `token` = $S_Token LIMIT 1");
          $Result = $Stmt->fetch();
          $ID = ((isset($_SESSION['id'])) ? $_SESSION['id'] : -1 );
          Functions::LogToDB("tokens", json_encode(array('id'=>$ID, 'user'=>$User, 'ip'=>Functions::GetIP(), 'tid'=>$Result['id'], 'options'=>array("Token"=>$Token, "Days"=>$Days, "Reserve Days"=>$RDays, "Buyer"=>$Name, "Amount Paid"=>$Paid, "Enabled"=>$Enabled, "Trial"=>$Trial), 'error'=>'generate_token')));
          return $Token;
        }
      }
    }
    //shouldn't get here
    return "";
  }
  
  public static function ApproveToken(int $ID) : int {
    $Pdo = Functions::GetDB();
    $TID = $Pdo->quote($ID);
    $Stmt = $Pdo->query("SELECT `enabled` FROM `tokens` WHERE `id` = $TID LIMIT 1");
    if ($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      if($Result['enabled'] == 1){
        return 2; //already enabled
      }
      $Stmt = $Pdo->exec("UPDATE `tokens` SET `enabled` = 1 WHERE `id` = $TID");
      Functions::LogToDB("tokens", json_encode(array('id'=>$_SESSION['id'], 'ip'=>Functions::GetIP(), 'tid'=>$ID, 'error'=>'approve_token')));
      return 1; //enabled fam
    }
    return 0; //token not exist
  }

  public static function GetUsername(int $ID) : string {
    $Pdo = Functions::GetDB();
    $ID = $Pdo->quote($ID);
    $Stmt = $Pdo->query("SELECT `username` FROM `logins` WHERE `id` = $ID LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return $Result['username'];
    }
    return "Unknown: ".Functions::GetIP();
  }

  public static function CheckUserStatus(string $CPUKey, string $Name = "", bool $Check = false) : bool {
    if(Functions::CheckIPBan()) return false;
    if(!preg_match("/([0-9A-Fa-f]){32}/", $CPUKey)) return false;
    $Pdo = Functions::GetDB();
    $CPUKey = $Pdo->quote($CPUKey);
    $Stmt = $Pdo->query("SELECT * FROM `clients` WHERE `cpukey` = $CPUKey LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      if($Check){
        if($Result['blacklisted'] == 1) return false;
        return $Name == $Result['username'];
      }
      return true;
    }
    return false;
  }

  public static function CheckUserBlacklist(string $CPUKey) : string {
    if(!preg_match("/([0-9A-Fa-f]){32}/", $CPUKey)) return false;
    $Pdo = Functions::GetDB();
    $CPUKey = $Pdo->quote($CPUKey);
    $Stmt = $Pdo->query("SELECT * FROM `clients` WHERE `cpukey` = $CPUKey LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return ($Result['blacklisted'] == 1);
    }
    return false;
  }

  public static function EditClient(int $ID, string $Name, string $CPUKey, string $Genealogy, string $Email, int $Fails, string $ExpDay, string $ExpTime, int $RDays, string $Notes, int $Lifetime, int $Blacklist, int $Developer, int $UsedTrial) : int {
    //Functions::LogToDB("clients", "[Set Client Options] UID: ".$ID." Days: ".$Days." Name: ".$Name." Fails: ".$Fails." Last: ".$Remaining." Lifetime: ".$Lifetime." Blacklist: ".$Blacklist." XEXHash: ".$XEXHash." Genealogy: ".$GHash." Executor: ".$_SESSION['name']." IP: ".Functions::GetIP());
    $Pdo = Functions::GetDB();
    //serialize id
    $ID = $Pdo->quote($ID);
    //check client exists
    $Stmt = $Pdo->query("SELECT * FROM `clients` WHERE `id` = $ID LIMIT 1");
    if($Stmt->rowCount() != 1) return 0;
    $Result = $Stmt->fetch();
    //start insert string
    $str = "";
    $comma = "";
    $Options = array();
    //name
    if($Name != $Result['name']) {
      $Options = array_merge($Options, array("Name"=>array($Result['name'], $Name)));
      $Name = $Pdo->quote($Name);
      $str = $comma."`name` = $Name";
      $comma = ", ";
    }
    //genealogy
    if($Genealogy != $Result['genealogy_hash']) {
      $Options = array_merge($Options, array("Genealogy Hash"=>array($Result['genealogy_hash'], $Genealogy)));
      $Genealogy = $Pdo->quote($Genealogy);
      $str .= $comma."`genealogy_hash` = $Genealogy";
      $comma = ", ";
    }
    //email
    if($Email != $Result['email']) {
      $Options = array_merge($Options, array("Email"=>array($Result['email'], $Email)));
      $Email = $Pdo->quote($Email);
      $str .= $comma."`email` = $Email";
      $comma = ", ";
    }
    //fails
    if($Fails != $Result['fails']) {
      $Options = array_merge($Options, array("Fails"=>array($Result['fails'], $Fails)));
      $Fails = $Pdo->quote($Fails);
      $str .= $comma."`fails` = $Fails";
      $comma = ", ";
    }
    //exp time and day
    $Expire = strtotime($ExpDay." ".$ExpTime." America/New_York");
    if($Expire != $Result['remaining']){
      $Options = array_merge($Options, array("Expire Time"=>array($Result['remaining'], $Expire)));
      $Expire = $Pdo->quote($Expire);
      $str .= $comma."`remaining` = $Expire";
      $comma = ", ";
    }
    //reserve days
    if($RDays != $Result['days']) {
      $Options = array_merge($Options, array("Reserve Days"=>array($Result['days'], $RDays)));
      $RDays = $Pdo->quote($RDays);
      $str .= $comma."`days` = $RDays";
      $comma = ", ";
    }
    //lifetime
    if($Lifetime != $Result['lifetime']) {
      $Options = array_merge($Options, array("Lifetime"=>array($Result['lifetime'], $Lifetime)));
      $Lifetime = $Pdo->quote($Lifetime);
      $str .= $comma."`lifetime` = $Lifetime";
      $comma = ", ";
    }
    //blacklist
    if($Blacklist != $Result['blacklisted']) {
      $Options = array_merge($Options, array("Blacklist"=>array($Result['blacklisted'], $Blacklist)));
      $Blacklist = $Pdo->quote($Blacklist);
      $str .= $comma."`blacklisted` = $Blacklist";
      $comma = ", ";
    }
    //developer
    if($Developer != $Result['developer']) {
      $Options = array_merge($Options, array("Developer"=>array($Result['developer'], $Developer)));
      $Developer = $Pdo->quote($Developer);
      $str .= $comma."`developer` = $Developer";
      $comma = ", ";
    }
    //used trial
    if($UsedTrial != $Result['usedtrial']) {
      $Options = array_merge($Options, array("Used Trial"=>array($Result['usedtrial'], $UsedTrial)));
      $UsedTrial = $Pdo->quote($UsedTrial);
      $str .= $comma."`usedtrial` = $UsedTrial";
      $comma = ", ";
    }
    //notes
    if($Notes != $Result['notes']) {
      $Options = array_merge($Options, array("Notes"=>array($Result['notes'], $Notes)));
      $Notes = $Pdo->quote($Notes);
      $str .= $comma."`notes` = $Notes";
      $comma = ", ";
    }
    //update
    if(strlen($str) > 0){
      $Pdo->exec("UPDATE `clients` SET ".$str." WHERE `id` = $ID");
      $UID = ((isset($_SESSION['id'])) ? $_SESSION['id'] : -1);
      Functions::LogToDB("clients", json_encode(array('id'=>$UID, 'ip'=>Functions::GetIP(), 'cid'=>$Result['id'], 'options'=>$Options, 'error'=>'edit_client')));
      return 1;
    } else {
      return 2;
    }
  }
  
  public static function EditToken(int $ID, int $Days, int $RDays, string $Buyer, string $Paid, int $Enabled, int $Redeemed, int $Trial, int $Display) : int {
    //Functions::LogToDB("clients", "[Set Client Options] UID: ".$ID." Days: ".$Days." Name: ".$Name." Fails: ".$Fails." Last: ".$Remaining." Lifetime: ".$Lifetime." Blacklist: ".$Blacklist." XEXHash: ".$XEXHash." Genealogy: ".$GHash." Executor: ".$_SESSION['name']." IP: ".Functions::GetIP());
    $Pdo = Functions::GetDB();
    //serialize id
    $ID = $Pdo->quote($ID);
    //check client exists
    $Stmt = $Pdo->query("SELECT * FROM `tokens` WHERE `id` = $ID LIMIT 1");
    if($Stmt->rowCount() != 1) return 0;
    $Result = $Stmt->fetch();
    //start insert string
    $str = "";
    $comma = "";
    $Options = array();
    //days
    if($Days != $Result['days']) {
      $Options = array_merge($Options, array("Days"=>array($Result['days'], $Days)));
      $Days = $Pdo->quote($Days);
      $str = $comma."`days` = $Days";
      $comma = ", ";
    }
    //rdays
    if($RDays != $Result['reserve_days']) {
      $Options = array_merge($Options, array("Reserve Days"=>array($Result['reserve_days'], $RDays)));
      $RDays = $Pdo->quote($RDays);
      $str = $comma."`reserve_days` = $RDays";
      $comma = ", ";
    }
    //buyer
    if($Buyer != $Result['whofor']) {
      $Options = array_merge($Options, array("Buyer"=>array($Result['whofor'], $Buyer)));
      $Buyer = $Pdo->quote($Buyer);
      $str .= $comma."`whofor` = $Buyer";
      $comma = ", ";
    }
    //paid
    if($Paid != $Result['paid']) {
      $Options = array_merge($Options, array("Amount Paid"=>array($Result['paid'], $Paid)));
      $Paid = $Pdo->quote($Paid);
      $str .= $comma."`paid` = $Paid";
      $comma = ", ";
    }
    //enabled
    if($Enabled != $Result['enabled']) {
      $Options = array_merge($Options, array("Enabled"=>array($Result['enabled'], $Enabled)));
      $Enabled = $Pdo->quote($Enabled);
      $str .= $comma."`enabled` = $Enabled";
      $comma = ", ";
    }
    //redeemed
    if($Redeemed != $Result['redeemed']) {
    $Options = array_merge($Options, array("Redeemed"=>array($Result['redeemed'], $Redeemed), "Redeemed By"=>array($Result['redeemed_by'], ""), "Redeemed Date"=>array($Result['redeemed_date'], 0)));
      $Redeemed = $Pdo->quote($Redeemed);
      $str .= $comma."`redeemed` = $Redeemed";
      $comma = ", ";
      if($Redeemed == 0 && $Result['redeemed'] == 1){
        $str .= $comma."`redeemed_by` = '', `redeemed_date` = 0";
      }
    }
    //trial
    if($Trial != $Result['trial']) {
      $Options = array_merge($Options, array("Trial"=>array($Result['trial'], $Trial)));
      $Trial = $Pdo->quote($Trial);
      $str .= $comma."`trial` = $Trial";
      $comma = ", ";
    }
    //display
    if($Display != $Result['display']) {
      $Options = array_merge($Options, array("Display"=>array($Result['display'], $Display)));
      $Display = $Pdo->quote($Display);
      $str .= $comma."`display` = $Display";
      $comma = ", ";
    }
    //update
    if(strlen($str) > 0){
      $Pdo->exec("UPDATE `tokens` SET ".$str." WHERE `id` = $ID");
      Functions::LogToDB("tokens", json_encode(array('id'=>$_SESSION['id'], 'ip'=>Functions::GetIP(), 'tid'=>$Result['id'], 'options'=>$Options, 'error'=>'edit_token')));
      return 1;
    } else {
      return 2;
    }
  }

  public static function GetClientDays(string $CPUKey) : int {
    $Pdo = Functions::GetDB();
    $CPUKey = $Pdo->quote($CPUKey);
    $Stmt = $Pdo->query("SELECT `days` FROM `clients` WHERE `cpukey` = $CPUKey LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return $Result['days'];
    }
    return 0;
  }
  
  public static function GetClientExpire(string $CPUKey) : int {
    $Pdo = Functions::GetDB();
    $CPUKey = $Pdo->quote($CPUKey);
    $Stmt = $Pdo->query("SELECT `remaining` FROM `clients` WHERE `cpukey` = $CPUKey LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return $Result['remaining'];
    }
    return 0;
  }

  public static function GetClientTime(string $CPUKey, string $Name) : string {
    if (Functions::CheckIPBan()){return '<span class="invalidcpu">You are IP Banned.</span>';}
    if(!preg_match("/([0-9A-Fa-f]){32}/", $CPUKey)) return '<span class="invalidcpu">The specified CPU Key is invalid.</span>';
    if (!Functions::CheckUserBlacklist($CPUKey)) return '<span class="blacklistcpu">The specified CPU Key is blacklisted.</span>';
    $Pdo = Functions::GetDB();
    $CPUKey = $Pdo->quote($CPUKey);
    $Name = $Pdo->quote($Name);
    $Stmt = $Pdo->query("SELECT * FROM `clients` WHERE `cpukey` = $CPUKey AND `name` = $Name LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      //Functions::LogToDB("checktime", "[Check Time] CPU: ".$Result['cpukey']." USER: ".$Result['name']." IP: ".Functions::GetIP());
      //lifetime
      if($Result['lifetime'] == 1){
        return '<span class="cpulifetime">Welcome '.$Result['name'].'! You have lifetime. Your time will never expire.</span><br><br>';
      }
      //days > 0
      if($Result['days'] > 0) {
        return '<span class="cputime">Welcome '.$Result['name'].'! You have '.$Result['days'].' more days on the specified CPU Key.</span><br><br>';
      }
      //time remaining on day
      if($Result['remaining'] > time()) {
        return '<span class="cputime">Welcome ' . $Result['name'] . '! This is your last day on the specified CPU Key. </font><br><br>';
      }
      return '<span class="cputime">Welcome ' . $Result['name'] . '! You have no more days on the specified CPU Key. </font><br><br>';
    }
    return '<span class="invalidcpu">The specified CPU Key and Name are invalid.</span>'; //shouldn't get here
  }

  public static function RedeemToken(string $CPUKey, string $Name, string $Token) : string {
    if (Functions::CheckIPBan()){return '<span class="invalidcpu">You are IP Banned.</span>';}
    if(!preg_match("/([0-9A-Fa-f]){32}/", $CPUKey)) return '<span class="invalidcpu">The specified CPU Key is invalid.</span>';
    if(!preg_match("/([0-9A-Fa-f]){4}-([0-9A-Fa-f]){4}-([0-9A-Fa-f]){4}/", $Token)) return '<span class="invalidtoken">The specified Token is invalid.</span>';
    if(Functions::CheckUserBlacklist($CPUKey)) return '<span class="blacklistcpu">The specified CPU Key is blacklisted.</span>';
    if(Functions::CheckTokenBrute($CPUKey)) return '<span class="blacklistcpu">The specified CPU Key has been temporarily disabled from redeeming bad tokens.</span>';
    //get database
    $Pdo = Functions::GetDB();
    //check token valid
    if(!Functions::IsTokenValid($Token)){
      $SCPUKey = $Pdo->quote($CPUKey);
      $SNow = $Pdo->quote(time());
      $SIP = $Pdo->quote(Functions::GetIP());
      $Pdo->exec("INSERT INTO `redeem_fails`(`cpukey`, `time`, `ip`) VALUES ($SCPUKey,$SNow,$SIP)");
      //Functions::LogToDB("redeem", "[Redeem Token] Bad Token CPU: ".$CPUKey." USER: ".$Name." TOKEN: ".$Token." IP: ".Functions::GetIP());
      return '<span class="invalidtoken">Token specified does not exist or has already been redeemed.</span>';
    }
    $Days = Functions::GetTokenDays($Token);
    $RDays = Functions::GetTokenReserveDays($Token);
    $Lifetime = ($Days == 99999);
    $SCPUKey = $Pdo->quote($CPUKey);
    $Now =  $Pdo->quote(time());
    $SToken = $Pdo->quote($Token);
    $SName = $Pdo->quote($Name);
    $Rem = Functions::GetClientExpire($CPUKey);
    if($Rem < 5000) $Rem = $Now;

    //check user valid
    if(Functions::CheckUserStatus($CPUKey)){
      //lifetime
      if($Lifetime){
        $Pdo->exec("UPDATE `clients` SET `days` = 0, `lifetime` = 1 WHERE `cpukey` = $SCPUKey");
        $Pdo->exec("UPDATE `tokens` SET `redeemed`= 1, `redeemed_date`= $Now, `redeemed_by` = $SName WHERE `token` = $SToken");
        //Functions::LogToDB("redeem", "[Redeem Token] Lifetime CPU: ".$CPUKey." USER: ".$Name." TOKEN: ".$Token." IP: ".Functions::GetIP());
        return '<span class="redeemed">You have added lifetime to specified CPU Key.</span>';
      }
      //add days
      else {
        $CDays = Functions::GetClientDays($CPUKey);
        $SDays = $Pdo->quote($RDays + $CDays);
        $SRem = $Pdo->quote(($Days * 86400) + $Rem);
        $Pdo->exec("UPDATE `clients` SET `days` = $SDays, `remaining` = $SRem WHERE `cpukey` = $SCPUKey");
        $Pdo->exec("UPDATE `tokens` SET `redeemed`= 1, `redeemed_date`= $Now, `redeemed_by` = $SName WHERE `token` = $SToken");
        //Functions::LogToDB("redeem", "[Redeem Token] CPU: ".$CPUKey." USER: ".$Name." TOKEN: ".$Token." IP: ".Functions::GetIP());
        return '<span class="redeemed">You have added '.($Days + $CDays).' days to the specified CPU Key.</span>';
      }
    }
    //new user
    else {
      //insert lifetime
      if($Lifetime){
        $Pdo->exec("INSERT INTO `clients`(`cpukey`, `name`, `lifetime`) VALUES ($SCPUKey,$SName,1)");
        $Pdo->exec("UPDATE `tokens` SET `redeemed`= 1, `redeemed_date`= $Now, `redeemed_by` = $SName WHERE `token` = $SToken");
        //Functions::LogToDB("redeem", "[Redeem Token] Lifetime CPU: ".$CPUKey." USER: ".$Name." TOKEN: ".$Token." IP: ".Functions::GetIP());
        return '<span class="redeemed">Thank you for joining us. You have added lifetime to specified CPU Key.</span>';
      }
      //insert days
      else {
        $SDays = $Pdo->quote($RDays);
        $SRem = $Pdo->quote(($Days * 86400) + $Rem);
        $Pdo->exec("INSERT INTO `clients`(`cpukey`, `name`, `days`, `remaining`) VALUES ($SCPUKey,$SName,$SDays,$SRem)");
        $Pdo->exec("UPDATE `tokens` SET `redeemed`= 1, `redeemed_date`= $Now, `redeemed_by` = $SName WHERE `token` = $SToken");
        //Functions::LogToDB("redeem", "[Redeem Token] CPU: ".$CPUKey." USER: ".$Name." TOKEN: ".$Token." IP: ".Functions::GetIP());
        return '<span class="redeemed">Thank you for joining us. You have just added '.$Days.' days to the specified CPU Key.</span>';
      }
    }
  }

  public static function IsTokenValid(string $Token) : bool {
    if(!preg_match("/([0-9A-Fa-f]){4}-([0-9A-Fa-f]){4}-([0-9A-Fa-f]){4}/", $Token)) return false;
    $Pdo = Functions::GetDB();
    $Token = $Pdo->quote($Token);
    $Stmt = $Pdo->query("SELECT * FROM `tokens` WHERE `token` = $Token LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      return ($Result['redeemed'] == 0);
    }
    return false;
  }

  public static function GetTokenDays(string $Token) : int {
    $Pdo = Functions::GetDB();
    $Token = $Pdo->quote($Token);
    $Stmt = $Pdo->query("SELECT `days` FROM `tokens` WHERE `token` = $Token LIMIT 1");
    if ($Stmt->rowCount() == 1) {
      $Result = $Stmt->fetch();
      return $Result['days'];
    }
    return 0;
  }
  
  public static function GetTokenReserveDays(string $Token) : int {
    $Pdo = Functions::GetDB();
    $Token = $Pdo->quote($Token);
    $Stmt = $Pdo->query("SELECT `reserve_days` FROM `tokens` WHERE `token` = $Token LIMIT 1");
    if ($Stmt->rowCount() == 1) {
      $Result = $Stmt->fetch();
      return $Result['reserve_days'];
    }
    return 0;
  }

  public static function CheckTokenBrute(string $CPUKey) : bool {
    $Pdo = Functions::GetDB();
    $Valid = $Pdo->quote(time() - (60 * 60)); //1 hour
    $CPUKey = $Pdo->quote($CPUKey);
    $Stmt = $Pdo->query("SELECT * FROM `redeem_fails` WHERE `cpukey` = $CPUKey AND `time` > $Valid");
    if ($Stmt->rowCount() >= 5) {
      return true;
    }
    return false;
  }

  public static function RemoveClient(int $ID) : bool {
    $Pdo = Functions::GetDB();
    $ID = $Pdo->quote($ID);
    $Stmt = $Pdo->query("SELECT * FROM `clients` WHERE `id` = $ID LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      $Pdo->exec("INSERT INTO `clients_restore` SELECT * FROM `clients` WHERE `id` = $ID");
      if($Pdo->exec("DELETE FROM `clients` WHERE `id` = $ID") == 1) {
        Functions::LogToDB("clients", json_encode(array('id'=>$_SESSION['id'], 'ip'=>Functions::GetIP(), 'cid'=>$Result['id'], 'error'=>'remove_client')));
        return true;
      }
      return false;
    }
    return false;
  }

  public static function AddClient(string $Name, string $CPUKey, string $Email, string $ExpDay, string $ExpTime, int $RDays, string $Notes, int $Lifetime, int $Blacklist, int $Developer, int $UsedTrial) : int {
    $Pdo = Functions::GetDB();
    $S_CPUKey = $Pdo->quote($CPUKey);
    $Stmt = $Pdo->query("SELECT `id` FROM `clients` WHERE `cpukey` = $S_CPUKey LIMIT 1");
    if($Stmt->rowCount() == 1){
      return 2;
    }
    //name
    $S_Name = $Pdo->quote($Name);
    $S_Email = $Pdo->quote($Email);
    $Expire = strtotime($ExpDay." ".$ExpTime." EST");
    $S_Expire = $Pdo->quote($Expire);
    $S_RDays = $Pdo->quote($RDays);
    $S_Lifetime = $Pdo->quote($Lifetime);
    $S_Blacklist = $Pdo->quote($Blacklist);
    $S_Developer = $Pdo->quote($Developer);
    $S_UsedTrial = $Pdo->quote($UsedTrial);
    $S_Notes = $Pdo->quote($Notes);
    if($Pdo->exec("INSERT INTO `clients`(`cpukey`, `name`, `email`, `days`, `remaining`, `lifetime`, `blacklisted`, `developer`, `usedtrial`, `notes`) VALUES ($S_CPUKey, $S_Name, $S_Email, $S_RDays, $S_Expire, $S_Lifetime, $S_Blacklist, $S_Developer, $S_UsedTrial, $S_Notes)") == 1){
      $Stmt = $Pdo->query("SELECT `id` FROM `clients` WHERE `cpukey` = $S_CPUKey LIMIT 1");
      $Result = $Stmt->fetch();
      Functions::LogToDB("clients", json_encode(array('id'=>$_SESSION['id'], 'ip'=>Functions::GetIP(), 'options'=>array("Name"=>$Name, "CPUKey"=>$CPUKey, "Email"=>$Email, "Expire Time"=>$Expire, "Reserve Days"=>$RDays, "Notes"=>$Notes, "Lifetime"=>$Lifetime, "Blacklist"=>$Blacklist, "Developer"=>$Developer, 'Used Trial'=> $UsedTrial), 'cid'=>$Result['id'], 'error'=>'add_client')));
      return 1;
    } else {
      return 0;
    }
  }

  public static function RemoveToken(int $ID){
    $Pdo = Functions::GetDB();
    $ID = $Pdo->quote($ID);
    $Stmt = $Pdo->query("SELECT * FROM `tokens` WHERE `id` = $ID LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      $Pdo->exec("INSERT INTO `tokens_restore` SELECT * FROM `tokens` WHERE `id` = $ID");
      $Pdo->exec("DELETE FROM `tokens` WHERE `id` = $ID");
      Functions::LogToDB("tokens", json_encode(array('id'=>$_SESSION['id'], 'ip'=>Functions::GetIP(), 'tid'=>$Result['id'], 'error'=>'remove_token')));
    }
  }

  public static function SetOption(string $Option, string $Value){
    if(strlen(Functions::GetOption($Option)) < 1) return;
    $Pdo = Functions::GetDB();
    $S_Option = $Pdo->quote($Option);
    $S_Value = $Pdo->quote($Value);
    $Stmt = $Pdo->query("SELECT * FROM `options` WHERE `object` = $S_Option LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      $Pdo->exec("UPDATE `options` SET `value`= $S_Value WHERE `object` = $S_Option");
      Functions::LogToDB("options", json_encode(array('id'=>$_SESSION['id'], 'ip'=>Functions::GetIP(), 'option'=>$Result['object'], 'values'=>array($Result['value'], $Value), 'error'=>'change_value')));
    }
  }

  public static function GenerateRandomString(int $Length = 10) : string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $Length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  public static function HandleRegistrationCode(){
    if(Functions::GetOption("generate_new_code") == "1") {
      Functions::SetOption("registration_code", Functions::GenerateRandomString(20));
    }
  }
  
  public static function GetTitleInfo(string $HexID) : array {
    $Pdo = Functions::GetDB();
    $SHexID = $Pdo->quote($HexID);
    $Stmt = $Pdo->query("SELECT * FROM `titleids` WHERE `hexid` = $SHexID LIMIT 1");
    if($Stmt->rowCount() == 1){
      $Result = $Stmt->fetch();
      $Title = (($Result['title'] != "" && $Result['title'] != "null") ? $Result['title'] : $Result['hexid']);
      $Title1 = (($Result['title'] != "" && $Result['title'] != "null") ? $Result['title'] : "No Title");
      $Picture = (($Result['boxart'] != "" && $Result['boxart'] != "null") ? $Result['boxart'] : "http://download.xbox.com/content/images/66acd000-77fe-1000-9115-d802fffe07d1/1033/boxartlg.jpg");
      $Developer = (($Result['developer'] != "" && $Result['developer'] != "null") ? $Result['developer'] : "No Developer Found.");
      $Description = (($Result['description'] != "" && $Result['description'] != "null") ? $Result['description'] : "No Description Available.");
      //fill in html
      $str = '<div class="gameid">'.$Title.'<div class="gamepopup"><div class="gamepic">';
      $str .= '<img src="'.$Picture.'"/></div><div class="gameinfo hidescroll"><center><span class="gameheader">';
      $str .= $Title1.'</span><br><span class="gamedata">Title ID: '.$Result['hexid'].'</br>Developer: ';
      $str .= $Developer.'</span></center><p>'.$Description.'</p></div></div></div>';
      return array("Title"=>htmlentities($Title, ENT_COMPAT | ENT_HTML5, 'ISO-8859-1'), "Title1"=>htmlentities($Title1, ENT_COMPAT | ENT_HTML5, 'ISO-8859-1'), "HexID"=>$Result['hexid'], "Picture"=>$Picture, "Developer"=>htmlentities($Developer, ENT_COMPAT | ENT_HTML5, 'ISO-8859-1'), "Description"=>htmlentities($Description, ENT_COMPAT | ENT_HTML5, 'ISO-8859-1'));
    }
    return array();
  }
  
  public static function GetRelativeTime(int $Timestamp) : string {
    if($Timestamp == 0) return "0 Seconds";
    $str = "";
    //do years
    $Years = floor($Timestamp/31556926);
    $Timestamp = $Timestamp % 31556926;
    if($Years > 0) $str = $Years." Year(s) ";
    //do months
    $Months = floor($Timestamp/2592000);
    $Timestamp = $Timestamp % 2592000;
    if($Months > 0) $str .= $Months." Month(s) ";
    //do weeks
    $Weeks = floor($Timestamp/604800);
    $Timestamp = $Timestamp % 604800;
    if($Weeks > 0) $str .= $Weeks." Week(s) ";
    //do days
    $Days = floor($Timestamp/86400);
    $Timestamp = $Timestamp % 86400;
    if($Days > 0) $str .= $Days." Day(s) ";
    //do hours
    $Hours = floor($Timestamp/3600);
    $Timestamp = $Timestamp % 3600;
    if($Hours > 0) $str .= $Hours." Hour(s) ";
    //do minutes
    $Minutes = floor($Timestamp/60);
    $Timestamp = $Timestamp % 60;
    if($Minutes > 0) $str .= $Minutes." Minute(s) ";
    //return
    return $str;
  }
  
  public static function ToString(array $Input) : string {
    $Return = "";
    for($X = 0; $X < count($Input); $X++){
      $Return .= chr($Input[$X]);
    }
    return $Return;
  }
  
  public static function ToHexString(array $Input) : string {
    $Return = "";
    for($X = 0; $X < count($Input); $X++){
      $Return .= strval(dechex($Input[$X]));
    }
    return $Return;
  }
  
  public static function SetServerOptions(string $Version, int $Freemode, int $GHash, int $XHash){
    if(Functions::GetOption('version') != $Version){
      Functions::SetOption('version', $Version);
    }
    if(Functions::GetOption('free') != $Freemode){
      Functions::SetOption('free', $Freemode);
    }
    if(Functions::GetOption('genealogy_hash') != $GHash){
      Functions::SetOption('genealogy_hash', $GHash);
    }
    if(Functions::GetOption('xexhash') != $XHash){
      Functions::SetOption('xexhash', $XHash);
    }
  }
  
  public static function SetPanelOptions(int $Registration, int $GenCode, string $RegCode, string $SockIO) {
    if(Functions::GetOption('registration') != $Registration){
      Functions::SetOption('registration', $Registration);
    }
    if(Functions::GetOption('generate_new_code') != $GenCode){
      Functions::SetOption('generate_new_code', $GenCode);
    }
    if(Functions::GetOption('registration_code') != $RegCode){
      Functions::SetOption('registration_code', $RegCode);
    }
    if(Functions::GetOption('socketio') != $SockIO){
      Functions::SetOption('socketio', $SockIO);
    }
  }
  
  public static function PrettyPrintMem(float $Mem) : string {
    $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
    $base = 1024;
    $class = min((int)log($Mem , $base) , count($si_prefix) - 1);
    return sprintf('%1.2f' , $Mem / pow($base,$class)) . ' ' . $si_prefix[$class];
  }
  
  public static function GetRelativeTime2(string $ts) : string {
    if(!ctype_digit($ts))
      $ts = strtotime($ts);

    $diff = time() - $ts;
    if($diff == 0)
      return 'now';
    elseif($diff > 0) {
      $day_diff = floor($diff / 86400);
      if($day_diff == 0) {
        if($diff < 60) return 'just now';
        if($diff < 120) return '1 minute ago';
        if($diff < 3600) return floor($diff / 60) . ' minutes ago';
        if($diff < 7200) return '1 hour ago';
        if($diff < 86400) return floor($diff / 3600) . ' hours ago';
      }
      if($day_diff == 1) return 'Yesterday';
      if($day_diff < 7) return $day_diff . ' days ago';
      if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
      if($day_diff < 60) return 'last month';
      return date('F Y', $ts);
    } else {
      $diff = abs($diff);
      $day_diff = floor($diff / 86400);
      if($day_diff == 0) {
      if($diff < 120) return 'in a minute';
      if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
      if($diff < 7200) return 'in an hour';
        if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
      }
      if($day_diff == 1) return 'Tomorrow';
      if($day_diff < 4) return date('l', $ts);
      if($day_diff < 7 + (7 - date('w'))) return 'next week';
      if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
      if(date('n', $ts) == date('n') + 1) return 'next month';
      return date('F Y', $ts);
    }
  }
}
