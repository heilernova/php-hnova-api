<?php

namespace HNova\Api\Settings\Db;

class DbInfo{
    public function __construct(
        public string $name,
        public string $type,
        public object $dataConnection
    ){}
}