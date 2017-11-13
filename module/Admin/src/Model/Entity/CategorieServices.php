<?php
    namespace Admin\Model\Entity;
    
	class CategorieServices
	{
    	public $id_typo;
        public $typo_categorie;
        public $id;
        
        public function __construct(array $options = null)
        {
            if (is_array($options))
                $this->setOptions($options);
        }
        
        public function setOptions(array $options)
        {
            $methods = get_class_methods($this);
            foreach ($options as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (in_array($method, $methods)) {
                    $this->$method($value);
                }
            }
            return $this;
        }
        
        public function exchangeArray($data)
        {
            $this->id     = isset($data['id']) ? $data['id'] : null;
            $this->typo_categorie = isset($data['typo_categorie']) ? $data['typo_categorie'] : null;
            $this->id_typo  = isset($data['id_typo']) ? $data['id_typo'] : null;
        }
    
        
        public function getArrayCopy()
        {
            return [
                'id'     => $this->id,
                'typo_categorie' => $this->typo_categorie,
                'id_typo'  => $this->id_typo,
            ];
        }
	    
		
   		
   		
	}
?>