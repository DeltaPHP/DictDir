<?php

return [
    'directoryFactory' => function (\DeltaCore\Prototype\ConfigInterface $c) {
        $factory = new \DictDir\Model\DirectoryFactory();
        $factory->setConfig($c->getConfig(["DictDir"]));
        return $factory;
    }
];
