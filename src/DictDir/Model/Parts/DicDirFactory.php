<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model\Parts;


trait DicDirFactory
{
    /**
     * @return \DeltaCore\Application
     */
    abstract public function getApplication();

    /**
     * @return \DictDir\Model\DirectoryFactory
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
