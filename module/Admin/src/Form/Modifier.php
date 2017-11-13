<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\View\Renderer\PhpRenderer;

class Modifier extends Form
{
    private $view;
    
    public function __construct(PhpRenderer $view)
    {
        // Define form name
        parent::__construct('modifier-form');
         
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->view = $view;
        $this->addElements();
        
    
    }
    
    protected function addElements()
    {
		
		$ticket = new Element\Text('ticket_ouvert');
		$ticket->setOptions(['label' => "N° de ticket ouvert"]);
		
		$this->add($ticket);
				
		
		// Select sur les motifs de modification d'un ticket cloturé			
		$motif = new Element\Select('motif');
		$motif->setOptions(['label' => 'Motif de la modification'])
			  ->setValueOptions(['' => 'Choisir', 'en-cours' => 'Impact toujours en cours', 'correction' => utf8_decode('Correction des criteres de l\'IG ')]);
	    $motif->setAttribute('onChange', 'setTimeout(function()
											    {
											    	document.forms["modifier"].action = "modifierTicket";
	    									  	  	document.forms["modifier"].submit()
    										  	},1000);');
		$this->add($motif);
		
		
		$cache = new Element\Hidden('hidden_cache');
		$cache->setValue(0);
			  
		$this->add($cache);	
		
		
		$ticketClos = new Element\Text('ticket_clos');
		$ticketClos->setOptions(['label' => "N° de ticket clos"]);
		$ticketClos->setAttribute('onChange',
											'if (document.getElementById("motif").value=="")
											{
												alert("Veuillez-saisir un motif de modification");
												this.value="";
    										}
    										else
    										{
    											setTimeout(function() 
											  	{
	    											if (document.getElementById("motif").value == "en-cours")
													{
														document.forms["modifier"].action = "modifierTicket";
	    												document.forms["modifier"].submit();
													}
	    											else
													{
														document.forms["modifier"].action = "cloturer";
		    											document.forms["modifier"].submit();
													}
    										  },1000);
    										};');

		$this->add($ticketClos);
		
		$hiddenControl = new Element\Hidden('path');
		$hiddenControl->setValue($this->view->basePath());
		$this->add($hiddenControl);
		
		

	}


}

