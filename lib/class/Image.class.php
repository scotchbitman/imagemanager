<?php
	/**
	 *	Classe Image.
	 *
	 *	la classe Image est la classe de base décrivant simplement une image au
	 *	travers de ses dimensions.
	 */
	class Image{
		protected $width, $height;
		
		public function __construct($w,$h){
			$this->width  = $w;
			$this->height = $h;
		}
		
		/**
		 *	Renvoie le ratio d'une image.
		 *
		 *	@return Float
		 */
		public function getRatio(){
			return $this->width / $this->height;
		}
		
		/**
		 *	Renvoie l'abscisse et l'ordonnée du coin supérieur gauche de l'image
		 *	calculé de manière à ce que cette image soit centrée par rapport aux
		 *	dimensions du background.
		 *
		 *	@param background : (Image) représente l'image de fond.
		 *
		 *	@return array()
		 *		x : la nouvelle abscisse de l'image par rapport au background
		 *		y : la nouvelle ordonnée de l'image par rapport au background
		 */
		public function getPosition($background){
			$pos = array();
			
			if(self::getOrientation() == 'landscape'){
				$pos['x'] = 0;
				$pos['y'] = ($background->getHeight() / 2) - ($this->height / 2);
			}
			
			if(self::getOrientation() == 'portrait' 
			|| self::getOrientation() == NULL){
				$pos['x'] = ($background->getWidth() / 2) - ($this->width / 2);
				$pos['y'] = 0;
			}
			
			return $pos;
		}
		
		/** 
		 *	Renvoie l'orientation d'une image en fonction de ses dimensions, 
		 *	renvoie NULL si les dimensions sont égales, c'est à dire si l'image 
		 *	est un carré.
		 */
		public function getOrientation(){
			if($this->width > $this->height) return  'landscape';
			else if($this->width < $this->height) return 'portrait';
			else return NULL;
		}
		
		public function getWidth(){return $this->width;}
		public function getHeight(){return $this->height;}
	}
?>