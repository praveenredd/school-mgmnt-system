<?php

class Admin_Model_Examtype {

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
            $this->setDbTable('Admin_Model_DbTable_Examtype');
        }
        return $this->_dbTable;
    }

    public function getAll() {
        $result = $this->getDbTable()->fetchAll();
        return $result->toArray();
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
        $row = $this->getDbTable()->fetchRow("examtype_id='$id'");
        if (!$row) {
            throw new Exception("Couldn't fetch such data");
        }
        return $row->toArray();
    }

    public function update($formData, $id) {
        $this->getDbTable()->update($formData, "examtype_id='$id'");
    }

    public function delete($id) {
        $data["del"] = "Y";
        try {
            $this->getDbTable()->update($data, "examtype_id='$id'");
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function listAll() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        return $result->toArray();
    }

    public function getexamType() {
        $result = $this->getDbTable()->fetchAll("del='N'");
        $options = array('' => '--Select--');
        foreach ($result as $result) {
            $options[$result['examtype_id']] = $result['name'];
        }
        return $options;
    }

    public function getSelectedExamType($grade) {
        $where = "s.del = 'N'";
        if ($grade) {
            $where .= " AND s.grade='$grade'";
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array("s" => "school_examtype"), array("s.name", "s.examtype_id"))
                ->where($where);
        $results = $db->fetchAll($select);
        $options = array();
        foreach ($results as $result) {
            $options[$result['examtype_id']] = $result['name'];
        }
        return $options;
    }

}

?>
