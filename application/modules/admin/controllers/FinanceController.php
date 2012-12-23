<?php

class Admin_FinanceController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    public function indexAction() {
        $financeModel = new Admin_Model_Finance();
        $this->view->result = $financeModel->getAll();
    }

    public function addAction() {
        $form = new Admin_Form_FinanceForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                unset($formData["transaction_id"]);
                try {
                    $financeModel = new Admin_Model_Finance();
                    $financeModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Finance added"));
                    $this->_helper->redirector('list');
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_FinanceForm();
        $form->submit->setLabel("Save");
        $financeModel = new Admin_Model_Finance();
        $id = $this->_getParam('id', 0);
        $data = $financeModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['transaction_id'];
                    unset($formData['transaction_id']);
                    unset($formData['submit']);

                    $financeModel->update($formData, $id);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Finance edited"));
                    $this->_helper->redirector('list');
                }
            }
        } catch (Exception $e) {
            $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
        }
    }

    public function deleteAction() {
        $id = $this->_getParam('id', 0);
        $financeModel = new Admin_Model_Finance();
        $this->view->id = $id;
        if ($this->getRequest()->isPost()) {
            try {
                $delete = $this->_getParam('delete');
                if ('Yes' == $delete) {
                    $financeModel->delete($id);
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
        $editColumn->setPosition('right')->setName('Edit')->setDecorator("<a href=\"$baseUrl/admin/finance/edit/id/{{transaction_id}}\">Edit</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{transaction_id}}\"/>");
        $detailColumn = new Bvb_Grid_Extra_Column();
        $detailColumn->setPosition('right')->setName('Detail')->setDecorator("<a href=\"$baseUrl/admin/finance/detail/id/{{transaction_id}}\">Detail</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{transaction_id}}\"/>");
        $deleteColumn = new Bvb_Grid_Extra_Column();
        $deleteColumn->setPosition('right')->setName('Delete')->setDecorator("<a class=\"delete-data\" href=\"$baseUrl/admin/finance/delete/id/{{transaction_id}}\">Delete</a>");
        $grid->addExtraColumns($detailColumn, $editColumn, $deleteColumn);
        $grid->updateColumn('transaction_id', array('hidden' => true));
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
        $menuModel = new Admin_Model_Finance();
        $allMenus = $menuModel->listAll();
        $i = 0;
        foreach ($allMenus as $menu):
            $i++;
            $data = array();
            $data['sn'] = $i;
            $data['transaction_id'] = $menu['transaction_id'];
            $data['title'] = $menu['title'];
            $data['amount'] = $menu['amount'];
            //$data['given_to'] = $menu['given_to'];
            //$data['reason'] = $menu['reason'];
            //$data['given_date'] = $menu['given_date'];
            // $data['given_by'] = $menu['given_by'];
            $data['type'] = $menu['type'];
            $data['interest_rate'] = $menu['interest_rate'];
            $menus[] = $data;
        endforeach;
        return $menus;
    }

    public function detailAction() {
        $id = $this->_getParam('id', 0);
        $financeModel = new Admin_Model_Finance();
        $data = $financeModel->getDetailById($id);
        $this->view->result = $data;
    }

}

