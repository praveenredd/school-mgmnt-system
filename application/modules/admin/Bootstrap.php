<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initHelper() {
        Zend_Controller_Action_HelperBroker::addPrefix('School_Helper');
    }

}

