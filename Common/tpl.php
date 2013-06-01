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
                <option value="0">0级，普通工作人员</option>
                <option value="1">1级，管理员</option>
                <option value="2">2级，超级管理员</option>
            </select>';
    } else {
        $str = '<select name="right">
                <option value=' . $selected . '>' . $selected . '级</option>
                <option value=' . $selected . '>--修改请重选--</option>
                <option value="0">0级，普通工作人员</option>
                <option value="1">1级，管理员</option>
                <option value="2">2级，超级管理员</option>
            </select>';
    }
    return $str;
}

function location_select($selected = "") {
    $html = "";
    $m_location = M("Location");
    $locations = $m_location->select();
    if ("" == $selected) {
        $html = '<select name="location">';
        foreach ($locations as $location) {
            $html.='<option value=' . $location["id"] . '>' . $location["name"] . '</option>';
        }
        $html.='</select>';
    } else {
        $location = $m_location->where("id= $selected")->find();
        $html = '<select name="location">
                <option value=' . $location["id"] . '>' . $location["name"] . '</option>
                <option value=' . $location["id"] . '>--修改请重选--</option>';
        foreach ($locations as $location) {
            $html.='<option value=' . $location["id"] . '>' . $location["name"] . '</option>';
        }
        $html.='</select>';
    }
    return $html;
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
