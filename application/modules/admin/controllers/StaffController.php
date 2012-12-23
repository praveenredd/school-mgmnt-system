<?php

class Admin_StaffController extends Zend_Controller_Action {

    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('staff-filter', 'json')
                ->addActionContext('delete', 'json')
                ->initContext();
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    public function indexAction() {
        $staffModel = new Admin_Model_Staff();
        $this->view->result = $staffModel->getAll();
    }

    public function addAction() {
        $form = new Admin_Form_StaffForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                unset($formData["staff_id"]);
                try {
                    $staffModel = new Admin_Model_Staff();
                    $staffModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully added staff"));
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_StaffForm();
        $form->submit->setLabel("Save");
        $staffModel = new Admin_Model_Staff();
        $id = $this->_getParam('id', 0);
        $data = $staffModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['staff_id'];
                    unset($formData['staff_id']);
                    unset($formData['submit']);

                    $staffModel->update($formData, $id);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Staff edited"));
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
            $staffModel = new Admin_Model_Staff();
            $staffModel->delete($id);
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
        $editColumn->setPosition('right')->setName('Edit')->setDecorator("<a href=\"$baseUrl/admin/staff/edit/id/{{staff_id}}\">Edit</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{staff_id}}\"/>");
        $detailColumn = new Bvb_Grid_Extra_Column();
        $detailColumn->setPosition('right')->setName('Detail')->setDecorator("<a href=\"$baseUrl/admin/staff/detail/id/{{staff_id}}\">Detail</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{staff_id}}\"/>");
        $deleteColumn = new Bvb_Grid_Extra_Column();
        $deleteColumn->setPosition('right')->setName('Delete')->setDecorator("<a class=\"delete-data\" href=\"$baseUrl/admin/staff/delete/id/{{staff_id}}\">Delete</a>");
        $grid->addExtraColumns($detailColumn, $editColumn, $deleteColumn);
        $grid->updateColumn('staff_id', array('hidden' => true));
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
        $menuModel = new Admin_Model_Staff();
        $allMenus = $menuModel->listAll();
        $i = 0;
        foreach ($allMenus as $menu):
            $i++;
            $data = array();
            $data['sn'] = $i;
            $data['staff_id'] = $menu['staff_id'];
            $data['full_name'] = $menu['full_name'];
            $data['phone'] = $menu['phone'];
            $data['position'] = $menu['position'];
            $menus[] = $data;
        endforeach;
        return $menus;
    }

    public function attendanceAction() {
        $staffModel = new Admin_Model_Staff();
        $this->view->results = $staffModel->listAll();
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (array_key_exists('student', $formData)) {
                try {
                    unset($formData['form-submit']);
                    $staffAttendanceModel = new Admin_Model_StaffAttendance();
                    $staffAttendanceModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Attendance added"));
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    //var_dump($e->getMessage());
                    $this->_helper->FlashMessenger->addMessage(array("error" => "It seems like attendance of this day of these teachers is already done."));
                }
            } else {
                $this->_helper->FlashMessenger->addMessage(array("error" => "Atleast One student should be present."));
            }
        }
    }

}
?>

