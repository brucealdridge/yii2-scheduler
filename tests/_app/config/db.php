<?php
/**
 * @copyright Copyright(c) 2016 Webtools Ltd
 * @link https://github.com/thamtech/yii2-scheduler
 * @license https://opensource.org/licenses/MIT
**/

$db = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1;dbname=scheduler_test',
    'username' => 'scheduler_test',
    'password' => 'scheduler_test',
    'charset' => 'utf8',
];

if (getenv('TRAVIS') == true || getenv('SCRUTINIZER') == true) {
    $db['username'] = 'root';
    $db['password'] = '';
}


if (file_exists(__DIR__ . '/db.local.php')) {
    $db = array_merge($db, require(__DIR__ . '/db.local.php'));
}

return $db;

