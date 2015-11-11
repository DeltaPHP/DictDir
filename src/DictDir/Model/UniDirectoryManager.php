<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model;


use DeltaDb\Repository;

class UniDirectoryManager extends Repository
{
    protected $metaInfo = [
        'class' => '\\DictDir\\Model\\UniDirectoryItem',
        'fields' => [
            'id',
            'name',
        ]
    ];

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->metaInfo["class"] = $entityClass;
    }

    public function setFields(array $fields)
    {
        $this->metaInfo["fields"] = $fields;
    }

    public function create(array $data = null)
    {
        /** @var ComboDirectoryItem $entity */
        $entity = parent::create();
        $entity->setFieldList($this->getFieldsList());
        if (!is_null($data)) {
            $this->load($entity, $data);
        }
        return $entity;
    }
}
