<?php

class Admin_Model_Feetype {

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
            $this->setDbTable('Admin_Model_DbTable_Feetype');
        }
        return $this->_dbTable;
    }

    public function getAll() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array("ur" => "school_fee_type"), array("ur.*"));
        $result = $db->fetchAll($select);
        return $result;
    }

    public function add($formData) {
        $db = $this->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        $grade = $formData['grade'];
        array_shift($grade);
        unset($formData['grade']);
        foreach ($grade as $class) {
            $formData['grade'] = $class;
            $lastId = $this->getDbTable()->insert($formData);
        }
        if ($lastId) {
            $db->commit();
        } else {
            $db->rollback();
            throw new Exception("Couldn't insert data into database");
        }
        return $lastId;
    }

    public function getDetailById($id) {
        $row = $this->getDbTable()->fetchRow("fee_type_id='$id'");
        if (!$row) {
            throw new Exception("Couldn't fetch such data");
        }
        return $row->toArray();
    }

    public function update($formData, $id) {
        $this->getDbTable()->update($formData, "fee_type_id='$id'");
    }

    public function delete($id) {
        $this->getDbTable()->delete("fee_type_id='$id'");
    }

    public function getAllByGrades($grade, $year) {
        $db = $this->getDbTable()->getDefaultAdapter();
        $select = $this->getDbTable()->select();
        $select->where("grade='$grade' AND year='$year'")->order("isMonthly DESC");
        $result = $db->fetchAll($select);
        return $result;
    }

    public function listAll() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array("ur" => "school_fee_type"), array("ur.*"));
        $result = $db->fetchAll($select);
        return $result;
    }

}

?>
