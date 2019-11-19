<?php

/**
 * @author Maksim Khortov <xoptov@mail.ru>
 * @version 1.0.0
 */

define('DEBUG_MODE', false);

define('API_KEY', '77826e8bc72600cc6be514fc9d6c507c70253c76'); // Необходимо указать ключь API внутри ''
define('ROOM_ID', 1057); // Идентификатор комнаты в которую будет добавляться лид.

//define('API_URL', 'https://cabinet.edward-lead.ru/api/v1/lead');
define('API_URL', 'http://edward.local/api/v1/lead');

define('API_TIMEOUT', 60);

ini_set('error_reporting', E_ERROR);

$formFields = ['name', 'phone', 'hasAgreement', 'email']; // Список полей в формы.

if (!extension_loaded('json')) {
    sendResponse('Необходимо подключить json расширение к php', 500, ['Content-Type: text/html; charset=UTF-8']);
}

if (isset($_GET['check']) && $_GET['check']) {
    $checks = [
        'curl' => extension_loaded('curl'),
    ];
    sendResponse(json_encode($checks));
}

if (!extension_loaded('curl')) {
    sendResponse('Необходимо подключить curl расширение к php', 500, ['Content-Type: text/html; charset=UTF-8']);
}

checkConfigs();

$errors = [];

foreach ($formFields as $field)
{
    $validateFunction = 'validate' . $field;
    if (function_exists($validateFunction)) {
        call_user_func_array($validateFunction, [&$errors]);
    }
}

if (count($errors)) {
    $content = ['errors' => $errors];
    sendResponse(json_encode($content), 400);
}

$dataForSubmit = [];

foreach ($formFields as $field) {
    if (isset($_POST[$field])) {
        $dataForSubmit[$field] = $_POST[$field];
    }
}

$dataForSubmit['room'] = ROOM_ID;
$postData = http_build_query($dataForSubmit);

$headers = [
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'X-Auth-Token: ' . API_KEY
];

if (DEBUG_MODE) {
    $headers[] = 'Cookie: XDEBUG_SESSION=PHPSTORM';
}

$ch = curl_init(API_URL);

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT);

$response = curl_exec($ch);
$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

curl_close($ch);

if (500 === $responseCode) {
    sendResponse(json_encode(['error' => 'Произошла ошибка при отправки лида на платформу']), 500);
}

if (403 === $responseCode) {
    sendResponse(json_encode(['error' => 'У сайта нет прав отправлять информацию на платформу Edward']), 403);
}

if (400 === $responseCode) {
    sendResponse($response, 400);
}

sendResponse($response, 200);

function validatePhone(array &$errors)
{
    if (empty($_POST['phone'])) {
        $errors['phone'] = ['Необходимо указать номер телефона'];
    }
}

function sendResponse(string $content, int $code = 200, array $headers = [])
{
    http_response_code($code);
    header('Content-Type: application/json; charset=UTF-8');
    foreach ($headers as $header) {
        header($header, true);
    }
    echo $content;
    exit();
}

function checkConfigs()
{
    if (empty(API_KEY)) {
        $content = ['error' => 'Необходимо указать API_KEY'];
        sendResponse(json_encode($content), 500);
    }

    if (empty(API_URL)) {
        $content = ['error' => 'Необходимо указать API_URL'];
        sendResponse(json_encode($content), 500);
    }

    if (empty(ROOM_ID)) {
        $content = ['error' => 'Необходимо указать ROOM_ID'];
        sendResponse(json_encode($content), 500);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $content = ['error' => 'Данные необходимо отправлять методом POST'];
        sendResponse(json_encode($content), 400);
    }
}