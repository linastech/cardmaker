<div class="cardPreviewTitle coolText">Card Preview</div>

<div class="cardsContainer">
	<div class="cardPreviewContainerLeft displayInline" ><?=isset($card1) ? $card1 : ""?></div>
	<div class="cardPreviewContainerRight displayInline" ><?=isset($card2) ? $card2 : ""?></div>
</div>

<div class="cardPreviewTitle coolText" style="margin-bottom:0px; height:20px;">Save Card</div>
<div class="cardPreviewSave">

<form action="home/download_document" target="cardSaveIframe" method="post" class="cardSaveForm">
	<input type="hidden" value="" name="data" class="dataInputHidden" />
	<input type="hidden" value="" name="action" class="actionInputHidden" />
</form>
<iframe src="home/download_document/image" class="cardSaveIframe" name="cardSaveIframe" style="display:none;" ></iframe>

<div class="displayInline cardSaveImagePayContainer">
	
	<div class="cardSaveImagePrice">1 &pound;</div>

	<div class="cardSaveImagePaySteps">
		<div class="displayInline cardSaveImagePayStep">1.</div>
		<div class="displayInline cardSaveImagePayStepText paymentCode" id="<?=$code?>">Text <?=$code?> to 1398</div>
	</div>	
	
	<div class="cardSaveImagePaySteps">
		<div class="displayInline cardSaveImagePayStep">2.</div>
		<div class="displayInline cardSaveImagePayStepText">Wait until we confirm payment.</div>
	</div>	
	
	<div class="cardSaveImagePaySteps">
		<div class="displayInline cardSaveImagePayStep">3.</div>
		<div class="displayInline cardSaveImagePayStepText">Save your card.</div>
	</div>

</div>

<div class="displayInline cardSaveImageContainer" >
	Please select in which format you wish to save the image:<br /><br />

	<div style="margin-bottom:5px;"><input <?=isset($imageChecked) ? $imageChecked : ""?> type="radio" name="image" id="saveCardTypeJPG" /><div class="displayInline savAsImage toolsIcons" title="Save as image"></div> &nbsp; .jpg</div>
	<div style="margin-bottom:5px;"><input <?=isset($pdfChecked) ? $pdfChecked : ""?> type="radio" name="image" id="saveCardTypePDF"/><div class="displayInline savAsPdf toolsIcons" title="Save as PDF"></div> .pdf</div>

	<div class="cardPreviewDLContainer">
		<div class="cardPreviewDlButton" title="Download card"></div>
		<div class="cardPreviewDlButtonDisable"></div>
	</div>
	
	<div class="cardPreviewDlProgressText">Waiting for payment...</div>
	<div class="progressBar"></div>
</div>

</div>