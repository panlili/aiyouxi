<?php

class GoodModel extends RelationModel {

    protected $_validate = array(
        array("serial", "require", "物资编号必须", Model::EXISTS_VALIDATE, "", Model:: MODEL_BOTH),
        array("name", "require", "物资名必须", Model::EXISTS_VALIDATE, "", Model:: MODEL_BOTH),
        array("serial", "", "编号已存在", Model::EXISTS_VALIDATE, "unique", Model:: MODEL_BOTH),
    );
    protected $_auto = array(
        array("status", "1", Model::MODEL_INSERT),
        //物资入库第一次填数据，及入库人为handman
        //在出库时更新数据，及出库中后出库确认人为verifier
        array('handman', 'get_session_user_id', Model::MODEL_BOTH, 'function'),
        //第一次添加的物资自动设置step(enum type)为1，即库存中状态
        array('step', "1", Model::MODEL_INSERT),
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
