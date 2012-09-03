<?php

class AdminAction extends BaseAction {

    public static $method_array = array(
        "index" => "管理首页",
        "users" => "用户管理",
        "analyse" => "统计分析",
    );

    //这一页做什么用?
    public function index() {
        $this->display();
    }

    public function users() {
        $this->display();
    }

    public function analyse() {
        $this->display();
    }

}

?>
