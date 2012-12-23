<?php

class Admin_Model_Image {

    protected $_dbTable;

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Admin_Model_DbTable_Image');
        }
        return $this->_dbTable;
    }

    public function getAll() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array("ur" => "school_image"), array("ur.*"))
                ->joinLeft(array("k" => "school_gallery"), "ur.gallery_id=k.gallery_id", array("k.gallery_name as gallery_name"));
        $results = $db->fetchAll($select);
        return $results;
    }

    public function add($formData) {
        $lastId = $this->getDbTable()->insert($formData);
        var_dump($formData);
        if (!$lastId) {
            throw new Exception("Couldn't insert data into database");
        }
        return $lastId;
    }

    public function getDetailById($id) {
        $row = $this->getDbTable()->fetchRow("image_id='$id'");
        if (!$row) {
            throw new Exception("Couldn't fetch such data");
        }
        return $row->toArray();
    }

    public function update($formData, $id) {
        $this->getDbTable()->update($formData, "image_id='$id'");
    }

    public function delete($id) {
        $data["del"] = "Y";
        try {
            $this->getDbTable()->update($data, "image_id='$id'");
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function listAll() {
        $db = $this->getDbTable()->getDefaultAdapter();
        $select = $db->select();
        $select->from(array("i" => "school_image"), array("i.*"))
                ->joinLeft(array("g" => "school_gallery"), "g.gallery_id=i.gallery_id", array("g.gallery_name as gallery_name"));
        $results = $db->fetchAll($select);
        return $results;
    }

    public function fetchImage($id) {
        $db = $this->getDbTable()->getDefaultAdapter();
        $select = $db->select();
        $select->from(array("i" => "school_image"), array("i.*"))
                ->where($id);
        $results = $db->fetchAll($select);
        return $results;
    }

}

?>