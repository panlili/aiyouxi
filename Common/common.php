<?php

/**
 * @author libiun
 * @param type $module 当前页面所在模块的名称
 * @param type $name 当前菜单项对应的模块名
 * @return 在<li>里面输出css的current类
 */
function echo_current($module, $name) {
    if ($module === $name)
        return 'class="current"';
}

//对页面右侧的子菜单进行标识
function get_second_menu($module, $action) {
    switch ($module) {
        case "Retrieval":$tmp = RetrievalAction::$method_array;
            break;
        case "Donater":$tmp = DonaterAction::$method_array;
            break;
        case "Admin":$tmp = AdminAction::$method_array;
            break;
        case "Family":$tmp = FamilyAction::$method_array;
            break;
        case "Good":$tmp = GoodAction::$method_array;
            break;
    }

    $str = "<ul>";
    foreach ($tmp as $k => $v) {
        if ($k == $action)
            $str.="<li><a class='basic-button' href='__APP__/$module/$k'><span style='color:blue;'>$v</span></a></li>";
        else
            $str.="<li><a class='basic-button' href='__APP__/$module/$k'><span>$v</span></a></li>";
    }
    return $str . "</ul>";
}

//获取用户登陆后的uid，即用户的id，自动填充到donater表的handman字段，标识这条数据最后的操作人员
function get_session_user_id() {
    if (isset($_SESSION["uid"]))
        return $_SESSION["uid"];
}

//根据用户id获取姓名
function get_user_truename_by_id($id) {
    return !empty($id) ? M("User")->where("id=$id")->getField("truename") : "";
}

//根据捐赠者id获取姓名
function get_donater_name_by_id($id) {
    return !empty($id) ? M("Donater")->where("id=$id")->getField("name") : "";
}

//根据家庭id获取编号
function get_family_serial_by_id($id) {
    return !empty($id) ? M("Family")->where("id=$id")->getField("serial") : "";
}

function auto_charset($fContents, $from = 'gbk', $to = 'utf-8') {
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
}
?>