<?php

class School_Layout_Plugin_Layout extends Zend_Controller_Plugin_Abstract {

    protected $_school;

    public function __construct($schoolName = "school") {
        $this->_school = $schoolName;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $options = array(
            'layoutPath' => BASE_PATH . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $this->_school . DIRECTORY_SEPARATOR . $module,
        );
        Zend_Layout::startMvc()->setLayoutPath($options);
        if ($module == "default") {
            $themeModel = new Admin_Model_Theme();
            $active = $themeModel->getActive();
            Zend_Layout::startMvc()->setLayout($active);
        }
    }

}
