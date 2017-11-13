<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Form\Element;

class Pilote extends Form
{
    public function __construct()
    {
        // Define form name
        parent::__construct('pilote-form');
         
        // Set POST method for this form
        $this->setAttribute('method', 'post');
    
        $this->addElements();
        $this->addInputFilter();
    }
    
    protected function addElements()
    {
		$ticket = new Element\Text('id_ticket');
		$ticket->setOptions(['label' =>'No. ticket'])
				->setAttribute('readonly', true);
		$this->add($ticket);
		
		$pilote = new Element\Text('pilote');
		$pilote->setOptions(['label' =>'Nouveau nom de pilote']);
		$this->add($pilote);

		$enregistrer = new Element\Submit('Enregistrer');
		$enregistrer->setValue('Enregistrer');
		$this->add($enregistrer);

	}
	
	/**
	 * This method creates input filter (used for form filtering/validation).
	 */
	private function addInputFilter()
	{
	    $inputFilter = new InputFilter();
	    $this->setInputFilter($inputFilter);
	     
	    $inputFilter->add([
	        'name'     => 'id_ticket',
	        'required' => true,
	        'filters'  => [
	            ['name' => 'StringTrim'],
	            ['name' => 'StripTags'],
	        ],
	    ]);
	     
	    $inputFilter->add([
	        'name'     => 'pilote',
	        'required' => true,
	        'filters'  => [
	            ['name' => 'StringTrim'],
	            ['name' => 'StripTags'],
	        ],
	    ]);
	     

	     
	     
	}
}
?>