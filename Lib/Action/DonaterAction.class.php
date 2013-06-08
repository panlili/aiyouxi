<?php

class DonaterAction extends BaseAction {

    public static $method_array = array(
        "index" => "捐赠人查询",
        "donaters" => "捐赠人管理"
    );

    public function donaters() {
        $m_donater = M("donater");
        $m_good = M("Good");
        import("ORG.Util.Page");
//如果是可以看所有站点的用户，提供所有站点的数据，特定站点的用户，提供特定站点的数据
        if (FALSE === is_all_user()) {
            $count = $m_donater->where("status=1 and location=" . session("locationid"))->count();
        } else {
            $count = $m_donater->where("status=1")->count();
        }

        $p = new Page($count, self::RECORDS_ONE_PAGE);
        $page = $p->show();
        //多站点改造
        if (FALSE === is_all_user()) {
            $donaterList = $m_donater->order("id desc")->where("status=1 and location=" . session("locationid"))->limit($p->firstRow . ',' . $p->listRows)->select();
        } else {
            $donaterList = $m_donater->order("id desc")->where("status=1")->limit($p->firstRow . ',' . $p->listRows)->select();
        }
        foreach ($donaterList as &$donater) {
            $donaterId = $donater["id"];
            $goodNumber = $m_good->where("donater_id='$donaterId'")->count();
            $donater["goodNumber"] = $goodNumber;
        }
        $this->assign("donaters", $donaterList);
        $this->assign("page", $page);
        $this->display();
    }

//获取捐赠者autocomplete, 接收donater name
    public function getDonaterList() {
        if ($this->isAjax()) {
            $text = $this->_param("serial");
            $m_donater = M("Donater");
            $map["name"] = array('like', "%" . $text . "%");
            $map["location"] = session("locationid");
            $list = $m_donater->field("serial,id,name,phone")->where($map)->select();
            if (is_null($list)) {
                $this->error("无此捐赠者,系统将自动跳到添加捐赠人页面");
            } else {
                $this->ajaxReturn($list);
            }
        } else {
            $this->redirect("Search/index");
        }
    }

//根据捐赠者姓名生成serial
    public function getPinyin() {
        $text = $this->_param("text");
        $text = strtoupper(Pinyin($text, 1, 1) . date("Ym"));
        $this->ajaxReturn($text, "", 1);
    }

    //获取单条数据的详细信息，返回的数据放在一个div里面作为模式对话框显示
    public function getOneDetail() {
        if ($this->isAjax()) {
            $id = $this->_param("id");
            $m_donater = M("Donater");
            $data = $m_donater->where("id=$id")->find();
            if (!empty($data)) {
                $this->assign("onedonaterdata", $data);
                $content = $this->fetch("_donater");
                $this->ajaxReturn($content, "数据获取成功", 1);
            } else {
                $this->error("数据不存在");
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    //输出捐赠者的详细信息，以及捐赠的物品
    public function printable() {
        $donaterid = $this->_param("id");
        $m_donater = M("Donater");
        $v_good = M("Good");
        $donaterdetail = $m_donater->where("id='$donaterid'")->find();
        if ($donaterdetail) {
            //$serial = $donaterdetail["serial"];
            $goodcount = $v_good->where("donater_id='$donaterid'")->count();
            if ($goodcount > 0) {
                $goods = $v_good->where("donater_id='$donaterid'")->select();
                $this->assign("goods", $goods);
            }
            $this->assign("goodcount", $goodcount);
            $this->assign("donater", $donaterdetail);
            $this->display();
        } else {
            $this->error("捐赠人数据不存在！");
        }
    }

}

?>
