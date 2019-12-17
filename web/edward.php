<?php

/**
 * @version 1.0.2
 */

ini_set('error_reporting', E_ERROR);
ini_set('log_errors', true);
ini_set('error_log', 'edward.log');

define('API_KEY', ''); // Необходимо указать ключ API внутри ''
define('ROOM_ID', null); // Идентификатор комнаты в которую будет добавляться лид.

define('API_URL', 'http://stage.edward-lead.ru/api/v1/lead');
define('API_TIMEOUT', 5);

$formFields = ['name', 'phone', 'hasAgreement']; // Список полей в формы.

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
    sendResponse(json_encode([$errors]), 400);
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

$ch = curl_init(API_URL);

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT);

$response = curl_exec($ch);
$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

curl_close($ch);

if (500 === $responseCode || !$response) {
    sendResponse(json_encode(['Произошла ошибка при отправки лида на платформу']), 500);
}

if (403 === $responseCode) {
    sendResponse(json_encode(['У сайта нет прав отправлять информацию на платформу Edward']), 403);
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

function sendResponse($content, $code=200, array $headers=array())
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
        $content = ['Необходимо указать API_KEY'];
        sendResponse(json_encode($content), 500);
    }

    if (empty(API_URL)) {
        $content = ['Необходимо указать API_URL'];
        sendResponse(json_encode($content), 500);
    }

    if (empty(ROOM_ID)) {
        $content = ['Необходимо указать ROOM_ID'];
        sendResponse(json_encode($content), 500);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $content = ['Данные необходимо отправлять методом POST'];
        sendResponse(json_encode($content), 400);
    }
}