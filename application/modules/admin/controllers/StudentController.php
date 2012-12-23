<?php

class Admin_StudentController extends Zend_Controller_Action {

    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('student-filter', 'json')
                ->addActionContext('delete', 'json')
                ->initContext();
        /* Initialize action controller here */
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

    public function indexAction() {
        $studentModel = new Admin_Model_Student();
        $this->view->result = $studentModel->getAll();
    }

    public function addAction() {
        $form = new Admin_Form_StudentForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['submit']);
                unset($formData["student_id"]);
                try {
                    $subjectModel = new Admin_Model_Student();
                    $subjectModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("edit" => "Successfully Student Added"));
                    $this->_helper->redirector('list');
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
                }
            }
        }
    }

    public function editAction() {

        $form = new Admin_Form_StudentForm();
        $form->submit->setLabel("Save");
        $studentModel = new Admin_Model_Student();
        $id = $this->_getParam('id', 0);
        $data = $studentModel->getDetailById($id);
        $form->populate($data);
        $this->view->form = $form;
        try {
            if ($this->getRequest()->isPost()) {
                if ($form->Valid($this->getRequest()->getPost())) {
                    $formData = $this->getRequest()->getPost();
                    $id = $formData['student_id'];
                    unset($formData['student_id']);
                    unset($formData['submit']);

                    $studentModel->update($formData, $id);
                    $this->_helper->FlashMessenger->addMessage(array("edit" => "Successfully Student Edited"));
                    $this->_helper->redirector('list');
                }
            }
        } catch (Exception $e) {
            $this->_helper->FlashMessenger->addMessage(array("error" => $e->getMessage()));
        }
    }

    public function deleteAction() {
        try {
            $id = $this->_getParam('id', 0);
            $studentModel = new Admin_Model_Student();
            $studentModel->delete($id);
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
        }
    }

    public function listAction() {
        $config = new Zend_Config_Ini(BASE_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "grid.ini", 'production');
        $grid = Bvb_Grid::factory('Table', $config);
        $data = $this->_listdata();
        $source = new Bvb_Grid_Source_Array($data);
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $grid->setSource($source);
        $grid->setImagesUrl("$baseUrl/grid/");
        $editColumn = new Bvb_Grid_Extra_Column();
        $editColumn->setPosition('right')->setName('Edit')->setDecorator("<a href=\"$baseUrl/admin/student/edit/id/{{student_id}}\">Edit</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{student_id}}\"/>");
        $detailColumn = new Bvb_Grid_Extra_Column();
        $detailColumn->setPosition('right')->setName('Detail')->setDecorator("<a href=\"$baseUrl/admin/student/detail/id/{{student_id}}\">Detail</a><input class=\"address-id\" name=\"address_id[]\" type=\"hidden\" value=\"{{student_id}}\"/>");
        $deleteColumn = new Bvb_Grid_Extra_Column();
        $deleteColumn->setPosition('right')->setName('Delete')->setDecorator("<a class=\"delete-data\" href=\"$baseUrl/admin/student/delete/id/{{student_id}}\">Delete</a>");
        $grid->addExtraColumns($detailColumn, $editColumn, $deleteColumn);
        $grid->updateColumn('student_id', array('hidden' => true));
        $grid->updateColumn('del', array('hidden' => true));
        $grid->setRecordsPerPage(20);
        $grid->setPaginationInterval(array(
            5 => 5,
            10 => 10,
            20 => 20,
            30 => 30,
            40 => 40,
            50 => 50,
            100 => 100
        ));
        $grid->setExport(array('print', 'word', 'csv', 'excel', 'pdf'));
        $this->view->grid = $grid->deploy();
    }

    public function _listdata() {
        $menus = array();
        $menuModel = new Admin_Model_Student();
        $allMenus = $menuModel->listAll();
        foreach ($allMenus as $menu):
            $data = array();
            $data['student_id'] = $menu['student_id'];
            $data['roll_no'] = $menu['roll_no'];
            $data['full_name'] = $menu['full_name'];
            $data['phone'] = $menu['phone'];
            $data['year'] = $menu['year'];
            $data['grade'] = $menu['grade'];
            $menus[] = $data;
        endforeach;
        return $menus;
    }

    public function detailAction() {
        $id = $this->_getParam('id', 0);
        $studentModel = new Admin_Model_Student();
        $data = $studentModel->getDetailById($id);
        $feeTypeModel = new Admin_Model_Feetype();
        $this->view->feetypes = $feeTypeModel->getAllByGrades($data['grade'], $data['year']);
        $studentFeeModel = new Admin_Model_StudentFee();
        $this->view->fees = $studentFeeModel->getFeesByStudentId($id);
        $this->view->result = $data;
        $config = new Zend_Config_Ini(BASE_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'month.ini', 'nepali');
        $this->view->monthOptions = $config->month->toArray();
    }

    public function studentFilterAction() {
        $data = array(
            'year' => $this->_getParam("year"),
            'grade' => $this->_getParam("grade"),
            'section' => $this->_getParam("section"),
            'del' => 'N'
        );
        $studentModel = new Admin_Model_Student();
        $options = $studentModel->getStudentNames($data);
        $this->view->results = $options;
        $this->view->html = $this->view->render("student/student-filter.phtml");
    }

    public function upgradeAction() {
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
                $this->view->searchResults = $results;
            }
            if (in_array("Upgrade Class", $formData)) {
                if (array_key_exists('student', $formData)) {
                    if (count($formData['new_roll_no']) == count(array_unique($formData['new_roll_no']))) {

                        $data = array(
                            'year' => $formData['year'] + 1,
                            'grade' => $this->_helper->upgradeGrade($formData['grade'])
                        );

                        foreach ($formData['new_roll_no'] as $key => $val) {
                            if (!array_key_exists($key, $formData['student'])) {
                                unset($formData['new_roll_no'][$key]);
                            }
                        }
                        print "<pre>";
                        print_r($data);
                        print_r($formData);
                        exit;
                        //$studentModel->upgradeRollNo($formData['new_roll_no']);
                        $studentModel->upgrade($formData['new_roll_no'], $data);
                        $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully upgraded class"));
                        $this->_helper->redirector("list");
                    } else {
                        $this->_helper->FlashMessenger->addMessage(array("error" => "Same class can't have same roll number"));
                    }
                } else {
                    $this->_helper->FlashMessenger->addMessage(array("error" => "Click on the check button to upgrade students"));
                }
            }
        }
    }

    public function attendanceAction() {
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
                $this->view->searchResults = $results;
            }
            if (array_key_exists('student', $formData)) {
                try {
                    unset($formData['form-submit']);
                    unset($formData['year']);
                    unset($formData['grade']);
                    unset($formData['section']);
                    $studentAttendanceModel = new Admin_Model_StudentAttendance();
                    $studentAttendanceModel->add($formData);
                    $this->_helper->FlashMessenger->addMessage(array("success" => "Successfully done attendance"));
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    // var_dump($e->getMessage());
                    $this->_helper->FlashMessenger->addMessage(array("error" => "It seems like attendance of this day of these students is already done."));
                }
            } else {
                $this->_helper->FlashMessenger->addMessage(array("error" => "Atleast One student should be present."));
            }
        }
    }

    public function studentfeeDetailAction() {
        $studentFeeModel = new Admin_Model_Studentfee();
        $studentID = $this->_getParam("student");
//                $values = $this->_getAllParams();
//                var_dump($values);exit;
        $feeId = $this->_getParam("fee_id");
        $month = $this->_getParam("month");
        $amount = $this->_getParam("amount");
        $discount = $this->_getParam("discount");
        $feeTypeId = $this->_getParam("fee_type_id");
        $schoolFeeTypeModel = new Admin_Model_Feetype();
        $total = $schoolFeeTypeModel->getDetailById($feeTypeId);
        $totalAmount = $total['amount'];
        if ($feeId == 0) {
            $dueAmount = $totalAmount - $amount - $discount;
            $formData['amount'] = $totalAmount - $discount;
            $formData['student_id'] = $studentID;
            $formData['fee_type_id'] = $feeTypeId;
            $formData['month'] = $month;
            $formData['due'] = $dueAmount;
            if ($formData['due'] == 0) {
                $formData['paid'] = "Y";
            }
            $formData['discount'] = $discount;
            $studentFeeModel->add($formData);
        } else {
            $data = $studentFeeModel->getDetailById($feeId);
            $formData['amount'] = $data['amount'];
            $formData['due'] = $data['due'] - $amount - $discount;
            if ($formData['due'] == 0) {
                $formData['paid'] = "Y";
            }
            $formData['student_id'] = $studentID;
            $formData['fee_type_id'] = $feeTypeId;
            $formData['month'] = $month;
            $formData['discount'] = $discount;
            $studentFeeModel->update($formData, $feeId);
        }
    }

}
?>

