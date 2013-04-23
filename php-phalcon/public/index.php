<?php

try {
    // Load the config
    $config = include(__DIR__."/../app/config/config.php");

    // Register an autoloader
    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array(
        $config->application->controllersDir,
        $config->application->modelsDir
    ))->register();

    // Create a DI
    $di = new Phalcon\DI\FactoryDefault();

    // Setting up the router
    $di->set('router', function() use ($config) {
        return include($config->application->routes);
    });

    // Setting up the view component (seems to be required even when not used)
    $di->set('view', function() use ($config) {
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir($config->application->viewsDir);
        return $view;
    });

    // Setting up the database connection
    $di->set('db', function() use ($config) {
        return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            "host"     => $config->database->host,
            "username" => $config->database->username,
            "password" => $config->database->password,
            "dbname"   => $config->database->name
        ));
    });

    // Handle the request
    $application = new \Phalcon\Mvc\Application();
    $application->setDI($di);
    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
    echo "PhalconException: ", $e->getMessage();
}