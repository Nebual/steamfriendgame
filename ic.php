<?php
require('global.php');

switch($_POST['ic-element-id']) {
	case 'steamname-form':
		$steam_id = $steam->run(new \Steam\Command\User\ResolveVanityUrl($_POST['steamname']))['response']['steamid'];
		if($steam_id) {
			$_SESSION['steamname'] = $_POST['steamname'];
			$_SESSION['steam_id'] = $steam_id;
		}
		break;
	case 'friends-form':
		unset($_SESSION['selected-friends']);
		foreach($_POST['friends'] as $steam_id) {
			$_SESSION['selected-friends'][$steam_id] = 1;
		}
		break;
}
