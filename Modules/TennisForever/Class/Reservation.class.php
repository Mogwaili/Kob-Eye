<?php
class Reservation extends genericClass {
    var $_partenaires = array();
    var $_produits = array();
    var $_date = null;
    var $_heuredebut = null;
    var $_nbinvites = 0;

    /**
     * UTILS
     */
    function deleteLigneFacture($type){
        $tmp = $this->_produits;
        $this->_produits = array();
        foreach ($tmp as $k=>$p){
            if ($p->Type!=$type) array_push($this->_produits,$p);
        }
    }
    function addLigneFacture($libelle,$tarif,$quantite=1,$prod=null,$type='Service') {
        $ser = genericClass::createInstance('TennisForever', 'LigneFacture');
        $ser->Libelle = $libelle;
        $ser->Type = $type;
        $ser->Quantite = $quantite;
        $ser->MontantUnitaireTTC = $tarif;
        $ser->MontantTTC = $tarif*$quantite;
        $ser->addParent($prod);
        array_push($this->_produits, $ser);
        return $ser;

    }

    /**
     * GETTER
     */
    function getFacture() {
        $total = $this->getTotal();
        $fact = Sys::getOneData('TennisForever','Reservation/'.$this->Id.'/Facture');
        if ($fact) return $fact;
        elseif ($total >0){
            //création de la facture
            $fact = genericClass::createInstance('TennisForever','Facture');
            $fact->MontantTTC = $total;
            $fact->MontantHT = $total/1.20;
            $fact->addParent($this->getClient());
            $fact->addParent($this);
            $fact->Save();

            //on attache aussi toutes les lignes facture
            $lf = Sys::getData('TennisForever','Reservation/'.$this->Id.'/LigneFacture');
            foreach ($lf as $l) {
                $l -> addParent($fact);
                $l->Save();
            }

            return $fact;
        }
        return false;
    }
    function getLigneFacture() {
        if ($this->Id){
            //on cherche dans les parents en base
            return Sys::getData('TennisForever','Reservation/'.$this->Id.'/LigneFacture');
        }else return $this->_produits;
    }
    function getPartenaires() {
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getData('TennisForever','Reservation/'.$this->Id.'/Partenaire');
            if ($out) return $out;
        }else return $this->_partenaires;
    }
    function getClient() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getOneData('TennisForever','Client/Reservation/'.$this->Id);
            if ($out) return $out;
        }
        if (!$out){
            //on cherche dans le tableau parrent temporaire.
            foreach ($this->Parents as $p){
                if ($p["Titre"]=='Client') {
                    return Sys::getOneData('TennisForever','Client/'.$p["Id"]);
                }
            }
        }
        return false;
    }
    function getService() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getOneData('TennisForever','Service/Reservation/'.$this->Id);
            if ($out) return $out;
        }
        if (!$out){
            //on cherche dans le tableau parent temporaire.
            if (is_array($this->Parents))foreach ($this->Parents as $p){
                if ($p["Titre"]=='Service') {
                    return Sys::getOneData('TennisForever','Service/'.$p["Id"]);
                }
            }
        }
        return false;
    }
    function getCourt() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getOneData('TennisForever','Court/Reservation/'.$this->Id);
            if ($out) return $out;
        }
        if (!$out){
            //on cherche dans le tableau parrent temporaire.
            foreach ($this->Parents as $p){
                if ($p["Titre"]=='Court') {
                    return Sys::getOneData('TennisForever','Court/'.$p["Id"]);
                }
            }
        }
        return false;
    }

    /**
     * FRONT SETTER
     */
    /**
     * Définit la réservation
     */
    function checkReservation() {
        //ajout de la ligne de service
        $client = $this->getClient();
        $service = $this->getService();
        $court = $this->getCourt();
        $typecourt = $court->getParents('TypeCourt');
        $typecourt = $typecourt[0];
        if ($service) {
            //suppression si existante
            $this->deleteLigneFacture('Réservation');
            //ajout de la réservation
            $this->addLigneFacture($service->Titre, $service->getTarif($client, $this->DateDebut, $this->DateFin), 1, $service, 'Réservation');
        }
        
        //partenaires
        $nbabonnes = sizeof($this->getPartenaires());
        $this->_nbinvites;
        $this->deleteLigneFacture('Invitation');
        if ($service->TarifInvite>0&&$this->_nbinvites>0&$client->isSubscriber()
            || $service->TarifInvite>0&&$this->_nbinvites>0&&$typecourt->GestionInvite=="Quantitatif"){
            $this->addLigneFacture($service->Titre.' - Invitation',$service->TarifInvite,$this->_nbinvites,$service,'Invitation');
        }

        $this->NbParticipant = $this->_nbinvites+ $nbabonnes + 1;

        //définition du Nom de la réservation
        $this->Nom = $client->Nom.' '.$client->Prenom.' - '.$service->Titre; //date('d/m/Y à H:i',$this->DateDebut);
    }


    function setPartenaires($parts){
        $this->_partenaires = array();
        if (is_array($parts))foreach ($parts as $p){
            $p = (array)$p;
            if ($p['Client']>0) {
                //recherche du client
                $cli = Sys::getOneData('TennisForever','Client/'.$p['Client']);
                $part = $cli->getChildren('Partenaire');
                if (!sizeof($part)){
                    //il faut le créer
                    $pa = genericClass::createInstance('TennisForever', 'Partenaire');
                    $pa->Nom = $cli->Nom;
                    $pa->Email = $cli->Email;
                    $pa->Prenom = $cli->Prenom;
                    $pa->addParent($cli);
                    $pa->Save();
                }else{
                    $pa = $part[0];
                }
                array_push($this->_partenaires, $pa);
            }else $this->_nbinvites++;
        }
        if (!sizeof($parts)){
            $this->_nbinvites=1;
        }
    }
    function setNombrePartenaires($nb){
        $this->_nbinvites=$nb;
    }

    function setProduits($produit){
        if (is_array($produit))foreach ($produit as $i=>$p){
            if ($p>0) {
                $client = $this->getClient();
                //récupération du produit
                $prod = Sys::getOneData('TennisForever', 'Service/' . $i);
//                $this->addLigneFacture($prod->Titre,$client->isSubscriber() ? $prod->TarifInvite:$prod->Tarif,$p,$prod,'Produit');
                $this->addLigneFacture($prod->Titre,$prod->Tarif,$p,$prod,'Produit');
            }
        }
    }
    function setClient($cli) {
        $this->addParent($cli);
    }
    function setCourt($court) {
        $this->addParent($court);
    }
    function setService($service) {
        $this->addParent($service);
    }
    function setDate($date){
        $this->_date = mktime(0,0,0,date('n', $date),date('j', $date),date('Y', $date));
    }
    function setHeureDebut($heure){
        //transformation de l'heure
        $t = explode(':',$heure);
        $sec = $t[0]*3600;
        $sec+= $t[1]*60;
        $this->_heuredebut = $sec;
    }

    /**
     * CHECKER
     */
    function checkClient() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getCount('TennisForever','Client/Reservation/'.$this->Id);
            if ($out) return true;
        }
        if (!$out){
            //on cherche dans le tableau parrent temporaire.
            foreach ($this->Parents as $p){
                if ($p["Titre"]=='Client') return true;
            }
        }
        return false;
    }
    function checkCourt() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getCount('TennisForever','Court/Reservation/'.$this->Id);
            if ($out) return true;
        }
        if (!$out){
            //on cherche dans le tableau parrent temporaire.
            foreach ($this->Parents as $p){
                if ($p["Titre"]=='Court') return true;
            }
        }
        return false;
    }
    function checkService() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getCount('TennisForever','Service/Reservation/'.$this->Id);
            if ($out) return true;
        }
        if (!$out){
            //on cherche dans le tableau parrent temporaire.
            foreach ($this->Parents as $p){
                if ($p["Titre"]=='Service') return true;
            }
        }
        return false;
    }
    function checkDate() {
        if ($this->_date&&$this->_heuredebut){
            //récupération du service pour la durée
            $service = $this->getService();
            if (!$service) return false;

            //definition des heuredebut et heurefin
            $this->DateDebut = $this->_date + $this->_heuredebut;
            $this->DateFin = $this->DateDebut + $service->Duree*60;
            $this->_date = null;
            $this->_heuredebut = null;
        }
        //il faut également le court
        $court = $this->getCourt();
        if (!$court) return false;

        if ($this->DateDebut && $this->DateFin){
            return true;
        }else return false;
    }
    function checkDateJour() {
        if ($this->_date){
            //definition des heuredebut et heurefin
            $this->DateDebut = $this->_date;
            $this->DateFin = $this->DateDebut + 86400;
        }
        //il faut également le court
        $court = $this->getCourt();
        if (!$court) return false;

        if ($this->DateDebut && $this->DateFin){
            return true;
        }else return false;
    }
    function checkDispo() {

        //il faut également le court
        if ($this->Id&&$this->Valide)return true;
        $court = $this->getCourt();
        $tc = $court->getParents('TypeCourt');
        $tc = $tc[0];
        if (!$court) return false;

        if ($this->DateDebut && $this->DateFin){
            //RESERVATION
            if ($tc->Reservation=='Horaire') {
                //test du debut à cheval
                $out = Sys::getCount('TennisForever', 'Court/' . $court->Id . '/Reservation/Valide=1&DateDebut>=' . $this->DateDebut . '&DateDebut<' . $this->DateFin);
                if ($out) return false;
                //test de la fin à cheval
                $out = Sys::getCount('TennisForever', 'Court/' . $court->Id . '/Reservation/Valide=1&DateFin>' . $this->DateDebut . '&DateFin<=' . $this->DateFin);
                if ($out) return false;
                //test des deux en encadrement intérieur
                $out = Sys::getCount('TennisForever', 'Court/' . $court->Id . '/Reservation/Valide=1&DateFin<=' . $this->DateFin . '&DateDebut>=' . $this->DateDebut);
                if ($out) return false;
                //test les deux en encadrement extérieur
                $out = Sys::getCount('TennisForever', 'Court/' . $court->Id . '/Reservation/Valide=1&DateFin>=' . $this->DateFin . '&DateDebut<=' . $this->DateDebut);
                if ($out) return false;
            }
            //DISPONIBILITE
            //test du debut à cheval
            $out = Sys::getCount('TennisForever','Court/'.$court->Id.'/Disponibilite/Debut>='.$this->DateDebut.'&Debut<'.$this->DateFin);
            if ($out) return false;
            //test de la fin à cheval
            $out = Sys::getCount('TennisForever','Court/'.$court->Id.'/Disponibilite/Fin>'.$this->DateDebut.'&Fin<='.$this->DateFin);
            if ($out) return false;
            //test des deux en encadrement intérieur
            $out = Sys::getCount('TennisForever','Court/'.$court->Id.'/Disponibilite/Fin<='.$this->DateFin.'&Debut>='.$this->DateDebut);
            if ($out) return false;
            //test les deux en encadrement extérieur
            $out = Sys::getCount('TennisForever','Court/'.$court->Id.'/Disponibilite/Fin>='.$this->DateFin.'&Debut<='.$this->DateDebut);
            if ($out) return false;
            return true;
        }else return false;
    }

    function Verify() {
        if (!$this->checkClient()){
            $this->addError(array("Message"=>"Veuillez saisir le client pour cette réservation"));
        }
        if (!$this->checkCourt()){
            $this->addError(array("Message"=>"Veuillez saisir le court pour cette réservation"));
        }
        //on récupère le type de court
        $court= $this->getCourt();
        $tc = $court->getParents('TypeCourt');
        $tc = $tc[0];
        if ($tc->Reservation=='Horaire') {
            if (!$this->checkDate()) {
                $this->addError(array("Message" => "Veuillez vérifier la saisie des dates"));
            }
            //vérifie le client
            if (!$this->checkService()) {
                $this->addError(array("Message" => "Veuillez saisir le service pour cette réservation"));
            }
            if (!$this->checkDispo()) {
                $this->addError(array("Message" => "Cette horaire n'est pas disponible"));
                $this->Valide = false;
            }
        }else{
            if (!$this->checkDateJour()) {
                $this->addError(array("Message" => "Veuillez vérifier la saisie de la date"));
            }
            if (!$this->checkDispo()) {
                $this->addError(array("Message" => "Ce jour n'est pas disponible"));
                $this->Valide = false;
            }
        }
        if (sizeof($this->Error)) return false;

        //si tout est ok alors on configure la réservation
        $this->checkReservation();

        return parent::Verify();
    }

    function Save(){
        $new = false;
        if (!$this->Id) {
            $new = true;
        }
        parent::Save();
        if ($new){
            //Ajout d'une ligne pour la réservation
            $service = $this->getService();
            $client = $this->getClient();


            //Saisie des partenaires.
            if (is_array($this->_partenaires)) foreach ($this->_partenaires as $p) {
                //on vérifie que le partenaire n'est pas abonne
                //creation du partenaire
                $p->AddParent($this);
                $p->Save();
            }

            //Saisie des produits
            if (is_array($this->_produits)) foreach ($this->_produits as $p) {
                //creation du partenaire
                $p->AddParent($this);
                $p->Save();
            }
        }

        //enregistrement de la réservation
        parent::Save();
    }

    function Delete() {
        if ($this->Id) {
            //suppression des lignesfactures
            $prods = Sys::getData('TennisForever', 'Reservation/'.$this->Id.'/LigneFacture');
            foreach($prods as $p) $p->Delete();

            //suppression de la facture
            $facts = Sys::getData('TennisForever', 'Reservation/'.$this->Id.'/Facture');
            foreach($facts as $f) $f->Delete();

            parent::Delete();
        }
    }
    function getTotal() {
        $lf = $this->getLigneFacture();
        $total = 0;
        foreach ($lf as $l){
            $total+= $l->MontantTTC;
        }
        return $total;
    }

    /**
     * Validation de la réservation
     */
    function setValide() {
        $this->Valide=1;
        $this->Save();
        //TODO
        //envoi du mail
        $this->sendMail();
    }

    function sendMail() {
        $cli = $this -> getClient();


        $Civilite = $cli -> Civilite . " " . $cli -> Prenom . ' <span style="text-transform:uppercase">' . $cli -> Nom . '</span>';

        $lf = $this->getLigneFacture();
        if (!sizeof($lf)) {
            $Lacommande = "Erreur";
        } else {
            $Lacommande = "<br /> Vous avez déclaré $this->NbParticipant participant(s) au total ";
            $parts = $this->getPartenaires();
            if (sizeof($parts)) {
                $Lacommande .= "<br /> Vous jouerez avec le(s) partenaire(s) suivant(s): <ul>";
                foreach ($parts as $p) {
                    $Lacommande .= "<li>$p->Nom $p->Prenom</li>";
                }
                $Lacommande .= "</ul>";
            }
            $Lacommande .= "<h2>Récapitulatif de votre réservation  : </h2><br /><br /><table width='100%'>";
            $Lacommande .= "<tr bgcolor='#666' padding='5'><td><font color='#ffffff'>Quantite</font></td><td><font color='#ffffff'>Titre</font></td><td><font color='#ffffff'>Tarif TTC</font></td></tr>";
            $total = 0;
            foreach ($lf as $l) :
                //récupération du produit
                $Lacommande .= "<td><h3>" . $l->Quantite . "</h3> </td>";
                $Lacommande .= "<td><h3>" . $l->Libelle . "</h3></td>";
                $Lacommande .= "<td><h2>" . $l->MontantTTC . " € TTC</h2></td></tr>";
                $total += $l->MontantTTC;
            endforeach;
            $Lacommande .= "
                <tr bgcolor='#666' padding='5'>
                    <td colspan='2'></td>
                    <td><font color='#ffffff'>TOTAL</font></td>
                    <td><font color='#ffffff'><h2>$total € TTC</h2></font></td>
                </tr>
            </table>";

        }

        require_once ("Class/Lib/Mail.class.php");
        $Mail = new Mail();
        $Mail->Subject("TENNISFOREVER Confirmation de reservation");
        $Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::TENNISFOREVER::CONTACT'));
        $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::TENNISFOREVER::CONTACT'));
        $Mail -> To($cli -> Mail);
        //$Mail -> To('enguer@enguer.com');
        $Mail -> Bcc('enguer@enguer.com');
        $Mail -> Cc($GLOBALS['Systeme'] -> Conf -> get('MODULE::TENNISFOREVER::CONTACT'));
        $bloc = new Bloc();
        $mailContent = "
            Bonjour " . $Civilite . ",<br /><br />
            Nous vous informons que votre réservation N° " . $this->Nom . " pour le ".date("d/m/Y à H:i",$this->DateDebut)." a bien été prise en compte.<br />Attention, il est possible dans certains cas , que votre réservation court extérieur soit basculée en court couvert. Aucun supplément ne vous sera facturé. 
            <br />Toute l'équipe de TENNIS FOREVER vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::TENNISFOREVER::CONTACT') . " .".$Lacommande;

        $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
        $Pr = new Process();
        $bloc -> init($Pr);
        $bloc -> generate($Pr);
        $Mail -> Body($bloc -> Affich());
        if (!$this->Cloture)
            $Mail -> Send();
    }
}