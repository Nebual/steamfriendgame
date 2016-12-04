<?php
require_once('global.php');
?>
<html>
<head>
	<script src="//code.jquery.com/jquery-3.1.1.js" integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA=" crossorigin="anonymous"></script>
	<script src="//cdn.intercoolerjs.org/intercooler-1.0.3.min.js"></script>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/bootstrap-table.min.css" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/bootstrap-table.min.js"></script>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>

	<script src="lib/jquery.ba-bbq.min.js" type="text/javascript"></script>

	<link rel="stylesheet" href="lib/bootstrap-table-sticky-header.css">
	<script src="lib/bootstrap-table-sticky-header.js" type="text/javascript"></script>

	<link rel="stylesheet" href="lib/rangeslider.css">
	<script src="lib/rangeslider.min.js" type="text/javascript"></script>

	<link rel="stylesheet" href="style.css" />
	<script src="lib.js" type="text/javascript"></script>
</head>
<body>
<div class="container">
	<h1>Steam Friend Game thing</h1>
	<form id="steamname-form" ic-post-to="ic.php?action=steamname" ic-indicator="#steamname-loading" style="max-width: 200px;">
		<div class="form-group">
			<label>Your Steam Vanity URL name</label>
			<i id="steamname-loading" class="fa fa-spinner fa-spin" style="display:none"></i>
			<input type="text" class="form-control" name="steamname" value="<?= !empty($_SESSION['steamname']) ? $_SESSION['steamname'] : ''?>"/>
		</div>
	</form>
	<form id="friends-form" ic-post-to="ic.php?action=friends" ic-indicator="#steamname-loading">
		<div ic-deps="ic.php?action=steamname" ic-src="friend-list.php" <?= !empty($_SESSION['steamname']) ? "ic-trigger-on='load'" : ''?>>
		</div>
	</form>

	<p id="game-table-loading" style="display: none;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading games...</p>
	<div ic-deps="ic.php?action=steamname" ic-src="game-table.php" <?= !empty($_SESSION['steamname']) ? "ic-trigger-on='load'" : ''?> ic-indicator="#game-table-loading">
	</div>
</div>

</body>
</html>
