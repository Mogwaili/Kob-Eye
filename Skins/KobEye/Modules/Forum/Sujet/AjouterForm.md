[HEADER]
	<script type="text/javascript" src="/Skins/[!Systeme::Skin!]/Js/tinyMce/tiny_mce.js"></script>
	<script type="text/javascript">
		tinyMCE.init({
			theme : "advanced",
			mode : "textareas",
			plugins : "bbcode,fullscreen,table,inlinepopups",
			theme_advanced_buttons1 : "bold,italic,underline,undo,redo,link,table,unlink,image,forecolor,removeformat,cleanup,code,bullist,numlist,fullscreen",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_styles : "Code=codeStyle;Quote=quoteStyle",
			content_css : "/Skins/[!Systeme::Skin!]/Css/bbcode.css",
			entity_encoding : "raw",
			add_unload_trigger : false,
			remove_linebreaks : false,
			force_br_newlines : false,
			convert_newlines_to_brs : false,
			convert_urls : false
		});
	</script>
[/HEADER]
[IF [!Systeme::User::Public!]]
	<div class="Infos">Vous n'&ecirc;tes pas autoris&eacute; &agrave; ajouter un sujet</div>
[ELSE]
	<form  action="#" method="post" class="ForumF">
		<p>Ajout d'un sujet</p>
		<div class="LigneForm">
			<label for="titreSujet">Sujet : </label>
			<input type="text" class="Text" name="titreSujet" style="width:815px;" />
		</div>
		<div class="LigneForm">
			<label for="descSujet">Message : [!Prefixe!][!Prop::Nom!]</label>
			<textarea ROWS="15" class="Champ" name="messSujet">[!Prop::Valeur!]</textarea>
		</div>
		[IF [!Systeme::User::Admin!]
			<div class="LigneForm">
				<label for="descSujet">Bloquer ce sujet : </label>
				<input type="checkbox" name="noResponse" value="1" style="width:2%"/>
			</div>
		[/IF]
		[BLOC Bouton|width:140px;margin-left:775px;||width:105px;]
			<input type="hidden" name="parent" id="parent" value="[!parent!]" />
			<input type="hidden" name="referer" value="[!referer!]" />
			<input type="submit" name="confSujet" value="Ajouter"/>
		[/BLOC]
	</form>
[/IF]