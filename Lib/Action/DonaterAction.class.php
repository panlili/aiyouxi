<?php

class DonaterAction extends BaseAction {

    public static $method_array = array(
        "index" => "捐赠者首页",
        "donaters" => "捐赠者管理"
    );

    public function index() {
        $this->display();
    }

    public function donaters() {
        $m_donater = M("donater");
        import("ORG.Util.Page");
        $count = $m_donater->where("status=1")->count();
        $p = new Page($count, self::RECORDS_ONE_PAGE);
        $page = $p->show();
        $donaterList = $m_donater->order("id desc")->where("status=1")->limit($p->firstRow . ',' . $p->listRows)->select();
        $this->assign("donaters", $donaterList);
        $this->assign("page", $page);
        $this->display();
    }

    public function getDonaterList() {
        if ($this->isAjax()) {
            $m_donater = M("Donater");
            $list = $m_donater->field("id")->select();
            $this->ajaxReturn($list);
        }else{
            $this->redirect("Search/index");
        }
    }

}

?>
