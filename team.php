<?php
include_once 'inc/functions.php';
 
Functions::SecStart();
if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 3) : ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TamperedLive Dashboard">
    <meta name="author" content="HaXzz">

    <link rel="shortcut icon" href="img/favicon.png">

    <title>TamperedLive - Team</title>


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
              <li class="has-submenu active">
                <a href="team.php"><i class="md md-perm-identity"></i>Team</a>
              </li>
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
          <?php 
            $x = 0;
            $Pdo = Functions::GetDB();
            $Stmt = $Pdo->query('SELECT `id`, `level`, `username`, `email`, `ip`, `enabled`, `avatar`, `login_time` FROM `logins` WHERE 1 ORDER BY `level` DESC');
            while($Result = $Stmt->fetch()){
          ?>
          <div class="col-md-4 col-sm-6">
            <div class="text-center card-box">
              <div class="member-card">
                <div class="thumb-lg member-thumb m-b-10 center-block">
                  <img src="<?php echo $Result['avatar']; ?>" class="img-circle img-thumbnail" style="width:80%;height:80%;" alt="profile-image">
                </div>
                <div class="">
                  <h4 class="m-b-5"><?php echo $Result['username']; ?></h4>
                  <p class="text-muted"><?php echo $Result['email']; ?></p>
                  <p class="text-muted"><?php echo "Last Login: " . Functions::GetRelativeTime2($Result['login_time']); ?></p>
                </div>
                <?php 
                  if($Result['level'] >= 3){
                    echo '<button type="button" onclick="return false;" class="btn btn-danger btn-sm w-sm waves-effect m-t-10 waves-light">Admin</button>';
                  }
                  else if ($Result['level'] >= 2){
                    echo '<button type="button" onclick="return false;" class="btn btn-warning btn-sm w-sm waves-effect m-t-10 waves-light">Staff</button>';
                  }
                  else if ($Result['level'] >= 1){
                    echo '<button type="button" onclick="return false;" class="btn btn-primary btn-sm w-sm waves-effect m-t-10 waves-light">Seller</button>';
                  }
                  else {
                    echo '<button type="button" onclick="return false;" class="btn btn-info btn-sm w-sm waves-effect m-t-10 waves-light">Registered</button>';
                  }
                ?>
                
              </div>
            </div> <!-- end card-box -->
          </div> <!-- end col -->
          
          <?php 
              $x++;
              if($x % 3 == 0){
                echo '</div><div class="row">';
              }
            } 
          ?>
        </div>
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
    <script src="js/bootstrap-checkbox.js"></script>
    <script src="js/switchery.min.js"></script>
    <script src="js/sweet-alert.min.js"></script>

    <!-- Counter Up  -->
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>

    <!-- circliful Chart -->
    <script src="js/jquery.circliful.min.js"></script>
    <script src="js/jquery.sparkline.min.js"></script>
    
    <!-- Custom main Js -->
    <script src="js/jquery.core.js"></script>
    <script src="js/jquery.app.js"></script>
    
    <script>
      $(function(){
        
      });
    </script>
  </body>
</html>
<?php else :
  header('Location: index.php'); 
endif; ?>