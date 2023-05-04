<?php
require_once __DIR__ . '/../Controllers/MessageControllers.php';

$app->post('/messages', [MessageControllers::class, 'post']);
$app->get('/messages', [MessageControllers::class, 'get']);