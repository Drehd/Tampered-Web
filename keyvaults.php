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

    <title>TamperedLive - Viewing Keyvaults</title>


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
                <a href="dashboard.php"><i class="md md-dashboard"></i>Dashboard</a>
              </li>
              <li class="has-submenu">
                <a href="clients.php"><i class="md md-account-child"></i>Clients</a>
              </li>
              <li class="has-submenu">
                <a href="tokens.php"><i class="md md-stars"></i>Tokens</a>
              </li>
              <li class="has-submenu active">
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
          <div class="col-md-12">
            <form class="form-inline" role="form" style="margin-bottom:20px;">
              <div class="row">
                <div class="col-md-12" align="right">
                  <div class="form-group">
	                  <label class="m-r-10">Sort</label>
	                  <select class="form-control" id="kvSort" name="sort_s">
	                      <option selected value="0">Start Date</option>
                        <option value="1">Alphabetical</option>
                        <option value="2">Usage</option>
                        <option value="3">Banned</option>
                        <option value="4">Unbanned</option>
	                  </select>
	                </div>
                  <a data-toggle="collapse" id="ssclick" href="#collapseSearch" aria-expanded="false" style="margin-left:20px;" class="btn btn-purple waves-effect waves-light collapsed">Show Search</a>
                </div>
              </div>
              <br>
              <div id="collapseSearch" class="panel-collapse collapse" aria-expanded="false">
                <div class="row">
                  <div class="col-md-12" align="right">
                    <div class="form-group" style="padding-bottom:20px;">
                      <label class="m-r-10">CPUKey</label>
                      <input type="text" name="cpukey_s" class="form-control" style="margin-right:20px;">
                    </div>
                    <div class="form-group" style="padding-bottom:20px;">
                      <label class="m-r-10">Serial</label>
                      <input type="text" name="serial_s" class="form-control" style="margin-right:20px;">
                    </div>
                    <div class="form-group" style="padding-bottom:20px;">
                      <a href="clients.php" onclick="updateKeyvaults();return false;" style="margin:0px 20px;" class="btn btn-warning waves-effect waves-light">Search</a>
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
              <h4 class="text-dark header-title m-t-0">Keyvaults</h4>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Serial</th>
                      <th># of Consoles</th>
                      <th>Start Date</th>
                      <th>Elapsed</th>
                      <th>Status</th>
                      <!--<th>Action</th>-->
                    </tr>
                  </thead>
                  <tbody id="kvsTable"></tbody>
                </table>
              </div>
              <div id="kvsButtons" align="right"><ul class="pagination pagination-split" id="kvspagination"></ul></div>
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
      var kvSort = 0;
      var kvPage = 0;
      var x = false;
      var sortChanged = false;
      var pgndefaults = {
          totalPages: <?php echo ceil(Functions::GetKVCount("`kvserial` != ''")/15); ?>,
          visiblePages: 5,
          startPage: 1,
          first: "<i class='fa fa-angle-left'></i><i class='fa fa-angle-left'></i>",
          last: "<i class='fa fa-angle-right'></i><i class='fa fa-angle-right'></i>",
          prev: "<i class='fa fa-angle-left'></i>",
          next: "<i class='fa fa-angle-right'></i>",
          onPageClick: function (event, page1) {
            getKeyvaults(page1-1);
          }
        };
      $(function(){
        $('#ssclick').on('click', function(){
          if(x) $(this).html('Show Search');
          else $(this).html('Hide Search');
          x = !x;
        });
        //cpukey
        $('input[name="cpukey_s"]').keyup(function(e){
          if(e.keyCode == 13) { $(this).trigger("enterKey"); }
        });
        $('input[name="cpukey_s"]').bind("enterKey",function(e){
          updateKeyvaults();
        });
        //serial
        $('input[name="serial_s"]').keyup(function(e){
          if(e.keyCode == 13) { $(this).trigger("enterKey"); }
        });
        $('input[name="serial_s"]').bind("enterKey",function(e){
          updateKeyvaults();
        });
        //client js
        $('#kvSort').on('change', function() {
          kvSort = this.value;
          kvPage = 0;
          sortChanged = true;
          getKeyvaults(kvPage);
        });
        
        getKeyvaults(0);
        $('#kvspagination').twbsPagination(pgndefaults);
      });
      function updateKeyvaults(){
        sortChanged = true;
        getKeyvaults(kvPage);
      }
      function getKeyvaults(page){
        kvPage = page;
        $.post('inc/handler.php', {"func":"getKeyvaults", "page": page, "cpukey":$('input[name="cpukey_s"]').val(), "serial": $('input[name="serial_s"]').val(), "sort": kvSort}, function(data) {
          if (data != null || data != undefined){
            var kvs = "";
            var n = data.n;
            var p = data.p;
            var c = data.c;
            if(data.s != null){
              kvs += "<tr><td colspan='8'>"+data.s+"</td></tr>";
              $('#kvsTable').html(kvs);
              $('#kvspagination').twbsPagination('destroy');
              $('#kvspagination').twbsPagination($.extend({}, pgndefaults, {
                startPage: 0,
                totalPages: 1
              }));
              return;
            }
            for (var i = 0; i < data.data.length; i++) {
              kvs += '<tr>'+
                '<td><font color="#00BBFF">'+data.data[i].index +'</font></td>'+
                '<td>'+data.data[i].serial+'</td>'+
                '<td>'+data.data[i].usage+'</td>'+
                '<td>'+data.data[i].sdate+'</td>'+
                '<td>'+data.data[i].elapsed+'</td>'+
                '<td>'+data.data[i].status+'</td>'+
                '</tr>';
            }
            $('#kvsTable').html(kvs);
            //pagination
            if(sortChanged){
              sortChanged = false;
              $('#kvspagination').twbsPagination('destroy');
              $('#kvspagination').twbsPagination($.extend({}, pgndefaults, {
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