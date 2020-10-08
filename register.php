<?php
include_once 'inc/functions.php';
 
Functions::SecStart();
if (Functions::GetOption("registration") == 1 && Functions::IsLoggedIn() == false) : 
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
        <a href="index.php" class="logo logo-lg"><i class="md md-equalizer"></i> <span>Registration</span> </a>
      </div>
      <form class="form-horizontal m-t-20" action="inc/handler.php" id="regform" method="post">
        <input type="text" hidden name="func" value="register">
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" name="username" id="username" placeholder="Username">
            <i class="md md-account-circle form-control-feedback l-h-34"></i>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="email" required="" name="email" id="email" placeholder="Email">
            <i class="md md-email form-control-feedback l-h-34"></i>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="password" required="" name="password" id="password" placeholder="Password">
            <i class="md md-vpn-key form-control-feedback l-h-34"></i>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="password" required="" name="confirm" id="confirm" placeholder="Confirm Password">
            <i class="md md-vpn-key form-control-feedback l-h-34"></i>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="password" required="" name="regcode" id="regcode" placeholder="Registration Code">
            <i class="md md-https form-control-feedback l-h-34"></i>
          </div>
        </div>
        <div class="form-group text-right m-t-20">
          <div class="col-xs-12">
            <button class="btn btn-primary btn-custom waves-effect waves-light w-md" onclick="return regformhash($('#regform'), $('#username'), $('#email'), $('#password'), $('#confirm'), $('#regcode'));" type="submit">Register</button>
          </div>
        </div>
        <div class="form-group m-t-30">
          <div class="col-sm-12 text-center">
            <a href="index.php" class="text-muted">Already have account?</a>
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
    function regformhash(form, name, email, password, confirm, code) {
      // Check each field has a value
      if (name.val() == '' || email.val() == '' || password.val() == ''  || confirm.val() == '' || code.val() == '') {
        swal({
          title: "Error",
          text: "Please fill in all values before trying to submit.",
          type: "error",
          showCancelButton: false,
          confirmButtonClass: "btn-danger waves-effect waves-light",
          confirmButtonText: "OK",
         });
        return false;
      }
      // Check the username
      var regex = /^[-\sa-zA-Z0-9]+$/; 
      if(!regex.test(name.val())) {
        swal({
          title: "Error",
          text: "Username must only contain letters, numbers, and spaces.",
          type: "error",
          showCancelButton: false,
          confirmButtonClass: "btn-danger waves-effect waves-light",
          confirmButtonText: "OK",
        });
        name.focus();
        return false; 
      }
      if (password.val().length < 6) {
        swal({
          title: "Error",
          text: "Password must be at least six characters long and contain a number and both uppercase and lowercase letters.",
          type: "error",
          showCancelButton: false,
          confirmButtonClass: "btn-danger waves-effect waves-light",
          confirmButtonText: "OK",
        });
        password.focus();
        return false;
      }
      regex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
      if (!regex.test(password.val())) {
        swal({
          title: "Error",
          text: "Password must contain a number and both uppercase and lowercase letters.",
          type: "error",
          showCancelButton: false,
          confirmButtonClass: "btn-danger waves-effect waves-light",
          confirmButtonText: "OK",
        });
        password.focus();
        return false;
      }
      if (password.val() != confirm.val()) {
        swal({
          title: "Error",
          text: "Passwords do not match.",
          type: "error",
          showCancelButton: false,
          confirmButtonClass: "btn-danger waves-effect waves-light",
          confirmButtonText: "OK",
        });
        confirm.focus();
        return false;
      }
      var p = document.createElement("input");
      form.append(p);
      p.name = "p";
      p.type = "hidden";
      p.value = hex_sha512(password.val());
      password.val("");
      confirm.val("");
      form.submit();
      return true;
    }
  </script>
</html>
<?php 
else :
  header("Location: index.php");
endif; ?>