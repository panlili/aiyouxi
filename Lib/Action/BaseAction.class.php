<?php

class BaseAction extends Action {

    const RECORDS_ONE_PAGE = 25;

    public function _initialize() {
        if (!session("?truename") || !session("?uid")) {
            $this->redirect("Search/index");
        } else {
            if (session("?right")) {
                $user_hole = session("right");
                $flag = $this->check_user_right($user_hole, MODULE_NAME, ACTION_NAME);
                if (0 == $flag) {
                    $this->error("您没有执行此操作的权限！");
                }
            } else {
                $this->redirect("Search/login");
            }
        }
    }

    //根据用户角色判断用户权限
    protected function check_user_right($user_hole, $modulename, $actionname) {
        //注意，配置数组里面的方法名应该全部小写。因为ACTION_NAME获取的字符串是全部小写的。
        $right_list = array(
            //普通工作人员
            "0" => array(
                "Admin" => array("changepassword"),
                "Donater" => array("index", "donaters", "getdonaterlist", "add", "geteditform", "edit", "unitsearch"),
                "Family" => array("index", "families", "survey", "getonedetail", "setserial", "getfamilylist",
                    "add", "geteditform", "edit", "unitsearch"),
                "Good" => array("index", "checkin", "add", "checkout", "getcheckoutgood", "docheckout", "rollback",
                    "endgood", "goods", "getgoodlist", "addrecord", "geteditform", "edit", "unitsearch"),
                "Retrieval" => array("index", "query")
            ),
            //管理员
            "1" => array(
                "Admin" => array("index", "analyse", "tongji", "changepassword", "recyle", "recyledata",),
                "Donater" => array("index", "donaters", "getdonaterlist", "add", "geteditform",
                    "edit", "changestatus", "unitsearch"),
                "Family" => array("index", "families", "survey", "getonedetail", "setserial",
                    "getfamilylist", "add", "geteditform", "edit", "changestatus", "unitsearch"),
                "Good" => array("index", "checkin", "add", "checkout", "getcheckoutgood", "docheckout", "rollback",
                    "endgood", "goods", "getgoodlist", "addrecord", "geteditform", "edit", "changestatus", "unitsearch"),
                "Retrieval" => array("index", "query", "toexcel")
            ),
            //超级管理员
            "2" => array(
                "Admin" => array("index", "users", "analyse", "tongji", "changestatus", "changepassword",
                    "recyle", "recyledata", "deletedata", "add", "geteditform", "edit"),
                "Donater" => array("index", "donaters", "getdonaterlist", "add", "geteditform", "edit",
                    "changestatus", "unitsearch"),
                "Family" => array("index", "families", "survey", "getonedetail", "setserial",
                    "getfamilylist", "add", "geteditform", "edit", "changestatus", "unitsearch"),
                "Good" => array("index", "checkin", "add", "checkout", "getcheckoutgood", "docheckout", "rollback",
                    "endgood", "goods", "getgoodlist", "addrecord", "geteditform", "edit", "changestatus", "unitsearch"),
                "Retrieval" => array("index", "query", "toexcel")
            )
        );
        $flag = 0;
        if (array_key_exists($user_hole, $right_list)) {
            $right_list_module = $right_list[$user_hole];
            if (array_key_exists($modulename, $right_list_module)) {
                $right_list_action = $right_list_module[$modulename];
                if (in_array($actionname, $right_list_action)) {
                    $flag = 1;
                } else {
                    $flag = 0;
                }
            } else {
                $flag = 0;
            }
        } else {
            $flag = 0;
        }
        //返回结果，1表示合法，0表示非法
        return $flag;
    }

    public function _empty() {
        $this->redirect($this->getActionName() . "/index");
    }

    //curd相关的操作方法
    //添加数据，create。FamilyAction Override
    public function add() {
        if ($this->isAjax()) {
            //user的操作是在AdminAction里做的，故要特别判断一下
            $action_name = $this->getActionName() != "Admin" ? $this->getActionName() : "User";
            $model = D($action_name);
            if ($model->create()) {
                if ($newid = $model->add()) {
                    $this->assign("newdata", $model->find($newid));
                    $content = $this->fetch("_" . strtolower($action_name));
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

    //读取数据->填充表单，read
    public function getEditForm() {
        if ($this->isAjax()) {
            //user的操作是在AdminAction里做的，故要特别判断一下
            $action_name = $this->getActionName() != "Admin" ? $this->getActionName() : "User";
            $model = M($action_name);
            $olddata = $model->find($this->_param("id"));
            if ($olddata) {
                $this->assign("olddata", $olddata);
                $content = $this->fetch("_" . strtolower($action_name));
                $this->ajaxReturn($content, "数据获取成功", 1);
            }
            $this->error("数据不存在");
        } else {
            $this->redirect("Search/index");
        }
    }

    //更新数据，update
    public function edit() {
        if ($this->isAjax()) {
            //user的操作是在AdminAction里做的，故要特别判断一下
            $action_name = $this->getActionName() != "Admin" ? $this->getActionName() : "User";
            $model = D($action_name);
            if ($model->create()) {
                if ($model->save()) {
                    $this->success("数据更新成功");
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

    //删除数据，不直接删除，通过status字段隐藏数据，delete
    //用户管理模块，AdminAction Override此方法
    public function changeStatus() {
        if ($this->isAjax()) {
            $id = $this->_param("id");
            $model = D($this->getActionName());
            if (FALSE !== $model->where("id=$id")->setField("status", 0)) {
                $this->ajaxReturn($id, "数据删除成功", 1);
            } else {
                $this->error("数据修改失败");
            }
        } else {
            $this->redirect("Search/index");
        }
    }

    //各个模型中的搜索方法
    public function unitSearch() {
        if ($this->isAjax()) {
            $action_name = $this->getActionName();
            $searchfield = $this->_param("search_field");
            $search_key = trim($this->_param("search_key"));
            $model = D($action_name);
            $tmp = $searchfield . " like '%" . $search_key . "%'";
            if ($searchfield != "") {
                $_SESSION['sKey'] = $tmp;
            }
            $tmp = $_SESSION['sKey'];
            $count = $model->where($tmp)->count();
            if ($count == 0) {
                $this->ajaxReturn("", "数据获取成功", 1);
            } else {
                import("@.ORG.Pagea");
                $p = new Pagea($count, self::RECORDS_ONE_PAGE, 'type=1', 'search_result', 'pages1');
                $data = $model->where($tmp)->limit($p->firstRow . ',' . $p->listRows)->select();
                $p->setConfig('header', '条记录');
                $p->setConfig('prev', "<");
                $p->setConfig('next', '>');
                $p->setConfig('first', '<<');
                $p->setConfig('last', '>>');
                $page = $p->show();

                $this->assign("page", $page);
                $this->assign("datalist", $data);

                $content = $this->fetch("_" . strtolower($action_name));
                $this->ajaxReturn($content, "数据获取成功", 1);
            }
        } else {
            $this->redirect($this->getActionName() . "/index");
        }
    }

}

?>
