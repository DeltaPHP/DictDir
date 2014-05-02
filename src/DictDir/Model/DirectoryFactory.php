<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model;


use DeltaUtils\Parts\InnerCache;

/**
 * Class DirectoryFactory
 * Создает для заданных таблиц объекты менеджеров на основе Uni|Combo DirectoryManager
 * @package DictDir\Model
 */
class DirectoryFactory
{
    use InnerCache;

    protected $managers = [];

    protected $tables = [];

    function __construct($tables)
    {
        foreach ($tables as $key => $value) {
            if (is_string($key)) {
                $this->tables[$key] = $value;
            } else {
                $this->tables[$value] = ["id", "name"];
            }
        }
    }

    public function addTable($table, $entityClass = null)
    {
        $this->tables[$table] = $entityClass;
    }

    /**
     * @param array $tables
     */
    public function setTables($tables)
    {
        $this->tables = $tables;
    }

    /**
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    public function getManager($table)
    {
        if (!isset($this->tables[$table])) {
            throw new \UnexpectedValueException("not defined table $table in DirFact");
        }
        $tableMeta = (array)$this->tables[$table];
        $entityClass = isset($tableMeta["class"]) ? $tableMeta["class"] : null;
        $cacheId = "managers|{$table}";

        if ($manager = $this->getInnerCache($cacheId)) {
            return $manager;
        }

        if (isset($tableMeta["dictList"])) {
            $dicts = $tableMeta["dictList"];
            $manager = new ComboDirectoryManager();
            $manager->setTable($table);
            foreach ($dicts as $field => $table) {
                $manager->addDictManager($field, $this->getManager($table));
            }
        } else {
            $manager = new UniDirectoryManager();
            $manager->setTable($table);
        }
        if (!is_null($entityClass)) {
            $manager->setEntityClass($entityClass);
        }
        if (isset($tableMeta["fields"]) && array($tableMeta["fields"])) {
            $manager->setFields($tableMeta["fields"]);
        }
        $this->setInnerCache($cacheId, $manager);
        return $manager;
    }

} 