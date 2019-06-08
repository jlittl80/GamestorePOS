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

$colname_rsProductDetails = "-1";
if (isset($_POST['barcode'])) {
  $colname_rsProductDetails = $_POST['barcode'];
}
mysql_select_db($database_GamestorePOS, $GamestorePOS);
$query_rsProductDetails = sprintf("SELECT * FROM games WHERE GameID = ".$colname_rsProductDetails);
$rsProductDetails = mysql_query($query_rsProductDetails, $GamestorePOS) or die(mysql_error());
$row_rsProductDetails = mysql_fetch_assoc($rsProductDetails);
$totalRows_rsProductDetails = mysql_num_rows($rsProductDetails);
?>
<?php if ($totalRows_rsProductDetails > 0) { // Show if recordset not empty ?>
  <table>
    <tr>
      <td width="75">Title Name</td>
      <td width="100"><?php echo $row_rsProductDetails['Title_Name']; ?></td>
      </tr>
    <tr>
      <td>Platform</td>
      <td><?php echo $row_rsProductDetails['Platform']; ?></td>
      </tr>
    <tr>
      <td>Stock</td>
      <td><?php echo $row_rsProductDetails['Stock']; ?></td>
      </tr>
    <tr>
      <td>Price</td>
      <td><input type="text" name="price" id="newTransactionPriceInput" class="inputglow" size="17" value="<?php echo $row_rsProductDetails['Default_Price']; ?>" readonly></td>
      <td><input type="button" name="editPrice" id="newTransactionPriceInputUnlock" size="17" value="Edit Price" onClick="editPrice()"></td>
      </tr>
    <tr>
      <td><input type="hidden" id="gameID" value="<?php echo $_POST['barcode']; ?>" /></td>
      <td><input type="button"</td>
      </tr>
  </table>
  <?php } // Show if recordset not empty ?>
<?php
mysql_free_result($rsProductDetails);

mysql_free_result($Recordset1);
?>
