<?php

// Загружаем .env
require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Загружаем access_token из token.json
$tokenFile = __DIR__ . '/token.json';

if (!file_exists($tokenFile)) {
    exit('❌ Не найден файл token.json. Сначала авторизуйтесь.');
}

$tokenData = json_decode(file_get_contents($tokenFile), true);
$accessToken = $tokenData['access_token'] ?? null;

if (!$accessToken) {
    exit('❌ Не найден access_token.');
}

// Данные из формы
$name  = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (!$name) {
    exit('❌ Укажите имя.');
}

// Собираем тело запроса
$data = [
    [
        "name" => $name,
        "custom_fields_values" => [
            [
                "field_code" => "PHONE",
                "values" => [
                    ["value" => $phone]
                ]
            ]
        ]
    ]
];

// Конвертируем в JSON
$jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);

// Инициализируем cURL
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => "https://{$_ENV['AMO_DOMAIN']}/api/v4/contacts",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => $jsonData,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// Обработка результата
if ($httpCode === 200 || $httpCode === 202) {
    echo "✅ Контакт успешно создан!";
} else {
    echo "❌ Ошибка. HTTP $httpCode\n";
    echo "Ответ: $response";
}
