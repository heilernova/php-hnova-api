<?php

use HNova\Api\Classes\ApiJsonClass;

$json = json_decode(file_get_contents("api.json"));
$d = new ApiJsonClass($json);

// echo json_encode($d->user->username , 128);