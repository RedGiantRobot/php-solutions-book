<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Formatting Dates with the date() Function</title>
</head>

<body>
<p>It's now <?php echo date('g.ia'); ?> on <?php echo date('l, F jS, Y'); ?></p>
<p>Christmas 2010 falls on a <?php echo date('l', strtotime('12/25/2010')); ?></p>
</body>
</html>