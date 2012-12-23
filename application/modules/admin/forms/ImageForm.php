<?php

class Admin_Form_ImageForm extends Zend_Form {

    public function init() {
        $this->setAttrib('enctype', 'multipart/form-data');

        $imageID = new Zend_Form_Element_Hidden("image_id");

        $gallerymodel = new Admin_Model_Gallery();
        $option = $gallerymodel->getKeysAndValues();

        $gallery = new Zend_Form_Element_Select("gallery_id");
        $gallery->setLabel("Gallery")
                ->addMultiOptions($option)
                ->setAttribs(array('class' => 'add-form-select'));

        $imagename = new Zend_Form_Element_File("image_name");
        $imagename->setLabel("Image")
                ->setAttribs(array('class' => 'add-form-file'))
                ->setDestination(UPLOAD_PATH)
                ->setRequired(true);

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Submit");

        $this->addElements(array(
            $imageID,
            $gallery,
            $imagename,
            $submit));
    }

}

?>
