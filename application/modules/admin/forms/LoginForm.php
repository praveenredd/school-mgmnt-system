<?php

class Admin_Form_LoginForm extends Zend_Form {

    public function init() {

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel("Email")
                ->setAttribs(array('class' => 'form-text'))
                ->setRequired('true')
                ->addValidator('EmailAddress')
                ->addErrorMessage("Please enter a valid e-mail address.");

        $password = new Zend_Form_Element_Text('password');
        $password->setLabel("Password")
                ->setAttribs(array('class' => 'form-text'))
                ->setRequired('true')
                ->addErrorMessage("Please enter a valid password.");;

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Login");
        $this->addElements(array(
            $email,
            $password,
            $submit));
        $this->setElementDecorators(array(
            'viewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'label-wrapperl')),
            array('Label', array('tag' => 'div')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-item'))
        ));
        $submit->removeDecorator("Label");
    }

}

