<?php
if (!class_exists("wpdreamsTextareaBase64")) {
    /**
     * Class wpdreamsTextarea
     *
     * A simple textarea field.
     *
     * @package  WPDreams/OptionsFramework/Classes
     * @category Class
     * @author Ernest Marcinko <ernest.marcinko@wp-dreams.com>
     * @link http://wp-dreams.com, http://codecanyon.net/user/anago/portfolio
     * @copyright Copyright (c) 2014, Ernest Marcinko
     */
    class wpdreamsTextareaBase64 extends wpdreamsType {
        function getType() {
            parent::getType();
            $this->pdata = "";
            $this->processData();
            echo "<label style='vertical-align: top;' for='wpdreamstextarea_" . self::$_instancenumber . "'>" . $this->label . "</label>";
            echo "<input type='hidden' name='base64-" . $this->name . "' id='base64-" . $this->name . "' />";
            echo "<textarea id='wpdreamstextarea_" . self::$_instancenumber . "' name='" . $this->name . "'>" . stripcslashes($this->pdata) . "</textarea>";
        }
        
        function processData() {
            $this->pdata = isset($_POST[$this->name])?$this->data:base64_decode($this->data);
        }
    }
}