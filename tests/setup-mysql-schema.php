<?php

include __DIR__ . '/../vendor/autoload.php';

$connection = include __DIR__ . '/mysql-connection.php';
$connection->exec("DROP TABLE IF EXISTS domain_messages");
$connection->exec("
CREATE TABLE IF NOT EXISTS domain_messages (
    event_id VARCHAR(36) NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    aggregate_root_id VARCHAR(36) NOT NULL,
    aggregate_root_version MEDIUMINT(36) UNSIGNED NOT NULL,
    payload JSON NOT NULL,
    INDEX aggregate_root_id (aggregate_root_id),
    UNIQUE KEY unique_id_and_version (aggregate_root_id, aggregate_root_version ASC)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE = InnoDB
");
