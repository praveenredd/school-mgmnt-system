<?php

class Admin_NewsController extends Zend_Controller_Action {

    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('news-filter', 'json')
                ->addActionContext('delete', 'json')
                ->initContext();
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    public function indexAction() {
        $newsModel = new Admin_Model_News();
        $this->view->result = $newsModel->getAll();
    }

    public function addAction() {
        $form = new Admin_Form_NewsForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                unset($formData["news_id"]);
                try {
                    $newsModel = new Admin_Model_News();
                    $newsModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully News added"));
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_NewsForm();
        $form->submit->setLabel("Save");
        $newsModel = new Admin_Model_News();
        $id = $this->_getParam('id', 0);
        $data = $newsModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['news_id'];
                    unset($formData['news_id']);
                    unset($formData['submit']);
                    $newsModel->update($formData, $id);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully News edited"));
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
            $newsModel = new Admin_Model_News();
            $newsModel->delete($id);
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
        }
    }

    public function listAction() {
        $config = new Zend_Config_Ini(BASE_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "grid.ini", 'production');
        $grid = Bvb_Grid::factory('Table', $config);
        $data = $this->_listdata();
        $source = new Bvb_Grid_Source_Array($data);
        $grid->setSource($source);
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $grid->setImagesUrl("$baseUrl/grid/");
        $editColumn = new Bvb_Grid_Extra_Column();
        $editColumn->setPosition('right')->setName('Edit')->setDecorator("<a href=\"$baseUrl/admin/news/edit/id/{{news_id}}\">Edit</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{news_id}}\"/>");
        $deleteColumn = new Bvb_Grid_Extra_Column();
        $deleteColumn->setPosition('right')->setName('Delete')->setDecorator("<a class=\"delete-data\" href=\"$baseUrl/admin/news/delete/id/{{news_id}}\">Delete</a>");
        $grid->addExtraColumns($editColumn, $deleteColumn);
        $grid->updateColumn('news_id', array('hidden' => true));
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
        $menuModel = new Admin_Model_News();
        $allMenus = $menuModel->listAll();

        foreach ($allMenus as $menu):
            $data = array();
            $data['news_id'] = $menu['news_id'];
            $data['title'] = $menu['title'];
            $data['content'] = $menu['content'];
            $menus[] = $data;
        endforeach;
        return $menus;
    }

}

