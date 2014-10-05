<?php

define("BASE_DIR", __DIR__);
define("CONFIG_DIR", __DIR__ . "/config");
define("ENGINE_DIR", __DIR__ . "/engine");
define("TEMPLATES_DIR", __DIR__ . "/templates");
require_once __DIR__ . "/engine/autoloader.php";
Application\Application::run(  );

Application\Application::run();
$a;
?>