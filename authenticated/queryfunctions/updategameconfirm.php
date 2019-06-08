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
<?php require_once('../../Connections/GamestorePOS.php'); if (!function_exists("GetSQLValueString")) {
	
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

<?php //Construct, Execute and Show Result before UPDATE

	$updatesUsed = $_POST['updatesUsed'];
	$updateFieldsWanted = $_POST['updateFieldsWanted'];
	$updateFieldValues = $_POST['updateFieldValues'];
	$whereUpdateField = $_POST['whereUpdateField'];
	$whereUpdateFieldValue = $_POST['whereUpdateFieldValue'];
	$andsUpdatesUsed = $_POST['andsUpdatesUsed'];
	$andUpdatesFields = $_POST['andUpdatesFields'];
	$andUpdatesFieldsValues = $_POST['andUpdatesFieldsValues'];
	
	$beforeUpdate = "SELECT ";
	
	$doUpdatesTenp = 0;
	do {
		if ($doUpdatesTenp < ($updatesUsed -1)) {
			$beforeUpdate .= $updateFieldsWanted[$doUpdatesTenp].", ";
		}
		else {
			$beforeUpdate .= $updateFieldsWanted[$doUpdatesTenp]." ";
		}
		$doUpdatesTenp++;
	} while($doUpdatesTenp < $updatesUsed);
	
	$beforeUpdate .= "FROM games ";
	
	$beforeUpdate .= "WHERE ".$whereUpdateField."='".$whereUpdateFieldValue."' ";
	
	$doAndTemp = 0;
	if ($andsUpdatesUsed > 0) {
		do {
			$beforeUpdate .= "AND ".$andUpdatesFields[$doAndTemp]."= '".$andUpdatesFieldsValues[$doAndTemp]."' ";
			$doAndTemp++;
		} while($doAndTemp < $andsUpdatesUsed);
	}
	
	mysql_select_db($database_GamestorePOS, $GamestorePOS);
	$query_rsBeforeUpdate = $beforeUpdate;
	$rsBeforeUpdate = mysql_query($query_rsBeforeUpdate, $GamestorePOS) or die(mysql_error());
	$row_rsBeforeUpdate = mysql_fetch_assoc($rsBeforeUpdate);
	$totalRows_rsBeforeUpdate = mysql_num_rows($rsBeforeUpdate);mysql_select_db($database_GamestorePOS, $GamestorePOS);
?>
<h1>Before UPDATE</h1>
<h3>QUERY: <?php echo $beforeUpdate; ?></h3>
<table width="100%" border="1">
  <tr>
    <?php $tempDoTableTitleRowBEFORE = 0; do { ?>
    <td><?php echo $updateFieldsWanted[$tempDoTableTitleRowBEFORE]; ?></td>
    <?php $tempDoTableTitleRowBEFORE++; } while ($tempDoTableTitleRowBEFORE < $updatesUsed) ?>
  </tr>
  <?php do { ?>
    <tr> 
    	<?php $tempDoTableBEFORE = 0; do { ?>
    	<td><?php echo $row_rsBeforeUpdate[$updateFieldsWanted[$tempDoTableBEFORE]]; ?></td>
    	<?php $tempDoTableBEFORE++; } while ($tempDoTableBEFORE < $updatesUsed) ?>
    </tr>
    <?php } while ($row_rsBeforeUpdate = mysql_fetch_assoc($rsBeforeUpdate)); ?>
</table>
<p>IF you continue with the UPDATE statement, these fields WILL be updated.</p>

<?php //Construct UPDATE query 

	$updateQuery = "UPDATE games SET ";
	
	$doUpdatesTenp = 0;
	do {
		if ($doUpdatesTenp < ($updatesUsed -1)) {
			$updateQuery .= $updateFieldsWanted[$doUpdatesTenp]."='".$updateFieldValues[$doUpdatesTenp]."', ";
		}
		else {
			$updateQuery .= $updateFieldsWanted[$doUpdatesTenp]."='".$updateFieldValues[$doUpdatesTenp]."' ";
		}
		$doUpdatesTenp++;
	} while($doUpdatesTenp < $updatesUsed);
	
	$updateQuery .= "WHERE ".$whereUpdateField."='".$whereUpdateFieldValue."'";
	
	$doAndTemp = 0;
	if ($andsUpdatesUsed > 0) {
		do {
			$updateQuery .= "AND ".$andUpdatesFields[$doAndTemp]."= '".$andUpdatesFieldsValues[$doAndTemp]."' ";
			$doAndTemp++;
		} while($doAndTemp < $andsUpdatesUsed);
	}
?>

<?php 	
	//Construct, Execute and Show Result after UPDATE 
	//Uses a lot of items from the 'Before Update' part, less code that was
	//Uses same query, simply resets what row it starts at back to 0
	
	mysql_data_seek($rsBeforeUpdate, 0);
	$row_rsBeforeUpdate = mysql_fetch_assoc($rsBeforeUpdate);
?>
<h1>After UPDATE</h1>
<h3>QUERY: <?php echo $beforeUpdate; ?></h3>
<table width="100%" border="1">
  <tr>
    <?php $tempDoTableTitleRowAFTER = 0; do { ?>
    <td><?php echo $updateFieldsWanted[$tempDoTableTitleRowAFTER]; ?></td>
    <?php $tempDoTableTitleRowAFTER++; } while ($tempDoTableTitleRowAFTER < $updatesUsed) ?>
  </tr>
  <?php do { ?>
    <tr> 
    	<?php $tempDoTableAFTER = 0; do { ?>
    	<td><?php echo $updateFieldValues[$tempDoTableAFTER]; ?></td>
    	<?php $tempDoTableAFTER++; } while ($tempDoTableAFTER < $updatesUsed) ?>
    </tr>
    <?php } while ($row_rsBeforeUpdate = mysql_fetch_assoc($rsBeforeUpdate)); ?>
</table>
<p>Are you sure you want to perform this UPDATE?</p>
<form action="queryfunctions/updategame.php" method="post">
<input type="hidden" value="<?php echo $updateQuery; ?>" name="updateQuery" />
<input type="button" value="CANCEL" style="float:left;" onClick="window.close()" /> <input type="submit" value="UPDATE" align="right" style="float:right;" />
</form>