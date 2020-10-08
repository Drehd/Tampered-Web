<?php
include_once 'inc/functions.php';
 
Functions::SecStart();
if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2) :
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TamperedLive Dashboard">
    <meta name="author" content="HaXzz">
    

    <link rel="shortcut icon" href="img/favicon.png">

    <title>TamperedLive - Edit Settings</title>


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
              <li class="has-submenu">
                <a href="tokens.php"><i class="md md-stars"></i>Tokens</a>
              </li>
              <li class="has-submenu">
                <a href="keyvaults.php"><i class="md md-vpn-key"></i>Keyvaults</a>
              </li>
              <li class="has-submenu active">
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
              <h4 class="text-dark header-title m-t-0">Server Options </h4>
              <form class="form-horizontal" role="form" action="inc/handler.php" method="post">
                <input type="text" hidden name="func" value="setServerOptions">
                <div class="form-group">
	                <label class="col-sm-3 control-label">Enable Free Mode</label>
	                <div class="col-sm-9">
                    <input type="checkbox" name="freemode" data-plugin="switchery" data-color="#3bafda" data-size="small" <?php if(Functions::GetOption("free") == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-3 control-label">Enforce XEX Hash</label>
	                <div class="col-sm-9">
                    <input type="checkbox" name="xexhash" data-plugin="switchery" data-color="#ef5350" data-size="small" <?php if(Functions::GetOption("xexhash") == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-3 control-label">Enforce Genealogy Hash</label>
	                <div class="col-sm-9">
                    <input type="checkbox" name="genehash" data-plugin="switchery" data-color="#ffaa00" data-size="small" <?php if(Functions::GetOption("genealogy_hash") == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-3 control-label">Release Version</label>
	                <div class="col-sm-9">
                    <input type="text" class="form-control ts-up-down" name="version" value="<?php echo Functions::GetOption("version"); ?>">
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
              <h4 class="text-dark header-title m-t-0">Panel Options</h4>
              <form class="form-horizontal" role="form" action="inc/handler.php" method="post">
                <input type="text" hidden name="func" value="setPanelOptions">
                <div class="form-group">
	                <label class="col-sm-3 control-label">Enable Registration</label>
	                <div class="col-sm-9">
                    <input type="checkbox" name="registration" data-plugin="switchery" data-color="#ef5350" data-size="small" <?php if(Functions::GetOption("registration") == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-3 control-label">Registration Code</label>
	                <div class="col-sm-9">
                    <input type="text" class="form-control" name="regcode" value="<?php echo Functions::GetOption("registration_code"); ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-3 control-label">Generate New Code</label>
	                <div class="col-sm-9">
                    <input type="checkbox" name="gencode" data-plugin="switchery" data-color="#00b19d" data-size="small" <?php if(Functions::GetOption("generate_new_code") == 1) echo "checked='true'"; ?>>
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-3 control-label">SocketIO URL</label>
	                <div class="col-sm-9">
                    <input type="text" class="form-control" name="sockio" value="<?php echo Functions::GetOption("socketio"); ?>">
	                </div>
	              </div>
                <div class="form-group" align="right" style="margin-right:20px">
                  <button type="submit" class="btn btn-purple waves-effect waves-light">Save</button>
                </div>
              </form>
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
          buttonup_class: "btn btn-primary",
          step: 0.01,
          decimals: 2
        });
      });
    </script>
  </body>
</html>
<?php else :
  header('Location: index.php'); 
endif; ?>