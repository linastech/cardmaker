<div id="adminLoginFormContainer">
	<div id="adminLoginTitle">
		<div class="AdminLoginTitleIcon displayInline"></div>
		<div class="AdminLoginTitleText displayInline">Administration control panel.</div>
		<a href="home/"><div class="adminReturn displayInline" title="Return to the main page"></div></a>
	</div>
	<form action="admin" method="post" id="AdminLoginForm"/>
		<label class="AdminLoginLabel displayInline" for="username" >Username: </label><input type="text" class="username textInput" name="username" class="textInput" style="margin-left:0px; width:150px;"/>
		<label class="AdminLoginLabel displayInline" for="password" >Password: </label><input type="password" class="password textInput" name="password" class="textInput" style=" margin-left:0px; width:150px;"/>
		<input type="submit" style="position:absolute; top: -99999999999px;"/>
		<div id="AdminLogin"></div>
	</form>
	
	<div class="loginErrorBox"></div>
</div>
<div id="loginBg"></div>