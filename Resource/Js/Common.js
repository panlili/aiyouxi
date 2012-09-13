/*各模块通用的ajax函数，具体操作交由具体的回调函数做*/

//在页面初始化和窗口尺寸变化的时候设置rightcontent的宽度。自适应。
function resetwindow(){
    var ss=$(".kkk").width()-$("#leftContent").width()-17;
    $("#rightContent").width(ss);
}

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

/* ajax操作的回调函数 */

//User相关
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
        $("#tabs-3").html("数据修改成功，刷新页面后进入数据列表能看到修改后的数据。");
    }
}

//donater相关
function callback_toggle_donater_status(json){
    if(1==json.status){
        $(".common_table tr#"+json.data).fadeOut("fast");
        $("#message").html(json.info).show().slideUp(1500);
    }else{
        alert(json.info);
    }
}

function callback_add_donater(json){
    if(0==json.status){
        alert(json.info);
    }else{
        $(".donaterlist:first").before(json.data);
        $("#tabs-2 input:reset").click();
        $("#message").html(json.info).show().slideUp(1500);
    }
}

function callback_get_donater_edit_form(json) {
    callback_get_user_edit_form(json);
}

function callback_edit_donater(json){
    callback_edit_user(json);
}

//family相关
function callback_toggle_family_status(json){
    callback_toggle_donater_status(json);
}

function callback_add_family(json){
    if(0==json.status){
        alert(json.info);
    }else{
        $(".familylist:first").before(json.data);
        $("#tabs-2 input:reset").click();
        $("#message").html(json.info).show().slideUp(1500);
    }
}

function callback_get_family_edit_form(json) {
    callback_get_user_edit_form(json);
}

function callback_edit_family(json){
    callback_edit_user(json);
}

function showFamilyDetail(path,id){
    $.get(path,{
        id:id
    },function(json){
        if(0==json.status){
            alert(json.info)
        }else{
            $("<div class='common_form'>").html(json.data).dialog({
                modal: true,
                width: 500,
                height: 650
            });
        }
    },"JSON");

}

function set_family_serial(path,id,text,callback){
    $.post(path, {
        id:id,
        text:text
    }, callback, "JSON");
}

function callback_set_family_serial(json){
    if(0==json.status){
        alert(json.info);
    }else{
        window.location.reload();
    }
}

function callback_add_good(json){
    if(0==json.status){
        alert(json.info);
    }else{
        window.location.reload();
    }
    
}