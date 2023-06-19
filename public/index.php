<?php

date_default_timezone_set("Asia/Calcutta");

const BASE_DIR         = __DIR__.'/../';
const BASE_DIR_PRIVATE = __DIR__.'/../private/';
const VIEWS_BASE_DIR   = __DIR__.'/../src/views';

// Load all dependencies
require BASE_DIR.'/vendor/autoload.php';

use Josantonius\Session\Session;

$DotEnv = Dotenv\Dotenv::createImmutable(BASE_DIR_PRIVATE);
$DotEnv->load();

Session::setPrefix('promulgate_');
Session::initCustom(env('SESSION_CUSTOM_NAME'), env('SESSION_EXPIRY_SECONDS'), env('SESSION_EXPIRY_PATH'), null, env('SESSION_EXPIRY_SECURE'), env('SESSION_EXPIRY_HTTPONLY'));

define('ALLOWED_TO_DEBUG', env('SITE_DEBUG') && input()->get('devDebug') && input()->get('devDebug')->getValue() == 1);
define('USE_TEST_API', (input()->get('devApi') && input()->get('devApi')->getValue() == 1));

// error_reporting(ALLOWED_TO_DEBUG ? E_ALL : 0);

error_reporting(E_ALL);


/* Load all routes */
require_once BASE_DIR.'routes.php';
