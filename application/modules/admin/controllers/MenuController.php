<?php

class Admin_MenuController extends Zend_Controller_Action {

    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('delete', 'json')
                ->addActionContext('status', 'json')
                ->initContext();
    }

    public function indexAction() {
        $menuModel = new Admin_Model_Menu();
        $this->view->result = $menuModel->getAll();
    }

    public function addAction() {
        $form = new Admin_Form_MenuForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                // unset($formData["parent_menu_id"]);
                try {
                    $menuModel = new Admin_Model_Menu();
                    $menuModel->add($formData);
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    $this->view->message = $e->getMessage();
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_MenuForm();
        $form->submit->setLabel("Save");
        $menuModel = new Admin_Model_Menu();
        $id = $this->_getParam('id', 0);
        $data = $menuModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        $this->view->id = $id;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['menu_id'];
                    unset($formData['menu_id']);
                    unset($formData['submit']);

                    $menuModel->update($formData, $id);
                    $this->_helper->redirector('index');
                }
            }
        } catch (Exception $e) {
            $this->view->message = $e->getMessage();
        }
    }

    public function deleteAction() {
        $id = $this->_getParam('id', 0);
        $resultModel = new Admin_Model_Menu();
        $this->view->id = $id;
        if ($this->getRequest()->isPost()) {
            try {
                $delete = $this->_getParam('delete');
                if ('Yes' == $delete) {
                    $resultModel->delete($id);
                }$this->_helper->redirector("index");
            } catch (Exception $e) {
                $this->view->message = $e->getMessage();
            }
        }
    }

    public function statusAction() {
        $id = $this->_getParam('id');
        $transportModel = new Admin_Model_Menu();
        $rowStatus = $transportModel->changeStatus($id);
        $permClass = ($rowStatus == 'Y') ? 'permitted' : 'notpermitted';
        $this->view->permClass = $permClass;
        $this->view->rowStatus = $rowStatus;
        $this->view->saja = "hello";
    }

    public function contentSearchAction() {

        $content = array(
            'keyword' => $this->_getParam("searchtext"),
            'menu_type' => $this->_getParam("menu_type")
        );
        $menuModel = new Admin_Model_Menu();
        $results = $menuModel->searchContents($content);
        $this->view->results = $results;
    }

    public function contentAction() {
        $menuModel = new Admin_Model_Menu();
        $this->view->result = $menuModel->getAll();
    }

    public function editContentAction() {
        $form = new Admin_Form_NavigationForm();
        $form->submit->setLabel("Save");
        $menuModel = new Admin_Model_Menu();
        $id = $this->_getParam('id', 0);
        $data = $menuModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        $this->view->id = $id;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['menu_id'];
                    unset($formData['menu_id']);
                    unset($formData['submit']);

                    $menuModel->update($formData, $id);
                    $this->_helper->redirector('content');
                }
            }
        } catch (Exception $e) {
            $this->view->message = $e->getMessage();
        }
    }

    public function addContentAction() {
        $form = new Admin_Form_NavigationForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                // unset($formData["parent_menu_id"]);
                try {
                    $menuModel = new Admin_Model_Menu();
                    $menuModel->add($formData);
                    $this->_helper->redirector('content');
                } catch (Exception $e) {
                    $this->view->message = $e->getMessage();
                }
            }
        }
    }

    public function deleteContentAction() {
        $id = $this->_getParam('id', 0);
        $resultModel = new Admin_Model_Menu();
        $this->view->id = $id;
        if ($this->getRequest()->isPost()) {
            try {
                $delete = $this->_getParam('delete');
                if ('Yes' == $delete) {
                    $resultModel->delete($id);
                }$this->_helper->redirector("content");
            } catch (Exception $e) {
                $this->view->message = $e->getMessage();
            }
        }
    }

    public function detailAction() {
        $id = $this->_getParam('id', 0);
        $studentModel = new Admin_Model_Menu();
        $data = $studentModel->getDetailById($id);
        $this->view->result = $data;
    }

}

