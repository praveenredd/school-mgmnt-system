<?php

class Admin_Form_FeetypeForm extends Zend_Form {

    protected $_param;

    public function __construct($param = null) {
        $this->_param = $param;
        parent::__construct($param);
    }

    public function init() {
        $config = new Zend_Config_Ini(BASE_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "class.ini", "production");
        $monthOptions = array('Y' => 'Yes', 'N' => 'No');
        $feeTypeID = new Zend_Form_Element_Hidden("fee_type_id");
        $feeTitle = new Zend_Form_Element_Text("name");
        $feeTitle->setLabel("Fee Title")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);
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

        $amount = new Zend_Form_Element_Text("amount");
        $amount->setLabel("Amount")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);
        $monthly = new Zend_Form_Element_Radio("isMonthly");
        $monthly->setLabel("Monthly:")
                ->addMultiOptions($monthOptions)
                ->setAttribs(array('class' => 'add-form-select'));

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $feeTypeID,
            $feeTitle,
            $grade,
            $amount,
            $monthly,
            $submit));
    }

}