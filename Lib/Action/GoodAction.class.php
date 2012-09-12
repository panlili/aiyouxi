<?php

class GoodAction extends BaseAction {

    public static $method_array = array(
        
        "checkin" => "入库登记",
        "checkout" => "出库登记",
        "endgood" => "领用登记",
        "index" => "物资管理"
    );

    public function index() {
        $m_good = M("Good");
        import("ORG.Util.Page");
        $count = $m_good->count();
        $p = new Page($count, self::RECORDS_ONE_PAGE);
        $page = $p->show();
        $goodList = $m_good->order("id desc")->limit($p->firstRow . ',' . $p->listRows)->select();
        $this->assign("goods", $goodList);
        $this->assign("page", $page);
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
