<?php
class Question extends genericClass{
    /**
     * getCategoriebloquante
     * Recherche dela categorie bloquante d'une question
     */
    function getCategorieBloquante() {
        $c = Sys::getOneData('Formation','Categorie/Question/'.$this->Id);
        if (is_object($c))
            return $c->getCategorieBloquante();
        else return false;
    }
    /**
     *
     */
    function getCategoryBreadcrumb() {
        $out = array();
        $c = Sys::getOneData('Formation','Categorie/Question/'.$this->Id);
        if (is_object($c))
           $out = array_merge($out,$c->getCategoryBreadcrumb());
        array_push($out,$this);
        return $out;
    }

    function Delete() {
        $questions = $this->getChildren('TypeQuestion');
        foreach ($questions as $q) $q->Delete();
        parent::Delete();
    }
}