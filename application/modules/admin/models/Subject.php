<?php

class Admin_Model_Subject {

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
            $this->setDbTable('Admin_Model_DbTable_Subject');
        }
        return $this->_dbTable;
    }

    public function getAll() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        return $result->toArray();
    }

    public function add($formData) {
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
            $options[$result['subject_id']] = $result['name'];
        }
        return $options;
    }

    public function getDetailById($id) {
        $row = $this->getDbTable()->fetchRow("subject_id='$id'");

        if (!$row) {
            throw new Exception("Couldn't fetch such data");
        }
        return $row->toArray();
    }

    public function update($formData, $id) {
        $this->getDbTable()->update($formData, "subject_id='$id'");
    }

    public function delete($id) {
        $data["del"] = "Y";
        try {
            $this->getDbTable()->update($data, "subject_id='$id'");
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function listAll() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        return $result->toArray();
    }

    public function getSubjects($grade) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array("s" => "school_subjects"), array("s.name", "s.subject_id"))
                ->where("s.grade ='$grade' AND s.del='N'");
        $results = $db->fetchAll($select);
        $options = array();
        foreach ($results as $result) {
            $options[$result['subject_id']] = $result['name'];
        }
        return $options;
    }

    public function getonlySubjects($grade) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array("s" => "school_subjects"), array("s.name"))
                ->where("s.grade ='$grade' AND s.del='N'");
        $results = $db->fetchAll($select);
        return $results;
    }

}

?>
