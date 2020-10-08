<?php
include_once 'inc/functions.php';
require 'inc/geoip2.phar';
use GeoIp2\WebService\Client;
 
Functions::SecStart();
if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2) :
if(!isset($_GET['id'])) header('Location: clients.php'); 

//filter input
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

//get db
$Pdo = Functions::GetDB();

$SID = $Pdo->quote($id);
$Stmt = $Pdo->query("SELECT * FROM `clients` WHERE `id` = $SID  LIMIT 1");
if($Stmt->rowCount() != 1) header('Location: clients.php?error=Client Doesn\'t Exist'); 
//get results
$Result = $Stmt->fetch();

$date1 = new DateTime();
$date1->setTimezone(new DateTimeZone('America/New_York'));
$date1->setTimestamp($Result['remaining']);

$ExpDate = $date1->format("m/d/Y");
$ExpTime = $date1->format("h:i A");

$Version = Functions::GetOption("version");

$CVersion = '<span class="version'. (($Result['version'] == $Version) ? 'good' : (($Result['version'] > $Version) ? "dev" : "bad")).'">'.$Result['version'].'</span>';

$TitleInfo = Functions::GetTitleInfo($Result['titleid']);

$KVObject = new KV($id);

if($KVObject->IsValid()) {
  //kv start date
  $kvdate = new DateTime();
  $kvdate->setTimestamp($Result['kvstart']);
  $KVStartDate = $kvdate->format("M j, Y @ g:i a");
}


$DoGeo = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $Result['ip']);

if($DoGeo){
  
  $GeoData2 = json_decode(file_get_contents("http://ipinfo.io/".$Result['ip']."/json"));
  
  $Client = new Client(119164, 'CGAwuW8jCqIm');
  $GeoData = $Client->city($Result['ip']);
  $LatLong = [ $GeoData->location->latitude, $GeoData->location->longitude];
  $SLatLong = $Pdo->quote($GeoData->location->latitude . ',' . $GeoData->location->longitude);
  $Pdo->exec("UPDATE `clients` SET `latlong` = $SLatLong WHERE `id` = $SID");
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TamperedLive Dashboard">
    <meta name="author" content="HaXzz">
    

    <link rel="shortcut icon" href="img/favicon.png">

    <title>TamperedLive - Edit Client #<?php echo $Result['id']; ?></title>


    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/core.css" rel="stylesheet" type="text/css" />
    <link href="css/components.css" rel="stylesheet" type="text/css" />
    <link href="css/icons.css" rel="stylesheet" type="text/css" />
    <link href="css/pages.css" rel="stylesheet" type="text/css" />
    <link href="css/menu.css" rel="stylesheet" type="text/css" />
    <link href="css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="css/jquery.circliful.css" rel="stylesheet" type="text/css" />
    <link href="css/dashboard.css" rel="stylesheet" type="text/css" />
    <link href="css/sweet-alert.css" rel="stylesheet" type="text/css" />
    <link href="css/switchery.min.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="css/select2.css" rel="stylesheet" type="text/css" />
    <link href="css/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Source+Code+Pro" rel="stylesheet">

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="js/modernizr.min.js"></script>
  </head>
  <body>
    <!-- Navigation Bar-->
    <header id="topnav">
      <div class="topbar-main">
        <div class="container">
          <!-- Logo container-->
          <div class="logo">
            <a href="dashboard.php" class="logo"><i class="md md-equalizer"></i> <span>TamperedLive</span> </a>
          </div>
          <!-- End Logo container-->
          <div class="menu-extras">
            <ul class="nav navbar-nav navbar-right pull-right">
              <li>
                <form role="search" class="navbar-left app-search pull-left hidden-xs">
                  <input type="text" placeholder="Search..." class="form-control">
                  <a href=""><i class="fa fa-search"></i></a>
                </form>
              </li>
              <li class="dropdown">
                <a href="" class="dropdown-toggle waves-effect waves-light profile" data-toggle="dropdown" aria-expanded="true"><img src="<?php echo Functions::GetAvatar(); ?>" alt="user-img" class="img-circle"> </a>
                <ul class="dropdown-menu">
                  <li><a href="editprofile.php"><i class="ti-user m-r-5"></i> Profile</a></li>
                  <li><a onclick="$('#logout').submit();" href="javascript:void(0)"><i class="ti-power-off m-r-5"></i> Logout</a></li>
                  <form id="logout" name="logout" action="inc/handler.php" method="POST"><input hidden name="func" value="logout"></form>
                </ul>
              </li>
            </ul>
            <div class="menu-item">
              <!-- Mobile menu toggle-->
              <a class="navbar-toggle">
                <div class="lines">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </a>
            <!-- End mobile menu toggle-->
            </div>
          </div>
        </div>
      </div>
      <!-- End topbar -->
      <!-- Navbar Start -->
      <div class="navbar-custom">
        <div class="container">
          <div id="navigation">
            <!-- Navigation Menu-->
            <ul class="navigation-menu">
              <li class="has-submenu">
                <a href="dashboard.php"><i class="md md-dashboard"></i>Dashboard</a>
              </li>
              <li class="has-submenu active">
                <a href="clients.php"><i class="md md-account-child"></i>Clients</a>
              </li>
              <li class="has-submenu">
                <a href="tokens.php"><i class="md md-stars"></i>Tokens</a>
              </li>
              <li class="has-submenu">
                <a href="keyvaults.php"><i class="md md-vpn-key"></i>Keyvaults</a>
              </li>
              <li class="has-submenu">
                <a href="settings.php"><i class="md md-settings"></i>Settings</a>
              </li>
              <?php if (Functions::GetLoginLevel() >= 3) { ?> 
              <li class="has-submenu">
                <a href="team.php"><i class="md md-perm-identity"></i>Team</a>
              </li>
              <?php } ?>
              <li class="has-submenu">
                <a href="map.php"><i class="md md-map"></i>Map</a>
              </li>
              <li class="has-submenu">
                <a href="logs.php"><i class="md md-assignment"></i>Logs</a>
              </li>
            </ul>
            <!-- End navigation menu -->
          </div> <!-- end #navigation -->
        </div> <!-- end container -->
      </div> <!-- end navbar-custom -->
    </header>
    <!-- End Navigation Bar-->
    <!-- =======================
         ===== START PAGE ======
         ======================= -->
    <div class="wrapper">
      <div class="container">
        <!-- Page-Title -->
        <div class="row">
          <div class="col-sm-12">
            <h4 class="page-title">Welcome <?php echo htmlentities($_SESSION['name']); ?>!</h4>
          </div>
        </div>
        <!-- end Page-Title -->
        
        <div class="row">
          <div class="col-md-6">
            <div class="card-box">
              <h4 class="text-dark header-title m-t-0">Edit Client </h4>
              <form class="form-horizontal" role="form" action="inc/handler.php" method="post">
                <input type="text" hidden name="id" value="<?php echo $id; ?>">
                <input type="text" hidden name="func" value="editClient">
                <div class="form-group">
	                <label class="col-sm-2 control-label">Name</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" value="<?php echo $Result['name']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">CPUKey</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control" readonly name="cpukey" value="<?php echo $Result['cpukey']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Genealogy</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control" name="genealogy" value="<?php echo $Result['genealogy_hash']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Email</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control" autocomplete="off" name="email" value="<?php echo $Result['email']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">IP</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control" readonly value="<?php echo $Result['ip']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Fails</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control ts-up-down" name="fails" value="<?php echo $Result['fails']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Expire Day</label>
	                <div class="col-sm-10 input-group remove-padding">
                    <input type="text" class="form-control dp-autoclose" name="expday" value="<?php echo $ExpDate; ?>">
                    <span class="input-group-addon bg-primary b-0 text-white"><i class="ion-calendar"></i></span>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Expire Time</label>
	                <div class="col-sm-10 input-group remove-padding">
                    <input type="text" class="form-control tp-12" name="exptime">
                    <span class="input-group-addon bg-primary text-white"><i class="glyphicon glyphicon-time"></i></span>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Reserve Days</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control ts-up-down" name="rdays" value="<?php echo $Result['days']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Lifetime</label>
	                <div class="col-sm-10">
                    <input type="checkbox" name="lifetime" data-plugin="switchery" data-color="#ffd865" data-size="small" <?php if($Result['lifetime'] == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Blacklisted</label>
	                <div class="col-sm-10">
                    <input type="checkbox" name="blacklisted" data-plugin="switchery" data-color="#ff4d4d" data-size="small" <?php if($Result['blacklisted'] == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Developer</label>
	                <div class="col-sm-10">
                    <input type="checkbox" name="developer" data-plugin="switchery" data-color="#c266ff" data-size="small" <?php if($Result['developer'] == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Used Trial</label>
	                <div class="col-sm-10">
                    <input type="checkbox" name="usedtrial" data-plugin="switchery" data-color="#00b19d" data-size="small" <?php if($Result['usedtrial'] == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Notes</label>
	                <div class="col-sm-10">
                    <textarea class="form-control" rows="5" name="notes"><?php echo $Result['notes']; ?></textarea>
	                </div>
	              </div>
                <div class="form-group" align="right" style="margin-right:20px">
                  <button type="submit" class="btn btn-purple waves-effect waves-light">Save</button>
                </div>
              </form>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card-box">
              <h4 class="text-dark header-title m-t-0">Client Info</h4>
              <div class="panel panel-color panel-purple">
                <div class="panel-heading">
                  <h3 class="panel-title">Technical Info</h3>
                </div>
                <div class="panel-body">
                  <p>
                    XEX Hash: <span class="white xexhash"><?php echo ((strlen($Result['xexhash']) > 0) ? $Result['xexhash'] : "-"); ?></span><br>
                    Version: <span class="white"><?php echo $CVersion; ?></span><br>
                    Time Used: <span class="white"><?php echo Functions::GetRelativeTime($Result['totaltimeused']); ?></span><br>
                    Gamertag: <span class="white"><?php echo ((strlen($Result['gamertag']) > 0) ? $Result['gamertag'] : "N/A"); ?></span><br>
                  </p>
                </div>
              </div>
              <?php if(!empty($TitleInfo)){ ?>
              <div class="panel panel-color panel-primary">
                <div class="panel-heading">
                  <h3 class="panel-title">Title Info: <?php echo $TitleInfo['Title']; ?></h3>
                </div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-5">
                      <img src="<?php echo $TitleInfo['Picture']; ?>"/>
                    </div>
                    <div class="col-md-7">
                      <center><h3><?php echo $TitleInfo['Title1']; ?></h3>
                      <p style="text-indent:20px;"><?php echo $TitleInfo['Description']; ?></p>
                      <br>
                      <h4>Developer:</h4>
                      <p><?php echo $TitleInfo['Developer']; ?></p></center>
                    </div>
                  </div>
                </div>
              </div>
              <?php } if($KVObject->IsValid()){ ?>
              <div class="panel panel-color panel-success">
                <div class="panel-heading">
                  <h3 class="panel-title">Keyvault Info</h3>
                </div>
                <div class="panel-body">
                  <p>
                    Type: <span class="white"><?php echo $KVObject->GetType(); ?></span><br>
                    Region: <span class="white"><?php echo $KVObject->GetRegion(); ?></span><br>
                    Serial: <span class="white kvserial"><?php echo $KVObject->GetSerial(); ?></span><br>
                    Console ID: <span class="white"><?php echo $KVObject->GetConsoleID(); ?></span><br>
                    Manufacture Date: <span class="white"><?php echo $KVObject->GetManufactureDate(); ?></span><br>
                    Drive Info: <span class="white"><?php echo $KVObject->GetDriveType(); ?></span><br>
                    DVD Key: <span class="white"><?php echo $KVObject->GetDVDKey(); ?></span><br>
                    Console Type: <span class="white"><?php echo $KVObject->GetConsoleType(); ?></span><br><br>
                    Start Time: <span class="white"><?php echo $KVStartDate; ?></span><br>
                    KV Used On: <span class="white"><?php echo $KVObject->GetUsedOn(); ?> Console(s)</span><br>
                    Status: <span id="kvstatus" class="white"><?php echo $KVObject->GetStatus(); ?></span> <span style="cursor:pointer;" onclick="UpdateKVStatus();" class="white">(Check)</span><br>
                  </p>
                </div>
              </div>
              <?php } if($DoGeo){ ?>
              <div class="panel panel-color panel-warning">
                <div class="panel-heading">
                  <h3 class="panel-title">Geolocation for <?php echo $Result['ip']; ?></h3>
                </div>
                <div class="panel-body">
                  <p>
                    Location: <span class="white"><?php echo $GeoData->city->name.", ".$GeoData->mostSpecificSubdivision->name." ".$GeoData->country->name." ".$GeoData->postal->code; ?></span><br>
                    ISP: <span class="white"><?php echo $GeoData2->org; ?></span><br>
                    Hostname: <span class="white"><?php echo $GeoData2->hostname; ?></span><br>
                    <br>
                    <div id="gmap-client" class="gmaps"></div>
                  </p>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <!-- end row -->
      </div> 
      <!-- end container -->
    </div>
    <!-- End wrapper -->

    <!-- jQuery  -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/detect.js"></script>
    <script src="js/fastclick.js"></script>
    <script src="js/jquery.blockUI.js"></script>
    <script src="js/waves.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/jquery.nicescroll.js"></script>
    <script src="js/jquery.scrollTo.min.js"></script>
    <script src="js/switchery.min.js"></script>
    <script src="js/sweet-alert.min.js"></script>
    <script src="js/bootstrap-tagsinput.min.js"></script>
    <script src="js/bootstrap-inputmask.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/jquery.bootstrap-touchspin.min.js"></script>
    <script src="js/moment.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/bootstrap-timepicker.min.js"></script>
    
    <!-- Counter Up  -->
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>

    <!-- circliful Chart -->
    <script src="js/jquery.circliful.min.js"></script>
    <script src="js/jquery.sparkline.min.js"></script>
    
    <!-- Custom main Js -->
    <script src="js/jquery.core.js"></script>
    <script src="js/jquery.app.js"></script>
    
    <!-- google maps api -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?&key=AIzaSyDBWACO_BiaPWWecps9kQnz2gw_uVmu55Y&callback=initMap"></script>

    <script type="text/javascript">
      $(function(){
        $(".ts-up-down").TouchSpin({
          buttondown_class: "btn btn-primary",
          buttonup_class: "btn btn-primary"
        });
        $('.dp-autoclose').datepicker({
          autoclose: true,
          format: 'mm/dd/yyyy',
          todayHighlight: true,
          disableTouchKeyboard: true
        });
        $('.tp-12').timepicker({
          defaultTime: '<?php echo $ExpTime; ?>',
          disableFocus: true,
          minuteStep: 5
				});
      });
      <?php if($DoGeo) { ?>
      function initMap() {
        var center = {lat: <?php echo $LatLong[0]; ?>, lng: <?php echo $LatLong[1]; ?>};
        var map = new google.maps.Map(document.getElementById('gmap-client'), {
          zoom: 8,
          center: center
        });
        var marker = new google.maps.Marker({
          position: center,
          map: map
        });
      }
      <?php } ?>
      function UpdateKVStatus() {
        $('#kvstatus').html('Fetching Status...');
        $.post("inc/handler.php", {func: "checkKV", id: <?php echo $id; ?>}, function (data) {
          if (data != null || data != undefined){
            $('#kvstatus').html(data.result);
          }
        }, 'json');
      }
    </script>
  </body>
</html>
<?php else :
  header('Location: index.php'); 
endif; ?>