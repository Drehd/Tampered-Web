<?php
include_once 'inc/functions.php';
 
Functions::SecStart();
if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 1) :

$Pdo = Functions::GetDB();

$ID = $Pdo->quote($_SESSION['id']);
$Stmt = $Pdo->query("SELECT * FROM `logins` WHERE `id` = $ID  LIMIT 1");
if($Stmt->rowCount() != 1) header('Location: dashboard.php?error=UID Not Valid.'); 
//get results
$Result = $Stmt->fetch();

$ga = new GoogleAuthenticator();

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TamperedLive Dashboard">
    <meta name="author" content="HaXzz">
    

    <link rel="shortcut icon" href="img/favicon.png">

    <title>TamperedLive - Viewing Profile</title>


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
    <link href="css/fileinput.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Source+Code+Pro" rel="stylesheet">

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="js/app.js"></script>
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
              <?php if (Functions::GetLoginLevel() >= 2){ ?>
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
              <li class="has-submenu">
                <a href="map.php"><i class="md md-map"></i>Map</a>
              </li>
              <li class="has-submenu">
                <a href="logs.php"><i class="md md-assignment"></i>Logs</a>
              </li>
              <li class="has-submenu active">
                <a href="profile.php"><i class="md md-assignment"></i>Profile</a>
              </li>
              <?php } else if (Functions::GetLoginLevel() >= 1) { ?>
              <li class="has-submenu">
                <a href="seller.php"><i class="md md-dashboard"></i>Dashboard</a>
              </li>
              <li class="has-submenu">
                <a href="clients-seller.php"><i class="md md-account-child"></i>Clients</a>
              </li>
              <li class="has-submenu">
                <a href="tokens-seller.php"><i class="md md-stars"></i>Tokens</a>
              </li>
              <li class="has-submenu active">
                <a href="profile.php"><i class="md md-assignment"></i>Profile</a>
              </li>
              <?php } ?>
              
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
              <div class="row">
                <div class="col-md-4">
                  <center><img style="border:1px solid #fff;border-radius:5px;" src="<?php echo $Result['avatar']; ?>" width="150px" height="150px"/></center>
                </div>
                <div class="col-md-8" style="padding-top:35px;">
                <form class="form-horizontal" role="form"  enctype="multipart/form-data" action="inc/handler.php" method="post">
                  <input type="text" hidden name="func" value="uploadAvatar">
                    <div class="form-group">
                      <label class="col-sm-3 control-label">Upload Picture</label>
                      <div class="col-sm-9">
                        <input type="file" class="form-control" id="picture" class="file" data-show-preview="false" data-allowed-file-extensions='["png", "jpg"]' name="picture">
                      </div>
                    </div>
                    <div class="form-group" align="right" style="margin-right:20px">
                      <button type="submit" class="btn btn-purple waves-effect waves-light">Save</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="card-box">
              <h4 class="text-dark header-title m-t-0">2FA Code</h4>
              <hr>
              <div class="row">
                <div class="col-md-4">
                  <img src="<?php echo $ga->getQRCodeGoogleUrl($Result['username']."@TLPanel", $Result['secret']); ?>"/>
                </div>
                <div class="col-md-8" style="padding-top:75px;">
                  <form class="form-horizontal">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">Secret</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?php echo $Result['secret']; ?>">
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card-box">
              <h4 class="text-dark header-title m-t-0">Edit Profile</h4>
              <hr>
              <form class="form-horizontal" role="form" action="inc/handler.php" method="post">
                <input type="text" hidden name="func" value="editProfile">
                <div class="form-group">
	                <label class="col-sm-2 control-label">Name</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" value="<?php echo $Result['username']; ?>">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-2 control-label">Email</label>
	                <div class="col-sm-10">
                    <input type="text" class="form-control" name="email" value="<?php echo $Result['email']; ?>">
	                </div>
	              </div>
                <div class="form-group" align="right" style="margin-right:20px">
                  <button type="submit" class="btn btn-purple waves-effect waves-light">Save</button>
                </div>
              </form>
              <br>
              <form class="form-horizontal" role="form" action="inc/handler.php" id="passwordForm" method="post">
                <input type="text" hidden name="func" value="changePassword">
                <h5 class="text-dark header-title m-t-0">Change Password</h5>
                <hr>
                <div class="form-group">
	                <label class="col-sm-3 control-label">Current Password</label>
	                <div class="col-sm-9">
                    <input type="password" class="form-control" name="oldpass" id="oldpass">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-3 control-label">New Password</label>
	                <div class="col-sm-9">
                    <input type="password" class="form-control" name="newpass" id="newpass">
	                </div>
	              </div>
                <div class="form-group">
	                <label class="col-sm-3 control-label">Confirm Password</label>
	                <div class="col-sm-9">
                    <input type="password" class="form-control" name="confirmpass" id="confirmpass">
	                </div>
	              </div>
                <div class="form-group" align="right" style="margin-right:20px">
                  <button type="submit" onclick="return changepass($('form#passwordForm'), $('#newpass'), $('#confirmpass'), $('#oldpass'));" class="btn btn-purple waves-effect waves-light">Save</button>
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
    <script src="js/sweet-alert.min.js"></script>
    <script src="js/fileinput.min.js"></script>
    
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
        $("#picture").fileinput({
          showUpload: false,
          showRemove: false,
          showCancel: false,
          allowedFileTypes: ['image'],
          allowedFileExtensions: ['jpg', 'jpeg', 'gif', 'png']
        });
      });
    </script>
  </body>
</html>
<?php else :
  header('Location: index.php'); 
endif; ?>