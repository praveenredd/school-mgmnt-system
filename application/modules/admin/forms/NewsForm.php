<?php

class Admin_Form_NewsForm extends Zend_Form {

    public function init() {

        $newsid = new Zend_Form_Element_Hidden("news_id");

        $title = new Zend_Form_Element_Text("title");
        $title->setLabel("Title")
                ->setAttribs(array( 'class' => 'add-form-text'))
                ->setRequired(true);

        $content = new Zend_Form_Element_Textarea("content");
        $content->setLabel("content")
                ->setAttribs(array( 'class' => 'add-form-textarea'))
                ->setRequired(true);

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $newsid,
            $title,
            $content,
            $submit));
    }

}