<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Form\Element;
/**
 *
 */
class AjouterCategorie extends Form
{
    public function __construct()
    {
        // Define form name
        parent::__construct('ajoutercategorie-form');
         
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        
        $this->addElements();
        $this->addInputFilter();
	}
	
	/**
	 * This method adds elements to form (input fields and submit button).
	 */
	protected function addElements()
	{
	    // Add "id_typo" field
	    $this->add([
	        'type'  => 'text',
	        'name' => 'id_typo',
	        'attributes' => [
	            'id' => 'id_typo',
	            'maxlength' => 3
	        ],
	        'options' => [
	            'label' => 'Identifiant',
	        ],
	    ]);
	    
	    // Add "typo_categorie" field
	    $this->add([
	        'type'  => 'text',
	        'name' => 'typo_categorie',
	        'attributes' => [
	            'id' => 'typo_categorie',
	            'maxlength' => 20
	        ],
	        'options' => [
	            'label' => 'Categorie',
	        ],
	    ]);
	    
	    // Add "id" field
	    $this->add([
	        'type'  => Element\Number::class,
	        'name' => 'id',
	        'attributes' => [
	            'id' => 'id',
	            'min' => '1',
	            'max' => '9',
	            'step' => '1', // default step interval is 1
	        ],
	        'options' => [
	            'label' => 'Numero',
	        ],
	    ]);
	    
	    // Add the submit button
	    $this->add([
	        'type'  => 'submit',
	        'name' => 'submit',
	        'attributes' => [
	            'value' => 'Ajouter',
	            'id' => 'submitbutton',
	        ],
	    ]);
	}
	
	/**
	 * This method creates input filter (used for form filtering/validation).
	 */
	private function addInputFilter()
	{
	    $inputFilter = new InputFilter();
	    $this->setInputFilter($inputFilter);
	    
	    $inputFilter->add([
                'name'     => 'id_typo',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'], 
                ],                
        ]);
	    
	    $inputFilter->add([
	        'name'     => 'typo_categorie',
	        'required' => true,
	        'filters'  => [
	            ['name' => 'StringTrim'],
	            ['name' => 'StripTags'],
	            ['name' => 'StripNewlines'],
	        ],
	    ]);
	    
	    $inputFilter->add([
	        'name'     => 'id',
	        'required' => true,
	        
	    ]);
	    
	    
	}
}
?>