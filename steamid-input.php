<?php
require_once('global.php');

if(!empty($_POST['steamname'])) {
	$matches = [];
	$posted_name = $_POST['steamname'];
	if(preg_match('/STEAM_([0-9]+):([0-9]+):([0-9]+)/', $posted_name, $matches)) {
		// Old style SteamID
		$steam_id = 76561197960265728 + (int)$matches[3] * 2 + (int)$matches[2];
	} elseif(preg_match('/U:1:([0-9]+)/', $posted_name, $matches)) {
		// 32 bit steamID3
		$steam_id = 76561197960265728 + (int)$matches[1];
	} elseif(preg_match('/(?:(?:https?:\/\/)?steamcommunity\.com\/profiles\/)?([0-9]+)(?:\/.*)?$/', $posted_name, $matches)) {
		// 64 bit steamID3
		$posted_name = $matches[1];
		$steam_id = $matches[1];
	} elseif(preg_match('/(?:(?:https?:\/\/)?steamcommunity\.com\/id\/)?([A-Za-z0-9_\-]+)(?:\/.*)?$/', $posted_name, $matches)) {
		// Vanity URL
		$posted_name = $matches[1];
		$steam_id = $steam->run(new \Steam\Command\User\ResolveVanityUrl($posted_name))['response']['steamid'];
	}

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
		<label>Steam Vanity URL/SteamID</label>
		<i id="steamname-loading" class="fa fa-spinner fa-spin" style="display:none"></i>
		<input type="text" class="form-control" name="steamname" value="<?= $_SESSION['steamname'] ?? ''?>"/>
	</div>
</form>
