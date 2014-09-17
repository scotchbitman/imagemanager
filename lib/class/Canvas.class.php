<?php
	require 'Image.class.php';
	/**
	 *	Classe Canvas.
	 */
	class Canvas extends Image{
	
		protected $path, $type, $mime;
	
		public function __construct($path){
			$img = getimagesize($path);
			list($width,$height,$type) = $img;
			parent::__construct($width,$height);
			$this->path = $path;
			$this->type = $type;
			$this->mime = $img['mime'];
		}
		
		public function getPath(){return $this->path;}
		public function getType(){return $this->type;}
		public function getMime(){return $this->mime;}
	
		/**
		 *	Permet de réduire la taille de la trame en fonction des dimensions
		 *	du fond passé en paramètre.
		 *
		 *	@param background : (Image) Image de fond.
		 *	@return Object : (Image) 
		 *		Nouvel objet Image aux dimensions réduites.
		 */
		public function reduce($background){
		
			$ratio = self::getRatio();
		
			if(self::getOrientation() == 'landscape'){
				$width = $background->getWidth();
				$height= round($background->getWidth() / $ratio);
			}
			
			/*	Si getOrientation() renvoie NULL l'image est un carré. 
			 *	La miniature étant toujours présentée en mode paysage, les 
			 *	nouvelles dimensions de l'image seront alors générées en se 
			 *	basant sur la hauteur maxi du fond, comme une image en mode 
			 *	portrait.
			 */
			if(self::getOrientation() == 'portrait' 
			|| self::getOrientation() == NULL){
				$width = round($background->getHeight() / (1 / $ratio));
				$height= $background->getHeight();
			}
			
			return new Image($width,$height);
		}
	}
?>