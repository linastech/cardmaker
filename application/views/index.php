<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Cards</title>
	<base href="<?php echo URL::base(TRUE) ?>" target="_self">
	<meta name="verify-webtopay" content="fddb0a1dcf59231075c6e6d75a1302bd">
	<link rel="icon" type="image/ico" href="media/media/images/images/favicon2.ico" />
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<?php if(isset($styles)){  foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type)), "\n"; }?>
	<link href="media/css/main.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="media/css/colorpicker.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="media/css/jquery-ui/jquery-ui.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="media/css/colorbox.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="media/css/gallery.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="media/css/edit.css" media="screen" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="media/js/plugins/jquery.js"></script>
	<script type="text/javascript" src="media/js/plugins/jquery-ui.js"></script>
	<script type="text/javascript" src="media/js/plugins/colorpicker.js"></script>
	<script type="text/javascript" src="media/js/plugins/colorbox.js"></script>
	<script type="text/javascript" src="media/js/plugins/galery.js"></script>
	<script type="text/javascript" src="media/js/main.js"></script>
	<script type="text/javascript" src="media/js/plugins/edit.js"></script> 

	<?php if(isset($scripts)){ foreach ($scripts as $file) echo HTML::script($file), "\n"; }?>
</head>

<body>
<div id="pageContainer">
	<div id="leftContainerBlock" class="displayInline">
		<div id="h_roler">
			<div class="h_roler_draggable" action="horizontal"></div>
		</div>
		<div id="v_roler" class="displayInline">
			<div class="v_roler_draggable" action="vertical"></div>
		</div>
		
		<div id="cardContainer" class="displayInline" style="background:#fff;">
			<div id="cardElementsWrapperBorder">
				<div id="cardElementsWrapper">
					<div id="companyName1" class="cardElementDraggable"></div>
					<div id="companyName2" class="cardElementDraggable"></div>
					<div id="typeOfActivity1" class="cardElementDraggable"></div>
					<div id="typeOfActivity2" class="cardElementDraggable"></div>
					<div id="typeOfActivity3" class="cardElementDraggable"></div>
					<div id="typeOfActivity4" class="cardElementDraggable"></div>
					<div id="typeOfActivity5" class="cardElementDraggable"></div>
					<div id="name" class="cardElementDraggable"></div>
					<div id="phone" class="cardElementDraggable"></div>
					<div id="phone2" class="cardElementDraggable"></div>
					<div id="fax" class="cardElementDraggable"></div>
					<div id="aditional" class="cardElementDraggable"></div>
					<div id="adress" class="cardElementDraggable"></div>
					<div id="adress2" class="cardElementDraggable"></div>
					<div id="email" class="cardElementDraggable"></div>
					<div id="wwww" class="cardElementDraggable"></div>
				</div>
			</div>
		</div>
	</div>
	
	<div id="rightContainerBlock" class="displayInline">
		<div id="toolsBar">
			<div class="toolsSelectText toolsTabSelected" action="toolsTabText">
				Text
			</div>			
			<div class="toolsBarBlock displayInline">
				<div id="changeEditState" class="stateAll" title="Chose which text elements you wish to edit"></div>
				<div class="toolsIcons  toolsISave displayInline" id="save" title="Save your card"></div>
				<div class="toolsIcons  toolsISavePdf displayInline" id="pdf" title="Save as pdf"></div>
				<div class="toolsIcons  toolsISaveImage displayInline" id="image" title="Save as image"></div>
			</div>
			
			<div class="blockSeperator displayInline"></div>
			
			<!--text tools-->
			<div id="toolsTabText" class="toolsSection" style="visibility:visible">
				<div class="toolsBarBlock displayInline">
					<select class="toolsChangeTextFont">
						<option value="Arial, Helvetica, sans-serif">Arial</option>
						<option value="Comic Sans MS, Comic Sans MS5, cursive">Comic Sans</option>
						<option value="Courier New, Courier New, monospace">Courier</option>
						<option value="Impact, Impact5, Charcoal6, sans-serif">Impact</option>
						<option value="Times New Roman, Times New Roman, Times, serif">Times New Roman</option>
						<option value="Verdana, Verdana, Geneva, sans-serif">Verdana</option>
					</select>
					<div style="margin-top:6px;">
						<div class="toolsIcons toolsHoverBg textToolsSingle toolsIconsTextBold " action="Bold" title="Make the selected text bold"></div>
						<div class="toolsIcons toolsHoverBg textToolsSingle toolsIconsTextItalic displayInline" action="Italic" title="Italicize the selected text"></div>
						<div class="toolsIcons toolsHoverBg textToolsSingle toolsIconsTextUnderline displayInline" action="Underline" title="Underline the selected text"></div>
						<div class="iconSeperator displayInline"></div>
						<input type="text" value="" class=" toolsHoverBg toolsIconsCardTextColor displayInline changeTextColor" />
						<div class="toolsIcons toolsHoverBg textToolsSingle toolsIconsTextIncreaseSize displayInline" action="IncreaseFontSize" title="Increase the font size"></div>
						<div class="toolsIcons toolsHoverBg textToolsSingle toolsIconsTextDecreaseSize displayInline" action="DecreaseFontSize" title="Decrease the font size"></div>
					</div>
				</div>
				
				<div class="blockSeperator displayInline"></div>
				
				<div class="toolsBarBlock displayInline" style="width:208px;">
					<div class="toolsIcons toolsIconsAlignSpacesVertical displayInline toolsHoverBg" action="verticalSpaces" title="Align text to the left" ></div>
					
					<div class="toolsIcons toolsIconsAlignLeft displayInline toolsHoverBg" action="verticalLine" title="Align text to the left" ></div>
					<div class="toolsIcons toolsIconsAlignCenter displayInline toolsHoverBg" action="verticalLine" title="Center text" ></div>
					<div class="toolsIcons toolsIconsAlignRight displayInline toolsHoverBg" action="verticalLine" title="Align text to the right" ></div>

					<div class="iconSeperator displayInline"></div>
					<div class="toolsIcons lines toolsIconsHLine displayInline toolsHoverBg" action="horizontalLine" title="Insert horizontal lines"></div>
					<div class="toolsIcons lines toolsIconsVLine displayInline toolsHoverBg" action="verticalLine" title="Insert vertical lines" ></div>
					<div class="toolsIcons lines toolsIconsDeleteLine displayInline toolsHoverBg" action="delete" title="Delete selected line" ></div>

					<div class="toolsIcons toolsIconsAlignSpacesHorizontal displayInline toolsHoverBg" action="verticalLine" title="Center text" ></div>
					<div class="toolsIcons toolsIconsAlignVerticalTop displayInline toolsHoverBg" action="verticalTop" title="Align selected text to the upper edge" ></div>
					<div class="toolsIcons toolsIconsAlignVerticalCenter displayInline toolsHoverBg" action="verticalMiddle" title="Align selected text to the center" ></div>
					<div class="toolsIcons toolsIconsAlignVerticalBottom displayInline toolsHoverBg" action="verticalBottom" title="Align selected text to the bottom" ></div>
					
					<div class="iconSeperator displayInline"></div>
					<div style="position:absolute; top:32px; left:322px;">
						<select class="linesStyle">
							<option value="1" bg="background-position: -384px -93px;"><div></div></option>
							<option value="2" bg="background-position: -384px -87px;"></option>
							<option value="3" bg="background-position: -384px -80px;"></option>
							<option value="4" bg="background-position: -384px -71px;"></option>
						</select>
					</div>
				</div>		
			</div>
			<!--Text tools ends-->

			<!--Image tools-->
			<div class="toolsSelectImage toolsSelectTab" action="toolsTabImage">
				Image
			</div>
			<div id="toolsTabImage" class="toolsSection" >

				<div class="toolsBarBlock displayInline" >
					<select class="cardDimensions">
						<optgroup label="Business Cards">
							<option value="0">90x50mm</option>
							<option value="1">85x55mm</option>
						</optgroup>
						<optgroup label="Postcards">
							<option value="2">101x152mm</option>
							<option value="3">127x177mm</option>
						</optgroup>
						</option>
					</select>
					<br />
					
					<div class="displayInline" style="margin-left:3px; margin-top:3px;">
						<div class="changeCardPosition radioSelected toolsIcons displayInline" action="switchHorizontal" ></div><span class="radio_text" >Horizontal</span><br />
						<div class="changeCardPosition radio toolsIcons displayInline" action="switchVertical"></div><span class="radio_text" >Vertical</span>
					</div>
				</div>
				
				<div class="blockSeperator displayInline"></div>
				
				<div class="toolsBarBlock displayInline" >
					<div class="toolsIcons  toolsIconsCardBgImages displayInline" action="bg-images" title="Change card background image"></div>
					<input type="text" value="" class="toolsIcons toolsIconsCardBgBucket displayInline " />
					<div class="toolsIcons grid toolsIconsAddGrid displayInline toolsHoverBg" action="grid" title="Insert grid to the card"></div>
					<div class="toolsIcons toolsIconsCardFloatingImage displayInline" action="floating-image" title="Insert image"></div>
					<div class="toolsIcons toolsIconsCardFloatingImageRotate displayInline floatinImageTool" action="rotate" title="Rotate selected image"></div>
					<div class="toolsIcons toolsIconsCardFloatingImageExpand displayInline floatinImageTool" action="expand" title="Expand selected image to the full size of the card"></div><br />
					<div class="toolsIcons toolsIconsCardBgDelete displayInline" action="image" title="Reset card background"></div>
					<div class="toolsIcons toolsIconsCardBgDelete displayInline" action="color" title="Reset card background"></div>


				</div>
			</div>
			<!--Image tools ends-->
			
			<!--Admin Tools-->
			<?php  if(isset($admin)) echo $admin; ?>
		</div>
		
		<div id="inputContainer">
			<div class="sideTab tabOne tabSelected" side="1">Side 1</div>
			<div class="sideTab tabTwo" side="2">Side 2</div>
			
			<div id="textSectionLeftBlock">
				<input name="companyName1" type="text"  class="textInput" value=""/>
				<input name="companyName2" type="text"  class="textInput" value=""/>
				<input name="typeOfActivity1" type="text" class="textInput"  value=""/>
				<input name="typeOfActivity2" type="text" class="textInput" value=""/>
				<input name="typeOfActivity3" type="text" class="textInput" value=""/>
				<input name="typeOfActivity4" type="text" class="textInput"  value=""/>
				<input name="typeOfActivity5" type="text" class="textInput"   value=""/>
				<input name="name" type="text"  class="textInput" />
			</div>
			
			<div id="textSectionRightBlock">
				<input name="phone" type="text" class="textInput" value=""/>
				<input name="phone2" type="text" class="textInput"  value=""/>
				<input name="fax" type="text" class="textInput"  value=""/>
				<input name="adress" type="text" class="textInput"  value=""/>
				<input name="adress2" type="text" class="textInput"  value=""/>
				<input name="email" type="text" class="textInput"  value=""/>
				<input name="wwww" type="text" class="textInput"  value=""/>
				<input name="aditional" type="text" class="textInput"  value=""/>
			</div>
		</div>
	</div>
	
	<div class="galery_minimized">
		<div class="galeryShow displayInline">Show</div>
		<div class="galeryShowIcon displayInline" title="Expand template gallery."></div>
	</div>
	
	<div id="gallery" class="ad-gallery">
		<div class="slideShowTitle coolText ">
			Select Card Template
		</div>
		
		<div class="slideShowHide"></div>
		<div class="slideShowMove"></div>

		<div class="slideShowNext"></div>
		<div class="slideShowPrevious"></div>
		<div class="ad-image-wrapper"></div>
		
		<div class="ad-control-bg">
			<div class="ad-controls displayInline"></div>
			<div class="ad-galery-set displayInline">
				Use this template
				<div class="ad-galery-set-icon displayInline"></div>
			</div>
			<div class="ad-nav">
				<div class="ad-thumbs">
					<ul class="ad-thumb-list">
						<?php if(isset($presetsList)) echo $presetsList?>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="footer">
		<div class="footer-block">
			<span class="pageLink contactUsButton">Contact Us</span><br />
			<span class="pageLink aboutUseButton">About Us</span><br />
			<a href="admin/"><span class="pageLink">Administration Login</span></a>
		</div>
		
		<div class="footer-copy coolText">
			Copyright (c) 2012 "Business Cards"
		</div>
	</div>
</div>
<div id="loadUserCard" style="display:none">
	<?php if(isset($load_user_card)) echo $load_user_card;?>
</div>
</body>
</html>