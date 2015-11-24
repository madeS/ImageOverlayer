<?
/*
	ImageOverlayer
	Author: Andrei Bogarevich
	License:  MIT License
	Site: https://github.com/madeS/ImageOverlayer
	v1.0.0.7
	Last Mod: 2015-07-20 20:00
*/

class ImageOverTasker extends ImageOverBase {
    
    public $background = null;
    
    public function __construct($background){ 
        $this->background = $background;
    }
    
}