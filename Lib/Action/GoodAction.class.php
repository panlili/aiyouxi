<?php

class GoodAction extends BaseAction {

    public static $method_array = array(
        "index" => "物资管理首页",
        "checkin" => "入库登记",
        "checkout" => "出库登记",
        "endgood" => "领用登记"
    );

    public function index() {
        $this->display();
    }

    public function checkin() {
        $this->display();
    }

    public function checkout() {
        $this->display();
    }

    public function endgood() {
        $this->display();
    }

}

?>
