<?php

require_once(__DIR__ . "/../vendor/autoload.php");
use dehydr8\Protobuf\Decoder;

$b = file_get_contents($argv[1]);
$d = new Decoder($b);
echo json_encode($d->decode(), JSON_PRETTY_PRINT);

?>