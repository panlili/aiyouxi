<?php

class GoodModel extends RelationModel {

    protected $_validate = array(
        array("serial", "", "编号已存在", Model::EXISTS_VALIDATE, "unique", Model:: MODEL_BOTH),
    );
    protected $_auto = array(
        array('donatetime', 'getOnlyDate', Model::MODEL_INSERT, 'function'),
        array("status", "1", Model::MODEL_INSERT),
        //物资入库第一次填数据，及入库人为handman
        //在出库时更新数据，及出库中后出库确认人为verifier
        array('handman', 'get_session_user_id', Model::MODEL_INSERT, 'function'),
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
