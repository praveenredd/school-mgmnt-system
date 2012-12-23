<?php

class Admin_Form_GalleryForm extends Zend_Form {

    public function init() {

        $galleryID = new Zend_Form_Element_Hidden("gallery_id");

        $galleryname = new Zend_Form_Element_Text("gallery_name");
        $galleryname->setLabel("Gallery Name")
                ->setAttribs(array( 'class' => 'add-form-text'))
                ->setRequired(true);
        
        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $galleryID,
            $galleryname,
            $submit));
    }

}
