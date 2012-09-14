<?php

class FamilyAction extends BaseAction {

    public static $method_array = array(
        "index" => "家庭首页",
        "families" =>"受捐家庭",
		"survey" => "困难家庭调研",
	);

	public function index(){
		$this->display();
	}
    public function families() {
        $m_family = M("Family");
        import("ORG.Util.Page");
        $count = $m_family->where("status=1 AND serial is not null")->count();
        $p = new Page($count, self::RECORDS_ONE_PAGE);
        $page = $p->show();
        $familyList = $m_family->order("id desc")->where("status=1 AND serial is not null")->limit($p->firstRow . ',' . $p->listRows)->select();
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

    //从调研家庭升级为受捐家庭
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

}

?>
