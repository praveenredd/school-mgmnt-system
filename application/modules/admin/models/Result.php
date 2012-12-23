<?php

class Admin_Model_Result {

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
            $this->setDbTable('Admin_Model_DbTable_Result');
        }
        return $this->_dbTable;
    }

    public function getAll() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array("ur" => "school_result"), array("ur.*"))
                ->joinLeft(array("k" => "school_students"), "ur.student_id=k.student_id", array("k.full_name as result_student"))
                ->where("ur.del='N'");
        $results = $db->fetchAll($select);
        return $results;
    }

    public function add($formData) {
        $formData['entered_date'] = date("Y-m-d");
        $lastId = $this->getDbTable()->insert($formData);
        if (!$lastId) {
            throw new Exception("Couldn't insert data into database");
        }
        return $lastId;
    }

    public function getKeysAndValues() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        $options = array('' => '--Select--');
        foreach ($result as $result) {
            $options[$result['result_id']] = $result['student_id'];
        }
        return $options;
    }

    public function getDetailById($id) {
        $row = $this->getDbTable()->fetchRow("result_id='$id'");
        if (!$row) {
            throw new Exception("Couldn't fetch such data");
        }
        return $row->toArray();
    }

    public function update($formData, $id) {
        $this->getDbTable()->update($formData, "result_id='$id'");
    }

    public function delete($id) {
        $data["del"] = "Y";
        try {
            $this->getDbTable()->update($data, "result_id='$id'");
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function searchDetail($data) {
        $where = "s.del='N' ";
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if ($val) {
                    $where .=" AND s.$key='$val'";
                }
            }
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array("s" => "school_result"), array("s.*"))
                ->where($where);
        $results = $db->fetchAll($select);
        return $results;
    }

    public function searchAllNames() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array("s" => "school_students"), array("s.*"))
                ->where("s.del='N'");
        $results = $db->fetchAll($select);
        $options = array('' => '--Select--');
        foreach ($results as $result) {
            $options[$result['student_id']] = $result['full_name'];
        }
        return $options;
    }

    public function searchResultRollWise($formData) {
        $rollno = $formData['roll_no'];
         $year = $formData['year'];
        $grade = $formData['grade'];
        $examtype = $formData['examtype_id'];
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array("ur" => "school_result"), array("ur.*"))
                ->joinLeft(array("k" => "school_students"), "ur.student_id=k.student_id", array("k.full_name","k.roll_no"))
                ->joinLeft(array("s" => "school_subjects"), "ur.subject_id=s.subject_id", array("s.name as subject"))
                ->joinLeft(array("e" => "school_examtype"), "ur.examtype_id=e.examtype_id", array("e.*"))
                ->where("ur.del='N' AND k.roll_no='$rollno' AND ur.grade='$grade'AND ur.year='$year' AND e.examtype_id ='$examtype'");
        $results = $db->fetchAll($select);
               return $results;
    }

    public function searchAllResults($formData) {
        $year = $formData['year'];
        $grade = $formData['grade'];
        $examtype = $formData['examtype_id'];
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array("ur" => "school_result"), array("ur.*"))
                ->joinLeft(array("s" => "school_students"), "ur.student_id=s.student_id", array("s.full_name","s.roll_no"))
                ->joinLeft(array("k" => "school_subjects"), "ur.subject_id=k.subject_id", array("k.name as subject"))
                ->joinLeft(array("e" => "school_examtype"), "ur.examtype_id=e.examtype_id", array("e.*"))
                ->where("ur.del='N' AND ur.year='$year' AND ur.grade='$grade' AND e.examtype_id='$examtype'");
        $results = $db->fetchAll($select);
        return $results;
    }

}

?>
