<?php
namespace Cron\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class EsmsApp extends BaseApp
{

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }
    
    public function splitIG()
    {
        $arrayIG = array();
        $ctr = 0;
        $query_ig = $this->fetchAllToArray("SELECT DISTINCT IG FROM esms_abo");
        foreach($query_ig as $igAchanger)
        {
            $arrayIG[$ctr] = $igAchanger['IG'];
            $igA = explode(" - ",$arrayIG[$ctr]);
            $igINI = $igA[0];
            $igFIN = $igA[1];
            if ((is_numeric($igINI))AND(is_numeric($igFIN)))
            {
                $update = $this->doQuery("UPDATE esms_abo
                    SET IG_INI='$igINI',
                    IG_FIN='$igFIN'
                    WHERE (IG='$arrayIG[$ctr]')");
            }
        }
    }
    
   /* public function checkBd($nombase)
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
    }*/
    
    function modifierAdresse()
    {
        $arrayAdresse = array();
        $cta = 0;
        $query_adr = $this->fetchAllToArray("SELECT EMAIL FROM esms_user WHERE (EMAIL LIKE '%serviceclient%') OR (EMAIL LIKE '%collectivites%')");
        foreach ($query_adr as $adrAchanger)
        {
            $arrayAdresse[$cta] = $adrAchanger['EMAIL'];
            $adrA = explode("@",$arrayAdresse[$cta]);
            $adrINI = $adrA[0];
            $adrFIN = $adrA[1];
            $adrRECOMP = $adrINI.'@sfr.com';
            $update = $this->doQuery("UPDATE esms_user
                SET EMAIL='$adrRECOMP'
                WHERE (EMAIL='$arrayAdresse[$cta]')");
        }
    }
    
    /*public function insertEsmsApp($fichier)
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
    
    }*/
    
    
    public function insertEsmsAbo($fichier)
    {
        $truncate_query = $this->doQuery("TRUNCATE TABLE esms_abo");
        $insertion_query = $this->doQuery("LOAD DATA LOCAL INFILE '$fichier' INTO TABLE esms_abo CHARACTER SET latin1  FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n' IGNORE 1 LINES");
    
    }
    
    public function insertEsmsAboList($fichier)
    {
        $truncate_query = $this->doQuery("TRUNCATE TABLE esms_abo_list");
        $insertion_query = $this->doQuery("LOAD DATA LOCAL INFILE '$fichier' INTO TABLE esms_abo_list CHARACTER SET latin1 FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n' IGNORE 1 LINES");
    
    }
    
    public function insertEsmsUser($fichier)
    {
        $truncate_query = $this->doQuery("TRUNCATE TABLE esms_user");
        $insertion_query = $this->doQuery("LOAD DATA LOCAL INFILE '$fichier' INTO TABLE esms_user CHARACTER SET latin1 FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n' IGNORE 1 LINES");
    
    }
    
    public function insertEsmsUserAbo($fichier)
    {
        $truncate_query = $this->doQuery("TRUNCATE TABLE esms_user_abo");
        $insertion_query = $this->doQuery("LOAD DATA LOCAL INFILE '$fichier' INTO TABLE esms_user_abo CHARACTER SET latin1 FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n' IGNORE 1 LINES");
    
    }
    
    public function insertEsmsUserAboList($fichier)
    {
        $truncate_query = $this->doQuery("TRUNCATE TABLE esms_user_abo_list");
        $insertion_query = $this->doQuery("LOAD DATA LOCAL INFILE '$fichier' INTO TABLE esms_user_abo_list CHARACTER SET latin1 FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n' IGNORE 1 LINES");
    
    }
    
    public function esmsIgsiHebdo($dateFirstDay, $dateLastDay){
        return $this->fetchAllToArray("SELECT vefd.prefix_ticket, vefd.id_ticket, vefd.nature_com,
            vefd.specifique_com, vefd.date_envoi_sms, vefd.domaine, vefd.ref_appli, vefd.nom_appli,
            vefd.ig, vefd.date_debut_inc, vefd.date_fin_inc, vefd.description
            FROM view_esms_si_dashboard AS vefd
            WHERE (vefd.date_debut_inc >= '$dateFirstDay') AND (vefd.date_debut_inc <= '$dateLastDay')
            ORDER BY vefd.nom_appli ASC");
    }
    
    
    public function esmsIgsiMensuel($dateFirstDay, $dateLastDay){
        return $this->fetchAllToArray("SELECT vefd.prefix_ticket, vefd.id_ticket, vefd.nature_com,
            vefd.specifique_com, vefd.date_envoi_sms, vefd.domaine, vefd.ref_appli, vefd.nom_appli,
            vefd.ig, vefd.date_debut_inc, vefd.date_fin_inc, vefd.description
            FROM view_esms_si_dashboard AS vefd
            WHERE (vefd.date_debut_inc >= '$dateFirstDay') AND (vefd.date_debut_inc <= '$dateLastDay')
            ORDER BY vefd.nom_appli ASC");
    }
    
    
    
    public function extractEsmsEcHebdo($date_debut, $date_fin){
    
        return $this->fetchAllToArray("SELECT vefd.prefix_ticket,
									 vefd.id_ticket,
									 vefd.nature_com,
									 vefd.specifique_com,
									 vefd.date_envoi_sms,
									 vefd.domaine,
									 vefd.ref_appli,
									 vefd.nom_appli,
									 vefd.ig,
									 vefd.date_debut_inc,
									 vefd.date_fin_inc,
									 vefd.description
							  FROM view_esms_si_dashboard AS vefd
							  WHERE vefd.date_debut_inc >= '".$date_debut."'
							  AND vefd.date_debut_inc <= '".$date_fin."'
							  ORDER BY vefd.nom_appli ASC");
    }
    
    public function extractEsmsHebdo($date_debut, $date_fin){
    
    
        return $this->fetchAllToArray("SELECT vefd.prefix_ticket, 
									 vefd.id_ticket, 
									 vefd.nature_com,
									 vefd.specifique_com, 
									 vefd.date_envoi_sms, 
									 vefd.domaine, 
									 vefd.ref_appli, 
									 vefd.nom_appli, 
									 vefd.ig, 
									 vefd.date_debut_inc, 
									 vefd.date_fin_inc, 
									 vefd.description 
							  FROM view_esms_si_dashboard AS vefd
							  WHERE vefd.date_debut_inc >= '".$date_debut."' 
							  AND vefd.date_debut_inc <= '".$date_fin."' 
							  ORDER BY vefd.nom_appli ASC");
    }
    
    
    public function esmsEnCours($date_debut, $date_fin){
        return $this->fetchAllToArray("SELECT vefd.prefix_ticket, 
									 vefd.id_ticket, 
									 vefd.nature_com,
									 vefd.specifique_com, 
									 vefd.date_envoi_sms, 
									 vefd.domaine, 
									 vefd.ref_appli, 
									 vefd.nom_appli, 
									 vefd.ig, 
									 vefd.date_debut_inc, 
									 vefd.date_fin_inc, 
									 vefd.description 
							  FROM view_esms_si_dashboard AS vefd
							  WHERE vefd.date_debut_inc >= '".$date_debut."' 
							  AND vefd.date_debut_inc <= '".$date_fin."' 
							  ORDER BY vefd.nom_appli ASC");
    }
    
    public function suppressionHisto($serv)
    {
        $nom_histo = 'esms_histo_';
        $nom_sms = 'sms_com_';
        $fromDate = date("Y-m-d 00:00:00", strtotime("-1 months"));
    
        if($serv=='noc') //traitement spécifique pour le NOC
            $requete = "delete FROM esms_histo_noc WHERE id_com_sms in (select id_com_sms from sms_com_reseau where date_envoi_sms < '".$fromDate."')";
        else
            $requete = "delete FROM $nom_histo$serv WHERE id_com_sms in (select id_com_sms from $nom_sms$serv where date_envoi_sms < '".$fromDate."')";
    
        $query_adr1 = $this->doQuery($requete);
    
                //echo $requete.'\n';
    }
    
    

}

