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
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>GamestorePOS :: Transactions</title>
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery-1.9.1.js"></script>
<script src="../js/jquery-ui.js"></script>
<script>
$(document).ready(function(){
    $("#p5").addClass("selected a");
});
</script>

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

function editPrice() {
	$("#newTransactionPriceInput").prop("readonly", false);
}
function transactionProductDetails() {
	var barcode = $("#newTransactionBarcodeInput").val();
	url = "queryfunctions/productdetails.php";
		var posting = $.post(url, {barcode:barcode});
		posting.done(function(data) {
		$("#productDetailsSpace").empty()
		$("#productDetailsSpace").append(data)
		});	
}
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
<h1>New Transaction</h1>
<table border="0">
	<tr>
		<td width="75">Barcode</td>
		<td width="100"><input type="text" name="barcode" id="newTransactionBarcodeInput" class="inputglow" size="17"></td>
        <td><input type="button" name="getProductDetails" id="newTransactionProductDetailsSearch" value="Get Details" onClick="transactionProductDetails()"></td>
	</tr>
</table>
<div id="productDetailsSpace"></div>
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