<section id="top-breadcrumb">
	<div class="container">
		<div class="row">
			<!-- Breadcrumb -->
			<div id="breadcrumb">
				<ul class="breadcrumb">
					[IF [!Lien!]!=]
						<li><a class="Last" href="/" title="Revenir à l'accueil">Accueil</a></li>
						[!LastMenu:=0!]
						[INFO Systeme/Menu/[!Lien!]|I]
						[STORPROC [!I::Historique!]|This]
							[!Men+=/[!This::Value!]!]
							[IF [!Systeme::CurrentMenu::Url!]=[!This::Value!]]
								// Si le menu en cours est égal à la valeur en cours, alors c est le dernier menu
								[!LastMenu:=1!]
							[/IF]
							[IF [!LastMenu!]]
								// Si c est le dernier menu 
								[IF [!Alias!]=]
									// ...et que l Alias est deja cree
									// On va cherche l alias pour completer la requete
									[!M:=[!Systeme::searchMenu([!This::Value!])!]!]
									[!Alias:=[!M::Alias!]!]
									<li><a [IF [!Pos!]=[!NbResult!]] class="Last" [/IF] href="[!Men!]" title="[!M::Titre!]">[!M::Titre!]</a></li>
								[ELSE]
									// Sinon, on le cree et on l incremente de la valeur en cours
									[!Alias+=/[!This::Value!]!]
									[INFO [!Alias!]|Al]
									[IF [!Al::ObjectType!]=[!Al::TypeChild!]]
										[IF [!Al::TypeSearch!]!=Child]
											[!PP:=[!Pos!]!]
											[!NR:=[!NbResult!]!]
											[STORPROC [!Alias!]|A|0|1]
																		
											    <li>
                                                                                                [IF [!PP!]!=[!NR!]]<a [IF [!PP!]=[!NR!]] class="Last" [/IF] href="[!Men!]" title="[!A::getFirstSearchOrder!]">[!A::getFirstSearchOrder!]</a>[ELSE][!A::getFirstSearchOrder!][/IF]</li>

											[/STORPROC]
											[!Pass:=0!]
										[ELSE]
											[!Pass:=1!]
										[/IF]
									[ELSE]
										<li><a [IF [!Pos!]=[!NbResult!]] class="Last" [/IF] href="[!Men!]" title="[!Al::TypeChild!]">[!Al::TypeChild!]</a></li>
									[/IF]
								[/IF]
							[ELSE]
								// S il s agit d un menu, on fait une requete pour connaitre son nom et on l affiche
								[STORPROC Systeme/Menu/[!This::Value!]|M|0|1|Ordre|ASC][/STORPROC]
								<li><a [IF [!Pos!]=[!NbResult!]] class="Last" [/IF] href="[!Men!]" title="[!M::Titre!]">[!M::Titre!]</a></li>
							[/IF]
							[IF [!Pos!]!=[!NbResult!]&&[!Pass!]!=1]
								// Si la position n est pas la dernière, on affiche le séparateur
								[!Sep!]
							[/IF]
						[/STORPROC]
					[ELSE]
						<li><a class="Last" href="/" title="Revenir à l'accueil">Accueil</a></li>
					[/IF]
				</ul>
			</div>
			<!-- /Breadcrumb -->
		</div>
	</div>
</section>
