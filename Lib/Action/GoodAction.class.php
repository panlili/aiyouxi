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
            $serial = strtoupper(trim($_POST["serial"]));
            $serial_array = preg_split('/[\s]+/', $serial);
            $errors = array();
            $success = array();
            //支持同一类物品同时输入多个条码号，统一添加数据
            foreach ($serial_array as $s) {
                $_POST["serial"] = $s;
                if (!$model->create()) {
                    $errors[] = "编号为" . $s . "的数据出错：" . $model->getError() . "<br/>";
                    break;
                } else {
                    $model->add();
                    $success[] = "编号为" . $s . "的数据添加成功</br>";
                }
            }

            if (0 === count($errors)) {
                $message = implode("", $success);
                $this->success($message);
            } else {
                $message = implode("", $success) . implode("", $errors);
                $this->error($message);
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
