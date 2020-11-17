var cardData;

$(window).load(function(){

//------------------------Card properties--------------------------//
var cardProperties = {
	container: $("#cardContainer"),
	elementsWrapper: $("#cardElementsWrapperBorder"),
	size: 0, //0 -> (90x50mm <-> 510px x 283px), 1-> (85x55mm <-> 482px x 312px)
	position: "h", //default horizontal
	bgImagesPath: "media/images/designs/backgrounds/", //path to card bg images
	floatingImagesPath: "media/images/designs/floating-images/", //path to card bg images
	imagesPerPage: 11,
	innerBorderMargins: 40, //this should be divided by 2 to get the actual margin
	currentCardSide: 1
};

var linesStyles = {
	"1": {"width": "1px", "style": "solid"},
	"2": {"width": "2px", "style": "solid"},
	"3": {"width": "3px", "style": "solid"},
	"4": {"width": "1px", "style": "dashed"},
}

var cardSettings = {
	sizes:  [ [[517], [287], [90], [50]], //size 1 -> 90x50mm
			  [[485], [312], [85], [55]], //size 2 -> 85x55mm
			  [[633], [873], [101], [152]], //size 3 -> 101x152mm
			  [[728], [1016], [127], [177]] //size 4 -> 127x177mm
			]
}


//------------------------Get data for card-----------------------//

//load user saved card
catchJsonString = $("#loadUserCard").html();
if(catchJsonString.length > 10){
	userSavedCardJson = $.parseJSON(catchJsonString);
	if(userSavedCardJson.status)
		loadCardDataJson(userSavedCardJson.path);
	else
		showError("Unknown error occured, we can't load your saved card.");
}else{
	loadCardDataJson("");
}

function loadCardDataJson(path){
	//default path
	if(path == "")
		path = "media/js/presetsdata/default.js";

	$.getJSON(path + "?" + Math.floor(Math.random(999)*9999), function(data){
		//set this data so we could use it later
		cardData = data;
		
		//now call function which will load everything up
		loadCardData("side1", true);
	});
}
	
var cardImage = "";
var cardColor = "#fff";

function loadCardData(side, firstLoad){
	//disable some fields when user flips side
	if(side != "side1"){
		$("#toolsCardType, #toolsCardSize").append( $("<div>").addClass("disable_field") );
		if(!firstLoad){
			$(function(){
				save("side1");
			});
		}
	}else{
		$("#toolsCardType, #toolsCardSize").find(".disable_field").remove();
		if(!firstLoad){
			$(function(){
				save("side2");
			});
		}
	}
	
	//load text
	$.each(cardData[side].textPositions, function(key){
		if(key == "__comment") return;

		$("#"+key).css({
			left: parseInt(this.left) + "px",
			top: parseInt(this.top) + "px",
			fontFamily: cardData[side].textProperties[key]["font-family"].replace("'", "")
		}).text(this.text);
		$("input[name='"+key+"']").val(this.text);
	});
	
		
	$("input[name=companyName1]").DefaultValue("Company Name");
	$("input[name=companyName2]").DefaultValue("Company Name");
	$("input[name=typeOfActivity1]").DefaultValue("Type of Activity");
	$("input[name=typeOfActivity2]").DefaultValue("Type of Activity");
	$("input[name=typeOfActivity3]").DefaultValue("Type of Activity");
	$("input[name=typeOfActivity4]").DefaultValue("Type of Activity");
	$("input[name=typeOfActivity5]").DefaultValue("Type of Activity");
	$("input[name=name]").DefaultValue("Name");
	$("input[name=phone]").DefaultValue("Mob. Phone");
	$("input[name=fax]").DefaultValue("Fax");
	$("input[name=adress]").DefaultValue("Adress Line");
	$("input[name=adress2]").DefaultValue("Adress Line 2");
	$("input[name=email]").DefaultValue("Email");
	$("input[name=www]").DefaultValue("Website Adress");
	$("input[name=phone2]").DefaultValue("Phone");
	$("input[name=wwww]").DefaultValue("Website Adress");
	$("input[name=aditional]").DefaultValue("Aditional Info");
	
	//apply css to the text
	$.each(cardData[side].textProperties, function(key){
		
		if(key == "__comment") return;
		
		var key = key;
		$.each(this, function(rule, value){
			$("#" + key).css(rule, value);
			
		});

	});
	
	//remove any floating images
	$(".aditionalImageContainer").remove();
	createdImages = 0;
	
	//add images
	$.each(cardData[side].floatingImages, function(){
		if(this.path == "") return;
		renderFloatingImage(this.path, this.left + "px", this.top + "px");
	});
	
	//change background
	if(cardData[side].background == "none"){
		cardColor = "#fff";
		$(cardProperties.container).css("background", "#fff");
	}else{
		 url = cardData[side].background;
		 start = url.indexOf('images');
		 result = "url(media/" + url.substr(start).replace("'", "").replace('"', "");
		
		cardImage = result;
		$(cardProperties.container).css("background", result);
	}
	
	//first try to delete lines
	$(".horizontalLine").remove();
	$(".verticalLine").remove();
	
	
	//create vertical lines
	$.each(cardData[side].verticalLine, function(key, value){
		if(key == "default") return;
		
		createLine("verticalLine", side, key);
	});	
	//create horizontal lines
	$.each(cardData[side].horizontalLine, function(key, value){
		if(key == "default") return;
		
		createLine("horizontalLine", side, key);
	});
}

var galleries = $('.ad-gallery').adGallery({
	width: 1144,
	height: 200,
	start_at_index: 0,
	slideshow:{
		start_label: "",
		stop_label: ""
	}
});

var renderCardFormat = "pdf";
$("#saveCardTypeJPG, #saveCardTypePDF").live("click", function(){
	if($(this).attr("id") == "saveCardTypePDF")
		renderCardFormat = "pdf";
	else
		renderCardFormat = "image";
});

//download image/pdf
$(".cardPreviewDlButton").live("click", function(){
	data = $.param(cardData);

	//set data
	$(".dataInputHidden").val(JSON.stringify(cardData));
	//set action
	$(".actionInputHidden").val(renderCardFormat);
	
	//submit form
	$(".cardSaveForm").submit();
	
});

$(function(){
	save = function(side){
		//---------------------Text Section---------------------//
		$.each($(".cardElementDraggable"), function(){
			name = $(this).attr("id");

			//extract font-size from style tag
			styletag = $(this).attr('style');
			stylestemp = styletag.split(';');
			styles = {};
			c = '';
			for (var x in stylestemp) {
				c = stylestemp[x].split(':');
				styles[$.trim(c[0])]=$.trim(c[1]);
			}
			
			
			cardData[side].textProperties[name].color = $(this).css("color");
			cardData[side].textProperties[name]['font-size'] = styles["font-size"];
			cardData[side].textProperties[name]['font-family'] = $(this).css("font-family");
			cardData[side].textProperties[name]['font-weight'] = $(this).css("font-weight");
			cardData[side].textProperties[name]['font-style'] = $(this).css("font-style");
			cardData[side].textProperties[name]['text-decoration'] = $(this).css("text-decoration");
			
			cardData[side].textPositions[name].left = $(this).css("left");
			cardData[side].textPositions[name].top = $(this).css("top");
			
			cardData[side].textPositions[name].text = $(this).text();
			cardData[side].textPositions[name].width = $(this).width();
		});
		
		//card background
		if($(cardProperties.container).css("background-image") == "none"){
			cardData[side].background = $(cardProperties.container).css("background-color")
		}else{
			cardData[side].background = $(cardProperties.container).css("background-image");
		}
			
		//aditional images
		$.each($(".aditionalImage"), function(i){
			leftOffset = $(this).parent().parent().position().left;
			topOffset = $(this).parent().parent().position().top;

			
			cardData[side].floatingImages[i + 1].left = leftOffset;
			cardData[side].floatingImages[i + 1].top = topOffset;
			cardData[side].floatingImages[i + 1].width = $(this).width();
			cardData[side].floatingImages[i + 1].height = $(this).height();
			cardData[side].floatingImages[i + 1].path = $(this).attr("src");
		});
		
		//card dimensions
		cardData.dimensions.width = $(cardProperties.container).width();
		cardData.dimensions.height = $(cardProperties.container).height();
		
		
		for(i = 1; i <= 2; i++){
			if(i == 1) type = "horizontalLine"; else type = "verticalLine";
			
			//delete old lines
			$.each(cardData['side' + cardProperties.currentCardSide][type], function(key, value){
				if(key != "default")
					delete cardData['side' + cardProperties.currentCardSide][type][key];
			});
			//add new lines
			$.each($("." + type + " div"), function(i, item){
				//set data
				cardData['side' + cardProperties.currentCardSide][type][i] = {
					"left": $(item).parent().position().left - 47,
					"top": $(item).parent().position().top - 52,
					"color": $(item).css("border-color"),
					"thickness": parseInt($(item).css("border-right-width")),
					"style": $(item).css("border-right-style")
				};
			});
		}
		
		//save card size in MM
		 value = $(".cardDimensions").val();

		if(cardProperties.position == "h"){
			cardData.dimensionsInMM.width = cardSettings.sizes[value][2];
			cardData.dimensionsInMM.height = cardSettings.sizes[value][3];
		}else{
			cardData.dimensionsInMM.width = cardSettings.sizes[value][3];
			cardData.dimensionsInMM.height = cardSettings.sizes[value][2];
		}
		
	};
});

//save user created card
$(".toolsISave").live("click", function(){
	$.colorbox({
		width: 500,
		height: 220,
		href: "home/save/",
		scrolling:false,
		data: {action: "html"},
		onComplete: function(){
			var text = $(".saveWorkInput");
			
			$(".saveWorkInput").keydown(function(e) {

				//uptade text
				window.setTimeout(function() {
					$(".saveWorkName").text($(text).val());
				}, 1);
				
				$(".saveWorkInput").simplyCountable({
					counter: "#descCounter",
					maxCount:30,
					strictMax: true
				});
			});
			
			
			//save
			$(".saveWorkButton").live("click", function(){
				
				//check if name isn't empty
				if(text.val().length < 2){
					console.log(text);
					showError("Please enter the name");
					return;
				}
				
				//save card data
				$(function(){
					save("side" + cardProperties.currentCardSide);
				});
				
				$.post("home/save/", {action: "saveCard", name: text.val(), data: cardData},
					function(response){
						if(response.response){
							$(".saveWorkMessageBox").css("visibility", "visible").text("Your card has been saved successfully.");
							$(".saveWorkCreatedLink").text(response.link);
							$(".saveWorkCreatedLink").parent().attr("href", response.link);
							$(".saveWorkLinkContainer").fadeIn("fast");
						}else{
							$(".saveWorkMessageBox").css("visibility", "visible").text(response.error);
						}
					}, "json"
				);
			});
		}
	});
});

//render image/pdf
$(".toolsISaveImage, .toolsISavePdf").click(function(){
	saveAs = $(this).attr("id");
	
	renderCardFormat = saveAs;
	
	$(function(){
		save("side" + cardProperties.currentCardSide);
	});

	
	$.colorbox({
		href: "image/render_image",
		data: {data: cardData, action: 'preview', selectedFormat: saveAs},
		width: 700,
		title: false,
		scrolling:false,
		onComplete: function() {
			var id = setInterval(function(){
				getWidth = $(".cardPreviewContainerLeft").width() + $(".cardPreviewContainerRight").width() + 160

				//delete generated pics from /temp/ folder
				$.post("image/delete_temp", {image1: $(".cardPreviewContainerLeft img").attr("src"), image2: $(".cardPreviewContainerRight img").attr("src")}, function(){});
				
				if(getWidth > 160){
					$(this).colorbox.resize({width: getWidth});
					clearInterval(id);
				}
			}, 500);
				
			$( ".progressBar" ).progressbar({
				value: 100
			});
			
			var paymentCode = $(".paymentCode").attr("id");

			//###### for demo purposes
			setTimeout(function(){
				//remove progressbar
				$( ".progressBar" ).progressbar('destroy');
				
				//create completed message
				$(".cardPreviewDlProgressText")
					.animate({
						top: "0px",
						left: "93px"									
					})
					.html(
						$("<div>").addClass("cardPreviewDlProgressIcon displayInline")
					)
					.append("Payment has been completed!");
				
				$(".cardPreviewDlButtonDisable").fadeOut('slow').remove();
			}, 6000)

			
			//keep checking if payment was made
			// var interval_id = setInterval(function(){
			
			// 	$.post("home/payment/", {code: paymentCode}, 
			// 		function(response){
			// 			if(response.payment_status){
			// 				clearInterval(interval_id);
							
			// 				//remove progressbar
			// 				$( ".progressBar" ).progressbar('destroy');
							
			// 				//create completed message
			// 				$(".cardPreviewDlProgressText")
			// 					.animate({
			// 						top: "0px",
			// 						left: "93px"									
			// 					})
			// 					.html(
			// 						$("<div>").addClass("cardPreviewDlProgressIcon displayInline")
			// 					)
			// 					.append("Payment has been completed!");
							
			// 				$(".cardPreviewDlButtonDisable").fadeOut('slow').remove();
			// 			}
			// 		},"json"
			// 	);
			
			// }, 2000);
		}
	});
});

$(".aboutUseButton").click(function(){
	$.colorbox({
		href: "home/about_us",
		width: 800
	});
});

//contact us window
$(".contactUsButton").click(function(){
	$.colorbox({
		href: "home/contact_us",
		data: {action: "getHtml"},
		width:600,
		height:300,
		onComplete: function(){
			$(".subject").DefaultValue("Subject:");
			$(".email").DefaultValue("Email:");
			$(".message").DefaultValue("Message:");
			
			$(".contactUsSendBg").live("click", function(){
				var errors = 0;
				//loop trough all fields
				$.each($(".contactUsFormContainer input, .contactUsFormContainer textarea"), function(){
				
					if($(this).val().length < 10){
						$(this).css("border", "1px solid #F58787");
						errors++;
					}else{
						$(this).css("border", "1px solid green");
						errors--;
					}
				});
				
				if(errors == -3){
					$.post("home/contact_us", {
												action: "send", 
												subject: $(".subject").val(),
												email: $(".email").val(),
												message: $(".message").val()
											},
						function(response){
							if(response.response){
								$(".contactUsFormContainer").append(
									$("<div>")
										.addClass("saveWorkMessageBox displayInline")
										.text(response.text)
										.css("visibility", "visible")
								);
							}else{
								showError("Unknown error occured, please contact administrators.");
							}
						}, "json"
					);
				}else{
					showErrors("Please fill in all fields");
				}
			});
		}
	});
});

//hide gallery 
$(".slideShowHide").live("click", function(){
	$("#gallery").animate({
		width: "0px",
		height: "0px",
		left: "-517px",
	}, 400, function(){
		$(".galery_minimized")
			.css("display", "block")
			.draggable({distance: 4});
	});
	
});

//show galery
$(".galeryShowIcon").live("click", function(){
	$(".galery_minimized").css("display", "none");
	
	$("#gallery").animate({
		width: "1144px",
		height: "381px",
		top: "10px",
		left: "0px"
	}, 400);
});

//drag gallery
$("#gallery").draggable({
    handle: ".slideShowMove",
	start: function(){
		updateIndex("#gallery");
	}
});

//set preset from gallery
$(".ad-galery-set").live("click", function(){
	path = $(".ad-image img").attr("data");
	loadCardDataJson(path);
});


//change tools tab
var toolsSelectedTab = "toolsTabText";

$(".toolsSelectTab").live("click", function(){
	action = $(this).attr("action");
	
	//switch classes
	$(".toolsTabSelected").removeClass("toolsTabSelected").addClass("toolsSelectTab");
	$(this).addClass("toolsTabSelected");
	
	//hide current tab
	$("#" + toolsSelectedTab).css("visibility", "hidden");
	
	//show selected tab
	$("#" + action).css("visibility", "visible");
	
	toolsSelectedTab = action;
	
});

//make tools and text fields draggable
$("#toolsBar, #inputContainer").draggable({
	containment: $("body"),
	distance: 7,
	start: function(){
		updateIndex("#" + $(this).attr("id"));
	}
});

$(".toolsHoverBg").wrap(
		$("<div>")
			.addClass("displayInline toolsSelectContainer")
			.bind("mouseover", function(){
				$(this).addClass("toolsSelected");
			})
			.bind("mouseout", function(){
				$(this).removeClass("toolsSelected");
			})
			.bind("click", function(){
				$(this).addClass("toolsSelected");
			})
	);
	

var oldValue = "switchHorizontal";
//------------------------Change Properties------------------------//
$(".changeCardPosition").click(function(){
	var action = $(this).attr('action');
	
	//check if user isn't clicking on same field
	if(action == oldValue) return;
	oldValue = action;
	
	$(".changeCardPosition").removeClass("radioSelected").addClass("radio");
	
	$(this).addClass("radioSelected");
	
	//find the right action
	switch(action){
		case "switchHorizontal":
			//update position
			cardProperties.position = "h";

			changeCardSize(cardSettings.sizes[cardProperties.size][0], cardSettings.sizes[cardProperties.size][1], "horizontal");
		break;

		case "switchVertical":
			//update position
			cardProperties.position = "v";

			changeCardSize(cardSettings.sizes[cardProperties.size][1], cardSettings.sizes[cardProperties.size][0], "vertical");
		break;
		
		//if no action was found show error
		default:
			showError("Requested action '"+action+"' was not found!");
		break;
	}
});

$(".cardDimensions").change(function(){
	value = $(this).val();
	//update card size
	cardProperties.size = value;
	
	//and now change the actual size
	if(cardProperties.position == "h")
		changeCardSize(cardSettings.sizes[value][0], cardSettings.sizes[value][1]); //for horizontal position
	else
		changeCardSize(cardSettings.sizes[value][1], cardSettings.sizes[value][0]); //for vertical position

});

//rolers
$(".h_roler_draggable, .v_roler_draggable").draggable({
	containment: "parent",
	drag: function(event, ui){
		if($(this).attr("action") == "horizontal")
			$("#cardElementsWrapper").css("left", ui.position.left + "px");
		else
			$("#cardElementsWrapper").css("top", ui.position.top + "px");
	}
});

var gridState = false;

$(".alignText, .grid, .lines").click(function(){
	action = $(this).attr("action");
	parentWidth = $(".cardElementDraggable").parent().width();
	
	switch(action){
		case "left":		
			$(".cardElementDraggable").css("left", "2px");
		break;
		
		case "right":
			$.each($(".cardElementDraggable"), function(){
				$(this).css("left", (parentWidth - $(this).width()) - 10);
			});

		break;
		
		case "middle":
			$.each($(".cardElementDraggable"), function(){
				$(this).css("left", (parentWidth / 2) - ($(this).width() / 2) );
			});
		break;
		
		case "horizontalLine":
			createLine("horizontalLine", "side" + cardProperties.currentCardSide, "default");
		break;		

		case "verticalLine":
			createLine("verticalLine", "side" + cardProperties.currentCardSide, "default");
		break;
		
		//delete selected lines
		case "delete":
			$.each(selectedObjects, function(i, obj){
				//check if this is a line id
				if($.inArray("line", obj.split("_")) == -1) 
					return;
				
				$("#" + obj).parent().remove();
				
				setTimeout(function(){
					//remove from object list
					selectedObjects.splice($.inArray(obj, selectedObjects), 1);
				}, 10);
			});
		break;
		
		case 'grid':
			if(gridState == true){
				$("#cardElementsWrapperBorder").css("background-image", "");
				gridState = false;
			}else{
				$("#cardElementsWrapperBorder").css("background-image", "url('media/images/grid.gif')");
				gridState = true;
			}
		break;
		
		default:
			showError("Requested action '"+action+"' was not found!");
		break;
	}

});

//change line style
$(".linesStyle").change(function(){

	var lineStyle = $(".linesStyle").val();
	
	$.each(selectedObjects, function(i, obj){
	
		//check if this is a line id
		if($.inArray("line", obj.split("_")) == -1) 
			return;

			color = $("#" + obj).css("border-top-color");
			
			$("#" + obj).css({
				borderTop: linesStyles[lineStyle].width,
				borderRight: linesStyles[lineStyle].width,
				borderTopStyle: linesStyles[lineStyle].style,
				borderRightStyle: linesStyles[lineStyle].style,
				borderTopColor: color,
				borderRightColor: color
			});
	});
});


function createLine(position, side, action){
	var timeStamp = new Date().getTime();
	if(position == "horizontalLine"){
		if(cardProperties.position == "h")
			width = cardSettings.sizes[cardProperties.size][0];
		else
			width = cardSettings.sizes[cardProperties.size][1];
			
		height = 1;
	}else{
		if(cardProperties.position == "h")
			height = cardSettings.sizes[cardProperties.size][1];
		else
			height = cardSettings.sizes[cardProperties.size][0];
		
		width = 1;
	}
	$("#cardContainer").append(
		$("<div>")
			.draggable({
				containment: "parent"
			})
			.css({
				position: "absolute",
				height: (parseInt(height) + 3) + "px",
				width: (parseInt(width) + 3) + "px",
				paddingTop: "1px",
				left: parseInt(cardData[side][position][action].left) + 47 + "px ",
				top: parseInt(cardData[side][position][action].top) + 52 + "px "
			})
			.addClass(position)
			.attr("position", position)
			.html(
				$("<div>")
					.css({
						width: width + "px",
						height: height + "px",
						borderTopWidth: parseInt(cardData[side][position][action].thickness) + "px ",
						borderRightWidth: parseInt(cardData[side][position][action].thickness) + "px ",
						borderTopStyle: cardData[side][position][action].style,
						borderRightStyle: cardData[side][position][action].style,
						borderColor: cardData[side][position][action].color
					})
					.attr("id",  "line_" + timeStamp)
					.addClass("cardLines")
			)
			.bind("click", function(){
				position = $(this).attr("position");

				selectObject($("div ", this).attr("id"), "lines");
				
				//if this is already selected
				if($(this).hasClass("selected")){
					$(this).removeClass("selected").css("border", "0px");
				}else{
					$(this).addClass("selected").css("border", "1px dashed #ccc");
				}
			})
	);
}

//change card bg
$(".toolsIconsCardBgBucket").colorpicker({
	altField: $(cardProperties.container),
	altProperties: 'background-color', 
	showOn: "button",
	buttonImage: "images/calendar.gif",
	buttonImageOnly: true,
	buttonText: "Change card background color",
	buttonClass: "toolsIconsCardBgBucketButton toolsIcons displayInline",
	select: function(e){
		cardColor = "#" + $(this).val();
		$(cardProperties.container).css('background-image', '');
	}

});

/**
remove card bg or color
these are the vars that contains bg color and image
var cardImage = "";
var cardColor = "";
**/
$(".toolsIconsCardBgDelete").click(function(){
	action = $(this).attr("action");
	console.log(cardImage);
	console.log(cardColor );
	if(action == "image"){
		$(cardProperties.container).css('background-image', '');
		$(cardProperties.container).css('background-color', cardColor);
	}else{
		$(cardProperties.container).css('background-color', '#ffffff');
		$(cardProperties.container).css('background-image', cardImage);
	}
});


var fileList;
var createdImages = 0;
var selectedImage;

$(".toolsIconsCardBgImages, .toolsIconsCardFloatingImage").colorbox({
	width: 520,
	height: 630,
	href: false,
	title: " ",
	scrolling:false,
	html: function(){
		var requestAction = $(this).attr("action");
		var pagination = $("<div>").addClass("popupPaginationContainer"); //add all bg elements here
		var finalHtml = $("<div>").addClass("displayInline");
		
		//first get all images names from the server
		$.post("image/pull_images_data", {action: requestAction},
			function(response){

				if(requestAction == "bg-images") var path = "bgImagesPath"; else var path = "floatingImagesPath";

				$(finalHtml).append(renderCardBackground(response.files.names, 1, cardProperties.imagesPerPage, response.files.path, requestAction)); //get images rendered
				
				//we need this so we could resize floating image if it's to big
				cardWidth = cardProperties.container.width();
				cardHeight = cardProperties.container.height();
				
				//build form for file uploading
				$(finalHtml).prepend(
					$("<form>", {
						method: "post",
						action: "image/upload/",
						target: "IframeUploadFiles",
						enctype: "multipart/form-data"
					}).addClass("uploadImageForm")
						//hidden data card width
						.append(
							$("<input />", {
								type: "hidden",
								name: "cardWidth",
								value: cardWidth
							})
						)
						//hidden data card height
						.append(
							$("<input />", {
								type: "hidden",
								name: "cardHeight",
								value: cardHeight
							})
						)
						//create button for image selecting
						.append(
							$("<div>")
							.addClass("uploadImageInputButton displayInline")
							.html(
								//inside put the actual file input field
								$("<input />",{
									type: "file",
									name: "fileName"
								}).addClass("uploadImageInput")
								  .bind("change", function(){
									$(".uploadImageSubmit").css("visibility", "visible");
								  })
							)
						)
						.append(
							$("<div>")
								.addClass("uploadImageText coolText displayInline")
								.text("Upload Image")
						)
						//submit button
						.append(
							$("<div>")
								.addClass("uploadImageSubmit coolText displayInline")
								.text("Upload")
								.bind("click", function(){
									$(".uploadImageForm").submit();
									//hide submit button
									$(".uploadImageSubmit").css("visibility", "hidden");
									//show loading icon
									$(".uploadImageLoading").fadeIn();
								})
						)
						//iframe here we gonna upload files
						.append(
							$("<iframe>",{
								src: "image/upload/",
								name: "IframeUploadFiles"
							})
							.bind("load", function(){
								//hide loading icon
								$(".uploadImageLoading").fadeOut();
								
								//get content of the iframe
								getJsonString = $(this).contents().find("body").text();
								
								//parse it to json
								filesResponse = $.parseJSON(getJsonString);
								if(filesResponse != null){
									//check for errors
									if(filesResponse.errors){
										showError(filesResponse.errorList["1"]);
									}else{
										if(requestAction == "floating-image")
											renderFloatingImage(filesResponse.path, null, null);
										else
											$("#cardContainer").css("background-image", "url(" + filesResponse.path + ")");
										$.colorbox.close();
									}
								}
							})
							.css("display", "none")
						)
						.append(
							$("<div>")
								.addClass("uploadImageLoading displayInline")
						)
						.append(
							$("<div>")
								.addClass("displayInline selectImageCategoryList")
								.html(
									$("<select>")
										.html(response.dir_list)
										.addClass("selectImageCategorySelect")
										.bind("change", function(){
											$.post("image/pull_images", {action: requestAction, dir: $(this).val()},
												function(response){
													$(".popupImagesContainer").html('').html(renderCardBackground(response.files.names, 1, cardProperties.imagesPerPage, response.files.path, requestAction));
												}, "json"
											);
										})
								)
							)
				)

				fileList = response.files.names;
				
				//build pagination
				var pages = Math.ceil(response.files.count / cardProperties.imagesPerPage);
		
				//create previous button
				pagination.append(	
					$("<div>")
						.addClass("popupPaginationPrevious displayInline")
						.bind("click", function(){
							paginationSwitchPage("previous", fileList, response.files.path, pages, requestAction);
						})
					);			
				for(i = 1; i <= pages; i++){
					if(i == 1) selectedClass = "popupPaginationLinkCurrent"; else selectedClass = "";
					//build links
					newLink = $("<div>").addClass("popupPaginationLink displayInline " + selectedClass)
							.attr("page", i)
							//attach click event for page changes
							.bind("click", function(){								
								paginationSwitchPage(parseInt($(this).attr('page')), fileList, response.files.path, pages, requestAction);
							})
							.append(
								$("<div>")
										.text(i)
										.addClass("popupPaginationLinkText")
							);
					
					$(pagination).append(newLink);	
				}
				
				//create next button
				pagination.append(	
					$("<div>")
						.addClass("popupPaginationNext displayInline")
						.bind("click", function(){
							paginationSwitchPage("next", fileList, response.files.path, pages, requestAction);
						})

					);
				
			}, "json"
		);

		return $(finalHtml).append(pagination).append($("<div>").addClass("popUpLoadingBg")).append($("<div>").addClass("popUpLoadingLoading"));
	},
	onComplete: function(){
		$(".selectImageCategorySelect").selectmenu({width: 190});
	}
});

paginationCurrentPage = 1;

function paginationSwitchPage(goTo, fileList, path, pages, action){

	if(goTo == "previous"){
		if(paginationCurrentPage > 1) goTo = paginationCurrentPage - 1; else return;

	}else if(goTo == "next"){
		if(paginationCurrentPage < pages ) goTo = paginationCurrentPage + 1; else return;
	}
	
	start = ((goTo * cardProperties.imagesPerPage) - cardProperties.imagesPerPage) + 1;
	end = start + cardProperties.imagesPerPage;
	
	//hide everything until it fully loads
	$(".popUpLoadingBg, .popUpLoadingLoading").fadeIn("fast");
	//render new html
	$(".popupImagesContainer").html('').html(renderCardBackground(fileList, start, end, path, action) ).ready(function(){
		$(".popUpLoadingBg, .popUpLoadingLoading").fadeOut("fast");
	});	
	
	pagesLinks = $(".popupPaginationLink");
	$(".popupPaginationLink").removeClass("popupPaginationLinkCurrent");

	$.each(pagesLinks, function(){
		if($(this).attr("page") == goTo) $(this).addClass("popupPaginationLinkCurrent");
	});
	paginationCurrentPage = goTo;
}

function renderCardBackground(data, start, end, path, action) {
	var container = $("<div>").addClass("popupImagesContainer"); //add all bg elements here
	var i = 0;
	$.each(data, function(){
		i++;
		if(i < start || i > end) return
		
		if(action == "bg-images"){
			//build image object
			newImage = $("<div>")
							.addClass("popupSelectImage displayInline")
							.attr("path", path + this)
							.bind("click", function(){
								cardImage = "url(" + $(this).attr("path") + ")";
								$(cardProperties.container).css({
										backgroundImage: "url(" + $(this).attr("path") + ")",
									});
								$.colorbox.close();
							})
							.html(
								$("<img />")
									.attr("src", path + this)
							);
			
			$(container).append(newImage);
		}else{
			//build floating image object
			newImage = $("<div>")
							.addClass("popupSelectImageFloating displayInline")
							.css("background-image", "url(" + path + this + ")")
							.attr("path", path + this)
							.bind("click", function(){
								renderFloatingImage($(this).attr("path"), null, null );
								//close popup window
								$.colorbox.close();
							});
			//return created list
			$(container).append(newImage);
		}
			
	});
		
	
	return(container);
}


//render floating image once user selects it
function renderFloatingImage(path, left, top){
	//check if limit wasn't reached since max amout of images is 3
	if(createdImages >= 3){
		showError("You can't add more then 3 images at the time!");
		return;
	}
	
	//create random id for that image
	rndId = Math.floor(Math.random(999999)*99999999999);
	
	createdImages = createdImages + 1;

	//when user selects image create new floating image and add it to the card
	container = $("<div>")
					.addClass("aditionalImageContainer")
					.attr("id", rndId)
					.css({
						left: left,
						top: top
					})
					//attatch event for selection
					.bind("click", function(){
						current = $(this).attr("id");

						if(selectedImage == current){
							$(this).css("border", "0px");
							selectedImage = null;
						}else{
							$(".aditionalImageContainer").css("border", "0px");
							$(this).css("border", "1px dashed #3A3737");
							selectedImage = current;
						}
					}).bind("mouseover", function(){
						$(this).find(".removeFloatingImage").css("visibility", "visible")
					}).bind("mouseout", function(){
						$(this).find(".removeFloatingImage").css("visibility", "hidden")
					})
					//create delete icon
					.append(
						$("<div>")
							.addClass("removeFloatingImage toolsIcons")
							.bind("click", function(){
								$(this).parent().remove();
								createdImages = createdImages - 1;
								//if this image was selected
								if($(this).attr("id") == selectedImage)
									selectedImage = null;
							})
					)
					.append(
							$("<img />")
							.attr("src", path)
							.addClass("aditionalImage")
							.bind('load', function(){
								$(this).resizable({
									containment: $(cardProperties.container),
									autoHide: true
								});
							})
						).draggable({
							containment: 'parent'
						}).css("position", "absolute");
	
	
	//append created object to card conainer					
	$(cardProperties.container).prepend(container);
}


//---------------------Floating image options--------------------//

//rotate image 90 degress
$(".floatinImageTool").live('click', function(){
	//check if any image was selected
	if(selectedImage == null || selectedImage == "undefined"){
		showError("Please select image you wish to rotate!");
		return;
	}
	//select image container
	obj = $("#" + selectedImage).find(".aditionalImage");
	
	//destroy resizable since it doesn't update it self with the image
	obj.resizable("destroy");
	
	switch($(this).attr("action")){
		case 'rotate':
			//send ajax request to the server since we gonna rotate it with php
			$.post("image/rotate/", {path: obj.attr("src") },
				function(response){
					obj.attr("src", response.url);
					
					//get right dimensions
					var newHeight = cardProperties.container.height();
					var newWidth = response.width / (response.height / newHeight);
					
					obj.css({
						width: newWidth + "px",
						height: newHeight + "px"
					});
					
					obj.resizable({
						containment: cardProperties.container,
						autoHide: true
					});
				}, "json"
			);
		break;
		
		//expand image to the card size
		case 'expand':
			width = cardProperties.container.width();
			height = cardProperties.container.height();
			
			$(obj.parent()).animate({
				left: "47px",
				top: "50px"
			}, 900);
			
			obj.animate({
				width: width,
				height: height
			}, 1000, function(){
				obj.resizable({
					containment: cardProperties.container,
					autoHide: true
				});
			});
		break;
	
	}
});


//here we gonna change card dimensions
function changeCardSize(width, height){
	//convert to int
	width = parseInt(width);
	height = parseInt(height);
	
	$("#leftContainerBlock").animate({width: width + 80, queue: false}, 1000);
	
	$("#pageContainer").animate({ width: width + 640, queue: false }, 1000);

	//change dimensions
	$(cardProperties.container).animate({
		width: width + "px",
		height: height + "px",
		queue: false
	}, 1000);
	
	newWidth = width - cardProperties.innerBorderMargins;
	newHeight = height - cardProperties.innerBorderMargins;
			
	//update text wrapper size
	$(cardProperties.elementsWrapper).animate({
		width: newWidth + "px",
		height: newHeight + "px"
	}, 1000);
	
	//change rulers
	$("#h_roler").animate({width: (width + 20) + "px"}, 1000);
	$("#v_roler").animate({height: (height + 20) + "px"}, 1000);
	
	$(".horizontalLine, .horizontalLine div").animate({width: width + "px", top: "55px"}, 1000);
	$(".verticalLine, .verticalLine div").animate({height: height + "px", left: "50px"}, 1000);
	
	
	//place text in the right position
	$(".cardElementDraggable").animate({left: "0px"});
	
	
}

//----------------------Update card text----------------------//
$(".textInput").keydown(function(e) {
    var text = $(this);
	var elementName = "#" + $(this).attr("name");//get element name
	var oldHeight = $(elementName).height();

	//uptade text
    window.setTimeout(function() {
		$(elementName).text($(text).val());
    }, 1);
	
});


//Align text to the card left
$(".toolsIconsAlignRight").click(function(){
	var cardSize = cardProperties.container.width() - 44 ;
	
	if(EditState == "all"){
		$.each($(".cardElementDraggable"), function(){
			offset = cardSize - $(this).width();
			
			$(this).css("left", offset + "px");
		});
	}else{
		$.each(selectedObjects, function(){
			offset = cardSize - $("#" + this).width();
			
			$("#" + this).css("left", offset + "px");
		});
	}
});

//Align text to the card cight
$(".toolsIconsAlignLeft").click(function(){
	if(EditState == "all"){
		$(".cardElementDraggable").css("left", "0px");
	}else{
		$.each(selectedObjects, function(){
			$("#" + this).css("left", "0px");
		});
	}
});

//Align text to the card center
$(".toolsIconsAlignCenter").click(function(){
	var cardSize = cardProperties.container.width() - 44 ;
	
	if(EditState == "all"){
		$.each($(".cardElementDraggable"), function(){
			offset = (cardSize  / 2) - ($(this).width() / 2);
			
			$(this).css("left", offset + "px");
		});
	}else{
		$.each($(selectedObjects), function(){
			offset = (cardSize  / 2) - ($("#" + this).width() / 2);
			
			$("#" + this).css("left", offset + "px");
		});
	}
});

//Align text to the upper edge
$(".toolsIconsAlignVerticalTop, .toolsIconsAlignSpacesHorizontal, .toolsIconsAlignVerticalBottom, .toolsIconsAlignVerticalCenter, .toolsIconsAlignSpacesVertical").click(function(){
	var cardSize = cardProperties.container.width() - 44 ;
		//check if anything is selected
		if(selectedObjects.length < 2){
			showError("Please select more then one block of text");
			return;
		}
		
		//get object that gonna be used to align other objects
		var leader;
		var found = false;
		
		$.each($(".textInput"), function(){
			if(found) return;
			
			if($.inArray($(this).attr("name"), selectedObjects) > -1){
				leader = $("#" + $(this).attr("name"));
				found = true;
			}
		});
		
		//get right action
		switch($(this).attr("action")){
			case 'verticalTop':
				var offset = leader.css("top");
					
				$.each(selectedObjects, function(){
					//skip leader object
					if(this == leader.attr("id"))
						return;
					$("#" + this).css("top", offset);
				});
			break;			
			
			case 'verticalBottom':
				$.each(selectedObjects, function(){
					//skip leader object
					if(this == leader.attr("id"))
						return;
					
					offset = parseInt(leader.css("top")) + leader.height() + 5;
					
					$("#" + this).css("top", offset);
				});
			break;			
			
			case 'verticalMiddle':
				$.each(selectedObjects, function(){
					//skip leader object
					if(this == leader.attr("id"))
						return;
					
					offset = (parseInt(leader.css("top")) + ((leader.height() + 5) / 2)) - ($("#" + this).height() / 2);
					
					$("#" + this).css("top", offset);
				});
			break;
			
			case 'horizontalSpaces':
				// Cache the elements
				var $obj = $('.textSelected');
				
				$obj = $obj.sort(function(a, b) {
					return $(a).position().left - $(b).position().left;
				});

				var obj_high = $obj.first();
				var obj_low = $obj.last();
				var elm_width = 0;
				
				elm_width = obj_low.position().left - obj_high.position().left;
				
				//spaces
				var spaces = elm_width / ($obj.length);
				console.log(spaces);
				topPosition = obj_high.position().left + spaces;

				$.each($obj, function(i, item){
					if(i == 0 || i == $obj.length -1) return;
					
					$(item).animate({
						left: topPosition + "px"
					});
					
					topPosition = topPosition + spaces;

				});

			break;
			
			case 'verticalSpaces':
				// Cache the elements
				var $obj = $('.textSelected');
				
				$obj = $obj.sort(function(a, b) {
					return $(a).position().top - $(b).position().top;
				});

				var obj_high = $obj.first();
				var obj_low = $obj.last();
				var elm_height = 0;
				
				//gauti container dydi
				$.each($obj, function(i, item){
					if(i == 0 || i == $obj.length - 1) return;
					
					elm_height = elm_height + $(item).height();
				});

				//gauti tarpus
				spaces = (( (obj_low.position().top - obj_high.position().top) - obj_high.height())
						- elm_height) / ($obj.length - 1);

				//gauti pirma top pozicija
				topPosition = obj_high.position().top + obj_high.height()  + spaces;
				
				$.each($obj, function(i, item){
					if(i == 0 || i == $obj.length -1) return;
					
					$(item).animate({
						top: topPosition + "px"
					});
					
					topPosition = topPosition + $(item).height() + spaces;
					
				});	
			break;
		}
});

//----------------------Change card side------------------------//
$(".sideTab").click(function(){
	side = $(this).attr("side");
	
	if(side == 	cardProperties.currentCardSide) return;
	
	$(".sideTab").removeClass("tabSelected");
	
	$(this).addClass("tabSelected");
	
	getCardData(side, "side" + cardProperties.currentCardSide, "side" + side);
	
	cardProperties.currentCardSide = side;
});

function getCardData(side, switchFrom, switchTo){

	//get text data
	$.each($(".cardElementDraggable"), function(){
		key = $(this).attr("id");
		cardData[switchFrom].textPositions[key].left = $(this).position().left;//update left position
		cardData[switchFrom].textPositions[key].top = $(this).position().top;//update top position
		cardData[switchFrom].textPositions[key].text = $(this).text();//update text
	});
	
	//load new data
	loadCardData(switchTo, false);
}

//--------------------Change card text properties-------------//
//make text draggable
$(".cardElementDraggable").draggable({
	containment: 'parent',
	distance: 4
});

//call other function on these to events and work data out there
$(".cardElementDraggable").click(function(){ selectObject($(this).attr("id"), 'click'); });
$(".textInput").focus(function(){ selectObject( $(this).attr("name"), 'focus') });

//select text objects
var selectedObjects = new Array();
var selectedObjectsForColor = "";
var selectedObjectsForColorAll = ".cardElementDraggable, .cardLines";
var previousSelectedObj;

//get selected objects
function selectObject(id, event){

	//when nothing is selected and state is `all` change it to `selected`
	if(selectedObjects.length == 0 && EditState == "all"){
		$("#changeEditState").removeClass("stateAll").addClass("stateSelected");
		EditState = "selected";
	}

	if(event == "focus"){
		$("#" + id).css("border", "1px dashed #3a3737");
		$("#" + previousSelectedObj).css("border", "0px");
		
		//check if we have selected it already
		if($.inArray(id, selectedObjects) == -1)
			selectedObjects.push(id);
		
		if($.inArray(previousSelectedObj, selectedObjects) > -1)
			selectedObjects.splice($.inArray(previousSelectedObj, selectedObjects), 1);
		
		previousSelectedObj = id;

	}else{
		//check if we have selected it already
		if($.inArray(id, selectedObjects) > -1){
			//remove this element
			selectedObjects.splice($.inArray(id, selectedObjects), 1);
			if(event != "lines")
				$("#" + id).css("border", "0px").removeClass("textSelected");
			
			//when user uncheks all selections and state is `selected` change it to `all`
			if(selectedObjects.length == 0 && EditState == "selected"){
				$("#changeEditState").removeClass("stateSelected").addClass("stateAll");
				EditState = "all";
			}
		}else{
			selectedObjects.push(id);
			
			if(event != "lines")
				$("#" + id).css("border", "1px dashed #3a3737").addClass("textSelected");
		}
	}
	
	//we need to build string which contains selected elements id, this is used for color changing
	$.each(selectedObjects, function(i){
		if(i == 0)
			selectedObjectsForColor = "#"+this;
		else
			selectedObjectsForColor = selectedObjectsForColor + ", #"+ this
	});

	if(selectedObjects.length == 0) selectedObjectsForColor = "#null"; //we need this when nothing is selected
	//reset color picker each time
	if(EditState == "all")
		runColorPicker(".changeTextColor", selectedObjectsForColorAll, "color, border-color", "toolsChangeTextColor", "Change text color");
	else
		runColorPicker(".changeTextColor", selectedObjectsForColor, "color, border-color", "toolsChangeTextColor", "Change text color");

}

//by default change text color for all text objects
runColorPicker(".changeTextColor", selectedObjectsForColorAll, "color, border-color", "toolsChangeTextColor", "Change text color");

function runColorPicker(bindTo, alterField, alterProperties, addClass, setTitle){
	$(bindTo).colorpicker({
		altField: alterField, 
		altProperties: alterProperties, 
		showOn: "button",
		buttonImage: "images/calendar.gif",
		buttonImageOnly: true,
		buttonText: setTitle,
		buttonClass: addClass +" toolsIcons displayInline"
	});
}

//by default edit all items
var EditState = "all";
//determine which elemts to edit
$("#changeEditState").click(function(){
	if(EditState == "all"){
		$(this).removeClass("stateAll").addClass("stateSelected");
		EditState = "selected";
		runColorPicker(".changeTextColor", selectedObjectsForColor, "color, border-color", "toolsChangeTextColor", "Change text color");
	}else{
		$(this).removeClass("stateSelected").addClass("stateAll");
		EditState = "all";
		runColorPicker(".changeTextColor", selectedObjectsForColorAll, "color, border-color", "toolsChangeTextColor", "Change text color");
	}
});

//apply new style to selectmenu
$('.toolsChangeTextFont').selectmenu({width:160});
$(".cardDimensions").selectmenu();
$(".linesStyle").selectmenu({width: 70, menuWidth: 100});

//change font family
$(".toolsChangeTextFont").change(function(){
	var value = $(this).val();
	
	//edit all items
	if(EditState == "all")
		objectForEdit = $(".cardElementDraggable");
	else
		objectForEdit = selectedObjects;
	
	$.each(objectForEdit, function(){
		if(EditState == "all")
			obj = this;
		else
			obj = "#" + this;
			
		$(obj).css("font-family", value);
	});
});
var timeout = $('#clicker');

$(document).mouseup(function(){
    clearInterval(timeout);
    return false;
});

//change text style
$(".textToolsSingle").mousedown(function(){
	var action = $(this).attr('action');
	
	switch(action){
		//change text weight
		case 'Bold':
			changeProperties("font-weight", "bold", "normal", 400);
		break;
		
		//change font style 
		case 'Italic':
			changeProperties("font-style", "italic", "normal");
		break;	
		
		//change font decoration
		case 'Underline':
			changeProperties("text-decoration", "underline", "none");
		break;
		
		//increase font size
		case 'IncreaseFontSize':
			timeout = setInterval(function(){
			
				//edit all items
				if(EditState == "all")
					objectForEdit = $(".cardElementDraggable");
				else
					objectForEdit = selectedObjects;
				
				$.each(objectForEdit, function(){
					if(EditState == "all")
						obj = this;
					else
						obj = "#" + this;
						
					//extract font-size from style tag
					styletag = $(obj).attr('style');
					stylestemp = styletag.split(';');
					styles = {};
					c = '';
					for (var x in stylestemp) {
						c = stylestemp[x].split(':');
						styles[$.trim(c[0])]=$.trim(c[1]);
					}
					
					newSize = parseInt(styles['font-size']) + 1;
					
					$(obj).css("font-size", newSize + "pt");
				});
			}, 70);
		break;
		
		//decrease font size
		case 'DecreaseFontSize':
			timeout = setInterval(function(){
				//edit all items
				if(EditState == "all")
					objectForEdit = $(".cardElementDraggable");
				else
					objectForEdit = selectedObjects;
				
				$.each(objectForEdit, function(){
					if(EditState == "all")
						obj = this;
					else
						obj = "#" + this;
						
					//extract font-size from style tag
					styletag = $(obj).attr('style');
					stylestemp = styletag.split(';');
					styles = {};
					c = '';
					for (var x in stylestemp) {
						c = stylestemp[x].split(':');
						styles[$.trim(c[0])]=$.trim(c[1]);
					}
					
					newSize = parseInt(styles['font-size']) - 1;
					
					$(obj).css("font-size", newSize + "pt");
				});
			}, 120);
		break;
		
		case 'left':
			$.each(selectedObjects, function(){
				$("#" + this).css("left", "2px");
			});
		break;
		
		case 'right':
			$.each(selectedObjects, function(){
				obj = $("#" + this);
				
				obj.css("left", (obj.parent().width() - obj.width()) - 10);
			});
		break;
		
		case 'middle':
			$.each(selectedObjects, function(){
				obj = $("#" + this);
				
				obj.css("left", ((obj.parent().width() / 2) - (obj.width() / 2) - 10 ));
			});
		break;

		default:
			showError("Requested action '"+action+"' was not found!");
		break;
	}

	
	//update css
	function changeProperties(a, b, c, d, execute){
		
		//edit all items
		if(EditState == "all")
			objectForEdit = $(".cardElementDraggable");
		else
			objectForEdit = selectedObjects;
		
		$.each(objectForEdit, function(){
			if(EditState == "all")
				obj = this;
			else
				obj = "#" + this;
			
			//don't do checking
			if(execute){
				$(obj).css(a, b);
				return;
			}
			
			if($(obj).css(a) == c || $(obj).css(a) == d){
				$(obj).css(a, b);
			}else{
				$(obj).css(a, c);
			}
		});
	}
});

masterIndex = 100;

function updateIndex(id){
	masterIndex++;
	$(id).css("z-index", masterIndex);
}

});
function showError(errorMessage){
	alert(errorMessage);
}


function getAllCardData(){
	//save current side data
	$(function(){
		save("side1");
	});
	//return both sides data
	return cardData;
}