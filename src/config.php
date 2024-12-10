<?php
require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

$firebase = (new Factory())
    ->withServiceAccount('path/to/your/firebase-service-account.json')
    ->withDatabaseUri('https://your-database.firebaseio.com');

$auth = $firebase->createAuth();
$database = $firebase->createDatabase();
