<?php

/*
 * 模版中使用的普通函数
 */

//根据status的值(数据库中是int类型)在html中输出一串文字
function get_status_str_by_int($status) {
    switch ($status) {
        case 0:return "禁用中";
            break;
        case 1:return "启用中";
            break;
        default:return "status值错误";
            break;
    }
}

?>
