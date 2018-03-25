<?php
namespace Nerrad\BuildMachine\WebHookListener;

require 'vendor/autoload.php';

use Exception;
use Nerrad\BuildMachine\WebHookListener\Http\RequestFactory;

define('CB_WEBHOOK_BASE_PATH', __DIR__ . '/');

//react
try {
    $config = new Config();
    //grab request and pass to React class.
    $request = RequestFactory::getRequestForRepositoryType($_REQUEST);
    new React($request, $config);
} catch (Exception $e) {
    $msg = $e->getMessage();
    error_log($msg);
    header($msg, true, 501);
    exit();
}
