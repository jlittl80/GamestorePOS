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

$MM_restrictGoTo = "../../index.php";
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
<?php require_once('../../Connections/GamestorePOS.php'); ?>
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

if ((isset($_POST['barcodeDelete'])) && ($_POST['amountDelete'] != "")) {
  $updateSQL = sprintf("UPDATE games SET Stock=Stock - %s WHERE GameID=%s",
                       GetSQLValueString($_POST['amountDelete'], "int"),
                       GetSQLValueString($_POST['barcodeDelete'], "int"));

  mysql_select_db($database_GamestorePOS, $GamestorePOS);
  $Result1 = mysql_query($updateSQL, $GamestorePOS) or die(mysql_error());

  $updateGoTo = "../success.php?redir=../games.php&action=deleteStock";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if(isset($_POST['barcode']) && isset($_POST['amount'])) {
	$queryString = "SELECT Title_Name, Stock FROM games WHERE GameID = ".$_POST['barcode'];
	mysql_select_db($database_GamestorePOS, $GamestorePOS);
	$query_rsSelectStock = $queryString;
	$rsSelectStock = mysql_query($query_rsSelectStock, $GamestorePOS) or die(mysql_error());
	$row_rsSimpleMemberSearch = mysql_fetch_assoc($rsSelectStock);
	$totalRows_rsSelectStock = mysql_num_rows($rsSelectStock);mysql_select_db($database_GamestorePOS, $GamestorePOS);
?>
<h1>Remove Stock</h1>
<p>Are you sure you would like to remove <strong><?php echo $_POST['amount']; ?></strong> from the product <strong><?php echo $row_rsSimpleMemberSearch['Title_Name']; ?></strong>?
<br />
This action cannot be undone!</p>
<form action="queryfunctions/removestock.php" method="post">
<input type="hidden" value="<?php echo $_POST['barcode']; ?>" name="barcodeDelete" />
<input type="hidden" value="<?php echo $_POST['amount']; ?>" name="amountDelete" />
<input type="button" value="CANCEL" style="float:left;" onClick="window.close()" /><input type="submit" value="DELETE" align="right" style="float:right;" />
</form>
<?php
}
?>