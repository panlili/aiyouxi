<?php

class FamilyModel extends RelationModel {

    protected $_validate = array(
        array("serial", "", "编号已存在", Model::EXISTS_VALIDATE, "unique", Model:: MODEL_BOTH),
    );
    protected $_auto = array(
        array("status", "1", Model::MODEL_INSERT),
        array('handman', 'get_session_user_id', Model::MODEL_BOTH, 'function'),
        array('addtime', 'time', Model::MODEL_INSERT, 'function'),
        array('uptime', 'time', Model::MODEL_UPDATE, 'function'),
    );

    //不create也能用
    public function _before_update(&$data, $options) {
        parent::_before_update($data, $options);
        $data["uptime"] = time();
        $data["handman"] = get_session_user_id();
    }

}

?>
