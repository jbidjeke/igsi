<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Aui\Service;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Aui\Service\Authentication\LdapResult;
use User\Entity\User;
use User\Service\UserManager;

/**
 * Description of LdapLogin
 *
 * @author u160009
 */
class LdapLogin implements AdapterInterface {

    /**
     * @var Type
     */
    protected $domain_controllers;
    protected $ldap_base_dn;
    protected $ldap_base_rdn;
    protected $admin_password;
    protected $filter;
    protected $ldap_connexion;
    protected $ldap_connt_status;
    protected $config, $username, $password;
    protected $user_dn;
    protected $user_ldap_connt_status;

    /**
     * User manager.
     * @var User\Service\UserManager
     */
    private $userManager;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($config, UserManager $userManager) {

        $this->userManager = $userManager;
        $this->config = $config['ldap'];
        $this->ldap_connexion = ldap_connect($this->config['domain_controllers']);
        ldap_set_option($this->ldap_connexion, LDAP_OPT_PROTOCOL_VERSION, 3);

        $this->ldap_connt_status = @ldap_bind(
                        $this->ldap_connexion, $this->config['ldap_base_dn'], $this->config['admin_password']
        );
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *     If authentication cannot be performed
     */
    public function authenticate() {

        $this->filter = "(|(uid=$this->username*))";

        try {
            $ldap_query = ldap_search($this->ldap_connexion, $this->config['ldap_base_rdn'], $this->filter)
                    or exit(">>Could not connect to LDAP server<<");
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $ldap_get_entries = ldap_get_entries($this->ldap_connexion, $ldap_query);

        $ldap_first_entry = ldap_first_entry($this->ldap_connexion, $ldap_query);
        $ldap_entries = ldap_get_entries($this->ldap_connexion, $ldap_query);
        //var_dump($ldap_entries[0]['mail'][0]);
        
        if ('resource' === gettype($ldap_first_entry)) {
            $this->user_dn = ldap_get_dn($this->ldap_connexion, $ldap_first_entry);
            $this->user_ldap_connt_status = @ldap_bind($this->ldap_connexion, $this->user_dn, $this->password);
        }

        if ($this->user_ldap_connt_status === TRUE) {
            /*             * Cas de non soumission d'un utilisateur AUI** */
            $user = $this->userManager->getUserExistsByFullNameWithStatus($this->username, User::STATUS_ACTIVE);
            if ($user != null)  // Récuperer le user
                return new LdapResult(LdapResult::SUCCESS, $this->username, ['Authenticated successfully'], $ldap_entries);
            else {   // Considérer le user comme un visiteur 
                $user = $this->userManager->getUserExistsByFullNameWithStatus($this->username, User::STATUS_DRAFT);
                if ($user != null)
                    return new LdapResult(LdapResult::FAILURE_UNCATEGORIZED, $this->username, ['Authenticated successfully'], $ldap_entries);
                else
                    return new LdapResult(LdapResult::FAILURE_IDENTITY_NOT_FOUND, $this->username, ['Authenticated successfully'], $ldap_entries);
            }
        } else
            return new LdapResult(LdapResult::FAILURE, null, ['Authenticated failure'], $ldap_entries);
    }

}
