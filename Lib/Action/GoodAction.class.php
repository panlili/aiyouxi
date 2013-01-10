<?php

class GoodAction extends BaseAction {

    public static $method_array = array(
        "index" => "物资信息查询",
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
            //根据表单donater_name的值查询是否有此名的捐赠人存在
            //如果有获取捐赠人的id，否则插入一条记录并获取id
            $donater_name = empty($_POST["donater_name"]) ? "匿名捐赠人" : trim($_POST["donater_name"]);
            $m_donater = M("Donater");
            $donater_id = $m_donater->where("name='$donater_name'")->getField("id");
            if (!is_numeric($donater_id)) {
                $data["name"] = $donater_name;
                $data["status"] = 1;
                $data["handman"] = get_session_user_id();
                $data["addtime"] = time();
                $donater_id = $m_donater->data($data)->add();
            }
            //至此根据表单输入获取或添加数据的donater id以存放在$donater_id中
            $m_good = D("Good");
            $serial = strtoupper(trim($_POST["serial"]));
            $serial_array = preg_split('/[\s]+/', $serial);
            $errors = array();
            $success = array();
            //支持同一类物品同时输入多个条码号，统一添加数据
            foreach ($serial_array as $s) {
                $_POST["serial"] = $s;
                $_POST["donater_id"] = $donater_id;
                if (!$m_good->create()) {
                    $errors[] = "编号为" . $s . "的数据出错：" . $m_good->getError() . "<br/>";
                    break;
                } else {
                    $m_good->add();
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
            if ($m_good->where("serial='$serial' AND step=2")->setField($data)) {
                $this->success("条码为：" . $serial . " 的物资撤销出货成功");
            } else {
                $this->error("无此条码物品或物品非出库状态！");
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    public function endgood() {
        $this->display();
    }

    public function goods() {
        $m_good = D("Good");
        import("ORG.Util.Page");
        //在库物资,step=1
        $count1 = $m_good->where("status=1 AND step=1")->count();
        $p1 = new Page($count1, self::RECORDS_ONE_PAGE);
        $page1 = $p1->show();
        $goodList1 = $m_good->order("id desc")->where("status=1 AND step=1")->limit($p1->firstRow . ',' . $p1->listRows)->select();
        $this->assign("goods1", $goodList1);
        $this->assign("page1", $page1);
        //出库物资,step=2
        $count2 = $m_good->where("status=1 AND step=2")->count();
        $p2 = new Page($count2, self::RECORDS_ONE_PAGE);
        $page2 = $p2->show();
        $goodList2 = $m_good->order("checkouttime desc")->where("status=1 AND step=2")->limit($p2->firstRow . ',' . $p2->listRows)->select();
        $this->assign("goods2", $goodList2);
        $this->assign("page2", $page2);
        //已捐物资,step=3
        $count3 = $m_good->where("status=1 AND step=3")->count();
        $p3 = new Page($count3, self::RECORDS_ONE_PAGE);
        $page3 = $p3->show();
        $goodList3 = $m_good->relation("record")->order("uptime desc")->where("status=1 AND step=3")->limit($p3->firstRow . ',' . $p3->listRows)->select();
        $this->assign("goods3", $goodList3);
        $this->assign("page3", $page3);

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
            //领用家庭姓名要求输名字，不用serial，故添加内容获取family id
            $family_agent = empty($_POST["family_agent"]) ? "匿名受助家庭" : trim($_POST["family_agent"]);
            $m_family = M("Family");
            $family_id = $m_family->where("agent='$family_agent'")->getField("id");
            if (!is_numeric($family_id)) {
                $data["agent"] = $family_agent;
                $data["status"] = 1;
                $data["handman"] = get_session_user_id();
                $data["addtime"] = time();
                $family_id = $m_family->data($data)->add();
            }
            //family id获取结束
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

            $m_record = D("Record");
            $m_good = D("Good");
            $errors = array();
            $success = array();
            foreach ($wellids as $goodid) {
                $_POST["good_id"] = $goodid;
                $_POST["family_id"] = $family_id;
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

    //领物登记后的物资信息修改
    public function getDoneEditForm() {
        if ($this->isAjax()) {
            $m_record = D("Record");
            $good_id = $this->_param("id");
            $oldrecord = $m_record->where("good_id='$good_id'")->find();
            if ($oldrecord) {
                $this->assign("oldrecord", $oldrecord);
                $content = $this->fetch("_good");
                $this->ajaxReturn($content, "数据获取成功", 1);
            }
            $this->error("数据不存在");
        } else {
            $this->redirect("Search/index");
        }
    }

    public function editRecord() {
        if ($this->isAjax()) {
            $m_record = D("Record");
            if ($m_record->create()) {
                $m_record->save();
                $this->success("数据修改成功");
            } else {
                $this->error($m_record->getError());
            }
        } else {
            $this->redirect("Search/index");
        }
    }

}

?>
