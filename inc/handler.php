<?php
include_once 'functions.php';
include_once 'GoogleAuthenticator.php';

Functions::SecStart();

class Handler {

  public static function Handle(){
    if(!isset($_POST['func'])) return;

    $Function = filter_input(INPUT_POST, 'func', FILTER_SANITIZE_STRING);

    //echo $Function;


    if($Function == "removeClient"){
      Handler::RemoveClient();
    }
    else if ($Function == "editClient"){
      Handler::EditClient();
    }
    else if ($Function == "editToken"){
      Handler::EditToken();
    }
    else if ($Function == "removeToken") {
      Handler::RemoveToken();
    }
    else if ($Function == "addClient") {
      Handler::AddClient();
    }
    else if ($Function == "generateToken") {
      Handler::GenerateToken();
    }
    else if ($Function == "generateAutobuyToken") {
      Handler::GenerateAutobuyToken();
    }
    else if ($Function == "approveToken") {
      Handler::ApproveToken();
    }
    else if ($Function == "getLogs") {
      Handler::GetLogs();
    }
    else if ($Function == "getKeyvaults") {
      Handler::GetKeyvaults();
    }
    else if ($Function == "getTokens") {
      Handler::GetTokens();
    }
    else if ($Function == "getClients"){
      Handler::GetClients();
    }
    else if ($Function == "logout"){
      Handler::Logout();
    }
    else if ($Function == "login"){
      Handler::Login();
    }
    else if ($Function == "register"){
      Handler::Register();
    }
    else if ($Function == "twofactor"){
      Handler::TwoFactor();
    }
    else if ($Function == "reset"){
      Handler::Reset();
    }
    else if ($Function == "getTitleID"){
      Handler::GetTitleId();
    }
    else if ($Function == "setServerOptions"){
      Handler::SetServerOptions();
    }
    else if ($Function == "setPanelOptions"){
      Handler::SetPanelOptions();
    }
    else if ($Function == "uploadAvatar"){
      Handler::UploadAvatar();
    }
    else if ($Function == "editProfile"){
      Handler::EditProfile();
    }
    else if ($Function == "changePassword"){
      Handler::ChangePassword();
    }
    else if ($Function == "recovery"){
      Handler::Recovery();
    }
    else if ($Function == "checkKV"){
      Handler::CheckKV();
    }
    else {
      if (Functions::GetLoginLevel() >= 2){
        header('Location: ../dashboard.php');
      } else {
        header('Location: ../index.php');
      }
    }
  }

  public static function RemoveClient(){
    if(!isset($_POST['id'])) return;
    $ID = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2){
      $Result = Functions::RemoveClient($ID);
      echo json_encode(array('status'=>$Result));
      return;
    }
    header('Location: ../index.php');
  }

  public static function EditClient(){
    if(!isset($_POST['id'])) return;
    if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2){
      $ID = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
      $Name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
      $CPUKey = filter_input(INPUT_POST, 'cpukey', FILTER_SANITIZE_STRING);
      $Genealogy = filter_input(INPUT_POST, 'genealogy', FILTER_SANITIZE_STRING);
      $Email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
      $Fails = filter_input(INPUT_POST, 'fails', FILTER_SANITIZE_NUMBER_INT);
      $ExpDay = filter_input(INPUT_POST, 'expday', FILTER_SANITIZE_STRING);
      $ExpTime = filter_input(INPUT_POST, 'exptime', FILTER_SANITIZE_STRING);
      $RDays = filter_input(INPUT_POST, 'rdays', FILTER_SANITIZE_NUMBER_INT);
      $Notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
      $Lifetime = ((isset($_POST['lifetime'])) ? 1 : 0);
      $Blacklist = ((isset($_POST['blacklisted'])) ? 1 : 0);
      $Developer = ((isset($_POST['developer'])) ? 1 : 0);
      $UsedTrial = ((isset($_POST['usedtrial'])) ? 1 : 0);
      $Result = Functions::EditClient($ID, $Name, $CPUKey, $Genealogy, $Email, $Fails, $ExpDay, $ExpTime, $RDays, $Notes, $Lifetime, $Blacklist, $Developer, $UsedTrial);
      if($Result == 1){ 
        header('Location: ../clients.php?success=Client Edited Successfully.');
      } else if($Result == 2) {
        header('Location: ../clients.php?warning=No changes were made.');
      } else {
        header('Location: ../clients.php?error=Failed to save client data.');
      }
      return;
    }
    header('Location: ../index.php');
  }
  
  public static function EditToken(){
    if(!isset($_POST['id'])) return;
    if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2){
      $ID = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
      $Days = filter_input(INPUT_POST, 'days', FILTER_SANITIZE_NUMBER_INT);
      $RDays = filter_input(INPUT_POST, 'rdays', FILTER_SANITIZE_NUMBER_INT);
      $Buyer = filter_input(INPUT_POST, 'buyer', FILTER_SANITIZE_STRING);
      $Paid = filter_input(INPUT_POST, 'paid', FILTER_SANITIZE_STRING);
      $Enabled = ((isset($_POST['enabled'])) ? 1 : 0);
      $Redeemed = ((isset($_POST['redeemed'])) ? 1 : 0);
      $Trial = ((isset($_POST['trial'])) ? 1 : 0);
      $Display = ((isset($_POST['display'])) ? 0 : 1);
      $Result = Functions::EditToken($ID, $Days, $RDays, $Buyer, $Paid, $Enabled, $Redeemed, $Trial, $Display);
      if($Result == 1){ 
        header('Location: ../tokens.php?success=Token Edited Successfully.');
      } else if($Result == 2) {
        header('Location: ../tokens.php?warning=No changes were made.');
      } else {
        header('Location: ../tokens.php?error=Failed to save token data.');
      }
      return;
    }
    header('Location: ../index.php');
  }

  public static function RemoveToken(){
    if(!isset($_POST['id'])) return;
    if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 3){
      $ID = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
      Functions::RemoveToken($ID);
      header('Location: ../tokens.php');
      return;
    }
    header('Location: ../index.php');
  }

  public static function AddClient(){
    if(!isset($_POST['cpukey'], $_POST['name'], $_POST['email'], $_POST['expday'], $_POST['exptime'], $_POST['rdays'], $_POST['notes'])) return;
    if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2) {
      $Name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
      $CPUKey = filter_input(INPUT_POST, 'cpukey', FILTER_SANITIZE_STRING);
      $Email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
      $ExpDay = filter_input(INPUT_POST, 'expday', FILTER_SANITIZE_STRING);
      $ExpTime = filter_input(INPUT_POST, 'exptime', FILTER_SANITIZE_STRING);
      $RDays = filter_input(INPUT_POST, 'rdays', FILTER_SANITIZE_NUMBER_INT);
      $Notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
      $Lifetime = ((isset($_POST['lifetime'])) ? 1 : 0);
      $Blacklist = ((isset($_POST['blacklisted'])) ? 1 : 0);
      $Developer = ((isset($_POST['developer'])) ? 1 : 0);
      $UsedTrial = ((isset($_POST['usedtrial'])) ? 1 : 0);
      $Result = Functions::AddClient($Name, $CPUKey, $Email, $ExpDay, $ExpTime, $RDays, $Notes, $Lifetime, $Blacklist, $Developer, $UsedTrial);
      if($Result == 1){
        header('Location: ../clients.php?success=Client Added Successfully.');
      } else if ($Result == 2){
        header('Location: ../clients.php?error=Client Already Exists.');
      } else {
        header('Location: ../clients.php?error=Failed to add client.');
      }
      
      
      return;
    }
    header('Location: ../index.php');
  }

  public static function GenerateToken(){
    if(!isset($_POST['days'], $_POST['rdays'], $_POST['buyer'], $_POST['paid'])) return;
    if(Functions::IsLoggedIn()) {
      if(Functions::GetLoginLevel() >= 2) {
        $Days = filter_input(INPUT_POST, 'days', FILTER_SANITIZE_NUMBER_INT);
        $RDays = filter_input(INPUT_POST, 'rdays', FILTER_SANITIZE_NUMBER_INT);
        $Buyer = filter_input(INPUT_POST, 'buyer', FILTER_SANITIZE_STRING);
        $Paid = filter_input(INPUT_POST, 'paid', FILTER_SANITIZE_STRING);
        $Trial = ((isset($_POST['trial'])) ? 1 : 0);
        $Amount = ((isset($_POST['amount'])) ? filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_INT) : 1);
        $GenBy = Functions::GetUsername($_SESSION['id']);
        if($Amount > 7) $Amount = 7;
        $arr = array();
        for($x = 0; $x < $Amount; $x++) {
          $arr[] = array('index'=>$x+1, 'token'=>Functions::GenerateToken($Days, $RDays, $Buyer, $Paid, true, $GenBy, $Trial));
        }
        echo json_encode(array('data'=>$arr));
      } else if (Functions::GetLoginLevel() >= 1) {
        $Days = filter_input(INPUT_POST, 'days', FILTER_SANITIZE_NUMBER_INT);
        $RDays = filter_input(INPUT_POST, 'rdays', FILTER_SANITIZE_NUMBER_INT);
        $Buyer = filter_input(INPUT_POST, 'buyer', FILTER_SANITIZE_STRING);
        $Paid = filter_input(INPUT_POST, 'paid', FILTER_SANITIZE_STRING);
        $Trial = ((isset($_POST['trial'])) ? 1 : 0);
        $Amount = ((isset($_POST['amount'])) ? filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_INT) : 1);
        $GenBy = Functions::GetUsername($_SESSION['id']);
        if($Amount > 7) $Amount = 7;
        $arr = array();
        for($x = 0; $x < $Amount; $x++) {
          $arr[] = array('index'=>1, 'token'=>Functions::GenerateToken($Days, $RDays, $Buyer, $Paid, false, $GenBy, $Trial));
        }
        echo json_encode(array('data'=>$arr));
      }
    } else {
      echo json_encode(array('data'=>null, 's'=>"Please Login."));
    }
  }
  
  public static function GenerateAutobuyToken(){
    if(!isset($_POST['days'], $_POST['rdays'])) return;
    if(Functions::IsLoggedIn()) {
      if(Functions::GetLoginLevel() >= 3) {
        $Days = filter_input(INPUT_POST, 'days', FILTER_SANITIZE_NUMBER_INT);
        $RDays = filter_input(INPUT_POST, 'rdays', FILTER_SANITIZE_NUMBER_INT);
        $Amount = ((isset($_POST['amount'])) ? filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_INT) : 0);
        $GenBy = Functions::GetUsername($_SESSION['id']);
        if($Amount > 7) $Amount = 7;
        $arr = array();
        for($x = 0; $x < $Amount; $x++) {
          $arr[] = array('index'=>$x+1, 'token'=>Functions::GenerateToken($Days, $RDays, "Auto-buy", "$0", true, $GenBy, 0));
        }
        echo json_encode(array('data'=>$arr));
      } else {
        echo json_encode(array('data'=>null, 's'=>"No Access."));
      }
    } else {
      echo json_encode(array('data'=>null, 's'=>"Please Login."));
    }
  }
  
  public static function ApproveToken(){
    if(!isset($_POST['id'])) return;
    if(Functions::IsLoggedIn()) {
      if(Functions::GetLoginLevel() >= 2) {
        $ID = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $Val = Functions::ApproveToken($ID);
        echo json_encode(array('error'=>$Val));
        return;
      } else if (Functions::GetLoginLevel() >= 1) {
        header('Location: ../tokens.php');
        return;
      }
    }
    header('Location: ../index.php');
  }

  public static function GetTokens(){
    if(!isset($_POST['page'])) return;
    if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 1) {
      $Page = filter_input(INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT);
      $Sort = ((isset($_POST['sort'])) ? filter_input(INPUT_POST, 'sort', FILTER_SANITIZE_NUMBER_INT) : 0);
      $CPUKey = ((isset($_POST['cpukey'])) ? filter_input(INPUT_POST, 'cpukey', FILTER_SANITIZE_STRING) : "");
      $Token = ((isset($_POST['token'])) ? filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING) : "");
      $GenBy = ((isset($_POST['genby'])) ? filter_input(INPUT_POST, 'genby', FILTER_SANITIZE_STRING) : "");
      $Buyer = ((isset($_POST['buyer'])) ? filter_input(INPUT_POST, 'buyer', FILTER_SANITIZE_STRING) : "");
      echo Functions::GetTokens($Page, $Sort, $CPUKey, $Token, $GenBy, $Buyer);
    } else {
      echo json_encode(array('p'=>-1,'n'=>-1,'c'=>1,'s'=>"Please Login."));
    }
  }
  
  public static function GetKeyvaults(){
    if(!isset($_POST['page'])) return;
    if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2) {
      $Page = filter_input(INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT);
      $Sort = ((isset($_POST['sort'])) ? filter_input(INPUT_POST, 'sort', FILTER_SANITIZE_NUMBER_INT) : 0);
      $CPUKey = ((isset($_POST['cpukey'])) ? filter_input(INPUT_POST, 'cpukey', FILTER_SANITIZE_STRING) : "");
      $Serial = ((isset($_POST['serial'])) ? filter_input(INPUT_POST, 'serial', FILTER_SANITIZE_STRING) : "");
      echo Functions::GetKeyvaults($Page, $Sort, $CPUKey, $Serial);
    } else {
      echo json_encode(array('p'=>-1,'n'=>-1,'c'=>1,'s'=>"Please Login."));
    }
  }

  public static function GetLogs(){
    if (!isset($_POST['page'], $_POST['sort'])) return;
    if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2) {
      $Search = ((isset($_POST['search'])) ? filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING) : "");
      $Sort = filter_input(INPUT_POST, 'sort', FILTER_SANITIZE_NUMBER_INT);
      $Page = filter_input(INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT);
      echo Functions::GetLogs($Page, $Sort, $Search);
    } else {
      echo json_encode(array('p'=>-1,'n'=>-1,'c'=>1,'s'=>"Please Login."));
    }
  }

  public static function GetClients(){
    if(!isset($_POST['page'])) return;
    if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 1) {
      $Page = filter_input(INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT);
      $CPUKey = ((isset($_POST['cpukey'])) ? filter_input(INPUT_POST, 'cpukey', FILTER_SANITIZE_STRING) : "");
      $IP = ((isset($_POST['ip'])) ? filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_STRING) : "");
      $Name = ((isset($_POST['name'])) ? filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING) : "");
      $Gamertag = ((isset($_POST['gamertag'])) ? filter_input(INPUT_POST, 'gamertag', FILTER_SANITIZE_STRING) : "");
      $Sort = ((isset($_POST['sort'])) ? filter_input(INPUT_POST, 'sort', FILTER_SANITIZE_NUMBER_INT) : 0);
      echo Functions::GetClients($Page, $CPUKey, $Name, $Gamertag, $IP, $Sort);
    } else {
      echo json_encode(array('p'=>-1,'n'=>-1,'c'=>1,'s'=>"Please Login."));
    }
  }

  public static function Logout(){
    if($_SESSION['id']){
      $Pdo = Functions::GetDB();
      $ID = $Pdo->quote($_SESSION['id']);
      $Pdo->exec("UPDATE `logins` SET `login_time` = '0', `cookie_expire` = '0' WHERE `id` = $ID");
    }
    $_SESSION = array();
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    session_destroy();
    setcookie("GFq4EuyLG5Prme5g", "", 1);
    header('Location: ../index.php');
  }

  public static function Login(){
    if (!isset($_POST['email'], $_POST['p'], $_POST['tfapin'])) return;
    $Email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $Password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    $TFA = filter_input(INPUT_POST, 'tfapin', FILTER_SANITIZE_STRING);
    $Rem = ((isset($_POST['remember'])) ? true : false);
    if (Functions::Login($Email, $Password, $TFA, $Rem) == true) {
      // Login success
      if (Functions::GetLoginLevel() >= 2){
        header('Location: ../dashboard.php');
      }
      else if (Functions::GetLoginLevel() >= 1){
        header('Location: ../seller.php');
      }
    }
    header('Location: ../index.php');
  }

  public static function Register(){
    if (Functions::GetOption("registration") == 0) {
      header('Location: ../index.php');
      return;
    }
    if (!isset($_POST['username'], $_POST['email'], $_POST['p'], $_POST['regcode'])) {
      header('Location: ../index.php');
      return;
    }
    if ($_POST['regcode'] != Functions::GetOption("registration_code")) {
      header('Location: ../index.php');
      return;
    }
    //filter and sanitize
    $Username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $Email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $Password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    //check email
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
      header('Location: ../register.php?error=Invalid Email!');
    }
    //check password
    if (strlen($Password) != 128) {
      header('Location: ../register.php?error=Invalid Password!');
    }
    //get database instance
    $Pdo = Functions::GetDB();
    $Email = $Pdo->quote($Email);
    $Stmt = $Pdo->query("SELECT `id` FROM `logins` WHERE `email` = $Email LIMIT 1");
    //email exists
    if ($Stmt->rowCount() == 1) {
      header('Location: ../register.php?error=Email Already Exists!');
    }
    //all is good... lets make a gauth secret
    $ga = new GoogleAuthenticator();
    $Secret = $ga->createSecret();
    $SSecret = $Pdo->quote($Secret);
    //now continue register stuff
    $Salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
    $SSalt = $Pdo->quote($Salt);
    $Password = $Pdo->quote(hash('sha512', $Password . $Salt));
    $Name = $Pdo->quote($Username);
    if ($Pdo->exec("INSERT INTO `logins`(`username`, `email`, `password`, `salt`, `level`, `secret`, `enabled`) VALUES($Name, $Email, $Password, $SSalt, 2, $SSecret, 0)")){
      Functions::HandleRegistrationCode();
      $tmp = hash('sha1', $Secret.$Username);
      header('Location: ../twofactor.php?a='.$tmp);
    } else {
      header('Location: ../register.php?error=Registration Failed!');
    }
  }
  
  public static function TwoFactor(){
    if(!isset($_POST['a'], $_POST['tfapin'])) return;
    $A = filter_input(INPUT_POST, 'a', FILTER_SANITIZE_STRING);
    $TFA = filter_input(INPUT_POST, 'tfapin', FILTER_SANITIZE_STRING);
    $B = Functions::Get2FASecret($A);
    if($B == "") header('Location: ../index.php?error=2FA Error!');
    $C = explode('~', $B);
    $ga = new GoogleAuthenticator();
    if($ga->verifyCode($C[0], $TFA, 2)){
      $Pdo = Functions::GetDB();
      $ID = $Pdo->quote($C[2]);
      $Username = $Pdo->quote($C[1]);
      if ($Pdo->exec("UPDATE `logins` SET `enabled`=1 WHERE `id` = $ID AND `username` = $Username")){
        Functions::LogToDB("login", json_encode(array('id'=>$C[2], 'ip'=>Functions::GetIP(), 'error'=>'register_success')));
        header('Location: ../index.php?success=You are now registered!');
      } else {
        header('Location: ../index.php?error=2FA Registration Failed!');
      }
    } else {
      header('Location: ../index.php?error=2FA Failed!');
    }
    
  }

  public static function Reset(){
    if(!isset($_GET['reset_code'])) return;
    $Reset = filter_input(INPUT_GET, 'reset_code', FILTER_SANITIZE_STRING);
    if(Functions::ResetUser($Reset)){
      header("Location: ../index.php?success=Your account has been successfully unlocked.");
    } else {
      header("Location: ../index.php?error=Failed to unlock your account.");
    }
  }

  public static function GetTitleId(){
    $Full = (isset($_GET['full'])) ? true : false;
    $TitleID = (isset($_GET['titleid'])) ? $_GET['titleid'] : "";

    if($TitleID == ""){
      echo json_encode(array("Name"=>"null"));
    }

    $ch = curl_init();

    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, "https://xboxapi.com/v2/game-details-hex/".$TitleID);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Auth: <redacted>', 'Content-Type: application/json'));

    //read json to string
    $response = json_decode(curl_exec($ch), true); //this is to break down response to array for second option

    // close cURL resource, and free up system resources
    curl_close($ch);

    if($Full){
      echo json_encode($response); //full response as json
    } else {
      $name = ($response["Items"][0]["Name"] != null) ? $response["Items"][0]["Name"] : "null";
      $desc = ($response["Items"][0]["ReducedDescription"] != null) ? $response["Items"][0]["ReducedDescription"] : "null";
      $dev = ($response["Items"][0]["DeveloperName"] != null) ? $response["Items"][0]["DeveloperName"] : "null";
      $image = "null";
      foreach ($response["Items"][0]["Images"] as $value) {
        if($value["Purpose"] == "BoxArt" && $value["Height"] == 300){
          $image = $value["Url"];
        }
      }
      echo json_encode(array("Name"=>$name, "Description"=>$desc, "Developer"=>$dev, "Image"=>$image), JSON_UNESCAPED_UNICODE);
    }
  }
  
  public static function SetServerOptions(){
    if(!isset($_POST['version'])) return;
    $Version = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING);
    $Freemode = ((isset($_POST['freemode'])) ? 1 : 0);
    $GHash = ((isset($_POST['genehash'])) ? 1 : 0);
    $XHash = ((isset($_POST['xexhash'])) ? 1 : 0);
    Functions::SetServerOptions($Version, $Freemode, $GHash, $XHash);
    header("Location: ../settings.php");
  }
  
  public static function SetPanelOptions(){
    if(!isset($_POST['regcode'], $_POST['sockio'])) return;
    $Registration = ((isset($_POST['registration'])) ? 1 : 0);
    $GenCode = ((isset($_POST['gencode'])) ? 1 : 0);
    $RegCode = filter_input(INPUT_POST, 'regcode', FILTER_SANITIZE_STRING);
    $SockIO = filter_input(INPUT_POST, 'sockio', FILTER_SANITIZE_STRING);
    Functions::SetPanelOptions($Registration, $GenCode, $RegCode, $SockIO);
    header("Location: ../settings.php");
  }
  
  public static function UploadAvatar(){
    if(!isset($_FILES["picture"])) return;
    $target_dir = str_replace('inc', '', getcwd())."img/avatars/";
    $imageFileType = pathinfo($_FILES["picture"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir.$_SESSION['id'].".".$imageFileType;
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["picture"]["tmp_name"]);
    if($check == false) {
      header("Location: ../editprofile.php?error=File is not an image!");
      return;
    }
    // Check if file already exists
    if (file_exists($target_file)) {
      unlink($target_file);
    }
    // Check file size
    if ($_FILES["picture"]["size"] > 5000000) { //5MB
      header("Location: ../editprofile.php?error=Image file size too big!");
      return;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
      header("Location: ../editprofile.php?error=Only JPG, PNG, and GIF formats allowed!");
      return;
    }
    // Check if $uploadOk is set to 0 by an error
    if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
      $Pdo = Functions::GetDB();
      $ID = $Pdo->quote($_SESSION['id']);
      $URL = $Pdo->quote('img/avatars/'.$_SESSION['id'].".".$imageFileType);
      $Pdo->exec("UPDATE `logins` SET `avatar` = $URL WHERE `id` = $ID");
      header("Location: ../editprofile.php?success=Avatar changed.");
      return;
    } else {
      header("Location: ../editprofile.php?error=Failed to upload image! Error: ".$_FILES["picture"]['error']);
      return;
    }
  }
  
  public static function EditProfile(){
    if(!isset($_POST['email'], $_POST['name'])) return;
    $Email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $Name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $Pdo = Functions::GetDB();
    $ID = $Pdo->quote($_SESSION['id']);
    $Stmt = $Pdo->query("SELECT * FROM `logins` WHERE `id` = $ID LIMIT 1");
    if ($Stmt->rowCount() == 1) {
      $Result = $Stmt->fetch();
      $str = "";
      $comma = "";
      $Options = array();
      //email
      if($Email != $Result['email']) {
        $Options = array_merge($Options, array("email"=>array($Result['email'], $Email)));
        $Email = $Pdo->quote($Email);
        $str = $comma."`email` = $Email";
        $comma = ", ";
      }
      //name
      if($Name != $Result['username']) {
        $Options = array_merge($Options, array("reserve"=>array($Result['username'], $Name)));
        $Name = $Pdo->quote($Name);
        $str = $comma."`username` = $Name";
        $comma = ", ";
      }
      //update
      if(strlen($str) > 0){
        $Pdo->exec("UPDATE `logins` SET ".$str." WHERE `id` = $ID");
        Functions::LogToDB("login", json_encode(array('id'=>$_SESSION['id'], 'ip'=>Functions::GetIP(), 'options'=>$Options, 'error'=>'edit_profile')));
        //logout
        if($_SESSION['id']){
          $Pdo->exec("UPDATE `logins` SET `login_time` = '0', `cookie_expire` = '0' WHERE `id` = $ID");
        }
        $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        session_destroy();
        setcookie("GFq4EuyLG5Prme5g", "", 1);
        header("Location: ../index.php?success=Profile Updated. Please Re-Login.");
        return;
      } else {
        header("Location: ../editprofile.php?warning=No changed to your profile were made.");
        return;
      }
    }
  }
  
  public static function ChangePassword(){
    if(!isset($_POST['p'], $_POST['op'])) return;
    $Password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    $OldPassword = filter_input(INPUT_POST, 'op', FILTER_SANITIZE_STRING);
    
    $Pdo = Functions::GetDB();
    $ID = $Pdo->quote($_SESSION['id']);
    $Stmt = $Pdo->query("SELECT * FROM `logins` WHERE `id` = $ID LIMIT 1");
    if($Stmt->rowCount() == 1) {
      $Result = $Stmt->fetch();
      $OldPassword = hash('sha512', $OldPassword . $Result['salt']);
      if ($Result['password'] != $OldPassword) {
        header("Location: ../editprofile.php?error=Current password is incorrect.");
        return;
      }
      //update pass
      $Salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
      $S_Salt = $Pdo->quote($Salt);
      $Password = $Pdo->quote(hash('sha512', $Password . $Salt));
      if($Pdo->exec("UPDATE `logins` SET `password` = $Password, `salt` = $S_Salt WHERE `id` = $ID") == 1){
        Functions::LogToDB("login", json_encode(array('id'=>$Result['id'], 'ip'=>Functions::GetIP(), 'error'=>'change_password')));
        //logout
        if($_SESSION['id']){
          $Pdo->exec("UPDATE `logins` SET `login_time` = '0', `cookie_expire` = '0' WHERE `id` = $ID");
        }
        $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        session_destroy();
        setcookie("GFq4EuyLG5Prme5g", "", 1);
        header("Location: ../index.php?success=Password Updated. Please Re-Login.");
      } else {
        header("Location: ../editprofile.php?error=Failed to update password.");
      }
    }
  }
  
  public static function Recovery(){
    if(!isset($_POST['email'])) return;
    $Email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $Pdo = Functions::GetDB();
    $S_Email = $Pdo->quote($Email);
    $Stmt = $Pdo->query("SELECT * FROM `logins` WHERE `email` = $S_Email LIMIT 1");
    if($Stmt->rowCount() == 1) {
      $Result = $Stmt->fetch();
      Functions::LogToDB("login", json_encode(array('id'=>$Result['id'], 'ip'=>Functions::GetIP(), 'error'=>'recovery')));
      Functions::Mail($Result['email'], 'TamperedLive Recovery', '');
    }
    header("Location: ../index.php");
  }

  public static function CheckKV(){
    if(!isset($_POST['id'])) return;
    if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2) {
      $ID = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
      $KVObject = new KV($ID);
      $Result = $KVObject->Check();
      //update in db
      $Pdo = Functions::GetDB();
      $S_ID = $Pdo->quote($ID);
      $S_Result = $Pdo->quote($Result);
      $Pdo->exec("UPDATE `clients` SET `kvstatus` = $S_Result WHERE `id` = $S_ID");
      //return pretty value
      $KVObject->SetStatus($Result);
      echo json_encode(array('result' => $KVObject->GetStatus()));
    }
  }
}

Handler::Handle();