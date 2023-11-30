<?php
ini_set("display_errors", "On");
error_reporting(1); //STRICT DEVELOPMENT
session_start();
if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "reset") {
unset($_SESSION["hostname"]);
unset($_SESSION["database"]);
unset($_SESSION["username"]);
unset($_SESSION["password"]);
$_SESSION["logout"] = true;
header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
exit();
} elseif (!$_SESSION["logout"]) {
if (file_exists("config.inc.php")) {
require_once "config.inc.php";
if (isset($dbconfig) && !empty($dbconfig)) {
$_SESSION["hostname"] = $dbconfig["db_server"];
$_SESSION["database"] = $dbconfig["db_name"];
$_SESSION["username"] = $dbconfig["db_username"];
$_SESSION["password"] = $dbconfig["db_password"];
}
}
}
require_once "config.php";
$Libraries = ParsVT_Check_Requirements::getLibrary();
$STABILITYCONF = ParsVT_Check_Requirements::getStabilityConf(false);
$SYSINFO = ParsVT_Check_Requirements::getSystemInfo();
$Security = ParsVT_Check_Requirements::getSecurityConf();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Check system requirements</title>
<link type="text/css" rel="stylesheet" href="style.css" />
<script src="script.js"></script>
</head>
<body>
<h1>Checking Pre-Requisites Before Installation ParsVT CRM</h1>
<div class="tabs">
<input type="radio" id="tab1" name="tab-control" checked>
<input type="radio" id="tab2" name="tab-control">
<input type="radio" id="tab3" name="tab-control">
<input type="radio" id="tab4" name="tab-control">
<input type="radio" id="tab5" name="tab-control">
<ul>
<li title="Database Information">
<label for="tab1" role="button">
<svg viewBox="0 0 448 512">
<use xlink:href="#icon-database"></use>
</svg>
<br><span>Database Info</span>
</label>
</li>
<li title="PHP Extensions">
<label for="tab2" role="button">
<svg viewBox="0 0 640 512">
<use xlink:href="#icon-php"></use>
</svg>
<br><span>PHP Extensions</span>
</label>
</li>
<li title="PHP Configuration">
<label for="tab3" role="button">
<svg viewBox="0 0 640 512">
<use xlink:href="#icon-config"></use>
</svg>
<br><span>PHP Config</span>
</label>
</li>
<li title="Security Configuration">
<label for="tab4" role="button">
<svg viewBox="0 0 512 512">
<use xlink:href="#icon-security"></use>
</svg>
<br><span>Security Config</span>
</label>
</li>
<li title="Other Information">
<label for="tab5" role="button">
<svg viewBox="0 0 512 512">
<use xlink:href="#icon-server"></use>
</svg>
<br><span>Other Info</span>
</label>
</li>
</ul>
<div class="slider">
<div class="indicator"></div>
</div>
<div class="content">
<section>
<?php
$connection = false;
$error = "";
if (isset($_SESSION["hostname"], $_SESSION["database"], $_SESSION["username"], $_SESSION["password"]) || (isset( $_REQUEST["hostname"], $_REQUEST["database"], $_REQUEST["username"], $_REQUEST["password"]) && !empty($_REQUEST["hostname"]) && !empty($_REQUEST["database"]) && !empty($_REQUEST["username"]))) {
if (isset($_REQUEST["hostname"], $_REQUEST["database"], $_REQUEST["username"], $_REQUEST["password"])) {
$_SESSION["hostname"] = $_REQUEST["hostname"];
$_SESSION["database"] = $_REQUEST["database"];
$_SESSION["username"] = $_REQUEST["username"];
$_SESSION["password"] = $_REQUEST["password"];
}
$con = mysqli_connect($_SESSION["hostname"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"]);
if (mysqli_connect_errno()) {
$connection = false;
$error = "<h3 style='color:red;'>Failed to connect to MySQL: " . mysqli_connect_error() . "</h3><br>";
unset($_SESSION["hostname"]);
unset($_SESSION["database"]);
unset($_SESSION["username"]);
unset($_SESSION["password"]);
} else {
$connection = true;
$DbConf = ParsVT_Check_Requirements::getDbConf($con);
echo "<div>";
echo "<div class='align'>";
echo "<h2>Database Information</h2><h3>In database settings there are some recommended settings as well as some settings that we strongly recommend not to use.<br>So, first at all, we DO NOT recommend to have for SQL Mode set STRICT_TRANS_TABLE. We recommend STRICT_TRANS_TABLE turned off.</h3><br>";
echo "<table><thead><tr><th>Parameter</th><th>Current value</th><th>Recommended</th><th>Status</th></tr></thead><tbody>";
foreach ($DbConf as $key => $item) {
echo "<tr " . ($item["status"] ? 'class="blink_me danger" ' . (isset($item["help"]) && $item["status"] ? 'data-toggle="tooltip" title="' . $item["help"] . '" title="' . $item["help"] . '"' : "") : "") . "><td>" . $key . "</td>";
if ($item["recommended"] === false) {
echo '<td colspan="3">' . $item["current"] . "</td>";
} else {
echo "<td>" . $item["current"] . "</td>";
echo "<td>" . $item["recommended"] . "</td>";
echo "<td>" . ($item["status"] ? ParsVT_Check_Requirements::$no : ParsVT_Check_Requirements::$yes) . "</td>";
}
echo "</tr>";
}
echo "<div>";
echo "<div>";
echo "<div>";
echo "</tbody></table>";
echo "<div>";
echo "</div>";
}
}
if (!$connection) {
?>
<div id="loginForm">
<div class="align">
<h2>Database Information</h2>
<h3>To obtain more information on MySQL requirements, please enter MySQL database connection information.</h3>
<br><?php echo $error; ?>
<div class="grid">
<form method="POST" class="form login">
<div class="form-field">
<label for="login-host">
<svg viewBox="0 0 512 512" class="icon-login">
<use xlink:href="#icon-server"></use>
</svg>
</label>
<input autocomplete="username" id="login-host" type="text" name="hostname" class="form-input" placeholder="Host name" value="<?php echo $_REQUEST["hostname"]; ?>" required>
</div>
<div class="form-field">
<label for="login-database">
<svg viewBox="0 0 448 512" class="icon-login">
<use xlink:href="#icon-database"></use>
</svg>
</label>
<input autocomplete="localhost" id="login-database" type="text" name="database" class="form-input" placeholder="Database name" value="<?php echo $_REQUEST["database"]; ?>" required>
</div>
<div class="form-field">
<label for="login-username">
<svg viewBox="0 0 448 512" class="icon-login">
<use xlink:href="#icon-user"></use>
</svg>
</label>
<input autocomplete="username" id="login-username" type="text" name="username" class="form-input" placeholder="Username"  value="<?php echo $_REQUEST["username"]; ?>" required>
</div>
<div class="form-field">
<label for="login-password">
<svg viewBox="0 0 448 512" class="icon-login">
<use xlink:href="#icon-lock"></use>
</svg>
</label>
<input id="login-password" type="password" name="password" class="form-input" placeholder="Password"  value="<?php echo $_REQUEST["hostname"]; ?>">
</div>
<div class="form-field"><input type="submit" value="Check Requirements"></div>
</form>
</div>
</div>
</div>
<?php } ?>
</section>
<section>
<div class="align">
<h2>PHP Extensions</h2>
<h3>Following extensions should be enabled for your PHP setup.</h3>
<table>
<thead>
<tr>
<th>Library</th>
<th>Mandatory</th>
<th>Installed</th>
</tr>
</thead>
<tbody>
<?php
foreach ($Libraries as $key => $item) {
$help = $signature = "";
if ($item["status"] == "No" && $item["mandatory"]) {
$signature = ' class="blink_me danger" ';
} elseif ($item["status"] == "No") {
$signature = ' class="blink_me warning" ';
}
if (isset($item["help"]) && $item["status"]) {
$help = 'data-toggle="tooltip" title="' . $item["help"] . '" title="' . $item["help"] . '"';
}
$icon = strtolower($item["status"]) == "yes" ? ParsVT_Check_Requirements::$yes : ParsVT_Check_Requirements::$no;
echo "<tr  " . $signature . " " . $help . " ><td>" . $key . "</td><td>" . ($item["mandatory"] ? "Mandatory" : "Optional") . "</td><td>" . $icon . "</td></tr>";
}
?>
</tbody>
</table>
</div>
</section>
<section>
<div class="align">
<h2>PHP Configuration</h2>
<h3>It is recommended to have php.ini values set as below. In case php.ini requirements are not met, installation process can be still proceeded. This can be adjusted later.</h3>
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Current value</th>
<th>Recommended</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php
foreach ($STABILITYCONF as $key => $item) {
echo "<tr " . ($item["incorrect"] ? 'class="blink_me danger" ' . (isset($item["help"]) && $item["incorrect"] ? 'data-toggle="tooltip" title="' . $item["help"] . '" title="' . $item["help"] . '"' : "") : "") . '><td>' . $key . "</td>";
if ($item["recommended"] === false) {
echo ' <td colspan="2">' . $item["current"] . "</td>";
} else {
echo "<td>" . $item["current"] . "</td><td>" . $item["recommended"] . "</td>";
}
echo "<td>" . ($item["incorrect"] ? ParsVT_Check_Requirements::$no : ParsVT_Check_Requirements::$yes) . "</td>";
echo "</tr>";
}
?>
</tbody>
</table>
</div>
</section>
<section>
<div class="align">
<h2>Security Configuration</h2>
<h3>Check server security.</h3>
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Current value</th>
<th>Recommended</th>
<th>Status</th>
</tr>
</thead>
<tbody><?php foreach ($Security as $key => $item) {echo '<tr ' . ($item['status'] ? 'class="blink_me danger" ' . ((isset($item['help']) && $item['status']) ? 'data-toggle="tooltip" title="' . $item['help'] . '" title="' . $item['help'] . '"' : '') : '') . '>';echo '<td>' . $key . '</td><td>' . $item['current'] . '</td><td>' . $item['recommended'] . '</td><td>' . ($item['status'] ? ParsVT_Check_Requirements::$no : ParsVT_Check_Requirements::$yes) . '</td></tr>';}?></tbody>
</table>
</div>
</section>
<section>
<div class="align">
<h2>Other Information</h2>
<h3>Server Information</h3>
<table>
<thead>
<tr>
<th>Parameter</th>
<th colspan="2">Value</th>
</tr>
</thead>
<tbody><?php foreach($SYSINFO as $key=>$item){echo "<tr><td>".$key.'</label></td><td colspan="2">'.$item."</td></tr>";} ?></tbody>
</table>
</div>
<div class='align'>
<h3>Client Information</h3>
<table>
<thead>
<tr>
<th>Parameter</th>
<th colspan="2">Value</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<tr>
<td>Browser</td>
<td colspan='2' id="browserName1"></td>
<td id="browserName2"></td>
</tr>
<tr>
<td>User-Agent</td>
<td colspan='2' id="userAgent1"></td>
<td id='userAgent2'></td>
</tr>
<tr>
<td>Operating system</td>
<td colspan='2' id="OS1"></td>
<td id='OS2'></td>
</tr>
<tr>
<td>Cookie enabled</td>
<td colspan='2' id="cookieEnabled1"></td>
<td id='cookieEnabled2'></td>
</tr>
<tr>
<td>HTML5 enabled</td>
<td colspan='2' id="HTML1"></td>
<td id='HTML2'></td>
</tr>
<tr>
<td>Screen width</td>
<td colspan='2' id="screenwidth1"></td>
<td id='screenwidth2'></td>
</tr>
<tr>
<td>Screen height</td>
<td colspan='2' id="screenheight1"></td>
<td id='screenheight2'></td>
</tr>
</tbody>
</table>
</div>
</section>
</div>
</div>
<footer class='app-footer'>
<p><span>Version</span> - 1.0.2 &nbsp;|&nbsp; <span>Patch</span> - 14020908 &nbsp;|&nbsp; <span>© 2023 <a href="https://parsvt.com" target="_blank">ParsVT Group</a>.</span></p>
</footer>
<ul class="sticky_sidebar">
<li>
<a href="#" onClick="window.location.reload();">
<svg viewBox="0 0 512 512">
<use xlink:href="#icon-refresh"></use>
</svg>
</a>
</li>
<?php if ($connection) { ?>
<li>
<a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?action=reset">
<svg viewBox="0 0 512 512">
<use xlink:href="#icon-logout"></use>
</svg>
</a>
</li>
<?php } ?>
</ul>
<svg xmlns="http://www.w3.org/2000/svg" class="icons">
<symbol id="icon-user">
<path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" />
</symbol>
<symbol id="icon-lock">
<path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z" />
</symbol>
<symbol id="icon-database">
<path d="M448 80v48c0 44.2-100.3 80-224 80S0 172.2 0 128V80C0 35.8 100.3 0 224 0S448 35.8 448 80zM393.2 214.7c20.8-7.4 39.9-16.9 54.8-28.6V288c0 44.2-100.3 80-224 80S0 332.2 0 288V186.1c14.9 11.8 34 21.2 54.8 28.6C99.7 230.7 159.5 240 224 240s124.3-9.3 169.2-25.3zM0 346.1c14.9 11.8 34 21.2 54.8 28.6C99.7 390.7 159.5 400 224 400s124.3-9.3 169.2-25.3c20.8-7.4 39.9-16.9 54.8-28.6V432c0 44.2-100.3 80-224 80S0 476.2 0 432V346.1z" />
</symbol>
<symbol id="icon-php">
<path d="M320 104.5c171.4 0 303.2 72.2 303.2 151.5S491.3 407.5 320 407.5c-171.4 0-303.2-72.2-303.2-151.5S148.7 104.5 320 104.5m0-16.8C143.3 87.7 0 163 0 256s143.3 168.3 320 168.3S640 349 640 256 496.7 87.7 320 87.7zM218.2 242.5c-7.9 40.5-35.8 36.3-70.1 36.3l13.7-70.6c38 0 63.8-4.1 56.4 34.3zM97.4 350.3h36.7l8.7-44.8c41.1 0 66.6 3 90.2-19.1 26.1-24 32.9-66.7 14.3-88.1-9.7-11.2-25.3-16.7-46.5-16.7h-70.7L97.4 350.3zm185.7-213.6h36.5l-8.7 44.8c31.5 0 60.7-2.3 74.8 10.7 14.8 13.6 7.7 31-8.3 113.1h-37c15.4-79.4 18.3-86 12.7-92-5.4-5.8-17.7-4.6-47.4-4.6l-18.8 96.6h-36.5l32.7-168.6zM505 242.5c-8 41.1-36.7 36.3-70.1 36.3l13.7-70.6c38.2 0 63.8-4.1 56.4 34.3zM384.2 350.3H421l8.7-44.8c43.2 0 67.1 2.5 90.2-19.1 26.1-24 32.9-66.7 14.3-88.1-9.7-11.2-25.3-16.7-46.5-16.7H417l-32.8 168.7z" />
</symbol>
<symbol id="icon-server">
<path d="M64 32C28.7 32 0 60.7 0 96v64c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm280 72a24 24 0 1 1 0 48 24 24 0 1 1 0-48zm48 24a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zM64 288c-35.3 0-64 28.7-64 64v64c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V352c0-35.3-28.7-64-64-64H64zm280 72a24 24 0 1 1 0 48 24 24 0 1 1 0-48zm56 24a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z" />
</symbol>
<symbol id="icon-check">
<path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z" />
</symbol>
<symbol id="icon-xmark">
<path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
</symbol>
<symbol id="icon-config">
<path d="M308.5 135.3c7.1-6.3 9.9-16.2 6.2-25c-2.3-5.3-4.8-10.5-7.6-15.5L304 89.4c-3-5-6.3-9.9-9.8-14.6c-5.7-7.6-15.7-10.1-24.7-7.1l-28.2 9.3c-10.7-8.8-23-16-36.2-20.9L199 27.1c-1.9-9.3-9.1-16.7-18.5-17.8C173.9 8.4 167.2 8 160.4 8h-.7c-6.8 0-13.5 .4-20.1 1.2c-9.4 1.1-16.6 8.6-18.5 17.8L115 56.1c-13.3 5-25.5 12.1-36.2 20.9L50.5 67.8c-9-3-19-.5-24.7 7.1c-3.5 4.7-6.8 9.6-9.9 14.6l-3 5.3c-2.8 5-5.3 10.2-7.6 15.6c-3.7 8.7-.9 18.6 6.2 25l22.2 19.8C32.6 161.9 32 168.9 32 176s.6 14.1 1.7 20.9L11.5 216.7c-7.1 6.3-9.9 16.2-6.2 25c2.3 5.3 4.8 10.5 7.6 15.6l3 5.2c3 5.1 6.3 9.9 9.9 14.6c5.7 7.6 15.7 10.1 24.7 7.1l28.2-9.3c10.7 8.8 23 16 36.2 20.9l6.1 29.1c1.9 9.3 9.1 16.7 18.5 17.8c6.7 .8 13.5 1.2 20.4 1.2s13.7-.4 20.4-1.2c9.4-1.1 16.6-8.6 18.5-17.8l6.1-29.1c13.3-5 25.5-12.1 36.2-20.9l28.2 9.3c9 3 19 .5 24.7-7.1c3.5-4.7 6.8-9.5 9.8-14.6l3.1-5.4c2.8-5 5.3-10.2 7.6-15.5c3.7-8.7 .9-18.6-6.2-25l-22.2-19.8c1.1-6.8 1.7-13.8 1.7-20.9s-.6-14.1-1.7-20.9l22.2-19.8zM112 176a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zM504.7 500.5c6.3 7.1 16.2 9.9 25 6.2c5.3-2.3 10.5-4.8 15.5-7.6l5.4-3.1c5-3 9.9-6.3 14.6-9.8c7.6-5.7 10.1-15.7 7.1-24.7l-9.3-28.2c8.8-10.7 16-23 20.9-36.2l29.1-6.1c9.3-1.9 16.7-9.1 17.8-18.5c.8-6.7 1.2-13.5 1.2-20.4s-.4-13.7-1.2-20.4c-1.1-9.4-8.6-16.6-17.8-18.5L583.9 307c-5-13.3-12.1-25.5-20.9-36.2l9.3-28.2c3-9 .5-19-7.1-24.7c-4.7-3.5-9.6-6.8-14.6-9.9l-5.3-3c-5-2.8-10.2-5.3-15.6-7.6c-8.7-3.7-18.6-.9-25 6.2l-19.8 22.2c-6.8-1.1-13.8-1.7-20.9-1.7s-14.1 .6-20.9 1.7l-19.8-22.2c-6.3-7.1-16.2-9.9-25-6.2c-5.3 2.3-10.5 4.8-15.6 7.6l-5.2 3c-5.1 3-9.9 6.3-14.6 9.9c-7.6 5.7-10.1 15.7-7.1 24.7l9.3 28.2c-8.8 10.7-16 23-20.9 36.2L315.1 313c-9.3 1.9-16.7 9.1-17.8 18.5c-.8 6.7-1.2 13.5-1.2 20.4s.4 13.7 1.2 20.4c1.1 9.4 8.6 16.6 17.8 18.5l29.1 6.1c5 13.3 12.1 25.5 20.9 36.2l-9.3 28.2c-3 9-.5 19 7.1 24.7c4.7 3.5 9.5 6.8 14.6 9.8l5.4 3.1c5 2.8 10.2 5.3 15.5 7.6c8.7 3.7 18.6 .9 25-6.2l19.8-22.2c6.8 1.1 13.8 1.7 20.9 1.7s14.1-.6 20.9-1.7l19.8 22.2zM464 304a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
</symbol>
<symbol id="icon-security">
<path d="M256 0c4.6 0 9.2 1 13.4 2.9L457.7 82.8c22 9.3 38.4 31 38.3 57.2c-.5 99.2-41.3 280.7-213.6 363.2c-16.7 8-36.1 8-52.8 0C57.3 420.7 16.5 239.2 16 140c-.1-26.2 16.3-47.9 38.3-57.2L242.7 2.9C246.8 1 251.4 0 256 0zm0 66.8V444.8C394 378 431.1 230.1 432 141.4L256 66.8l0 0z"/>
</symbol>
<symbol id="icon-refresh">
<path d='M105.1 202.6c7.7-21.8 20.2-42.3 37.8-59.8c62.5-62.5 163.8-62.5 226.3 0L386.3 160H336c-17.7 0-32 14.3-32 32s14.3 32 32 32H463.5c0 0 0 0 0 0h.4c17.7 0 32-14.3 32-32V64c0-17.7-14.3-32-32-32s-32 14.3-32 32v51.2L414.4 97.6c-87.5-87.5-229.3-87.5-316.8 0C73.2 122 55.6 150.7 44.8 181.4c-5.9 16.7 2.9 34.9 19.5 40.8s34.9-2.9 40.8-19.5zM39 289.3c-5 1.5-9.8 4.2-13.7 8.2c-4 4-6.7 8.8-8.1 14c-.3 1.2-.6 2.5-.8 3.8c-.3 1.7-.4 3.4-.4 5.1V448c0 17.7 14.3 32 32 32s32-14.3 32-32V396.9l17.6 17.5 0 0c87.5 87.4 229.3 87.4 316.7 0c24.4-24.4 42.1-53.1 52.9-83.7c5.9-16.7-2.9-34.9-19.5-40.8s-34.9 2.9-40.8 19.5c-7.7 21.8-20.2 42.3-37.8 59.8c-62.5 62.5-163.8 62.5-226.3 0l-.1-.1L125.6 352H176c17.7 0 32-14.3 32-32s-14.3-32-32-32H48.4c-1.6 0-3.2 .1-4.8 .3s-3.1 .5-4.6 1z'/>
</symbol>
<symbol id="icon-logout">
<path d='M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z'/>
</symbol>
</svg>
</body>
</html>
