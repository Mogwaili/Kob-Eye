[INFO [!Query!]|I]
[STORPROC [!Systeme::Modules!]|mo]
	[IF [!Key!]=[!I::Module!]]
		[!mm:=[!mo!]!]
	[/IF]
[/STORPROC]
[!db:=[!mm::Db!]!]
[!dc:=[!db::Dico()!]!]

[!forms:=!]
[!index:=0!]
{"form":{"type":"MDIWindow","id":"AP:[!Systeme::CurrentMenu::Url!]","title":"[!Systeme::CurrentMenu::Titre!]", 
"height":650,"width":1100,"popup":"free","localProxy":1,
"components":[
	{"type":"DividedBox", "direction":"horizontal", "percentHeight":100, "liveDragging":0,"resizeToContent":0,
	"components":[
		{"type":"Accordion", "id":"accordion", "width":220, "percentHeight":100,
		"components":[
			{"type":"VBox","label":"Parcourir","styleName":"AccordionStyle","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":5,"paddingBottom":5,"paddingRight":5,"paddingLeft":5},
			"components":[
				[STORPROC [!Systeme::CurrentMenu::Menus!]/Affiche=1|C|0|100|Ordre|ASC]
					[IF [!Pos!]>1],[!forms+=,!][/IF]
					{"type":"IconButton","label":"[!C::Titre!]","styleName":"buttonFirstLevel","percentWidth":100,"setStyle":{"paddingTop":15,"paddingBottom":15},"events":[
						{"type":"click", "action":"loadForm", "params":{"kobeyeClass":{"form":"[!Systeme::CurrentMenu::Url!]/[!C::Url!].json"}, "containerLabel":"[!S::Titre!]","containerID":"tabNav"}}
					]}
					[!forms+={"form":"[!Systeme::CurrentMenu::Url!]/[!C::Url!].json", "containerLabel":"[!C::Titre!]"}!]
					[STORPROC [!C::Menus!]/Affiche=1|S|0|100|Ordre|ASC]
						[IF [!Pos!]>1],[/IF]
						{"type":"IconButton","label":"[!S::Titre!]","styleName":"buttonFirstLevel","percentWidth":100,"setStyle":{"paddingTop":15,"paddingBottom":15},"events":[
							{"type":"click", "action":"loadForm", "params":{"kobeyeClass":{"form":"[!Systeme::CurrentMenu::Url!]/[!C::Url!]/[!S::Url!].json"}, "containerLabel":"[!S::Titre!]","containerID":"tabNav"}}
						]}
						[STORPROC [!S::Menus!]/Affiche=1|S2|0|100|Ordre|ASC]
							,{"type":"VBox", "label":"Parcourir","percentHeight":100,"percentWidth":100,"setStyle":{"paddingLeft":10}, "components":[
								[LIMIT 0|100]
									[IF [!Pos!]>1],[/IF]
									{"type":"IconButton","label":"[!S2::Titre!]","styleName":"buttonSecondLevel","percentWidth":100,"setStyle":{"paddingTop":15,"paddingBottom":15},"events":[
										{"type":"click", "action":"loadForm", "params":{"kobeyeClass":{"form":"[!Systeme::CurrentMenu::Url!]/[!C::Url!]/[!S::Url!]/[!S2::Url!].json"}, "containerLabel":"[!S2::Titre!]","containerID":"tabNav"}}
									]}
								[/LIMIT]
							]}
						[/STORPROC]
					[/STORPROC]
				[/STORPROC]
			]}
		],
		"events":[
			{"type":"indexChanged", "action":[
				{"action":"invoke", "objectID":"firstTab", "method":"selectIndex", "params":{}},
				{"action":"invoke", "objectID":"tabNav", "method":"selectIndex", "params":{"index":0}}
			]}
		]},
		{"type":"TabNavigator", "id":"tabNav", "percentWidth":100, "percentHeight":100, "closePolicy":"close_rollover", "minTabWidth":"150",
		"setStyle":{"paddingTop":1},
		"components":[
			{"type":"ViewStack", "id":"firstTab", "percentWidth":100, "percentHeight":100,"styleName":"ViewStackStyle", "bindLabel":1,
			"forms":[
				[!forms!]
			]}
		]}
	]}
],
"actions":[
//	{"type":"close", "action":"confirm"},
]
}}
