# Doctrine Message Repository for EventSauce



```php
use Doctrine\DBAL\Connection;
use EventSauce\DoctrineMessageRepository\DoctrineMessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;

/** @var Connection $doctrineConnection */
$doctrineConnection = setup_doctrine_connection();
/** @var MessageSerializer $messageSerializer */
$messageSerializer = setup_message_serializer();
$messageRepository = new DoctrineMessageRepository(
    $doctrineConnection,
    $messageSerializer,
    'your_table_name',
);
```

Next step: [Use it when bootstrapping your aggregate root repository](https://eventsauce.io/docs/event-sourcing/bootstrap/)

For the schema used for this repository, see:

- [The MySQL schema setup](https://github.com/EventSaucePHP/DoctrineMessageRepository/blob/master/tests/setup-mysql-schema.php)
- [The PostgreSQL schema setup](https://github.com/EventSaucePHP/DoctrineMessageRepository/blob/master/tests/setup-postgres-schema.php)
