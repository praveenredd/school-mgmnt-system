<?php

class Admin_IndexController extends Zend_Controller_Action {

    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('search-content', 'json')
                ->initContext();
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    
    public function indexAction() {
        $themeModel = new Admin_Model_Theme();
        $theme = $themeModel->getActive("N",true);
        $this->view->themeOptions = $themeModel->listinKeyValue("N");
         if ($this->getRequest()->isPost()) {
            $themeId = $this->getRequest()->getPost("theme");
            $themeModel->setActive($themeId,"N");
            $theme = $themeId;
        }
        $this->view->theme = $theme;
    }

    public function listAction() {
        $config = new Zend_Config_Ini(BASE_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "grid.ini", 'production');
        $grid = Bvb_Grid::factory('Table', $config);
        $data = $this->_listdata();
        // echo "<pre>";
        //print_r($data);exit;
        $source = new Bvb_Grid_Source_Array($data);
        $grid->setSource($source);
        $grid->setImagesUrl('/grid/');
        $editColumn = new Bvb_Grid_Extra_Column();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $editColumn->setPosition('right')->setName('Edit')->setDecorator("<a href=\"$baseUrl/admin/menu/edit/id/{{menu_id}}\">Edit</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{menu_id}}\"/>");
        $deleteColumn = new Bvb_Grid_Extra_Column();
        $deleteColumn->setPosition('right')->setName('Delete')->setDecorator("<a class=\"delete-data\" href=\"$baseUrl/menu/delete/id/{{menu_id}}\">Delete</a>");
        $grid->addExtraColumns($editColumn, $deleteColumn);
        $grid->updateColumn('menu_id', array('hidden' => true));
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
        $menuModel = new Admin_Model_Menu();
        $allMenus = $menuModel->listAll();
        $i = 1;
        foreach ($allMenus as $menu):
            $data = array();
            $data['sn'] = $i++;
            $data['menu'] = $menu['title'];
            $data['menu_id'] = $menu['menu_id'];
            $data['upper_menu'] = ($menu['upper_menu']) ? $menu['upper_menu'] : "None";
            $data['entered_date'] = $menu['entered_date'];
            $data['update_date'] = $menu['update_date'];
            $data['show'] = ("Y" == $menu['show']) ? "Yes" : "No";
            $menus[] = $data;
        endforeach;
        return $menus;
    }

    public function themeAction() {
        $themeModel = new Admin_Model_Theme();
        $this->view->themeOptions = $themeModel->listinKeyValue();
        if ($this->getRequest()->isPost()) {
            $themeId = $this->getRequest()->getPost("theme");
            $themeModel->setActive($themeId);
            $this->view->theme = $themeId;
        }
    }

    public function searchContentAction() {
        $content = array(
            'keyword' => $this->_getParam("content"),
            'menu_type' => $this->_getParam("menu_type")
        );
        $menuModel = new Admin_Model_Menu();
        $results = $menuModel->searchContents($content);
        $this->view->results = $results;
        $this->view->size = sizeof($results);
        $this->view->html = $this->view->render("index/search-content.phtml");
    }

}

