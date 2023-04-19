<?php

require __DIR__ . '/gearman/vendor/autoload.php';

class Gearmanworker {

    private $service;

    public function __construct() {
        $this->service = new \Kicken\Gearman\Worker('127.0.0.1:4730');
    }

    public function init($queue, $callback) {
        $this->service->registerFunction($queue, $callback);
    }

    public function work() {
        return $this->service->work();
    }

}
