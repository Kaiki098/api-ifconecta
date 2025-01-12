<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/src/routes/main.php";

use App\Core\Core;
use App\Http\Route;
use Dotenv\Dotenv;
use App\Middleware\CorsMiddleware;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

CorsMiddleware::handle();

Core::dispatch(Route::routes());