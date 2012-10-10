<?php

class FamilyAction extends BaseAction {

    public static $method_array = array(
        "index" => "受助家庭查询",
        "families" => "受助家庭列表",
        "survey" => "困难家庭调研",
    );

    public function index() {
        $this->display();
    }

    public function families() {
        $m_family = M("Family");
        $m_record = M("Record");
        import("ORG.Util.Page");
        $map["status"] = 1;
        $map["serial"] = array("neq", "");
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

    public function survey() {
        $m_family = M("Family");
        import("ORG.Util.Page");
        $count = $m_family->where("status=1")->count();
        $p = new Page($count, self::RECORDS_ONE_PAGE);
        $page = $p->show();
        $familyList = $m_family->order("id desc")->where("status=1")->limit($p->firstRow . ',' . $p->listRows)->select();
        $this->assign("families", $familyList);
        $this->assign("page", $page);
        $this->display();
    }

    //添加数据
    public function add() {
        if ($this->isAjax()) {
            $model = D("Family");
            if (!empty($_FILES)) {
                $this->_uploadPhoto();
            }
            if ($model->create()) {
                if ($newid = $model->add()) {
                    $this->assign("newdata", $model->find($newid));
                    $content = $this->fetch("_family");
                    $this->ajaxReturn($content, "数据添加成功", 1);
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

    protected function _uploadPhoto() {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        $upload->maxSize = 3292200;
        $upload->allowExts = explode(",", "jpg,gif,png,jpeg");
        $upload->savePath = "./Resource/Uploads/Family/";
        $upload->thumb = true;
        $upload->imageClassPath = "ORG.Util.Image";
        //两张图
        $upload->thumbPrefix = "m_,s_";
        $upload->thumbMaxWidth = "400,100";
        $upload->thumbMaxHeight = "400,100";
        $upload->thumbRemoveOrigin = true;
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
            $text = $this->_param("text");
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

    //获取家庭列表，endgood页面调用
    public function getFamilyList() {
        if ($this->isAjax()) {
            $text = $this->_param("serial");
            $m_family = M("Family");
            $map["serial"] = array('like', "%" . $text . "%");
            $list = $m_family->field("serial,id,agent")->where($map)->select();
            if (is_null($list)) {
                $this->error("无此家庭或为分配家庭编号,请确认家庭信息,跳转到相关页面？");
            } else {
                $this->ajaxReturn($list);
            }
        } else {
            $this->redirect("Search/index");
        }
    }

}

?>
