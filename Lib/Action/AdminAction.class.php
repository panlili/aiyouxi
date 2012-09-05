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
     * @return message
     */
    public function changeStatus() {
        if ($this->isAjax()) {
            $user_id = $_GET["id"];
            $m_user = M("User");
            $old_status = $m_user->where("id=$user_id")->getField("status");
            $new_status = $old_status == 0 ? 1 : 0;
            if (FALSE !== M("User")->where("id=$user_id")->setField("status", $new_status)) {
                $this->success("数据修改成功");
            }
        } else {
            $this->redirect("Admin/users");
        }
    }

}

?>
