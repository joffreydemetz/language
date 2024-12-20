<?php
require_once realpath(__DIR__ . '/../vendor/autoload.php');

use JDZ\Language\Language as jLanguage;

$language = new jLanguage(
    ['fr', 'en'],
    'fr'
);

$language->load('fr');
$language->loadArray([
    'test' => [
        'key1' => 'Test 1 nested',
        'key2' => 'Test 1 nested 2',
    ],
    'test2' => 'Test 2',
]);
$language->loadYmlFile(__DIR__ . '/example.yml');

echo $language->get('test.key2');
exit();
