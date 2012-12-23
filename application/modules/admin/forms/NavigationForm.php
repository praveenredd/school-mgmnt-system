<?php

class Admin_Form_NavigationForm extends Zend_Form {

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
                ->setAttribs(array('class' => 'add-form-text', 'id' => 'menu_type'))
                ->setRequired(true);

        $enteredDate = new Zend_Form_Element_Text("entered_date");
        $enteredDate->setLabel("Entered Date")
                ->setAttribs(array('class' => 'add-form-text form-date'))
                ->setRequired(true);

        $updateDate = new Zend_Form_Element_Text("update_date");
        $updateDate->setLabel("Updated Date")
                ->setAttribs(array('class' => 'add-form-text form-date'))
                ->setRequired(true);
        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $frontMenuID,
            $title,
            $content,
            $parentMenuID,
            $submit));
    }

}
?>

