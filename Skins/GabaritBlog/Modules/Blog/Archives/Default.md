<div id="Archives">
	<h1>Archives</h1>
	[STORPROC Blog/Post|P|0|1|tmsCreate|ASC][/STORPROC]
	[!Old:=[!Utils::getDate(Y,[!P::tmsCreate!])!]!]
	[!New:=[!Utils::getDate(Y,[!TMS::Now!])!]!]
	[!OldMonth:=[!Utils::getDate(n,[!P::tmsCreate!])!]!]
	[!NewMonth:=[!Utils::getDate(n,[!TMS::Now!])!]!]
	[!New-=[!Old!]!]
	[STORPROC [!New:+1!]|Ann|0|[!Old!]]
		[!NbMois:=12!]
		[!MoisDep:=0!]
		//si ann = 0, on affiche l annee du + vieux post.
		[IF [!New:-[!Ann!]!]=0]
			[!MoisDep:=[!OldMonth:-1!]!]
		[/IF]
		[IF [!Ann!]=0]
			[!NbMois:=[!NewMonth:-1!]!]
		[/IF]
		<div id="ListArchives">
			<h2>[!Old:+[!New:-[!Ann!]!]!]</h2>
			<ul>
				[STORPROC 12|Mois|[!MoisDep!]|[!NbMois!]]
					[!Depart:=[!Utils::getTms(1,[!Mois:+1!],[!Old:+[!New:-[!Ann!]!]!])!]!]
					[!Fin:=[!Utils::getTms(1,[!Mois:+2!],[!Old:+[!New:-[!Ann!]!]!])!]!]
					[!Requete:=Blog/Post/tmsCreate>[!Depart!]&tmsCreate<[!Fin!]&Actif=1&Brouillon=0!]
					[COUNT [!Requete!]|NbPost]
					[IF [!NbPost!]]
						<li>
							<a href="/Blog/Archives/Post?Mois=[!Mois:+1!]&amp;Annee=[!Old:+[!New:-[!Ann!]!]!]" title="Posts du mois de [UTIL MONTH][!Mois:+1!][/UTIL] [!Old:+[!New:-[!Ann!]!]!]">[UTIL MONTH][!Mois:+1!][/UTIL]([!NbPost!])</a>
							<ul>
								[STORPROC [!Requete!]|Po|0|5|tmsCreate|DESC]
									<li><a href="/Blog/Post/[!Po::Link!]" title="[!Po::Titre!], post du [DATE d.m.Y][!Po::tmsCreate!][/DATE]">
									<span class="Bold">[DATE d][!Po::tmsCreate!][/DATE] . </span>[SUBSTR 25|[...]][!Po::Titre!][/SUBSTR]</a>
									</li>
								[/STORPROC]
								<li>
									<a href="/Blog/Archives/Post?Mois=[!Mois:+1!]&amp;Annee=[!Old:+[!New:-[!Ann!]!]!]" title="Posts du mois de [UTIL MONTH][!Mois:+1!][/UTIL] [!Old:+[!New:-[!Ann!]!]!]">Tous les posts ...</a>
								</li>
							</ul>
						</li>
					[/IF]
				[/STORPROC]
			</ul>
		</div>
	[/STORPROC]
</div>
