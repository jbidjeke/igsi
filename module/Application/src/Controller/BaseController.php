<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class BaseController extends AbstractActionController
{

    protected $log, $histo, $actionName, $controllerName, $container, $baseUrl, $extractCsvHebdo;

    protected function logApplication($type_action, $insertion_bdd, $description)
    {
        
        /**
         * ****** LOG APPLICATIF *******
         */
        $this->log->logUser($this->container->id_user, $this->container->remoteAddr, $this->controllerName, $this->actionName, $type_action, $insertion_bdd, $description);
        unset($log_applicatif);
    /**
     * **** FIN LOG APPLICATIF *****
     */
    }

    protected function historisation($id_ticket, $type_modif)
    {
        $tab = [
            'id_ticket' => $id_ticket,
            'date_modification' => date('Y-m-d H:i:s'),
            'nom_admin' => $this->container->username,
            'type_modif' => $type_modif
        ];
        
        $this->histo->insert($tab);
    }
    
    // pour le téléchargement
    public function download($file)
    {
        return [
            'fichier_created_csv' => $this->extractCsvHebdo->download_file($file)
        ];
    }

    protected function messagelog($etat)
    {
        $action = "";
        if ($etat == 51)
            $action = "Soumission modification";
        
        if ($etat == 11)
            $action = "Soumission création";
        
        elseif (in_array($etat, [
            21,
            31,
            41
        ]))
            $action = "Soumission";
        elseif (in_array($etat, [
            10,
            20,
            30,
            40,
            50
        ]))
            $action = "Annulation";
        elseif (in_array($etat, [
            12,
            22,
            32,
            42,
            52
        ]))
            $action = "Sauvegarde";
            
            // modification
        return $action;
    }
}
