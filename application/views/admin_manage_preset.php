<div class="manageImgBlockTitle coolText">Create new template</div>

<span class="presetsRequiredField">Fields marked with <span class="presetStar">*</span> is required.</span><br /><br />
<label class="presetFields displayInline">* Template Name:</label><input class="textInput presetName" type="text"  style="margin-top:0px;width:229px;"/><br />
<label class="presetFields displayInline">Template Title</label><input class="textInput presetTitle" type="text"   style="margin-top:0px;width:229px;"/><br />
<label class="presetFields displayInline" style="padding-top: 13px;">Template Description:</label><textarea class="textInput presetDesc" /></textarea><div class="displayInline" id="descCounter"></div><br />

<button class="savePreset displayInline">Save</button>
<div class="adminCpMessage displayInline" id="presetMessage1"></div>

<div class="presetsRequiredField presetCreatePreviewTitle">Card preview:</div>
<div class="managePresetPreviewImgContainerCreate">
	<img class="managePresetPreviewImgCreate" src="<?=$preview_path?>" title="Preview of the front side."/>
</div>

<div class="manageImgBlockTitle coolText">Delete template</div>

<span class="presetsRequiredField">Please select template:</span><br />
<select class="managePresetList">
	<?=$list?>
</select><br /><br />

<span class="presetsRequiredField">Please select action:</span><br />
<select class="managePresetListAction">
	<option value="delete">Delete</option>
</select><br />

<button class="editPreset displayInline">Ok</button>
<div class="adminCpMessage displayInline" id="presetMessage2" style="margin-top:28px;"></div>

<div class="presetsRequiredField presetCreatePreviewTitle" style="top:200px;">Card preview:</div>
<div class="managePresetPreviewContainer">
	<img class="managePresetPreviewImg" src="media/images/designs/preview/thumb/default.jpg" title="Preview of the front side."/>
</div>