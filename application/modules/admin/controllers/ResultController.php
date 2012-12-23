<?php

class Admin_ResultController extends Zend_Controller_Action {

    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('exam-type-option', 'json')
                ->initContext();
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    public function indexAction() {
        $form = new Admin_Form_StudentSearchForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $data = array(
                'year' => $formData['year'],
                'grade' => $formData['grade'],
                'section' => $formData['section']
            );
            $a = $form->isValid($formData);
            if ($a) {
                $studentModel = new Admin_Model_Student();
                $results = $studentModel->search($data);
                $subjectModel = new Admin_Model_Subject();
                $subOptions = $subjectModel->getSubjects($formData['grade']);
                $examTypeModel = new Admin_Model_Examtype();
                $examTypeOption = $examTypeModel->getSelectedExamType($formData['grade']);
                $addForm = new Admin_Form_ResultaddForm(sizeof($results));
                $addForm->subject_id->addMultiOptions($subOptions);
                $addForm->examtype_id->addMultiOptions($examTypeOption);
                $addForm->grade->setValue($formData['grade']);
                $addForm->year->setValue($formData['year']);
                $this->view->addForm = $addForm;
                $this->view->searchResults = $results;
            }
            if ("Add" == $formData['Search']) {
                if ($addForm->isValid($formData)) {
                    unset($formData['section']);
                    try {
                        foreach ($formData as $data) {
                            $resultModel = new Admin_Model_Result();
                            if (is_array($data)) {
                                foreach ($data as $row) {
                                    $arr = array();
                                    unset($row['result_id']);
                                    unset($formData['Search']);
                                    $arr = $row;
                                    $arr['grade'] = $formData['grade'];
                                    $arr['subject_id'] = $formData['subject_id'];
                                    $arr['examtype_id'] = $formData['examtype_id'];
                                    $arr['year'] = $formData['year'];
                                    $resultModel->add($arr);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        $this->_helper->FlashMessenger->addMessage(array("error" => "It seems like you have already added result of this subject for this type of exam"));
                        //var_dump($e->getMessage());
                    }
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Result added"));
                }
            }
        }
    }

    public function addAction() {
        $form = new Admin_Form_ResultForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                unset($formData["result_id"]);
                try {
                    $resultModel = new Admin_Model_Result();
                    $resultModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Result added"));
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_ResultForm();
        $form->submit->setLabel("Save");
        $resultModel = new Admin_Model_Result();
        $id = $this->_getParam('id', 0);
        $data = $resultModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['result_id'];
                    unset($formData['result_id']);
                    unset($formData['submit']);

                    $resultModel->update($formData, $id);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully Result edited"));
                    $this->_helper->redirector('index');
                }
            }
        } catch (Exception $e) {
            $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
        }
    }

    public function deleteAction() {
        $id = $this->_getParam('id', 0);
        $resultModel = new Admin_Model_Result();
        $this->view->id = $id;
        if ($this->getRequest()->isPost()) {
            try {
                $delete = $this->_getParam('delete');
                if ('Yes' == $delete) {
                    $resultModel->delete($id);
                }$this->_helper->redirector("index");
            } catch (Exception $e) {
                $this->view->message = $e->getMessage();
            }
        }
    }

    public function detailAction() {
        $id = $this->_getParam("id", 0);
        $form = new Admin_Form_ResultdetailForm();
        $form->result_id->setValue($id);
        echo $form;
        echo $id;
    }

    public function searchAction() {
        $grade = null;
        $form = new Admin_Form_ResultsearchForm();
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ("Search" == $formData['Search']) {
                if ($form->isValid($formData)) {
                    unset($formData['Search']);
                    /* in order to preserve post value..so as to get filtered student names */
                    $data = array(
                        'year' => $this->_getParam("year"),
                        'grade' => $this->_getParam("grade"),
                        'section' => $this->_getParam("section"),
                        'del' => 'N'
                    );
                    $studentModel = new Admin_Model_Student();
                    $options = $studentModel->getStudentNames($data);
                    $form->full_name->addMultiOptions($options);
                    /* end of filtering section */
                    if ($formData['year'] != "" && $formData['grade'] != "" && $formData['examtype_id'] != "" && $formData['roll_no'] == "") {
                        $resultModel = new Admin_Model_Result();
                        $results = $resultModel->searchAllResults($formData);
                        $subjectModel = new Admin_Model_Subject();
                        $subOptions = $subjectModel->getonlySubjects($formData['grade']);
                        $this->view->subjects = $subOptions;
                        $this->view->searchResults = $results;
                    } elseif ($formData['roll_no'] != "" && $formData['year'] != "" && $formData['grade'] != "" && $formData['examtype_id'] != "") {
                        $resultModel = new Admin_Model_Result();
                        $results = $resultModel->searchResultRollWise($formData);
                        $this->view->searchoneResult = $results;
                    }
                }
                $grade = $formData['grade'];
                $examtypeModel = new Admin_Model_Examtype();
                $options = $examtypeModel->getSelectedExamType($grade);
                $form->examtype_id->addMultiOptions($options);
            }
        }

        $this->view->form = $form;
    }

    public function examTypeOptionAction() {
        $grade = $this->_getParam("grade");
        $examtypeModel = new Admin_Model_Examtype();
        $options = $examtypeModel->getSelectedExamType($grade);
        $this->view->results = $options;
        $this->view->html = $this->view->render("result/exam-type-option.phtml");
    }

}

