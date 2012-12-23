<?php

class Admin_ExamtypeController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    public function indexAction() {
        $examtypeModel = new Admin_Model_Examtype();
        $this->view->result = $examtypeModel->getAll();
    }

    public function addAction() {
        $form = new Admin_Form_ExamtypeForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                unset($formData["examtype_id"]);
                try {
                    $examtypeModel = new Admin_Model_Examtype();
                    $examTypeId = $examtypeModel->add($formData);
                    if ($examTypeId) {
                        $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully added Exam type "));
                        $this->_helper->redirector('list');
                    }
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_ExamtypeForm(true);
        $form->submit->setLabel("Save");
        $examtypeModel = new Admin_Model_Examtype();
        $id = $this->_getParam('id', 0);
        $data = $examtypeModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['examtype_id'];
                    unset($formData['examtype_id']);
                    unset($formData['submit']);
                    $examtypeModel->update($formData, $id);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully updated Exam type."));
                    $this->_helper->redirector('list');
                }
            }
        } catch (Exception $e) {
            $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
        }
    }

    public function deleteAction() {
        $id = $this->_getParam('id', 0);
        $examtypeModel = new Admin_Model_Examtype();
        $this->view->id = $id;
        if ($this->getRequest()->isPost()) {
            try {
                $delete = $this->_getParam('delete');
                if ('Yes' == $delete) {
                    $examtypeModel->delete($id);
                }$this->_helper->redirector("list");
            } catch (Exception $e) {
                $this->view->message = $e->getMessage();
            }
        }
    }

    public function listAction() {
        $config = new Zend_Config_Ini(BASE_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "grid.ini", 'production');
        $grid = Bvb_Grid::factory('Table', $config);
        $data = $this->_listdata();
        $source = new Bvb_Grid_Source_Array($data);
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $grid->setSource($source);
        $grid->setImagesUrl("$baseUrl/grid/");
        $editColumn = new Bvb_Grid_Extra_Column();
        $editColumn->setPosition('right')->setName('Edit')->setDecorator("<a href=\"$baseUrl/admin/examtype/edit/id/{{examtype_id}}\">Edit</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{examtype_id}}\"/>");
        $deleteColumn = new Bvb_Grid_Extra_Column();
        $deleteColumn->setPosition('right')->setName('Delete')->setDecorator("<a class=\"delete-data\" href=\"$baseUrl/admin/examtype/delete/id/{{examtype_id}}\">Delete</a>");
        $grid->addExtraColumns($editColumn, $deleteColumn);
        $grid->updateColumn('examtype_id', array('hidden' => true));
        $grid->updateColumn('del', array('hidden' => true));
        $grid->setRecordsPerPage(20);
        $grid->setPaginationInterval(array(
            5 => 5,
            10 => 10,
            20 => 20,
            30 => 30,
            40 => 40,
            50 => 50,
            100 => 100
        ));
        $grid->setExport(array('print', 'word', 'csv', 'excel', 'pdf'));
        $this->view->grid = $grid->deploy();
    }

    public function _listdata() {
        $menus = array();
        $menuModel = new Admin_Model_Examtype();
        $allMenus = $menuModel->listAll();

        foreach ($allMenus as $menu):
            $data = array();
            $data['examtype_id'] = $menu['examtype_id'];
            $data['name'] = $menu['name'];
            $data['grade'] = $menu['grade'];
            $data['full_marks'] = $menu['full_marks'];
            $data['pass_marks'] = $menu['pass_marks'];
            $menus[] = $data;
        endforeach;
        return $menus;
    }

}
?>

