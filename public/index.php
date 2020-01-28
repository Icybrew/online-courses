<?php

/* Marking start time */
define('APP_START_TIME', microtime(true));

/* Auto loading classes */
require '../vendor/autoload.php';

/* Starting application */
require_once __DIR__ . '/../bootstrap/app.php';
