<?php

use Doctrine\DBAL\Connection;

include __DIR__ . '/../vendor/autoload.php';

/** @var Connection $connection */
$connection = include __DIR__ . '/postgres-connection.php';
$connection->exec("DROP TABLE IF EXISTS domain_messages");
$connection->exec("CREATE TABLE domain_messages (
    event_id UUID NOT NULL,
    event_type VARCHAR(255) NOT NULL,
    aggregate_root_id UUID NOT NULL,
    aggregate_root_version INTEGER NOT NULL,
    time_of_recording TIMESTAMP(6) WITH TIME ZONE NOT NULL,
    payload JSON NOT NULL,
    PRIMARY KEY(event_id)
)");
$connection->exec("CREATE UNIQUE INDEX
    IF NOT EXISTS
    unique_id_and_version on domain_messages (
        aggregate_root_id,
        aggregate_root_version ASC
    )");
