<?php

class Admin_Model_Staffsalary {

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
            $this->setDbTable('Admin_Model_DbTable_Staffsalary');
        }
        return $this->_dbTable;
    }

    public function getAll() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array("ur" => "school_staff_salary"), array("ur.*"))
                ->joinLeft(array("p" => "school_staff"), "ur.staff_id=p.staff_id", array("p.full_name as staff_name"))
                ->where("ur.del='N'");
        $result = $db->fetchAll($select);
        return $result;
    }

    public function add($formData) {
        $formData['entered_date'] = date("Y-m-d");
        $lastId = $this->getDbTable()->insert($formData);
        var_dump($formData);
        if (!$lastId) {
            throw new Exception("Couldn't insert data into database");
        }
        return $lastId;
    }

    public function getDetailById($id) {
        $row = $this->getDbTable()->fetchRow("salary_id='$id'");
        if (!$row) {
            throw new Exception("Couldn't fetch such data");
        }
        return $row->toArray();
    }

    public function getAllByYear($year) {
        $db = $this->getDbTable()->getDefaultAdapter();
        $select = $this->getDbTable()->select();
        $select->where("year='$year'")->order("isMonthly DESC");
        $result = $db->fetchAll($select);
        return $result;
    }

    public function update($formData, $id) {
        $this->getDbTable()->update($formData, "salary_id='$id'");
    }

    public function delete($id) {
        $data["del"] = "Y";
        try {
            $this->getDbTable()->update($data, "salary_id='$id'");
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function listAll() {
        $db = $this->getDbTable()->getDefaultAdapter();
        $select = $db->select();
        $select->from(array("ss" => "school_staff_salary"), array("ss.*"))
                ->joinLeft(array("st" => "school_staff"), "st.staff_id=ss.staff_id", array("st.full_name as staff_name"))
                ->where("ss.del='N'");
        $results = $db->fetchAll($select);
        return $results;
    }

}

?>
