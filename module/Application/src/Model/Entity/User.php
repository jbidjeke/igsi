<?php
    namespace Application\Model\Entity;
    
	class User
	{
	    protected $idUser;
	    protected $login;
	    protected $pass;
	    protected $passSalt;
	    protected $nom;
	    protected $prenom;
	    protected $nomCompose;
	    protected $mailUser;
	    protected $organisation;
	    protected $active;
	    protected $nomRole;
	    
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
   		
   		public function setIdUser($idUser)
   		{
   			$this->idUser = $idUser;
       		return $this;
   		}
   		
	    public function getIdUser()
	    {
	    	return $this->idUser;
	    }

	    public function setLogin($login)
	    {
	    	$this->login= $login;
       		return $this;
	    }
	    
	    public function getLogin()
	    {
	    	return $this->login;
	    }

	    public function setPass($pass)
	    {
	    	$this->pass = $pass;
       		return $this;
	    }
	    
	    public function getPass()
	    {
	    	return $this->pass;
	    }
	    
	    public function setPassSalt($passSalt)
	    {
	    	$this->passSalt = $passSalt;
       		return $this;
	    }
	    
	    public function getPassSalt()
	    {
	    	return $this->passSalt;
	    }
       	
	    public function setNom($nom)
	    {
	    	$this->nom = $nom;
       		return $this;
	    }
	    
	    public function getNom()
	    {
	    	return $this->nom;
	    }
       	
	    public function setPrenom($prenom)
	    {
	    	$this->prenom = $prenom;
       		return $this;
	    }
	    
	    public function getPrenom()
	    {
	    	return $this->prenom;
	    }
       	
	  	public function setNomCompose($nomCompose)
	    {
	    	$this->nomCompose = $nomCompose;
       		return $this;
	    }
	    
	    public function getNomCompose()
	    {
	    	return $this->nomCompose;
	    }
	    
	    public function setMailUser($mailUser)
	    {
	    	$this->mailUser = $mailUser;
       		return $this;
	    }
	    
	    public function getMailUser()
	    {
	    	return $this->mailUser;
	    }
	           	
	    public function setOrganisation($organisation)
	    {
	    	$this->organisation = $organisation;
       		return $this;
	    }
	    
	    public function getOrganisation()
	    {
	    	return $this->organisation;
	    }
       	
		public function setActive($active)
	    {
	    	$this->active = $active;
       		return $this;
	    }
	    
	    public function getActive()
	    {
	    	return $this->active;
	    }
	    
		public function setNomRole($nomRole)
	    {
	    	$this->nomRole = $nomRole;
       		return $this;
	    }
	    
	    public function getNomRole()
	    {
	    	return $this->nomRole;
	    }
	    
		/***********************************/
		/*     Génère un mot de passe      */
		/***********************************/
		// $size : longueur du mot passe voulue
		public function genere_password($size)
		{
			$password = "";
			
		    // Initialisation des caractères utilisables
		    $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "*", "&", "#", "$", "*", "@");
		    
		    for($i=0;$i<$size;$i++)
		    {
		        $password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
		    }		    
		    
		    return $password;
		}
		/*
		public function search($user, $term)
		{
			$filter = function($user_search) use ($term)
			{
				if (stristr($user_search, $term))
					return true;
				return false;
			};
			return array_filter($user, $filter);
		}
		*/
	}
?>