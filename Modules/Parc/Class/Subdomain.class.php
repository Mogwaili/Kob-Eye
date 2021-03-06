<?php

class Subdomain extends genericClass {
	var $_isVerified = false;
	var $_KEDomain = false;
	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	void
	 */
	public function Save( $synchro = true ) {
		parent::Save();
		// Forcer la vérification
		if(!$this->_isVerified) $this->Verify( $synchro );
		// Enregistrement si pas d'erreur
		if($this->_isVerified) {
			parent::Save();
			if($synchro) $this->_KEDomain->updateDnsSerial();
		}
	}

	/**
	 * Verification des erreurs possibles
	 * @param	boolean	Verifie aussi sur LDAP
	 * @return	Verification OK ou NON
	 */
	public function Verify( $synchro = true ) {

		if(parent::Verify()) {

			$this->_isVerified = true;

			if($synchro) {

				// Outils
				$KEDomain = $this->getKEDomain();
				$dn = 'cn='.$this->Url.',cn='.$KEDomain->Url.',ou=domains,'.PARC_LDAP_BASE;
				// Verification à jour
				$res = Server::checkTms($this);
				if($res['exists']) {
					if(!$res['OK']) {
						$this->AddError($res);
						$this->_isVerified = false;
					}
					else {
						// Déplacement
						$res = Server::ldapRename($this->LdapDN, 'cn='.$this->Url, 'cn='.$KEDomain->Url.',ou=domains,'.PARC_LDAP_BASE);
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
					if($KEDomain) {
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
						$this->AddError(array('Message' => "Une entrée de ce type doit être créée dans un domaine donné.", 'Prop' => ''));
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
	 * Configuration d'une nouvelle entrée type
	 * Utilisé lors du test dans Verify
	 * puis lors du vrai ajout dans Save
	 * @param	boolean		Si FALSE c'est simplement une mise à jour
	 * @return	Array
	 */
	private function buildEntry( $new = true ) {
		$entry = array();
		$entry['dnsipaddr'] = $this->IP;
		// Vérifie qu'il y a le A:
		$pre = substr($this->Url, 0, 2);
		if($pre != 'A:') $this->Url = 'A:' . $this->Url;
		// Récupère la partie "sous domaine"
		$entry['cn'] = $this->Url;
		$sD = substr($this->Url, 2);
		if (!empty($sD)) $entry['dnsdomainname'] = $sD;
		if($new) {
			$entry['dnsclass'] = 'IN';
			$entry['dnsttl'] = 86400;
			$entry['dnstype'] = 'A';
			$entry['objectclass'][0] = 'dnsrrset';
			$entry['objectclass'][1] = 'top';
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
		Server::ldapDelete($this->LdapID);
		parent::Delete();
	}

	/**
	 * Récupère une référence vers l'objet KE "Domain" parent
	 * On conserve une référence vers le host
	 * pour le cas d'une utilisation ultérieure
	 * @return	L'objet Kob-Eye
	 */
	private function getKEDomain() {
		if(!is_object($this->_KEDomain)) {
			$tab = $this->getParents('Domain');
			if(empty($tab)) return false;
			else $this->_KEDomain = $tab[0];
		}
		return $this->_KEDomain;
	}

	/**
	 * Récupère une référence vers l'objet KE "Server"
	 * pour effectuer des requetes LDAP
	 * On conserve une référence vers le serveur
	 * pour le cas d'une utilisation ultérieure
	 * @return	L'objet Kob-Eye
	 */
	private function getKEServer() {
		if(!isset($this->_KEServer)||!is_object($this->_KEServer)) {
			$Tab = Sys::$Modules["Parc"]->callData('Parc/Server/1', "", 0, 1);
			$this->_KEServer = genericClass::createInstance('Parc', $Tab[0]);
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
		// Parent Domain
		$Tab = Sys::$Modules["Parc"]->callData("Parc/Domain/Url=".$Parts[1][1], "", 0, 1);
		if(!empty($Tab)) {
			$obj = genericClass::createInstance('Parc', $Tab[0]);
			$this->AddParent($obj);
			// Parent Apache
			$fullDomain = ((strlen($this->Url)>2) ? substr($this->Url,2).'.' : '') . $obj->Url;
			$Exacts = Sys::$Modules["Parc"]->callData("Parc/Apache/ApacheServerName=$fullDomain", "", 0, 1);
			if(!empty($Exacts)) {
				$obj2 = genericClass::createInstance('Parc', $Exacts[0]);
				$this->AddParent($obj2);
			}
			else {
				$Approx = Sys::$Modules["Parc"]->callData("Parc/Apache/ApacheServerAlias~%$fullDomain%", "", 0, 100);
				if(!empty($Approx)) {
					foreach($Approx as $a) {
						$obj3 = genericClass::createInstance('Parc', $Approx[0]);
						$Candidats = explode("\r", $obj3->ApacheServerAlias);
						if(in_array($fullDomain, $Candidats)) $this->AddParent($obj3);
					}
				}
			}
		}
	}

	function checkIntegrity() {
		//recupération de la ligne ldap
		$KEDomain = $this->getKEDomain();
		$dn = 'cn='.$this->Url.',cn='.$KEDomain->Url.',ou=domains,'.PARC_LDAP_BASE;
		// Verification à jour
		$res = Server::checkTms($this);
		return $res['exists'];
	}

}