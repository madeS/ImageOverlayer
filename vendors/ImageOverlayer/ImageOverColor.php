<?
/*
	ImageOverlayer
	Author: Andrei Bogarevich
	License:  MIT License
	Site: https://github.com/madeS/ImageOverlayer
	v1.1.0.10
	Last Mod: 2015-08-01 20:00
*/
class ImageOverColor {
    
    private $red = 0x41;
    private $green = 0x41;
    private $blue = 0x41;
    private $alpha = 127;
    
    public function __construct($red = null, $green = null, $blue = null, $alpha = 127){ 
        if ($red !== null && $green !== null && $blue !== null){
            $this->setColor($red, $green, $blue, $alpha);
        }
    }
    
    public function setColor($red, $green, $blue, $alpha = 127){
        $this->setRed($red);
        $this->setGreen($green);
        $this->setBlue($blue);
        $this->setAlpha($alpha);
    }
    
    public function setRed($value){
        $this->red = $value;
        if ($this->red < 0) $this->red = 0;
        if ($this->red > 255) $this->red = 255;
    }
    public function setGreen($value){
        $this->green = $value;
        if ($this->green < 0) $this->green = 0;
        if ($this->green > 255) $this->green = 255;
    }
    public function setBlue($value){
		$this->blue = $value;
        if ($this->blue < 0) $this->blue = 0;
        if ($this->blue > 255) $this->blue = 255;       
    }
	
	public function setAlpha($value){
		$this->alpha = $value;
        if ($this->alpha < 0) $this->alpha = 0;
        if ($this->alpha > 127) $this->alpha = 127;    
	}
    
    public function getRed(){
        return $this->red;
    }
    public function getGreen(){
        return $this->green;
    }
    public function getBlue(){
        return $this->blue;
    }
	public function getAlpha(){
        return $this->alpha;
    }
    
}