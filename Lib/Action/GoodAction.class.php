<?php

class GoodAction extends BaseAction {

    public static $method_array = array(
        "index" => "物资管理首页",
    );

    public function index() {
        $this->display();
    }

}

?>
