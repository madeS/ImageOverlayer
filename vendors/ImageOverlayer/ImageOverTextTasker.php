<?
/*
	ImageOverlayer
	Author: Andrei Bogarevich
	License:  MIT License
	Site: https://github.com/madeS/ImageOverlayer
	v1.0.0.7
	Last Mod: 2015-07-20 20:00
*/

class ImageOverTextTasker extends ImageOverTasker {
    
    private $box_line_left = -1;
    private $box_line_top = -1;
    private $box_line_right = -1;
    private $box_line_bottom = -1;
    
    private $font = '';
    private $fontMaxSize = 30;
    private $fontMinSize = 8;
    private $fontLineHeight = 2.7;
    private $fontColorRed = 0x41;
    private $fontColorGreen = 0x41;
    private $fontColorBlue = 0x41;
    
    private $align = '';
    private $text = '';
    
    private $debugFlag = false;
    
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
    
    public function setFont($fontPath, $maxSize = 30, $minSize = 8, $lineHeight = 1.7, $align = 'left', $colorRed = 0x41, $colorGreen = 0x41, $colorBlue = 0x41){
        if (!(file_exists($fontPath) && is_file($fontPath))){
            throw new ImageOverlayException('Font file not found');
        }
        $this->font = $fontPath;
        $this->fontMaxSize = (int) $maxSize;
        $this->fontMinSize = (int) $minSize;
        $this->fontLineHeight = (int) $lineHeight;
        $this->align = $align;
        
        $this->fontColorRed = $colorRed;
        if ($this->fontColorRed < 0) $this->fontColorRed = 0;
        if ($this->fontColorRed > 255) $this->fontColorRed = 255;
        $this->fontColorGreen = $colorGreen;
        if ($this->fontColorGreen < 0) $this->fontColorGreen = 0;
        if ($this->fontColorGreen > 255) $this->fontColorGreen = 255;
        $this->fontColorBlue = $colorBlue;
        if ($this->fontColorBlue < 0) $this->fontColorBlue = 0;
        if ($this->fontColorBlue > 255) $this->fontColorBlue = 255;
        
        return true;
    }
    
    public function setText($text){
        $this->text = $text;
        return true;
    }
    
    public function activeDebug(){
        $this->debugFlag = true;
    }
    
    public function execute(&$source){
        if ($this->box_line_left < 0) return false;
        
        if ($this->debugFlag) $this->debugging($source);
        
        $words = explode(' ', $this->text);
        
        $textLined = '';
        $newLineWordsCount = 0;
        $newLine = '';

        $fontsizeStep = 2;
        
        for ($fontsize = $this->fontMaxSize; $fontsize >= $this->fontMinSize; $fontsize = $fontsize - $fontsizeStep){
            for($i = 0; $i < count($words); $i++) {
                $word = $words[$i];

                $tmpString = $newLine . ($newLineWordsCount ? ' ' : '') . $word;

                $textbox = imagettfbbox($fontsize, 0, $this->font, $tmpString);
                $leftedbox = $textbox[0];
                $bottomed = $textbox[1];
                $righted = $textbox[4];
                $topped = $textbox[5];

                if ($righted - $leftedbox < $this->box_line_right - $this->box_line_left){ // Вмещается
                    $newLine = $tmpString;
                    $newLineWordsCount++;
                } else { // Не вмещается
                    if ($newLineWordsCount){ // В строке уже были слова
                        $textLined .= ($textLined===''?'':"\n") . $newLine; // Мерджим сформированную старую строку
                        $newLine = ''; $newLineWordsCount = 0;
                        $i--; // Новое слово отправлем на повторуню обработку в новой строке
                    } else {
                       $newLine = $tmpString; 
                       $newLineWordsCount++;
                       $textLined .= ($textLined===''?'':"\n") . $newLine; // Мерджим сформированную новую строку выходящую за пределы бокса (так как одно слово)
                       $newLine = ''; $newLineWordsCount = 0;
                    }
                }

                if ($newLineWordsCount && $i == count($words) - 1){
                    // Последний проход и в строке остались слова
                    $textLined .= ($textLined===''?'':"\n") . $newLine; // Мерджим
                    $newLine = ''; $newLineWordsCount = 0;
                }
            }
            // На выходе есть текст $textLined разбитый на \n который вмещается* в бокс-ширину со шрифтом $fontsize
            $lineHeight = intval($this->fontLineHeight * $fontsize);
            $lines = explode("\n", $textLined);
            if ($fontsize - $fontsizeStep >= $this->fontMinSize){ // Это не последняя попытка
                if (count($lines) * $lineHeight > $this->box_line_bottom - $this->box_line_top){ // Не вмещается по высоте
                    $textLined = '';
                    continue;
                }
            }
            break;
        }
        
        $lines = explode("\n", $textLined);
        $lineHeight = intval($this->fontLineHeight * $fontsize);
        
        $color	= imagecolorallocate($source, $this->fontColorRed, $this->fontColorGreen, $this->fontColorBlue);
        
        for($i = 0; $i < count($lines); $i++){
            $line = $lines[$i];
            
            $textbox = imagettfbbox($fontsize, 0, $this->font, $line);
            $leftedbox = $textbox[0];
            $bottomed = $textbox[1];
            $righted = $textbox[4];
            $topped = $textbox[5];
            
            $topOffset = ($i+1) * $lineHeight;
            $leftOffset = 0;
            if($this->align == 'center'){
                $leftOffset = intval((($this->box_line_right - $this->box_line_left) - ($righted - $leftedbox)) / 2);
            } elseif($this->align == 'right'){
                $leftOffset = ($this->box_line_right - $this->box_line_left) - ($righted - $leftedbox);
            }
            
            imagettftext($source, $fontsize ,0 , 
                    $this->box_line_left + $leftOffset, $this->box_line_top + $topOffset,
                    $color, $this->font, $line);
            
        }
        
    }
    
    
    
    
    
    private function debugging(&$source){
        
        imageline($source, $this->box_line_left, $this->box_line_top, $this->box_line_right, $this->box_line_top, 0xFF0000);
        imageline($source, $this->box_line_right, $this->box_line_top, $this->box_line_right, $this->box_line_bottom, 0xFF0000);
        imageline($source, $this->box_line_right, $this->box_line_bottom, $this->box_line_left, $this->box_line_bottom, 0xFF0000);
        imageline($source, $this->box_line_left, $this->box_line_bottom, $this->box_line_left, $this->box_line_top, 0xFF0000);
        
    }
    
    
    
}