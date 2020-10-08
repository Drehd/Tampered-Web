<?php
include_once 'inc/functions.php';
include_once 'inc/GoogleAuthenticator.php';
 
Functions::SecStart();
if (Functions::IsLoggedIn() == false) : 

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TamperedLive Dashboard">
    <meta name="author" content="HaXzz">

    <link rel="shortcut icon" href="img/favicon.png">
    
    <title>TamperedLive - Recovery</title>
    
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

    <script src="js/app.js"></script>
    <script src="js/modernizr.min.js"></script>

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
        <a href="index.php" class="logo logo-lg"><i class="md md-equalizer"></i> <span>2FA + Password Recovery</span> </a>
      </div>
      <form method="post" action="inc/handler.php" role="form" class="text-center m-t-20">
        <input type="text" hidden name="func" value="resetPassword">
        <div class="alert alert-success alert-dismissable">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          Enter your <b>Email</b> and instructions will be sent to you!
        </div>
        <div class="form-group m-b-0">
          <div class="input-group">
            <input type="email" class="form-control" name="email" placeholder="Enter Email" required="">
            <i class="md md-email form-control-feedback l-h-34" style="left:6px;z-index: 99;"></i>
            <span class="input-group-btn"> <button type="submit" class="btn btn-email btn-primary waves-effect waves-light">Reset</button> </span>
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
    
    <!-- Modal-Effect -->
    <script src="js/custombox.js"></script>
    <script src="js/legacy.min.js"></script>
	
	</body>
  <script>
  $(function(){
    
  });
</html>
<?php 
else :
  header("Location: index.php");
endif; ?>