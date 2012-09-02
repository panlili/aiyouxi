<?php

class FamilyAction extends BaseAction {

    public static $method_array = array(
        "index" => "受捐家庭管理首页",
    );

    public function index() {
        $this->display();
    }

}

?>
