<?php

abstract class SteamAPIWrapper {

	public static function get_friends(string $steam_id) : array
	{
		if($friends_cached = Cache::get_friends($steam_id)) {
			return $friends_cached;
		}
		global $steam;
		$friends = $steam->run(new \Steam\Command\User\GetFriendList($steam_id))['friendslist']['friends'];
		Cache::set_friends($steam_id, $friends);

		$friends_to_fetch = [];
		foreach($friends as $friend) {
			if(!Cache::get_player($friend['steamid'])) {
				$friends_to_fetch[] = $friend['steamid'];
			}
		}
		for($I=0; $I<count($friends_to_fetch); $I+=99) {
			$current_request = array_slice($friends_to_fetch, $I, 99);
			$raw_results = $steam->run(new \Steam\Command\User\GetPlayerSummaries($current_request));
			foreach($raw_results['response']['players'] as $friend) {
				unset($friend['commentpermission'],
					$friend['avatarfull'],
					$friend['personastate'],
					$friend['primaryclanid'],
					$friend['timecreated'],
					$friend['personastateflags'],
					$friend['loccountrycode'],
					$friend['locstatecode']
				);
				Cache::set_player($friend['steamid'], $friend);
			}
		}
		return $friends;
	}

	public static function get_player_games(string $steam_id, bool $include_names=false) : array
	{
		if($games_cached = Cache::get_games($steam_id)) {
			return $games_cached;
		}
		global $steam;
		$response = $steam->run(
			(new \Steam\Command\PlayerService\GetOwnedGames($steam_id))
				->setIncludeFreeGames(true)
				->setIncludeAppInfo($include_names)
		);
		if(empty($response['response']['games'])) {
			slog("Failed fetching games for {$steam_id}", $response);
			Cache::set_games($steam_id, [], 0);
			return [];
		}
		$games_unsorted = $response['response']['games'];

		$games_by_appid = [];
		foreach($games_unsorted as $game) {
			$games_by_appid[$game['appid']] = $game;
		}

		Cache::set_games($steam_id, $games_by_appid);
		return $games_by_appid;
	}

	protected static $player_games = [];
	public static function player_has_game(string $steam_id, string $appid) : bool
	{
		if(!isset(self::$player_games[$steam_id])) {
			self::$player_games[$steam_id] = self::get_player_games($steam_id);
		}
		return !empty(self::$player_games[$steam_id][$appid]);
	}

	public static function get_prices(array $app_ids) : array
	{
		$app_ids_to_fetch = [];
		foreach($app_ids as $app_id) {
			if(Cache::get_price($app_id) === null) {
				$app_ids_to_fetch[] = $app_id;
			}
		}
		for($I=0; $I<count($app_ids_to_fetch); $I+=250) {
			$app_ids_current_request = array_slice($app_ids_to_fetch, $I, 250);
			$raw_prices = jcurl('http://store.steampowered.com/api/appdetails?filters=price_overview&appids='.implode($app_ids_current_request, ','));
			foreach($raw_prices as $app_id => $details) {
				Cache::set_price($app_id, $details['data']['price_overview'] ?? []);
			}
		}

		$ret = [];
		foreach($app_ids as $app_id) {
			$ret[$app_id] = Cache::get_price($app_id);
		}
		return $ret;
	}

}
