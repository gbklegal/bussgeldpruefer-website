<?php

	$fileUrl = __DIR__.'/file/faq.json';

	if (isset($_POST['faqJsonContent'])) {
		$content = $_POST['faqJsonContent'];
		file_put_contents($fileUrl, $content);
	}

	$fileContent = file_get_contents($fileUrl);
	$fileArray = json_decode($fileContent, true);

?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
<head>

    <meta charset="utf-8">

    <link rel="stylesheet" href="jquery-ui.min.css">

    <title>FAQs für BGP App</title>

	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		#app {
			background-color: lightblue;
			padding: 30px;
		}
		#sortable {
			padding-bottom: 50px;
			display: grid;
			grid-template-columns: auto auto;
			grid-gap: 10px;
		}
		#sortable .item {
			padding: 10px;
			background-color: #fff;
			transition: box-shadow 0.15s;
			border-radius: 4px;
			display: inline-flex;
			align-items: center;
		}
		#sortable .item:not(input) {
			cursor: grab;
		}
		#sortable .item:active:not(input) {
			cursor: grabbing;
		}
		#sortable .item.ui-sortable-helper {
			box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
		}
		#sortable .item.ui-sortable-placeholder {
			visibility: visible !important;
			background-color: lightgrey;
		}
		#sortable .item .ui-icon {
			background-image: url(images/ui-icons_8d78a5_256x240.png);
		}
		#sortable .item label {
			font-weight: bold;
			padding-bottom: 5px;
			display: block;
		}
		#sortable .item textarea,
		#sortable .item input {
			padding: 4px;
			resize: vertical;
			width: 100%;
		}
		#sortable .item textarea {
			height: 150px;
		}
		#sortable .item .content {
			width: 100%;
			padding: 10px 10px 0 10px;
		}
		#sortable .item .content > div {
			padding-bottom: 10px;
		}

		#control {
			position: fixed;
			left: 0;
			right: 0;
			bottom: 0;
			padding: 20px 30px;
			background-color: lightblue;
		}

		#dialogRemove,
		#dialogSave {
			display: none;
		}

		span.ui-icon-alert {
			float: left;
			margin: 2px 12px 0 0;
		}

		#formSend {
			display: none;
		}
	</style>

</head>
<body>

	<div id="app">

		<div id="sortable" class="faq-items">
			<?php $qaCount = 0; ?>
			<?php foreach ($fileArray as $val): ?>
			<div class="item ui-widget" data-item="<?=$qaCount?>">
				<div class="ui-icon ui-icon-grip-dotted-vertical"></div>
				<div class="content">
					<div class="question">
						<label>Frage</label>
						<input type="text" value="<?=$val['question']?>" data-question="<?=$qaCount?>">
					</div>
					<div class="answer">
						<label>Antwort</label>
						<textarea data-answer="<?=$qaCount?>"><?=$val['answer']?></textarea>
					</div>
					<div class="control">
						<button class="ui-button" onclick="removeItem(<?=$qaCount?>)"><span class="ui-icon ui-icon-trash"></span> Löschen</button>
					</div>
				</div>
			</div>
			<?php $qaCount++; ?>
			<?php endforeach; ?>
		</div>

		<div id="control">
			<button class="ui-button" onclick="addNewItem()"><span class="ui-icon ui-icon-plus"></span> Hinzufügen</button>
			<button class="ui-button" onclick="saveFaq()">Speichern</button>
		</div>

		<div id="dialogRemove" title="Sind Sie sicher?">
			<p><span class="ui-icon ui-icon-alert"></span>Dieses Element löschen?</p>
		</div>

		<div id="dialogSave" title="Sind Sie sicher?">
			<p><span class="ui-icon ui-icon-alert"></span>FAQs speichern?</p>
		</div>

	</div>

	<form id="formSend" method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<input type="text" id="faqJsonContent" name="faqJsonContent">
	</form>

    <script src="external/jquery/jquery.js"></script>
    <script src="jquery-ui.min.js"></script>
    <script>
        jQuery(document).ready(function() {
            jQuery('#sortable').sortable().disableSelection();
        });

		function addNewItem() {
			let sortableElmt = document.querySelector('#sortable');
			sortableElmt.innerHTML+=`<div class="item ui-sortable-handle ui-widget">
				<div class="ui-icon ui-icon-grip-dotted-vertical"></div>
				<div class="content ui-widget">
					<div class="question">
						<label>Frage</label>
						<textarea class="q"></textarea>
					</div>
					<div class="answer">
						<label>Antwort</label>
						<textarea class="a"></textarea>
					</div>
					<div class="control">
						<button class="ui-button" onclick="removeItem(this)"><span class="ui-icon ui-icon-trash"></span> Löschen</button>
					</div>
				</div>
			</div>`;
		}

		function removeItem(itemId) {
			jQuery('#dialogRemove').dialog({
				resizeable: false,
				height: 'auto',
				width: 400,
				modal: true,
				buttons: {
					'Ja': function() {
						jQuery('[data-item='+itemId+']').remove();
						jQuery(this).dialog('close');
					},
					'Abbrechen': function() {
						jQuery(this).dialog('close');
					}
				}
			});
		}

		function saveFaq() {
			jQuery('#dialogSave').dialog({
				resizeable: false,
				height: 'auto',
				width: 400,
				modal: true,
				buttons: {
					'Ja': function() {
						let items = document.querySelectorAll('.faq-items .item'),
							faqs = new Array();

						items.forEach(item => {
							let qElmt = jQuery(item).find('[data-question]'),
								aElmt = jQuery(item).find('[data-answer]'),
								faqObj = {
									'question': qElmt.val(),
									'answer': aElmt.text()
								};
							faqs.push(faqObj);
						});
						console.log(faqs);
						faqs = JSON.stringify(faqs);
						// return;
						jQuery('#faqJsonContent').val(faqs);
						jQuery('#formSend').submit();
						jQuery(this).dialog('close');
					},
					'Abbrechen': function() {
						jQuery(this).dialog('close');
					}
				}
			});
		}
    </script>

</body>
</html>
