<?php
include_once 'inc/functions.php';
 
Functions::SecStart();
if(Functions::IsLoggedIn() && Functions::GetLoginLevel() >= 2) :

$totalclients = Functions::GetCurrentClientCount();
$totaltokens = Functions::GetTokenCount("1");

//db
$Pdo = Functions::GetDB();

//sales
$one_day = 0;
$one_week = 0;
$two_week = 0;
$one_month = 0;
$life = 0;
$none = 1;
//last week
$one_day1 = 0;
$one_week1 = 0;
$two_week1 = 0;
$one_month1 = 0;
$life1 = 0;
$none2 = 1;
//last month
$one_day2 = 0;
$one_week2 = 0;
$two_week2 = 0;
$one_month2 = 0;
$life2 = 0;
$none_all = 1;

// -------------------------------------------------------------------------------------

if(date("w", time())==0)
  $Time = $Pdo->quote(strtotime('today')); //1 week starting sunday
else 
  $Time = $Pdo->quote(strtotime('last sunday')); //1 week starting sunday

//1 days
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time AND `days` = 1 LIMIT 1");
if($Result = $Stmt->fetch()) { $one_day = $Result['count']; if($Result['count'] > 0 ) $none = 0; }

//1 week
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time AND `days` = 7 LIMIT 1");
if($Result = $Stmt->fetch()) { $one_week = $Result['count']; if($Result['count'] > 0 ) $none = 0; }

//2 weeks
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time AND `days` = 14 LIMIT 1");
if($Result = $Stmt->fetch()) { $two_week = $Result['count']; if($Result['count'] > 0 ) $none = 0; }

//1 month
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time AND `days` = 30 LIMIT 1");
if($Result = $Stmt->fetch()) { $one_month = $Result['count']; if($Result['count'] > 0 ) $none = 0; }

//lifetime
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time AND `days` = 99999 LIMIT 1");
if($Result = $Stmt->fetch()) { $life = $Result['count']; if($Result['count'] > 0 ) $none = 0; }

$est_imcome = ($one_day * 7.50) + ($one_week * 25) + ($two_week * 50) + ($one_month * 85) + ($life * 500);

// -------------------------------------------------------------------------------------

//1 days
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `days` = 1 LIMIT 1");
if($Result = $Stmt->fetch()) { $one_day_all = $Result['count']; if($Result['count'] > 0 ) $none_all = 0; }

//1 week
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `days` = 7 LIMIT 1");
if($Result = $Stmt->fetch()) { $one_week_all = $Result['count']; if($Result['count'] > 0 ) $none_all = 0; }

//2 weeks
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `days` = 14 LIMIT 1");
if($Result = $Stmt->fetch()) { $two_week_all = $Result['count']; if($Result['count'] > 0 ) $none_all = 0; }

//1 month
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `days` = 30 LIMIT 1");
if($Result = $Stmt->fetch()) { $one_month_all = $Result['count']; if($Result['count'] > 0 ) $none_all = 0; }

//lifetime
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `days` = 99999 LIMIT 1");
if($Result = $Stmt->fetch()) { $life_all = $Result['count']; if($Result['count'] > 0 ) $none_all = 0; }

$est_imcome_all = ($one_day_all * 7.50) + ($one_week_all * 25) + ($two_week_all * 50) + ($one_month_all * 85) + ($life_all * 500);


// -------------------------------------------------------------------------------------

//last week
if(date("w", time())==0)
  $Time2 = $Pdo->quote(strtotime('today -1 week'));
else 
  $Time2 = $Pdo->quote(strtotime('last sunday -1 week'));

//1 days
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time2 AND `generated_date` <= $Time AND `days` = 1 LIMIT 1");
if($Result = $Stmt->fetch()) $one_day1 = $Result['count'];

//1 week
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time2 AND `generated_date` <= $Time AND `days` = 7 LIMIT 1");
if($Result = $Stmt->fetch()) $one_week1 = $Result['count'];

//2 weeks
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time2 AND `generated_date` <= $Time AND `days` = 14 LIMIT 1");
if($Result = $Stmt->fetch()) $two_week1 = $Result['count'];

//1 month
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time2 AND `generated_date` <= $Time AND `days` = 30 LIMIT 1");
if($Result = $Stmt->fetch()) $one_month1 = $Result['count'];

//lifetime
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time2 AND `generated_date` <= $Time AND `days` = 99999 LIMIT 1");
if($Result = $Stmt->fetch()) $life1 = $Result['count'];

$est_imcome_last_week = ($one_day1 * 7.50) + ($one_week1 * 25) + ($two_week1 * 50) + ($one_month1 * 85) + ($life1 * 500);

// -------------------------------------------------------------------------------------

//this month
$Time3 = $Pdo->quote(strtotime(date('Y-m-1'))+2592000);
$Time4 = $Pdo->quote(strtotime(date('Y-m-01')));

//1 days
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time4 AND `generated_date` <= $Time3 AND `days` = 1 LIMIT 1");
if($Result = $Stmt->fetch()) { $one_day2 = $Result['count']; if($Result['count'] > 0 ) $none2 = 0; }

//1 week
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time4 AND `generated_date` <= $Time3 AND `days` = 7 LIMIT 1");
if($Result = $Stmt->fetch()) { $one_week2 = $Result['count']; if($Result['count'] > 0 ) $none2 = 0; }

//2 weeks
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time4 AND `generated_date` <= $Time3 AND `days` = 14 LIMIT 1");
if($Result = $Stmt->fetch()) { $two_week2 = $Result['count']; if($Result['count'] > 0 ) $none2 = 0; }

//1 month
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time4 AND `generated_date` <= $Time3 AND `days` = 30 LIMIT 1");
if($Result = $Stmt->fetch()) { $one_month2 = $Result['count']; if($Result['count'] > 0 ) $none2 = 0; }

//lifetime
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time4 AND `generated_date` <= $Time3 AND `days` = 99999 LIMIT 1");
if($Result = $Stmt->fetch()) { $life2 = $Result['count']; if($Result['count'] > 0 ) $none2 = 0; }

$est_imcome_this_month = ($one_day2 * 7.50) + ($one_week2 * 25) + ($two_week2 * 50) + ($one_month2 * 85) + ($life2 * 500);

// -------------------------------------------------------------------------------------

//last month
$Time5 = $Pdo->quote(strtotime(date('Y-m-1')));
$Time6 = $Pdo->quote(strtotime(date('Y-m-1'))-2592000);

//1 days
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time6 AND `generated_date` <= $Time5 AND `days` = 1 LIMIT 1");
if($Result = $Stmt->fetch()) $one_day3 = $Result['count'];

//1 week
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time6 AND `generated_date` <= $Time5 AND `days` = 7 LIMIT 1");
if($Result = $Stmt->fetch()) $one_week3 = $Result['count'];

//2 weeks
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time6 AND `generated_date` <= $Time5 AND `days` = 14 LIMIT 1");
if($Result = $Stmt->fetch()) $two_week3 = $Result['count'];

//1 month
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time6 AND `generated_date` <= $Time5 AND `days` = 30 LIMIT 1");
if($Result = $Stmt->fetch()) $one_month3 = $Result['count'];

//lifetime
$Stmt = $Pdo->query("SELECT COUNT(*) as `count` FROM `tokens` WHERE `redeemed` = 1 AND `generated_date` >= $Time6 AND `generated_date` <= $Time5 AND `days` = 99999 LIMIT 1");
if($Result = $Stmt->fetch()) $life3 = $Result['count'];

$est_imcome_last_month = ($one_day3 * 7.50) + ($one_week3 * 25) + ($two_week3 * 50) + ($one_month3 * 85) + ($life3 * 500);

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

// disk usage
$TotalHdd = disk_total_space("/");
$FreeHdd = disk_free_space("/");
$UsedHdd = $TotalHdd - $FreeHdd;
//get percent
$HddUsage = round(($UsedHdd/$TotalHdd)*100);
//pretty print
$TotalHdd = Functions::PrettyPrintMem($TotalHdd);
$FreeHdd = Functions::PrettyPrintMem($FreeHdd);
$UsedHdd = Functions::PrettyPrintMem($UsedHdd);



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
          <div class="col-sm-6 col-lg-3">
            <div class="widget-simple-chart text-right card-box">
              <div class="circliful-chart" data-dimension="90" data-text="<?php echo round($totalclients/10); ?>%" data-width="5" data-fontsize="14" data-percent="<?php echo $totalclients/10; ?>" data-fgcolor="#5fbeaa" data-bgcolor="#505A66"></div>
              <h3 class="text-success"><span class="counter"><?php echo $totalclients; ?></span> / 1,000</h3>
              <p class="text-muted text-nowrap">Total Clients</p>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="widget-simple-chart text-right card-box">
              <div class="circliful-chart" data-dimension="90" data-text="<?php echo round($est_imcome/10); ?>%" data-width="5" data-fontsize="14" data-percent="<?php echo $est_imcome/10; ?>" data-fgcolor="#f76397" data-bgcolor="#505A66"></div>
              <h3 class="text-pink">$ <span class="counter"><?php echo number_format($est_imcome, 2, ".", ","); ?></span> / $ 1,000.00</h3>
              <p class="text-muted text-nowrap">Estimated Earnings</p>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="widget-simple-chart text-right card-box">
              <div class="circliful-chart" data-dimension="90" data-text="<?php echo round($totaltokens/10); ?>%" data-width="5" data-fontsize="14" data-percent="<?php echo $totaltokens/10; ?>" data-fgcolor="#3bafda" data-bgcolor="#505A66"></div>
              <h3 class="text-primary"><span class="counter"><?php echo $totaltokens; ?></span> / 1,000</h3>
              <p class="text-muted text-nowrap">Total Tokens</p>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="widget-simple-chart text-right card-box">
              <div class="circliful-chart" data-dimension="90" data-text="<?php echo round((Functions::GetOption('max_online')/Functions::GetCurrentClientCount(""))*100); ?>%" data-width="5" data-fontsize="14" data-percent="<?php echo round((Functions::GetOption('max_online')/Functions::GetCurrentClientCount(""))*100); ?>" data-fgcolor="#98a6ad" data-bgcolor="#505A66"></div>
              <h3 class="text-inverse"><span class="counter"><?php echo Functions::GetOption('max_online')."</span> / ".Functions::GetCurrentClientCount(""); ?></h3>
              <p class="text-muted text-nowrap">Most Clients Online</p>
            </div>
          </div>
        </div>
        <!-- end row -->
        <div class="row">
          <div class="col-lg-4">
            <div class="card-box">
              <h4 class="text-dark header-title m-t-0 m-b-30">Weekly Sales Report</h4>
              <div class="widget-chart text-center">
                <div id="tokenpiechart"></div>
                <ul class="list-inline m-t-15">
                  <li>
                    <h5 class="text-muted m-t-20">Target</h5>
                    <h4 class="m-b-0">$500</h4>
                  </li>
                  <li>
                    <h5 class="text-muted m-t-20">This week</h5>
                    <h4 class="m-b-0">$ <?php echo number_format($est_imcome, 2, ".", ","); ?></h4>
                  </li>
                  <li>
                    <h5 class="text-muted m-t-20">Last Week</h5>
                    <h4 class="m-b-0">$ <?php echo number_format($est_imcome_last_week, 2, ".", ","); ?></h4>
                  </li>
                </ul>
              </div>
            </div>
            <div class="card-box">
              <h4 class="text-dark header-title m-t-0 m-b-30">Monthly Sales Report</h4>
              <div class="widget-chart text-center">
                <div id="tokenpiechartmonth"></div>
                <ul class="list-inline m-t-15">
                  <li>
                    <h5 class="text-muted m-t-20">Target</h5>
                    <h4 class="m-b-0">$2,000</h4>
                  </li>
                  <li>
                    <h5 class="text-muted m-t-20">This Month</h5>
                    <h4 class="m-b-0">$ <?php echo number_format($est_imcome_this_month, 2, ".", ","); ?></h4>
                  </li>
                  <li>
                    <h5 class="text-muted m-t-20">Last Month</h5>
                    <h4 class="m-b-0">$ <?php echo number_format($est_imcome_last_month, 2, ".", ","); ?></h4>
                  </li>
                </ul>
              </div>
            </div>
            <div class="card-box">
              <h4 class="text-dark header-title m-t-0 m-b-30">All-Time Sales Report</h4>
              <div class="widget-chart text-center">
                <div id="tokenpiechartall"></div>
                <ul class="list-inline m-t-15">
                  <li>
                    <h5 class="text-muted m-t-20">Overall</h5>
                    <h4 class="m-b-0">$ <?php echo number_format($est_imcome_all, 2, ".", ","); ?></h4>
                  </li>
                </ul>
              </div>
            </div>
            <div class="consoleLog">
              <span class="hideScroll consoleBody" id="conLogger"></span>
              <div class="consoleHead" id="consoleScroll">
                <span class="glyphicon glyphicon-cog"></span>
                <div class="form-group">
                  <label for="showWrapper_q" class="col-lg-4 control-label">Show Wrapper Logs</label>
                  <div class="col-lg-8">
                    <input id="showWrapper_q" type="checkbox" checked></input>
                  </div>
                </div>
                <br>
                <br>
                <div class="form-group">
                  <label for="showUrl_q" class="col-lg-4 control-label">Show URL Logs</label>
                  <div class="col-lg-8">
                    <input id="showUrl_q" type="checkbox" checked></input>
                  </div>
                </div>
                <br>
                <br>
                <div class="form-group">
                  <label for="pauseLogs_q" class="col-lg-4 control-label">Pause Logs</label>
                  <div class="col-lg-8">
                    <input id="pauseLogs_q" type="checkbox"></input>
                  </div>
                </div>
                <br>
                <br>
                <div class="form-group">
                  <label for="query_q" class="col-lg-3 control-label">Search</label>
                  <div class="col-lg-9">
                    <input name="query_q" id="query_q" style="color:black;" type="textbox"></input>
                  </div>
                </div>
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
            <div class="card-box">
              <h4 class="text-dark  header-title m-t-0">HDD Usage</h4>
              <div class="progress progress-lg m-b-5">
                <div class="progress-bar progress-bar-purple" role="progressbar" aria-valuenow="<?php echo $HddUsage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $HddUsage; ?>%;">
                  <?php echo $HddUsage; ?>%
                </div>
              </div>
              <h4 class="text-dark  header-title m-t-0">Clients Online</h4>
              <div class="row">
                <div class="col-md-12">
                  <div class="progress progress-lg m-b-5">
                    <div class="progress-bar progress-bar-warning" id="prog_clients" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
                  </div>
                  <div align="center" id="prog_clients_text" style="color:white;margin-top: -37px; margin-bottom: 15px;">0 / 50</div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div align="center">
                    <div style="width:10px;height:10px;background:#ffaa00;border:1px solid #ffbe3d;display:inline-block;"></div> Number of Clients
                  </div>
                </div>
              </div>
              <h4 class="text-dark  header-title m-t-0">Program Memory Usage</h4>
              <div class="row">
                <div class="col-md-12">
                  <div class="progress progress-lg m-b-5">
                    <div class="progress-bar progress-bar-primary" id="prog_mem_used" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
                    <div class="progress-bar progress-bar-purple" id="prog_mem_free" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
                    <div class="progress-bar progress-bar-danger" id="prog_mem_total" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div align="center">
                    <div style="width:10px;height:10px;background:#3bafda;border:1px solid #47c1ef;display:inline-block;"></div> Used Memory 
                    <div style="margin-left:20px;width:10px;height:10px;background:#7266ba;border:1px solid #8e7df3;display:inline-block;"></div> Free Memory 
                    <div style="margin-left:20px;width:10px;height:10px;background:#ef5350;border:1px solid #fd6d6b;display:inline-block;"></div> Total Memory 
                  </div>
                </div>
              </div>
              <h4 class="text-dark  header-title m-t-0">System Memory Usage</h4>
              <div class="row">
                <div class="col-md-12">
                  <div class="progress progress-lg m-b-5">
                    <div class="progress-bar progress-bar-pink" id="sys_mem_used" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
                    <div class="progress-bar progress-bar-success" id="sys_mem_free" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
                    <div class="progress-bar progress-bar-danger" id="sys_mem_total" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div align="center">
                    <div style="width:10px;height:10px;background:#f76397;border:1px solid #ff83ae;display:inline-block;"></div> Used Memory 
                    <div style="margin-left:20px;width:10px;height:10px;background:#00b19d;border:1px solid #00d4bc;display:inline-block;"></div> Free Memory 
                    <div style="margin-left:20px;width:10px;height:10px;background:#ef5350;border:1px solid #fd6d6b;display:inline-block;"></div> Total Memory 
                  </div>
                </div>
              </div>
              <h4 class="text-dark  header-title m-t-0">System Load</h4>
              <div class="row">
                <div class="col-md-12">
                  <div class="progress progress-lg m-b-5">
                    <div class="progress-bar progress-bar-info" id="sys_load" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
                  </div>
                  <div align="center" id="sys_load_text" style="color:white;margin-top: -37px; margin-bottom: 15px;">0%</div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div align="center">
                    <div style="width:10px;height:10px;background:#3ddcf7;border:1px solid #66e9ff;display:inline-block;"></div> CPU Load
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
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
              </div>
              <div class="col-md-6">
                <div class="panel panel-border panel-purple">
                  <div class="panel-heading">
                    <h3 class="panel-title">Connected Clients</h3>
                  </div>
                  <div class="panel-body" id="connectedclients">
                  </div>
                </div>
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
      var clientchart, memchart, memchart2, cpuchart;
      var doOnce = true;
      var hasntScrolled = true;
      var paused = false;
      var showWrapper = true;
      var showURL = true;
      var lastLogData = null;
      var currentLogData = null;
      $(function(){
        //weekly sales
        $('#tokenpiechart').sparkline([<?php echo $one_day.", ".$one_week.", ".$two_week.", ".$one_month.", ".$life.", ".$none; ?>], {
          type: 'pie',
          width: '165',
          height: '165',
          sliceColors: ['#dcdcdc', '#3bafda', '#ef5350', '#00b19d', '#f76397', '#333333'],
          tooltipFormat: '{{offset:offset}} ({{value}} Token(s))',
          tooltipValueLookups: {
            'offset': {
              0: '1 Day',
              1: '1 Week',
              2: '2 Weeks',
              3: '1 Month',
              4: 'Unlimited',
              5: 'No Tokens'
            }
          },
        });
        //monthly sales
        $('#tokenpiechartmonth').sparkline([<?php echo $one_day2.", ".$one_week2.", ".$two_week2.", ".$one_month2.", ".$life2.", ".$none2; ?>], {
          type: 'pie',
          width: '165',
          height: '165',
          sliceColors: ['#dcdcdc', '#3bafda', '#ef5350', '#00b19d', '#f76397', '#333333'],
          tooltipFormat: '{{offset:offset}} ({{value}} Token(s))',
          tooltipValueLookups: {
            'offset': {
              0: '1 Day',
              1: '1 Week',
              2: '2 Weeks',
              3: '1 Month',
              4: 'Unlimited',
              5: 'No Tokens'
            }
          },
        });
        //all-time sales
        $('#tokenpiechartall').sparkline([<?php echo $one_day_all.", ".$one_week_all.", ".$two_week_all.", ".$one_month_all.", ".$life_all.", ".$none_all; ?>], {
          type: 'pie',
          width: '165',
          height: '165',
          sliceColors: ['#dcdcdc', '#3bafda', '#ef5350', '#00b19d', '#f76397', '#333333'],
          tooltipFormat: '{{offset:offset}} ({{value}} Token(s))',
          tooltipValueLookups: {
            'offset': {
              0: '1 Day',
              1: '1 Week',
              2: '2 Weeks',
              3: '1 Month',
              4: 'Unlimited',
              5: 'No Tokens'
            }
          },
        });
        //console toggles and search
        $('#showWrapper_q').checkboxpicker({
          html: true,
          offLabel: '<span class="glyphicon glyphicon-remove">',
          onLabel: '<span class="glyphicon glyphicon-ok">'
        }).change(function() {
          showWrapper = $('#showWrapper_q').is(':checked');
          parseConsoleLogs();
        });
        $('#showUrl_q').checkboxpicker({
          html: true,
          offLabel: '<span class="glyphicon glyphicon-remove">',
          onLabel: '<span class="glyphicon glyphicon-ok">'
        }).change(function() {
          showURL = $('#showUrl_q').is(':checked');
          parseConsoleLogs();
        });
        $('#pauseLogs_q').checkboxpicker({
          html: true,
          offLabel: '<span class="glyphicon glyphicon-remove">',
          onLabel: '<span class="glyphicon glyphicon-ok">'
        }).change(function() {
          paused = $('#pauseLogs_q').is(':checked');
          parseConsoleLogs();
        });
        $('#query_q').on('input propertychange paste', function() {
          parseConsoleLogs();
        });
        //enter key on chat input
        $('#chatInput').keyup(function(e){
          if(e.keyCode == 13) { $(this).trigger("enterKey"); }
        });
        $('#chatInput').bind("enterKey",function(e){
          AdminWrapper.sendChat();
        });
      });
      //define some functions before wrapper
      function parseConsoleLogs(){
        //if paused dont edit
        if(!paused) lastLogData = currentLogData;
        //get modifiers
        var query = $('input[name="query_q"]').val();
        //init string
        var str = "";
        //loop logs
        for (var i = 0; i < lastLogData.logs.length; i++){
          if(query != "" && query !== undefined){
            if(lastLogData.logs[i].cpukey.toLowerCase().indexOf(query.toLowerCase()) >= 0 || lastLogData.logs[i].name.toLowerCase().indexOf(query.toLowerCase()) >= 0 || lastLogData.logs[i].message.toLowerCase().indexOf(query.toLowerCase()) >= 0 || lastLogData.logs[i].ip.toLowerCase().indexOf(query.toLowerCase()) >= 0){
              if(!showURL && lastLogData.logs[i].name == "URL") continue;
              if(!showWrapper && lastLogData.logs[i].name == "Wrapper") continue;
              str += lastLogData.logs[i].message + "<br>";
            }
          } else {
            if(!showURL && lastLogData.logs[i].name == "URL") continue;
            if(!showWrapper && lastLogData.logs[i].name == "Wrapper") continue;
            str += lastLogData.logs[i].message + "<br>";
          }
        }
        //get pre height
        var height = $("#conLogger").height(), scrollHeight = $("#conLogger").get(0).scrollHeight;
        var scroll = $("#conLogger").scrollTop() === (scrollHeight - height - 40);
        //set text
        $("#conLogger").html(str);
        //if scroll == bottom or hasnt scrolled to bottom then go to bottom
        if(scroll || hasntScrolled){
          $("#conLogger").scrollTop($("#conLogger")[0].scrollHeight);
          if($("#conLogger").scrollTop() === (scrollHeight - height - 40)) {
            hasntScrolled = false;
          }
        }
      }
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
          AdminWrapper.socket.on('stats', function (data) { 
            //clients
            var dividend = 50;
            while (data.clients > dividend) dividend += 50;
            var clients = Math.round((data.clients/dividend)*100);
            $('#prog_clients').css('width', clients+'%').attr('aria-valuenow', clients);
            $('#prog_clients_text').html(data.clients+" / "+dividend);
            //program memory usage
            var prog_free = Math.round((data.prog_Freemem / data.prog_Totalmem)*100)-10;
            var prog_used = (100-prog_free)-10;
            var prod_total = 100 - prog_free - prog_used;
            $('#prog_mem_free').css('width', prog_free+'%').attr('aria-valuenow', prog_free).html(data.prog_Freemem+" MB");
            $('#prog_mem_used').css('width', prog_used+'%').attr('aria-valuenow', prog_used).html(data.prog_Usedmem+" MB");
            $('#prog_mem_total').css('width', prod_total+'%').attr('aria-valuenow', prod_total).html(data.prog_Totalmem+" MB");
            //system memory usage
            var sys_free = Math.round((data.sys_freemem / data.sys_totalmem)*100)-10;
            var sys_used = (100-sys_free)-10;
            var sys_total = 100 - sys_free - sys_used;
            $('#sys_mem_free').css('width', sys_free+'%').attr('aria-valuenow', sys_free).html(data.sys_freemem+" GB");
            $('#sys_mem_used').css('width', sys_used+'%').attr('aria-valuenow', sys_used).html(data.sys_usedmem+" GB");
            $('#sys_mem_total').css('width', sys_total+'%').attr('aria-valuenow', sys_total).html(data.sys_totalmem+" GB");
            //system load
            $('#sys_load').css('width', data.load+'%').attr('aria-valuenow',data.load);
            $('#sys_load_text').html(data.load+"%");
          });
          AdminWrapper.socket.on('connectedclients', function(data){
            console.log(data);
            var str = "";
            for (var i = 0; i < data.clients.length; i++){
              str += '<div class="card-box widget-user"><div><img src="'+data.clients[i].avatar+'" class="img-responsive img-circle" alt="user"><div class="wid-u-info"><h4 class="m-t-0 m-b-5">'+data.clients[i].name+'</h4><p class="text-muted m-b-5 font-13">'+data.clients[i].ip+'</p>';
              if(data.clients[i].level == 3) str += '<small class="text-danger"><b>Admin</b></small>';
              else if(data.clients[i].level == 2) str += '<small class="text-warning"><b>Staff</b></small>';
              else if(data.clients[i].level == 1) str += '<small class="text-primary"><b>Seller</b></small>';
              else str += '<small class="text-info"><b>Registered</b></small>';
              str += '</div></div></div>';
            }
            if(str.length < 1) str = "<h4>None</h4>";
            $('#connectedclients').html(str);
          });
          AdminWrapper.socket.on('logs', function (data) {
            currentLogData = data;
            parseConsoleLogs();
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