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

    <title>TamperedLive - Viewing Logs</title>


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
              <li class="has-submenu">
                <a href="map.php"><i class="md md-map"></i>Map</a>
              </li>
              <li class="has-submenu active">
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
          <div class="col-md-12">
            <form class="form-inline" role="form" style="margin-bottom:20px;">
              <div class="row" align="right">
                <div class="col-md-12">
                  <div class="form-group">
	                  <label class="m-r-10">Sort</label>
	                  <select class="form-control" id="catSort" name="sort_s" style="margin-right:20px;">
                      <option value="0">All Types</option>
	                    <option value="1">Clients</option>
                      <option value="2">Tokens</option>
                      <option value="3">Logins</option>
                      <option value="4">Options</option>
                      <option value="5">IP Bans</option>
	                  </select>
	                </div>
                  <a data-toggle="collapse" id="ssclick" href="#collapseSearch" aria-expanded="false" style="margin:0px 20px;" class="btn btn-purple waves-effect waves-light collapsed">Show Search</a>
                </div>
              </div>
              <br>
              <div id="collapseSearch" class="panel-collapse collapse" aria-expanded="false">
                <div class="row">
                  <div class="col-md-12" align="right">
                    <div class="form-group" style="padding-bottom:20px;">
                      <label class="m-r-10">Keyword</label>
                      <input type="text" name="search_s" class="form-control" style="margin-right:20px;">
                    </div>
                    <div class="form-group" style="padding-bottom:20px;">
                      <a href="logs.php" onclick="sortChanged=true;logPage = 0;updateLogs();return false;" style="margin:0px 20px;" class="btn btn-warning waves-effect waves-light">Search</a>
                    </div>
                  </div>
                </div> 
              </div>
            </form>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card-box">
              <h4 class="text-dark header-title m-t-0">Logs</h4>
              <div class="table-responsive">
                <table class="table">
                  <thead id="logheader">
                    <tr>
                      <th>#</th>
                      <th>Action</th>
                      <th>Time</th>
                      <th>IP</th>
                    </tr>
                  </thead>
                  <tbody id="logTable"></tbody>
                </table>
              </div>
              <div align="right"><ul class="pagination pagination-split" id="logpagination"></ul></div>
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

    <script type="text/javascript">
      var logSort = 0;
      var logPage = 0;
      var x = false;
      var sortChanged = false;
      var pgndefaults = {
          totalPages: <?php echo ceil(Functions::GetLogCount("1")/15); ?>,
          visiblePages: 5,
          startPage: 1,
          first: "<i class='fa fa-angle-left'></i><i class='fa fa-angle-left'></i>",
          last: "<i class='fa fa-angle-right'></i><i class='fa fa-angle-right'></i>",
          prev: "<i class='fa fa-angle-left'></i>",
          next: "<i class='fa fa-angle-right'></i>",
          onPageClick: function (event, page1) {
            getLogs(page1-1);
          }
        };
      $(function(){
        //client js
        $('#catSort').on('change', function() {
          logSort = this.value;
          logPage = 0;
          sortChanged = true;
          getLogs(logPage);
        });
        $('#ssclick').on('click', function(){
          if(x) $(this).html('Show Search');
          else $(this).html('Hide Search');
          x = !x;
        });
        $('input[name="search_s"]').keyup(function(e){
          if(e.keyCode == 13) { $(this).trigger("enterKey"); }
        });
        $('input[name="search_s"]').bind("enterKey",function(e){
          sortChanged = true;
          getLogs(0);
        });
        
        getLogs(0);
        setInterval(updateLogs, 30000);
        $('#logpagination').twbsPagination(pgndefaults);
      });
      function updateLogs(){
        getLogs(logPage);
      }
      function getLogs(page){
        logPage = page;
        $.post('inc/handler.php', {"func":"getLogs", "page": page, "sort": logSort, "search": $('input[name="search_s"]').val() }, function(data) {
          if (data != null || data != undefined){
            var log = "";
            var n = data.n;
            var p = data.p;
            var c = data.c;
            if(data.s != null){
              log += "<tr><td colspan='9'>"+data.s+"</td></tr>";
              $('#logTable').html(log);
              $('#logpagination').twbsPagination('destroy');
              $('#logpagination').twbsPagination($.extend({}, pgndefaults, {
                startPage: 0,
                totalPages: 1
               }));
              return;
            }
            //console.log(data);
            for (var i = 0; i < data.data.length; i++) {
              log += '<tr><td><font color="#00BBFF">'+data.data[i].index+'</font></td><td>'+data.data[i].message+"</td><td>"+data.data[i].time+"</td><td>"+data.data[i].ip+"</td></tr>";
            }
            $('#logTable').html(log);
            //pagination
            if(sortChanged){
              sortChanged = false;
              $('#logpagination').twbsPagination('destroy');
              $('#logpagination').twbsPagination($.extend({}, pgndefaults, {
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