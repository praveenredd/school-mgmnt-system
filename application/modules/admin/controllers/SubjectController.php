<?php

class Admin_SubjectController extends Zend_Controller_Action {

    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('subject-filter', 'json')
                ->addActionContext('delete', 'json')
                ->initContext();
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    public function indexAction() {
        $subjectModel = new Admin_Model_Subject();
        $this->view->result = $subjectModel->getAll();
    }

    public function addAction() {
        $form = new Admin_Form_SubjectForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                unset($formData["subject_id"]);
                try {
                    $subjectModel = new Admin_Model_Subject();
                    $subjectModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Subject added"));
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_SubjectForm();
        $form->submit->setLabel("Save");
        $subjectModel = new Admin_Model_Subject();
        $id = $this->_getParam('id', 0);
        $data = $subjectModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['subject_id'];
                    unset($formData['subject_id']);
                    unset($formData['submit']);
                    $subjectModel->update($formData, $id);
                    $this->_helper->FlashMessenger->addMessage(array("edit" => "Successfully Subject edited"));
                    $this->_helper->redirector('index');
                }
            }
        } catch (Exception $e) {
            $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
        }
    }

    public function deleteAction() {
        try {
            $id = $this->_getParam('id', 0);
            $subjectModel = new Admin_Model_Subject();
            $subjectModel->delete($id);
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
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
        $editColumn->setPosition('right')->setName('Edit')->setDecorator("<a href=\"$baseUrl/admin/subject/edit/id/{{subject_id}}\">Edit</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{subject_id}}\"/>");
        $deleteColumn = new Bvb_Grid_Extra_Column();
        $deleteColumn->setPosition('right')->setName('Delete')->setDecorator("<a class=\"delete-data\" href=\"$baseUrl/admin/subject/delete/id/{{subject_id}}\">Delete</a>");
        $grid->addExtraColumns($editColumn, $deleteColumn);
        $grid->updateColumn('subject_id', array('hidden' => true));
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
        $menuModel = new Admin_Model_Subject();
        $allMenus = $menuModel->listAll();
        $i = 1;
        foreach ($allMenus as $menu):
            $data = array();
            $data['subject_id'] = $menu['subject_id'];
            $data['grade'] = $menu['grade'];
            $data['name'] = $menu['name'];
            $data['short_name'] = $menu['short_name'];
            $menus[] = $data;
        endforeach;
        return $menus;
    }

}
