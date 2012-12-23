<?php

class Admin_Form_SubjectForm extends Zend_Form {

    public function init() {

        /* fetching grade options from class.ini file */
        $config = new Zend_Config_Ini(BASE_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "class.ini", "production");
//        var_dump($config->latitude->home);exit;
        $gradeOptions = $config->grade->toArray();

        $subjectID = new Zend_Form_Element_Hidden("subject_id");

        $grade = new Zend_Form_Element_Select("grade");
        $grade->setLabel("Grade")
                ->setAttribs(array('class' => 'add-form-select'))
                ->addMultiOptions($gradeOptions)
                ->setRequired(true);

        $name = new Zend_Form_Element_Text("name");
        $name->setLabel("Name")
                ->setAttribs(array( 'class' => 'add-form-text'))
                ->setRequired(true);
        
        $short_name = new Zend_Form_Element_Text("short_name");
        $short_name->setLabel("Short Name")
                            ->setAttribs(array('size'=>30, 'class'=>'add-form-text'));

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $subjectID,
            $grade,
            $name,
            $short_name,
            $submit));
    }

}
