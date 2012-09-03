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
            $str.="<li><a class='basic-button' href='__APP__/$module/$k'><span style='color:yellow;'>$v</span></a></li>";
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

?>