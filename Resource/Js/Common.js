//在页面初始化和窗口尺寸变化的时候设置rightcontent的宽度。自适应。
function resetwindow(){
    var ss=$(".kkk").width()-$("#leftContent").width()-17;
    $("#rightContent").width(ss);
}
//用户查询的时候在表单输入判断是否输入了回车，如果回车执行响应的函数
function checkenter(){
    var keyinput=window.event.keyCode;
    alert(keyinput);
    var li;
    if(keyinput != 13){
        li+=keyinput;
        alert(keyinput);
    }else
    {
        //doajaxget;
    }
}
$(document).ready(function(){
  // 在这里写你的代码...
  $("#search_key").keydown(function(){alert("kick");});//不起作用？为什么呢
});

/*各模块通用的ajax函数，具体操作交由具体的回调函数做*/

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

function post_data(path,formdata,callback){    
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

//恢复数据
function recyle_data(path,model,id,callback){
    $.post(path,{
        model:model,
        id:id
    },callback,"JSON");
}

//永久数据
function delete_data(path,model,id,callback){
    $.post(path,{
        model:model,
        id:id
    },callback,"JSON");
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

function callback_recyle_donater(json){
    if(0==json.status){
        alert(json.info);
    }else{
        $("#tabs-1 tr#"+json.data).fadeOut("fast");
        $("#message").html(json.info).show().slideUp(1500);
    }
}

function callback_delete_donater(json){
    callback_recyle_donater(json);
}

function callback_search_donater(json){
   
    if(0==json.status){
        
    }else{        
        $("#message").html(json.info).show().slideUp(1500);
        $("#search_result").empty().append(json.data).show();
    }
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

function show_family_detail(path,id){
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

function callback_recyle_family(json){
    if(0==json.status){
        alert(json.info);
    }else{
        $("#tabs-2 tr#"+json.data).fadeOut("fast");
        $("#message").html(json.info).show().slideUp(1500);
    }
}

function callback_delete_family(json){
    callback_recyle_family(json);
}

//物资相关
function callback_add_good(json){
    if(0==json.status){
        $("#message").html(json.info).show();
    }else{
        $("#message").html(json.info).show().fadeOut(4000);
        $("input:reset").click();
    }
}

function callback_get_good_edit_form(json) {
    if(0==json.status){
        alert(json.info);
    }else{
        $("#tabs-2").html(json.data);
        $("input:button,input:reset").button();
        $("#tabs").tabs().tabs('select',1);
    }
}

function callback_edit_good(json){
    if(0==json.status){
        alert(json.info);
    }else{
        $("#message").html(json.info).show().slideUp(1500);
        $("#tabs-2").html("数据修改成功，刷新页面后进入数据列表能看到修改后的数据。");
    }
}

function callback_toggle_good_status(json){
    callback_toggle_donater_status(json);
}

function callback_recyle_good(json){
    if(0==json.status){
        alert(json.info);
    }else{
        $("#tabs-3 tr#"+json.data).fadeOut("fast");
        $("#message").html(json.info).show().slideUp(1500);
    }
}

function callback_delete_good(json){
    callback_recyle_good(json);
}

function callback_add_record(json){
    if(0==json.status){
        $("#message").html(json.info).show();
    }else{
        $("#message").html(json.info).show().fadeOut(4000);
        $("input:reset").click();
    }
}

//search相关
function callback_search(json){
    if(0==json.status){       
        $("#message").html(json.info).show();
    }else{        
        $("#message").html(json.info).show().slideUp(1500);
        $("#search_result").empty().append(json.data).show();
    }

}

function callback_index_search(json) {    
    if(0==json.status){       
        $("#message").html(json.info).show();
        $("#index_result").empty().append("ajax回调发生错误").show();
    }else{  
        if(json.data==""){
            $("#index_result").empty().append("没有命中记录，请检查物资编号输入是否正确，或者重新输入。").show();
            
        }else{
            $("#message").html(json.info).show().slideUp(1500);
            $("#index_result").empty().append(json.data).show();
        }
    
        
    }

}
