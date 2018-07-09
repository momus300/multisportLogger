<?php
/**
 * Created by PhpStorm.
 * User: marek
 * Date: 05.07.18
 * Time: 13:00
 */

$fileName = basename(__FILE__);
if ($argc != 3) {
    die("Type like this:\nphp {$fileName} <loginName> <password>\n");
}

$login = $argv[1];
$pass = $argv[2];

$url = "https://www.kartamultisport.pl/moj-profil?user={$login}&pass={$pass}&submit=Zaloguj&logintype=login&pid=561%2C531&redirect_url=%2F&tx_felogin_pi1%5Bnoredirect%5D=1";

echo 'Wchodzę na: ' . $url . PHP_EOL;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
curl_close($ch);

$dateTime = date('Y-m-d H:i:s');
$responseLocation = __DIR__ . '/websitesResponses/' . str_replace(' ', '_', $dateTime) . '.html';
file_put_contents($responseLocation, $response);

$data = '[' . date('Y-m-d H:i:s') . '] ' . 'loguje sie jako: ' . $login . ' ' . $pass . ' response: ' . (!empty($response) ? 'true' : 'false') . ' [' . $responseLocation . ']' . PHP_EOL;

file_put_contents(__DIR__ . '/output.txt', $data, FILE_APPEND);
