<?php

class SearchAction extends Action {

    /**
     * 对外查询页面
     * @author libiun libiun@gmail.com
     */
    public function index() {
        $this->display();
    }

    //公共查询,先查物资，如果没有命中则查捐赠人
    public function search() {
        if ($this->isAjax()) {
            load('extend');

            $search_key_tmp = trim($this->_param("searchkey"));
            if ($search_key_tmp != "") {
                $_SESSION['search_key'] = $search_key_tmp;
            }
            $search_key = $_SESSION['search_key'];
            //$search_key="SF";
            $map['goodserial'] = array('EQ', $search_key);
            $model = M("Fullgood");
            $result = $model->where($map)->select();
            if ($result == null) {
                //查询物资结果没有命中，则查捐赠人
                $condition['donaterserial'] = array('EQ', $search_key);
                $count = $model->where($condition)->count();
                if ($count == 0) {
                    $this->ajaxReturn("", "没有命中记录", 1);
                } else {//捐赠人命中，输出捐赠人相关信息
                    $donatername = $model->where($condition)->field("donatername,donatersex")->find();

                    import("@.ORG.Pagea");
                    $p = new Pagea($count, 10, '', 'index_result', 'pages1');
                    $result = $model->where($condition)->limit($p->firstRow . ',' . $p->listRows)->select();
                    $p->setConfig('header', '条记录');
                    $p->setConfig('prev', "<");
                    $p->setConfig('next', '>');
                    $p->setConfig('first', '<<');
                    $p->setConfig('last', '>>');
                    $page = $p->show();

                    $this->assign("page", $page); //page code
                    $this->assign("donaterserial", $search_key);
                    $this->assign("donatersex", $donatername["donatersex"]);
                    $this->assign("donatername", $donatername["donatername"]);
                    $this->assign("index_result2", $result);
                    $content = $this->fetch("_result");
                    $this->ajaxReturn($content, "", 1);
                }
            } else {//物资编号命中，输出物资相关信息
                $this->assign("index_result", $result);
                $content = $this->fetch("_result");
                $this->ajaxReturn($content, "数据获取成功", 1);
            }
        } else {
            $this->redirect($this->getActionName() . "/index");
        }
    }

    //下面action都是跟登陆相关,省去再造一个module
    public function login() {
        if (session("?truename")) {
            $this->redirect("Good/index");
        }
        $this->display();
    }

    public function verify() {
        import('ORG.Util.Image');
        Image::buildImageVerify(4, 1, "png");
    }

    public function checkUser() {
        if (empty($_POST['username'])) {
            $this->error("帐号必须");
        } elseif (empty($_POST['password'])) {
            $this->error("密码必须");
        }

        if ($_SESSION["verify"] !== md5($_POST["verify"])) {
            $this->error("验证码错误！");
        }

        $m_user = M('User');
        $condition["username"] = $_POST["username"];
        $condition["password"] = md5($_POST["password"]);
        $condition["status"] = "1";

        $result = $m_user->where($condition)->find();
        if ($result) {
            session("uid", $result["id"]);
            session("username", $result["username"]);
            session("truename", $result["truename"]);
            session("right", $result["right"]);
            //保存登录信息
            $data['id'] = $result['id'];
            $data['lastlogintime'] = time();
            $data['logincount'] = array('exp', 'logincount+1');
            $data['lastloginip'] = get_client_ip();
            $m_user->save($data);

            $this->redirect("Retrieval/index");
        } else {
            $this->error("验证失败或帐号已禁用！");
        }
    }

    public function logout() {
        //清除用户信息缓存
        F("login_user" . $_SESSION["uid"], null);
        session(null);
        session_destroy();
        $this->redirect("Search/index");
    }

    public function _empty() {
        $this->redirect("Search/index");
    }

}

?>
