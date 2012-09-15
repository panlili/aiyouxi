<?php

class GoodAction extends BaseAction {

    public static $method_array = array(
        "index" => "物资首页",
        "checkin" => "入库登记",
        "checkout" => "出库登记",
        "endgood" => "领用登记",
        "goods" => "物资管理"
    );

    public function index() {
        $this->display();
    }

    public function checkin() {
        $this->display();
    }

    //因为不用返回新添加数据ajax更新，故需重写此方法
    public function add() {
        if ($this->isAjax()) {
            $model = D("Good");
            if ($model->create()) {
                if ($model->add()) {
                    $this->success("数据添加成功");
                } else {
                    $this->error("写入数据库错误");
                }
            } else {
                $this->error($model->getError());
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    public function checkout() {
        $this->display();
    }

    public function endgood() {
        $this->display();
    }

    public function goods() {
        $m_good = M("Good");
        import("ORG.Util.Page");
        $count = $m_good->where("status=1")->count();
        $p = new Page($count, self::RECORDS_ONE_PAGE);
        $page = $p->show();
        $goodList = $m_good->order("id desc")->where("status=1")->limit($p->firstRow . ',' . $p->listRows)->select();
        $this->assign("goods", $goodList);
        $this->assign("page", $page);
        $this->display();
    }

}

?>
