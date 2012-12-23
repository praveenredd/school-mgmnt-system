<?php

class Admin_Form_StudentSearchForm extends Zend_Form {

    protected $_param;

    public function __construct($param = null) {
        $this->_param = $param;
        parent::__construct($param);
    }

    public function init() {
        $this->setMethod("post");
        $config = new Zend_Config_Ini(BASE_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "class.ini", "production");
        $gradeOptions = $config->grade->toArray();
        if ($this->_param) {
            $sectionOptions = array("All"=>"All");
        } else {
            $sectionOptions = $config->section->toArray();
        }
        $yearOption = array();
        for ($i = 2010; $i < 2020; $i++) {
            $yearOption[$i] = $i;
        }
        //grade
        $grade = new Zend_Form_Element_Select("grade");
        $grade->setLabel("Grade")
                ->addMultiOptions($gradeOptions)
                ->setAttribs(array('class' => 'form-long-select', 'id' => 'grade'));
        //section
        $section = new Zend_Form_Element_Select("section");
        $section->setLabel("Section")
                ->addMultiOptions($sectionOptions)
                ->setAttribs(array('class' => 'form-long-select', 'id' => 'section'));
        //year
        $year = new Zend_Form_Element_Select("year");
        $year->setLabel("Year")
                ->addMultiOptions($yearOption)
                ->setAttribs(array('class' => 'form-long-select', 'id' => 'year'));


        //submit
        $search = new Zend_Form_Element_Submit("Search");
        $formElements = array(
            $grade,
            $section,
            $year,
            $search,
        );
        $this->addElements($formElements);
        $this->setElementDecorators(array(
            'viewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-item')),
            array('Label', array('tag' => 'div')),
        ));
        $grade->removeDecorator("label");
        $section->removeDecorator("label");
        $year->removeDecorator("label");
        $search->removeDecorator("label");
    }

}

?>