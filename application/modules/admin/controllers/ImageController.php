<?php

class Admin_ImageController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    public function indexAction() {
        $imageModel = new Admin_Model_Image();
        $this->view->result = $imageModel->getAll();
    }

    public function addAction() {
        $form = new Admin_Form_ImageForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                unset($formData["image_id"]);
                unset($formData["MAX_FILE_SIZE"]);
                $image = $formData["image_name"] = $form->image_name->getFileName();
                $exp = explode(DIRECTORY_SEPARATOR, $image);
                $originalFilename = $formData['image_name'] = $exp[sizeof($exp) - 1];
                $newFilename = $formData['image_name'] = time() . $formData['image_name'];
                $form->image_name->addFilter('Rename', $newFilename);
                try {
                    $form->image_name->receive();
                    //upload complete!
                    $file = new Zend_File_Transfer();
                    $file->setDisplayFilename($originalFilename['basename'])
                            ->setActualFilename($newFilename);
                    $file->save();
                } catch (Exception $e) {
                    //error: file couldn't be received, or saved (one of the two)
                }
                try {
                    $imageModel = new Admin_Model_Image();
                    $imageModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Image added"));
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_ImageForm();
        $form->submit->setLabel("Save");
        $imageModel = new Admin_Model_Image();
        $id = $this->_getParam('id', 0);
        $data = $imageModel->getDetailById($id);
        $form->populate($data);
        $imageName = $data["image_name"];
        $this->view->form = $form;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['image_id'];
                    unset($formData['image_id']);
                    unset($formData['submit']);
                    unset($formData["MAX_FILE_SIZE"]);
                    $image = $formData["image_name"] = $form->image_name->getFileName();
                    $exp = explode(DIRECTORY_SEPARATOR, $image);
                    $originalFilename = $formData['image_name'] = $exp[sizeof($exp) - 1];
                    $newFilename = $formData['image_name'] = time() . $formData['image_name'];
                    $form->image_name->addFilter('Rename', $newFilename);
                    try {
                        $form->image_name->receive();
                        //upload complete!
                        $file = new Zend_File_Transfer();
                        $file->setDisplayFilename($originalFilename['basename'])
                                ->setActualFilename($newFilename);
                        $file->save();
                    } catch (Exception $e) {
                        //error: file couldn't be received, or saved (one of the two)
                    }

                    $imageModel->update($formData, $id);
                    $path = BASE_PATH . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "school" . DIRECTORY_SEPARATOR . "admin" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $imageName;
                     unlink($path);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Image edited"));
                    $this->_helper->redirector('index');
                }
            }
        } catch (Exception $e) {
            $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
        }
    }

    public function deleteAction() {
        $id = $this->_getParam('id', 0);
        $imageModel = new Admin_Model_Image();
        $this->view->id = $id;
        if ($this->getRequest()->isPost()) {
            try {
                $delete = $this->_getParam('delete');
                if ('Yes' == $delete) {
                    $result = $imageModel->getDetailById($id);
                    $path = BASE_PATH . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "school" . DIRECTORY_SEPARATOR . "admin" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $result['image_name'];
                    unlink($path);
                    $imageModel->delete($id);
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
        $imageColumn = new Bvb_Grid_Extra_Column();
        $imageColumn->setPosition('right')->setName('Image')->setDecorator("<img height=\"40\" width=\"40\"src=\"$baseUrl/school/admin/uploads/{{image_name}}\"/>");
        $editColumn = new Bvb_Grid_Extra_Column();
        $editColumn->setPosition('right')->setName('Edit')->setDecorator("<a href=\"$baseUrl/admin/image/edit/id/{{image_id}}\">Edit</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{image_id}}\"/>");
        $deleteColumn = new Bvb_Grid_Extra_Column();
        $deleteColumn->setPosition('right')->setName('Delete')->setDecorator("<a class=\"delete-data\" href=\"$baseUrl/admin/image/delete/id/{{image_id}}\">Delete</a>");
        $grid->addExtraColumns($imageColumn, $editColumn, $deleteColumn);
        $grid->updateColumn('image_id', array('hidden' => true));
        $grid->updateColumn('image_name', array('hidden' => true));
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
        //$grid->setExport(array('print', 'word', 'csv', 'excel', 'pdf'));
        $this->view->grid = $grid->deploy();
    }

    public function _listdata() {
        $menus = array();
        $menuModel = new Admin_Model_Image();
        $allMenus = $menuModel->listAll();

        foreach ($allMenus as $menu):
            $data = array();
            $data['image_id'] = $menu['image_id'];
            $data['gallery_name'] = $menu['gallery_name'];
            $data['image_name'] = $menu['image_name'];
            $menus[] = $data;
        endforeach;
        return $menus;
    }

}

