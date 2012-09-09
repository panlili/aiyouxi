<?php

class InformationWidget extends Widget {

    public function render($data) {
        if (isset($_SESSION["uid"]) && isset($_SESSION["truename"])) {
            //用户登陆进来后获取其相关信息显示在左侧菜单下面
            //如果缓存存在，直接读取缓存信息，不需要再去读数据库操作
            //如果缓存不存在，读取用户信息并缓存，这样每个页面不需要都去读次数据库
            if (F("login_user" . $_SESSION["uid"])) {
                $data = F("login_user" . $_SESSION["uid"]);
            } else {
                $m_user = M("User");
                $data = $m_user->field("password", true)->find($_SESSION["uid"]);
                F("login_user" . $_SESSION["uid"], $data);
            }
            //渲染user_information.html，并传递用户数据
            $content = $this->renderFile("user_information", $data);
            return $content;
        }
    }

}

?>
