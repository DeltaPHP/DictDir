<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model\Parts;


use DeltaCore\Application;
use DictDir\Model\DirectoryFactory;

trait DicDirFactory
{
    /**
     * @return Application
     */
    abstract public function getApplication();

    /**
     * @return DirectoryFactory
     */
    public function getDirectoryFactory()
    {
        return $this->getApplication()["directoryFactory"];
    }

    /**
     * @param $table
     * @return \DeltaDb\Repository|\DictDir\Model\ComboDirectoryManager|\DictDir\Model\UniDirectoryManager|null
     */
    public function getDirectoryManager($table)
    {
        return $this->getDirectoryFactory()->getManager($table);
    }
} 