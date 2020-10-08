<?php
include_once 'inc/functions.php';
require 'inc/geoip2.phar';
use GeoIp2\WebService\Client;
 
Functions::SecStart();
if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2) :

$Pdo = Functions::GetDB();

$Client = new Client(119164, 'CGAwuW8jCqIm');

//find any not filled in
$Stmt = $Pdo->query('SELECT * FROM `clients` WHERE `latlong` = "" LIMIT 50');
if($Stmt->rowCount() > 0){
  $Results = $Stmt->fetchAll();
  foreach($Results as $Result){
    $DoGeo = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $Result['ip']);
    if($DoGeo){
      $SID = $Pdo->quote($Result['id']);
      try {
        $GeoData = $Client->city($Result['ip']);
        $SLatLong = $Pdo->quote($GeoData->location->latitude . ',' . $GeoData->location->longitude);
        $Pdo->exec("UPDATE `clients` SET `latlong` = $SLatLong WHERE `id` = $SID");
      } catch (Exception $e) {
        $GeoData = json_decode(file_get_contents("http://ipinfo.io/".$Result['ip']."/json"));
        $SLatLong = $Pdo->quote($GeoData->loc);
        $Pdo->exec("UPDATE `clients` SET `latlong` = $SLatLong WHERE `id` = $SID");
      }
    }
  }
}

//make da list
$Markers = '';

$Stmt = $Pdo->query('SELECT `latlong`, `name`, `cpukey`, `id` FROM `clients` WHERE `latlong` != ""');
if($Stmt->rowCount() > 0){
  $Results = $Stmt->fetchAll();
  $Comma = '';
  $I = 0;
  foreach($Results as $Result){
    $Markers .= $Comma . "{ id: ".$I.", cid: ".$Result['id'].", latLng : [".$Result['latlong']."], name : '".$Result['name'] ." - ". $Result['cpukey'] . "' }";
    $Comma = ',';
    $I++;
  }
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

    <title>TamperedLive - Viewing User Map</title>


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
    <link href="css/custombox.min.css" rel="stylesheet" type="text/css" />
    <link href="css/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
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
              <li class="has-submenu">
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
              <li class="has-submenu active">
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
          <div class="col-md-12" align="right">
            <form class="form-inline" role="form" onsubmit="return false;" style="margin-bottom:20px;">
              <div class="form-group" style="padding-bottom:20px;">
                <label class="m-r-10">Search</label>
                <input type="text" name="search_s" class="form-control" style="margin-right:20px;">
              </div>
              <div class="form-group" style="padding-bottom:20px;">
                <a href="maps.php" onclick="searchMap();return false;" style="margin:0px 20px;" class="btn btn-purple waves-effect waves-light">Search</a>
              </div>
            </form>
          </div>
        </div> 
        <div class="row">
          <div class="col-md-12">
            <div class="card-box">
              
              <div id="world-map-markers" style="height: 600px;"></div>
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
    <script src="js/jquery.twbsPagination.min.js"></script>
    <script src="js/bootstrap-tagsinput.min.js"></script>
    <script src="js/bootstrap-inputmask.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/jquery.bootstrap-touchspin.min.js"></script>
    <script src="js/parsley.min.js"></script>
    
    <!-- Counter Up  -->
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>

    <!-- circliful Chart -->
    <script src="js/jquery.circliful.min.js"></script>
    <script src="js/jquery.sparkline.min.js"></script>
    
    <!-- Custom main Js -->
    <script src="js/jquery.core.js"></script>
    <script src="js/jquery.app.js"></script>
    
    <!-- Modal-Effect -->
    <script src="js/custombox.js"></script>
    <script src="js/legacy.min.js"></script>
    
    <!-- Vector Map -->
    <script src="js/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="js/jquery-jvectormap-world-mill-en.js"></script>
    <script src="js/gdp-data.js"></script>
    <script src="js/jquery-jvectormap-us-aea-en.js"></script>
    <script src="js/jquery-jvectormap-uk-mill-en.js"></script>
    <script src="js/jquery-jvectormap-au-mill.js"></script>
    <script src="js/jquery-jvectormap-us-il-chicago-mill-en.js"></script>
    <script src="js/jquery-jvectormap-ca-lcc.js"></script>
    <script src="js/jquery-jvectormap-de-mill.js"></script>
    <script src="js/jquery-jvectormap-in-mill.js"></script>
    <script src="js/jquery-jvectormap-asia-mill.js"></script>
    
    <script type="text/javascript">
      var map;
      var markers = [<?php echo $Markers; ?>];
      $(function(){
        //search
        $('input[name="search_s"]').keyup(function(e){
          if(e.keyCode == 13) { $(this).trigger("enterKey"); }
        });
        $('input[name="search_s"]').bind("enterKey",function(e){
          searchMap();
        });
        map = new jvm.Map({
          container: $('#world-map-markers'),
          map : 'world_mill_en',
          normalizeFunction : 'polynomial',
          hoverOpacity : 0.7,
          hoverColor : false,
          zoomMax: 32,
          regionStyle : {
            initial : {
              fill : '#3bafda'
            }
          },
          markerStyle: {
            initial: {
              r: 5,
              'fill': '#7266ba',
              'fill-opacity': 0.9,
              'stroke': '#6254b2',
              'stroke-width' : 2,
              'stroke-opacity': 0.4
            },
            hover: {
              'stroke': '#fff',
              'fill-opacity': 1,
              'stroke-width': 1.5
            },
            selected: {
              r:9,
              fill: 'red'
            }
          },
          backgroundColor : 'transparent',
          markers : markers
        });
      });
      function searchMap(){
        map.clearSelectedMarkers();
        search = $('input[name="search_s"]').val().toLowerCase();
        if(search == "") return;
        for (var i = 0; i < markers.length; i++) {
          if(markers[i].name.toLowerCase().includes(search) || parseInt(search) == markers[i].cid){
            map.setSelectedMarkers([i]);
            map.setFocus({scale: 16, lat: String(markers[i].latLng).split(",")[0], lng: String(markers[i].latLng).split(",")[1]});
            return;
          }
        }
      }
    </script>
  </body>
</html>
<?php else :
  header('Location: index.php'); 
endif; ?>