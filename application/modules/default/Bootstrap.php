<?php

class Default_Bootstrap extends Zend_Application_Module_Bootstrap {

    public function __initSession() {
        $requestUri = new Zend_Session_Namespace("request_uri");
        $requestUri->setExpirationSeconds("7200");
        Zend_Registry::set("request_uri", $requestUri);
    }

}

