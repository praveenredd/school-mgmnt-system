<?php

class Admin_Model_StudentAttendance {

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
            $this->setDbTable('Admin_Model_DbTable_StudentAttendance');
        }
        return $this->_dbTable;
    }

    public function getAll() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        return $result->toArray();
    }

    public function add($formData) {
        $data = $formData['student'];
        $arr = array();
        $db = $this->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        foreach ($data as $key => $row) {
            $arr['student_id'] = $key;
            $arr['entered_by'] = Zend_Auth::getInstance()->getIdentity()->staff_id;
            $arr['date'] = $formData['date'];
            $arr['entered_date'] = date("Y-m-d");
            $lastId = $this->getDbTable()->insert($arr);
        }
        if ($lastId) {
            $db->commit();
        } else {
            $db->rollback();
            throw new Exception("Couldn't insert data into database");
        }
        return $lastId;
    }

    public function getKeysAndValues() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        $options = array('' => '--Select--');
        foreach ($result as $result) {
            $options[$result['staff_id']] = $result['full_name'];
        }
        return $options;
    }

    public function getDetailById($id) {
        $row = $this->getDbTable()->fetchRow("staff_id='$id'");
        if (!$row) {
            throw new Exception("Couldn't fetch such data");
        }
        return $row->toArray();
    }

    public function update($formData, $id) {
        $this->getDbTable()->update($formData, "staff_id='$id'");
    }

    public function delete($id) {
        $data["del"] = "Y";
        $this->getDbTable()->update($data, "staff_id='$id'");
    }

    public function listAll() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        return $result->toArray();
    }

}

?>
