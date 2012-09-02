<?php

class AdminAction extends BaseAction {

    public static $method_array = array(
        "index" => "管理首页",
        "users" => "用户管理",
    );

    public function index() {
        $this->display();
    }

    public function users() {
        $this->display();
    }

}

?>
