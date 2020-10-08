<?php
include_once 'inc/functions.php';
 
Functions::SecStart();
if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2) :

$date1 = new DateTime();
$date1->setTimezone(new DateTimeZone('America/New_York'));

$ExpDate = $date1->format("m/d/Y");
$ExpTime = $date1->format("h:i A");

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
                <a href="dashboard.php"><i class="md md-dashboard"></i>Dashboard</a>
              </li>
              <li class="has-submenu active">
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
	                  <select class="form-control" id="clientSort" name="sort_s">
	                      <option selected value="0">Version</option>
                        <option value="1">Blacklisted</option>
                        <option value="2">Developer</option>
                        <option value="3">Lifetime</option>
                        <option value="4">Fails</option>
                        <option value="5">Expire Time</option>
	                  </select>
	                </div>
                  <a data-toggle="collapse" id="ssclick" href="#collapseSearch" aria-expanded="false" style="margin-left:20px;" class="btn btn-purple waves-effect waves-light collapsed">Show Search</a>
                  <!--<a href="#SudoRedeemModal" class="btn btn-warning waves-effect waves-light" style="margin-left:20px;" data-animation="push" data-plugin="custommodal" data-overlaySpeed="100" data-overlayColor="#ffaa00">Sudo-Redeem</a>-->
                  <a href="#AddClientModal" class="btn btn-primary waves-effect waves-light" style="margin-left:20px;" data-animation="push" data-plugin="custommodal" data-overlaySpeed="100" data-overlayColor="#36404a">Add Client</a>
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
                      <label class="m-r-10">Name</label>
                      <input type="text" name="name_s" class="form-control" style="margin-right:20px;">
                    </div>
                    <div class="form-group" style="padding-bottom:20px;">
                      <label class="m-r-10">Gamertag</label>
                      <input type="text" name="gt_s" class="form-control" style="margin-right:20px;">
                    </div>
                    <div class="form-group" style="padding-bottom:20px;">
                      <label class="m-r-10">IP</label>
                      <input type="text" name="ip_s" class="form-control" style="margin-right:20px;">
                    </div>
                    <div class="form-group" style="padding-bottom:20px;">
                      <a href="clients.php" onclick="updateClients();return false;" style="margin:0px 20px;" class="btn btn-warning waves-effect waves-light">Search</a>
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
                      <th>Fails</th>
                      <th colspan=2>Action</th>
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
    
    <!-- Late Night Modal -->
    <div id="AddClientModal" class="modal-demo">
      <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Close</span>
      </button>
      <h4 class="custom-modal-title">Add Client</h4>
      <div class="custom-modal-text">
        <form class="form-horizontal" role="form" id="addClientForm" action="inc/handler.php" method="post">
          <input type="text" hidden name="func" value="addClient">
          <div class="form-group">
	          <label class="col-sm-2 control-label">Name</label>
	          <div class="col-sm-10">
              <input type="text" class="form-control" required name="name">
	          </div>
	        </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">CPUKey</label>
	          <div class="col-sm-10">
              <input type="text" class="form-control" required name="cpukey">
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-2 control-label">Email</label>
	          <div class="col-sm-10">
              <input type="text" class="form-control" name="email">
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-2 control-label">Expire Day</label>
	          <div class="col-sm-10 input-group remove-padding">
              <input type="text" class="form-control dp-autoclose" name="expday" value="<?php echo $ExpDate; ?>">
              <span class="input-group-addon bg-primary b-0 text-white"><i class="ion-calendar"></i></span>
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-2 control-label">Expire Time</label>
	          <div class="col-sm-10 input-group remove-padding">
              <input type="text" class="form-control tp-12" name="exptime">
              <span class="input-group-addon bg-primary text-white"><i class="glyphicon glyphicon-time"></i></span>
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-2 control-label">Reserve Days</label>
            <div class="col-sm-10">
              <input type="text" class="form-control ts-up-down" name="rdays" value="0">
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-2 control-label">Lifetime</label>
	          <div class="col-sm-10" align="left">
              <input type="checkbox" name="lifetime" data-plugin="switchery" data-color="#ffd865" data-size="small">
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-2 control-label">Blacklisted</label>
	          <div class="col-sm-10" align="left">
              <input type="checkbox" name="blacklisted" data-plugin="switchery" data-color="#ff4d4d" data-size="small">
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-2 control-label">Developer</label>
	          <div class="col-sm-10" align="left">
              <input type="checkbox" name="developer" data-plugin="switchery" data-color="#c266ff" data-size="small">
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-2 control-label">Used Trial</label>
	          <div class="col-sm-10" align="left">
              <input type="checkbox" name="usedtrial" data-plugin="switchery" data-color="#00b19d" data-size="small">
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-2 control-label">Notes</label>
	          <div class="col-sm-10">
              <textarea class="form-control" rows="5" name="notes"></textarea>
	          </div>
	        </div>
          <div class="form-group" align="right" style="margin-right:20px">
            <button onclick="Custombox.close();" class="btn btn-default waves-effect waves-light">Cancel</button>
            <button onclick="" class="btn btn-success waves-effect waves-light">Add Client</button>
          </div>
        </form>
      </div>
    </div>
    
    <div id="SudoRedeemModal" class="modal-demo">
      <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Close</span>
      </button>
      <h4 class="custom-modal-title">Sudo Redeem Token</h4>
      <div class="custom-modal-text">
        <form class="form-horizontal" role="form" id="sudoRedeemForm" action="inc/handler.php" method="post">
          <input type="text" hidden name="func" value="sudoRedeem">
          <div class="form-group">
            <label class="col-sm-2 control-label">CPUKey</label>
	          <div class="col-sm-10">
              <input type="text" class="form-control" required name="cpukey">
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-2 control-label">Token</label>
	          <div class="col-sm-10">
              <input type="text" class="form-control" name="token">
	          </div>
	        </div>
          <div class="form-group" align="right" style="margin-right:20px">
            <button onclick="Custombox.close();" class="btn btn-default waves-effect waves-light">Cancel</button>
            <button onclick="" class="btn btn-success waves-effect waves-light">Redeem</button>
          </div>
        </form>
      </div>
    </div>

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
      var clientSort = 0;
      var clientPage = 0;
      var x = false;
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
          updateClients();
        });
        //name
        $('input[name="name_s"]').keyup(function(e){
          if(e.keyCode == 13) { $(this).trigger("enterKey"); }
        });
        $('input[name="name_s"]').bind("enterKey",function(e){
          updateClients();
        });
        //gamertag
        $('input[name="gt_s"]').keyup(function(e){
          if(e.keyCode == 13) { $(this).trigger("enterKey"); }
        });
        $('input[name="gt_s"]').bind("enterKey",function(e){
          updateClients();
        });
        //ip
        $('input[name="ip_s"]').keyup(function(e){
          if(e.keyCode == 13) { $(this).trigger("enterKey"); }
        });
        $('input[name="ip_s"]').bind("enterKey",function(e){
          updateClients();
        });
        $(".ts-up-down").TouchSpin({
          buttondown_class: "btn btn-primary",
          buttonup_class: "btn btn-primary"
        });
        $('.dp-autoclose').datepicker({
          autoclose: true,
          format: 'mm/dd/yyyy',
          todayHighlight: true,
          disableTouchKeyboard: true
        });
        $('.tp-12').timepicker({
          defaultTime: '<?php echo $ExpTime; ?>',
          disableFocus: true,
          minuteStep: 5
				});
        //client js
        $('#clientSort').on('change', function() {
          clientSort = this.value;
          clientPage = 0;
          sortChanged = true;
          getClients(clientPage);
        });
        
        getClients(0);
        $('#clipagination').twbsPagination(pgndefaults);
      });
      //client stuff
      function removeClientDialog(id){
        swal({
          title: "Are you sure you want to delete this client?",
          text: "You will be deleting this client permanently. This action cannot be reversed!",
          type: "error",
          showCancelButton: true,
          confirmButtonClass: "btn-danger waves-effect waves-light",
          confirmButtonText: "Yes",
          cancelButtonText: "No",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if (isConfirm) {
            $.post("inc/handler.php",{ func: "removeClient", id:id }, function(data) {
              if(data.status == true){
                swal({
                  title: "Client Deleted",
                  text: "The client has been deleted permanently",
                  type: "success",
                  showCancelButton: false,
                  confirmButtonText: "OK",
                  confirmButtonClass: "btn-success waves-effect waves-light",
                });
              } else {
                swal({
                  title: "Failed to Delete Client",
                  text: "An error occurred trying to delete the specified client. ID #"+id,
                  type: "error",
                  showCancelButton: false,
                  confirmButtonText: "OK",
                  confirmButtonClass: "btn-danger waves-effect waves-light",
                });
              }
            }, 'json');
            setTimeout(function(){ updateClients(); }, 1000);
          } else {
            swal({
              title: "Action Canceled",
              text: "Client was not deleted",
              type: "success",
              showCancelButton: false,
              confirmButtonText: "OK",
              confirmButtonClass: "btn-success waves-effect waves-light",
            });
          }
        });
      }
      function updateClients(){
        sortChanged = true;
        getClients(clientPage);
      }
      function getClients(page){
        clientPage = page;
        $.post('inc/handler.php', {"func":"getClients", "page": page, "cpukey":$('input[name="cpukey_s"]').val(), "name": $('input[name="name_s"]').val(), "ip": $('input[name="ip_s"]').val(), "gamertag": $('input[name="gt_s"]').val(), "sort": clientSort}, function(data) {
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
                startPage: 0,
                totalPages: 1
              }));
              return;
            }
            for (var i = 0; i < data.data.length; i++) {
              cli += '<tr>'+
                '<td><font color="#00BBFF">'+data.data[i].index +'</font></td>'+
                '<td><a href="editclient.php?id='+data.data[i].id+'"><font color="#fff">'+data.data[i].cname+'</font></a></td>'+
                '<td>'+data.data[i].cpukey+'</td>'+
                '<td>'+((data.data[i].lifetime == 1) ? "<span class='lifetime'>Unlimited Access</span>" : ((data.data[i].fexpire == "Expired") ? "<span style='color:darkred'>Expired</span>" : data.data[i].fexpire))+'</td>'+
                '<td align="center">'+data.data[i].version+'</td>'+
                '<td align="center">'+data.data[i].fails+'</td>'+
                '<td><a class="editbutton" href="editclient.php?id='+data.data[i].id+'"><span class="glyphicon glyphicon-pencil"></span></a></td>'+
                '<td><a class="editbutton" href="clients.php" onclick="removeClientDialog('+data.data[i].id+');return false;"><span class="glyphicon glyphicon-remove-circle"></span></a></td>'+
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