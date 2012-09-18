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

    public function getCheckoutGood() {
        if ($this->isAjax()) {
            $serial = $this->_param("serial");
            $m_good = M("Good");
            //与扫描条码一致，且step=1(库存中状态)
            $good = $m_good->where("serial='$serial' AND step=1")->find();
            if (false != $good) {
                $this->assign("good", $good);
                $content = $this->fetch("_good");
                $this->ajaxReturn($content, "", 1);
            } else {
                $this->ajaxReturn("", "物资非库存状态或条码数据不存在！", 0);
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    public function doCheckout() {
        if ($this->isAjax()) {
            //同一物资多次扫描去重复id
            $ids = array_unique($this->_param("ids"));
            $checkoutman = $this->_param("checkoutman");
            $m_good = D("Good");
            $error = 0;
            foreach ($ids as $id) {
                $data = array("id" => $id, "checkouttime" => time(), "checkoutman" => $checkoutman, "step" => 2);
                if (fales == $m_good->where("id='$id'")->setField($data)) {
                    $error++;
                }
            }
            if ($error !== 0) {
                $this->error("写入数据库错误");
            } else {
                $this->success(count($ids) . "件物资出库成功！");
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    public function rollback() {
        //一次只能撤销一条
        if ($this->isAjax()) {
            $serial = $this->_param("serial");
            $m_good = D("Good");
            $data = array("checkoutman" => null, "checkouttime" => null, "step" => 1);
            if ($m_good->where("serial='$serial'")->setField($data)) {
                $this->success("条码为：" . $serial . " 的物资撤销出货成功");
            } else {
                $this->error("无此条码物品");
            }
        } else {
            $this->redirect("Search/index");
        }
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

    public function getGoodList() {
        if ($this->isAjax()) {
            $text = $this->_param("serial");
            $m_good = M("Good");
            $map["serial"] = array('like', "%" . $text . "%");
            $map["step"] = 2;
            $list = $m_good->field("serial,id,name,number,unit")->where($map)->select();
            if (is_null($list)) {
                $this->error("无此物资或物资非出库状态,请先确认物资,跳转到相关页面？");
            } else {
                $this->ajaxReturn($list);
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    //领物回来添加record数据
    public function addRecord() {
        if ($this->isAjax()) {
            $m_record = D("Record");
            $m_good = D("Good");
            $goodids = preg_split("/,/", trim($this->_param("good_id")));
            foreach ($goodids as &$value) {
                //强制去除元素前后的空格
                $value = (int) ($value);
            }
            $wellids = array_filter(array_unique($goodids));

            //至此，good_id表单的数据处理完毕，全是数字id
            if (0 == count($wellids)) {
                $this->error("请填写物资");
            }

            $errors = array();
            $success = array();
            foreach ($wellids as $goodid) {
                $_POST["good_id"] = $goodid;
                if (!$m_record->create()) {
                    $errors[] = "物资ID为" . $goodid . "的数据出错：" . $m_record->getError() . "<br/>";
                    break;
                } else {
                    $m_record->add();
                    $m_good->where("id='$goodid'")->setField("step", 3);
                    $success[] = "物资ID为" . $goodid . "的数据领用登记成功<br/>";
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

}

?>
