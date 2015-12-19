<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model;


use DeltaDb\EntityInterface;

class ComboDirectoryManager extends UniDirectoryManager
{
    protected $metaInfo = [
        'class' => '\\DictDir\\Model\\ComboDirectoryItem',
        'fields' => [
            'id',
            'name',
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
        $entity = parent::create();
        $entity->setComboManager($this);
        $entity->setFieldList($this->getFieldsList());
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

    public function reserve(EntityInterface $entity)
    {
        $data = parent::reserve($entity);
        foreach($data["fields"] as $name => $value)
            if ($value instanceof EntityInterface) {
                $data["fields"][$name] = $value->getId();
            }
        return $data;
    }
}
