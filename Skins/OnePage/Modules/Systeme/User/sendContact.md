[IF [!Utils::isMail([!email!])!]]
    //Verification des informations du formulaire
    [LIB Mail|LeMail]
    [METHOD LeMail|Subject][PARAM][ENG.SYSTEMS] Nouveau message du formulaire de contact [!name!][/PARAM][/METHOD]
    [METHOD LeMail|From][PARAM][!email!][/PARAM][/METHOD]
    [METHOD LeMail|ReplyTo][PARAM][!email!][/PARAM][/METHOD]
    [METHOD LeMail|To][PARAM]contact@eng.systems[/PARAM][/METHOD]
    [METHOD LeMail|Body]
        [PARAM]
            [BLOC Mail]
                <font face="arial" color="#000000" size="2">
                <strong>SUBJECT</strong> : [!subject!]<br/>
                <strong>SENT BY</strong> : <span style="text-transform:uppercase">[!name!]</span><br/>
                <strong>EMAIL</strong> : [!email!]<br/>
                <strong>MESSAGE</strong> : [!message!]<br /></font>
            [/BLOC]
        [/PARAM]
    [/METHOD]
    [METHOD LeMail|BuildMail][/METHOD]
    [METHOD LeMail|Send][/METHOD]

    //Mail de confirmation
    [LIB Mail|LeMail]
    [METHOD LeMail|Subject][PARAM][ENG.SYSTEMS] Votre message a bien été envoyé.[/PARAM][/METHOD]
    [METHOD LeMail|From][PARAM]contact@eng.systems[/PARAM][/METHOD]
    [METHOD LeMail|ReplyTo][PARAM]contact@eng.systems[/PARAM][/METHOD]
    [METHOD LeMail|To][PARAM][!email!][/PARAM][/METHOD]
    [METHOD LeMail|Body]
    [PARAM]
    [BLOC Mail]
        Votre message a bien été envoyé et est en cours de traitement par nos services.<br/>
    Nous nous effrocons de répondre aussi vite que possible.<br/>
    Merci pour l'intéret que vous portez à la société ENG.SYSTEMS.<br/>
    <br/>

    Cordialement.
    [/BLOC]
    [/PARAM]
    [/METHOD]
    [METHOD LeMail|BuildMail][/METHOD]
    [METHOD LeMail|Send][/METHOD]
    {
    "success": true
    }
[ELSE]
{
    "success": false,
    "msg": 'email incorrect'
}
[/IF]