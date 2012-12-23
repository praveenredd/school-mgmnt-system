<?php

class Admin_FrontController extends Zend_Controller_Action
{

    public function init() {
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    public function indexAction()
    {
        // action body
    }


}

