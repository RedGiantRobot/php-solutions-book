<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Get Server's Time Zone</title>
</head>

<body>
<p>The server's time zone is
<?php 
$now = new DateTime();
$timezone = $now->getTimezone();
echo $timezone->getName();
?>
.</p>
</body>
</html>