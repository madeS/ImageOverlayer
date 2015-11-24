<?
/*
	ImageOverlayer
	Author: Andrei Bogarevich
	License:  MIT License
	Site: https://github.com/madeS/ImageOverlayer
	v1.1.0.10
	Last Mod: 2015-08-01 20:00
*/
class ImageOverTasker extends ImageOverBase {
    
    
    public $background = null;
    
    protected $box_line_left = -1;
    protected $box_line_top = -1;
    protected $box_line_right = -1;
    protected $box_line_bottom = -1;
    
    protected $debugFlag = false;
    
    public function __construct($background){ 
        $this->background = $background;
    }
    
    public function execute(&$source){
        if ($this->debugFlag) $this->debugging($source);   
    }
    
    /**
     * Устанавливает размер поля для ввода текста размеры устанавливаются в пикселях или процентах( для процентов передается строка со знаком процент вконце)
     * @param type $left
     * @param type $top
     * @param type $width
     * @param type $height
     */
    public function setBox($left, $top, $width, $height){
        if (!$this->background) {
            throw new ImageOverlayException('Canvas not setted!'); 
        }
        if (is_string($left) && mb_substr($left, -1) === '%') {
            $this->box_line_left = intval(floatval($left) * $this->background['width'] / 100);
        } else {
            $this->box_line_left = intval($left);
        }
        if (is_string($top) && mb_substr($top, -1) === '%') {
            $this->box_line_top = intval(floatval($top) * $this->background['height'] / 100);
        } else {
            $this->box_line_top = intval($top);
        }
        if (is_string($width) && mb_substr($width, -1) === '%') {
            $this->box_line_right = $this->box_line_left + intval(floatval($width) * $this->background['width'] / 100);
        } else {
            $this->box_line_right = $this->box_line_left + intval($width);
        }
        if (is_string($height) && mb_substr($height, -1) === '%') {
            $this->box_line_bottom = $this->box_line_top + intval(floatval($height) * $this->background['height'] / 100);
        } else {
            $this->box_line_bottom = $this->box_line_top + intval($height);
        }
        if (    $this->box_line_left < 0 || $this->box_line_left > $this->background['width'] ||
                $this->box_line_right < 0 || $this->box_line_right > $this->background['width'] ||
                $this->box_line_top < 0 || $this->box_line_top > $this->background['height'] ||
                $this->box_line_bottom < 0 || $this->box_line_bottom > $this->background['height'] )
        {
            $this->box_line_left = $this->box_line_right = $this->box_line_top = $this->box_line_bottom = -1;
            throw new ImageOverlayException('Box out of range');
            return false;
        }
        return true;
    }
    
    public function activeDebug(){
        $this->debugFlag = true;
    }
    
    protected function debugging(&$source){
        imageline($source, $this->box_line_left, $this->box_line_top, $this->box_line_right, $this->box_line_top, 0xFF0000);
        imageline($source, $this->box_line_right, $this->box_line_top, $this->box_line_right, $this->box_line_bottom, 0xFF0000);
        imageline($source, $this->box_line_right, $this->box_line_bottom, $this->box_line_left, $this->box_line_bottom, 0xFF0000);
        imageline($source, $this->box_line_left, $this->box_line_bottom, $this->box_line_left, $this->box_line_top, 0xFF0000);
        
    }
    
}