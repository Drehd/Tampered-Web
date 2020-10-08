<?php
//start api
if (isset($_GET['action'])){
  //lowercase action
  $action = strtolower($_GET['action']);
  if($action == "gettitleinfo"){
    $full = (isset($_GET['full'])) ? $_GET['full'] : 0;
    $titleid = (isset($_GET['titleid'])) ? $_GET['titleid'] : "";
    echo getTitleInfo($titleid, $full);
  }
  


}

function getTitleInfo($titleid, $full){
  if($titleid == ""){
    return json_encode(array("Name"=>"null"));
  }
  
  
  $ch = curl_init();

  // set URL and other appropriate options
  curl_setopt($ch, CURLOPT_URL, "https://xboxapi.com/v2/game-details-hex/".$titleid);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Auth: <redacted>', 'Content-Type: application/json'));

  //read json to string
  $response = json_decode(curl_exec($ch), true); //this is to break down response to array for second option

  // close cURL resource, and free up system resources
  curl_close($ch);

  if($full == 1 || $full == "1"){
    return json_encode($response); //full response as json
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
    //Name, ReducedDescription, Developer, Image
    
    return json_encode(array("Name"=>$name, "Description"=>$desc, "Developer"=>$dev, "Image"=>$image), JSON_UNESCAPED_UNICODE); //just name
  }
}




?>