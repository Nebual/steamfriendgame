<?php
require_once('global.php');

if(!empty($_SESSION['steam_id'])) {

	$multiplayer_tags = SteamAPIWrapper::get_multiplayer_games();
	$games = SteamAPIWrapper::get_player_games($_SESSION['steam_id'], true);
	$prices = SteamAPIWrapper::get_prices(array_keys($games));

	$max_price_grand_total = 0;
	$current_price_grand_total = 0;
	$rows = '';
	foreach($games as $appid => $game) {
		$is_multiplayer = in_array((int)$appid, $multiplayer_tags, true) ? '1' : '';
		if(!empty($prices[$appid])) {
			$max_price_grand_total += $prices[$appid]['initial'];
			$current_price_grand_total += $prices[$appid]['final'];
			$price = '$' . number_format($prices[$appid]['final'] / 100, 2) . " ({$prices[$appid]['currency']})";
		} else {
			$price = '-';
		}
		$percent = !empty($prices[$appid]['discount_percent']) ? ($prices[$appid]['discount_percent'] . '%') : '-';
		$playtime_hours = ((($game['playtime_forever']??0)/60) > 0.3) ? number_format($game['playtime_forever']/60, 1, '.', '') : '-';

		$rows .= "
			<tr data-multiplayer='{$is_multiplayer}'>
				<td>{$game['name']}</td>
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

	<label><input type="checkbox" name="multiplayer-only" class="multiplayer-only" value="1"/> Multiplayer Only</label><br>
	<div class="min-ownership-container">
		<label>Minimum Ownership</label>: <output></output>
		<input id="min-count" type="range" min="0" max="<?=count($_SESSION['selected-friends']??[])?>" value="0" style="width: 200px;" />
	</div>

	<table id='my-games' class='table' data-sort-name="hours" data-sort-order="desc" data-sticky-header="true">
		<thead>
		<tr>
			<th data-field='name_plain' data-sortable='true' data-visible="false"></th>
			<th data-field='name' data-sortable='true' data-sort-name="name_plain" data-order="desc">Name</th>
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
		$('input[type="range"]').rangeslider({polyfill: false});

		function numericOnly(a, b) {
			function stripNonNumber(s) {
				s = s.replace(new RegExp(/[^0-9]/g), "");
				return parseInt(s, 10) || 0;
			}

			return stripNonNumber(a) - stripNonNumber(b);
		}

		function filter_games() {
			var min = $('#min-count').val() || 0;
			$('#min-count').parent().find('output').text(min);
			var multiplayer_only = $('.multiplayer-only').is(':checked');
			$('#my-games tbody').find('tr').each(function() {
				var visible = true;
				if(visible) {
					if (multiplayer_only) {
						visible = $(this).data('multiplayer') == '1';
					}
				}
				if(visible) {
					visible = $(this).find('.owned').length >= min;
				}
				$(this).toggle(visible);
			});
		}
		$('#min-count').on('input', filter_games).trigger('input');
		$('.multiplayer-only').change(filter_games);
		$('#my-games').on('post-body.bs.table', function() {
			filter_games();
		});
	</script>
	<?
}
