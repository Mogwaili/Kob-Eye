[INFO [!Query!]|I]
[OBJ [!Int::module!]|[!Int::objectClass!]|O]
[!container:="containerID":"tabNav"!]
{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"verticalGap":0},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":"invoke","method":"groupState","params":{"group":"selection","property":"enabled","selection":1}}
	}
},
"components":[
	
	{"type":"HBox","percentWidth":100,"setStyle":{"gap":1,"paddingLeft":4,"paddingTop":4,"paddingBottom":4,"backgroundColor":"#d9d9d9"},
		"components":[
			{"type":"ImageButton","id":"edit:[!Int::objectClass!]","width":16,"height":16,"cornerRadius":8,"image":"pic_i","borderWidth":1,"stateGroup":"selection","enabled":0},
			{"type":"ImageButton","id":"new:[!Int::objectClass!]","width":16,"height":16,"cornerRadius":8,"image":"pic_plus","borderWidth":1},
			{"type":"ImageButton","id":"delete:[!Int::objectClass!]","width":16,"height":16,"cornerRadius":8,"image":"pic_moins","borderWidth":1,"stateGroup":"selection","enabled":0}
		]
	},
	{"type":"HBox","minHeight":1,"percentWidth":100,"percentHeight":100,
		"components":[
			{"type":"List",
				"setStyle":{"horizontalGap":2},"percentWidth":100,"percentHeight":100,
				"kobeyeClass":{
					"dirtyParent":1,
					"module":"[!Int::module!]",
					"objectClass":"[!Int::objectClass!]",
					"select":["Id","Date","Commentaires","Nom","Prenom"],
					"columns":[
						{"type":"horizontal","setStyle":{"paddingTop":10,"paddingBottom":10,"paddingRight":10,"paddingLeft":10},"components":[
							{"type":"background","color":"0xffffff","dropShadow":1,"components":[
								{"type":"vertical","setStyle":{"paddingTop":10,"paddingBottom":10,"paddingRight":10,"paddingLeft":10,"gap":10},"components":[
									{"type":"horizontal","setStyle":{"gap":10},"components":[
										{"type":"varchar","field":"Prenom","setStyle":{"fontWeight":"bold"}},
										{"type":"varchar","field":"Nom","setStyle":{"fontWeight":"bold"}},
										{"type":"date","field":"Date","setStyle":{"fontWeight":"bold"}}
									]},
									{"type":"text","field":"Commentaires"}
								]}
							]}
						]}
					],
					[STORPROC [!O::getElementsByAttribute(iconField,1)!]|Ic]
						[STORPROC [!Ic::elements!]|Id]
							"iconField":"[!Id::name!]"
						[/STORPROC]
					[/STORPROC]
					"icon":"[!O2::getIcon!]"
					,"form":"FormDetail.json"
				}
				,"events":[
					{"type":"start","action":"loadValues","params":{"needsParentId":1}},
					{"type":"dblclick","action":"invoke","method":"loadFormWithSelection","params":{}},
					{"type":"proxy", "triggers":[
						{"trigger":"new:[!Int::objectClass!]","action":"invoke","method":"createForm"},
						{"trigger":"edit:[!Int::objectClass!]","action":"invoke","method":"loadFormWithSelection","params":{}},
						{"trigger":"delete:[!Int::objectClass!]","action":"invoke","method":"deleteWithID"}
					]}
				]
			}
		]
	}
]}