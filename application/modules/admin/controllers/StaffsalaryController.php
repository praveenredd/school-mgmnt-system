<?php

class Admin_StaffsalaryController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }
    
    public function indexAction() {
        $salaryModel = new Admin_Model_Staffsalary();
        $this->view->result = $salaryModel->getAll();
    }

    public function addAction() {
        $form = new Admin_Form_StaffsalaryForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                unset($formData["salary_id"]);
                try {
                    $salaryModel = new Admin_Model_Staffsalary();
                    $salaryModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Salary added"));
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_StaffsalaryForm();
        $form->submit->setLabel("Save");
        $salaryModel = new Admin_Model_Staffsalary();
        $id = $this->_getParam('id', 0);
        $data = $salaryModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['salary_id'];
                    unset($formData['salary_id']);
                    unset($formData['submit']);

                    $salaryModel->update($formData, $id);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Salary edited"));
                    $this->_helper->redirector('index');
                }
            }
        } catch (Exception $e) {
            $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
        }
    }

    public function deleteAction() {
        $id = $this->_getParam('id', 0);
        $salaryModel = new Admin_Model_Staffsalary();
        $this->view->id = $id;
        if ($this->getRequest()->isPost()) {
            try {
                $delete = $this->_getParam('delete');
                if ('Yes' == $delete) {
                    $salaryModel->delete($id);
                }$this->_helper->redirector("index");
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
        $editColumn->setPosition('right')->setName('Edit')->setDecorator("<a href=\"$baseUrl/admin/staffsalary/edit/id/{{salary_id}}\">Edit</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{salary_id}}\"/>");
        $deleteColumn = new Bvb_Grid_Extra_Column();
        $deleteColumn->setPosition('right')->setName('Delete')->setDecorator("<a class=\"delete-data\" href=\"$baseUrl/admin/staffsalary/delete/id/{{salary_id}}\">Delete</a>");
        $grid->addExtraColumns($editColumn, $deleteColumn);
        $grid->updateColumn('salary_id', array('hidden' => true));
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
        $menuModel = new Admin_Model_Staffsalary();
        $allMenus = $menuModel->listAll();

        foreach ($allMenus as $menu):
            $data = array();
            $data['salary_id'] = $menu['salary_id'];
            $data['staff_name'] = $menu['staff_name'];
            $data['month'] = $menu['month'];
            $data['amount'] = $menu['amount'];
            $data['due'] = $menu['due'];
            $menus[] = $data;
        endforeach;
        return $menus;
    }
    
    public function detailAction() {
        $id = $this->_getParam('id', 0);
        $staffModel = new Admin_Model_Staff();
        $data = $staffModel->getDetailById($id);
        $salaryModel = new Admin_Model_Staffsalary();
        $this->view->salarytypes = $salaryModel->getAllByYear($data['year']);
        $this->view->result = $data;
        $config = new Zend_Config_Ini(BASE_PATH.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'month.ini','nepali');
        $this->view->monthOptions = $config->month->toArray();
    }

}

