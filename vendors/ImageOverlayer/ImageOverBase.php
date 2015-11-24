<?
/*
	ImageOverlayer
	Author: Andrei Bogarevich
	License:  MIT License
	Site: https://github.com/madeS/ImageOverlayer
	v1.1.0.10
	Last Mod: 2015-08-01 20:00
*/
class ImageOverlayException extends Exception {
    
}

class ImageOverBase {
    
    
    protected function getCheckedImage($imagePath){
        if (!(file_exists($imagePath) && is_file($imagePath))){
            throw new ImageOverlayException('File not found ['.$imagePath.']');
        }
        $size = getimagesize($imagePath);
        if(!$size) { 
            throw new ImageOverlayException('Invalid file type ['.$imagePath.']');
        }
        $width = isset($size[0]) ? $size[0] : 0;
        $height = isset($size[1]) ? $size[1] : 0;
        $type = $size['mime'];
        if (!$width || !$height) { 
            throw new ImageOverlayException('Invalid image size ['.$imagePath.']');
        }
        if (!in_array($type, array('image/jpeg','image/png','image/gif'))) { 
            throw new ImageOverlayException('Invalid image mime ['.$imagePath.']');
        }
        return array(
            'image' => $imagePath,
            'width' => $width,
            'height' => $height,
            'mime' => $type,
        );       
    }
    
    protected function getImageResource($image){
        $src = false;
		if ($image['mime'] == 'image/jpeg'){
			$src = @imagecreatefromjpeg($image['image']);
		} elseif($image['mime'] == 'image/png') {
			$src = @imagecreatefrompng($image['image']);
		} elseif($image['mime'] == 'image/gif') {
			$src = @imagecreatefromgif($image['image']);
		} else {
            throw new ImageOverlayException('unsupport MIME type when open ['.$image['image'].']');
		}
		if (!$src) {
            throw new ImageOverlayException('unsupport file, open error ['.$image['image'].']');

		}
        return $src;
    }



    public function debug($var){
        echo '<pre>'.print_r($var,true).'</pre>';
    }
    
}