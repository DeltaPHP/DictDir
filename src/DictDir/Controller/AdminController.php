<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DictDir\Controller;


use DeltaCore\AbstractController;
use DeltaUtils\ArrayUtils;
use DictDir\Model\ComboDirectoryManager;
use DictDir\Model\DirectoryFactory;
use DictDir\Model\Parts\DicDirFactory;
use DictDir\Model\UniDirectoryManager;
use DeltaDb\EntityInterface;

class AdminController extends AbstractController
{
    use DicDirFactory;

    public function init()
    {
        parent::init();
        //init all managers
        $managersList = $this->getConfig(["DictDir", "managers"], [])->toArray();
        $disabled = array_filter($managersList, function($value, $key) {
            return !is_integer($key) && empty($value);
        }, ARRAY_FILTER_USE_BOTH);
        $managersList = array_diff(array_diff($managersList, $disabled), array_keys($disabled));

        foreach($managersList as $managerItem) {
            $this->getApplication()[$managerItem];
        }
    }

    public function listAction(array $params = [])
    {
        $this->getView()->assign("pageTitle", "Directory list");
        $tables = $this->getDirectoryFactory()->getTables();
        if (empty($tables)) {
           throw new \Exception("Empty tables in DictDir");
        }
        $tables = array_keys($tables);
        $this->getView()->assign("tables", $tables);

        $table = ArrayUtils::get($params, "table", $tables[0]);
        $this->getView()->assign("currentTable", $table);
        $manager = $this->getDirectoryManager($table);
        $items = $manager->find();
        $fields = $manager->getFieldsList($manager->getTable());
        $this->getView()->assign("fields", $fields);
        $this->getView()->assign("items", $items);
    }

    public function getDictFields(UniDirectoryManager $manager)
    {
        return $manager instanceof ComboDirectoryManager ? $manager->getDictFieldsList() : [];
    }

    public function getFields(UniDirectoryManager $manager)
    {
        $fields = $manager->getFieldsList($manager->getTable());
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
                    if ($fieldValue instanceof EntityInterface) {
                        $fieldValue = $fieldValue->getId();
                    }
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

    public function addAction(array $params = [])
    {
        $this->setViewTemplate("edit");
        $request = $this->getRequest();

        $table = ArrayUtils::get($params, "table");
        if (is_null($table)) {
            throw new \Exception("Dict table not defined");
        }
        $this->getView()->assign("currentTable", $table);
        $manager = $this->getDirectoryManager($table);

        if ($request->isGet()) {
            $fields = $this->getFields($manager);
            unset($fields["id"]);
            $this->getView()->assign("fields", $fields);
            $item = $this->getVarFields($manager);
            $this->getView()->assign("item", $item);
            return;
        }
    }

    public function editAction(array $params = [])
    {
        $table = ArrayUtils::get($params, "table");
        if (is_null($table)) {
            throw new \Exception("Dict table not defined");
        }
        $this->getView()->assign("currentTable", $table);
        $this->getView()->assign("pageTitle", $table);
        $manager = $this->getDirectoryManager($table);

        $id = ArrayUtils::get($params, "id");
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

    public function saveAction(array $params = [])
    {
        $request = $this->getRequest();
        $table = ArrayUtils::get($params, "table");
        $id = $request->getParam("id");
        $manager = $this->getDirectoryManager($table);
        $item = $id ? $manager->findById($id) : $manager->create();
        $postData = $request->getParams();
        unset($postData["id"]);
        $manager->load($item, $postData);
        $manager->save($item);
//        $url = $this->getListUrl();
        $this->getResponse()->redirect($this->getRouteUrl("dictdir_list", ["table" => $table]));
    }

    public function rmAction(array $params = [])
    {
        $table = ArrayUtils::get($params, "table");
        if (is_null($table)) {
            throw new \Exception("Dict table not defined");
        }
        $this->getView()->assign("currentTable", $table);
        $this->getView()->assign("pageTitle", $table);
        $manager = $this->getDirectoryManager($table);

        $id = ArrayUtils::get($params, "id");
        $item = $manager->findById($id);
        if (!$item) {
            throw new \Exception("Item from $table with id #$id not found");
        }
        $manager->delete($item);

        $this->autoRenderOff();
        $this->getResponse()->redirect($this->getRouteUrl("dictdir_list", ["table" => $table]));
    }

}
