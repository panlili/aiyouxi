<?php

class FamilyAction extends BaseAction {

    public static $method_array = array(
        "index" => "受捐家庭管理首页",
        "survey" => "困难家庭调研",
    );

    public function index() {
        $this->display();
    }

    public function survey() {
        $this->display();
    }

}

?>
