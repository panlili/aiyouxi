<?php

class DonaterAction extends BaseAction {

    public static $method_array = array(
        "index" => "捐赠者管理首页",
    );

    public function index() {
        $m_d = M("donater");
        $dList = $m_d->order("id asc")->select();
        $this->assign("donater", $dList);
        $this->display();
    }
    
    public function newone(){
        $this->display();
    }
    
        /**
     * 添加ayx_donater表中数据
     * @param form的值
     * @return message
     */
    public function add() {
        if ($this->isAjax()) {
//             $this->redirect("donater/index");
//            echo "fuck";
            $m_d = M("donater");
            if ($m_d->create()) {
                if ($m_d->add()) {
                    $this->success("数据添加成功");
                } else {
                    $this->error("写入数据库错误");
                }
            } else {
                $this->error($m_d->getError());
            }
        } else {
            $this->redirect("donater/index");
        }
    }
    /**
     * 转换ayx_donater表中status的值
     * @param userid
     * @return message
     */
    public function changeStatus() {
        if ($this->isAjax()) {
            $d_id = $_GET["id"];
            $m_user = M("donater");
            $old_status = $m_user->where("id=$d_id")->getField("status");
            $new_status = $old_status == 0 ? 1 : 0;
            if (FALSE !== M("donater")->where("id=$d_id")->setField("status", $new_status)) {
                $this->success("数据修改成功");
            }
        } else {
            $this->redirect("donater/index");
        }
    }

}

?>
