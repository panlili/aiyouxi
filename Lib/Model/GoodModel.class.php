<?php

class GoodModel extends RelationModel {

    protected $_validate = array(
        array("serial", "", "编号已存在", Model::EXISTS_VAILIDATE, "unique", Model:: MODEL_BOTH),
    );
    protected $_auto = array(
        array("status", "1", Model::MODEL_INSERT),
        //物资入库第一次填数据，及入库人为handman
        //在出库时更新数据，及出库中后出库确认人为verifier
        array('handman', 'get_session_user_id', Model::MODEL_INSERT, 'function'),
        array('addtime', 'time', Model::MODEL_INSERT, 'function'),
        array('uptime', 'time', Model::MODEL_UPDATE, 'function'),
    );

}

?>
