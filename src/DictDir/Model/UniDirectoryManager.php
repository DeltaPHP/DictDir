<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model;


use DeltaDb\Repository;

class UniDirectoryManager extends Repository
{
    protected $metaInfo = [
        'table' => [
            'class'  => '\\DictDir\\Model\\UniDirectoryItem',
            'id'     => 'id',
            'fields' => [
                'id',
                'name',
            ]
        ]
    ];

    protected $table = "table";
    protected $entityClass = "\\DictDir\\Model\\UniDirectoryItem";

    public function setTable($table)
    {
        $meta = $this->metaInfo;
        if (!isset($meta[$this->table])) {
            throw new \Exception("Old table {$this->table} not defined");
        }
        $meta[$table] = $meta[$this->table];
        unset($meta[$this->table]);
        $this->metaInfo = $meta;

        $this->table = $table;
    }

    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->metaInfo[$this->table]["class"] = $entityClass;
        $this->entityClass = $entityClass;
    }

    public function setFields(array $fields)
    {
        $this->metaInfo[$this->table]["fields"] = $fields;
    }
}