<?php
include_once 'inc/functions.php';
 
Functions::SecStart();
if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 1) :

$totalclients = Functions::GetCurrentClientCount();
$totaltokens = Functions::GetTokenCount("1");

//db
$Pdo = Functions::GetDB();

// -------------------------------------------------------------------------------------

//last 10 clients
$last_ten_clients = "";
$tmp = 1;

$Stmt = $Pdo->query("SELECT * FROM `clients` ORDER BY `id` DESC LIMIT 5");
while($Result = $Stmt->fetch()){
  //blacklist
  $CCPUKey = '<span class="cpu'. (($Result['blacklisted'] == 1) ? 'bad' : 'good').'">'.$Result['cpukey'].'</span>';
  //dev name
  $CName = (($Result['developer'] == 1) ? '<span class="dev-name">'.$Result['name'].'</span>' : (($Result['lifetime'] == 1) ? '<span class="unlimited-access">'.$Result['name'].'</span>' : $Result['name']));
  //formatted expire
  $tmpdate = new DateTime();
  $tmpdate->setTimestamp($Result['remaining']);
  $tmpexpire = (($Result['lifetime'] == 1) ? "<span class='lifetime'>Unlimited Access</span>" : (($Result['remaining'] > time()) ? $tmpdate->format("M j, Y @ g:i a") : "<span class='expired'>Expired</span>"));
  $last_ten_clients .= "<tr><td><font color='#3bafda'>".$tmp."</font></td><td><font color='#fff'>".$CName."</font></td><td><a href='editclient.php?id=".$Result['id']."'>".$CCPUKey."</a></td><td>".$tmpexpire."</td></tr>";
  $tmp++;
}

// -------------------------------------------------------------------------------------

//last 10 tokens
$last_ten_tokens = "";
$tmp = 1;

$Stmt = $Pdo->query("SELECT * FROM `tokens` ORDER BY `id` DESC LIMIT 5");
while($Result = $Stmt->fetch()){
  $tmpdate = new DateTime();
  $tmpdate->setTimestamp($Result['generated_date']);
  $TStatus = '<span class="token'.(($Result['redeemed'] == 1) ? 'used">Used' : 'good">Unused').'</span>';
  $last_ten_tokens .= "<tr><td><font color='#3bafda'>".$tmp."</font></td><td><font color='#fff'>".$Result['generated_by']."</font></td><td><a href='edittoken.php?id=".$Result['id']."'><span class='tokenblue'>".$Result['token']."</span></a></td><td>".$Result['days']."</td><td>".$tmpdate->format("M j, Y @ g:i a")."</td><td>".$TStatus."</td></tr>";
  $tmp++;
}

// -------------------------------------------------------------------------------------

//longest 10 kvs
$longest_ten_kvs = "";
$tmp = 1;

$Stmt = $Pdo->query("SELECT * FROM `clients` WHERE `kvstart` > 0 AND `kvstatus` = 2 ORDER BY `kvstart` ASC LIMIT 5");
while($Result = $Stmt->fetch()){
  //blacklist
  $CCPUKey = '<span class="cpu'. (($Result['blacklisted'] == 1) ? 'bad' : 'good').'">'.$Result['cpukey'].'</span>';
  //dev name
  $CName = (($Result['developer'] == 1) ? '<span class="dev-name">'.$Result['name'].'</span>' : (($Result['lifetime'] == 1) ? '<span class="unlimited-access">'.$Result['name'].'</span>' : $Result['name']));
  //formatted start time
  $tmpdate = new DateTime();
  $tmpdate->setTimestamp($Result['kvstart']);
  $tmpmonths = floor((time() - $Result['kvstart']) / 2592000);
  $tmpdays = floor(((time() - $Result['kvstart']) % 2592000)/86400);
  $longest_ten_kvs .= "<tr><td><font color='#3bafda'>".$tmp."</font></td><td><font color='#fff'>".$CName."</font></td><td><a href='editclient.php?id=".$Result['id']."'>".$CCPUKey."</a></td><td>".$Result['kvserial']."</td><td>".$tmpdate->format("M j, Y @ g:i a")."</td><td>".$tmpmonths." Months ".$tmpdays." Days</td></tr>";
  $tmp++;
}

// -------------------------------------------------------------------------------------

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TamperedLive Dashboard">
    <meta name="author" content="HaXzz">

    <link rel="shortcut icon" href="img/favicon.png">

    <title>TamperedLive - Dashboard</title>


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
              <li class="has-submenu active">
                <a href="seller.php"><i class="md md-dashboard"></i>Dashboard</a>
              </li>
              <li class="has-submenu">
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
          <div class="col-lg-4">
            <div class="card-box">
              <audio id="notify" hidden="true" src="mp3/noice.mp3"></audio>
              <h4 class="m-t-0 m-b-20 header-title"><b>Chat</b></h4>
              <div class="chat-conversation">
                <ul class="conversation-list nicescroll" id="chatlog">
                  <li class="clearfix">
                    <div class="chat-avatar">
                      <img src="img/default-avatar.png" alt="Female">
                      <i>00:00</i>
                    </div>
                    <div class="conversation-text">
                      <div class="ctext-wrap">
                        <i>Server</i>
                        <p>
                          Connecting... Please wait!
                        </p>
                      </div>
                    </div>
                  </li>
                </ul>
                <div class="row">
                  <div class="col-sm-9 chat-inputbar">
                    <input type="text" id="chatInput" class="form-control chat-input" placeholder="Enter a message">
                  </div>
                  <div class="col-sm-3 chat-send">
                    <button onclick="AdminWrapper.sendChat();" class="btn btn-md btn-primary btn-block waves-effect waves-light">Send</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-border panel-purple">
              <div class="panel-heading">
                <h3 class="panel-title">Connected Clients</h3>
              </div>
              <div class="panel-body" id="connectedclients">
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="card-box">
              <h4 class="text-dark  header-title m-t-0">Last 5 Clients</h4>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>CPUKey</th>
                      <th>Expire Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php echo $last_ten_clients; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-box">
              <h4 class="text-dark  header-title m-t-0">Last 5 Tokens</h4>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Generated By</th>
                      <th>Token</th>
                      <th>Days</th>
                      <th>Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php echo $last_ten_tokens; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-box">
              <h4 class="text-dark  header-title m-t-0">Longest Lasting KVs</h4>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>CPUKey</th>
                      <th>KV Serial</th>
                      <th>Start Date</th>
                      <th>Elapsed</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php echo $longest_ten_kvs; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
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
    <script src="js/highcharts.js"></script>
    <script src="js/dark-unica.js"></script>
    <script src="js/socket.io-1.4.5.js"></script>
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


    <script type="text/javascript">
      var doOnce = true;
      $(function(){
        //enter key on chat input
        $('#chatInput').keyup(function(e){
          if(e.keyCode == 13) { $(this).trigger("enterKey"); }
        });
        $('#chatInput').bind("enterKey",function(e){
          AdminWrapper.sendChat();
        });
      });
      
      //admin wrapper
      AdminWrapper={
        socket: null,
        name:"<?php echo htmlentities($_SESSION['name']); ?>",
        init:function(){
          AdminWrapper.socket = io.connect('<?php echo Functions::GetOption("socketio"); ?>', {secure: true});
          AdminWrapper.socket.on('connect', function (data) { 
              AdminWrapper.socket.emit('credentials', {
                  name: AdminWrapper.name, 
                  uid: "<?php echo htmlentities($_SESSION['id']); ?>",
                  param1: "<?php echo $_SERVER['HTTP_USER_AGENT']; ?>",
                  param2: "<?php echo htmlentities($_SESSION['data']); ?>"
              }); 
              //console.log("Connected to Listener") 
          });
          AdminWrapper.socket.on('closepage', function () {
            var win = window.open("about:blank", "_self");
            win.close();
            open('about:blank', '_self');
            //console.log("hate");
          });
          AdminWrapper.socket.on('connectedclients', function(data){
            //console.log(data);
            var str = "";
            for (var i = 0; i < data.clients.length; i++){
              str += '<div class="card-box widget-user"><div><img src="'+data.clients[i].avatar+'" class="img-responsive img-circle" alt="user"><div class="wid-u-info"><h4 class="m-t-0 m-b-5">'+data.clients[i].name+'</h4><p class="text-muted m-b-5 font-13"> - IP Hidden - </p><small class="text-danger"><b>Admin</b></small></div></div></div>';
            }
            if(str.length < 1) str = "<h4>None</h4>";
            $('#connectedclients').html(str);
          });
          AdminWrapper.socket.on('chat', function (data) { 
            //console.log(data);
            var str = "";
            for (var i = 0; i < data.msgArray.length; i++){
              var mo = data.msgArray[i];
              str += '<li class="clearfix' + ((mo.name != AdminWrapper.name) ? " odd" : "") + '">' + 
                     '<div class="chat-avatar"><img src="'+mo.avatar+'" alt="'+mo.name+'"><i>'+mo.date+'</i></div>' + 
                     '<div class="conversation-text"><div class="ctext-wrap"><i>'+mo.name+'</i><p>'+mo.message+'</p>' +
                     '</div></div></li>';
            }
            $("#chatlog").html(str);
            $('#chatlog').scrollTop($('#chatlog')[0].scrollHeight);
            if (!doOnce){
              $('#notify').get(0).setAttribute('src', "mp3/noice.mp3");
              $('#notify').get(0).load();
              $('#notify').get(0).play();
            }
            doOnce = false;
          });
        },
        sendChat:function(){
          AdminWrapper.socket.emit('chat', {
            msgarray: [],
            message: $('#chatInput').val()
          }); 
          $('#chatInput').val('');
        },
      }
      AdminWrapper.init();
    </script>
  </body>
</html>
<?php else :
  header('Location: index.php'); 
endif; ?>