<?php

class Admin_Form_ExamtypeForm extends Zend_Form {

    protected $_param;

    public function __construct($param = null) {
        $this->_param = $param;
        parent::__construct($param);
    }

    public function init() {

        /* fetching grade options from class.ini file */
        $config = new Zend_Config_Ini(BASE_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "class.ini", "production");

        $examtypeID = new Zend_Form_Element_Hidden("examtype_id");

        $name = new Zend_Form_Element_Text("name");
        $name->setLabel("Name")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $full_marks = new Zend_Form_Element_Text("full_marks");
        $full_marks->setLabel("Full Marks")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true)
                ->addValidator('float', true, array('locale' => 'en_US'))
                ->addValidator('greaterThan', true, array('min' => 0));

        if (!$this->_param) {
            $gradeOptions = array("all" => "All") + $config->grade->toArray();
            $grade = new Zend_Form_Element_MultiCheckbox("grade");
        } else {
            $gradeOptions = $config->grade->toArray();
            $grade = new Zend_Form_Element_Select("grade");
        }
        $grade->setLabel("Grade")
                ->addMultiOptions($gradeOptions)
                ->setRequired(true)
                ->setAttribs(array('class' => 'add-form-checkbox'));

        $pass_marks = new Zend_Form_Element_Text("pass_marks");
        $pass_marks->setLabel("Pass Marks")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true)
                ->addValidator('float', true, array('locale' => 'en_US'))
                ->addValidator('greaterThan', true, array('min' => 0));

        $isFinal = new Zend_Form_Element_Radio("is_final");
        $isFinal->setLabel("Is added to Final?")
                ->setAttribs(array('class' => 'add-form-radio'))
                ->addMultiOptions(array("Y" => "Yes", "N" => "No"))
                ->setValue("N")
                ->setRequired(true);
        $finalValue = new Zend_Form_Element_Text("final_value");
        $finalValue->setLabel("Final Value")
                ->setAttribs(array('class' => 'add-form-text', 'placeholder' => 'Optional'))
                ->addValidator('float', true, array('locale' => 'en_US'))
                ->addValidator('greaterThan', true, array('min' => 0));

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $examtypeID,
            $name,
            $grade,
            $full_marks,
            $pass_marks,
            $isFinal,
            $finalValue,
            $submit));
    }

}
