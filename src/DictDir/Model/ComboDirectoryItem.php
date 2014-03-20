<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model;


use DeltaDb\EntityInterface;
use DeltaDb\VariableEntity;

class ComboDirectoryItem extends VariableEntity implements EntityInterface
{
    /**
     * @var ComboDirectoryManager
     */
    protected $comboManager;

    public function setComboManager($comboManager)
    {
        $this->comboManager = $comboManager;
    }

    public function getComboManager()
    {
        return $this->comboManager;
    }

    public function getFieldVariants($field)
    {
        return $this->getComboManager()->getFieldVariants($field);
    }

    public function getFieldTitle($field)
    {
        $fieldValue = $this->getComboManager()->getField($this, $field);
        return $this->getComboManager()->getFieldValue($field, $fieldValue);
    }



} 