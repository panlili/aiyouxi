<?php

class DonaterAction extends BaseAction {

    public static $method_array = array(
        "index" => "捐赠者管理首页",
    );

    public function index() {
        $this->display();
    }

}

?>
