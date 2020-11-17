<div class="manageImgBlock">
	<div class="manageImgBlockTitle coolText">Edit Categorys</div>
	
	<div class="displayInline">
		<div class="displayInline manageImgTitle" style="margin-left:4px;" >Select section you wish to edit</div><br />
		<select class="manageImgSelect deleteCategorySection">
			<option value="bg">Card backgrounds</option>
			<option value="aditional">Aditional images</option>
		</select>
	</div>
	
	<div class="displayInline categorysListContainer" style="margin-left:50px;">		
		<div class="displayInline manageImgTitle" style="margin-left:4px;" >Delete existing category</div><br />
		<select class="selectCategory deleteCategoryList">
			<?=$categorys_list?>
		</select>
		
		<div class="displayInline manageImgDeleteCategory adminIcon" title="Delete category along with it's contents" ></div>
	</div>
	
	<div class="displayInline" style="margin-left:41px;">
		<div class="displayInline manageImgTitle">Create new category:</div><br />
		<input type="text" name="createcategory" class="textInput createCategoryInput" style="width:125px; margin-top:0px;"/>
		<div class="displayInline manageImgCreate adminIcon" title="Create new category"></div>
	</div>
	
</div>

<div class="manageImgBlock" style="border-top: 1px solid #F0EEEE;padding-top: 7px;">
	<div class="manageImgBlockTitle coolText" style="margin-bottom:16px;">Upload Images</div>
	
	<div class="manageUploadBlock displayInline" style="margin-left:0px;">
		<form method="post" enctype="multipart/form-data" target="upload_image_iframe" action="admin/upload_category_images" class="uploadForm">
			<div class="displayInline manageImgTitle" style="margin-left:0px;" >Choose name of the image.</div><br />
			<input type="text" name="name" class="textInput uploadImageName" style="margin-left:0px; margin-top:0px;width:190px;" value="Image" /><br /><br />
			
			<div class="displayInline manageImgTitle" style="margin-left:0px;">Choose the image you you wish to upload</div><br />
			<input type="file" name="fileName" />
			<input type="hidden" name="section" class="uploadSection" value="bg" />
			<input type="hidden" name="dir" class="uploadDir" value="random" />
			<input type="submit" class="uploadSubmit" value="Upload"/><br /><br />
			
			<div class="saveWorkMessageBox displayInline" style="margin-top:4px;"></div>	
		</form>
	</div>
	
	<div class="displayInline uploadImageCatgeroys" style="margin-left:-98px;">
		<div class="displayInline manageImgTitle" style="margin-left:4px;">Select where do you wish to upload selected image</div><br />
		
		<select class="selectCategory uploadCategoryList">
			<?=$categorys_list?>
		</select>
	</div>
	
	<iframe src="admin/upload_category_images" name="upload_image_iframe" class="uploadImage" style="display:none;"></iframe>
</div>
