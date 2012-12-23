<?php

class Admin_Model_Student {

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
            $this->setDbTable('Admin_Model_DbTable_Student');
        }
        return $this->_dbTable;
    }

    public function getAll() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        return $result->toArray();
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

    public function getKeysAndValues() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        $options = array('' => '--Select--');
        foreach ($result as $result) {
            $options[$result['student_id']] = $result['full_name'];
        }
        return $options;
    }

    public function getDetailById($id) {
        $row = $this->getDbTable()->fetchRow("student_id='$id'");
        if (!$row) {
            throw new Exception("Couldn't fetch such data");
        }
        return $row->toArray();
    }

    public function update($formData, $id) {
        $this->getDbTable()->update($formData, "student_id='$id'");
    }

    public function delete($id) {
        $data["del"] = "Y";
        try {
            $this->getDbTable()->update($data, "student_id='$id'");
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function listAll() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        return $result->toArray();
    }

    public function search($data) {
        $where = "s.del='N' ";
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if ($val == "All" && $key == "section") {
                    continue;
                }
                if ($val) {
                    $where .=" AND s.$key='$val'";
                }
            }
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array("s" => "school_students"), array("s.*"))
                ->where($where)
                ->order("s.roll_no");
        $results = $db->fetchAll($select);
        return $results;
    }

    public function getStudentNames($data = null) {
        $where = '';
        if ($data) {
            foreach ($data as $key => $val) {
                $where .= "s." . $key . "='" . $val . "' AND ";
            }
        }
        $where .=1;
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array("s" => "school_students"), array("s.*"))
                ->where($where)
                ->order("s.roll_no");
        $results = $db->fetchAll($select);
        $options = array('' => '--Select--');
        foreach ($results as $result) {
            $options[$result['roll_no'] . "::" . $result['student_id']] = $result['full_name'];
        }
        return $options;
    }

    public function searchAllNames() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array("s" => "school_students"), array("s.*"))
                ->where("s.del='N'")
                ->order("s.roll_no");
        $results = $db->fetchAll($select);
        $options = array('' => '--Select--');
        foreach ($results as $result) {
            $options[$result['student_id']] = $result['full_name'];
        }
        return $options;
    }

    public function upgrade($students, $data) {
        foreach ($students as $key => $val) {
            $data['roll_no'] = $val;
            $this->getDbTable()->update($data, "student_id=" . $key);
        }
    }

    public function upgradeRollNo($students) {
        foreach ($students as $key => $val) {
            $data['roll_no'] = $val;
            $this->getDbTable()->update($data, "student_id=" . $key);
        }
    }

}

?>
