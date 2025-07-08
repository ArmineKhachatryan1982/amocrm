<?php
require_once __DIR__ . '/vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use Dotenv\Dotenv;

// Загружаем переменные окружения
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Получаем переменные
$clientId = $_ENV['AMO_CLIENT_ID'];
$clientSecret = $_ENV['AMO_CLIENT_SECRET'];
$redirectUri = $_ENV['AMO_REDIRECT_URI'];

$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
// echo '<pre>';
// print_r($apiClient);
// echo '</pre>';

$authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
            'state' => $state,
            'mode' => 'post_message', //post_message - редирект произойдет в открытом окне, popup - редирект произойдет в окне родителе
        ]);

header('Location: ' . $authorizationUrl);