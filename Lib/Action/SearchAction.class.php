<?php

class SearchAction extends Action {

    /**
     * 对外查询页面
     * @author libiun libiun@gmail.com
     */
    public function index() {
        $this->display();
    }

    //下面action都是跟登陆相关,省去再造一个module
    public function login() {
        if (session("?truename")) {
            $this->redirect("Admin/index");
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

            $this->redirect("Admin/index");
        } else {
            $this->error("验证失败或帐号已禁用！");
        }
    }

    public function logout() {
        //清除用户信息缓存
        F("login_user" . $_SESSION["uid"], null);
        session(null);

        $this->redirect("Search/index");
    }

    public function _empty() {
        $this->redirect("Search/index");
    }

}

?>
