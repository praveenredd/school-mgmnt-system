<?php

class Admin_Model_Menu {

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
            $this->setDbTable('Admin_Model_DbTable_Menu');
        }
        return $this->_dbTable;
    }

    public function listAll() {
        $allResults = array();
        $db = $this->getDbTable()->getDefaultAdapter();
        $select = $db->select();
        $select->from(array("current" => "school_menu"), array("current.*"))
                ->joinLeft(array("parent" => "school_menu"), "current.parent_menu_id=parent.menu_id", array("parent.title as upper_menu"));
        $results = $db->fetchAll($select);
        return $results;
    }

    public function getAll() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array("m" => "school_menu"), array("m.*"))
                ->where("m.del='N'");
        $results = $db->fetchAll($select);
        return $results;
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
        $options = array('' => '--Select--', '0' => 'Main Menu');
        foreach ($result as $result) {
            $options[$result['menu_id']] = $result['title'];
        }
        return $options;
    }

    public function getDetailById($id) {
        $row = $this->getDbTable()->fetchRow("menu_id='$id'");
        if (!$row) {
            throw new Exception("Couldn't fetch such data");
        }
        return $row->toArray();
    }

    public function update($formData, $id) {
        $this->getDbTable()->update($formData, "menu_id='$id'");
    }

    public function delete($id) {
        $data["del"] = "Y";
        try {
            $this->getDbTable()->update($data, "menu_id='$id'");
        } catch (Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function changeStatus($element_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "SELECT if(sh = 'Y', 'N', 'Y' ) as sh FROM school_menu WHERE menu_id ='$element_id'";
        $row = $db->fetchRow($sql);
        $data = array('sh' => $row['sh']);
        $this->getDbTable()->update($data, 'menu_id = ' . $element_id);
        return $row['sh'];
    }

    public function fetchHierarchy() {
        $refs = array();
        $list = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array("c" => "school_menu"), array("c.*"))
                ->where("c.del='N' AND c.sh='Y' ");
        $results = $db->fetchAll($select);
        foreach ($results as $data) {
            $thisref = &$refs[$data['menu_id']];
            $thisref = $data;
            if ($data['parent_menu_id'] == 0) {
                $list[$data['menu_id']] = &$thisref;
            } else {
                $refs[$data['parent_menu_id']]['children'][$data['menu_id']] = &$thisref;
            }
        }
        return substr($this->listToUl($list), 20, -7);
    }

    public function listToUl($arr) {
        $html = "<ul class = sf-menu>" . PHP_EOL;
        foreach ($arr as $v) {
            if ($v['controller'] && $v['action']) {
                $url = "/" . $v['controller'] . '/' . $v['action'];
            } else {
                $title = str_replace(" ", "_", $v['title']);
                $url = "/index/content/display/" . $title . '-' . base64_encode(base64_encode(base64_encode($v['menu_id'] . '-' . $title)));
            }
            $url = Zend_Controller_Front::getInstance()->getBaseUrl() . $url;
            $html .= "<li class='sf-parent' ><a href=\"$url\">" . $v['title'] . "</a>";
            if (array_key_exists('children', $v)) {
                $html .= $this->listToUl($v['children']);
            }
            $html .= '</li>' . PHP_EOL;
        }
        $html .= '</ul>' . PHP_EOL;
        return $html;
    }

    public function searchContents($content) {
        $where = "c.del='N' AND c.sh='Y' AND ";
        if ($content['menu_type'] == 'content') {
            $where .= "c.title LIKE '%" . $content['keyword'] . "' OR c.content LIKE '%" . $content['keyword'] . "' AND c.menu_type='front'";
        } elseif ($content['menu_type'] == 'menu') {
            $where .= "c.title LIKE '%" . $content['keyword'] . "' OR c.controller LIKE '%" . $content['keyword'] . "' OR c.action LIKE '%" . $content['keyword'] . "' OR c.content LIKE '%" . $content['keyword'] . "' AND c.menu_type<>'front' AND c.menu_type<>'superUser'";
        }
       
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array("c" => "school_menu"), array("c.*"))
                ->where($where);
       // echo $select->__toString();exit;
        $results = $db->fetchAll($select);
        return $results;
    }

}

