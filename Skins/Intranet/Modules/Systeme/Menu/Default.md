[STORPROC [!Systeme::Menus!]/MenuPrincipal=1&Affiche=1|M]
	<table id="MenuTab">
		<tr>
			[LIMIT 0|100]
				<td class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] Current [/IF] [IF [!Pos!]=1] First [/IF] [IF [!Pos!]=[!NbResult!]] Last [/IF]">
					[IF [!M::Url!]~http]
						[IF [!M::Url!]=http://mail.unibio.fr]
							[IF [!CasUser!]]
								[STORPROC ProxyCas/Host/[!CasUser::Id!]|Cu|0|1]
								<a href="[!Cu::getZimbraUrl()!]" target="_blank" >[!M::Titre!]</a>
								[/STORPROC]
							[ELSE]
								<a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>
							[/IF]
						[ELSE]
							<a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>
						[/IF]
					[ELSE]
						<a href="/[!M::Url!]">[!M::Titre!]</a>
						[IF [!M::Alias!]~Redaction]
							[STORPROC [!M::Alias!]/Categorie/Publier=1|SCat]
								<ul>
									[LIMIT 0|100]
										<li>
											<a href="/[!M::Url!]/[!SCat::Url!]">[!SCat::Nom!]</a>
										</li>
									[/LIMIT]
								</ul>
							[/STORPROC]
						[/IF]
					[/IF]
				</td>
			[/LIMIT]
		</tr>
	</table>
[/STORPROC]