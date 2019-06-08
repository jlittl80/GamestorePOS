<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php require_once('../Connections/GamestorePOS.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsTodayTransactions = "SELECT IF(COUNT(transactions.TransID) > 0, COUNT(transactions.TransID), 0) AS 'TodayTrans' FROM transactions WHERE Trans_Date = CURDATE()";
$rsTodayTransactions = mysql_query($query_rsTodayTransactions, $GamestorePOS) or die(mysql_error());
$row_rsTodayTransactions = mysql_fetch_assoc($rsTodayTransactions);
$totalRows_rsTodayTransactions = mysql_num_rows($rsTodayTransactions);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsTodayMoney = "SELECT IF(SUM(trans_lookup.Price) > 0, SUM(trans_lookup.Price), 0) AS 'TodayMoney' FROM trans_lookup LEFT JOIN transactions ON trans_lookup.TransID = transactions.TransID WHERE Trans_Date = CURDATE()";
$rsTodayMoney = mysql_query($query_rsTodayMoney, $GamestorePOS) or die(mysql_error());
$row_rsTodayMoney = mysql_fetch_assoc($rsTodayMoney);
$totalRows_rsTodayMoney = mysql_num_rows($rsTodayMoney);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsThisWeekTransactions = "SELECT IF(COUNT(transactions.TransID) > 0, COUNT(transactions.TransID), 0) AS 'ThisWeekTrans'  FROM transactions  WHERE Trans_Date >= ( SELECT DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) )";
$rsThisWeekTransactions = mysql_query($query_rsThisWeekTransactions, $GamestorePOS) or die(mysql_error());
$row_rsThisWeekTransactions = mysql_fetch_assoc($rsThisWeekTransactions);
$totalRows_rsThisWeekTransactions = mysql_num_rows($rsThisWeekTransactions);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsThisWeekMoney = "SELECT IF(SUM(trans_lookup.Price)>0, SUM(trans_lookup.Price), 0) AS 'ThisWeekMoney' FROM trans_lookup LEFT JOIN transactions ON trans_lookup.TransID = transactions.TransID WHERE Trans_Date >= ( SELECT DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) )";
$rsThisWeekMoney = mysql_query($query_rsThisWeekMoney, $GamestorePOS) or die(mysql_error());
$row_rsThisWeekMoney = mysql_fetch_assoc($rsThisWeekMoney);
$totalRows_rsThisWeekMoney = mysql_num_rows($rsThisWeekMoney);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsThisMonthTransactions = "SELECT IF(COUNT(TransID)>0, COUNT(TransID), 0) AS 'ThisMonthTrans' FROM transactions WHERE Trans_Date >= ( SELECT CONCAT(YEAR(CURDATE()), '-', MONTH(CURDATE()), '-00') )";
$rsThisMonthTransactions = mysql_query($query_rsThisMonthTransactions, $GamestorePOS) or die(mysql_error());
$row_rsThisMonthTransactions = mysql_fetch_assoc($rsThisMonthTransactions);
$totalRows_rsThisMonthTransactions = mysql_num_rows($rsThisMonthTransactions);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsThisMonthMoney = "SELECT IF(SUM(trans_lookup.Price)>0, SUM(trans_lookup.Price), 0) AS 'ThisMonthMoney' FROM trans_lookup LEFT JOIN transactions ON trans_lookup.TransID = transactions.TransID WHERE Trans_Date >= ( SELECT CONCAT(YEAR(CURDATE()), '-', MONTH(CURDATE()), '-00') )";
$rsThisMonthMoney = mysql_query($query_rsThisMonthMoney, $GamestorePOS) or die(mysql_error());
$row_rsThisMonthMoney = mysql_fetch_assoc($rsThisMonthMoney);
$totalRows_rsThisMonthMoney = mysql_num_rows($rsThisMonthMoney);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsThisYearTransactions = "SELECT IF(COUNT(TransID)>0, COUNT(TransID), 0) AS 'ThisYearTrans' FROM transactions WHERE Trans_Date >= ( SELECT CONCAT(YEAR(CURDATE()), '-01-01') )";
$rsThisYearTransactions = mysql_query($query_rsThisYearTransactions, $GamestorePOS) or die(mysql_error());
$row_rsThisYearTransactions = mysql_fetch_assoc($rsThisYearTransactions);
$totalRows_rsThisYearTransactions = mysql_num_rows($rsThisYearTransactions);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsThisYearMoney = "SELECT IF(SUM(trans_lookup.Price)>0, SUM(trans_lookup.Price), 0) AS 'ThisYearMoney' FROM trans_lookup LEFT JOIN transactions ON trans_lookup.TransID = transactions.TransID WHERE Trans_Date >= ( SELECT CONCAT(YEAR(CURDATE()), '-01-01') )";
$rsThisYearMoney = mysql_query($query_rsThisYearMoney, $GamestorePOS) or die(mysql_error());
$row_rsThisYearMoney = mysql_fetch_assoc($rsThisYearMoney);
$totalRows_rsThisYearMoney = mysql_num_rows($rsThisYearMoney);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsTotalTransactions = "SELECT IF(COUNT(transactions.TransID)>0, COUNT(transactions.TransID), 0) AS 'TotalTrans' FROM transactions";
$rsTotalTransactions = mysql_query($query_rsTotalTransactions, $GamestorePOS) or die(mysql_error());
$row_rsTotalTransactions = mysql_fetch_assoc($rsTotalTransactions);
$totalRows_rsTotalTransactions = mysql_num_rows($rsTotalTransactions);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsTotalMoney = "SELECT IF(SUM(trans_lookup.Price)>0, SUM(trans_lookup.Price), 0) AS 'TotalMoney' FROM trans_lookup";
$rsTotalMoney = mysql_query($query_rsTotalMoney, $GamestorePOS) or die(mysql_error());
$row_rsTotalMoney = mysql_fetch_assoc($rsTotalMoney);
$totalRows_rsTotalMoney = mysql_num_rows($rsTotalMoney);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsGameStock = "SELECT Title_Name, Platform, Stock FROM games WHERE Stock < 20 AND Deleted = 0 ORDER BY Stock ASC";
$rsGameStock = mysql_query($query_rsGameStock, $GamestorePOS) or die(mysql_error());
$row_rsGameStock = mysql_fetch_assoc($rsGameStock);
$totalRows_rsGameStock = mysql_num_rows($rsGameStock);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>GamestorePOS :: Home</title>
<script src="../js/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#p1").addClass("selected a");
	if($("#errorField").length == 1) {
		$("#errorField").delay(5000).fadeOut(1000);
	}
});
</script>
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<link href="../css/banner.css" rel="stylesheet" type="text/css" />
<link href="../css/footer.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#7d7d7d; margin:0; padding:0;">
<div id="wrap">
<div id="main">
<?php
	include '../html/banner.php';
?>
<!--↑↑↑ ALL THIS NEEDED AT ALL TIMES ↑↑↑-->
<!--↓↓↓ ALL STUFF IN HERE ↓↓↓-->
<div class="allstuff">
<?php
if(isset($_GET['denied'])) {
	$errorCode = "";
	if($_GET['denied'] == 'staff.php') {
		$errorCode = "You have tried to access the STAFF page, and have been denied access. Please see your manager if you require access to this page.";
	}
	echo '<div id="errorField" class="textbg" style="width:870px; position:relative; background:rgba(232, 13, 12, 1);">'.$errorCode.'</div>';
}
?>
<div class="textbg" style="width:300px; position:relative; float:right;">
<table width="300">
	<tr>
    	<td colspan="3"><h1 align="right">Transaction Sumamry</h1></td>
    </tr>
	<tr>
    	<td width="40%"></td>
    	<td width="30%">Transactions</td>
        <td width="30%">Money Made</td>
    </tr>
    <tr>
    	<td style="text-align:right">Total:</td>
        <td><?php echo $row_rsTotalTransactions['TotalTrans']; ?></td>
        <td>$<?php echo $row_rsTotalMoney['TotalMoney']; ?></td>
    </tr>
    <tr>
    	<td style="text-align:right">Today:</td>
        <td><?php echo $row_rsTodayTransactions['TodayTrans']; ?></td>
        <td>$<?php echo $row_rsTodayMoney['TodayMoney']; ?></td>
    </tr>
    <tr>
    	<td style="text-align:right">This Week:</td>
        <td><?php echo $row_rsThisWeekTransactions['ThisWeekTrans']; ?></td>
        <td>$<?php echo $row_rsThisWeekMoney['ThisWeekMoney']; ?></td>
    </tr>
    <tr>
    	<td style="text-align:right">This Month:</td>
        <td><?php echo $row_rsThisMonthTransactions['ThisMonthTrans']; ?></td>
        <td>$<?php echo $row_rsThisMonthMoney['ThisMonthMoney']; ?></td>
    </tr>
    <tr>
    	<td style="text-align:right">This Year:</td>
        <td><?php echo $row_rsThisYearTransactions['ThisYearTrans']; ?></td>
        <td>$<?php echo $row_rsThisYearMoney['ThisYearMoney']; ?></td>
    </tr>
</table>
</div>
<div class="textbg" style="width:540px; position:relative; float:left;">
<table width="540">
	<tr>
    	<td colspan="2"><h1>Low Stock Levels</h1></td>
    </tr>
	<tr>
    	<td width="50%">Item</td>
        <td width="30%">Platform</td>
        <td width="20%">Amount in Stock</td>
    </tr>
    <?php do { ?>
    <tr>
      <td align="left"><?php echo $row_rsGameStock['Title_Name']; ?></td>
      <td align="left"><?php echo $row_rsGameStock['Platform']; ?></td>
      <td align="left"><?php echo $row_rsGameStock['Stock']; ?></td>
    </tr>
    <?php } while ($row_rsGameStock = mysql_fetch_assoc($rsGameStock)); ?>
</table>
</div>
</div>
<!--↑↑↑ ALL STUFF IN HERE ↑↑↑-->
<!--↓↓↓ ALL THIS NEEDED AT ALL TIMES ↓↓↓-->
</div>
</div>
<?php
	include '../html/footer.html';
?>
</body>
</html>
<?php
mysql_free_result($rsTodayTransactions);

mysql_free_result($rsTodayMoney);

mysql_free_result($rsThisWeekTransactions);

mysql_free_result($rsThisWeekMoney);

mysql_free_result($rsThisMonthTransactions);

mysql_free_result($rsThisMonthMoney);

mysql_free_result($rsThisYearTransactions);

mysql_free_result($rsThisYearMoney);

mysql_free_result($rsTotalTransactions);

mysql_free_result($rsTotalMoney);

//mysql_free_result($rsTotalMembers);

//mysql_free_result($rsTodayMembers);

//mysql_free_result($rsThisWeekMembers);

//mysql_free_result($rsThisMonthMembers);

//mysql_free_result($rsThisYearMembers);

mysql_free_result($rsGameStock);
?>
