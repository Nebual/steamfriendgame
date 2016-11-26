<?php
require_once('global.php');

if(!empty($_SESSION['steam_id'])) {
	$games = SteamAPIWrapper::get_player_games($_SESSION['steam_id'], true);
	$prices = SteamAPIWrapper::get_prices(array_keys($games));

	$max_price_grand_total = 0;
	$current_price_grand_total = 0;
	$rows = '';
	foreach($games as $appid => $game) {
		if(!empty($prices[$appid])) {
			$max_price_grand_total += $prices[$appid]['initial'];
			$current_price_grand_total += $prices[$appid]['final'];
			$price = '$' . number_format($prices[$appid]['final'] / 100, 2) . " ({$prices[$appid]['currency']})";
			$percent = $prices[$appid]['discount_percent'] . '%';
		} else {
			$price = '';
			$percent = '';
		}
		$playtime_hours = number_format($game['playtime_forever']/60, 1, '.', '');

		$rows .= "
			<tr>
				<td><a href='http://store.steampowered.com/app/{$appid}/'>{$game['name']}</a></td>
				<td>{$price}</td>
				<td>{$percent}</td>
				<td>{$playtime_hours}</td>
		";
		foreach((isset($_SESSION['selected-friends']) ? $_SESSION['selected-friends'] : []) as $steam_id => $_) {
			$td_class = SteamAPIWrapper::player_has_game($steam_id, $appid) ? 'owned' : '';
			$rows .= "<td class='{$td_class}'></td>\n";
		}
		$rows .= "</tr>";
	}
	?>
	<p>
		You have <?=count($games)?> games in your Steam library.
		<br>If you purchased your entire library right now, it'd cost you: $<?=number_format($current_price_grand_total / 100, 2)?>.
		<br>The current 'full retail' value is: $<?=number_format($max_price_grand_total / 100, 2)?>.
		<br>Of course, you probably bought half these off Humble Bundle anyway.
	</p>


	<table id='my-games' class='table' data-sort-name="hours" data-sort-order="desc" data-sticky-header="true">
		<thead>
		<tr>
			<th data-field='name' data-sortable='true' data-sorter='numericOnly'>Name</th>
			<th data-field='price' data-sortable='true' data-sorter='numericOnly'>Current Price</th>
			<th data-field='savings' data-sortable='true' data-sorter='numericOnly'>Savings</th>
			<th data-field='hours' data-sortable='true' data-sorter='numericOnly'>Hours</th>
			<? foreach((isset($_SESSION['selected-friends']) ? $_SESSION['selected-friends'] : []) as $steam_id => $_) { ?>
				<th class="friend-th"><img src='<?=Cache::get_player($steam_id)['avatarmedium']?>'></th>
			<? } ?>
		</tr>
		</thead>
		<tbody>
			<?=$rows?>
		</tbody>
	</table>
	<script>
		$('#my-games').bootstrapTable();

		function numericOnly(a, b) {
			function stripNonNumber(s) {
				s = s.replace(new RegExp(/[^0-9]/g), "");
				return parseInt(s, 10);
			}

			return stripNonNumber(a) - stripNonNumber(b);
		}
	</script>
	<?
}
