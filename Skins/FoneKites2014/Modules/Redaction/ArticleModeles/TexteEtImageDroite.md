<div class="article [!Couleur!]">
    <div class=" container nopadding-right nopadding-left">
        [IF [!Art::AfficheTitre!]]
            <h4>[!Art::Titre!]</h4>
        [/IF]
        <div class="row">
            <div class="col-md-6">
                    [!Art::Contenu!]
                    // Partie lien
                    [STORPROC Redaction/Article/[!Art::Id!]/Lien|ArtLie]
                            <div class="panel panel-default">
                                    <a href="[!ArtLie::URL!]">
                                            <div style="margin:15px 20px 0;" class="glyphicon glyphicon-circle-arrow-right pull-right"></div>
                                            <div class="panel-body">
                                              <strong>[!ArtLie::Titre!]</strong>
                                            </div>
                                    </a>
                            </div>
                    [/STORPROC]
            
                    // Partie fichier et vidéo
                    [STORPROC Redaction/Article/[!Art::Id!]/Fichier|ArtFic]
                            <div class="panel panel-default">
                                    <a href="/[!ArtFic::URL!]">
                                            <div class="glyphicon glyphicon-save pull-right"style="margin:15px 20px 0;"></div>
                                            <div class="panel-body">
                                              <strong>[!ArtFic::Titre!]</strong>
                                            </div>
                                    </a>
                            </div>
                    [/STORPROC]
            </div>
            <div class="col-md-6">
                    <img src="/[!Art::Image!]" class="img-responsive"/>
            </div>
        </div>
       </div>
</div>