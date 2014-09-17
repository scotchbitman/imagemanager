<?php
	/**
	 *	Classe permettant de gérer les images lors d'uploads.
	 *	Gestion des thumbnails (miniatures)... etc.
	 *
	 *	@author Alexandre BLIEUX
	 */
	class ImageManager{
		
		protected $rsrc, $config = array(), $width, $height, $type, $mime;
		protected $bkg, $img, $thumb;
		/**
		 *	CONSTRUCTEUR
		 *
		 *	@params
		 *		$rsrc (Mixed): 
		 *			Représente une image ou une collection d'images (Array)
		 *
		 *		$config (Array): 
		 *			Tableau regroupant les paramètres personnalisables en
		 *			fonction des besoins. Une configuration par défaut est
		 *			en place et n'est modifiée qu'en fonction des nouveaux
		 *			paramètres entrés.
		 *
		 *	@since version 0.1
		 */
		public function __construct($rsrc,$config = NULL){
			// Valeurs par defaut
			$this->config = array(
				'maxWidth' => 90,
				'ratio' => NULL,
				'path' => 'thumbnails/'
			);
			
			// Ajout des valeur entrées en config;
			if($config) $this->config = $config + $this->config;
			$this->rsrc = $rsrc;
			$img = getimagesize($rsrc);
			list($this->width,$this->height,$this->type) = $img;
			$this->mime = $img['mime'];
			
			$this->img = new Canvas($this->width,$this->height);
			
			if($this->config['ratio'] == NULL) 
			$this->config['ratio'] = round($this->img->getRatio());
			
			$this->bkg = new Image(
				$this->config['maxWidth'],
				round($this->config['maxWidth'] / $this->config['ratio'])
			);
		}
		
		/**
		 *	@return (String) Retourne la dernière partie de l'URL
		 *					 du fichier, c'est à dire son nom.
		 */
		private function getNewNameOfUploadedFile(){
			$parts = explode('/',$this->rsrc);
			return $parts[count($parts) - 1];
		}
		
		public function getWidth(){return $this->width;}
		public function getHeight(){return $this->height;}
		public function getType(){return $this->type;}
		
		/**
		 *	Méthode permattant de redimentionner une image suivant un ratio
		 *	(largeur ou hauteur maxi) et d'enregistrer la miniature générée.
		 */
		public function makeThumb(){
			$maxWidth = $this->config['maxWidth'];
			
			if (($this->width < $maxWidth) && ($this->height < $maxWidth))
			throw new ImageManagerErrorException(
				'L\'image choisie est trop petite.'
			);
			
			$this->thumb = $this->img->reduce($this->bkg);
			$coord = $this->thumb->getPosition($this->bkg);
			
			$path = $this->config['path'];
			
			// DIR
			if (!is_dir($path)) mkdir($path, 0777, true);

			## Case JPEG
			if($this->mime == "image/jpeg"){									
				$new_pict = imagecreatefromjpeg($this->rsrc);
				
				$thumbnail = imagecreatetruecolor(
					$this->bkg->getWidth(), 
					$this->bkg->getHeight()
				);
				
				$back = imagecolorallocate($thumbnail, 255, 255, 255);
				
				imagefilledrectangle(
					$thumbnail, 
					0, 0, 
					$this->bkg->getWidth(), 
					$this->bkg->getHeight(), 
					$back
				);
				
				imagecopyresampled(
					$thumbnail, 
					$new_pict, 
					$coord['x'], 
					$coord['y'], 
					0, 0, 
					$this->thumb->getWidth(), 
					$this->thumb->getHeight(), 
					$this->width, 
					$this->height
				);
				// enregistrement thumbnail dans repertoire thumb
				imagejpeg($thumbnail,$path.self::getNewNameOfUploadedFile());
				imagedestroy($thumbnail);
			}					
			## Case GIF
			if($this->mime == "image/gif"){	
				$new_pict = imagecreatefromgif($this->rsrc);		
				$thumbnail = imagecreatetruecolor(
					$this->bkg->getWidth(), 
					$this->bkg->getHeight()
				);
				# Si image GIF transparente
				if (imagecolortransparent($new_pict) >= 0) {
					$color = imagecolorallocate($thumbnail, 255, 255, 255);
					imagefill($thumbnail, 0, 0, $color);
					imagecolortransparent($thumbnail, $color);						
				} # -- end
				imagecopyresampled(
					$thumbnail, 
					$new_pict, 
					$coord['x'], 
					$coord['y'], 
					0, 0, 
					$this->thumb->getWidth(), 
					$this->thumb->getHeight(), 
					$this->width, 
					$this->height
				);
				// enregistrement thumbnail dans repertoire thumb
				imagegif($thumbnail,$path.self::getNewNameOfUploadedFile());
				imagedestroy($thumbnail);
			}
			## Case PNG
			if($this->mime == "image/png"){	
				$new_pict = imagecreatefrompng($this->rsrc);
					
				$thumbnail = imagecreatetruecolor(
					$this->bkg->getWidth(), 
					$this->bkg->getHeight()
				);	
				# Gestion de la transparence
				imagealphablending($thumbnail, false);
				imagesavealpha($thumbnail, true);
				
				$trans_colour = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
    			imagefill($thumbnail, 0, 0, $trans_colour);
					
				# -- end				
				imagecopyresampled(
					$thumbnail, 
					$new_pict, 
					$coord['x'], 
					$coord['y'], 
					0, 0, 
					$this->thumb->getWidth(), 
					$this->thumb->getHeight(), 
					$this->width, 
					$this->height
				);
				// enregistrement thumbnail dans repertoire thumb
				imagepng($thumbnail,$path.self::getNewNameOfUploadedFile());
				imagedestroy($thumbnail);
			}
		}
	}
	
	
	/**
	 *	TODO
	 *
	 *	Classe d'exception générale relative à la classe ImageManager.
	 */
	class ImageManagerErrorException extends Exception{}
?>