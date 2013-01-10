<?php

class DonaterModel extends RelationModel {

    //只有create方法才有用
    protected $_validate = array(
        array("name", "require", "姓名必须", Model::EXISTS_VALIDATE, "", Model:: MODEL_BOTH),
        //array("serial", "require", "编号必须", Model::EXISTS_VALIDATE, "", Model:: MODEL_BOTH),
        //array("serial", "", "编号已存在", Model::EXISTS_VALIDATE, "unique", Model:: MODEL_BOTH),
    );
    protected $_auto = array(
        array('addtime', 'time', Model::MODEL_INSERT, 'function'),
        array('uptime', 'time', Model::MODEL_UPDATE, 'function'),
        array('status', '1', Model::MODEL_INSERT),
        //数据操作人员，自动获取用户登陆后的$_SESSOIN["id"]
        array('handman', 'get_session_user_id', Model::MODEL_BOTH, 'function'),
    );

    //不create也能用
    public function _before_update(&$data, $options) {
        parent::_before_update($data, $options);
        $data["uptime"] = time();
        $data["handman"] = get_session_user_id();
    }

}

?>
