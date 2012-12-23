<?php

/**
 * Action helper to upgrade grade
 */
class School_Helper_UpgradeGrade extends Zend_Controller_Action_Helper_Abstract {

    protected $_grade;
    public $pluginLoader;

    public function __construct() {
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }

    public function upgradeGrade($grade) {
        $config = new Zend_Config_ini(BASE_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'class.ini', 'production');
        $gradeOptions = $config->grade->toArray();
        $option = array();
        foreach ($gradeOptions as $value) {
            $option[] = $value;
        }
        foreach ($option as $key => $val) {
            if ($val == $grade) {
                $this->_grade = (array_key_exists($key + 1, $option)) ? $option[$key + 1] : "Pass Out";
            }
        }
        return $this->_grade;
    }

    public function direct($grade) {
        return $this->upgradeGrade($grade);
    }

}