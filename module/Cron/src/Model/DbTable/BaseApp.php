<?php
namespace Cron\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class BaseApp extends Base
{

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }
    
   
    
    public function checkBd($nombase)
    {
        try{
            $test_query = $this->fetchAllToArray("SELECT COUNT(*) FROM $nombase");
            if(count($test_query)==0)
            {
                return false;
            }
            else
            {
                return true;
            }
        }catch (\Exception $e){
            return false;
        }
    }
    
    
    
    public function insertEsmsApp($fichier)
    {
        $truncate_query = $this->doQuery("TRUNCATE TABLE esms_app");
        $insertion_query = $this->doQuery(addcslashes("LOAD DATA LOCAL INFILE '$fichier' INTO TABLE esms_app CHARACTER SET latin1 FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n' IGNORE 1 LINES", "\\"));
    }
    
  
    public function updateEsmsAppWeb()
    {
        $PLA_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('SVC0019','SVC0049','SVC0064','SVC0065','SVC0084')");
        $OR_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('SVC0046','SVC0096','SVC0133')");
        $ARG_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('SVC0026','SVC0036','SVC0060','SVC0061','SVC0087','SVC0099','SVC0118','SVC0126')");
        $BE_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('SVC0023','SVC0130')");
    
    }
    
    public function updateEsmsAppSpp()
    {
        $futur_buzz_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('APP2707','APP2708')");
        $appli_bol_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('APP1642','APP1980','APP1983')");
    
    }
    
    
    

}

