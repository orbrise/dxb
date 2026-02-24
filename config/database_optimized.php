<?php
// Database optimization configurations to be manually applied to your config/database.php

/* 
Add these optimizations to your existing mysql connection in config/database.php:

'options' => extension_loaded('pdo_mysql') ? array_filter([
    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    // Performance optimizations
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
]) : [],

// Optional: Add connection pool settings if your hosting supports it
'pool' => [
    'min_connections' => 1,
    'max_connections' => 10,
    'connect_timeout' => 10,
    'wait_timeout' => 3,
    'heartbeat' => -1,
    'max_idle_time' => 60,
],
*/