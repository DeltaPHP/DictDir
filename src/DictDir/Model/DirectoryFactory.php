<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model;


use DeltaCore\Parts\Configurable;
use DeltaDb\Repository;
use DeltaUtils\Parts\InnerCache;

/**
 * Class DirectoryFactory
 * Создает для заданных таблиц объекты менеджеров на основе Uni|Combo DirectoryManager
 * @package DictDir\Model
 */
class DirectoryFactory
{
    use Configurable;
    use InnerCache;

    protected $managers = [];

    protected $tables = [];

    function __construct(array $tables = [])
    {
        foreach ($tables as $key => $value) {
            $this->addTable($key, $value);
        }
    }

    public function addTable($table, $value = null)
    {
        if (!isset($value)) {
            $this->tables[$table] = ["id", "name"];
        } else {
            if (is_string($table)) {
                $this->tables[$table] = $value;
            } else {
                $this->tables[$value] = ["id", "name"];
            }
        }
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

    /**
     * @param $table
     * @return ComboDirectoryManager|UniDirectoryManager|Repository|null
     * @throws \Exception
     */
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
                $childManager = is_callable($table) ? call_user_func($table) : $this->getManager($table);
                $manager->addDictManager($field, $childManager);
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

    public function getDirectoryNames()
    {
        return array_keys($this->tables);
    }
}
