$(window).load(function(){

//-----------------Admin login-----------//

//check if user is logged in
$.post("admin/is_logged_in", {},
	function(response){
		if(response.loggedIn){
			//render html
			$("#toolsBar").append(response.html);
		}else{
			$("#adminLoginFormContainer").fadeIn();
			$("#loginBg").fadeIn();
		}
	}, "json"
);

//----------------Admin logout----------//
$("#adminLogoutButton").live("click", function(){
	$.post("admin/logout", {},
		function(response){
			window.location = "admin";
		}
	);
});


//---------------Manage users----------//
$("#manageusers").live("click", function(){
	$.colorbox({
		width: 750,
		href: "admin/manage_users",
		scrolling: false,
		onComplete: function(){
		
			//change password
			$(".manageUserChangePassword").live("click", function(){				
				userId = $(this).parent().siblings(".userName").attr("userId");
				password = $(this).siblings(".adminUpdateUsername").val();
				
				if(password.length < 4){
					showError("Password cannot be shorter than 4 letters.");
					return;
				}
				
				popupboxLoading("hide");
				
				$.post("admin/manage_users_do", {action: "set_passwrd", newPassword: password, id: userId},
					function(response){
						popupboxLoading("show");
						
						showError("Password has been changed successfully");
					}, "json"
				);
			});			
			
			//Delete user
			$(".manageImgDeleteUser").click(function(){				
				userId = $(this).parent().siblings(".userName").attr("userId");
				var container = $(this).parent().parent();
				
				popupboxLoading("hide");
				$.post("admin/manage_users_do", {action: "delete_user", id: userId},
					function(response){
						popupboxLoading("show");

						container.remove();
						
						showError("User has been deleted successfully");
					}, "json"
				);
			});
			
			//disable user
			$(".disableUser").live("click", function(){				
				userId = $(this).parent().siblings(".userName").attr("userId");
				var action = $(this).attr("action");
				var obj = $(this);
				
				popupboxLoading("hide");
				
				$.post("admin/manage_users_do", {action: "disable_user", id: userId, disable_action: action},
					function(response){
						popupboxLoading("show");
						if(action == "enable"){
							obj.attr("action", "disable");
							obj.removeClass("manageUserenable").addClass("manageUserdisable");
						}else{
							obj.attr("action", "enable");
							obj.removeClass("manageUserdisable").addClass("manageUserenable");
						}
					}, "json"
				);
			});			
			//create user
			$(".createUser").live("click", function(){
				username = $(".createUserName").val();
				password = $(".createUserPassword").val();
				email = $(".email").val();
				var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/; 

				if(username.length < 3 || password.length < 4 || !emailPattern.test(email)){
					showError("Please fill in all fields correctly");
					return;
				}
				
				popupboxLoading("hide");
				
				$.post("admin/manage_users_do", {action: "create_user", username: username, password: password, email:email},
					function(response){
						popupboxLoading("show");
						
						$(this).parent().parent().remove();
						
						$(".userCreationProgressMessage").text(response.message).css("visibility", "visible");
					}, "json"
				);
			});
		}
	});
});

//-------------Manage presets----------//
$("#managepresets").live("click", function(){
	var data = getAllCardData();
	$.colorbox({
		width: 750,
		height: 430,
		href: "admin/manage_preset",
		data: {cardData: data},
		scrolling: false,
		onComplete: function(){
			
			//----------------save new preset---------------//
			$(".savePreset").live("click", function(){
				
				//preset name
				presetName = $(".presetName").val();
				//preset title
				presetTitle = $(".presetTitle").val();
				//preset description
				presetDesc = $(".presetDesc").val();
				
				if(presetName.length < 3){
					showError("Please enter the name of preset.");
					return;
				}
				
				//send data to the server and add title, desc there
				$.post("admin/save_preset", {cardData: data, name: presetName, title: presetTitle, desc: presetDesc},
					function(response){
						if(response.response){
							$("#presetMessage1").text("Template has been saved successfully.").css("visibility", "visible");
						}else{
							showError(response.errors);
						}
					}, "json"
				);
			});
			
			//--------------------Manage existing preset----------//
			$(".managePresetList, .managePresetListAction").selectmenu({width:160});
			$(".ui-selectmenu").css("cssText", "margin-left: 0px !important; width:160px;");
			
			var previewPathLeft;
			var previewPathRight;
			
			//when user selects preset
			$(".managePresetList").live("change", function(){

				name = $(".managePresetList").val();
				//load preset data
				$.getJSON("media/js/presetsdata/"+name+"?"+Math.floor(Math.random(999)*9999), function(data){
					previewPath = data['preview']['thumb'];
					
					//set new src to preview img
					$(".managePresetPreviewImg").attr("src", previewPath);
				});
			});
			
			$(".editPreset").click(function(){
				action = $(".managePresetListAction").val();
				name = $(".managePresetList").val();

				//check if user isn't trying to delete default template
				if(name == "default.js" && action == "delete"){
					showError("Default template cannot bet deleted!");
					return;
				}else{
					items = new Array (
						"media/js/presetsdata/" + name,
						previewPathLeft,
						previewPathRight
					);

					//delete preset
					$.post("admin/delete_preset", {items: items},
						function(response){
							if(response){
								fileName = name.replace("-", " ").replace(".js", "").split("_");

								$("#presetMessage2").text(fileName[0] + " has been deleted successfully.").css("visibility", "visible");
							}else{
								showError(response.error);
							}
						}, "json"
					);
				}
			});
		
			$(".presetDesc").simplyCountable({
				counter: "#descCounter",
				maxCount:100,
				strictMax: true
			});
		}
	});
});

$("#AdminLogin, #AdminLoginForm").bind("click keypress", function(e){
	if(e.type == "keypress" && e.keyCode ==  13){
		e.preventDefault();
	}else if(e.target.id != "AdminLogin"){
		return;
	}
	
	username = $(".username").val();
	password = $(".password").val();
	
	$.post("admin/login", {username: username, password: password},
		function(response){

			//if we got no errors it means user is logged in
			if(!response.errors){
				$("#adminLoginFormContainer").remove();
				$("#loginBg").remove();
			
				//render html
				$("#toolsBar").append(response.html);
				
			}else{
				$("#adminLoginFormContainer").animate({height: "152px"});
				$(".loginErrorBox").text(response.errors).fadeIn('slow');
			}
		}, "json"
	);
});


$(".adminToolLink").hover(function(){
	$(this).children(".AdminCpToolText").css("color", "#413B3B");
}).mouseout(function(){
	$(this).children(".AdminCpToolText").css("color", "#0F62BB");
});

$("#editAboutUs").live("click", function(){
	$.colorbox({
		width: 700,
		height: 380,
		html: function(t){
			var html = $("<div>");
			
			
			//create text area
			$.post("admin/about_us", {action: "get_content"},
				function(response){
					html.append(
						$("<textarea>")
							.addClass("editAboutUsTextarea")
							.text(response.html)
					);	
				}, "json"
			);
			
			//add title
			html.prepend(
				$("<div>")
					.addClass("coolText")
					.css("text-align", "center")
					.text("Edit About Us")
			);
			
			//create submit button
			html.append(
				$("<div>")
					.addClass("aboutUsEditSubmit")
					.bind("click", function(){
						var text;
						
						//save desc
						$.post("admin/about_us", {action: "save_desc", desc: $(".editAboutUsTextarea").val()},
							function(response){
								if(response.status){
									text = "Description has been saved successfully.";
								}else{
									text = "Unknown error occured!";
								}
								
								$(".saveWorkMessageBox").remove();
								
								html.append(
									$("<div>")
										.addClass("saveWorkMessageBox displayInline")
										.css({
											visibility: "visible",
											marginLeft: "15px",
											marginTop: "25px"
										})
										.text(text)
								);
								
							}, "json"
						);

					})
			);
			
			
			return (html);
		},
		onComplete: function(){
			$(".editAboutUsTextarea").wysiwyg({
				 rmUnusedControls: true,
				controls: {
					bold: { visible : true },
					italic: { visible : true },
					strikeThrough: { visible : true },
					underline: { visible : true },
					justifyLeft: { visible : true },
					justifyCenter: { visible : true },
					justifyRight: { visible : true },
					justifyFull: { visible : true },
					indent: { visible : true },
					outdent: { visible : true },
					undo: { visible : true },
					redo: { visible : true },
					insertOrderedList: { visible : true },
					insertUnorderedList: { visible : true },
					insertHorizontalRule: { visible : true },
					createLink: { visible : true },
					h1: { visible : true },
					h2: { visible : true },
					h3: { visible : true },
					paragraph: { visible : true },
					increaseFontSize: { visible : true },
					decreaseFontSize: { visible : true }
				}
			});
		}
	});
});

$("#addBgImg").live("click", function(){
	$.colorbox({
		width: 700,
		height: 340,
		href: "admin/manage_images",
		data: {requestType: "bg"},
		transferClasses: true,
		scrolling: false,
		onComplete: function(){
			$(".uploadImageName").DefaultValue("Image");
			$(".selectCategory, .deleteCategorySection, .manageImgSelectUpload").selectmenu({width: 150});	
			
			//change section
			$(".deleteCategorySection").change(function(){
				//recreate list
				recreateCategoryList($(this).val());
				$(".uploadSection").val($(this).val());
			});
			
			//--------------Create new category-------------//
			$(".manageImgCreate").live("click", function(){
				var name = $(".createCategoryInput").val();
				var section = $(".deleteCategorySection").val();
				
				//check if user wrote a name
				if(name.length < 2){
					showError("Please write category name!");
					return;
				}
				
				popupboxLoading("hide");
				
				$.post("admin/manage_categorys", {action: "create_category", doFor: section, name: name},
					function(response){
						//remove loading icon
						popupboxLoading("show");
						
						//show server response
						if(response.response){
							//recreate list
							recreateCategoryList($(".deleteCategorySection").val());
							showError("Directory has been created.");
						}else{
							showError("Directory already exists.");
						}
					}, "json"
				);
				
			});
			
			//-----------Delete category--------------//
			$(".manageImgDeleteCategory").live("click", function(){
				name = $(".deleteCategoryList").val();
				section = $(".deleteCategorySection").val();
								
				if(name == "random"){
					showError("You can't delete random category!");
					return;
				}
				
				popupboxLoading("hide");

				$.post("admin/manage_categorys", {action: "delete_category", doFor: section, name: name},
					function(response){
						//remove loading icon
						popupboxLoading("show");
						
						//show server response
						if(response.response){
							showError("Directory has been deleted.");
							recreateCategoryList($(".deleteCategorySection").val());
						}else{
							showError(response.error);
						}
						
					}, "json"
				);
			});

			//submit form
			$(".uploadSubmit").click(function(e){
				e.preventDefault();
				
				$(".section").val($(".uploadSection").val());
				$(".uploadDir").val($(".uploadCategoryList").val());
				
				$(".uploadForm").submit();
			});
			
			//update upload dir
			$(".uploadCategoryList").change(function(){
				$(".uploadDir").val($(this).val());
			});
			
			//----------Upload Image------------//
			$(".uploadImage").bind("load", function(){
					//get content of the iframe
					getJsonString = $(this).contents().find("body").text();

					//parse it to json
					filesResponse = $.parseJSON(getJsonString);
					
					if(filesResponse.uploaded){
							$(".saveWorkMessageBox").text("Image has been uploaded successfully.").css("visibility", "visible");
					}else{
						showError(filesResponse.error);
					}
			});
		}
	});
});

function recreateCategoryList(action){
	$.post("admin/manage_images", {requestType: action, action: "changeSection"},
		function(response){
			//remove selectmenu
			$(".deleteCategoryList, .uploadCategoryList").selectmenu("destroy").remove();
			
			//append that list
			$(".manageImgDeleteCategory").before(
				list = $("<select>")
					.html(response.list)
					.addClass("selectCategory deleteCategoryList")
			);
			$(".uploadImageCatgeroys").append(
				list = $("<select>")
					.html(response.list)
					.addClass("selectCategory uploadCategoryList")
			);
			
			//attach selectmenu plugin
			$(".selectCategory, .uploadCategoryList").selectmenu({width: 150});
			
		}, "json"
	);
}


function popupboxLoading(action){

	if(action == "hide"){
		$("#cboxLoadedContent").append(
			$("<div>")
				.addClass("popupboxLoadingContainer")
				.fadeIn("fast")
		);
		
		$("#cboxLoadingGraphic").fadeIn();
	}else{
		$(".popupboxLoadingContainer").remove();
		$("#cboxLoadingGraphic").fadeOut();
	}
}

});