<?php

class Admin_Form_ResultForm extends Zend_Form {

    public function init() {

        $resultID = new Zend_Form_Element_Hidden("result_id");
        
        $studentmodel = new Admin_Model_Student();
        $option = $studentmodel->getKeysAndValues();

        $student = new Zend_Form_Element_Select("student_id");
        $student->setLabel("Student")
                ->addMultiOptions($option)
                ->setAttribs(array('class' => 'form-select'));

        $grade = new Zend_Form_Element_Text("grade");
        $grade->setLabel("Grade")
                ->setAttribs(array('size' => 30, 'class' => 'form-text'))
                ->setRequired(true);

        $percent = new Zend_Form_Element_Text("percent");
        $percent->setLabel("Percent")
                ->setAttribs(array('size' => 30, 'class' => 'form-text'))
                ->setRequired(true);

        $ExamType = new Zend_Form_Element_Text("exam_type");
        $ExamType->setLabel("Exam Type")
                ->setAttribs(array('size' => 30, 'class' => 'form-text'))
                ->setRequired(true);

        $remarks = new Zend_Form_Element_Text("remarks");
        $remarks-> setLabel("Remarks")
                        ->setAttribs(array('size' => 30, 'class' => 'form-text'))
                        ->setRequired(true);

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $resultID,
            $student,
            $grade,
            $percent,
            $ExamType,
            $remarks,
            $submit));
    }

}

?>
