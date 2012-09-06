<?php

class UserModel extends RelationModel {

    protected $_validate = array(
        array("username", "require", "用户名必须"),
        array("username", "/^[a-z]\w{3,}$/i", '用户名格式:长度大于3个的字母组合'),
        array("username", "", "帐号已存在", Model::EXISTS_VALIDATE, "unique", Model:: MODEL_BOTH),
        array("truename", "require", "姓名必须"),
        array("truename", "", "该姓名已存在", Model::EXISTS_VALIDATE, "unique", Model:: MODEL_BOTH),
        array("password", "require", "密码必须"),
    );
    protected $_auto = array(
        array('password', 'md5', Model:: MODEL_INSERT, 'function'),
        array('addtime', 'time', Model::MODEL_INSERT, 'function'),
        array('status', '1', Model::MODEL_INSERT),
    );

}

?>
