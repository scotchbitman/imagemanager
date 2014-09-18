<?php
	/**
	 *	Classe ImageManager
	 *
	 *	Cette classe s'appuie essentiellement sur les fonctions de traitement
	 *	des images de la librairie GD fournie en standard avec PHP, en cas de
	 *	dysfonctionnement il peut être intéressant de vérifier que cette
	 *	librairie est bien active.
	 *
	 *	@see phpinfo()
	 *	@param config : 
	 *		(Array) Tableau de paramètres permettant de configurer l'instance
	 *		de classe.
	 *
	 *	@Author Alexandre BLIEUX
	 *	@version 0.1
	 */
	class ImageManager{

		protected $config = array();

		public function __construct($config = NULL){
			$this->config = array(
				'path' => 'thumbnails/',
				'ratio' => NULL,
				'maxWidth' => 90,
				'landscapeThumb' => true
			);
			
			if($config) $this->config = $config + $this->config;
		}

		/**
		 *	Permet de créer une image de fond vide afin d'y appliquer la
		 *	miniature générée.
		 *
		 *	@param img : (Canvas)
		 *		Objet de type Canvas
		 *
		 *	@return Object : (Image)
		 */
		private function getBackground($img){
		
			$ratio = (!$this->config['ratio']) ? 
			$img->getRatio() : $this->config['ratio'];
			
			if(!$this->config['landscapeThumb'] && 
			$img->getOrientation() == 'portrait') $ratio = 1 / $ratio;
			
			$width = $this->config['maxWidth'];
			$height= $this->config['maxWidth'] / $ratio;
			
			return new Image($width,$height);
		}
		
		/**
		 *	Permet de générer une miniature.
		 *
		 *	Suivant le type d'image sur laquelle on travaille, des méthodes
		 *	spécifiques seront appelées.
		 *
		 *	@param img : (Canvas)
		 *		Objet de type Canvas.
		 */
		public function makeThumb($img){
			$bkg = self::getBackground($img);
			$thumb = $img->reduce($bkg);
			$coord = $thumb->getPosition($bkg);
			
			$path = $this->config['path'];
			if (!is_dir($path)) mkdir($path, 0777, true);

			$imct  = imagecreatetruecolor($bkg->getWidth(),$bkg->getHeight());
			
			switch($img->getMime()){
				case 'image/jpeg': 
					self::createJPEG($img,$imct,$bkg,$thumb,$coord,$path);
					break;
				case 'image/gif': 
					self::createGIF($img,$imct,$thumb,$coord,$path); 
					break;
				case 'image/png': 
					self::createPNG($img,$imct,$thumb,$coord,$path);
					break;
			}
			
			imagedestroy($imct);
		}
		
		/**
		 *	Méthode de création d'image JPEG
		 *
		 *	@params
		 *		img : (Image) Objet image à manipuler.
		 *		imct:(Resource) Renvoyée par la méthode imagecreatetruecolor()
		 *		bkg : (Image) Objet image vide sur lequel on va appliquer la
		 *			miniature.
		 *		thumb:(Image) Objet image réduit.
		 *		coord:(Array)
		 *			x : abscisse du coin supérieur gauche de la miniature.
		 *			y: ordonnée du coin supérieur gauche de la miniature.
		 *		path:(String) Chemin de sauvegarde de la mininature.
		 */
		private static function createJPEG($img,$imct,$bkg,$thumb,$coord,$path){
			$imc = imagecreatefromjpeg($img->getPath());
			$back = imagecolorallocate($imct, 255, 255, 255);
			imagefilledrectangle(
				$imct, 0, 0, 
				$bkg->getWidth(), $bkg->getHeight(), 
				$back
			);
			
			imagecopyresampled(
				$imct, $imc, $coord['x'], $coord['y'], 0, 0,
				$thumb->getWidth(), $thumb->getHeight(),
				$img->getWidth(), $img->getHeight()
			);
			
			imagejpeg($imct,$path.$img->getName());
		}
		
		/**
		 *	Méthode de création d'image GIF
		 *
		 *	@params
		 *		img : (Image) Objet image à manipuler.
		 *		imct:(Resource) Renvoyée par la méthode imagecreatetruecolor()
		 *		thumb:(Image) Objet image réduit.
		 *		coord:(Array)
		 *			x : abscisse du coin supérieur gauche de la miniature.
		 *			y: ordonnée du coin supérieur gauche de la miniature.
		 *		path:(String) Chemin de sauvegarde de la mininature.
		 */
		private static function createGIF($img,$imct,$thumb,$coord,$path){
			$imc = imagecreatefromgif($img->getPath());
			
			if (imagecolortransparent($imc) >= 0) {
				$color = imagecolorallocate($imct, 255, 255, 255);
				imagefill($imct, 0, 0, $color);
				imagecolortransparent($imct, $color);						
			}
			
			imagecopyresampled(
				$imct, $imc, $coord['x'], $coord['y'], 0, 0, 
				$thumb->getWidth(), $thumb->getHeight(), 
				$img->getWidth(), $img->getHeight()
			);
			
			imagegif($imct,$path.$img->getName());
		}
		
		/**
		 *	Méthode de création d'image PNG
		 *
		 *	@params
		 *		img : (Image) Objet image à manipuler.
		 *		imct:(Resource) Renvoyée par la méthode imagecreatetruecolor()
		 *		thumb:(Image) Objet image réduit.
		 *		coord:(Array)
		 *			x : abscisse du coin supérieur gauche de la miniature.
		 *			y: ordonnée du coin supérieur gauche de la miniature.
		 *		path:(String) Chemin de sauvegarde de la mininature.
		 */
		private static function createPNG($img,$imct,$thumb,$coord,$path){
			$imc = imagecreatefrompng($img->getPath());
			
			imagealphablending($imct, false);
			imagesavealpha($imct, true);
			
			$trans_colour = imagecolorallocatealpha($imct, 0, 0, 0, 127);
			imagefill($imct, 0, 0, $trans_colour);
							
			imagecopyresampled(
				$imct, $imc, $coord['x'], $coord['y'], 0, 0, 
				$thumb->getWidth(), $thumb->getHeight(), 
				$img->getWidth(), $img->getHeight()
			);
			
			imagepng($imct,$path.$img->getName());
		}
	}
	
	/**
	 *	***** TODO *****
	 *
	 *	Classe d'exception générale relative à la classe Manager.
	 */
	class ImageManagerErrorException extends Exception{}
?>