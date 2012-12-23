<?php

class Admin_Form_FinanceForm extends Zend_Form {

    public function init() {

        $transactionid = new Zend_Form_Element_Hidden("transaction_id");

        $title = new Zend_Form_Element_Text("title");
        $title->setLabel("Title")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $amount = new Zend_Form_Element_Text("amount");
        $amount->setLabel("amount")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $givenTo = new Zend_Form_Element_Text("given_to");
        $givenTo->setLabel("Given To")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);
        $reason = new Zend_Form_Element_Text("reason");
        $reason->setLabel("Reason")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);
        $givenDate = new Zend_Form_Element_Text("given_date");
        $givenDate->setLabel("Given Date")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);
        $givenBy = new Zend_Form_Element_Text("given_by");
        $givenBy->setLabel("Given By")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);
        $typeOptions = array('' => '--Select--', 'Debit' => 'Debit', 'Credit' => 'Credit');
        $type = new Zend_Form_Element_Select("type");
        $type->setLabel("Type")
                ->addMultiOptions($typeOptions)
                ->setAttribs(array('class' => 'add-form-select'))
                ->setRequired(true);
        $interestRate = new Zend_Form_Element_Text("interest_rate");
        $interestRate->setLabel("Interest Rate")
                ->setAttribs(array('class' => 'add-form-text'))
                ->setRequired(true);

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $transactionid,
            $title,
            $amount,
            $givenTo,
            $reason,
            $givenDate,
            $givenBy,
            $type,
            $interestRate,
            $submit));
    }

}