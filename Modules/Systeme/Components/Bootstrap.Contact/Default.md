[IF [!CONTACTMAIL!]=]
	[!CONTACTMAIL:=[!CONF::MODULE::SYSTEME::CONTACT!]!]
[/IF]
[IF [!SendContact!]!=]

	//Verification des informations du formulaire
	[!C_Error:=0!]
	[IF [!C_Nom!]][ELSE][!C_Nom_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Mail!]][ELSE][!C_Mail_Error:=1!][!C_Error:=1!][/IF]
	[IF [!SUJET_ACTIF!]]
		[IF [!C_Objet!]][ELSE][!C_Objet_Error:=1!][!C_Error:=1!][/IF]
	[/IF]
	[IF [!MESSAGE_ACTIF!]]
		[IF [!C_Mess!]][ELSE][!C_Mess_Error:=1!][!C_Error:=1!][/IF]
	[/IF]
	[IF [!ADRESSE_ACTIF!]]
		[IF [!C_Code!]][ELSE][!C_Code_Error:=1!][!C_Error:=1!][/IF]
		[IF [!C_Ville!]][ELSE][!C_Ville_Error:=1!][!C_Error:=1!][/IF]
	[/IF]
	[IF [!MESSAGE_ACTIF!]]
		[IF [!C_Mess!]][ELSE][!C_Mess_Error:=1!][!C_Error:=1!][/IF]
	[/IF]
	[IF [!CAPTCHA_ACTIF!]]
		[IF [!n3:+[!n4!]!]!=[!tot2!]][!C_Calc_Error:=1!][!C_Error:=1!][/IF]
	[/IF]


	[IF [!C_Error!]]
		// Si il y a des erreurs, on les affiche
	    <div class="alert alert-error">
			<strong>Veuillez remplir les champs obligatoires suivants :</strong>
	    	<ul>
				[IF [!C_Nom_Error!]]<li>Merci de renseigner votre Nom</li>[/IF]
				[IF [!C_Mail_Error!]]<li>Merci de renseigner votre adresse email</li>[/IF]
				[IF [!C_Tel_Error!]]<li>Merci de renseigner votre n° de téléphone</li>[/IF]
				[IF [!C_Objet_Error!]]<li>Merci de renseigner le sujet de votre demande</li>[/IF]
				[IF [!C_Mess_Error!]]<li>Merci de laisser votre message</li>[/IF]
				[IF [!C_Calc_Error!]=1]<p>Calcul de vérification erroné</p>[/IF]
				[IF [!C_Code_Error!]]<li>Merci de renseigner votre code postal</li>[/IF]
				[IF [!C_Ville_Error!]]<li>Merci de renseigner votre Ville</li>[/IF]
			</ul>
		</div>
	[ELSE]
		// Sinon envoi du mail
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - [!C_Objet!][/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|ReplyTo][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|To][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
		[IF [!CONTACTMAILCC!]!=][METHOD LeMail|Cc][PARAM][!CONTACTMAILCC!][/PARAM][/METHOD][/IF]
		[IF [!CONTACTMAILBCC!]!=][METHOD LeMail|Bcc][PARAM][!CONTACTMAILBCC!][/PARAM][/METHOD][/IF]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					<font face="arial" color="#000000" size="2">
					<strong>Adresse Ip</strong> : <span><a href="http://geotool.flagfox.net/?ip=[!SERVER::REMOTE_ADDR!]">[!SERVER::REMOTE_ADDR!]</a></span><br/><br />
					[IF [!SUJET_ACTIF!]]
					<strong>Objet de la demande</strong> : [!C_Objet!]<br/>
					[/IF]
					<strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!C_Nom!]</span> [!C_Prenom!]<br/>
					[IF [!TEL_ACTIF!]]
					<strong>Numéro de téléphone</strong> : [!C_Tel!]<br/>
					[/IF]
					<strong>Adresse e-mail</strong> : [!C_Mail!]<br/>
					[IF MESSAGE_ACTIF]
					<strong>Message</strong> : [UTIL BBCODE][!C_Mess!][/UTIL]<br /></font>
					[/IF]
					<strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!C_Nom!]</span> [!C_Prenom!]<br/>

					[IF [!ADRESSE_ACTIF!]]
						<strong>Adresse</strong> : [!C_Adresse!]<br/>
						<strong>Code postal</strong> : [!C_Code!]<br/>
						<strong>Ville</strong> : [!C_Ville!]<br/>
					[/IF]
					<strong>Adresse e-mail</strong> : [!C_Mail!]<br/>

				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]

		// Enregistrement Contact + Message
		[STORPROC [!CONF::MODULE!]|Mod]
			[IF [!Key!]=NEWSLETTER]
				// 1 - on vérifie que le groupe existe, s'il n'existe pas on le créé
				[STORPROC Newsletter/GroupeEnvoi/Titre=Contacts par le site|GR|0|1]
					[NORESULT]
						[OBJ Newsletter|GroupeEnvoi|GR]
						[METHOD GR|Set]
							[PARAM]Titre[/PARAM]
							[PARAM]Contacts par le site[/PARAM]
						[/METHOD]
						[METHOD GR|Save][/METHOD]
					[/NORESULT]
				[/STORPROC]

				// 2 - on vérifie que le contact existe, s'il n'existe pas on le créé
				[STORPROC Newsletter/Contact/Email=[!C_Mail!]|Con|0|1]
					[NORESULT]
						[OBJ Newsletter|Contact|Con]
						[METHOD Con|Set]
							[PARAM]Email[/PARAM]
							[PARAM][!C_Mail!][/PARAM]
						[/METHOD]
						[METHOD Con|Set]
							[PARAM]Nom[/PARAM]
							[PARAM][!C_Nom!][/PARAM]
						[/METHOD]
						[METHOD Con|Set]
							[PARAM]Prenom[/PARAM]
							[PARAM][!C_Prenom!][/PARAM]
						[/METHOD]
						[METHOD Con|Set]
							[PARAM]Telephone[/PARAM]
							[PARAM][!C_Tel!][/PARAM]
						[/METHOD]
						[METHOD Con|AddParent]
							[PARAM]Newsletter/GroupeEnvoi/[!GR::Id!][/PARAM]
						[/METHOD]
						[METHOD Con|Save][/METHOD]
					[/NORESULT]
				[/STORPROC]

				// 3 - enregistrement du message
				[OBJ Newsletter|Reception|Rec]
				[METHOD Rec|Set]
					[PARAM]Contenu[/PARAM]
					[PARAM][!C_Mess!][/PARAM]
				[/METHOD]
				[METHOD Rec|Set]
					[PARAM]Sujet[/PARAM]
					[PARAM][!C_Objet!][/PARAM]
				[/METHOD]
				[METHOD Rec|AddParent]
					[PARAM]Newsletter/Contact/[!Con::Id!][/PARAM]
				[/METHOD]
				[METHOD Rec|Save][/METHOD]
			[/IF]
		[/STORPROC]

		// Mail de confirmation
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - Confirmation[/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
		[METHOD LeMail|ReplyTo][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
		[METHOD LeMail|To][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					Bonjour [!C_Prenom!] <span style="text-transform:uppercase">[!C_Nom!]</span>,<br />
					Nous avons bien reçu votre demande par email et vous remercions de votre confiance.<br />
					Nous traitons votre demande dans les plus brefs délais.
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]
		<div class="alert alert-success">Message envoy&eacute; avec succ&egrave;s.</div>
		<div class="alert alert-info">Un mail de confirmation vous a été adressé.</div>
		<div class="well darker">
	        <div class="row-fluid">
            <input type="hidden" name="SendContact" value="1">
	            <div class="span5 offset1">
				  <a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-block btn-info">Nouveau message</a>
				</div>
	            <div class="span5 ">
				  <a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-block btn-danger">Retour à l'accueil</a>
				</div>
			</div>
        </div>
	[/IF]
[/IF]

[IF [!SendContact!]=||[!C_Error!]]
	    <form id="FormContact" method="post" action="/[!Lien!]" class="form-horizontal">
			<div class="well">
				    <div class="row-fluid">
						<div class="span5">
							    <div class="control-group  [IF [!C_Nom_Error!]]error[/IF]">
									<label class="control-label" for="C_Nom">Nom <span class="Obligatoire">*</span></label>
									<div class="controls">
										    <input type="text" id="C_Nom" name="C_Nom" class="" style="text-transform:uppercase" value="[!C_Nom!]" required/>
									</div>
							    </div>
							    [IF [!PRENOM_ACTIF!]]
							    <div class="control-group  [IF [!C_Prenom_Error!]]error[/IF]">
									<label class="control-label" for="C_Prenom">Prénom</label>
									<div class="controls">
										    <input type="text" name="C_Prenom" class="" value="[!C_Prenom!]" />
									</div>
							    </div>
							    [/IF]
							    [IF [!TEL_ACTIF!]]
							    <div class="control-group [IF [!C_Tel_Error!]]error[/IF]">
									<label class="control-label" for="C_Tel">Numéro de téléphone</label>
									<div class="controls">
										    <input type="text" name="C_Tel" class=""  value="[!C_Tel!]"/>
									</div>
							    </div>
							    [/IF]
							    [IF [!ADRESSE_ACTIF!]]
							    <div class="control-group  [IF [!C_Adresse_Error!]]error[/IF]">
									<label class="control-label" for="C_Adresse">Adresse<span class="Obligatoire">*</span></label>
									<div class="controls">
										    <input type="text" name="C_Adresse" id="C_Adresse" value="[!C_Adresse!]" class="" required/>
									</div>
							    </div>
							    <div class="control-group  [IF [!C_Code_Error!]]error[/IF]">
									<label class="control-label" for="C_Code">Code postal <span class="Obligatoire">*</span></label>
									<div class="controls">
										<input type="text" name="C_Code" id="C_Code" value="[!C_Code!]"  class="" required/>
									</div>
							    </div>
							    <div class="control-group [IF [!C_Ville_Error!]]error[/IF]">
								    <label class="control-label" for="C_Ville">Ville<span class="Obligatoire">*</span></label>
								    <div class="controls">
									<input type="text" name="C_Ville" id="C_Ville" value="[!C_Ville!]" class="" required/>
								    </div>
							    </div>
							    [/IF]
						</div>

						<div class="span7">
							    <div class="control-group  [IF [!C_Mail_Error!]]error[/IF]">
									<label class="control-label" for="C_Mail">Adresse e-mail <span class="Obligatoire">*</span></label>
									<div class="controls">
										    <div class="input-prepend">
												<span class="add-on"><i class="icon-envelope"></i></span>
												<input type="text" id="C_Mail" name="C_Mail" value="[!C_Mail!]" class="input-large" required/>
										    </div>
									</div>
							    </div>
							    [IF [!SUJET_ACTIF!]]
							    <div class="control-group  [IF [!C_Objet_Error!]]error[/IF]">
									<label class="control-label" for="C_Objet">Sujet <span class="Obligatoire">*</span></label>
									<div class="controls">
										    <input type="text" name="C_Objet" value="[!C_Objet!]"  class="input-large" required/>
									</div>
							    </div>
							    [/IF]
							    [IF [!MESSAGE_ACTIF!]]
							    <div class="control-group [IF [!C_Mess_Error!]]error[/IF]">
									<label class="control-label" for="C_Mess">Message <span class="Obligatoire">*</span></label>
									<div class="controls">
										    <textarea cols="80" rows="6" name="C_Mess" class="input-xlarge" required>[!C_Mess!]</textarea>
									</div>
							    </div>
							    [/IF]
						</div>
				    </div>
			</div>
			[IF [!CAPTCHA_ACTIF!]]
			<div class="well">
				    <div class="row-fluid">
						<div class="span12">
							    <div class="control-group last [IF [!C_Calc_Error!]]error[/IF]">
									<label class="control-label span6" for="C_Nom">Merci de résoudre l'opération ci-dessous avant de valider <span class="Obligatoire">*</span></label>
									<div class="controls form-inline">
										    <input type="text" name="n3" id="n3" value="[!Utils::Random(9)!]" maxlength="2" readonly="readonly" class="span1"/>+
										    <input type="text" name="n4" value="[!Utils::Random(9)!]" maxlength="2" readonly="readonly" class="span1"/>
										    <span style="width:40px;text-align:center;">=</span>
										    <input type="text" name="tot2" value=""  maxlength="2" class="span1 [IF [!Calc2_Error!]]Error[/IF]" required/>
									</div>
							    </div>
						</div>
				    </div>
			</div>
			[/IF]
			<div class="well darker">
				    <div class="row-fluid">
						<input type="hidden" name="SendContact" value="1">
						<div class="span5 offset1">
							    <button type="submit" class="btn btn-block btn-success">Envoyer</button>
						</div>
						<div class="span5 ">
							    <a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-block btn-danger">Annuler</a>
						</div>
				    </div>	
			</div>
	    </form>
	    
	    <div class="well">
			<div class="row-fluid">
				    <div class="span6">
						<p>Les champs marqués (<span class="Obligatoire">*</span>) sont obligatoires.</p>
						<p class="ContactTel">Vous pouvez aussi nous contacter par :<br />
						Tel : [!CurrentMagasin::Tel!]<br />
						Fax : [!CurrentMagasin::Fax!]</p>
				    </div>
				    <div class="span6">
						<p>
						Conformément à la loi n°78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés,
						vous disposez d'un droit d'accès, de rectification, de suppression des informations qui vous concernent que vous pouvez exercer en vous adressant à
						[!CurrentMagasin::Nom!] - [!CurrentMagasin::Adresse!] - [!CurrentMagasin::CodPos!] [!CurrentMagasin::Ville!] - [!CurrentMagasin::Pays!].
						</p>
	    
						<p>
						[!TEXT_BAS!]
						</p>
				    </div>
			</div>
	    </div>

[/IF]
