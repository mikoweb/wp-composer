<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Core\WordPress\InitApp;

define('WP_USE_THEMES', true);
$wp_did_header = true;
$init = new InitApp();
$init->init(__DIR__);
