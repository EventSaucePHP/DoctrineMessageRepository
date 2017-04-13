<?php

include __DIR__ . '/../vendor/autoload.php';

$connection = include __DIR__ . '/connection.php';
$connection->exec("
CREATE TABLE IF NOT EXISTS domain_messages (
    event_id VARCHAR(36) NOT NULL,
    aggregate_root_id VARCHAR(36) NOT NULL,
    time_of_recording DATETIME(6) NOT NULL,
    payload TEXT NOT NULL,
    INDEX aggregate_root_id (aggregate_root_id),
    INDEX time_of_recording (time_of_recording)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
");