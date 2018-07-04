<?php

include __DIR__ . '/../vendor/autoload.php';

$connection = include __DIR__ . '/mysql-connection.php';
$connection->exec("DROP TABLE IF EXISTS domain_messages");
$connection->exec("
CREATE TABLE IF NOT EXISTS domain_messages (
    event_id VARCHAR(36) NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    aggregate_root_id VARCHAR(36) NULL,
    time_of_recording DATETIME(6) NOT NULL,
    payload JSON NOT NULL,
    INDEX aggregate_root_id (aggregate_root_id),
    INDEX time_of_recording (time_of_recording)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE = InnoDB
");