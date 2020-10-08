<?php
include_once 'inc/functions.php';
include_once 'inc/GoogleAuthenticator.php';
 
Functions::SecStart();
if (Functions::GetOption("registration") == 1 && Functions::IsLoggedIn() == false) : 

if(!isset($_GET['a'])) header("Location: index.php?error=An error has occurred.");

$A = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);

$B = Functions::Get2FASecret($A);

if($B == "") header("Location: index.php?error=An error has occurred.");

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TamperedLive Dashboard">
    <meta name="author" content="HaXzz">

    <link rel="shortcut icon" href="img/favicon.png">
    
    <title>TamperedLive - Registration</title>
    
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
        <a href="index.php" class="logo logo-lg"><i class="md md-equalizer"></i> <span>Two-Factor Auth.</span> </a>
      </div>
      <?php if (isset($_POST['error'])){ echo $_POST['error']; } ?>
      <form action="inc/handler.php" method="post" class="form-horizontal">
        <input hidden="true" type="text" name="func" value="twofactor"/>
        <input hidden="true" type="text" name="a" value="<?php echo $A; ?>"/>
        <br>
        <div class="form-group">
          <div class="col-xs-12">
            <center><span style="font-size:12px;">
              Running Chrome? Try this: <a href="https://chrome.google.com/webstore/detail/authenticator/bhghoamapcdpbohphigoooaddinpkbai?hl=en">Authenticator Extension</a><br>
              Running Android? Try this: <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en">Google Authenticator App</a><br>
              Running iOS? Try this: <a href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8">Google Authenticator App</a>
            </span>
            <br>
            <br>
            <?php
              $C = explode('~', $B);
              $ga = new GoogleAuthenticator();
              echo '<img src="'.$ga->getQRCodeGoogleUrl($C[1]."@TLPanel", $C[0]).'" />';
            ?>
            </center>
	        </div>
	      </div>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" name="tfapin" placeholder="Two-Factor Authentication Pin">
            <i class="md md-restore form-control-feedback l-h-34"></i>
          </div>
        </div>
        <div class="form-group text-right m-t-20">
          <div class="col-xs-12">
            <button class="btn btn-primary btn-custom w-md waves-effect waves-light" type="submit">Submit</button>
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