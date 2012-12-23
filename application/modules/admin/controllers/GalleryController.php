<?php

class Admin_GalleryController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    public function indexAction() {
        $galleryModel = new Admin_Model_Gallery();
        $this->view->result = $galleryModel->getAll();
    }

    public function addAction() {
        $form = new Admin_Form_GalleryForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                unset($formData["gallery_id"]);
                try {
                    $galleryModel = new Admin_Model_Gallery();
                    $galleryModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Gallery added"));
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_GalleryForm();
        $form->submit->setLabel("Save");
        $galleryModel = new Admin_Model_Gallery();
        $id = $this->_getParam('id', 0);
        $data = $galleryModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['gallery_id'];
                    unset($formData['gallery_id']);
                    unset($formData['submit']);

                    $galleryModel->update($formData, $id);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Gallery edited"));
                    $this->_helper->redirector('index');
                }
            }
        } catch (Exception $e) {
            $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
        }
    }

    public function deleteAction() {
        $id = $this->_getParam('id', 0);
        $galleryModel = new Admin_Model_Gallery();
        $this->view->id = $id;
        if ($this->getRequest()->isPost()) {
            try {
                $delete = $this->_getParam('delete');
                if ('Yes' == $delete) {
                    $galleryModel->delete($id);
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
        $grid->setSource($source);
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $grid->setImagesUrl("$baseUrl/grid/");
        $editColumn = new Bvb_Grid_Extra_Column();
        $editColumn->setPosition('right')->setName('Edit')->setDecorator("<a href=\"$baseUrl/admin/gallery/edit/id/{{gallery_id}}\">Edit</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{gallery_id}}\"/>");
        $deleteColumn = new Bvb_Grid_Extra_Column();
        $deleteColumn->setPosition('right')->setName('Delete')->setDecorator("<a class=\"delete-data\" href=\"$baseUrl/admin/gallery/delete/id/{{gallery_id}}\">Delete</a>");
        $grid->addExtraColumns($editColumn, $deleteColumn);
        $grid->updateColumn('gallery_id', array('hidden' => true));
        $grid->updateColumn('del', array('hidden' => true));
        $grid->updateColumn("view_image", array("decorator" => "{{view_image}}", "escape" => true));
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
        $menuModel = new Admin_Model_Gallery();
        $allMenus = $menuModel->listAll();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        foreach ($allMenus as $menu):
            $data = array();
            $data['gallery_id'] = $menu['gallery_id'];
            $data['gallery_name'] = $menu['gallery_name'];
            $data['view_image'] = "<a href=\"$baseUrl/admin/gallery/gallery-images/id/" . $data['gallery_id'] . "\">" . $menu['gallery_name'] . "</a>";
            $menus[] = $data;
        endforeach;
        return $menus;
    }

    public function galleryImagesAction() {
        $id = $this->_getParam('id', 0);
        $imageModel = new Admin_Model_Image();
        $result = $imageModel->fetchImage($id);
        $this->view->result = $result;
    }

}
