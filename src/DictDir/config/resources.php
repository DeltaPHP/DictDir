<?php
use DeltaCore\Application;

return [
    //TODO "fields" => ["id", "name", "country"] переделать в  "country" => ["dict"=>"countries"]
    'directoryFactory' => function ($c) {
        $factory = new \DictDir\Model\DirectoryFactory();

        return $factory;
    }
];
