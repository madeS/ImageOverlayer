<?
/*
	ImageOverlayer
	Author: Andrei Bogarevich
	License:  MIT License
	Site: https://github.com/madeS/ImageOverlayer
	v1.1.0.10
	Last Mod: 2015-08-01 20:00
*/
require_once 'ImageOverBase.php';
require_once 'ImageOverColor.php';
require_once 'ImageOverTasker.php';
require_once 'ImageOverTextTasker.php';
require_once 'ImageOverImageTasker.php';
require_once 'ImageOverRectTasker.php';

class ImageOverlayer extends ImageOverBase {
    
    private $background = null;
    private $workImage = null;
    
    public function __construct(){ }
    
    
    /**
     * Устанавливает изображение подложку
     * @param type $backgroundImagePath
     * @return boolean
     */
    public function setCanvas($backgroundImagePath){
        try {
            $background = $this->getCheckedImage($backgroundImagePath);
        } catch (ImageOverlayException $ex){
            throw $ex;
        }
        
        $this->background = $background;
        
        $this->createWorkImage();
        
        return true;
    }
    
    
    /**
     * 
     * @return ImageOverTextTasker
     */
    public function createTextTask(){
        if (!$this->checkBackground()) return false;
        return new ImageOverTextTasker($this->background);
    }
    
    public function createImageTask(){
        if (!$this->checkBackground()) return false;
        return new ImageOverImageTasker($this->background);
    }
	public function createRectTask(){
        if (!$this->checkBackground()) return false;
        return new ImageOverRectTasker($this->background);
    }
    
    public function overlay($arrayOfTasks){
        if (!$this->checkWorkImage()) return false;
        if (!is_array($arrayOfTasks)) {
            throw new ImageOverlayException('Overlay: invalid params');
        }
        
        foreach($arrayOfTasks as $task){
            if ($task instanceof ImageOverTasker){
                $task->execute($this->workImage);
            }
        }
        return true;
    }
    
    public function save($directory, $fileName, $opt = array()){
        
        $out_mode = (isset($opt['out_mode'])) ? $opt['out_mode'] : 0777;
        $directory = rtrim($directory, '/').'/';
        if (!file_exists($directory) || !is_dir($directory)){
            mkdir($directory, $out_mode, true);
        }
        
        $fileName = $this->filterFileName($fileName);

        $output_type = $this->background['mime'];
		if (isset($opt['out_ext'])){
			if ($opt['out_ext'] == 'jpg') $output_type = 'image/jpeg';
			if ($opt['out_ext'] == 'png') $output_type = 'image/png';
			if ($opt['out_ext'] == 'gif') $output_type = 'image/gif';
		}
        
        if ($output_type == 'image/jpeg'){
			$fileName = $fileName.'.jpg';
            $resultFile = $directory.$fileName;
			$quality = (isset($opt['quality']))?intval($opt['quality']):85;
			imagejpeg($this->workImage, $resultFile, $quality);
		} elseif($output_type == 'image/png') {
			$fileName = $fileName.'.png';
            $resultFile = $directory.$fileName;
			imagepng($this->workImage, $resultFile);
		} elseif($output_type == 'image/gif') {
			$fileName = $fileName.'.gif';
            $resultFile = $directory.$fileName;
			imagegif($this->workImage, $resultFile);
		} else {
             throw new ImageOverlayException('Save: unsupport MIME type when save');
		}
        chmod($resultFile, $out_mode);
        
        return $fileName;
    }
    
    public function destroy(){
        @imagedestroy($this->workImage);
    }
    
    private function filterFileName($fileName){
 		if (strpos($fileName,'{#rand}') !== false){
			$fileName = str_replace('{#rand}',md5(filesize($this->background['image']).rand(0, 10000)),$fileName);
		}
		if (strpos($fileName,'{#rand6}') !== false){
			$fileName = str_replace('{#rand6}',substr(md5($this->background['image'].rand(0, 10000)),0,6),$fileName);
		}
		if (strpos($fileName,'{#time}') !== false){
			$fileName = str_replace('{#time}',time(),$fileName);
		}
        $fileName = preg_replace('%[^A-Za-z0-9_-]%', '', $fileName);
        return $fileName;
    }
    
    private function createWorkImage(){
        $src = $this->createSrc();
        $this->workImage = imagecreatetruecolor($this->background['width'], $this->background['height']);
        
        // [TODO] Прозрачность сохранить
        //imagecolortransparent($this->workImage, imagecolorallocate($this->workImage, 255, 255, 255));
        
		imagecopyresampled($this->workImage, $src, 
                0,0, // workImage point
                0, 0, // source image point
                $this->background['width'], $this->background['height'], // workImage size insert
                $this->background['width'], $this->background['height'] // source image size cut
        );
        
        
        imagedestroy($src);
    }
    
    private function checkBackground(){
        if ($this->background) {
            return true;
        }
        throw new ImageOverlayException('Canvas not setted');
    }
    
    private function checkWorkImage(){
        if (!$this->checkBackground()) return false;
        if (!$this->workImage){
            throw new ImageOverlayException('workImage has been destroyed');
        }
        return true;
    }
    
    private function createSrc(){
        try {
           $src = $this->getImageResource($this->background);
        } catch (ImageOverlayException $ex){
            throw $ex;
        }
        return $src;
    }
    
    
}