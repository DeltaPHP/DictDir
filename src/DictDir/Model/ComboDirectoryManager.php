<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model;


use DeltaDb\Repository;

class ComboDirectoryManager extends UniDirectoryManager
{
    protected $metaInfo = [
        'table' => [
            'class'  => '\\DictDir\\Model\\ComboDirectoryItem',
            'id'     => 'id',
            'fields' => [
                'id',
                'name',
            ]
        ]
    ];

    /**
     * @var UniDirectoryManager[]
     */
    protected $dictManagers = [];

    public function addDictManager($field, $manager)
    {
        $this->dictManagers[$field] = $manager;
    }

    public function getDictManager($field)
    {
        if (!isset($this->dictManagers[$field])) {
            return null;
        }
        return $this->dictManagers[$field];
    }

    public function create(array $data = null, $entityClass = null)
    {
        /** @var ComboDirectoryItem $entity */
        $entity = parent::create(null, $entityClass);
        $entity->setComboManager($this);
        $table = $this->getTableName($entityClass);
        $entity->setFieldList($this->getFieldsList($table));
        if (!is_null($data)) {
            $this->load($entity, $data);
        }
        return $entity;
    }

    /**
     * @param $field
     * @param $value
     * @return null
     * @deprecated
     */
    public function getFieldValue($field, $value)
    {
        $dm = $this->getDictManager($field);
        $fieldInfo = $dm->findById($value);
        if(is_callable([$fieldInfo, "getName"])) {
            return $fieldInfo->getName();
        }
        return $fieldInfo;
    }

    public function getDictField($field, $value)
    {
        $dm = $this->getDictManager($field);
        $item = $dm->findById($value);
        return $item;
    }

    /**
     * @param $field
     * @return UniDirectoryItem[]|ComboDirectoryItem[]
     */
    public function getFieldVariants($field)
    {
        $dm = $this->getDictManager($field);
        $vars = $dm->find();
        return $vars;
    }

    public function getDictFieldsList()
    {
        return array_keys($this->dictManagers);
    }

    public function isDictField($field)
    {
        return isset($this->dictManagers[$field]);
    }



} 