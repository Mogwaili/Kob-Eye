[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Child]
    [OBJ Boutique|Produit|O]
[ELSE]
        [STORPROC [!Query!]|O|0|1][/STORPROC]

[/IF]
[METHOD O|Set]
    [PARAM]Nom[/PARAM]
    [PARAM][!Nom!][/PARAM]
[/METHOD]
[METHOD O|Set]
    [PARAM]EAN[/PARAM]
    [PARAM][!EAN!][/PARAM]
[/METHOD]
[METHOD O|Set]
    [PARAM]Commentaire[/PARAM]
    [PARAM][!Commentaire!][/PARAM]
[/METHOD]
[IF [!I::TypeSearch!]=Direct]
    //on force les stocks
    [METHOD O|setStockForce][PARAM][!StockReference!][/PARAM][/METHOD]
[ELSE]
    [METHOD O|Set]
        [PARAM]StockReference[/PARAM]
        [PARAM][!StockReference!][/PARAM]
    [/METHOD]
[/IF]
[METHOD O|Set]
    [PARAM]Tarif[/PARAM]
    [PARAM][!Tarif!][/PARAM]
[/METHOD]
[METHOD O|Save][/METHOD]
{
success: true
}
