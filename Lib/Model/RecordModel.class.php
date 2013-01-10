<?php

class RecordModel extends RelationModel {

    protected $_validate = array(
        //领用记录每个物资只能分配一次
        array("good_id", "", "物资已被领用登记!", Model::EXISTS_VALIDATE, "unique", Model:: MODEL_BOTH),
        array("good_id", "require", "物资必须!", Model::EXISTS_VALIDATE, "", Model:: MODEL_BOTH),
        array("family_id", "require", "领用家庭必须!", Model::EXISTS_VALIDATE, "", Model:: MODEL_BOTH),
        array("serial", "require", "领物编号必须!", Model::EXISTS_VALIDATE, "", Model:: MODEL_BOTH),
    );
    protected $_auto = array(
        array('handman', 'get_session_user_id', Model::MODEL_INSERT, 'function'),
        array('addtime', 'time', Model::MODEL_INSERT, 'function'),
        array('uptime', 'time', Model::MODEL_UPDATE, 'function'),
    );

}

?>
