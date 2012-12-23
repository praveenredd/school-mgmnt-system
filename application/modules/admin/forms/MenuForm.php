<?php

class Admin_Form_MenuForm extends Zend_Form {

    public function init() {

        $frontMenuID = new Zend_Form_Element_Hidden("menu_id");

        $menumodel = new Admin_Model_Menu();
        $option = $menumodel->getKeysAndValues();

        $title = new Zend_Form_Element_Text("title");
        $title->setLabel("Title")
                ->setRequired(true)
                ->setAttribs(array('class' => 'add-form-text'));

        $content = new Zend_Form_Element_Textarea("content");
        $content->setLabel("Content")
                ->setAttribs(array('rows' => 7, 'columns' => 10, 'class' => 'add-form-text'))
                ->setRequired(true);

        $parentMenuID = new Zend_Form_Element_Select("parent_menu_id");
        $parentMenuID->setLabel("Parent Menu")
                ->addMultiOptions($option)
                ->setAttribs(array('class' => 'add-form-text', 'id' => 'menu_type'));

        $enteredDate = new Zend_Form_Element_Text("entered_date");
        $enteredDate->setLabel("Entered Date")
                ->setAttribs(array('class' => 'form-date'))
                ->setRequired(true);

        $updateDate = new Zend_Form_Element_Text("update_date");
        $updateDate->setLabel("Updated Date")
                ->setAttribs(array('class' => 'form-date'))
                ->setRequired(true);


        $action = new Zend_Form_Element_Text("action");
        $action->setLabel("Action")
                ->setAttribs(array('class' => 'add-form-text'));
        $menutypeOption = array('select' => "--Select--", 'front' => "front", 'admin' => "admin", 'superUser' => "superuser", 'dashboard' => "dashboard");
        $menuType = new Zend_Form_Element_Select("menu_type");
        $menuType->setLabel("Menu Type")
                ->addMultiOptions($menutypeOption)
                ->setAttribs(array('class' => 'add-form-select'));


        $controller = new Zend_Form_Element_Text("controller");
        $controller->setLabel("Controller ")
                ->setAttribs(array('class' => 'form-date'));


        $module = new Zend_Form_Element_Text("module");
        $module->setLabel("Module")
                ->setAttribs(array('class' => 'form-date'));


        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $frontMenuID,
            $title,
            $content,
            $parentMenuID,
            $action,
            $menuType,
            $controller,
            $module,
            $submit));
    }

}
?>

