function logout(){
    var url = 'login_action.php?action=logout&r='+Math.random();
    $.get(url,{},function(data){
        if(data=='logout'){
            location.href = 'login.php';
        } else {
            alert(data);
        }
    });		
}

function changepswd(){
    $('#change_pswd_div').show();
}

function close_change_pswd_div(){
    $('#change_pswd_div').hide();
}

function change_passwd_click(){
    var old_passwd = $('#ori_passwd').val();
    var new_passwd = $('#new_passwd').val();
    var re_new_passwd = $('#re_new_passwd').val();
    if(new_passwd!=re_new_passwd){
        alert('确认新密码和新密码不一致');
        return;
    }
    var url = 'login_action.php?action=update_user_and_pwd';
    var para = {old_passwd:old_passwd,new_passwd:new_passwd,r:Math.random()};
    $.post(url,para,function(data){
         if(data=='success'){
         	$('#ori_passwd').val('');
         	$('#new_passwd').val('');
         	$('#re_new_passwd').val('');
         	$('#change_password_success_span').html('修改成功');
         	setTimeout("close_change_pswd_div();$('#change_password_success_span').html('');", 3000 );
         	 
         } else {
         	alert(data); 
         }
    });
}