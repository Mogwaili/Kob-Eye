[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]{"form":{"type":"TitleWindow","id":"ImgCat/[!I::TypeChild!]","title":"Génération du catalogue","minWidth":500,"width":500,"height":600,
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"localProxy":1,
"components":[
	{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100,
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"paddingTop":0,"paddingBottom":0,"verticalGap":0},
		"components":[
			{"type":"HBox","percentWidth":100,"setStyle":{"horizontalGap":0,"backgroundColor":"#eeeeee"},
			"components":[
				{"type":"VBox","percentWidth":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":6,"horizontalGap":0},
				"components":[
					{"type":"FormItem","labelWidth":50,"label":"Titre","components":[
						{"type":"TextInput","dataField":"Titre","width":217},
						{"type":"Label","setStyle":{"color":"red"},"width":330,"text":"Attention: Le nombre de caractères est limité à l'impression."},
						{"type":"Label","setStyle":{"color":"red","fontWeight":"bold"},"width":330,"text":"Cliquer sur 'Test' pour avoir un aperçu du catalogue."}
					]},
					{"type":"FormItem","labelWidth":50,"label":"Validité","components":[
						{"type":"TextInput","dataField":"Validite","width":330}
					]}
				]},
				{"type":"VBox","width":90,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":6,"horizontalGap":0},
				"components":[
					{"type":"Button","id":"save","label":"Démarrer","width":80,
					"events":[
						{"type":"click","action":[
							//{"action":"invoke","method":"uploadImage","objectID":"image"},
							{"action":"invoke","method":"setProperty","params":{"property":"enabled","value":0}},
							{"action":"invoke","method":"setProperty","objectID":"test","params":{"property":"enabled","value":0}}
						]}
					]},
					{"type":"Button","id":"test","label":"Test","width":80,
					"events":[
						{"type":"click","action":[
							//{"action":"invoke","method":"uploadImage","objectID":"image","params":{"test":1}},
							{"action":"invoke","method":"setProperty","params":{"property":"enabled","value":0}},
							{"action":"invoke","method":"setProperty","objectID":"save","params":{"property":"enabled","value":0}}
						]}
					]},
					//{"type":"Spacer","percentWidth":100},
					{"type":"Button","id":"cancel","label":"Annuler","width":80,
					"events":[
						{"type":"click","action":[
							{"action":"invoke","objectID":"image","method":"stop"},
							{"action":"invoke","objectID":"parentForm","method":"closeForm"}
						]}
					]}
				]}
			]},
			{"type":"CreateBitmap","id":"image","dataField":"ImageCatalogue","percentHeight":100,"percentWidth":100,
			"uploadURL":"/Flipbook/Page/Upload.htm",
			"events":[
				{"type":"cancel","action":[
					{"action":"invoke","objectID":"save","method":"setProperty","params":{"property":"enabled","value":1}},
					{"action":"invoke","objectID":"test","method":"setProperty","params":{"property":"enabled","value":1}}
				]},
				{"type":"proxy","triggers":[
					{"trigger":"save","action":"invoke","method":"callMethod","params":{"method":"query","function":"getImage","args":"dv:Titre,dv:Validite,v:0"}},
					{"trigger":"test","action":"invoke","method":"callMethod","params":{"method":"query","function":"getImage","args":"dv:Titre,dv:Validite,v:1"}}
				]}
			]}
		]}
	],
	"events":[
		{"type":"start","action":"loadValues","params":{"needsId":1}}
	]}
],
"popup":"modal"
}}
