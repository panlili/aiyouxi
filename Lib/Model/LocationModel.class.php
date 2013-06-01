<?php

class LocationModel extends RelationModel{
 protected $_validate = array(
        array("name", "require", "站点名必须"),
        array("name", "", "站点名已存在", Model::EXISTS_VALIDATE, "unique", Model:: MODEL_BOTH),
    );

}

?>
