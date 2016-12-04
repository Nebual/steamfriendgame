<?php
require_once('global.php');

if(($_POST['ic-element-id']??'') === 'friends-form') {
	unset($_SESSION['selected-friends']);
	foreach($_POST['friends'] as $steam_id) {
		$_SESSION['selected-friends'][$steam_id] = 1;
	}
	return;
}
?>

<form id="friends-form" ic-deps="ignore" ic-post-to="friend-list.php" ic-indicator="#steamname-loading">
<?
if(!empty($_SESSION['steam_id'])) {
	$friends = SteamAPIWrapper::get_friends($_SESSION['steam_id']);
	?>
	<select id="friends-select" name="friends[]" class="selectpicker" multiple data-live-search="true" title="Enable friends..." data-max-options="8" data-show-icon="false" data-size="10">
		<? foreach($friends as $friend) {
			$friend_data = Cache::get_player($friend['steamid']);
			$selected = !empty($_SESSION['selected-friends'][$friend['steamid']]) ? 'selected' : '';
			if($friend_data['communityvisibilitystate'] === 3) {
				echo "<option value='{$friend['steamid']}' {$selected} data-icon='glyphicon-plus invisible'
					data-content='<img src=\"{$friend_data['avatar']}\"> {$friend_data['personaname']}' title='{$friend_data['personaname']}'>
					{$friend_data['personaname']}</option>";
			} else {
				echo "<option value='{$friend['steamid']}' disabled data-icon='glyphicon-lock'>{$friend_data['personaname']}</option>";
			}
		} ?>
	</select>
	<button class="btn btn-default">Update</button>
	<script>
		$('.selectpicker').selectpicker();
	</script>
	<?
}
?>
</form>
