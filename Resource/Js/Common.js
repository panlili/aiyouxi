/**
 *各个模块Html可以共用的操作数据的函数
 *根据path的不同操作不同的model数据
 */

//交替变换status的值
function toggle_status(path,id,callback){
    $.get(path, {
        id:id
    },callback,"JSON");
}

//通过表单数据添加数据
function add_data(path,formdata,callback){
    $.post(path,formdata,callback,"JSON");
}

//获取指定数据
function get_data(path,id,callback){
    $.get(path,{
        id:id
    },callback,"JSON");
}

//修改数据
function edit_data(path,formdata,callback){
    $.post(path, formdata, callback, "JSON");
}

//ajax操作的回调函数
function callback_toggle_user_status(json){
    if(1==json.status){
        //原来是禁用的文字改为启用，原来启用改成禁用，而不从数据库读其状态了。
        if($.trim($("a#status_"+json.data).text())=="启用中"){
            $("a#status_"+json.data).text("禁用中");
        }else{
            $("a#status_"+json.data).text("启用中");
        }
        $("#message").html(json.info).show().slideUp(1500);
    }else{
        //应该不会到这里的
        alert(json.info);
    }
}

function callback_add_user(json){
    if(0==json.status){
        alert(json.info);
    }else{
        $(".userlist:last").after(json.data);
        $("#tabs-2 input:reset").click();
        $("#message").html(json.info).show().slideUp(1500);
    }
}

function callback_get_user_edit_form(json){
    if(0==json.status){
        alert(json.info);
    }else{
        $("#tabs-3").html(json.data);
        $("input:button,input:reset").button();
        $("#tabs").tabs().tabs('select', 2);
    }
}

function callback_edit_user(json){
    if(0==json.status){
        alert(json.info);
    }else{
        $("#message").html(json.info).show().slideUp(1500);
       //修改一次后，如果不做任何修改在点按钮，会显示save数据时出错(error("数据写入错误"))
       //没搞懂哪里的问题
       $("#tabs-3").html("数据修改成功，刷新页面后进入用户列表能看到修改后的数据。");
    }
}

