<?
/*
	ImageOverlayer
	Author: Andrei Bogarevich
	License:  MIT License
	Site: https://github.com/madeS/ImageOverlayer
	v1.1.0.10
	Last Mod: 2015-08-01 20:00
*/
class ImageOverImageTasker extends ImageOverTasker {
    
    private $image = '';
    private $imageDestParams = null;
    private $imageHorisontalAlign = 'center';
    private $imageVerticalAlign = 'center';

    /**
     * Borders вокруг Бокса для картинки
     * @var type 
     */
    private $boxBorders = array();
    /**
     * Borders вокруг самой картинки
     * @var type 
     */
    private $imageBorders = array();
    /**
     * Цвет заливки Бокса
     * @var type 
     */
    private $fillColor = null;
    
    
    
    /**
     * Устанавливает Изображение 
     * @param type $imagePath
     * @param type $align
     * @return boolean
     * @throws ImageOverlayException
     */
    public function setImageInBox($imagePath, $horisontalAlign = 'center', $verticalAlign = 'center', $fill = null){

        try {
            $image = $this->getCheckedImage($imagePath);
        } catch (ImageOverlayException $ex){
            throw $ex;
        }
        
        $this->image = $image;
        if (in_array($horisontalAlign, array('center','left', 'right','cut'))){
            $this->imageHorisontalAlign = $horisontalAlign;
        }
        if (in_array($verticalAlign, array('center','top', 'bottom','cut'))){
            $this->imageVerticalAlign = $verticalAlign;
        }
        
        return true;
    }
	
    
    public function addBoxBorder($width, ImageOverColor $color = null){
        $width = intval($width);
        if (!$width) return false;
        $this->boxBorders[] = array(
                'width' => $width,
                'color' => ($color ? $color : new ImageOverColor())
            );
        return true;
    }
    
    public function addImageBorder($width, ImageOverColor $color = null){
        $width = intval($width);
        if (!$width) return false;
        $this->imageBorders[] = array(
                'width' => $width,
                'color' => ($color ? $color : new ImageOverColor())
            );
        return true;
    }   

    
    public function execute(&$source){
        if ($this->box_line_left < 0) return false;
        parent::execute($source);
        
        try {
           $imgSrc = $this->getImageResource($this->image);
        } catch (ImageOverlayException $ex){
            return false;
        }
        
        
        $this->calculateImageDestParams($this->image['width'], $this->image['height']);
        
        usort($this->imageBorders, array($this, 'bordersCompare'));
        
        $this->drawBorders($source, $this->imageBorders, $this->imageDestParams);
        
        imagecopyresampled(
                $source, $imgSrc,
                $this->imageDestParams['left'], $this->imageDestParams['top'], 
                $this->imageDestParams['src_x'], $this->imageDestParams['src_y'], 
                $this->imageDestParams['width'], $this->imageDestParams['height'], 
                $this->imageDestParams['src_width'], $this->imageDestParams['src_height']
            );
        
                
    }
    
    private function drawBorders(&$source, $borders, $box){
        foreach($borders as $border){
            $color = imagecolorallocatealpha($source, $border['color']->getRed(), $border['color']->getGreen(), $border['color']->getBlue(),$border['color']->getAlpha());
            imagefilledrectangle($source, 
                    $box['left'] - $border['width'], $box['top'] - $border['width'], 
                    $box['right'] + $border['width'], $box['bottom'] + $border['width'], 
                    $color
                );
        }
    }
    
    private function calculateImageDestParams($imgSrcWidth,$imgSrcHeight){
        $containerWidth = $this->box_line_right - $this->box_line_left;
        $containerHeight = $this->box_line_bottom - $this->box_line_top;
        
        // Max to container
		if ($this->imageHorisontalAlign === 'cut' && $this->imageVerticalAlign === 'cut'){
			$srcX = $srcY = 0;
			if ((1.0 * $containerWidth / $containerHeight) > (1.0 * $imgSrcWidth / $imgSrcHeight) ) {
				$srcWidth = $imgSrcWidth;
				$srcHeight = intval($srcWidth * $containerHeight / $containerWidth);
				$srcY = $this->intval(($imgSrcHeight-$srcHeight)/2,0);
			} else {
				$srcHeight = $imgSrcHeight;
				$srcWidth = intval($srcHeight * $containerWidth / $containerHeight);
				$srcX = $this->intval(($imgSrcWidth-$srcWidth)/2,0);
			}
			$destX = $this->box_line_left;
			$destY = $this->box_line_top;
			$destWidth = $containerWidth;
			$destHeight = $containerHeight;
		} else {
			$srcX = $srcY = 0;
			$srcWidth = $imgSrcWidth;
			$srcHeight = $imgSrcHeight;
			if ((1.0 * $containerWidth / $containerHeight) < (1.0 * $imgSrcWidth / $imgSrcHeight) ) {
				$destWidth = $containerWidth;
				$destHeight = intval($containerWidth * $imgSrcHeight / $imgSrcWidth);
			} else {
				$destHeight = $containerHeight;
				$destWidth = intval($containerHeight * $imgSrcWidth / $imgSrcHeight);
			}
			$destX = $this->box_line_left;
			if ($this->imageHorisontalAlign == 'right'){
				$destX = $this->box_line_right - $destWidth;
			} elseif ($this->imageHorisontalAlign == 'center'){
				$destX = intval(($this->box_line_right - $destWidth + $this->box_line_left) / 2);
			}

			$destY = $this->box_line_top;
			if ($this->imageVerticalAlign == 'bottom'){
				$destY = $this->box_line_bottom - $destHeight;
			} elseif ($this->imageVerticalAlign == 'center'){
				$destY = intval(($this->box_line_bottom - $destHeight + $this->box_line_top) / 2);
			}
		}
        
        $this->imageDestParams = array(
			'src_x' => $srcX,
			'src_y' => $srcY,
			'src_width' => $srcWidth,
			'src_height' => $srcHeight,
            'width' => $destWidth,
            'height' => $destHeight,
            'left' => $destX,
            'top' => $destY,
            'right' => $destX + $destWidth,
            'bottom' => $destY + $destHeight,
        );
    }
    
    
    function bordersCompare($a, $b){
        return ($a['width'] < $b['width']);
    }

    private  function intval($val, $min = false, $max = false) {
		$val = intval($val);
		if (($min !== false) && ($val < $min)) $val = $min;
		if (($max !== false) && ($val > $max)) $val = $max;
		return $val;
	}
    
    
    

    
    
    
}