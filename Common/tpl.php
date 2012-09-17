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

//输出权限选择的select
function right_select($selected = "") {
    $str = "";
    if ("" == $selected) {
        $str = '<select name="right">
                <option value="0">0级</option>
                <option value="1">1级</option>
                <option value="2">2级</option>
            </select>';
    } else {
        $str = '<select name="right">
                <option value=' . $selected . '>' . $selected . '级</option>
                <option value=' . $selected . '>--修改请重选--</option>
                <option value="0">0级</option>
                <option value="1">1级</option>
                <option value="2">2级</option>
            </select>';
    }
    return $str;
}

function reverseIt($arg) {
    if ($arg === "男")
        return "女";
    if ($arg === "女")
        return "男";
}

function user_select($name) {
    $m_user = M("User");
    $users = $m_user->where("status=1")->select();
    $html = '<select name=' . $name . '>';
    foreach ($users as $user) {
        $html.='<option value=' . $user["id"] . '>' . $user["truename"] . '</option>';
    }
    $html.='</select>';
    return $html;
}

?>
