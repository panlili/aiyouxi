
//交替变换status的值
function toggle_status(path,id){
    $.get(path, {
        id:id
    },function(data){
        window.location.reload();
        $("#message").html(data.info).show().slideUp(1500);
    },"JSON");
}

function add_user(path,formdata){
    $.post(path,formdata,function(data){
        if(data.status===1){
            window.location.reload();
            $("#message").html(data.info).show().slideUp(1500);
        }else{
            alert(data.info);
        }
    },"JSON");
}