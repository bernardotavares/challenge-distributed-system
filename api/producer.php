<?php

// Required Headers
header("Access-Control-Allow-Origin: http://localhost/challenge/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Producer Configurations
require '../vendor/autoload.php';
$config = \Kafka\ProducerConfig::getInstance();
$config->setMetadataRefreshIntervalMs(10000);
$config->setMetadataBrokerList('127.0.0.1:9092');
$config->setBrokerVersion('1.0.0');
$config->setRequiredAck(1);
$config->setIsAsyn(false);
$config->setProduceInterval(500);


// $topic = $data['topic'];
// $data = $_POST['myData'];


$producer = new \Kafka\Producer(
    function() {
        $data = json_decode($_POST['myData'], true);
        $topic = $data['topic'];

        return [
            [
                'topic' => $topic,
                'value' => $_POST['myData'],
                'key' => 'testkey',
            ],
        ];
    }
);
$producer->success(function($result) {
    // var_dump($result);
    echo json_encode($result);
});
$producer->error(function($errorCode) {
    echo json_encode($errorCode);
});
$producer->send(true);
?>