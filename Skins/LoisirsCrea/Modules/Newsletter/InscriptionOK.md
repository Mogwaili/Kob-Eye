[IF [!EmailNewsletter!]!=]

	[IF [!Desabo!]=1]
		[STORPROC Newsletter/Contact/Email=[!EmailNewsletter!]|Con|0|1]
			[METHOD Con|DelParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
			[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/3[/PARAM][/METHOD]
			[METHOD Con|Save][/METHOD]
			[!LeMessage:=Vous êtes désabonné!]
		[/STORPROC]
	[ELSE]
		[COUNT Newsletter/Contact/Email=[!EmailNewsletter!]|C]
		[IF [!C!]=0]
			[OBJ Newsletter|Contact|Con]
			[METHOD Con|Set]
				[PARAM]Email[/PARAM][PARAM][!EmailNewsletter!][/PARAM]
			[/METHOD]
			[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
			[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
			[METHOD Con|Save][/METHOD]
			[!LeMessage:=Votre inscription a bien été prise en compte!]
		[ELSE]
			[COUNT Newsletter/GroupeEnvoi/3/Contact/Email=[!EmailNewsletter!]|BL]
			[IF [!BL!]]
				[STORPROC Newsletter/Contact/Email=[!EmailNewsletter!]|Con|0|1]
					[METHOD Con|DelParent][PARAM]Newsletter/GroupeEnvoi/3[/PARAM][/METHOD]
					[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
					[METHOD Con|Save][/METHOD]
				[/STORPROC]
				[!LeMessage:=Vous êtes réinscrit.!]
			[ELSE]
				[!LeMessage:=Vous êtes déjà inscrit!]
			[/IF]
		[/IF]

	[/IF]
	<h2>[!LeMessage!]</h2>
[ELSE]
	<h2>Veuillez saisir votre adresse mail.</h2>
[/IF]
