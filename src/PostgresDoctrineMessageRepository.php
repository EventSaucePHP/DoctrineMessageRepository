<?php

namespace EventSauce\DoctrineMessageRepository;

class PostgresDoctrineMessageRepository extends BaseDoctrineMessageRepository
{
    protected function baseSql(string $tableName): string
    {
        return "INSERT INTO {$tableName} (event_id, event_type, aggregate_root_id, time_of_recording, payload) VALUES ";
    }
}