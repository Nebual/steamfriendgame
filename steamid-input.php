<?php
require_once('global.php');

if(!empty($_POST['steamname'])) {
	$posted_name = $_POST['steamname'];
	$steam_id = $steam->run(new \Steam\Command\User\ResolveVanityUrl($_POST['steamname']))['response']['steamid'];

	if(!empty($steam_id)) {
		$_SESSION['steamname'] = $posted_name;
		if(empty($_SESSION['steam_id']) or $_SESSION['steam_id'] != $steam_id) {
			unset($_SESSION['selected-friends']);
			$_SESSION['steam_id'] = $steam_id;
			header('X-IC-Refresh: game-table.php');
		}
	}
}
?>

<form id="steamname-form" ic-post-to="steamid-input.php" ic-indicator="#steamname-loading" ic-replace-target="true" style="max-width: 200px;">
	<div class="form-group">
		<label>Steam Vanity URL</label>
		<i id="steamname-loading" class="fa fa-spinner fa-spin" style="display:none"></i>
		<input type="text" class="form-control" name="steamname" value="<?= $_SESSION['steamname'] ?? ''?>"/>
	</div>
</form>
