<?php

class Admin_Form_StaffForm extends Zend_Form {

    public function init() {

        $staffid = new Zend_Form_Element_Hidden("staff_id");

        $fullname = new Zend_Form_Element_Text("full_name");
        $fullname->setLabel("Full Name")
                ->setAttribs(array( 'class' => 'add-form-text'))
                ->setRequired(true);

        $phone = new Zend_Form_Element_Text("phone");
        $phone->setLabel("Phone")
                ->setAttribs(array( 'class' => 'add-form-text'))
                ->setRequired(true);

        $address = new Zend_Form_Element_Text("address");
        $address->setLabel("Address")
                ->setAttribs(array( 'class' => 'add-form-text'))
                ->setRequired(true);

        $email = new Zend_Form_Element_Text("email");
        $email->setLabel("E-mail")
                ->setAttribs(array( 'class' => 'add-form-text'))
                ->setRequired(true);

        $position = new Zend_Form_Element_Text("position");
        $position->setLabel("Position")
                ->setAttribs(array( 'class' => 'add-form-text'))
                ->setRequired(true);

        $joindate = new Zend_Form_Element_Text("joined_date");
        $joindate->setLabel("Join Date")
                ->setAttribs(array( 'class' => 'add-form-text'))
                ->setRequired(true);

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $staffid,
            $fullname,
            $phone,
            $address,
            $email,
            $position,
            $joindate,
            $submit));
    }

}