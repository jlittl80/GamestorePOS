<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "http://localhost/GamestorePOS/";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<div class="bannerbg"></div>
<div class="bannerwrapper">
<div class="bannercontainer">
	<ul class="bannermenu">
    	<li id="p1" style="width:150px;"><a href="index.php">Home</a></li>
		<li id="p2" style="width:150px;"><a href="members.php">Members</a></li>
		<li id="p3" style="width:140px;"><a href="games.php">Games</a></li>
		<li id="p4" style="width:140px;"><a href="staff.php">Staff</a></li>
		<li id="p5" style="width:169px;"><a href="transactions.php">Transactions</a></li>
        <li id="p6" style="width:149px;"><a href="<?php echo $logoutAction ?>">Log Out</a></li>
	</ul>
</div>
</div>