<?php

class FamilyAction extends BaseAction {

    public static $method_array = array(
        "index" => "受助家庭查询",
        "families" => "受助家庭列表",
        "survey" => "困难家庭调研",
    );

    public function families() {
        $m_family = M("Family");
        $m_record = M("Record");
        import("ORG.Util.Page");
        $map["status"] = 1;
        $map["serial"] = array("neq", "");
        //多站点
        if (FALSE === is_all_user()){
            $map["location"]=array("eq",session("locationid"));
        }
        $count = $m_family->where($map)->count();
        $p = new Page($count, self::RECORDS_ONE_PAGE);
        $page = $p->show();
        $familyList = $m_family->order("id desc")->where($map)->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach ($familyList as &$family) {
            $familyId = $family["id"];
            $goodNumber = $m_record->where("family_id='$familyId'")->count();
            $family["goodNumber"] = $goodNumber;
        }
        $this->assign("families", $familyList);
        $this->assign("page", $page);
        $this->display();
    }

    public function printable() {
        $familyid = $this->_param("id");
        $m_family = M("Family");
        $v_fullgood = M("Fullgood");
        $familydetail = $m_family->where("id='$familyid'")->find();
        if ($familydetail) {
            $serial = $familydetail["serial"];
            $goodcount = $v_fullgood->where("familyserial='$serial'")->count();
            if ($goodcount > 0) {
                $goods = $v_fullgood->where("familyserial='$serial'")->select();
                $this->assign("goods", $goods);
            }
            $this->assign("goodcount", $goodcount);
            $this->assign("family", $familydetail);
            $this->display();
        } else {
            $this->error("家庭数据不存在！");
        }
    }

    public function survey() {
        $m_family = M("Family");
        import("ORG.Util.Page");

         if (FALSE === is_all_user()){
        $count = $m_family->where("status=1 and location=" . session("locationid"))->count();

        }  else {
            $count = $m_family->where("status=1")->count();
        }

        $p = new Page($count, self::RECORDS_ONE_PAGE);
        $page = $p->show();

        if (FALSE === is_all_user()){
            $familyList = $m_family->order("id desc")->where("status=1 and location=" . session("locationid"))->limit($p->firstRow . ',' . $p->listRows)->select();
        }  else {
            $familyList = $m_family->order("id desc")->where("status=1")->limit($p->firstRow . ',' . $p->listRows)->select();
        }

        $this->assign("families", $familyList);
        $this->assign("page", $page);
        $this->display();
    }

    //添加数据
    public function add() {
        if (!empty($_FILES["photo"]["name"])) {
            $this->_uploadPhoto();
        }
        $model = D("Family");
        if ($model->create()) {
            if ($newid = $model->add()) {
                $this->success("调研家庭数据添加成功！");
            } else {
                $this->error("写入数据库错误");
            }
        } else {
            $this->error($model->getError());
        }
    }

    //修改数据
    public function edit() {
        if (!empty($_FILES["photo"]["name"])) {
            $this->_uploadPhoto();
        }
        $model = D("Family");
        if ($model->create()) {
            if ($model->save()) {
                $this->success("数据更新成功");
            } else {
                $this->error("写入数据库错误");
            }
        } else {
            $this->error($model->getError());
        }
    }

    protected function _uploadPhoto() {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        $upload->allowExts = explode(",", "jpg,gif,png,jpeg");
        $upload->maxSize = 4292200;

        $upload->savePath = "./Resource/Uploads/Family/";
        $upload->thumb = true;
        $upload->imageClassPath = "ORG.Util.Image";
        //两张图
        $upload->thumbPrefix = "m_,s_";
        $upload->thumbMaxWidth = "600,100";
        $upload->thumbMaxHeight = "600,100";
        $upload->thumbRemoveOrigin = true;
        $upload->saveRule = uniqid;
        if (!$upload->upload()) {
            $this->error($upload->getErrorMsg());
        } else {
            $uploadList = $upload->getUploadFileInfo();
            $_POST["photo"] = $uploadList[0]['savename'];
        }
    }

    //获取单条数据的详细信息，返回的数据放在一个div里面作为模式对话框显示
    public function getOneDetail() {
        if ($this->isAjax()) {
            $id = $this->_param("id");
            $m_family = M("Family");
            $data = $m_family->where("id=$id")->find();
            if (!empty($data)) {
                $this->assign("onefamilydata", $data);
                $content = $this->fetch("_family");
                $this->ajaxReturn($content, "数据获取成功", 1);
            } else {
                $this->error("数据不存在");
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    //从调研家庭升级为受助家庭
    public function setSerial() {
        if ($this->isAjax()) {
            $id = $this->_param("id");
            $text = strtoupper($this->_param("text"));
            $m_family = D("Family");
            $isexist = $m_family->where("serial='$text'")->select();
            if ($isexist) {
                $this->error("此序号已存在");
            } else {
                if (FALSE !== $m_family->where("id=$id")->setField("serial", $text)) {
                    $this->success("设置成功");
                } else {
                    $this->error("写入数据库失败");
                }
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    //获取家庭列表autocomplete，接收family agent
    public function getFamilyList() {
        if ($this->isAjax()) {
            $text = $this->_param("serial");
            $m_family = M("Family");
            $map["agent"] = array('like', "%" . $text . "%");
            $map["location"]=session("locationid");
            $list = $m_family->field("serial,id,agent")->where($map)->select();
            if (is_null($list)) {
                $this->error("无此家庭或未分配家庭编号,请确认家庭信息,跳转到相关页面？");
            } else {
                $this->ajaxReturn($list);
            }
        } else {
            $this->redirect("Search/index");
        }
    }

}

?>
