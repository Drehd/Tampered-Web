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

    <title>TamperedLive - Viewing Tokens</title>


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
                <a href="seller.php"><i class="md md-dashboard"></i>Dashboard</a>
              </li>
              <li class="has-submenu">
                <a href="clients-seller.php"><i class="md md-account-child"></i>Clients</a>
              </li>
              <li class="has-submenu active">
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
                    <label class="col-sm-3 control-label">Token</label>
                    <div class="col-sm-9">
                      <input type="text" name="token_s" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="col-sm-3 control-label">CPUKey</label>
                    <div class="col-sm-9">
                      <input type="text" name="cpukey_s" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Generated By</label>
                    <div class="col-sm-9">
                      <input type="text" name="genby_s" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="col-md-2" align="right">
                  <a href="tokens.php" onclick="searchTokens();return false;" style="margin:0px 10px;" class="btn btn-purple waves-effect waves-light">Search</a>
                  <a href="#GenModalNormal" class="btn btn-primary waves-effect waves-light" data-animation="push" data-plugin="custommodal" data-overlaySpeed="100" data-overlayColor="#36404a">Generate Token</a>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card-box">
              <h4 class="text-dark header-title m-t-0">Tokens</h4>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Token</th>
                      <th>Enabled</th>
                      <th>Used</th>
                      <th>Days</th>
                      <th>Generated</th>
                      <th>Redeemed</th>
                      <th>Buyer</th>
                      <th>Trial</th>
                    </tr>
                  </thead>
                  <tbody id="tokenTable"></tbody>
                </table>
              </div>
              <div id="tokButtons" align="right"><ul class="pagination pagination-split" id="tokpagination"></ul></div>
            </div>
          </div>
        </div>
        <!-- end row -->
      </div> 
      <!-- end container -->
    </div>
    <!-- End wrapper -->
    
    <!-- Late Night Modal -->
    
    <div id="GenModalNormal" class="modal-demo">
      <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Close</span>
      </button>
      <h4 class="custom-modal-title">Generate Token</h4>
      <div id="GenModalNormalBody" class="custom-modal-text">
        <form class="form-horizontal" role="form" id="gentokenform">
          <input type="text" hidden name="func" value="generateToken">
          <div class="form-group">
	          <label class="col-sm-3 control-label">Days</label>
	          <div class="col-sm-9">
              <select class="form-control dblclickupdown" min="0" max="356" name="days" id="days" required >
                <option value="1">1 Day</option>
                <option value="3">3 Days</option>
                <option value="7">7 Days</option>
                <option value="30">1 Month</option>
                <option value="90">3 Months</option>
                <option value="180">6 Months</option>
                <option value="365">1 Year</option>
                <option value="99999">Unlimited Access</option>
              </select>
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-3 control-label">Reserve Days</label>
	          <div class="col-sm-9">
              <select class="form-control dblclickupdown" min="0" max="356" name="rdays" id="rdays" required >
                <option value="0">0 Days</option>
                <option value="1">1 Day</option>
                <option value="3">3 Days</option>
                <option value="7">7 Days</option>
                <option value="30">1 Month</option>
                <option value="90">3 Months</option>
                <option value="180">6 Months</option>
                <option value="365">1 Year</option>
              </select>
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-3 control-label">Amount</label>
	          <div class="col-sm-9">
              <select class="form-control dblclickupdown" min="1" max="7" name="amount" id="amount">
                <option value="1">1 Token</option>
                <option value="3">3 Tokens</option>
                <option value="5">5 Tokens</option>
                <option value="7">7 Tokens</option>
              </select>
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-3 control-label">Buyer</label>
	          <div class="col-sm-9">
              <input type="text" class="form-control" name="buyer" id="buyer" required />
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-3 control-label">Amount Paid</label>
	          <div class="col-sm-9">
              <input type="text" class="form-control" name="paid" id="paid" required />
	          </div>
	        </div>
          <div class="form-group">
	          <label class="col-sm-3 control-label">Trial</label>
	          <div class="col-sm-9" align="left">
              <input type="checkbox" name="trial" data-plugin="switchery" data-color="#00b19d" data-size="small" />
	          </div>
	        </div>
          <div class="form-group" align="right" style="margin-right:20px">
            <button onclick="Custombox.close();" class="btn btn-default waves-effect waves-light">Cancel</button>
            <button onclick="tokenAjax('gentokenform');" class="btn btn-purple waves-effect waves-light">Generate</button>
          </div>
        </form>
      </div>
    </div>
    
    <div id="GenModalDone" class="modal-demo">
      <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Close</span>
      </button>
      <h4 class="custom-modal-title">Token(s) Generated</h4>
      <div id="GenModalDoneBody" class="custom-modal-text">
        <textarea rows="10" id="tokenbox"></textarea>
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
      var tokenPage = 0;
      var a = $('input[name="cpukey_s"]').val();
      var b = $('input[name="token_s"]').val();
      var c = $('input[name="genby_s"]').val();
      var sortChanged = false;
      var pgndefaults = {
          totalPages: <?php echo ceil(Functions::GetTokenCount("`display` = 1 && `generated_by` = '".Functions::GetUsername($_SESSION['id'])."'")/15); ?>,
          visiblePages: 5,
          startPage: 1,
          first: "<i class='fa fa-angle-left'></i><i class='fa fa-angle-left'></i>",
          last: "<i class='fa fa-angle-right'></i><i class='fa fa-angle-right'></i>",
          prev: "<i class='fa fa-angle-left'></i>",
          next: "<i class='fa fa-angle-right'></i>",
          onPageClick: function (event, page1) {
            getTokens(page1-1);
          }
        };
      $(function(){
        $(".ts-up-down").TouchSpin({
          buttondown_class: "btn btn-primary",
          buttonup_class: "btn btn-primary"
        });
        $(".dblclickupdown").on('dblclick', function(e) {
          var parent = $(this).parent();
          var value = $(this).val();
          var name = $(this).attr('name');
          var id = $(this).attr('id');
          var min = $(this).attr('min');
          var max = $(this).attr('max');
          parent.html('<input type="text" class="form-control" required name="'+name+'" value="'+value+'" id="'+id+'"/>');
          $('#'+id).TouchSpin({
            buttondown_class: "btn btn-primary",
            buttonup_class: "btn btn-primary",
            min: min,
            max: max
          });
        });
        $('#gentokenform').parsley().on('form:submit', function() {
          return false; // Don't submit form for this demo
        });;
        //client js
        $('#tokenSort').on('change', function() {
          tokenPage = 0;
          sortChanged = true;
          a = $('input[name="cpukey_s"]').val();
          b = $('input[name="token_s"]').val();
          c = $('input[name="genby_s"]').val();
          getTokens(tokenPage);
        });
        
        getTokens(0);
        setInterval(updateTokens, 30000);
        $('#tokpagination').twbsPagination(pgndefaults);
      });
      function tokenAjax(x){
        if(x == "gentokenform"){
          if($('#buyer').val() == ""){
            $('#buyer').focus();
            return;
          }
          if($('#paid').val() == ""){
            $('#paid').focus();
            return;
          }
        }
        Custombox.close();
        $.post("inc/handler.php", $('#'+x).serialize(), function(data){
          if (data != null || data != undefined){
            if(data.s != null){
              $('#GenModalDoneBody').html('<h3>'+data.s+'<h3>');
              Custombox.open({ target: '#GenModalDone', effect: 'push' });
              return;
            }
            var str = "";
            var txt = "";
            for (var x = 0; x < data.data.length; x++) {
              str += '<div class="form-group"><label class="col-sm-3 control-label">Token '+data.data[x].index+'</label><div class="col-sm-9"><input id="tokeninput'+data.data[x].index+'" onclick="this.select(); document.execCommand(\'copy\'); setTimeout(function(){ $(tokeninput'+data.data[x].index+').val(\'Copied!\');}, 200); setTimeout(function(){ $(tokeninput'+data.data[x].index+').val(\''+data.data[x].token+'\');}, 1000);" type="text" value="'+data.data[x].token+'" class="form-control"></input></div></div>';
              txt += data.data[x].token+'&#013;&#010;';
            }
            $('#GenModalDoneBody').html('<form class="form-horizontal">'+str+'<div class="form-group"><label class="col-sm-3 control-label">All Tokens</label><div class="col-sm-9"><textarea class="form-control" rows="10">'+txt+'</textarea></div></div></form>');
            setTimeout(function(){
              Custombox.open({target:'#GenModalDone', effect:'push'});
            }, 500);
            getTokens(tokenPage);
          }
        }, 'json');
      }
      //token stuff
      function searchTokens(){
        if(a != $('input[name="cpukey_s"]').val()) { a = $('input[name="cpukey_s"]').val(); sortChanged = true;}
        if(b != $('input[name="token_s"]').val()) { b = $('input[name="token_s"]').val(); sortChanged = true;}
        if(c != $('input[name="genby_s"]').val()) { c = $('input[name="genby_s"]').val(); sortChanged = true;}
        updateTokens();
      }
      function updateTokens(){
        getTokens(tokenPage);
      }
      function getTokens(page){
        tokenPage = page;
        $.post('inc/handler.php', {"func":"getTokens", "page": page, "cpukey":a, "token": b, "genby": c, "sort": 1}, function(data) {
          if (data != null || data != undefined){
            var tok = "";
            var n = data.n;
            var p = data.p;
            var c = data.c;
            if(data.s != null){
              tok += "<tr><td colspan='9'>"+data.s+"</td></tr>";
              $('#tokenTable').html(tok);
              $('#tokpagination').twbsPagination('destroy');
              $('#tokpagination').twbsPagination($.extend({}, pgndefaults, {
                startPage: 1,
                totalPages: 1,
                onPageClick: function (event, page1) {}
               }));
              return;
            }
            for (var i = 0; i < data.data.length; i++) {
              tok += '<tr><td><font color="#00BBFF">'+data.data[i].index +'</font></td><td class="tokenblue">'+data.data[i].token +"</td><td>"+data.data[i].enabled+"</td><td>"+data.data[i].status+"</td><td>"+data.data[i].days+"</td><td>"+data.data[i].generated+"</td><td class='tokenblue'>"+data.data[i].redeemed+"</td><td>"+data.data[i].buyer+"</td><td>"+data.data[i].trial+"</td></td></tr>";
            }
            $('#tokenTable').html(tok);
            //pagination
            if(sortChanged){
              sortChanged = false;
              $('#tokpagination').twbsPagination('destroy');
              $('#tokpagination').twbsPagination($.extend({}, pgndefaults, {
                startPage: 1,
                totalPages: c,
                onPageClick: function (event, page1) {
                  getTokens(page1-1);
                }
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