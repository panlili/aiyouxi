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

?>