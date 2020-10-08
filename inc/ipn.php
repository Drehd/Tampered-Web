<?php

include_once 'functions.php';

Functions::SecStart();

class IPN {
  
  private static $Secret = '<redacted>';
  private static $Merchant = "<redacted>"; //mechant
  
  function GetPackage(int $Num){
    $Amount = 0.00;
    $Name = 'No Package Selected';
    $Days = 0;
    
    if ($Num == 209640){
      $Amount = 7.50;
      $Name = '1 Day Token';
      $Days = 1;
    }
    else if ($Num == 912990){
      $Amount = 20.00;
      $Name = '1 Week Token';
      $Days = 7;
    }
    else if ($Num == 391993){
      $Amount = 40.00;
      $Name = '2 Week Token';
      $Days = 14;
    }
    else if ($Num == 560197){
      $Amount = 80.00;
      $Name = '1 Month Token';
      $Days = 30;
    }
    
   return array(
      'amount' => $Amount,
      'item_name' => $Name,
      'item_number' => $Num,
      'days' => $Days);
  }
  
  public static function DoIPN() : bool {
    if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
      error_log("Error: 0x0001", 0);
      return false;
    }

    $request = file_get_contents('php://input');
    if ($request === FALSE || empty($request)) {
      error_log("Error: 0x0002", 0);
      return false;
    }

    $merchant = isset($_POST['merchant']) ? $_POST['merchant']:'';
    if (empty($merchant)) {
      error_log("Error: 0x0003", 0);
      return false;
    }
    
    if ($merchant != IPN::$Merchant) {
      error_log("Error: 0x0004", 0);
      return false;
    }

    $hmac = hash_hmac("sha512", $request, IPN::$Secret);
    if ($hmac != $_SERVER['HTTP_HMAC']) {
      error_log("Error: 0x0005", 0);
      return false;
    }
    
    //ipn values
    $ipn_version = $_POST['ipn_version'];
    $ipn_type = $_POST['ipn_type'];
    $ipn_id = $_POST['ipn_id']; 
    $ipn_mode = $_POST['ipn_mode'];

    //transaction
    $txn_id = $_POST['txn_id']; 
    
    //status`
    $status = intval($_POST['status']);
    $status_text = $_POST['status_text'];
    
    //currencies
    $currency1 = $_POST['currency1']; 
    $currency2 = $_POST['currency2'];  
    
    //amounts
    $amount1 = floatval($_POST['amount1']); 
    $amount2 = floatval($_POST['amount2']); 
    
    //fee
    $fee = $_POST['fee'];  
    
    //buyer info
    $buyer_name = $_POST['buyer_name'];  
    
    //email
    $email = $_POST['email']; 
    
    //item
    $item_name = $_POST['item_name']; 
    $item_number = $_POST['item_number']; 
    
    //form email (the client)
    $custom = $_POST['custom']; 
    
    //received
    $rcv_amount = $_POST['received_amount'];
    $rcv_confirms = $_POST['received_confirms'];
    
    IPN::LogToDatabase($ipn_version, $ipn_type, $ipn_id, $ipn_mode, $txn_id, $status, $status_text, $currency1, $currency2, $amount1, $amount2, $fee, $buyer_name, $email, $item_name, $item_number, $custom, $rcv_amount, $rcv_confirms);
    
    $Item = IPN::GetPackage(intval($item_number));
    
    if ($currency1 != "USD") { 
      error_log("Error: 0x0006", 0);
      return false;
    } 

    if ($currency2 != "BTC") { 
      error_log("Error: 0x000A", 0);
      return false;
    }
    
    if ($amount1 < $Item['amount']) { 
      error_log("Error: 0x0007", 0); 
      return false;
    }
    
    if ($ipn_type != 'api') { 
      error_log("Error: 0x000B", 0); 
      return false;
    }
    
    if ($rcv_confirms < 2) { 
      error_log("Error: 0x000C", 0); 
      return false;
    }

    if ($status >= 100) { 
      //do ipn shit here
      $Token = Functions::GenerateToken($Item['days'], 0, $custom, "$".$amount1, true, "Bitcoin Auto-buy");
      IPN::AddTokenDB($txn_id, $Token);
      
      //send the email
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "http://<redacted>/mailer.php?key=t3wnr92W3Jz3edhFgEamfPMLvyYqUTGV&token=".$Token."&email=".$custom);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_exec($ch);
      curl_close($ch);
      
      return true;
    } else if ($status < 0) { 
      error_log("Error: 0x0008", 0);
      return false;
    } else { 
      error_log("Error: 0x0009", 0);
      return false;
    } 
  }
  
  public static function AddTokenDB($txn_id, $token){
    $Pdo = Functions::GetDB();
    $TID = $Pdo->quote($txn_id);
    $Token = $Pdo->quote($token);
    $Pdo->exec("UPDATE `transactions` SET `token`=$Token WHERE `txn_id` = $TID");
  }
  
  public static function LogToDatabase($ipn_version, $ipn_type, $ipn_id, $ipn_mode, $txn_id, $status, $status_text, $currency1, $currency2, $amount1, $amount2, $fee, $buyer_name, $email, $item_name, $item_number, $custom, $rcv_amount, $rcv_confirms) {
    $Pdo = Functions::GetDB();
    //ipn
    $IPN_Ver = $Pdo->quote($ipn_version);
    $IPN_Type = $Pdo->quote($ipn_type);
    $IPN_ID = $Pdo->quote($ipn_id);
    $IPN_Mode = $Pdo->quote($ipn_mode);
    //transaction
    $TID = $Pdo->quote($txn_id);
    //status
    $Status = $Pdo->quote($status);
    $SText = $Pdo->quote($status_text);
    //currencies
    $Cur1 = $Pdo->quote($currency1);
    $Cur2 = $Pdo->quote($currency2);
    //amounts
    $Amt1 = $Pdo->quote($amount1);
    $Amt2 = $Pdo->quote($amount2);
    //fee
    $Fee = $Pdo->quote($fee);
    //buyer
    $BName = $Pdo->quote($buyer_name);
    //email
    $Email = $Pdo->quote($email);
    //item
    $INum = $Pdo->quote($item_number);
    $IName = $Pdo->quote($item_name);
    //custom
    $Custom = $Pdo->quote($custom);
    //receive
    $RcvAmount = $Pdo->quote($rcv_amount);
    $RcvCon = $Pdo->quote($rcv_confirms);
    //check is in db
    $Stmt = $Pdo->query("SELECT `id` FROM `transactions` WHERE `txn_id` = $TID LIMIT 1");
    if ($Stmt->rowCount() == 1) {
      //update
      $Pdo->exec("UPDATE `transactions` SET `ipn_version`=$IPN_Ver,`ipn_type`=$IPN_Type,`ipn_id`=$IPN_ID,`ipn_mode`=$IPN_Mode,`status`=$Status,`status_text`=$SText,`currency1`=$Cur1,`currency2`=$Cur2,`amount1`=$Amt1,`amount2`=$Amt2,`fee`=$Fee,`buyer_name`=$BName,`email`=$Email,`item_name`=$IName,`item_number`=$INum,`custom`=$Custom,`received_amount`=$RcvAmount,`received_confirms`=$RcvCon WHERE `txn_id` = $TID");
    }
    else {
      //insert
      $Pdo->exec("INSERT INTO `transactions`(`ipn_version`, `ipn_type`, `ipn_id`, `ipn_mode`, `txn_id`, `status`, `status_text`, `currency1`, `currency2`, `amount1`, `amount2`, `fee`, `buyer_name`, `email`, `item_name`, `item_number`, `custom`, `received_amount`, `received_confirms`, `token`) VALUES ($IPN_Ver,$IPN_Type,$IPN_ID,$IPN_Mode,$TID,$Status,$SText,$Cur1,$Cur2,$Amt1,$Amt2,$Fee,$BName,$Email,$IName,$INum,$Custom,$RcvAmount,$RcvCon,'')");
    }
  }
}

IPN::DoIPN();

?>