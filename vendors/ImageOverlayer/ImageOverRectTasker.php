<?
/*
	ImageOverlayer
	Author: Andrei Bogarevich
	License:  MIT License
	Site: https://github.com/madeS/ImageOverlayer
	v1.1.0.10
	Last Mod: 2015-08-01 20:00
*/
class ImageOverRectTasker extends ImageOverTasker {
    
    private $fillColor = null;
    

    public function setColor($color){
		$this->fillColor = ($color ? $color : new ImageOverColor());
        return true;
    }
	
    
    public function execute(&$source){
        if ($this->box_line_left < 0) return false;
        if (!$this->fillColor) return false;
        parent::execute($source);
        $color = imagecolorallocatealpha($source, $this->fillColor->getRed(), $this->fillColor->getGreen(), $this->fillColor->getBlue(), $this->fillColor->getAlpha());
		imagefilledrectangle($source, 
				$this->box_line_left, $this->box_line_top, 
				$this->box_line_right, $this->box_line_bottom, 
				$color
			);
    }

}