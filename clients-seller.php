<?php
include_once 'inc/functions.php';
 
Functions::SecStart();
if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 1) :

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TamperedLive Dashboard">
    <meta name="author" content="HaXzz">

    <link rel="shortcut icon" href="img/favicon.png">

    <title>TamperedLive - Viewing Clients</title>


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
                <a href="seller.php"><i class="md md-dashboard"></i>Dashboard</a>
              </li>
              <li class="has-submenu active">
                <a href="clients-seller.php"><i class="md md-account-child"></i>Clients</a>
              </li>
              <li class="has-submenu">
                <a href="tokens-seller.php"><i class="md md-stars"></i>Tokens</a>
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
          <div class="col-md-12">
            <form class="form-horizontal" role="form" style="margin-bottom:20px;">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="col-sm-3 control-label">CPUKey</label>
                    <div class="col-sm-9">
                      <input type="text" name="cpukey_s" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Name</label>
                    <div class="col-sm-9">
                      <input type="text" name="name_s" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="col-sm-3 control-label">IP</label>
                    <div class="col-sm-9">
                      <input type="text" name="ip_s" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="col-md-2" align="right">
                  <a href="clients.php" onclick="updateClients();return false;" style="margin:0px 20px;" class="btn btn-purple waves-effect waves-light">Search</a>
                  <a href="#AddClientModal" class="btn btn-primary waves-effect waves-light" data-animation="push" data-plugin="custommodal" data-overlaySpeed="100" data-overlayColor="#36404a">Add Client</a>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card-box">
              <h4 class="text-dark header-title m-t-0">Clients</h4>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>CPUKey</th>
                      <th>Expire Time</th>
                      <th>Version</th>
                    </tr>
                  </thead>
                  <tbody id="clientTable"></tbody>
                </table>
              </div>
              <div id="cliButtons" align="right"><ul class="pagination pagination-split" id="clipagination"></ul></div>
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
    
    <!-- Modal-Effect -->
    <script src="js/custombox.js"></script>
    <script src="js/legacy.min.js"></script>

    <script type="text/javascript">
      var clientPage = 0;
      var sortChanged = false;
      var pgndefaults = {
          totalPages: <?php echo ceil(Functions::GetCurrentClientCount("")/15); ?>,
          visiblePages: 5,
          startPage: 1,
          first: "<i class='fa fa-angle-left'></i><i class='fa fa-angle-left'></i>",
          last: "<i class='fa fa-angle-right'></i><i class='fa fa-angle-right'></i>",
          prev: "<i class='fa fa-angle-left'></i>",
          next: "<i class='fa fa-angle-right'></i>",
          onPageClick: function (event, page1) {
            getClients(page1-1);
          }
        };
      $(function(){
        $(".ts-up-down").TouchSpin({
          buttondown_class: "btn btn-primary",
          buttonup_class: "btn btn-primary"
        });
        getClients(0);
        $('#clipagination').twbsPagination(pgndefaults);
      });
      //client stuff
      function updateClients(){
        sortChanged = true;
        getClients(clientPage);
      }
      function getClients(page){
        clientPage = page;
        $.post('inc/handler.php', {"func":"getClients", "page": page, "cpukey":$('input[name="cpukey_s"]').val(), "name": $('input[name="name_s"]').val(), "ip": $('input[name="ip_s"]').val(), "sort": 0}, function(data) {
          if (data != null || data != undefined){
            var cli = "";
            var n = data.n;
            var p = data.p;
            var c = data.c;
            if(data.s != null){
              cli += "<tr><td colspan='8'>"+data.s+"</td></tr>";
              $('#clientTable').html(cli);
              $('#clipagination').twbsPagination('destroy');
              $('#clipagination').twbsPagination($.extend({}, pgndefaults, {
                startPage: 1,
                totalPages: 1
              }));
              return;
            }
            for (var i = 0; i < data.data.length; i++) {
              cli += '<tr>'+
                '<td><font color="#00BBFF">'+data.data[i].index +'</font></td>'+
                '<td><font color="#fff">'+data.data[i].cname+'</font></td>'+
                '<td>'+data.data[i].cpukey+'</td>'+
                '<td>'+((data.data[i].lifetime == 1) ? "<span class='lifetime'>Unlimited Access</span>" : ((data.data[i].fexpire == "Expired") ? "<span style='color:darkred'>Expired</span>" : data.data[i].fexpire))+'</td>'+
                '<td align="center">'+data.data[i].version+'</td>'+
                '</tr>';
            }
            $('#clientTable').html(cli);
            //pagination
            if(sortChanged){
              sortChanged = false;
              $('#clipagination').twbsPagination('destroy');
              $('#clipagination').twbsPagination($.extend({}, pgndefaults, {
                startPage: 1,
                totalPages: c
              }));
            }
          } else {
            //console.log("ERROR: No tokens?");
          }
        }, 'json');
      }
    </script>
  </body>
</html>
<?php else :
  header('Location: index.php'); 
endif; ?>