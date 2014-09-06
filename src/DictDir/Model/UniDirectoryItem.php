<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model;

use DeltaDb\VariableEntity;

class UniDirectoryItem extends VariableEntity
{
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->setField("id", (integer)$id);
    }

    function __toString()
    {
        return $this->name;
    }


} 