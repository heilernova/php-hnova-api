<?php

use HNova\Api\Scripts\Script;

$v = Script::getEvent()->getComposer()->getConfig()->getConfigSource()->getName();

echo  $v . "\n";
$n =  strpos($v, 'htdocs');
echo $n . "\n";
echo dirname(substr($v, $n + 7)) . "\n";

// echo json_encode(basename(dirname($v)));