<?php

require __DIR__ . '/gearman/vendor/autoload.php';

class Gearmanclient {

    private $service;

    public function __construct() {
        $this->service = new \Kicken\Gearman\Client('127.0.0.1:4730');
    }

    public function doBackground($queue, $data) {
        $this->service->submitBackgroundJob($queue, json_encode($data));
    }

}
