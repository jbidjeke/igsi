<?php

namespace Admin\Model\DbTable;

use Application\Model\DbTable\Base;

class AssetQuery extends Base {

    protected $table = 'asset_query';

    public function __construct($db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function find() {
        return $this->fetchAll(
                        $this->select()
        );
    }

}
