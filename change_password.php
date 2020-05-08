	<div id='change_pswd_div' 
	style='z-index:99;background-color:white;position:fixed;width:250px;height:250px;left:200px;top:200px;
	padding-left:50px;padding-right:50px;padding-top:15px;padding-bottom:15px;display:none;'>
		
		<div style='text-align:right;'>
		<input type='button' value='x' onclick='close_change_pswd_div()' />
		</div>

		原密码：<input type='password' id='ori_passwd' /><br />
		
		新密码：<input type='password' id='new_passwd' /><br />
		
		确认新密码：<input type='password' id='re_new_passwd' /><br />

		<input type='button' value='确认' onclick='change_passwd_click()'  class='btn btn-primary'/>
        <br />
        	<span id='change_password_success_span' style='color:green;'></span>

	</div>