<?php

class Apache extends genericClass {
	var $_KEHost;
	var $_KEServer;
	var $_isVerified = false;
	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	void
	 */
	public function Save( $synchro = true ) {
		if ($this->Ssl){
			//test de l'activation ssl
			$old = Sys::getOneData('Parc','Apache/'.$this->Id);
			if (!$old->Ssl&&$this->SslMethod=="Letsencrypt") $this->enableSsl();
		}
		// Forcer la vérification
		if(!$this->_isVerified) $this->Verify( $synchro );
		// Enregistrement si pas d'erreur
		if($this->_isVerified) parent::Save();
	}

	public function enableSsl() {
		if (empty($this->SslMethod))$this->SslMethod = "Letsencrypt";
		if ($this->Ssl&&!empty($this->SslCertificate)&&!empty($this->SslCertificateKey)&&$this->SslExpiration>time()){
			$this->addError(array("Message"=>"Le certificat est déjà généré et valide."));
			return;
		}
		switch($this->SslMethod){
			case "Letsencrypt":
				//définition de la date d'expiration
				$this->SslExpiration=time()+(86400*90);
				$this->Ssl = true;
				$serv = $this->getKEServer();
				//pour activer ssl il faut déclencher une tache
				$task  = genericClass::createInstance('Parc','Tache');
				$task->Nom = "Activation SSL pour la configuration Apache ".$this->ApacheServerName." ( ".$this->Id." )";
				$task->Type = "Ssh";
				$task->Contenu = "/usr/src/certbot/certbot-auto certonly --quiet --pre-hook \"service httpd stop | killall -9 httpd.worker\" --post-hook \"service httpd start\" --standalone -d ".$this->ApacheServerName;
				// ajout des server alias
				$sa = explode("\n",$this->ApacheServerAlias);
				if (!empty($sa[0]))foreach ($sa as $s ){
					$task->Contenu .= " -d ".trim($s);
				}
				$task->Contenu .= "\n cat /etc/letsencrypt/live/".$this->ApacheServerName."/fullchain.pem";
				$task->Contenu .= "\n cat /etc/letsencrypt/live/".$this->ApacheServerName."/privkey.pem";
				$task->addParent($this);
				$task->addParent($serv);
				$task->Save();
				parent::Save();
			break;
			default:
			break;
		}
	}

	public function getRootPath() {
		if (!$this->Id){
			//recherche de l'hote dans le parent
			foreach ($this->Parents as $p){
				if ($p['Titre']=='Host'){
					$ho = Sys::getOneData('Parc','Host/'.$p['Id']);
					return '/home/'.$ho->Nom.'/www';
				}
			}
			return 'Tout neuf';
		}else return $this->DocumentRoot;
	}

	/**
	 * Verification des erreurs possibles
	 * @param	boolean	Verifie aussi sur LDAP
	 * @return	Verification OK ou NON
	 */
	public function Verify( $synchro = true ) {
		$this->addWarning(array("Message"=>"Veuillez bien vérifier que les ServerName soit bien configuré et pointe bien vers le serveur. Les ServerAlias doivent également bien pointer sur le serveur, sinon l'activation SSL échouera."));

		//check documentRoot
		if (substr($this->DocumentRoot,strlen($this->DocumentRoot)-1,1)=='/') $this->DocumentRoot = substr($this->DocumentRoot,0,-1);

		if(parent::Verify()) {

			$this->_isVerified = true;

			if($synchro) {

				// Outils
				$KEHost = $this->getKEHost();
				$KEServer = $this->getKEServer();
				$dn = 'apacheServerName='.$this->ApacheServerName.',cn='.$KEHost->Nom.',ou='.$KEServer->LDAPNom.',ou=servers,'.PARC_LDAP_BASE;
	
				// Verification à jour
				$res = Server::checkTms($this);
				if($res['exists']) {
					if(!$res['OK']) {
						$this->AddError($res);
						$this->_isVerified = false;
					}
					else {
						// Déplacement
						$res = Server::ldapRename($this->LdapDN, 'apacheServerName='.$this->ApacheServerName, 'cn='.$KEHost->Nom.',ou='.$KEServer->LDAPNom.',ou=servers,'.PARC_LDAP_BASE);
						if($res['OK']) {
							// Modification
							$entry = $this->buildEntry(false);
							$res = Server::ldapModify($this->LdapID, $entry);
							if($res['OK']) {
								// Tout s'est passé correctement
								$this->LdapDN = $dn;
								$this->LdapTms = $res['LdapTms'];
							}
							else {
								// Erreur
								$this->AddError($res);
								$this->_isVerified = false;
								// Rollback du déplacement
								$tab = explode(',', $this->LdapDN);
								$leaf = array_shift($tab);
								$rest = implode(',', $tab);
								Server::ldapRename($dn, $leaf, $rest);
							}
						}
						else {
							$this->AddError($res);
							$this->_isVerified = false;
						}
					}
	
				}
				else {
					////////// Nouvel élément
					if($KEHost) {
						$entry = $this->buildEntry();
						$res = Server::ldapAdd($dn, $entry);
						if($res['OK']) {
							$this->LdapDN = $dn;
							$this->LdapID = $res['LdapID'];
							$this->LdapTms = $res['LdapTms'];
						}
						else {
							$this->AddError($res);
							$this->_isVerified = false;
						}
					}
					else {
						$this->AddError(array('Message' => "Une configuration Apache doit obligatoirement être créé dans un hébergement donné.", 'Prop' => ''));
						$this->_isVerified = false;
					}
				}

			}

		}
		else {

			$this->_isVerified = false;

		}

		return $this->_isVerified;

	}

	/**
	 * Récupère une référence vers l'objet KE "Host" parent
	 * On conserve une référence vers le host
	 * pour le cas d'une utilisation ultérieure
	 * @return	L'objet Kob-Eye
	 */
	private function getKEHost() {
		if(!is_object($this->_KEHost)) {
			$tab = $this->getParents('Host');
			if(empty($tab)) return false;
			else $this->_KEHost = $tab[0];
		}
		return $this->_KEHost;
	}

	/**
	 * Configuration d'une nouvelle entrée type
	 * Utilisé lors du test dans Verify
	 * puis lors du vrai ajout dans Save
	 * @param	boolean		Si FALSE c'est simplement une mise à jour
	 * @return	Array
	 */
	private function buildEntry( $new = true ) {
		$entry = array();
		if(!empty($this->ApacheServerAlias)) {
			$alias = explode("\n", $this->ApacheServerAlias);
			$entry['apacheserveralias'] = array();
			foreach($alias as $k => $a)	$entry['apacheserveralias'][$k] = $a;
		}elseif (!$new) $entry['apacheserveralias'] = array();

		$entry['apachesuexecuid'] = $this->_KEHost->Nom;
		$Client = $this->_KEHost->getKEClient();
		$entry['apachesuexecgid'] = $Client->NomLDAP;
		$entry['apacheservername'] = $this->ApacheServerName;
		$entry['apachescriptalias'] = '/cgi-bin/ /home/'.$this->_KEHost->Nom.'/cgi-bin/';
		$entry['apachedocumentroot'] = $this->DocumentRoot;
		if($new) {
			$entry['objectclass'][0] = 'apacheConfig';
			$entry['objectclass'][1] = 'top';
		}
		$entry['apachevhostenabled'] = $this->Actif?'yes':'no';
		$entry['apacheHtPasswordEnabled'] = $this->PasswordProtected?'yes':'no';
		if ($this->PasswordProtected) {
			$entry['apacheOptions'][] = 'AuthType Basic';
			$entry['apacheOptions'][] = 'AuthName "Authentication Required"';
			$entry['apacheOptions'][] = 'AuthUserFile "'.$this->DocumentRoot.'/.htpasswd"';
			$entry['apacheOptions'][] = 'Require valid-user';
			$entry['apacheHtPasswordUser'] = $this->HtaccessUser;
			$entry['apacheHtPasswordPassword'] = $this->HtaccessPassword;
		}elseif (!$new){
			$entry['apacheOptions'] = Array();
		}
		if ($this->Ssl&&!empty($this->SslCertificate)&&!empty($this->SslCertificateKey)){
			$entry['apacheSslEnabled'] = 'yes';
			$entry['apacheCertificate'] = base64_encode($this->SslCertificate);
			$entry['apacheCertificateKey'] = base64_encode($this->SslCertificateKey);
			$entry['apacheCertificateExpiration'] = $this->SslExpiration;
		}else{
			$entry['apacheSslEnabled'] = 'no';
		}
		return $entry;
	}


	/**
	 * Suppression de la BDD
	 * Relai de cette suppression à LDAP
	 * On utilise aussi la fonction de la superclasse
	 * @return	void
	 */
	public function Delete() {
		$KEServer = $this->getKEServer();
		Server::ldapDelete($this->LdapID);
		parent::Delete();
	}

	/**
	 * Récupère une référence vers l'objet KE "Server"
	 * pour effectuer des requetes LDAP
	 * On conserve une référence vers le serveur
	 * pour le cas d'une utilisation ultérieure
	 * @return	L'objet Kob-Eye
	 */
	private function getKEServer() {
		if(!is_object($this->_KEServer)) {
			$KEHost = $this->getKEHost();
			if($KEHost)	$this->_KEServer = $KEHost->getKEServer();
			else return false;
		}
		return $this->_KEServer;
	}

	/**
	 * Retrouve les parents lors d'une synchronisation
	 * @return	void
	 */
	public function findParents() {
		$Parts = explode(',', $this->LdapDN);
		foreach($Parts as $i => $P) $Parts[$i] = explode('=', $P);
		// Parent Host
		$Tab = Sys::$Modules["Parc"]->callData("Parc/Host/Nom=".$Parts[1][1], "", 0, 1);
		if(!empty($Tab)) {
			$obj = genericClass::createInstance('Parc', $Tab[0]);
			$this->AddParent($obj);
		}
	}

	/**
	 * Retrouve les sous domaines qui correspondent
	 * ( Nécessité d'avoir ça dans les 2 sens )
	 * @return	void
	 */
	public function findSubDomains() {
		// Liste des domaines
		$Domains = array();
		if(!empty($this->ApacheServerName)) $Domains[] = $this->ApacheServerName;
		if(!empty($this->ApacheServerAlias)) {
			$Tab = explode("\r", $this->ApacheServerAlias);
			foreach($Tab as $url) if(!empty($url)) $Domains[] = $url;
		}
		$result = 0;
		// Recherche
		foreach($Domains as $url) {
			$Parts = explode('.', $url);
			$domain = "";
			$domain = array_pop($Parts);
			$domain = array_pop($Parts).'.'.$domain;
			$subdomain = implode('.', $Parts);
			$Tab = Sys::$Modules["Parc"]->callData("Parc/Domain/Url=$domain/Subdomain/Url=$subdomain", "", 0, 100);
			if(!empty($Tab)) {
				foreach($Tab as $o) {
					$result++;
					$obj = genericClass::createInstance('Parc', $Tab[0]);
					$obj->AddParent($this);
					$obj->Save(false);
				}
			}
		}
		return $result;
	}

	/**
	 * callBackTask
	 * function callback pour le retour de la tache
	 */
	public function callBackTask($msg){
		if (preg_match("#-----BEGIN CERTIFICATE-----#", $msg,$out)){
			//enregistrement du fullchain
			$this->SslCertificate = $msg;
			parent::Save();
		}
		if (preg_match("#-----BEGIN PRIVATE KEY-----#", $msg,$out)){
			$this->SslCertificateKey = $msg;
			parent::Save();
		}
	}
}