<?php
class imageresize{
	protected $image;
	
	protected $newWidth;
	
	protected $newHeight;
	
	protected $width;
	
	protected $height;
	
    /**
     * Holds the type of the image, represented by a number 
	 * (1 = GIF, 2 = JPG, 3 = PNG).
     *
     * @var int
     */	
	protected $imageType;
	
	protected $newImageObj;
	
	protected $imageObj;
	
	protected $waterMarkImage;

    /**
     * Watermark position 'topLeft', 'topCenter', 'topRight', 'bottomLeft', 'bottomCenter', 'bottomRight' and 'center'.
     *
     * @var string
     */
    protected $waterMarkPosition = 'bottomRight';	
	
    protected $waterMarkOpacity = 50;	
	
	public function __construct($image){
		$this->image = $image;
		list($this->width, $this->height, $this->imageType) = getimagesize($image);
	}
    
    /**
     * Sets the position of the watermark.
     *
     * @param string $waterMarkPosition
     * @access public
     */
    public function setWaterMarkPosition($waterMarkPosition) {
        if ($waterMarkPosition == 'topLeft' || 
           $waterMarkPosition == 'topCenter' || 
           $waterMarkPosition == 'topRight' || 
           $waterMarkPosition == 'bottomLeft' || 
           $waterMarkPosition == 'bottomCenter' || 
           $waterMarkPosition == 'bottomRight' || 
           $waterMarkPosition == 'center') {
            $this->waterMarkPosition = $waterMarkPosition;
        }
        else {
            throw new Exception("Position of the watermark is invalid.");
        }
    }	
	
    /**
     * Returns the position of the watermark.
     *
     * @return string
     * @access public
     */
    public function getWaterMarkPosition() {
        return $this->waterMarkPosition;
    }
    
    /**
     * Set the opacity value of the watermark image.
	 * Valid range is 0 to 100
     *
     * @param int $waterMarkOpacity
     * @access public
     */
    public function setWaterMarkOpacity($waterMarkOpacity) {
        if ($waterMarkOpacity >= 0 && $waterMarkOpacity <= 100) {
            $this->waterMarkOpacity = $waterMarkOpacity;
        }
        else {
            throw new Exception("Opacity is invalid! Use values from 0 to 100.");
        }
    }
    
    /**
     * Returns the opacity of the watermark.
     *
     * @return int
     * @access public
     */
    public function getWaterMarkOpacity() {
        return $this->waterMarkOpacity;
    }
	
	public function rotateImage($degrees = 0){
		if ($degrees > 360 || $degrees < -360) {
            throw new Exception("Angle is invalid. Accepted value is from -360 to 360.");
        }
	
        if ($degrees != 0) {
			$bgcolor = imagecolorallocatealpha($this->newImageObj, 255,255, 255, 127);
			imagefill($this->newImageObj, 0, 0, $bgcolor);
			$this->newImageObj = imagerotate($this->newImageObj, $degrees, $bgcolor);

			// need to do this in order to properly position the watermark
			$this->newWidth = imagesx($this->newImageObj);
			$this->newHeight = imagesy($this->newImageObj);
        }	
	}
	
	public function addWatermark($watermark){
		$watermarkWidth = 0;
		$watermarkHeight = 0;
		$watermarkType = 0;
	
        if ($watermark) {
			try {
                list($watermarkWidth,$watermarkHeight,$watermarkType) = getimagesize($watermark);
                if ($watermarkHeight > $this->newHeight || $watermarkWidth  > $this->newWidth) {
                    throw new Exception("Watermark is larger than the resized image.");
                }
            }
            catch (Exception $e) {
                throw new Exception("Unable to identify the size of the watermark image.");
            }
			
            switch ($watermarkType) {
                case 1:
                    $markImg = imagecreatefromgif($watermark);
                    break;
                case 2:
                    $markImg = imagecreatefromjpeg($watermark);
                    break;
                case 3:
                    $markImg = imagecreatefrompng($watermark);
                    break;
                default:
                    throw new Exception("Type of watermark image is not a valid type!");
                    break;
            }
            
            /**
             * Calculates an appropriate position
             */
            switch($this->getWaterMarkPosition()) {
                case 'topLeft':
                    $x = 10;
                    $y = 10;
                    break;
                case 'topCenter':
                    $x = round(($this->newWidth / 2) - ($watermarkWidth / 2));
                    $y = 10;
                    break;
                case 'topRight':
                    $x = $this->newWidth - $watermarkWidth - 10;
                    $y = 10;
                    break;
                case 'bottomLeft':
                    $x = 10;
                    $y = $this->newHeight - $watermarkHeight - 10;
                    break;
                case 'bottomCenter':
                    $x = round(($this->newWidth / 2) - ($watermarkWidth / 2));
                    $y = $this->newHeight - $watermarkHeight - 10;
                    break;
                case 'bottomRight':
                    $x = $this->newWidth - $watermarkWidth - 10;
                    $y = $this->newHeight - $watermarkHeight - 10;
                    break;
                case 'center':
                    $x = round(($this->newWidth / 2) - ($watermarkWidth / 2));
                    $y = round(($this->newHeight / 2) - ($watermarkHeight / 2));
                    break;
                default:
                    $x = 10;
                    $y = 10;
            }
            
            try {
                imagecopymerge($this->newImageObj, $markImg, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight, $this->getWaterMarkOpacity());
            }
            catch (Exception $e) {
                throw new Exception("Unable to add watermark.");
            }
        }	
	}
	
	public function setNewSize($width = 0,$height = 0){
		if ($this->newWidth && $this->newHeight){
			$this->width = $this->newWidth;
			$this->height = $this->newHeight;
		}
		$this->newWidth = $width;
		$this->newHeight = $height;
	}
	
	protected function createImgObj(){
		switch ($this->imageType) {
			case 1:
				$this->imageObj = imagecreatefromgif($this->image);
				$this->newImageObj = imagecreate($this->newWidth,$this->newHeight);
				break;
			case 2:
				$this->imageObj = imagecreatefromjpeg($this->image);
				$this->newImageObj = imagecreatetruecolor($this->newWidth,$this->newHeight);
				break;
			case 3:
				$this->imageObj = imagecreatefrompng($this->image);
				$this->newImageObj = imagecreatetruecolor($this->newWidth,$this->newHeight);
				break;
			default:
				throw new Exception("Image Type is not a valid type!");
				break;
		}		
	}
		
	public function copyImage(){
		$this->imageObj = $this->newImageObj;		
		switch ($this->imageType) {
			case 1:
				$this->newImageObj = imagecreate($this->newWidth,$this->newHeight);
				break;
			case 2:
				$this->newImageObj = imagecreatetruecolor($this->newWidth,$this->newHeight);
				break;
			case 3:
				$this->newImageObj = imagecreatetruecolor($this->newWidth,$this->newHeight);
				break;
			default:
				throw new Exception("Image Type is not a valid type!");
				break;
		}
	}	

	public function cropImage($startX,$startY,$endX,$endY){
		if (!$this->imageObj & !$this->newImageObj){
			self::createImgObj();
		} else {			
			self::copyImage();
		}
		imagecopy ($this->newImageObj, $this->imageObj, 0, 0, $startX, $startY, $endX, $endY);	
	}
	
	public function resizeImage($proportionalFlag = 'H'){
		if (!$this->imageObj & !$this->newImageObj){
			self::createImgObj();
		} else {
			self::copyImage();
		}	
		
		if ($proportionalFlag == 'H') {
			$this->newHeight = round(($this->newWidth * $this->height) / $this->width);
		}
		elseif ($proportionalFlag == 'V') {
			$this->newWidth = round(($this->newHeight * $this->width) / $this->height);
		}
		else {
			throw new Exception("Incorrect value no attribute 'proportional flag'.");
		}
		
		if (imagesx($this->imageObj) > $this->newWidth && imagesy($this->imageObj) > $this->newHeight){
			/* resizes if the image is bigger than the new size */
			/* Check if this image is PNG or GIF, then set Transparent */  
			if (($this->imageType == 1) || ($this->imageType == 3)){
				$this->newImageObj = imagecreatetruecolor($this->newWidth, $this->newHeight);
				imagealphablending($this->newImageObj, false);
				imagesavealpha($this->newImageObj,true);
				$transparent = imagecolorallocatealpha($this->newImageObj, 255, 255, 255, 127);
				imagefilledrectangle($this->newImageObj, 0, 0, $this->newWidth, $this->newHeight, $transparent);
			}
			imagecopyresampled($this->newImageObj, $this->imageObj, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width, $this->height);	
		} else {
			/* the image is smaller than the new size, but do this to preserve transparency */
			/* Check if this image is PNG or GIF, then set if Transparent */  
			if (($this->imageType == 1) || ($this->imageType == 3)){
				$this->newImageObj = imagecreatetruecolor($this->width, $this->height);
				imagealphablending($this->newImageObj, false);
				imagesavealpha($this->newImageObj,true);
				$transparent = imagecolorallocatealpha($this->newImageObj, 255, 255, 255, 127);
				imagefilledrectangle($this->newImageObj, 0, 0, $this->width, $this->height, $transparent);
			}
			imagecopyresampled($this->newImageObj, $this->imageObj, 0, 0, 0, 0, $this->width, $this->height, $this->width, $this->height);		
		}
	}
	
	public function saveImage($newImage){
        try {
			switch ($this->imageType) {
				case 1:
					imagegif ($this->newImageObj, $newImage);
					break;
				case 2:
					imagejpeg($this->newImageObj, $newImage, 90);
					break;
				case 3:
					imagepng($this->newImageObj, $newImage);
					break;
				default:
					throw new Exception("Image Type is not compatible.");
					break;
			}
        }
        catch (Exception $e) {
            throw new Exception($e);
        }
	}
}
?>