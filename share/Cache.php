<?php

class Cache
{
	/** @var SQLite3 */
	private static $db;

	protected static function db() : SQLite3
	{
		if (isset(self::$db)) return static::$db;

		static::$db = new SQLite3('/tmp/steamfriendcache.db');

		static::$db->exec("CREATE TABLE IF NOT EXISTS cache (scope TEXT, id TEXT, value TEXT, expiry INTEGER)");
		static::$db->exec("DELETE FROM cache WHERE expiry < " . time());
		return static::$db;
	}


	protected static function get(string $scope, string $id)
	{
		$scope = SQLite3::escapeString($scope);
		$id = SQLite3::escapeString($id);
		$value = self::db()->querySingle("SELECT value FROM cache WHERE scope = '{$scope}' AND id = '{$id}'") ?? null;
		return $value !== null ? json_decode($value, true) : null;
	}
	protected static function set(string $scope, string $id, array $value, int $expiry_seconds=720)
	{
		$scope = SQLite3::escapeString($scope);
		$id = SQLite3::escapeString($id);
		$value = json_encode($value);
		$value = SQLite3::escapeString($value);
		$time = time() + $expiry_seconds;
		self::db()->exec("INSERT INTO cache VALUES ('{$scope}','{$id}','{$value}', {$time})");
	}

	public static function get_price(string $app_id)
	{
		return self::get('price', $app_id);
	}
	public static function set_price(string $app_id, array $price)
	{
		self::set('price', $app_id, $price, 60*60*6);
	}

	public static function get_games(string $steam_id)
	{
		return self::get('games', $steam_id);
	}
	public static function set_games(string $steam_id, array $games, int $expiry=null)
	{
		self::set('games', $steam_id, $games, $expiry ?? 60*60);
	}

	public static function get_friends(string $steam_id)
	{
		return self::get('friends', $steam_id);
	}
	public static function set_friends(string $steam_id, array $friends)
	{
		self::set('friends', $steam_id, $friends, 60*60*12);
	}

	public static function get_player(string $steam_id)
	{
		return self::get('player', $steam_id);
	}
	public static function set_player(string $steam_id, array $playerinfo)
	{
		self::set('player', $steam_id, $playerinfo, 60*60*12);
	}
}
