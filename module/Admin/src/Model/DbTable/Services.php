<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class Services extends Base {

    protected $table = 'esms_app';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function findBySI($SI = null) {
        
        if ($SI == null){
            return $this->getAdapter()->fetchAll('SELECT NOM,typo_categorie
                                                                    FROM esms_app s, categorie_services cs
                                                                    WHERE NIV_SVC_ID=cs.id AND SI in ("SI") ORDER BY NOM ASC
                                                                    '
            );
        
        }else
            return $this->getAdapter()->fetchAll('SELECT NOM,typo_categorie
								FROM services s, categorie_services cs
								WHERE NIV_SVC_ID=cs.id AND SI="'.$SI.'"
								ORDER BY NOM
								'
								);       
    }

    public function findCategory($cat) {
        if (strpos($cat, "''") === false)
            $cat = str_replace("'", "''", $cat);
        return $this->getAdapter()->fetchAll("SELECT typo_categorie
								FROM esms_app s, categorie_services cs
								WHERE NIV_SVC_ID=cs.id AND SI='SI' AND NOM LIKE '" . $cat . "%' 
								limit 0,1; 
								"
        );
    }

    public function findCategoryByRef($cat) {
        if (strpos($cat, "''") === false)
            $cat = str_replace("'", "''", $cat);
        return $this->getAdapter()->fetchAll("SELECT typo_categorie
								FROM esms_app s, categorie_services cs
								WHERE NIV_SVC_ID=cs.id AND SI='SI' AND REF LIKE '" . $cat . "%' 
								limit 0,1; 
								"
        );
    }

    public function gerRefSi($nom) {
        /* if (strpos($nom, "''") === false)
          $nom = str_replace("'", "''", $nom); */
        $nom = addslashes($nom);
        $sql = "SELECT REF, SI FROM esms_app  WHERE  NOM = '" . $nom . "'";
        return $this->getAdapter()->fetchAllToArray($sql);
    }

    public function verifService($ser) {
        /* if (strpos($ser, "''") === false)
          $ser = str_replace("'", "''", $ser); */
        $ser = addslashes($ser);
        return $this->getAdapter()->fetchAll("SELECT typo_categorie
								FROM esms_app 
								WHERE NOM = '" . $ser . "' 
								"
        );
    }

    public function getS($ser) {
        /* if (strpos($ser, "''") === false)
          $ser = str_replace("'", "''", $ser); */
        $ser = addslashes($ser);
        return $this->getAdapter()->fetchAll("SELECT NOM
								FROM esms_app 
								WHERE NOM = '" . $ser . "' 
								"
        );
    }

    public function findList($list) {
        /* if (strpos($list, "''") === false)
          $list = str_replace("'", "''", $list); */
        $list = addslashes($list);
        return $this->getAdapter()->fetchAll("SELECT NOM,typo_categorie,SI
								FROM esms_app s, categorie_services cs
								WHERE NIV_SVC_ID=cs.id AND SI in ('SI')AND LOWER(NOM) LIKE '" . $list . "%' ORDER BY NOM
								"
        );
    }

    public function findtestList() {
        return $this->getAdapter()->fetchAll("SELECT NOM,typo_categorie,SI
								FROM esms_app s, categorie_services cs
								WHERE NIV_SVC_ID=cs.id AND SI in ('SI') ORDER BY NOM
								"
        );
    }

    public function findApplication($list) {
        /* if (strpos($list, "''") === false)
          $list = str_replace("'", "''", $list); */
        $list = addslashes($list);
        return $this->getAdapter()->fetchAll("SELECT NOM,typo_categorie,SI
								FROM esms_app s, categorie_services cs
								WHERE NIV_SVC_ID=cs.id AND SI in ('SI')AND NOM LIKE '" . $list . "' ORDER BY NOM
								"
        );
    }

    public function findByCat($id_typo) {
        return $this->getAdapter()->fetchAll('SELECT NOM,typo_categorie
								FROM esms_app s, categorie_services cs
								WHERE NIV_SVC_ID=cs.id AND id_typo="' . $id_typo . '" AND SI IN ("SI") 
								ORDER BY NOM
								'
        );
    }

    public function findByC($id_typo) {
        return $this->getAdapter()->fetchAll('SELECT NOM as name,NOM as id
								FROM esms_app s, categorie_services cs
								WHERE NIV_SVC_ID=cs.id AND id_typo="' . $id_typo . '" AND SI IN ("SI") 
								ORDER BY NOM
								'
        );
    }

    public function complete($q) {
        if (strpos($list, "''") === false)
            $list = str_replace("'", "''", $list);
        $select = $this->_db->select();
        $select->from($this->_name);
        $select->where('NOM = ?', $q);
        $result = $select->query()->fetchAll();
        return $result;
    }

    public function findByCatWeb($id_typo) {
        return $this->getAdapter()->fetchAll('SELECT NOM,typo_categorie
								FROM esms_app s, categorie_services cs
								WHERE NIV_SVC_ID=cs.id AND id_typo="' . $id_typo . '" AND SI IN ("SI") and REF like "SVC%"
								ORDER BY NOM
								'
        );
    }

}
