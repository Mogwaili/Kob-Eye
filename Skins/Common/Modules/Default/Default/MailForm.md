//[INFO [!Query!]|I]
//[OBJ [!I::Module!]|[!I::TypeChild!]|O]
//[!chldrn:=[!O::getChildTypes!]!]
//[!firstField:=!]
[IF [!MC!]]
	[!child:=Message!]
[ELSE]
	[!child:=MailUser!]
[/IF]
[IF [!POPUP!]][!form:=FormBasePopup!][ELSE][!form:=MailForm!][/IF]
{"form":{[IF [!POPUP!]]"type":"TitleWindow","id":"MailPopup?","title":"Message"[ELSE]"type":"GradientVBox","id":"MailForm?","label":"Message"[/IF],"percentHeight":100,
"setStyle":{"paddingTop":5,"paddingLeft":5,"paddingRight":5,"verticalGap":5},[IF [!POPUP!]][ELSE]"clipContent":0,[/IF]
"kobeyeClass":{"module":"Mail","objectClass":"[!child!]"},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"editable","property":"editable","noID":1}},
			{"action":"invoke","method":"groupState","params":{"group":"enabled","property":"enabled","hasID":1}},
			{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1,"hasID":1}},
			{"action":"invoke","method":"groupState","params":{"group":"disabled","property":"enabled","noID":1}}
		]}
	}
},
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"verticalScrollPolicy":"auto","minWidth":0,"minHeight":0,
	"components":[
		{"type":"MenuTab","maxLines":1,
		"menuItems":[
			{"children":[
				//{"label":"$__Send__$", "icon":"msgSend", "data":"saveClose","stateGroup":"disabled"},
				{"label":"$__Save__$", "icon":"save", "data":"saveClose","stateGroup":"updated"},
				//{"label":"$__Cancel__$", "icon":"cancel", "data":"cancel","stateGroup":"disabled"},
				//{"type":"vseparator"},
				[IF [!MC!]!=]
					{"label":"$__Delete__$", "icon":"iconDelete", "data":"delete","stateGroup":"enabled"},
				[/IF]
				//{"type":"vseparator"},
				{"label":"$__Reply__$", "icon":"msgAnswer", "data":"reply","stateGroup":"enabled"},
				{"label":"$__Reply all__$", "icon":"msgAnswerAll", "data":"replyAll","stateGroup":"enabled"},
				{"label":"$__Forward__$", "icon":"msgForward", "data":"forward","stateGroup":"enabled"},
				{"label":"$__Close__$", "icon":"close", "data":"close"}
			]}
	//	],
	//	"actions":[
	//		{"type":"proxy","triggers":[
	//			{"trigger":"proxy_kobeye_status","action":[
	//				{"action":"invoke","method":"groupState","params":{"group":"enabled","property":"enabled","hasID":1}},
	//				{"action":"invoke","method":"groupState","params":{"group":"disabled","property":"enabled","noID":1}}
	//			]}
	//		]}
		]},
		{"type":"Box","percentWidth":100,"percentHeight":100,"minHeight":0,
		"components":[
			{"type":"EditContainer","percentHeight":100, "id":"edit",
			"components":[
				{"type":"DividedBox","percentWidth":100,"percentHeight":100,"direction":"horizontal",
				"components":[							
					{"type":"VBox","percentWidth":80,"percentHeight":100,"verticalScrollPolicy":"auto","minWidth":0,"minHeight":0,
					"setStyle":{"verticalGap":1,"backgroundColor":"#dcdcdc","paddigTop":8,"paddingBottom":4,"paddingLeft":4,"paddingRight":4},
					"components":[
						{"type":"TextInput","dataField":"modeReply","includeInLayout":0,"visible":0,"editable":0},
						{"type":"TextInput","dataField":"idReply","includeInLayout":0,"visible":0,"editable":0},
						{"type":"TextInput","dataField":"inReplyTo","includeInLayout":0,"visible":0,"editable":0},
						{"type":"TextInput","dataField":"MessageId","includeInLayout":0,"visible":0,"editable":0},
						{"type":"TextInput","dataField":"MailUserId","includeInLayout":0,"visible":0,"editable":0},
						{"type":"FormItem","label":"Enquiry","labelWidth":80,"components":[
							{"type":"DataItem","dataField":"EnquiryIdEnquiry","displayFields":[
								{"name":"Reference","description":"Enquiry reference"}
							],
							"keyType":"field","keyMandatory":false,
							"kobeyeClass":{"module":"Murphy","objectClass":"Enquiry","keyName":"EnquiryIdEnquiry",
							"select":["Id","Reference"],"icon":"[None]","form":"PopupList.json"},
							"actions":[
								{"type":"proxy", "triggers":[
									{"trigger":"linkEnquiry","action":"invoke","method":"linkParent"},
									{"trigger":"unlinkEnquiry","action":"invoke","method":"unlinkParent"}
								]}
							]}
						]},					
						{"type":"FormItem","label":"Contract","labelWidth":80,"components":[
							{"type":"DataItem","dataField":"ContractIdContract","displayFields":[
								{"name":"Reference","description":"Contract reference"}
							],
							"keyType":"field","keyMandatory":false,
							"kobeyeClass":{"module":"Murphy","objectClass":"Contract","keyName":"ContractIdContract",
							"select":["Id","Reference"],"icon":"[None]","form":"PopupList.json"},
							"actions":[
								{"type":"proxy", "triggers":[
									{"trigger":"linkContract","action":"invoke","method":"linkParent"},
									{"trigger":"unlinkContract","action":"invoke","method":"unlinkParent"}
								]}
							]}
						]},	
//						{"type":"FormItem","label":"Contact","labelWidth":80,"components":[
//							{"type":"DataItem","dataField":"ContactIdMessage","displayFields":[
//								{"name":"FullName","description":"Nom"}
//							],
//							"keyType":"field","keyMandatory":false,
//							"kobeyeClass":{"module":"Murphy","objectClass":"Contact","keyName":"ContactIdMessage",
//							"select":["Id","FullName"],"icon":"[None]","form":"PopupList.json"},
//							"actions":[
//								{"type":"proxy", "triggers":[
//									{"trigger":"linkContract","action":"invoke","method":"linkParent"},
//									{"trigger":"unlinkContract","action":"invoke","method":"unlinkParent"}
//								]}
//							]}
//						]},	
						{"type":"FormItem","label":"Date","labelWidth":80,"components":[
							{"type":"TextInput","dataField":"DateTime","width":90,"editable":0,"enabled":0}
						]},					
						{"type":"FormItem","label":"From","labelWidth":80,"components":[
							{"type":"MailAddress","dataField":"FromAddress","percentWidth":100,"minHeight":22,"listWidth":250,"maxAddresses":1,
							"params":{"method":"object","data":{"module":"Mail","objectClass":"Message"},"function":"GetMailAddresses"},
							//"kobeyeClass":{"module":"Murphy","objectClass":"Contact","select":"Id,Email,FullName","setFilter":"Email!="},
							"defaultValue":"userMail","stateGroup":"editable"}
						]},					
						{"type":"FormItem","label":"To","labelWidth":80,"components":[
							{"type":"MailAddress","dataField":"ToAddress","percentWidth":100,"listWidth":250,
							"params":{"method":"object","data":{"module":"Mail","objectClass":"Message"},"function":"GetMailAddresses"},
							//"kobeyeClass":{"module":"Murphy","objectClass":"Contact","select":"Id,Email,FullName","setFilter":"Email!="},
							"stateGroup":"editable"}
						]},					
						{"type":"FormItem","label":"CC","labelWidth":80,"components":[
							{"type":"MailAddress","dataField":"CcAddress","percentWidth":100,"listWidth":250,
							"params":{"method":"object","data":{"module":"Mail","objectClass":"Message"},"function":"GetMailAddresses"},
							//"kobeyeClass":{"module":"Murphy","objectClass":"Contact","select":"Id,Email,FullName","setFilter":"Email!="},
							"stateGroup":"editable"}
						]},					
//						{"type":"FormItem","label":"CCi","labelWidth":80,"components":[
//							{"type":"MailAddress","dataField":"CciAddress","percentWidth":100,"listWidth":250,
//							"params":{"method":"object","data":{"module":"Mail","objectClass":"Message"},"function":"GetMailAddresses"},
//							//"kobeyeClass":{"module":"Murphy","objectClass":"Contact","select":"Id,Email,FullName","setFilter":"Email!="},
//							"stateGroup":"editable"}
//						]},					
						{"type":"FormItem","label":"Subject","labelWidth":80,"components":[
							{"type":"TextInput","dataField":"Subject","percentWidth":100,"stateGroup":"editable"}
						]},
						{"type":"TextArea","dataField":"Body","percentWidth":100,"percentHeight":100,
						"setStyle":{"headerHeight":0,"dropShadowVisible":0,"borderVisible":0},"stateGroup":"editable"}
					]},
					{"type":"VBox","percentWidth":20,"percentHeight":100,"components":[
						{"type":"HBox","percentWidth":100,"setStyle":{"gap":1,"paddingLeft":4,"paddingTop":4,"paddingBottom":4,"backgroundColor":"#d9d9d9"},
							"components":[
								{"type":"ImageButton","id":"editAtt","width":16,"height":16,"cornerRadius":8,"image":"mwc_i","borderWidth":1},
								{"type":"ImageButton","id":"newAtt","width":16,"height":16,"cornerRadius":8,"image":"mwc_plus","borderWidth":1,"stateGroup":"disabled",
								"events":[
									{"type":"click","action":"invoke","method":"selectFile","params":{"dataComponentID":"attachment","property":"Doc"}}
								]},
								{"type":"ImageButton","id":"deleteAtt","width":16,"height":16,"cornerRadius":8,"image":"mwc_moins","borderWidth":1,"stateGroup":"disabled"}
							]
						},
						{"type":"AdvancedDataGrid","id":"attachment","dataField":"attachment","percentWidth":100,"percentHeight":100,"rowHeight":20,"variableRowHeight":1,
						"kobeyeClass":{"dirtyParent":1,"objectClass":"Attachment"},
						"events":[
							//{"type":"start","action":"loadValues","params":{"needsParentId":1}},
							{"type":"dblclick","action":"invoke","method":"loadURL","params":{"url":"Doc"}},
							{"type":"proxy", "triggers":[
								{"trigger":"editAtt","action":"invoke","method":"loadURL","params":{"url":"Doc"}},
								{"trigger":"deleteAtt","action":"invoke","method":"deleteFromSelection"}
							]}
						],
						"columns":[
							{"type":"column","dataField":"Id","headerText":"ID","visible":0},
							{"type":"column","dataField":"Doc","headerText":"Attachment","width":200}
						]}
					]}				
				]}
			],
			"events":[
	//			[IF [!MC!]=1]
					{"type":"start","action":"invoke","method":"callMethod",
					"params":{"method":"object","function":"GetMessage",
					"args":"id:formCreator,pv:replyMode,pv:listId,ob:formCreator"}},
	//			[ELSE]
	//				{"type":"start","action":"invoke","method":"callMethod",
	//				"params":{"method":"object","function":"GetMessage",
	//				"args":[{"id":["formCreator"]},{"proxyValue":["replyMode"]}]}},
	//			[/IF]
				{"type":"proxy","triggers":[
					{"trigger":"saveClose","action":"invoke","method":"callMethod","params":{
					"method":"object","function":"saveMessage","args":"dv:*","closeForm":1}},
					{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":0}},
					{"trigger":"close","action":"invoke","objectID":"parentForm","method":"closeForm"},
					{"trigger":"delete","action":"invoke","method":"deleteData"}
					//{"trigger":"cancel","action":"invoke","method":"cancelEdit"}
				]}
			]}
		],
		"events":[
			{"type":"proxy","triggers":[
				{"trigger":"reply","action":"invoke","method":"callMethod",
				"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
				"args":"id:parentForm,v:reply,id:maillist,ob:parentForm"}},
				{"trigger":"replyAll","action":"invoke","method":"callMethod",
				"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
				"args":"id:parentForm,v:replyAll,id:maillist,ob:parentForm"}},
				{"trigger":"forward","action":"invoke","method":"callMethod",
				"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
				"args":"id:parentForm,v:forward,id:maillist,ob:parentForm"}}
//				{"trigger":"reply","action":"invoke","action":"loadForm","params":{"containerID":"parentContainer",
//				"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"[!form!].json",
//				"proxyValues":{"vars":{"replyMode":"reply"}}}}},
//				{"trigger":"replyAll","action":"invoke","action":"loadForm","params":{"containerID":"parentContainer",
//				"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"[!form!].json",
//				"proxyValues":{"vars":{"replyMode":"replyAll"}}}}},
//				{"trigger":"forward","action":"invoke","action":"loadForm","params":{"containerID":"parentContainer",
//				"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"[!form!].json",
//				"proxyValues":{"vars":{"replyMode":"forward"}}}}}
			]}
		]}
	]}
],
[IF [!firstField!]]"focusedID":"[!firstField!]",[/IF]
[IF [!POPUP!]]"popup":"modal",[/IF]
"actions":[
	{"type":"close", "action":"confirmUpdate"}
]}
}


