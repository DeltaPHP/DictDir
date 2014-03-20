<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Model;


use DeltaDb\AbstractEntity;

class UniDirectoryItem extends AbstractEntity
{
    protected $id;
    protected $name;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = (integer) $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


} 