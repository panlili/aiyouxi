<?php

class DonaterAction extends BaseAction {

    public static $method_array = array(
        "index" => "捐赠人查询",
        "donaters" => "捐赠人管理"
    );

    public function index() {
        $this->display();
    }

    public function donaters() {
        $m_donater = M("donater");
        $m_good = M("Good");
        import("ORG.Util.Page");
        $count = $m_donater->where("status=1")->count();
        $p = new Page($count, self::RECORDS_ONE_PAGE);
        $page = $p->show();
        $donaterList = $m_donater->order("id desc")->where("status=1")->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach ($donaterList as &$donater) {
            $donaterId = $donater["id"];
            $goodNumber = $m_good->where("donater_id='$donaterId'")->count();
            $donater["goodNumber"] = $goodNumber;
        }
        $this->assign("donaters", $donaterList);
        $this->assign("page", $page);
        $this->display();
    }

    public function getDonaterList() {
        if ($this->isAjax()) {
            $text = $this->_param("serial");
            $m_donater = M("Donater");
            $map["serial"] = array('like', "%" . $text . "%");
            $list = $m_donater->field("serial,id,name")->where($map)->select();
            if (is_null($list)) {
                $this->error("无此捐赠者,请先添加捐赠者,跳转到相关页面？");
            } else {
                $this->ajaxReturn($list);
            }
        } else {
            $this->redirect("Search/index");
        }
    }

}

?>
