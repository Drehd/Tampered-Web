<?php
include_once 'inc/functions.php';
 
Functions::SecStart();
if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2) :
if(!isset($_GET['id'])) header('Location: tokens.php'); 

//filter input
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

//get db
$Pdo = Functions::GetDB();

$SID = $Pdo->quote($id);
$Stmt = $Pdo->query("SELECT * FROM `tokens` WHERE `id` = $SID  LIMIT 1");
if($Stmt->rowCount() != 1) header('Location: tokens.php?error=Token Doesn\'t Exist'); 
//get results
$Result = $Stmt->fetch();

$date1 = new DateTime();
$date1->setTimezone(new DateTimeZone('America/New_York'));
$date1->setTimestamp($Result['generated_date']);
$GenDate = $date1->format("m/d/y g:i a");


$RedDate = "";
if($Result['redeemed_date'] > 0){
  $date2 = new DateTime();
  $date2->setTimezone(new DateTimeZone('America/New_York'));
  $date2->setTimestamp($Result['redeemed_date']);
  $RedDate = $date2->format("m/d/y g:i a");
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

    <title>TamperedLive - Edit Token #<?php echo $Result['id']; ?></title>


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
              <li class="has-submenu">
                <a href="clients.php"><i class="md md-account-child"></i>Clients</a>
              </li>
              <li class="has-submenu active">
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
              <h4 class="text-dark header-title m-t-0">Edit Token </h4>
              <form class="form-horizontal" role="form" action="inc/handler.php" method="post">
                <input type="text" hidden name="id" value="<?php echo $id; ?>">
                <input type="text" hidden name="func" value="editToken">
                <div class="form-group">
	                <label class="col-sm-2 control-label">Token</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control" readonly value="<?php echo $Result['token']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Days</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control ts-up-down" name="days" value="<?php echo $Result['days']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Reserve Days</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control ts-up-down" name="rdays" value="<?php echo $Result['reserve_days']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Enabled</label>
	                <div class="col-sm-10">
                    <input type="checkbox" name="enabled" data-plugin="switchery" data-color="#00b19d" data-size="small" <?php if($Result['enabled'] == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Redeemed</label>
	                <div class="col-sm-10">
                    <input type="checkbox" name="redeemed" data-plugin="switchery" data-color="#ef5350" data-size="small" <?php if($Result['redeemed'] == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Trial</label>
	                <div class="col-sm-10">
                    <input type="checkbox" name="trial" data-plugin="switchery" data-color="#7266ba" data-size="small" <?php if($Result['trial'] == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Hidden</label>
	                <div class="col-sm-10">
                    <input type="checkbox" name="display" data-plugin="switchery" data-color="#3ddcf7" data-size="small" <?php if($Result['display'] == 0) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Buyer Name</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control" name="buyer" value="<?php echo $Result['whofor']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Transaction Amount</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control" name="paid" value="<?php echo $Result['paid']; ?>">
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
              <h4 class="text-dark header-title m-t-0">Token Info</h4>
              <div class="panel panel-color panel-purple">
                <div class="panel-heading">
                  <h3 class="panel-title">Technical Info</h3>
                </div>
                <div class="panel-body">
                  <p>
                    Generated By: <span class="white xexhash"><?php echo $Result['generated_by']; ?></span><br>
                    Generated Date: <span class="white"><?php echo $GenDate; ?></span><br>
                    <?php if($Result['redeemed'] == 1){ ?>
                    Redeemed By: <span class="white"><?php echo $Result['redeemed_by']; ?></span><br>
                    Redeemed Date: <span class="white"><?php echo $RedDate; ?></span><br>
                    <?php } ?>
                  </p>
                </div>
              </div>
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
    
    <script type="text/javascript">
      $(function(){
        $(".ts-up-down").TouchSpin({
          buttondown_class: "btn btn-primary",
          buttonup_class: "btn btn-primary"
        });
      });
    </script>
  </body>
</html>
<?php else :
  header('Location: index.php'); 
endif; ?>