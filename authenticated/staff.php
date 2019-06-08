<?php require_once('../Connections/GamestorePOS.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "10,9,8,,6,5,4,3,2,1";
$MM_donotCheckaccess = "false";

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
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php?denied=staff.php";
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

$colname_rsGetStaffRoles = "-1";
if (isset($_SESSION['MM_UserGroup'])) {
  $colname_rsGetStaffRoles = $_SESSION['MM_UserGroup'];
}
mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsGetStaffRoles = sprintf("SELECT * FROM staff_roles WHERE RoleID >= %s ORDER BY RoleID ASC", GetSQLValueString($colname_rsGetStaffRoles, "int"));
$rsGetStaffRoles = mysql_query($query_rsGetStaffRoles, $GamestorePOS) or die(mysql_error());
$row_rsGetStaffRoles = mysql_fetch_assoc($rsGetStaffRoles);
$totalRows_rsGetStaffRoles = mysql_num_rows($rsGetStaffRoles);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsShowColumns = "SHOW columns FROM staff WHERE Field != 'Password'";
$rsShowColumns = mysql_query($query_rsShowColumns, $GamestorePOS) or die(mysql_error());
$row_rsShowColumns = mysql_fetch_assoc($rsShowColumns);
$totalRows_rsShowColumns = mysql_num_rows($rsShowColumns);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsCountStaffTableColumns = "SELECT COUNT(COLUMN_NAME) FROM information_schema.columns WHERE table_schema = 'game_store' AND TABLE_NAME = 'staff'";
$rsCountStaffTableColumns = mysql_query($query_rsCountStaffTableColumns, $GamestorePOS) or die(mysql_error());
$row_rsCountStaffTableColumns = mysql_fetch_assoc($rsCountStaffTableColumns);
$totalRows_rsCountStaffTableColumns = mysql_num_rows($rsCountStaffTableColumns);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsGetColumnsWithoutRolePasswordPrimaryKey = "SHOW columns FROM staff WHERE Field != 'Role' AND Field != 'Password' AND Field != 'StaffID'";
$rsGetColumnsWithoutRolePasswordPrimaryKey = mysql_query($query_rsGetColumnsWithoutRolePasswordPrimaryKey, $GamestorePOS) or die(mysql_error());
$row_rsGetColumnsWithoutRolePasswordPrimaryKey = mysql_fetch_assoc($rsGetColumnsWithoutRolePasswordPrimaryKey);
$totalRows_rsGetColumnsWithoutRolePasswordPrimaryKey = mysql_num_rows($rsGetColumnsWithoutRolePasswordPrimaryKey);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsCountColumnsWithoutRolePasswordPrimaryKey = "SELECT COUNT(COLUMN_NAME) FROM information_schema.columns WHERE table_schema = 'game_store' AND TABLE_NAME = 'staff' AND COLUMN_NAME != 'StaffID' AND COLUMN_NAME != 'Password' AND COLUMN_NAME != 'Role'";
$rsCountColumnsWithoutRolePasswordPrimaryKey = mysql_query($query_rsCountColumnsWithoutRolePasswordPrimaryKey, $GamestorePOS) or die(mysql_error());
$row_rsCountColumnsWithoutRolePasswordPrimaryKey = mysql_fetch_assoc($rsCountColumnsWithoutRolePasswordPrimaryKey);
$totalRows_rsCountColumnsWithoutRolePasswordPrimaryKey = mysql_num_rows($rsCountColumnsWithoutRolePasswordPrimaryKey);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsGetColumnsWithoutPassword = "SHOW columns FROM staff WHERE Field != 'Password'";
$rsGetColumnsWithoutPassword = mysql_query($query_rsGetColumnsWithoutPassword, $GamestorePOS) or die(mysql_error());
$row_rsGetColumnsWithoutPassword = mysql_fetch_assoc($rsGetColumnsWithoutPassword);
$totalRows_rsGetColumnsWithoutPassword = mysql_num_rows($rsGetColumnsWithoutPassword);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsStaffRolesInfo = "SELECT RoleID, Role_Name FROM staff_roles";
$rsStaffRolesInfo = mysql_query($query_rsStaffRolesInfo, $GamestorePOS) or die(mysql_error());
$row_rsStaffRolesInfo = mysql_fetch_assoc($rsStaffRolesInfo);
$totalRows_rsStaffRolesInfo = mysql_num_rows($rsStaffRolesInfo);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>GamestorePOS :: Staff</title>
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery-1.9.1.js"></script>
<script src="../js/jquery-ui.js"></script>
<script>
$(document).ready(function(){
    $("#p4").addClass("selected a");
});
</script>

<script>
function checkRole() {
	var role = $("#roleSelectInfo").val()
	if (role != -1) {
	url = "queryfunctions/roleinfo.php";
		var posting = $.post(url, {role:role});
	
		posting.done(function(data) {
			$("#queryRoleResultSpace").empty()
			$("#queryRoleResultSpace").append(data)
		});
	}
}
</script>

<script>
var whereClause = 0
var andClause = 0

//Works
function addWHEREField() {
	if (whereClause == 0) {
		$("#where_clause").empty();
		$("#where_clause").append('<select name="where_field" id="whereField">' 
			<?php
			do {  
			?> + 
			'<option value="<?php echo $row_rsShowColumns['Field']?>"><?php echo $row_rsShowColumns['Field']?></option>'
			<?php
			} while ($row_rsShowColumns = mysql_fetch_assoc($rsShowColumns));
			$rows = mysql_num_rows($rsShowColumns);
			if($rows > 0) {
			mysql_data_seek($rsShowColumns, 0);
			$row_rsShowColumns = mysql_fetch_assoc($rsShowColumns);
			}
			?> + '</select>' +
			'=' +
			'<input name="where_value" id="whereFieldValue" type="text" size="15" maxlength="100"><br>');
		whereClause = 1;
		$("#addwhereclause").prop("disabled",true);
		$("#removewhereclause").prop("disabled",false);
	}
}

//Works
function removeWHEREField() {
	if (whereClause == 1) {
		$("#where_clause").empty();
		$("#addwhereclause").prop("disabled",false);
		$("#removewhereclause").prop("disabled",true);
		$("#where_clause").append("<p>You don't seem to have a WHERE clause... yet</p>");
		whereClause = 0
	}
}

//Works
function addANDFields() {
	if (andClause < (<?php echo $row_rsCountStaffTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
		if (andClause == 0) {
			$("#and_clauses").empty();
		}
		$("#and_clauses").append('<div id="AND' + andClause + '">' + '<select name="and_field' + andClause + '" id="ANDselect' + andClause + '">'
			<?php
			do {  
			?> + 
			'<option value="<?php echo $row_rsShowColumns['Field']?>"><?php echo $row_rsShowColumns['Field']?></option>'
			<?php
			} while ($row_rsShowColumns = mysql_fetch_assoc($rsShowColumns));
			$rows = mysql_num_rows($rsShowColumns);
			if($rows > 0) {
			mysql_data_seek($rsShowColumns, 0);
			$row_rsShowColumns = mysql_fetch_assoc($rsShowColumns);
			}
			?> + '</select>' +
			'=' +
			'<input name="and_value' + andClause + '" id="ANDinput' + andClause + '" type="text" size="15" maxlength="100"><br></div>');
		andClause++;
		$("#removeandclause").prop("disabled",false);
		if (andClause >= (<?php echo $row_rsCountStaffTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
			$("#addandclause").prop("disabled",true);
		}
	}
}
//Works
function removeANDFields() {
	if (andClause >= 0) {
		andClause = andClause - 1;
		if (andClause < (<?php echo $row_rsCountStaffTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
			$("#addandclause").prop("disabled",false);
		}
		$("#AND" + andClause).remove();
		if (andClause == 0) {
			$("#removeandclause").prop("disabled",true);
			$("#and_clauses").append("<p>You don't seem to have any AND clauses... yet</p>");
		}
	}
}

//Works
function submitSimpleQuery() {
	//Fields Wanted
	fieldsWanted = []
	
	//WHERE Stuff
	whereUsed = 0;
	whereField = "";
	whereFieldValue = "";
	
	//AND Stuff
	andsUsed = 0;
	amountOfAndsUsed = 0;
	andFields = [];
	andFieldValues = [];
	
	//Where Clauses Stuff
	if (whereClause == 1) {
		whereUsed = 1;
		whereField = $("#whereField").val();
		whereFieldValue = $("#whereFieldValue").val();
	}
	
	//Now to get AND stuff
	//And Clauses Stuff
	if (andClause >= 1) {
		andsUsed = 1;
		amountOfAndsUsed = andClause;
		var andClauseStuff = 0;
		while (andClauseStuff < amountOfAndsUsed) {
    		andFields[andClauseStuff] = $("#ANDselect" + andClauseStuff).val();
			andFieldValues[andClauseStuff] = $("#ANDinput" + andClauseStuff).val();
    		andClauseStuff++;
		}
	}
	
	//Now to get FIELD stuff
	//Field stuff
	var positionInArray = 0; //Only inc. if something is added to array, to keep everything in order
	var positionOverall = 0; //Inc. until end to check all possible options that can be added to the array
	do {
		if ($("#fieldWanted" + positionOverall).is(":checked")) {
			fieldsWanted[positionInArray] = $("#fieldWanted" + positionOverall).val()
			positionInArray++;
			positionOverall++;
		}
		else {
			positionOverall++;
		}
	} while (positionOverall <= (<?php echo $row_rsCountStaffTableColumns['COUNT(COLUMN_NAME)']; ?> -1));
	
	//Fix a bug where it sends no data for andFields & andFieldsValues and php says no
	if (andsUsed == 0) {
		andFields[0] = "nullData";
		andFieldValues[0] = "nullData";
	}

	url = "queryfunctions/getstaffsimple.php";
	
	var posting = $.post(url, {fields:fieldsWanted, numberOfFields:positionInArray, whereUsed:whereUsed, whereField:whereField, whereFieldValue:whereFieldValue, andsUsed:andsUsed, amountOfAndsUsed:amountOfAndsUsed, andFields:andFields, andFieldValues:andFieldValues});

	posting.done(function(data) {
		var win = window.open ('about:blank', 'Simple Games Result', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+screen.width+', height='+screen.height+', top=0, left=0');
		
		//var win=window.open('about:blank'); origional
    	with(win.document) {
      		open();
      		write(data);
      		close();
    	}
	});
}
</script>

<script>
var updateFields = 0
var whereUpdateClause = 0
var andUpdateClauses = 0

//Works
function addUpdateFields() {
	if ( updateFields < (<?php echo $totalRows_rsGetColumnsWithoutRolePasswordPrimaryKey; ?> )) {
		if (updateFields == 0) {
			$("#update_clauses").empty();
		}
		$("#update_clauses").append(
			'<div id="UPDATE' + updateFields + '">' + '<select name="update_field' + updateFields + '" id="UPDATEselect' + updateFields + '">'
			<?php
			do {  
			?> + 
			'<option value="<?php echo $row_rsGetColumnsWithoutRolePasswordPrimaryKey['Field']?>"><?php echo $row_rsGetColumnsWithoutRolePasswordPrimaryKey['Field']?></option>'
			<?php
			} while ($row_rsGetColumnsWithoutRolePasswordPrimaryKey = mysql_fetch_assoc($rsGetColumnsWithoutRolePasswordPrimaryKey));
			$rows = mysql_num_rows($rsGetColumnsWithoutRolePasswordPrimaryKey);
			if($rows > 0) {
			mysql_data_seek($rsGetColumnsWithoutRolePasswordPrimaryKey, 0);
			$row_rsGetColumnsWithoutRolePasswordPrimaryKey = mysql_fetch_assoc($rsGetColumnsWithoutRolePasswordPrimaryKey);
			}
			?> + '</select>' +
			'=' +
			'<input name="update_field_value' + updateFields + '" id="UPDATEinput' + updateFields + '" type="text" size="15" maxlength="100"><br></div>'
			);
		updateFields++;
		$("#removeupdateclause").prop("disabled",false);
		if (updateFields >= (<?php echo $row_rsCountColumnsWithoutRolePasswordPrimaryKey['COUNT(COLUMN_NAME)']; ?> -1)) {
			$("#addupdateclause").prop("disabled",true);
		}
	}
}

//Works
function removeUpdateField() {
	if (updateFields >= 0) {
		updateFields = updateFields - 1;
		if (updateFields < (<?php echo $totalRows_rsGetColumnsWithoutRolePasswordPrimaryKey; ?> -1)) {
			$("#addupdateclause").prop("disabled",false);
		}
		$("#UPDATE" + updateFields).remove();
		if (updateFields == 0) {
			$("#removeupdateclause").prop("disabled",true);
			$("#update_clauses").append("<p>You don't seem to have any UPDATE clauses... yet</p>");
		}
	}
}

//Works
function addUpdateWhereField() {
	if (whereUpdateClause == 0) {
		$("#where_update_clause").empty();
		$("#where_update_clause").append(
			'<select name="update_where_field" id="updateWhereField">' 
			<?php
			do {  
			?> + 
			'<option value="<?php echo $row_rsGetColumnsWithoutPassword['Field']?>"><?php echo $row_rsGetColumnsWithoutPassword['Field']?></option>'
			<?php
			} while ($row_rsGetColumnsWithoutPassword = mysql_fetch_assoc($rsGetColumnsWithoutPassword));
			$rows = mysql_num_rows($rsGetColumnsWithoutPassword);
			if($rows > 0) {
			mysql_data_seek($rsGetColumnsWithoutPassword, 0);
			$row_rsGetColumnsWithoutPassword = mysql_fetch_assoc($rsGetColumnsWithoutPassword);
			}
			?> + '</select>' +
			'=' +
			'<input name="update_where_field_value" id="updateWhereFieldValue" type="text" size="15" maxlength="100"><br>'
			);
		whereUpdateClause = 1;
		$("#addupdatewhereclause").prop("disabled",true);
		$("#removeupdatewhereclause").prop("disabled",false);
	}
}

//Works
function removeUpdateWhereField() {
	if (whereUpdateClause == 1) {
		$("#where_update_clause").empty();
		$("#addupdatewhereclause").prop("disabled",false);
		$("#removeupdatewhereclause").prop("disabled",true);
		$("#where_update_clause").append("<p>You don't seem to have a WHERE clause... yet</p>");
		whereUpdateClause = 0
	}
}

//Works
function addUpdateAndFields() {
	if ( andUpdateClauses < (<?php echo $row_rsCountColumnsWithoutRolePasswordPrimaryKey['COUNT(COLUMN_NAME)']; ?> -1)) {
		if (andUpdateClauses == 0) {
			$("#and_update_clauses").empty();
		}
		$("#and_update_clauses").append(
			'<div id="ANDUPDATE' + andUpdateClauses + '">' + '<select name="and_update_field' + andUpdateClauses + '" id="ANDUPDATEselect' + andUpdateClauses + '">'
			<?php
			do {  
			?> + 
			'<option value="<?php echo $row_rsGetColumnsWithoutPassword['Field']?>"><?php echo $row_rsGetColumnsWithoutPassword['Field']?></option>'
			<?php
			} while ($row_rsGetColumnsWithoutPassword = mysql_fetch_assoc($rsGetColumnsWithoutPassword));
			$rows = mysql_num_rows($rsGetColumnsWithoutPassword);
			if($rows > 0) {
			mysql_data_seek($rsGetColumnsWithoutPassword, 0);
			$row_rsGetColumnsWithoutPassword = mysql_fetch_assoc($rsGetColumnsWithoutPassword);
			}
			?> + '</select>' +
			'=' +
			'<input name="and_update_value' + andUpdateClauses + '" id="ANDUPDATEinput' + andUpdateClauses + '" type="text" size="15" maxlength="100"><br></div>'
			);
		andUpdateClauses++;
		$("#removeupdateandclause").prop("disabled",false);
		if (andUpdateClauses >= (<?php echo $row_rsCountColumnsWithoutRolePasswordPrimaryKey['COUNT(COLUMN_NAME)']; ?> -1)) {
			$("#addupdateandclause").prop("disabled",true);
		}
	}
}

//Works
function removeUpdateAndFields() {
	if (andUpdateClauses >= 0) {
		andUpdateClauses = andUpdateClauses - 1;
		if (andUpdateClauses < (<?php echo $row_rsCountColumnsWithoutRolePasswordPrimaryKey['COUNT(COLUMN_NAME)']; ?> -1)) {
			$("#addandclause").prop("disabled",false);
		}
		$("#ANDUPDATE" + andUpdateClauses).remove();
		if (andUpdateClauses == 0) {
			$("#removeupdateandclause").prop("disabled",true);
			$("#and_update_clauses").append("<p>You don't seem to have any AND clauses... yet</p>");
		}
	}
}

//
function submitUpdateQuery() {
	//Where Field Stuff
	var whereUpdateField = ""
	var whereUpdateFieldValue = ""
	
	//Update Fields Stuff
	var updatesUsed = 0
	var updateFieldsWanted = []
	var updateFieldValues = []
	
	//And field stuff
	var andsUpdatesUsed = 0
	var andUpdatesFields = []
	var andUpdatesFieldsValues = []
	
	if (whereUpdateClause == 1) {
		whereUpdateField = $("#updateWhereField").val();
		whereUpdateFieldValue = $("#updateWhereFieldValue").val();
	}
	
	updatesUsed = updateFields
	if (updatesUsed > 0) {
		var tempUpdatesLoop = 0
		do {
			updateFieldsWanted[tempUpdatesLoop] = $("#UPDATEselect"+ tempUpdatesLoop).val();
			updateFieldValues[tempUpdatesLoop] = $("#UPDATEinput"+ tempUpdatesLoop).val();
			tempUpdatesLoop++;
		} while (tempUpdatesLoop < updatesUsed)
	}
	
	andsUpdatesUsed = andUpdateClauses
	if (andsUpdatesUsed > 0) {
		var tempAndsLoop = 0
		do {
			andUpdatesFields[tempAndsLoop] = $("#ANDUPDATEselect"+ tempAndsLoop).val();
			andUpdatesFieldsValues[tempAndsLoop] = $("#ANDUPDATEinput"+ tempAndsLoop).val();
			tempAndsLoop++;
		} while (tempAndsLoop < andsUpdatesUsed)
	}
	
	//Fix a bug where it sends no data for andFields & andFieldsValues and php says no
	if (andsUpdatesUsed == 0) {
		andUpdatesFields[0] = "nullData";
		andUpdatesFieldsValues[0] = "nullData";
	}
	
	//Now that ive got all of my info time to send it off to a php file and get it to return a response (the response being a table or results)
	url = "queryfunctions/updatestaffconfirm.php";
	if ((updatesUsed > 0 ) && (whereUpdateClause == 1)) {
		  var posting = $.post(url, {updatesUsed:updatesUsed, updateFieldsWanted:updateFieldsWanted, updateFieldValues:updateFieldValues, whereUpdateField:whereUpdateField, whereUpdateFieldValue:whereUpdateFieldValue, andsUpdatesUsed:andsUpdatesUsed, andUpdatesFields:andUpdatesFields, andUpdatesFieldsValues:andUpdatesFieldsValues});
		
		posting.done(function(data) {
			var win = window.open ('about:blank', 'Simple Query Result', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+screen.width+', height='+screen.height+', top=0, left=0');
			with(win.document) {
				open();
				write(data);
				close();
			}
		});	
	}
}
</script>

<script>
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

function isDateKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 45 || charCode > 57) || (charCode==46) || (charCode==47))
        return false;
	else
		return true;
}

function isHouseNoKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 45 || charCode > 57) || (charCode==46))
        return false;
	else
		return true;
}
function MM_validateForm() { //v4.0
  if (document.getElementById){
    var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
    for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=document.getElementById(args[i]);
      if (val) { nm=val.name; if ((val=val.value)!="") {
        if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
          if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
        } else if (test!='R') { num = parseFloat(val);
          if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
          if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
            min=test.substring(8,p); max=test.substring(p+1);
            if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
      } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
    } if (errors) alert('The following error(s) occurred:\n'+errors);
    document.MM_returnValue = (errors == '');
} }
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
<div class="containerRight" style="width:150px; float:right">
<div class="textbg" style="width:150px; height:214.281px; position:relative; float:right;">
<h1 style="line-height:21px;">Reset<br>Password</h1>
<form action="queryfunctions/resetpassword.php" method="get" name="resetStaffPassword" onSubmit="MM_validateForm('staffID','','RisNum');return document.MM_returnValue">
Staff ID
<br>
<input name="staffID" type="text" class="inputglow" id="staffID" size="15" maxlength="10">
<br><input name="type" type="hidden" value="staff">
<input name="resetStaffPassword" type="submit" value="Reset">
</form>
</div>
<div class="textbg" style="width:150px; position:relative; float:right;">
<h1 style="line-height:21px;">Change<br>Staff Role</h1>
<form action="" method="get" name="changeStaffRole">
Staff ID
<br>
<input name="staffID" type="text" class="inputglow" id="staffID" size="15" maxlength="10">
<br>
Role
<br>
<select name="role">
                <?php
do {  
?>
                <option value="<?php echo $row_rsGetStaffRoles['RoleID']?>"><?php echo $row_rsGetStaffRoles['Role_Name']?></option>
                <?php
} while ($row_rsGetStaffRoles = mysql_fetch_assoc($rsGetStaffRoles));
  $rows = mysql_num_rows($rsGetStaffRoles);
  if($rows > 0) {
      mysql_data_seek($rsGetStaffRoles, 0);
	  $row_rsGetStaffRoles = mysql_fetch_assoc($rsGetStaffRoles);
  }
?>
              </select>
              <br>
<input name="changeStaffRole" type="submit" value="Change">
</form>
</div>
</div>
<div class="textbg" style="width:690px; position:relative; float:left;">
<h1>Add Staff</h1>
<form action="" method="POST" name="addStaff" onSubmit="MM_validateForm('first_name','','R','last_name','','R','email_address','','RisEmail','dob','','R','h_phone','','RisNum','m_phone','','RisNum','house_no','','R','street_name','','R','suburb','','R','postcode','','RisNum','city','','R','password','','R');return document.MM_returnValue" autocomplete="off">
          <table width="100%">
            <tr>
              <td><label for="first_name">First Name *</label></td>
              <td><label for="last_name">Last Name *</label></td>
              <td><label for="email_address">Email Address *</label></td>
              <td><label for="dateofbirth">Date of Birth *</label></td>
              <td><label for="home_phone">Home Phone *</label></td>
            </tr>
            <tr>
              <td><input name="first_name" type="text" class="inputglow" id="first_name" size="15" maxlength="50" placeholder="James"></td>
              <td><input name="last_name" type="text" class="inputglow" id="last_name" size="15" maxlength="50" placeholder="Matheson"></td>
              <td><input name="email_address" type="text" class="inputglow" id="email_address" onBlur="return validateEmail()" size="15" maxlength="100" placeholder="me@emailprovider.com"></td>
              <td><input name="dateofbirth" type="text" class="inputglow" id="dob" onKeyPress="return isDateKey(event)" size="15" maxlength="10" placeholder="YYYY-MM-DD"></td>
              <td><input name="home_phone" type="text" class="inputglow" id="h_phone" onKeyPress="return isNumberKey(event)" size="15" maxlength="8" placeholder="55123456"></td>
            </tr>
            <tr>
              <td><label for="mobile_phone">Mobile Phone *</label></td>
              <td><label for="house_no">House No. *</label></td>
              <td><label for="street_name">Street Name *</label></td>
              <td><label for="suburb">Suburb *</label></td>
              <td><label for="postcode">Postcode *</label></td>
            </tr>
            <tr>
              <td><input name="mobile_phone" type="text" class="inputglow" id="m_phone" onKeyPress="return isNumberKey(event)" size="15" maxlength="10" placeholder="0412345678"></td>
              <td><input name="house_no" type="text" class="inputglow" id="house_no" onKeyPress="return isHouseNoKey(event)" size="15" maxlength="5" placeholder="1 OR 1/33"></td>
              <td><input name="street_name" type="text" class="inputglow" id="street_name" size="15" maxlength="50" placeholder="Smith Street"></td>
              <td><input name="suburb" type="text" class="inputglow" id="suburb" size="15" maxlength="50" placeholder="Southport"></td>
              <td><input name="postcode" type="text" class="inputglow" id="postcode" onKeyPress="return isNumberKey(event)" size="15" maxlength="4" placeholder="4215"></td>
            </tr>
            <tr>
              <td><label for="city">City *</label></td>
              <td><label for="state">State *</label></td>
              <td><label for="password">Password *</label></td>
              <td><label for="role">Role *</label></td>
            </tr>
            <tr>
              <td><input name="city" type="text" class="inputglow" id="city" size="15" maxlength="50" placeholder="Gold Coast"></td>
              <td><select name="state">
                  <option value="QLD">Queensland</option>
                  <option value="NSW">New South Wales</option>
                  <option value="VIC">Victoria</option>
                  <option value="ACT">ACT</option>
                  <option value="TAS">Tasmania</option>
                  <option value="NT">Northern Territory</option>
                  <option value="SA">South Australia</option>
                  <option value="WA">Western Australia</option>
                </select></td>
              <td><input name="password" type="password" class="inputglow" id="password" onKeyPress="return isNumberKey(event)" size="15" maxlength="4"></td>
              <td><select name="role">
                <?php
do {  
?>
                <option value="<?php echo $row_rsGetStaffRoles['RoleID']?>"><?php echo $row_rsGetStaffRoles['Role_Name']?></option>
                <?php
} while ($row_rsGetStaffRoles = mysql_fetch_assoc($rsGetStaffRoles));
  $rows = mysql_num_rows($rsGetStaffRoles);
  if($rows > 0) {
      mysql_data_seek($rsGetStaffRoles, 0);
	  $row_rsGetStaffRoles = mysql_fetch_assoc($rsGetStaffRoles);
  }
?>
              </select>
              </td>
              <td align="center"><input type="submit" value="Add User"></td>
            </tr>
          </table>
        </form>
</div>
<div class="textbg" style="width:690px; position:relative; float:left;">
	<h1>Simple Staff Search</h1>
        <table width="100%">
          <tr>
            <td><h3>Fields:</h3></td>
            <td><h3>WHERE & AND Clauses</h3></td>
          </tr>
          <tr>
            <td><?php
				$fieldCount = 0;
				do {  
				?>
              	<input type="checkbox" id="fieldWanted<?php echo $fieldCount; $fieldCount++; ?>" value="<?php echo $row_rsShowColumns['Field']?>" checked>
              	<?php echo $row_rsShowColumns['Field']?>
              	</option>
              	<br>
              	<?php
				} while ($row_rsShowColumns = mysql_fetch_assoc($rsShowColumns));
				$rows = mysql_num_rows($rsShowColumns);
				if($rows > 0) {
				mysql_data_seek($rsShowColumns, 0);
				$row_rsShowColumns = mysql_fetch_assoc($rsShowColumns);
				}
				?>
            </td>
            <td><h3>Where Clause</h3>
              <div id="where_clause">
                <p>You don't seem to have a WHERE clause... yet</p>
              </div>
              <h3>And Clauses</h3>
              <div id="and_clauses">
                <p>You don't seem to have any AND clauses... yet</p>
              </div>
              <input id="addwhereclause" type="button" value="Add WHERE Clause" onClick="addWHEREField()">
              <br>
              <input id="removewhereclause" type="button" value="Remove WHERE Clause" onClick="removeWHEREField()" disabled>
              <br>
              <input id="addandclause" type="button" value="Add AND Clause" onClick="addANDFields()">
              <br>
              <input id="removeandclause" type="button" value="Remove last AND Clause" onClick="removeANDFields()" disabled></td>
            <td width="10%"><input id="submitQuery" type="button" value="Submit" onClick="submitSimpleQuery()"></td>
              </td>
        </table>
</div>
<div class="textbg" style="width:870px; position:relative; float:left;">
<h1 style="line-height:21px;">Role<br>Details</h1>
<table>
    <tr>
        <td>Role</td>
        <td rowspan="3"><div id="queryRoleResultSpace" style="text-align:left;">&nbsp;</div></td>
    </tr>
    <tr>
        <td><select name="roleInfo" id="roleSelectInfo">
          <option value="-1"></option>
          <?php
do {  
?>
          <option value="<?php echo $row_rsStaffRolesInfo['RoleID']?>"><?php echo $row_rsStaffRolesInfo['Role_Name']?></option>
          <?php
} while ($row_rsStaffRolesInfo = mysql_fetch_assoc($rsStaffRolesInfo));
  $rows = mysql_num_rows($rsStaffRolesInfo);
  if($rows > 0) {
      mysql_data_seek($rsStaffRolesInfo, 0);
	  $row_rsStaffRolesInfo = mysql_fetch_assoc($rsStaffRolesInfo);
  }
?>
        </select></td>
    </tr>
    <tr>
        <td><input type="button" id="submitRoleQuery" onClick="checkRole()" value="Check"></td>
    </tr>
</table>
</div>
<div class="textbg" style="width:870px; position:relative; float:left;">
    <h1>Edit Staff Details</h1>
    <table width="100%">
      <tr>
        <td><h3>UPDATE</h3></td>
        <td><h3>WHERE</h3></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><h3>Update Clauses</h3>
          <div id="update_clauses">
            <p>You don't seem to have any UPDATE clauses... yet</p>
          </div>
          <input id="addupdateclause" type="button" value="Add UPDATE Clause" onClick="addUpdateFields()">
          <br>
          <input id="removeupdateclause" type="button" value="Remove UPDATE Clause" onClick="removeUpdateField()" disabled></td>
        <td><div id="where_update_clause">
            <p>You don't seem to have a WHERE clause... yet</p>
          </div>
          <h3>And Clauses</h3>
          <div id="and_update_clauses">
            <p>You don't seem to have any AND clauses... yet</p>
          </div>
          <input id="addupdatewhereclause" type="button" value="Add WHERE Clause" onClick="addUpdateWhereField()">
          <br>
          <input id="removeupdatewhereclause" type="button" value="Remove WHERE Clause" onClick="removeUpdateWhereField()" disabled>
          <br>
          <input id="addupdateandclause" type="button" value="Add AND Clause" onClick="addUpdateAndFields()">
          <br>
          <input id="removeupdateandclause" type="button" value="Remove last AND Clause" onClick="removeUpdateAndFields()" disabled></td>
        <td width="10%"><input type="button" value="Update" onClick="submitUpdateQuery()"></td>
      </tr>
    </table>
</div>
<div class="textbg" style="width:870px; position:relative; float:right;">
Adding roles should ONLY be done by the manager (Role 1)<br>
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
mysql_free_result($rsGetStaffRoles);

mysql_free_result($rsStaffRolesInfo);
?>
