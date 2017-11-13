<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\View\Renderer\PhpRenderer;

/**
 * This form is used to collect user's login, password and 'Remember Me' flag.
 */
class LoginForm extends Form
{

    /**
     * User manager.
     *
     * @var Zend\View\Renderer\PhpRenderer
     */
    private $view;

    /**
     * Constructor.
     */
    public function __construct(PhpRenderer $view)
    {
        $this->view = $view;
        // Define form name
        parent::__construct('login-form');
        
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        
        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    public function addElements()
    {
        // Add "email" field
        $this->add([
            'type' => 'hidden',
            'name' => 'step',
            'attributes' => [
                'value' => 0
            ]
        ]);
        
        // Add "email" field
        $this->add([
            'type' => 'text',
            'name' => 'full_name',
            'options' => [
                'label' => 'Nom d\'utilisateur'
            ]
        ]);
        
        // Add "password" field
        $this->add([
            'type' => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Mot de passe'
            ]
        ]);
        
        $this->add([
            'type' => 'select',
            'name' => 'role',
            'attributes' => [
                'id' => 'role'
            ],
            'options' => [
                'label' => 'Rôle',
                'empty_option' => '-- Choisir un rôle --',
                'value_options' => [
                    'Administrateur' => 'Administrateur',
                    'Pilote' => 'Pilote',
                    'Ls' => 'Ls'
                ]
            ]
        ]);
        
        // Add "remember_me" field
        $this->add([
            'type' => 'checkbox',
            'name' => 'check_me',
            'options' => [
                
                'label' => 'Me proposer un rôle'
            ]
        ]);
        
        // Add "remember_me" field
        $this->add([
            'type' => 'checkbox',
            'name' => 'remember_me',
            'options' => [
                'label' => 'Se souvenir de moi'
            ]
        ]);
        
        // Add hidden "path" field
        $this->add([
            'type' => 'hidden',
            'name' => 'path',
            'attributes' => [
                'value' => $this->view->baseUrl(),
                'id' => 'path'
            ]
        ]);
        
        // Add "redirect_url" field
        $this->add([
            'type' => 'hidden',
            'name' => 'redirect_url'
        ]);
        
        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ]
        ]);
        
        // Add the Submit button
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Sign in',
                'id' => 'submit'
            ]
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);
        
        // Add input for "email" field
        
        $inputFilter->add([
            'name' => 'email',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ]
            ],
            'validators' => [
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false
                    ]
                ]
            ]
        ]);
        
        // Add input for "password" field
        $inputFilter->add([
            'name' => 'password',
            'required' => true,
            'filters' => [],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 6,
                        'max' => 64
                    ]
                ]
            ]
        ]);
        
        // Add input for "remember_me" field
        $inputFilter->add([
            'name' => 'remember_me',
            'required' => false,
            'filters' => [],
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => [
                            0,
                            1
                        ]
                    ]
                ]
            ]
        ]);
        
        // Add input for "redirect_url" field
        $inputFilter->add([
            'name' => 'redirect_url',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ]
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 0,
                        'max' => 2048
                    ]
                ]
            ]
        ]);
    }
}

