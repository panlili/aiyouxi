<?php

class AdminAction extends BaseAction {

    public static $method_array = array(
        "index" => "系统管理首页",
        "users" => "用户管理",
        "analyse" => "统计分析",
        "recyle" => "数据回收站",
    );

    //这一页做什么用?
    public function index() {
        $this->display();
    }

    public function users() {
        $m_user = D("User");
        $userList = $m_user->order("id asc")->select();
        $this->assign("users", $userList);
        $this->display();
    }

    public function recyle() {
        $m_donater = M("Donater");
        $m_family = M("Family");
        $m_good = M("Good");
        $map["status"] = 0;

        $donaters = $m_donater->where($map)->select();
        $families = $m_family->where($map)->select();
        $goods = $m_good->where($map)->select();

        $this->assign("donaters", $donaters);
        $this->assign("families", $families);
        $this->assign("goods", $goods);
        $this->display();
    }

    public function analyse() {
        $this->display();
    }

    /**
     * 转换ayx_user表中status的值
     * @param userid
     * @return message,修改成功的数据的id
     */
    public function changeStatus() {
        if ($this->isAjax()) {
            $user_id = $this->_param("id");
            $m_user = M("User");
            $old_status = $m_user->where("id=$user_id")->getField("status");
            $new_status = $old_status == 0 ? 1 : 0;
            if (FALSE !== $m_user->where("id=$user_id")->setField("status", $new_status)) {
                $this->ajaxReturn($user_id, "数据修改成功", 1);
            } else {
                $this->error("数据修改失败");
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    //恢复数据，即将status变成1,包括donater,family,good
    public function recyleData() {
        if ($this->isAjax()) {
            $id = $this->_param("id");
            $modelName = $this->_param("model");
            $model = M($modelName);
            if (FALSE !== $model->where("id=$id")->setField("status", 1)) {
                $this->ajaxReturn($id, "数据恢复成功", 1);
            } else {
                $this->error("数据恢复失败");
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    //永久删除数据，包括donater,family,good
    public function deleteData() {
        if ($this->isAjax()) {
            $id = $this->_param("id");
            $modelName = $this->_param("model");
            $model = M($modelName);
            if (FALSE !== $model->where("id=$id")->delete()) {
                $this->ajaxReturn($id, "数据被永久删除", 1);
            } else {
                $this->error("数据删除失败");
            }
        } else {
            $this->redirect("Search/index");
        }
    }

}

?>
