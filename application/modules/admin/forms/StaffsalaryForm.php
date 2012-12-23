<?php

class Admin_Form_StaffsalaryForm extends Zend_Form {

    public function init() {

        $salaryid = new Zend_Form_Element_Hidden("salary_id");

        $staffmodel = new Admin_Model_Staff();
        $option = $staffmodel->getKeysAndValues();

        $staff = new Zend_Form_Element_Select("staff_id");
        $staff->setLabel("Staff")
                ->addMultiOptions($option)
                ->setAttribs(array('class' => 'add-form-select'));
        $monthlysalary = new Zend_Form_Element_Text("monthly_salary");
        $monthlysalary->setLabel("Monthly Salary")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $amount = new Zend_Form_Element_Text("amount");
        $amount->setLabel("Amount")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $due = new Zend_Form_Element_Text("due");
        $due->setLabel("Due")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $salaryid,
            $staff,
            $monthlysalary,
            $amount,
            $due,
            $submit));
    }

}