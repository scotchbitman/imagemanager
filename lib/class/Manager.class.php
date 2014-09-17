<?php
	/**
	 *	Classe Manager
	 */
	class Manager{

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
			
			if($this->config['landscapeThumb'] && 
			$img->getOrientation() == 'portrait') $ratio = 1 / $ratio;
			
			$width = $this->config['maxWidth'];
			$height= $this->config['maxWidth'] / $ratio;
			
			return new Image($width,$height);
		}
		
		/**
		 *	Permet de générer une miniature.
		 *
		 *	@param img : (Canvas)
		 *		Objet de type Canvas.
		 */
		public function makeThumb($img){
			$bkg = self::getBackground($img);
			$thumb = $img->reduce($bkg);

			header('Content-Type: image/jpeg');

			$imc = @imagecreatefromjpeg($img->getPath());
			$imct  = imagecreatetruecolor($bkg->getWidth(),$bkg->getHeight());
			$back = imagecolorallocate($imct, 255, 255, 255);
			imagefilledrectangle(
				$imct, 
				0, 0, 
				$bkg->getWidth(), $bkg->getHeight(), 
				$back
			);
			
			$coord = $thumb->getPosition($bkg);
			
			imagecopyresampled(
				$imct, $imc,
				$coord['x'], $coord['y'],
				0, 0,
				$thumb->getWidth(), $thumb->getHeight(),
				$img->getWidth(), $img->getHeight()
			);
			
			imagejpeg($imct);
			imagedestroy($imct);
		}
	}
	
	/**
	 *	***** TODO *****
	 *
	 *	Classe d'exception générale relative à la classe Manager.
	 */
	class ManagerErrorException extends Exception{}
?>