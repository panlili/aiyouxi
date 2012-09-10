<?php

class FamilyAction extends BaseAction {

    public static $method_array = array(
        "index" => "受捐家庭管理",
        "survey" => "困难家庭调研",
    );

    public function index() {
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

}

?>
