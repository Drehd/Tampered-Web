<?php
include_once 'inc/functions.php';
 
Functions::SecStart();
if(Functions::IsLoggedIn() == false) :
  if(isset($_POST['email'], $_POST['p'])) :
?>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="TamperedLive Login">
    <meta name="author" content="HaXzz">
    
    <link rel="shortcut icon" href="img/favicon.png">
    
    <title>TamperedLive - Login</title>
    
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/core.css" rel="stylesheet" type="text/css">
    <link href="css/icons.css" rel="stylesheet" type="text/css">
    <link href="css/components.css" rel="stylesheet" type="text/css">
    <link href="css/pages.css" rel="stylesheet" type="text/css">
    <link href="css/menu.css" rel="stylesheet" type="text/css">
    <link href="css/responsive.css" rel="stylesheet" type="text/css">
    <link href="css/dashboard.css" rel="stylesheet" type="text/css" />
    <link href="css/sweet-alert.css" rel="stylesheet" type="text/css" />
        
    <script src="js/modernizr.min.js"></script>
    <script src="js/app.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="wrapper-page">
      <div class="text-center">
        <a href="index.php" class="logo logo-lg"><i class="md md-equalizer"></i> <span>TamperedLive Login</span> </a>
      </div>
      <form class="form-horizontal m-t-20" autocomplete="off" action="inc/handler.php" method="post" name="login_form">
        <input hidden="true" type="text" name="func" value="login"/>
        <input hidden="true" type="text" name="email" value="<?php echo $_POST['email']; ?>"/>
        <input hidden="true" type="text" name="p" value="<?php echo $_POST['p']; ?>"/>
        <?php if(isset($_POST['remember'])) echo '<input hidden="true" type="text" name="remember" value="1"/>'; ?>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" name="tfapin" placeholder="Two-Factor Authentication Pin">
            <i class="md md-restore form-control-feedback l-h-34"></i>
          </div>
        </div>
        <div class="form-group text-right m-t-20">
          <div class="col-xs-12">
            <button class="btn btn-primary btn-custom w-md waves-effect waves-light" type="submit">Login</button>
          </div>
        </div>
        <div class="form-group m-t-30">
          <div class="col-sm-7">
            <a href="lostcode.php" class="text-muted"><i class="fa fa-lock m-r-5"></i> Can't retrieve code?</a>
          </div>
          <div class="col-sm-5 text-right">
            <a href="register.php" class="text-muted">Register</a>
          </div>
        </div>
      </form>
    </div>
    <script>
      var resizefunc = [];
    </script>
    <!-- Main  -->
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
    
    <!-- Counter Up  -->
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>

    <!-- circliful Chart -->
    <script src="js/jquery.circliful.min.js"></script>
    <script src="js/jquery.sparkline.min.js"></script>
    
    <!-- Custom main Js -->
    <script src="js/jquery.core.js"></script>
    <script src="js/jquery.app.js"></script>
	</body>
</html>
<?php else : ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="TamperedLive Login">
    <meta name="author" content="HaXzz">
    
    <link rel="shortcut icon" href="img/favicon.png">
    
    <title>TamperedLive - Login</title>
    
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/core.css" rel="stylesheet" type="text/css">
    <link href="css/icons.css" rel="stylesheet" type="text/css">
    <link href="css/components.css" rel="stylesheet" type="text/css">
    <link href="css/pages.css" rel="stylesheet" type="text/css">
    <link href="css/menu.css" rel="stylesheet" type="text/css">
    <link href="css/responsive.css" rel="stylesheet" type="text/css">
    <link href="css/dashboard.css" rel="stylesheet" type="text/css" />
    <link href="css/sweet-alert.css" rel="stylesheet" type="text/css" />
        
    <script src="js/modernizr.min.js"></script>
    <script src="js/app.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="wrapper-page">
      <div class="text-center">
        <a href="index.php" class="logo logo-lg"><i class="md md-equalizer"></i> <span>TamperedLive Login</span> </a>
      </div>
      <form class="form-horizontal m-t-20" action="index.php" method="post" id="login" name="login_form">
        <input hidden="true" type="text" name="func" value="login"/>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" name="email" placeholder="Email">
            <i class="md md-account-circle form-control-feedback l-h-34"></i>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="password" required="" id="password" name="password" placeholder="Password">
            <i class="md md-vpn-key form-control-feedback l-h-34"></i>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <div class="checkbox checkbox-primary">
              <input id="checkbox-signup" name="remember" type="checkbox">
              <label for="checkbox-signup">Remember me </label>
            </div>
          </div>
        </div>
        <div class="form-group text-right m-t-20">
          <div class="col-xs-12">
            <button class="btn btn-primary btn-custom w-md waves-effect waves-light" onclick="passhash($('form#login'), $('#password'));" type="submit">Login</button>
          </div>
        </div>
        <div class="form-group m-t-30">
          <div class="col-sm-7">
            <a href="recovery.php" class="text-muted"><i class="fa fa-lock m-r-5"></i> Forgot your password?</a>
          </div>
          <div class="col-sm-5 text-right">
            <a href="register.php" class="text-muted">Register</a>
          </div>
        </div>
      </form>
    </div>
    <script>
      var resizefunc = [];
    </script>
    <!-- Main  -->
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
    
    <!-- Counter Up  -->
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>

    <!-- circliful Chart -->
    <script src="js/jquery.circliful.min.js"></script>
    <script src="js/jquery.sparkline.min.js"></script>
    
    <!-- Custom main Js -->
    <script src="js/jquery.core.js"></script>
    <script src="js/jquery.app.js"></script>
	</body>
</html>
<?php
  endif;
elseif (Functions::GetLoginLevel() >= 2): 
  header('Location: ../dashboard.php');
elseif (Functions::GetLoginLevel() >= 1): 
  header('Location: ../seller.php');
endif; 
?>