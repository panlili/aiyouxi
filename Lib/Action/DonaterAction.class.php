<?php

class DonaterAction extends BaseAction {

    public static $method_array = array(
        "index" => "捐赠者首页",
        "donaters" => "捐赠者管理"
    );

    public function index() {
        $this->display();
    }

    public function donaters() {
        $m_donater = M("donater");
        $donaterList = $m_donater->order("id desc")->where("status=1")->select();
        $this->assign("donaters", $donaterList);
        $this->display();
    }

    /**
     * 删除数据功能，但不是物理删除，只是改变status的值
     * @param 数据的id
     * @return message
     */
    public function changeStatus() {
        if ($this->isAjax()) {
            $donater_id = $this->_param("id");
            $m_donater = D("Donater");
            if (FALSE !== $m_donater->where("id=$donater_id")->setField("status", 0)) {
                $this->ajaxReturn($donater_id, "数据删除成功", 1);
            } else {
                $this->error("数据修改失败");
            }
        } else {
            $this->redirect("index");
        }
    }

    public function addDonater() {
        if ($this->isAjax()) {
            $m_donater = D("Donater");
            if ($m_donater->create()) {
                if ($newid = $m_donater->add()) {
                    $this->assign("newdonater", $m_donater->find($newid));
                    $content = $this->fetch("_donater");
                    $this->ajaxReturn($content, "数据添加成功", 1);
                } else {
                    $this->error("写入数据库错误");
                }
            } else {
                $this->error($m_donater->getError());
            }
        } else {
            $this->redirect("index");
        }
    }

    public function getDonaterEditForm() {
        if ($this->isAjax()) {
            $m_donater = M("Donater");
            $d_donater = $m_donater->field("id,serial,name,phone,sex,comment")->find($this->_param("id"));
            if ($d_donater) {
                $this->assign("donaterdata", $d_donater);
                $content = $this->fetch("_donater");
                $this->ajaxReturn($content, "数据获取成功", 1);
            }
            $this->error("数据不存在");
        } else {
            $this->redirect("index");
        }
    }

    public function editDonater() {
        if ($this->isAjax()) {
            $m_donater = D("Donater");
            if ($m_donater->create()) {
                if ($m_donater->save()) {
                    $this->success("数据更新成功");
                } else {
                    $this->error("写入数据库错误");
                }
            } else {
                $this->error($m_donater->getError());
            }
        } else {
            $this->redirect("index");
        }
    }

}

?>
