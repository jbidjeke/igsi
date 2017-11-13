<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Aui\Service\Authentication;

use Zend\Authentication\Result;

class LdapResult extends Result
{

    /**
     * ldap_entries
     *
     * @var array
     */
    protected $ldap_entries;

    /**
     * Sets the result code, identity, and failure messages
     *
     * @param  int     $code
     * @param  mixed   $identity
     * @param  array   $messages
     * @param  array   $ldap_entries
     */
    public function __construct($code, $identity, array $messages = [], $ldap_entries)
    {
        $this->ldap_entries = $ldap_entries;
        parent::__construct($code, $identity, $messages);
    }


    /**
     * getLdap_entries() - Get the ldap_entries
     *
     * @return array
     */
    public function getLdap_entries()
    {
        return $this->ldap_entries;
    }

    
}
