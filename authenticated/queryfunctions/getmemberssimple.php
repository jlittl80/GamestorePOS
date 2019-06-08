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
<?php require_once('../../Connections/GamestorePOS.php');

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
?>
<?php


	$openHtml = '<div class="textbg" id="simpleQueryResult" style="width:880px; position:relative; float:left;">';
	$closeHtml = "</div>";
	$queryString = "SELECT ";
	
	$fields = $_POST['fields']; //All the fields that are wanted (In an ARRAY)
	$noOfFields = $_POST['numberOfFields']; //The number of fields in the above array
	$whereUsed = $_POST['whereUsed']; //WHERE used (Either true or False)
	$whereField = $_POST['whereField']; //Field that is being used in the WHERE statement
	$whereFieldValue = $_POST['whereFieldValue']; // The value that wants to be used in the WHERE
	$andsUsed = $_POST['andsUsed']; //If a AND has been used
	$amountOfAndsUsed = $_POST['amountOfAndsUsed']; //The number of ANDS used
	$andFields = $_POST['andFields']; //Fields wanted in the AND ststements (In an ARRAY)
	$andFieldValues = $_POST['andFieldValues']; //Values wanted in the AND statements (In an ARRAY)
	
	//Adds fields wanted
	$tempDoField = 0;
	do {
		if ($tempDoField < ($noOfFields -1)) {
			$queryString .= $fields[$tempDoField].", ";
		}
		else {
			$queryString .= $fields[$tempDoField]." ";
		}
		$tempDoField++;
	} while ($tempDoField < $noOfFields);
	
	//Adds FROM
	$queryString .= "FROM members ";
	
	//Adds WHERE
	if ($whereUsed == 1) {
		$queryString .= "WHERE ".$whereField." = '".$whereFieldValue."' ";
	}
	
	//Adds ANDS
	$tempAndField = 0;
	if ($andsUsed == 1) {
		do {
			$queryString .= "AND ".$andFields[$tempAndField]." = '".$andFieldValues[$tempAndField]."' ";
			$tempAndField++;
		} while ($tempAndField < $amountOfAndsUsed);
	}
	
	mysql_select_db($database_GamestorePOS, $GamestorePOS);
	$query_rsSimpleMemberSearch = $queryString;
	$rsSimpleMemberSearch = mysql_query($query_rsSimpleMemberSearch, $GamestorePOS) or die(mysql_error());
	$row_rsSimpleMemberSearch = mysql_fetch_assoc($rsSimpleMemberSearch);
	$totalRows_rsSimpleMemberSearch = mysql_num_rows($rsSimpleMemberSearch);mysql_select_db($database_GamestorePOS, $GamestorePOS);
	
	
	//Making table
	//Calculates column widths
	//Makes column headers from $feilds ARRAY
	//Inserts into table using do while loop and $fields ARRAY
	$columnWidth = ('880' / $noOfFields);
?>
<h1>Simple Search Results</h1>
<h3>QUERY: <?php echo $queryString; ?></h3>
<table width="100%" border="1">
  <tr>
    <?php $tempDoTableTitleRow = 0; do { ?>
    <td width="<?php echo $columnWidth; ?>"><?php echo $fields[$tempDoTableTitleRow]; ?></td>
    <?php $tempDoTableTitleRow++; } while ($tempDoTableTitleRow < $noOfFields) ?>
  </tr>
  <?php do { ?>
    <tr> 
    	<?php $tempDoTable = 0; do { ?>
    	<td width="<?php echo $columnWidth; ?>"><?php echo $row_rsSimpleMemberSearch[$fields[$tempDoTable]]; ?></td>
    	<?php $tempDoTable++; } while ($tempDoTable < $noOfFields) ?>
    </tr>
    <?php } while ($row_rsSimpleMemberSearch = mysql_fetch_assoc($rsSimpleMemberSearch)); ?>
</table>
