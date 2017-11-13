<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class Ponderations extends Base {

    protected $table = 'ponderations';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function selectVal() {

        return $this->fetchAll('SELECT * 
								FROM ponderations p,categorie_services cs 
								WHERE p.id_typo=cs.id_typo
								ORDER BY id,id_duree="T0_24H",id_duree="T0_12H",id_duree="T0_8H",id_duree="T0_4H",id_duree="T0_2H",id_duree="T0_1H",id_duree="T0",id_etat', null);
    }

}
