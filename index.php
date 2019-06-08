
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GamestorePOS :: Login</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/banner.css" rel="stylesheet" type="text/css" />
<link href="css/footer.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#7d7d7d; margin:0; padding:0;">
<div id="wrap">
<div id="main">

<!--Makes Blank Banner-->
<div class="bannerbg"></div>
<div class="bannerwrapper">
<div class="bannercontainer">
</div>
</div>
<!--Finish Making Blank Banner-->

<div class="allstuff" align="center">
<div class="textbg" style="width:450px; text-align:center;">
<h1>Login</h1>
<p>
<form name="loginform" method="POST" action="<?php echo $loginFormAction; ?>" autocomplete="off">
<table width="450px">
	<tr >
		<td valign="top">
			<label for="u_name">Username *</label>
		</td>
		<td valign="top">
			<input type="text" name="u_name" maxlength="50" size="31" class="inputglow">
		</td>
	</tr>
	<tr>
		<td valign="top">
			<label for="p_word">Password *</label>
		</td>
		<td valign="top">
			<input  type="password" name="p_word" maxlength="50" size="31" class="inputglow">
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="Submit">
		</td>
	</tr>
</table>
</form>
</p>
</div>
</div>
</div>
</div>
<?php
	include 'html/footer.html';
?>
</body>
</html>