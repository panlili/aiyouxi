
//交替变换status的值
function toggle_status(path,id){
    $.get(path, {
        id:id
    },function(data){
        window.location.reload();
        $("#message").html(data.info).show().slideUp(1500);
    },"JSON");
}