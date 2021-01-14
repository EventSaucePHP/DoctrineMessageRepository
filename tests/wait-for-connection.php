<?php

$start = time();
$driver = $argv[1];

while (true) {
    try {
        new PDO($driver.':host=127.0.0.1;dbname=domain_messages', 'username', 'password');
        fwrite(STDOUT, 'Docker container started!' . PHP_EOL);
        exit(0);
    } catch (PDOException $exception) {
        $elapsed = time() - $start;

        if ($elapsed > 30) {
            throw $exception;
        }

        fwrite(STDOUT, 'Waiting for container to start...' . PHP_EOL);
        sleep(1);
    }
};
