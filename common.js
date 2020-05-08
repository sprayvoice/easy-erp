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


function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
                "SymbianOS", "Windows Phone",
                "iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}


	function get_going_expire_drug(){
		$.get('drug_action.php',{action:'get_going_expire_drug',r:Math.random(),page_name:'drug.php'},
			function(data){	
			
                    $('#global_warning_info').html(data);        			            			
				});
	
		}
		
		
		 function accDiv(arg1, arg2) {
                var t1 = 0, t2 = 0, r1, r2;
                try {
                    t1 = arg1.toString().split('.')[1].length
                } catch (e) {
                }
                try {
                    t2 = arg2.toString().split('.')[1].length
                } catch (e) {
                }
                with (Math) {
                    r1 = Number(arg1.toString().replace('.', ''));
                    r2 = Number(arg2.toString().replace('.', ''));
                    return (r1 / r2) * pow(10, t2 - t1);
                }
            }
            function accMul(arg1, arg2)
            {
                var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
                try {
                    m += s1.split('.')[1].length
                } catch (e) {
                }
                try {
                    m += s2.split('.')[1].length
                } catch (e) {
                }
                return Number(s1.replace('.', '')) * Number(s2.replace('.', '')) / Math.pow(10, m);
            }
            function accAdd(arg1, arg2) {
                var r1, r2, m;
                try {
                    r1 = arg1.toString().split('.')[1].length
                } catch (e) {
                    r1 = 0
                }
                try {
                    r2 = arg2.toString().split('.')[1].length
                } catch (e) {
                    r2 = 0
                }
                m = Math.pow(10, Math.max(r1, r2));
                return (arg1 * m + arg2 * m) / m;
            }
            function accSub(arg1, arg2) {
                var r1, r2, m, n;
                try {
                    r1 = arg1.toString().split('.')[1].length
                } catch (e) {
                    r1 = 0
                }
                try {
                    r2 = arg2.toString().split('.')[1].length
                } catch (e) {
                    r2 = 0
                }
                m = Math.pow(10, Math.max(r1, r2));
                //动态控制精度长度
                n = (r1 >= r2) ? r1 : r2;
                return ((arg2 * m - arg1 * m) / m).toFixed(n);
            }
		
		