<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Controller;


use DeltaCore\AbstractController;
use DictDir\Model\ComboDirectoryManager;
use DictDir\Model\DirectoryFactory;
use DictDir\Model\UniDirectoryManager;
use DeltaDb\EntityInterface;

class DirectoryController extends AbstractController
{
    /**
     * @return DirectoryFactory
     */
    public function getDirFactory()
    {
        $app = $this->getApplication();
        $df = $app["directoryFactory"];
        return $df;
    }

    public function init()
    {
        $urls = $this->getConfig([$this->getModuleName(), "urls"]);
        $this->getView()->assignArray($urls->toArray());
    }

    public function getCurrentTable($default = null)
    {
        return $this->getRequest()->getUriPartByNum(4, $default);
    }

    public function getListUrl()
    {
        return   $this->getConfig([$this->getModuleName(), "urls", "listUrl"]);
    }

    public function indexAction()
    {
        $url = $this->getListUrl();
        $this->autoRenderOff();
        $this->getResponse()->redirect("{$url}");
    }

    public function listAction()
    {
        $this->getView()->assign("pageTitle", "Directory list");
        $tables = $this->getDirFactory()->getTables();
        if (empty($tables)) {
           throw new \Exception("Empty tables in DictDir");
        }
        $tables = array_keys($tables);
        $this->getView()->assign("tables", $tables);

        $table = $this->getCurrentTable($tables[0]);
        $this->getView()->assign("currentTable", $table);
        $manager = $this->getDirFactory()->getManager($table);
        $items = $manager->find();
        $fields = $manager->getFieldsList($manager->getTableName());
        $this->getView()->assign("fields", $fields);
        $this->getView()->assign("items", $items);
    }

    public function getDictFields(UniDirectoryManager $manager)
    {
        return $manager instanceof ComboDirectoryManager ? $manager->getDictFieldsList() : [];
    }

    public function getFields(UniDirectoryManager $manager)
    {
        $fields = $manager->getFieldsList($manager->getTableName());
        $dictFields = array_flip($this->getDictFields($manager));

        $newFields = [];
        foreach ($fields as $field) {
            $nField = [
                "name" => $field,
                "dict" => isset($dictFields[$field]),
            ];
            $newFields[$field] = $nField;
        }
        return $newFields;
    }

    public function itemToArray(UniDirectoryManager $manager, EntityInterface $item = null)
    {
        $fields = $this->getFields($manager);
        foreach ($fields as $field) {
            $fieldName = $field["name"];
            $fieldDict = $field["dict"];
            $fieldValue = $manager->getField($item, $fieldName);
            if (!$fieldDict || !($manager instanceof ComboDirectoryManager)) {
                $showItem[$fieldName] = $fieldValue;
            } else {
                $options = [];
                $optionVars = $manager->getFieldVariants($fieldName);
                foreach ($optionVars as $oVar) {
                    $oId = $oVar->getId();
                    $options[] = [
                        "id"     => $oId,
                        "name"   => $oVar->getName(),
                        "active" => $oId == $fieldValue,
                    ];
                }
                $showItem[$fieldName] = $options;
            }
        }
        return $showItem;
    }

    public function getVarFields(UniDirectoryManager $manager)
    {
        $fields = $this->getFields($manager);
        $showItem = [];
        foreach ($fields as $field) {
            $fieldName = $field["name"];
            $fieldDict = $field["dict"];
            if ($fieldDict) {
                $options = [];
                $optionVars = $manager->getFieldVariants($fieldName);
                foreach ($optionVars as $oVar) {
                    $oId = $oVar->getId();
                    $options[] = [
                        "id"   => $oId,
                        "name" => $oVar->getName(),
                    ];
                }
                $showItem[$fieldName] = $options;
            }
        }
        return $showItem;
    }

    public function addAction()
    {
        $this->setViewTemplate("edit");
        $request = $this->getRequest();

        $table = $this->getCurrentTable();
        if (is_null($table)) {
            throw new \Exception("Dict table not defined");
        }
        $this->getView()->assign("currentTable", $table);
        $manager = $this->getDirFactory()->getManager($table);

        if ($request->isGet()) {
            $fields = $this->getFields($manager);
            unset($fields["id"]);
            $this->getView()->assign("fields", $fields);
            $item = $this->getVarFields($manager);
            $this->getView()->assign("item", $item);
            return;
        }
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $table = $this->getCurrentTable();
        if (is_null($table)) {
            throw new \Exception("Dict table not defined");
        }
        $this->getView()->assign("currentTable", $table);
        $this->getView()->assign("pageTitle", $table);
        $manager = $this->getDirFactory()->getManager($table);

        $id = $request->getUriPartByNum(5);
        $this->getView()->assign("id", $id);
        $item = $manager->findById($id);
        if (!$item) {
            throw new \Exception("Item from $table with id #$id not found");
        }
        $fields = $this->getFields($manager);
        unset($fields["id"]);
        $this->getView()->assign("fields", $fields);
        $this->getView()->assign("item", $this->itemToArray($manager, $item));
        return;
    }

    public function saveAction()
    {
        $request = $this->getRequest();
        $table = $this->getCurrentTable();
        $id = $request->getParam("id");
        $manager = $this->getDirFactory()->getManager($table);
        $item = $id ? $manager->findById($id) : $manager->create();
        $postData = $request->getParams();
        unset($postData["id"]);
        $manager->load($item, $postData);
        $manager->save($item);
        $url = $this->getListUrl();
        $this->getResponse()->redirect("{$url}/$table");
    }

    public function rmAction()
    {
        $request = $this->getRequest();
        $table = $this->getCurrentTable();
        if (is_null($table)) {
            throw new \Exception("Dict table not defined");
        }
        $this->getView()->assign("currentTable", $table);
        $this->getView()->assign("pageTitle", $table);
        $manager = $this->getDirFactory()->getManager($table);

        $id = $request->getUriPartByNum(5);
        $item = $manager->findById($id);
        if (!$item) {
            throw new \Exception("Item from $table with id #$id not found");
        }
        $manager->delete($item);

        $this->autoRenderOff();
        $url = $this->getListUrl();
        $this->getResponse()->redirect("{$url}/$table");
    }

} 