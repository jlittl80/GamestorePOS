<?php require_once('../Connections/GamestorePOS.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addmember")) {
  $insertSQL = sprintf("INSERT INTO members (First_Name, Last_Name, Email_Address, Date_Of_Birth, Home_Phone, Mobile_Phone, House_No, Street_Name, Suburb, Postcode, City, `State`, PIN) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['first_name'], "text"),
                       GetSQLValueString($_POST['last_name'], "text"),
                       GetSQLValueString($_POST['email_address'], "text"),
                       GetSQLValueString($_POST['dateofbirth'], "date"),
                       GetSQLValueString($_POST['home_phone'], "int"),
                       GetSQLValueString($_POST['mobile_phone'], "int"),
                       GetSQLValueString($_POST['house_no'], "int"),
                       GetSQLValueString($_POST['street_name'], "text"),
                       GetSQLValueString($_POST['suburb'], "text"),
                       GetSQLValueString($_POST['postcode'], "int"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['pin'], "int"));

  mysql_select_db($database_GamestorePOS, $GamestorePOS);
  $Result1 = mysql_query($insertSQL, $GamestorePOS) or die(mysql_error());

  $insertGoTo = "members.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsTotalMembers = "SELECT IF(COUNT(MemberID)>0, COUNT(MemberID), 0) AS 'TotalMembers' FROM members";
$rsTotalMembers = mysql_query($query_rsTotalMembers, $GamestorePOS) or die(mysql_error());
$row_rsTotalMembers = mysql_fetch_assoc($rsTotalMembers);
$totalRows_rsTotalMembers = mysql_num_rows($rsTotalMembers);mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsTotalMembers = "SELECT IF(COUNT(MemberID)>0, COUNT(MemberID), 0) AS 'TotalMembers' FROM members";
$rsTotalMembers = mysql_query($query_rsTotalMembers, $GamestorePOS) or die(mysql_error());
$row_rsTotalMembers = mysql_fetch_assoc($rsTotalMembers);
$totalRows_rsTotalMembers = mysql_num_rows($rsTotalMembers);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsTodayMembers = "SELECT IF(COUNT(MemberID)>0, COUNT(MemberID), 0) AS 'TodayMembers' FROM members WHERE Member_Since = CURDATE()";
$rsTodayMembers = mysql_query($query_rsTodayMembers, $GamestorePOS) or die(mysql_error());
$row_rsTodayMembers = mysql_fetch_assoc($rsTodayMembers);
$totalRows_rsTodayMembers = mysql_num_rows($rsTodayMembers);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsThisWeekMembers = "SELECT IF(COUNT(MemberID)>0, COUNT(MemberID), 0) AS 'ThisWeekMembers' FROM members WHERE Member_Since >= ( SELECT DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) )";
$rsThisWeekMembers = mysql_query($query_rsThisWeekMembers, $GamestorePOS) or die(mysql_error());
$row_rsThisWeekMembers = mysql_fetch_assoc($rsThisWeekMembers);
$totalRows_rsThisWeekMembers = mysql_num_rows($rsThisWeekMembers);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsThisMonthMembers = "SELECT IF(COUNT(MemberID)>0, COUNT(MemberID), 0) AS 'ThisMonthMembers' FROM members WHERE Member_Since >= ( SELECT CONCAT(YEAR(CURDATE()), '-01-01') )";
$rsThisMonthMembers = mysql_query($query_rsThisMonthMembers, $GamestorePOS) or die(mysql_error());
$row_rsThisMonthMembers = mysql_fetch_assoc($rsThisMonthMembers);
$totalRows_rsThisMonthMembers = mysql_num_rows($rsThisMonthMembers);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsThisYearMembers = "SELECT IF(COUNT(MemberID)>0, COUNT(MemberID), 0) AS 'ThisYearMembers' FROM members WHERE Member_Since >= ( SELECT CONCAT(YEAR(CURDATE()), '-01-01') )";
$rsThisYearMembers = mysql_query($query_rsThisYearMembers, $GamestorePOS) or die(mysql_error());
$row_rsThisYearMembers = mysql_fetch_assoc($rsThisYearMembers);
$totalRows_rsThisYearMembers = mysql_num_rows($rsThisYearMembers);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsShowColumns = "SHOW columns FROM members WHERE Field != 'PIN'";
$rsShowColumns = mysql_query($query_rsShowColumns, $GamestorePOS) or die(mysql_error());
$row_rsShowColumns = mysql_fetch_assoc($rsShowColumns);
$totalRows_rsShowColumns = mysql_num_rows($rsShowColumns);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsCountMemberTableColumns = "SELECT COUNT(COLUMN_NAME) FROM information_schema.columns WHERE table_schema = 'game_store' AND TABLE_NAME = 'members'";
$rsCountMemberTableColumns = mysql_query($query_rsCountMemberTableColumns, $GamestorePOS) or die(mysql_error());
$row_rsCountMemberTableColumns = mysql_fetch_assoc($rsCountMemberTableColumns);
$totalRows_rsCountMemberTableColumns = mysql_num_rows($rsCountMemberTableColumns);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsShowColumnsWithoutPQ = "SHOW columns FROM members WHERE Extra != 'auto_increment' AND Field != 'PIN'";
$rsShowColumnsWithoutPQ = mysql_query($query_rsShowColumnsWithoutPQ, $GamestorePOS) or die(mysql_error());
$row_rsShowColumnsWithoutPQ = mysql_fetch_assoc($rsShowColumnsWithoutPQ);
$totalRows_rsShowColumnsWithoutPQ = mysql_num_rows($rsShowColumnsWithoutPQ);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>GamestorePOS :: Members</title>

<!--Highlights the tab that is open in the nav bar-->
<script src="../js/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#p2").addClass("selected a");
});
</script>

<!--All Controls For Simple Query-->
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
	if ( andClause < (<?php echo $row_rsCountMemberTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
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
		if (andClause >= (<?php echo $row_rsCountMemberTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
			$("#addandclause").prop("disabled",true);
		}
	}
}

//Works
function removeANDFields() {
	if (andClause >= 0) {
		andClause = andClause - 1;
		if (andClause < (<?php echo $row_rsCountMemberTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
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
	} while (positionOverall <= (<?php echo $row_rsCountMemberTableColumns['COUNT(COLUMN_NAME)']; ?> -1));
	
	//Fix a bug where it sends no data for andFields & andFieldsValues and php says no
	if (andsUsed == 0) {
		andFields[0] = "nullData";
		andFieldValues[0] = "nullData";
	}

	url = "queryfunctions/getmemberssimple.php";
	
	var posting = $.post(url, {fields:fieldsWanted, numberOfFields:positionInArray, whereUsed:whereUsed, whereField:whereField, whereFieldValue:whereFieldValue, andsUsed:andsUsed, amountOfAndsUsed:amountOfAndsUsed, andFields:andFields, andFieldValues:andFieldValues});

	posting.done(function(data) {
		var win = window.open ('about:blank', 'Simple Query Result', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+screen.width+', height='+screen.height+', top=0, left=0');
		
		//var win=window.open('about:blank'); origional
    	with(win.document) {
      		open();
      		write(data);
      		close();
    	}
	});
}
</script>

<!--All Controls For Update Field Control-->
<script>
var updateFields = 0
var whereUpdateClause = 0
var andUpdateClauses = 0

//Works
function addUpdateFields() {
	if ( updateFields < (<?php echo $totalRows_rsShowColumnsWithoutPQ; ?> )) {
		if (updateFields == 0) {
			$("#update_clauses").empty();
		}
		$("#update_clauses").append(
			'<div id="UPDATE' + updateFields + '">' + '<select name="update_field' + updateFields + '" id="UPDATEselect' + updateFields + '">'
			<?php
			do {  
			?> + 
			'<option value="<?php echo $row_rsShowColumnsWithoutPQ['Field']?>"><?php echo $row_rsShowColumnsWithoutPQ['Field']?></option>'
			<?php
			} while ($row_rsShowColumnsWithoutPQ = mysql_fetch_assoc($rsShowColumnsWithoutPQ));
			$rows = mysql_num_rows($rsShowColumnsWithoutPQ);
			if($rows > 0) {
			mysql_data_seek($rsShowColumnsWithoutPQ, 0);
			$row_rsShowColumnsWithoutPQ = mysql_fetch_assoc($rsShowColumnsWithoutPQ);
			}
			?> + '</select>' +
			'=' +
			'<input name="update_field_value' + updateFields + '" id="UPDATEinput' + updateFields + '" type="text" size="15" maxlength="100"><br></div>'
			);
		updateFields++;
		$("#removeupdateclause").prop("disabled",false);
		if (updateFields >= (<?php echo $row_rsCountMemberTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
			$("#addupdateclause").prop("disabled",true);
		}
	}
}

//Works
function removeUpdateField() {
	if (updateFields >= 0) {
		updateFields = updateFields - 1;
		if (updateFields < (<?php echo $totalRows_rsShowColumnsWithoutPQ; ?> -1)) {
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
	if ( andUpdateClauses < (<?php echo $row_rsCountMemberTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
		if (andUpdateClauses == 0) {
			$("#and_update_clauses").empty();
		}
		$("#and_update_clauses").append(
			'<div id="ANDUPDATE' + andUpdateClauses + '">' + '<select name="and_update_field' + andUpdateClauses + '" id="ANDUPDATEselect' + andUpdateClauses + '">'
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
			'<input name="and_update_value' + andUpdateClauses + '" id="ANDUPDATEinput' + andUpdateClauses + '" type="text" size="15" maxlength="100"><br></div>'
			);
		andUpdateClauses++;
		$("#removeupdateandclause").prop("disabled",false);
		if (andUpdateClauses >= (<?php echo $row_rsCountMemberTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
			$("#addupdateandclause").prop("disabled",true);
		}
	}
}

//Works
function removeUpdateAndFields() {
	if (andUpdateClauses >= 0) {
		andUpdateClauses = andUpdateClauses - 1;
		if (andUpdateClauses < (<?php echo $row_rsCountMemberTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
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
	url = "queryfunctions/updatemembersconfirm.php";
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

<!--Controls input for PIN and PHONE number input feilds-->
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

<!--Makes stuff look pretty-->
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
      <div class="textbg" style="width:150px; position:relative; float:right; height:214px;">
        <table width="150">
          <tr>
            <td colspan="2" style="text-align:center"><h1 style="line-height:23px">Members Joined</h1></td>
          </tr>
          <tr>
            <td style="text-align:right" width="70%">Total:</td>
            <td width="30%"><?php echo $row_rsTotalMembers['TotalMembers']; ?></td>
          </tr>
          <tr>
            <td style="text-align:right">Today:</td>
            <td><?php echo $row_rsTodayMembers['TodayMembers']; ?></td>
          </tr>
          <tr>
            <td style="text-align:right">This Week:</td>
            <td><?php echo $row_rsThisWeekMembers['ThisWeekMembers']; ?></td>
          </tr>
          <tr>
            <td style="text-align:right">This Month:</td>
            <td><?php echo $row_rsThisMonthMembers['ThisMonthMembers']; ?></td>
          </tr>
          <tr>
            <td style="text-align:right">This Year:</td>
            <td><?php echo $row_rsThisYearMembers['ThisYearMembers']; ?></td>
          </tr>
        </table>
      </div>
      <div class="textbg" style="width:690px; position:relative; float:left;">
      <h1>Add User</h1>
        <form action="<?php echo $editFormAction; ?>" method="POST" name="addmember" onSubmit="MM_validateForm('first_name','','R','last_name','','R','email_address','','RisEmail','dob','','R','h_phone','','RisNum','m_phone','','RisNum','house_no','','R','street_name','','R','suburb','','R','postcode','','RisNum','city','','R','pin','','RisNum');return document.MM_returnValue" autocomplete="off">
          <table width="690px">
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
              <td><label for="pin">PIN *</label></td>
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
              <td><input name="pin" type="password" class="inputglow" id="pin" onKeyPress="return isNumberKey(event)" size="15" maxlength="4"></td>
              <td colspan="2" align="center"><input type="submit" value="Add User"></td>
            </tr>
          </table>
          <input type="hidden" name="MM_insert" value="addmember">
        </form>
      </div>
      <div class="textbg" style="width:870px; position:relative; float:left;">
        <h1>Simple User Search</h1>
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
		?></td>
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
        <h1>Edit User Details</h1>
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
mysql_free_result($rsTotalMembers);

mysql_free_result($rsTodayMembers);

mysql_free_result($rsThisWeekMembers);

mysql_free_result($rsThisMonthMembers);

mysql_free_result($rsThisYearMembers);

mysql_free_result($rsShowColumns);

mysql_free_result($rsCountMemberTableColumns);

mysql_free_result($rsShowColumnsWithoutPQ);
?>
