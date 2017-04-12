<?php

use Doctrine\DBAL\DriverManager;

return DriverManager::getConnection([
    'dbname' => 'domain_messages',
    'user' => 'username',
    'password' => 'password',
    'host' => '127.0.0.1',
    'driver' => 'pdo_mysql',
]);
