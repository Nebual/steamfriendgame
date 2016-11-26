<?php
require __DIR__ . '/vendor/autoload.php';
include('share/Cache.php');
include('share/SteamAPIWrapper.php');

function slog($subject, $body = '') {
	if (is_array($body) or is_object($body)) $body = var_export($body, true);

	if (is_array($subject) or is_object($subject)) $subject = "\n" . var_export($subject, true);
	file_put_contents('/tmp/php_error.log', "\n-- SLOG -- [" . strftime('%b %d %H:%M:%S') . "]: '" . $subject . "'\n" . $body, FILE_APPEND);
}

session_start();

use GuzzleHttp\Client;
use Steam\Configuration;
use Steam\Runner\GuzzleRunner;
use Steam\Runner\DecodeJsonStringRunner;
use Steam\Steam;
use Steam\Utility\GuzzleUrlBuilder;

$steam = new Steam(new Configuration([
	Configuration::STEAM_KEY => jcurl('secrets.json')['steam_api_key']
]));
$steam->addRunner(new GuzzleRunner(new Client(), new GuzzleUrlBuilder()));
$steam->addRunner(new DecodeJsonStringRunner());


function jcurl($url) : array
{
	return json_decode(file_get_contents($url), true) ?: [];
}
