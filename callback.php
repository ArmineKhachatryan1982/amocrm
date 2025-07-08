<?php

require_once __DIR__ . '/vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use Dotenv\Dotenv;
use League\OAuth2\Client\Token\AccessToken;

// Загружаем переменные окружения
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Получаем из .env
$clientId = $_ENV['AMO_CLIENT_ID'];
$clientSecret = $_ENV['AMO_CLIENT_SECRET'];
$redirectUri = $_ENV['AMO_REDIRECT_URI'];
$baseDomain = $_ENV['AMO_DOMAIN'];

$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

// ✅ ВАЖНО: СНАЧАЛА УКАЗЫВАЕМ ДОМЕН
$apiClient->setAccountBaseDomain($baseDomain);

if (!isset($_GET['code'])) {
    exit('❌ Не передан код авторизации.');
}

try {
    // ✅ Только теперь получаем access_token
    $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

    // Устанавливаем токен
    $apiClient->setAccessToken($accessToken);

    // Сохраняем токен в файл
    file_put_contents(__DIR__ . '/token.json', json_encode($accessToken->jsonSerialize()));

    echo '✅ Токен получен и сохранён!';
} catch (Throwable $e) {
    echo '❌ Ошибка: ' . $e->getMessage();
}
