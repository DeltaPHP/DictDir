<?php

return [
    'directoryFactory' => function (\DeltaCore\Config $c) {
        $factory = new \DictDir\Model\DirectoryFactory();
        $factory->setConfig($c->getConfig(["DictDir"]));
        return $factory;
    }
];
