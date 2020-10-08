<?php

include_once "functions.php";

class KV {

  private static $Pdo;
  private static $IsValid = false;
  private static $KVString;
  private static $KVSerial;
  private static $KVArray;
  private static $KVStatus;

  /**
   * KV constructor.
   * @param int $ClientID
   */
  public function __construct(int $ClientID){
    self::$Pdo = Functions::GetDB();
    $ID = self::$Pdo->quote($ClientID);
    $Stmt = self::$Pdo->query("SELECT `kv`, `kvserial`, `kvstatus` FROM `clients` WHERE `id` = $ID  LIMIT 1");
    if($Stmt->rowCount() != 1) return;
    $Result = $Stmt->fetch();
    if($Result['kv'] == null || strlen($Result['kvserial']) < 0) return;
    self::$KVString = $Result['kv'];
    self::$KVSerial = $Result['kvserial'];
    self::$KVArray = $Result['kv'];
    self::$KVStatus = $Result['kvstatus'];
    self::$IsValid = true;
  }

  public function IsValid() : bool {
    return self::$IsValid;
  }

  public function GetSerial() : string {
    return self::$KVSerial;
  }

  public function GetType() : int {
    $KVSig = array_values(unpack('C*', substr(self::$KVArray, 0x1DF8, 0x100)));
    $count = 0;
    for($x = 0; $x < 0xFF; $x++){
      if($KVSig[$x] == 0){
        $count++;
      }
    }
    if($count > 0x50) return 1;
    else return 2;
  }

  public function GetRegion() : string {
    $Type = array_values(unpack('n*', substr(self::$KVArray, 0xC8, 0x2)))[0];
    if($Type == 0xFF){
      return "NTSC-U";
    } else if ($Type == 0x1FC){
      return "NTSC-KOR";
    } else if ($Type == 0x1FE){
      return "NTSC-J";
    } else if ($Type == 0x201){
      return "PAL-AUS";
    } else if ($Type == 0x2FE){
      return "PAL-EU";
    } else {
      return "Unknown";
    }
  }

  public function GetConsoleType() : string {
    $Type = hexdec(substr(self::$KVArray, 0x9D1, 0x2));
    if($Type < 0x10) return "Xenon";
    else if($Type < 0x14) return "Zephyr";
    else if($Type < 0x18) return "Falcon";
    else if($Type < 0x52) return "Jasper";
    else if($Type < 0x58) return "Trinity";
    else return "Corona";
  }

  public function GetConsoleID() : string {
    return strtoupper(array_values(unpack('H*', substr(self::$KVArray, 0x9CA, 0x5)))[0]);
  }

  public function GetDVDKey() : string {
    return strtoupper(array_values(unpack('H*', substr(self::$KVArray, 0x100, 0x10)))[0]);
  }

  public function GetManufactureDate() : string {
    return substr(self::$KVArray, 0x9E4, 0x8)." (".substr(self::$KVArray, 0x9D0, 0xA).")";
  }

  public function GetDriveType(){
    return preg_replace("/[^A-Za-z0-9- ]/", '', substr(self::$KVArray, 0xC92, 0x1C));
  }

  public function Check() : int {
    $Exec = 'java -jar "/TLKVC/CheckKV.jar" "'.array_values(unpack('H*', substr(self::$KVArray, 0xB0, 0xC)))[0] . ' ' . array_values(unpack('H*', substr(self::$KVArray, 0x29C, 0x4)))[0] . ' ' . array_values(unpack('H*', substr(self::$KVArray, 0x2A8, 0x1C0)))[0] . ' ' . array_values(unpack('H*', substr(self::$KVArray, 0x9C8, 0x1A8)))[0] . '"';
    $Result = exec($Exec);
    if ($Result == "true") return 2;
    else if ($Result == "false") return 1;
    else return 0;
  }

  public function GetStatus() : string {
    if(self::$KVStatus == 1) {
      return '<span class="hashbad">Banned</span>';
    }
    else if(self::$KVStatus == 2) {
      return '<span class="hashgood">Unbanned</span>';
    }
    else if(self::$KVStatus == 3) {
      return '<span class="hashbad">Invalid</span>';
    }
    else {
      return '<span class="hashbad">Error</span>';
    }
  }
  
  public function GetUsedOn(){
    $Serial = self::$Pdo->quote(self::$KVSerial);
    $Stmt = self::$Pdo->query("SELECT COUNT(*) as `count` FROM `clients` WHERE `kvserial` = $Serial  LIMIT 1");
    if($Stmt->rowCount() != 1) return 0;
    $Result = $Stmt->fetch();
    return $Result['count'];
  }

  public function SetStatus(int $Status) {
    self::$KVStatus = $Status;
  }
}