<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class NumServicesImpactes extends Base {

    protected $table = 'num_services_impactes';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function find() {
        return $this->fetchAll(
                        $this->select()
        );
    }

    public function extract_ponderation($nb_service, $visibilite, $busy_hour) {
        $result = $this->getAdapter()->fetchAll(
                "SELECT num_services_impactes.impact_pond, visibilite_impact.event_pond, busy_hour.periode_pond 
			FROM num_services_impactes,visibilite_impact,busy_hour 
			WHERE num_services_impactes.impact='$nb_service' AND visibilite_impact.event='$visibilite' AND busy_hour.periode='$busy_hour'");

        return $result;
    }

}
