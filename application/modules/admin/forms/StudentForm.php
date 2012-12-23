<?php

class Admin_Form_StudentForm extends Zend_Form {

    public function init() {
        /* fetching grade options from class.ini file */
        $config = new Zend_Config_Ini(BASE_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "class.ini", "production");
        $gradeOptions = array("" => "---------------Select Class-------------") + $config->grade->toArray();
        $sectionOptions = array("" => "-------------Select Section-----------") + $config->section->toArray();
        $studentID = new Zend_Form_Element_Hidden("student_id");

        $rollno = new Zend_Form_Element_Text("roll_no");
        $rollno->setLabel("Roll Number")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $fullname = new Zend_Form_Element_Text("full_name");
        $fullname->setLabel("Full Name")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $option = array(
            'Male' => 'Male',
            'Female' => 'Female',
        );

        $sex = new Zend_Form_Element_Select('sex');
        $sex->setLabel("Sex")
                ->addMultiOptions($option)
                ->setAttribs(array('class' => 'add-form-select'))
                ->setRequired(true);

        $dob = new Zend_Form_Element_Text('dob');
        $dob->setLabel('Date of Birth')
                ->setAttribs(array('class' => 'add-form-text'));

        $email = new Zend_Form_Element_Text("email");
        $email->setLabel("Email")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $phone = new Zend_Form_Element_Text("phone");
        $phone->setLabel("Phone")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $guardian = new Zend_Form_Element_Text("guardian_name");
        $guardian->setLabel("Guardain")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $year = new Zend_Form_Element_Text("year");
        $year->setLabel("Year")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $grade = new Zend_Form_Element_Select("grade");
        $grade->setLabel("Grade")
                ->setAttribs(array('class' => 'add-form-select'))
                ->addMultiOptions($gradeOptions)
                ->setRequired(true);

        $section = new Zend_Form_Element_Select("section");
        $section->setLabel("Section")
                ->setAttribs(array('class' => 'add-form-select'))
                ->addMultiOptions($sectionOptions)
                ->setRequired(true);

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $studentID,
            $rollno,
            $fullname,
            $sex,
            $dob,
            $email,
            $phone,
            $guardian,
            $year,
            $grade,
            $section,
            $submit));
    }

}