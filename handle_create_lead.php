<?php

require_once __DIR__ . '/vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteType\CommonNote;
use Dotenv\Dotenv;
use League\OAuth2\Client\Token\AccessToken;

// Проверка на POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('❌ Недопустимый метод запроса.');
}

// Получение данных из формы
$name = trim($_POST['name'] ?? '');
$price = intval($_POST['price'] ?? 0);
$noteText = trim($_POST['note'] ?? '');

if (!$name || $price <= 0) {
    exit('❌ Заполните все обязательные поля.');
}

// Загрузка переменных .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientId     = $_ENV['AMO_CLIENT_ID'];
$clientSecret = $_ENV['AMO_CLIENT_SECRET'];
$redirectUri  = $_ENV['AMO_REDIRECT_URI'];
$baseDomain   = $_ENV['AMO_DOMAIN'];

$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
$apiClient->setAccountBaseDomain($baseDomain);

// Загрузка токена
if (!file_exists(__DIR__ . '/token.json')) {
    exit('❌ Токен не найден. Авторизуйтесь.');
}

$tokenData = json_decode(file_get_contents(__DIR__ . '/token.json'), true);
$accessToken = new AccessToken($tokenData);
$apiClient->setAccessToken($accessToken);

// Создание сделки
try {
    $lead = new LeadModel();
    $lead->setName($name)
         ->setPrice($price);

    $lead = $apiClient->leads()->addOne($lead);


    // Добавим примечание, если введено
    if ($noteText) {
        $note = new CommonNote();
        $note->setEntityId($lead->getId());
        $note->setText($noteText);
        $apiClient->notes(AmoCRM\Models\NoteType\CommonNote::NOTE_TYPE)->addOne($note);
    }

    echo '✅ Сделка успешно создана! ID: ' . $lead->getId();
} catch (Throwable $e) {
    echo '❌ Ошибка: ' . $e->getMessage();
}
