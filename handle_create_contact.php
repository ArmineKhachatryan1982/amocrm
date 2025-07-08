<?php

require_once __DIR__ . '/vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use Dotenv\Dotenv;
use League\OAuth2\Client\Token\AccessToken;
use AmoCRM\Models\ContactModel;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Enums\EntityTypes;

// Загружаем данные из формы
$name  = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (!$name) {
    exit('❌ Укажите имя.');
}

// Загружаем переменные окружения
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientId     = $_ENV['AMO_CLIENT_ID'];
$clientSecret = $_ENV['AMO_CLIENT_SECRET'];
$redirectUri  = $_ENV['AMO_REDIRECT_URI'];
$baseDomain   = $_ENV['AMO_DOMAIN'];

$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
$apiClient->setAccountBaseDomain($baseDomain);

// Загружаем access token
if (!file_exists(__DIR__ . '/token.json')) {
    exit('❌ Токен не найден. Сначала авторизуйтесь.');
}

$tokenData   = json_decode(file_get_contents(__DIR__ . '/token.json'), true);
$accessToken = new AccessToken($tokenData);
$apiClient->setAccessToken($accessToken);

// 🟢 5. Получаем enum_id для телефона (например, "WORK")
$customFieldsService = $apiClient->customFields('contacts');
$contactFields = $customFieldsService->get();
// foreach ($contactFields as $field) {
//     echo "Название: " . $field->getName() . " — Код: " . $field->getCode() . PHP_EOL;
// }



try {
    $contact = new ContactModel();
    $contact->setName($name);

    if ($phone) {
    $phoneValue = new MultitextCustomFieldValueModel();
    $phoneValue->setValue($phone);
    // НЕ вызываем $phoneValue->setEnumId()

    $phoneField = new MultitextCustomFieldValuesModel();
    $phoneField->setFieldCode('PHONE');
    $phoneField->setValues(
        (new MultitextCustomFieldValueCollection())
        ->add($phoneValue)
    );

    $contact->setCustomFieldsValues(
        new CustomFieldsValuesCollection([$phoneField])
    );
}

    $contact = $apiClient->contacts()->addOne($contact);

    echo "✅ Контакт успешно создан! ID: " . $contact->getId();
} catch (Throwable $e) {
    echo "❌ Ошибка: " . $e->getMessage();
}
