<?php

class AdminAction extends BaseAction {

    public static $method_array = array(
        "index" => "管理首页",
        "users" => "用户管理",
        "analyse" => "统计分析",
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

    /**
     * 添加ayx_user表中用户数据
     * @param form的值
     * @return message,添加成功后的数据的值(表格中的一个tr行)
     */
    public function addUser() {
        if ($this->isAjax()) {
            $m_user = D("User");
            if ($m_user->create()) {
                if ($newid = $m_user->add()) {
                    $this->assign("newuser", $m_user->find($newid));
                    $content = $this->fetch("_user");
                    $this->ajaxReturn($content, "数据添加成功", 1);
                } else {
                    $this->error("写入数据库错误");
                }
            } else {
                $this->error($m_user->getError());
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    /**
     * 获取修改数据的表单，表单中填上了值
     * @param userid
     * @return message,修改数据的表单
     */
    public function getUserEditForm() {
        if ($this->isAjax()) {
            $m_user = M("User");
            $d_user = $m_user->field("id,username,truename,right,comment")->find($this->_param("id"));
            if ($d_user) {
                $this->assign("userdata", $d_user);
                $content = $this->fetch("_user");
                $this->ajaxReturn($content, "数据获取成功", 1);
            }
            $this->error("数据不存在");
        } else {
            $this->redirect("Search/index");
        }
    }

    /**
     * 修改ayx_user表中用户数据
     * @param form的值
     * @return message
     */
    public function editUser() {
        if ($this->isAjax()) {
            $m_user = D("User");
            if ($m_user->create()) {
                if ($m_user->save()) {
                    $this->success("数据更新成功");
                } else {
                    $this->error("写入数据库错误");
                }
            } else {
                $this->error($m_user->getError());
            }
        } else {
            $this->redirect("Search/index");
        }
    }

}

?>
