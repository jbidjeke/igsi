<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\View\Renderer\PhpRenderer;

class Dashboard extends Form
{
    private $view, $taille_text;
    
    public function __construct(PhpRenderer $view)
    {
        // Define form name
        parent::__construct('dashboard');
         
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->view = $view;
        $this->taille_text = '35%';
    }

}

