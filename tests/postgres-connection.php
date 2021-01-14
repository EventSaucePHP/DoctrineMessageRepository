<?php

use Doctrine\DBAL\DriverManager;

return DriverManager::getConnection([
    'dbname' => 'domain_messages',
    'user' => 'username',
    'password' => 'password',
    'host' => 'pgsql',
    'driver' => 'pdo_pgsql',
]);
