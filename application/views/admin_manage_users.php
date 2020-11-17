<div class="manageImgBlockTitle coolText">Manage existing users</div>

<ul class="AdminUsersList">
	<li class="AdminUserListItem" style="border-top: 1px solid #B8B2B2; background:#E4FAF6;">
		<div class="displayInline adminUsersListTitle">
			Username
		</div>
		<div class="displayInline adminUsersListTitle">
			Last Login
		</div>
		<div class="displayInline adminUsersListTitle">
			Last IP
		</div>
		<div class="displayInline adminUsersListTitle">
			Remove Account
		</div>
		<div class="displayInline adminUsersListTitle">
			Disable/Enable
		</div>
		<div class="displayInline adminUsersListTitle">
			Set Password
		</div>
	</li>
	<?=$list?>
</ul>

<div class="manageImgBlockTitle coolText">Create new user</div>

<div >
	<label class="createUserLabel displayInline" for="username">Username:</label><input name="username" type="text" class="createUserName textInput displayInline" style="margin-top:10px; width:139px;"/><br />
	<label class="createUserLabel displayInline" for="password">Password:</label><input name="password" type="text" class="createUserPassword textInput displayInline" style="margin-top:10px; width:139px;"/><br />
	<label class="createUserLabel displayInline" for="password">Email:</label><input name="email" type="text" class="email textInput displayInline" style="margin-top:10px; width:139px;"/><br />
	<input type="submit" class="createUser" value="Create" style=" margin-top: 11px; "/>
	<div class="saveWorkMessageBox displayInline userCreationProgressMessage" ></div>	
</div>