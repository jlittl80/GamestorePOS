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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "insertGame")) {
  $insertSQL = sprintf("INSERT INTO game (GameID, Title_Name, Release_Date, Rating, `Description`, Genre, Platform, Default_Price) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['Barcode'], "text"),
                       GetSQLValueString($_POST['Title_Name'], "text"),
                       GetSQLValueString($_POST['Release_Date'], "date"),
                       GetSQLValueString($_POST['Game_Rating'], "int"),
                       GetSQLValueString($_POST['Description'], "text"),
                       GetSQLValueString($_POST['Genre'], "text"),
                       GetSQLValueString($_POST['Platform'], "text"),
                       GetSQLValueString($_POST['Default_Price'], "double"));
  mysql_select_db($database_GamestorePOS, $GamestorePOS);
  $Result1 = mysql_query($insertSQL, $GamestorePOS) or die(mysql_error());

  $insertGoTo = "success.php?redir=games.php&action=insertUser";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "UpdateAddStock")) {
  $updateSQL = sprintf("UPDATE games SET Stock=Stock + %s WHERE GameID=%s",
                       GetSQLValueString($_POST['Stock_Number'], "int"),
                       GetSQLValueString($_POST['Barcode'], "int"));

  mysql_select_db($database_GamestorePOS, $GamestorePOS);
  $Result1 = mysql_query($updateSQL, $GamestorePOS) or die(mysql_error());

  $updateGoTo = "success.php?redir=games.php&action=updateStockAdd";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "AddGenre")) {
  $insertSQL = sprintf("INSERT INTO game_genres (Genre_Name, `Genre _Value`) VALUES (%s, %s)",
                       GetSQLValueString($_POST['GenreName'], "text"),
                       GetSQLValueString($_POST['GenreName'], "text"));

  mysql_select_db($database_GamestorePOS, $GamestorePOS);
  $Result1 = mysql_query($insertSQL, $GamestorePOS) or die(mysql_error());

  $insertGoTo = "success.php?redir=games.php&action=addGenre";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsSelectGenres = "SELECT * FROM game_genres ORDER BY `Genre _Value` ASC";
$rsSelectGenres = mysql_query($query_rsSelectGenres, $GamestorePOS) or die(mysql_error());
$row_rsSelectGenres = mysql_fetch_assoc($rsSelectGenres);
$totalRows_rsSelectGenres = mysql_num_rows($rsSelectGenres);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsShowColumns = "SHOW columns FROM games";
$rsShowColumns = mysql_query($query_rsShowColumns, $GamestorePOS) or die(mysql_error());
$row_rsShowColumns = mysql_fetch_assoc($rsShowColumns);
$totalRows_rsShowColumns = mysql_num_rows($rsShowColumns);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsCountGameTableColumns = "SELECT COUNT(COLUMN_NAME) FROM information_schema.columns WHERE table_schema = 'game_store' AND TABLE_NAME = 'games'";
$rsCountGameTableColumns = mysql_query($query_rsCountGameTableColumns, $GamestorePOS) or die(mysql_error());
$row_rsCountGameTableColumns = mysql_fetch_assoc($rsCountGameTableColumns);
$totalRows_rsCountGameTableColumns = mysql_num_rows($rsCountGameTableColumns);

mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsShowColumnsWithoutPQ = "SHOW columns FROM games WHERE Field != 'GameID'";
$rsShowColumnsWithoutPQ = mysql_query($query_rsShowColumnsWithoutPQ, $GamestorePOS) or die(mysql_error());
$row_rsShowColumnsWithoutPQ = mysql_fetch_assoc($rsShowColumnsWithoutPQ);
$totalRows_rsShowColumnsWithoutPQ = mysql_num_rows($rsShowColumnsWithoutPQ);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>GamestorePOS :: Games</title>
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery-1.9.1.js"></script>
<script src="../js/jquery-ui.js"></script>
<script>
$(document).ready(function(){
    $("#p3").addClass("selected a");
});
</script>

<script>
function checkStock() {
	var barcode = $("#barcodeAmountInStock").val()
	
	url = "queryfunctions/stocklevels.php";
	var posting = $.post(url, {barcode:barcode});
	
	posting.done(function(data) {
		$("#queryStockResultSpace").empty()
		$("#queryStockResultSpace").append(data)
	});	
}

function deleteGenre() {
	url = "queryfunctions/deletegenre.php";
	
	var posting = $.post(url, {genre:$("#removeGenreSelect").val()});
	posting.done(function(data) {
		var win = window.open ('about:blank', 'Delete Genre', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+screen.width+', height='+screen.height+', top=0, left=0');
    	with(win.document)
    		{
      			open();
      			write(data);
      			close();
    		}
		});	
}

function removeStock() {
	if($("#updateRemoveStockBarcode").val()) {
		if($("#updateRemoveStockAmount").val()) {
			url = "queryfunctions/removestock.php";
	
			var posting = $.post(url, {barcode:$("#updateRemoveStockBarcode").val(), amount:$("#updateRemoveStockAmount").val()});
			posting.done(function(data) {
				var win = window.open ('about:blank', 'Remove Stock', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+screen.width+', height='+screen.height+', top=0, left=0');
    			with(win.document) {
      				open();
      				write(data);
      				close();
				}
			});	
		}
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
	if ( andClause < (<?php echo $row_rsCountGameTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
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
		if (andClause >= (<?php echo $row_rsCountGameTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
			$("#addandclause").prop("disabled",true);
		}
	}
}

//Works
function removeANDFields() {
	if (andClause >= 0) {
		andClause = andClause - 1;
		if (andClause < (<?php echo $row_rsCountGameTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
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
	} while (positionOverall <= (<?php echo $row_rsCountGameTableColumns['COUNT(COLUMN_NAME)']; ?> -1));
	
	//Fix a bug where it sends no data for andFields & andFieldsValues and php says no
	if (andsUsed == 0) {
		andFields[0] = "nullData";
		andFieldValues[0] = "nullData";
	}

	url = "queryfunctions/getgamessimple.php";
	
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
		if (updateFields >= (<?php echo $row_rsCountGameTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
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
	if ( andUpdateClauses < (<?php echo $row_rsCountGameTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
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
		if (andUpdateClauses >= (<?php echo $row_rsCountGameTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
			$("#addupdateandclause").prop("disabled",true);
		}
	}
}

//Works
function removeUpdateAndFields() {
	if (andUpdateClauses >= 0) {
		andUpdateClauses = andUpdateClauses - 1;
		if (andUpdateClauses < (<?php echo $row_rsCountGameTableColumns['COUNT(COLUMN_NAME)']; ?> -1)) {
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
	url = "queryfunctions/updategameconfirm.php";
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
$(function() {
    $("#datepicker").datepicker({ 
	dateFormat: "yy-mm-dd" ,
	changeMonth: true,
    changeYear: true
	});
});

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

function isPriceKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 46 || charCode > 57) || (charCode==45) || (charCode==47))
        return false;
	else
		return true;
}

$(function() {
	$('#genreSelect').change(function() {
		var ii = $("#genreInput").val().length;
		if (ii < 1) {
			$('#genreInput').val($(this).val());
		}
		else {
			$('#genreInput').val($('#genreInput').val() + ", " + $(this).val());
		}
	});
});
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
<link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
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
<div class="textbg" style="width:870px; position:relative; float:left;">
<h1>Add Game</h1>
<form action="<?php echo $editFormAction; ?>" method="POST" name="insertGame" onSubmit="MM_validateForm('barcode','','RisNum','title_name','','R','datepicker','','R','default_price','','RisNum','description','','R','genreInput','','R');return document.MM_returnValue" autocomplete="off">
<table border="0" width="100%" align="left">
	<tr>
    	<td>Barcode</td>
    	<td>Platfom</td>
        <td>Description</td>
        <td>Genre</td>
    </tr>
    <tr>
    	<td><input name="Barcode" type="text" class="inputglow" id="barcode" placeholder="978213131121" size="15" maxlength="12" onKeyPress="return isNumberKey(event)"></td>
    	<td><select id="insertGamePlatform" name="Platform">
        <option value="PC">PC</option>
        <option value="Xbox 360">Xbox 360</option>
        <option value="Xbox 1">Xbox 1</option>
        <option value="PS3">PS3</option>
        <option value="PS4">PS4</option>
        <option value="Wii">Wii</option>
        </select></td>
        <td rowspan="5"><textarea name="Description" id="description" maxlength="10000" class="inputglow" rows="8" cols="32"></textarea></td>
        <td rowspan="4"><textarea name="Genre" id="genreInput" maxlength="100" class="inputglow" rows="4" cols="32"></textarea>
        <br>
        Select: 
        <select id="genreSelect">
        	<?php
				do {  
			?>
        		<option value="<?php echo $row_rsSelectGenres['Genre_Name']?>"><?php echo $row_rsSelectGenres['Genre _Value']?></option>
          	<?php
			} while ($row_rsSelectGenres = mysql_fetch_assoc($rsSelectGenres));
  			$rows = mysql_num_rows($rsSelectGenres);
  			if($rows > 0) {
     			mysql_data_seek($rsSelectGenres, 0);
	  			$row_rsSelectGenres = mysql_fetch_assoc($rsSelectGenres);
  			}
			?>
        </select>
        </td>
    </tr>
    <tr>
    	<td>Title Name</td>
        <td>Rating</td>
    </tr>
    <tr>
    	<td><input name="Title_Name" type="text" class="inputglow" id="title_name" placeholder="Grand Theft Auto V" size="15" maxlength="50"></td>
        <td><select id="insertGameRating" name="Game_Rating">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        </select></td>
    </tr>
    <tr>
        <td>Release Date</td>
        <td>Default Price</td>
    </tr>
    <tr>
        <td><input name="Release_Date"type="text" id="datepicker" size="15" class="inputglow" placeholder="1996-07-20" onKeyPress="return isDateKey(event)"></td>
        <td>$<input name="Default_Price" type="text" class="inputglow" id="default_price" value="100" size="15" maxlength="12" onKeyPress="return isPriceKey(event)"></td>
        <td><input name="submitInsert" type="submit" value="Submit" size="15"></td>
    </tr>
</table>
<input type="hidden" name="MM_insert" value="insertGame">
</form>
</div>
<div class="textbg" style="width:150px; position:relative; float:left;">
<h1 style="line-height:21px;">Add<br>Stock</h1>
<form action="<?php echo $editFormAction; ?>" name="UpdateAddStock" id="UpdateAddStock" method="POST">
<table>
    <tr>
        <td>Barcode</td>
    </tr>
    <tr> 
        <td><input type="text" name="Barcode" class="inputglow" size="17" onKeyPress="return isNumberKey(event)" autocomplete="off"></td>
    </tr>
    <tr>
        <td>Amount</td>
    </tr>
    <tr>
        <td><input type="text" name="Stock_Number" class="inputglow" size="17" onKeyPress="return isNumberKey(event)" autocomplete="off"></td>
    </tr>
    <tr>
        <td><input type="submit" id="submitAddStock" value="Add"></td>
    </tr>
</table>
<input type="hidden" name="MM_update" value="UpdateAddStock">
</form>
</div>
<div class="textbg" style="width:150px; position:relative; float:left;">
<h1 style="line-height:21px;">Remove Stock</h1>
<table>
    <tr>
        <td>Barcode</td>
    </tr>
    <tr>
        <td><input type="text" name="Barcode" class="inputglow" size="17" onKeyPress="return isNumberKey(event)" autocomplete="off" id="updateRemoveStockBarcode"></td>
    </tr>
    <tr>
        <td>Amount</td>
    </tr>
    <tr>
        <td><input type="text" name="Stock_Number" class="inputglow" size="17" onKeyPress="return isNumberKey(event)" autocomplete="off" id="updateRemoveStockAmount"></td>
    </tr>
    <tr>
        <td><input type="button" id="submitRemoveStock" value="Remove" onClick="removeStock()"></td>
    </tr>
</table>
</div>
<div class="textbg" style="width:150px; height:214px; position:relative; float:left;">
<h1 style="line-height:21px;">Check<br>Stock</h1>
<table>
    <tr>
        <td>Barcode</td>
    </tr>
    <tr>
        <td><input type="text" id="barcodeAmountInStock" name="Barcode" class="inputglow" size="17" onKeyPress="return isNumberKey(event)" autocomplete="off"></td>
    </tr>
    <tr>
        <td><input type="button" id="submitStockQuery" onClick="checkStock()" value="Check"></td>
    </tr>
    <tr>
    	<td><div id="queryStockResultSpace"></div></td>
   </tr>
</table>
</div>
<div class="textbg" style="width:150px; height:214px; position:relative; float:left;">
<h1 style="line-height:21px;">Add<br>Genre</h1>
<form action="<?php echo $editFormAction; ?>" name="AddGenre" id="AddGenre" method="POST">

<table>
    <tr>
        <td>Name</td>
    </tr>
    <tr>
        <td><input type="text" id="genreName" name="GenreName" class="inputglow" size="17" autocomplete="off"></td>
    </tr>
    <tr>
        <td><input type="submit" id="submitAddGenre" value="Add"></td>
    </tr>
</table>
<input type="hidden" name="MM_insert" value="AddGenre">
</form>
</div>
<?php
	mysql_data_seek($rsSelectGenres, 0);
	$row_rsSelectGenres = mysql_fetch_assoc($rsSelectGenres);
?>
<div class="textbg" style="width:150px; height:214px; position:relative; float:left;">
<h1 style="line-height:21px;">Remove<br>Genre</h1>
<table>
    <tr>
        <td>Name</td>
    </tr>
    <tr>
        <td>
        <select id="removeGenreSelect">
          	<?php
			do {  
			?>
          		<option value="<?php echo $row_rsSelectGenres['Genre_Name']?>"><?php echo $row_rsSelectGenres['Genre _Value']?></option>
          	<?php
			} while ($row_rsSelectGenres = mysql_fetch_assoc($rsSelectGenres));
  			$rows = mysql_num_rows($rsSelectGenres);
  			if($rows > 0) {
      			mysql_data_seek($rsSelectGenres, 0);
	  			$row_rsSelectGenres = mysql_fetch_assoc($rsSelectGenres);
  			}
			?>
        </select>
        </td>
    </tr>
    <tr>
        <td><input type="button" id="submitRemoveGenre" value="Delete" onClick="deleteGenre()"></td>
    </tr>
</table>
</div>
<div class="textbg" style="width:870px; position:relative; float:left;">
	<h1>Simple Game Search</h1>
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
<h1>Edit Game Details</h1>
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
mysql_free_result($rsSelectGenres);

mysql_free_result($rsShowColumns);
?>
