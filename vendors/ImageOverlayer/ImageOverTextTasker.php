<?
/*
	ImageOverlayer
	Author: Andrei Bogarevich
	License:  MIT License
	Site: https://github.com/madeS/ImageOverlayer
	v1.1.0.10
	Last Mod: 2015-08-01 20:00
*/
class ImageOverTextTasker extends ImageOverTasker {
    
    private $font = '';
    private $fontMaxSize = 30;
    private $fontMinSize = 8;
    private $fontLineHeight = 2.7;
    private $fontLineHeightCorrector = array(
		'ratio' => 0.7, 
		'added' => 2,
	);
    private $fontColor = null;
    
    private $align = '';
    private $valign = '';
    private $text = '';

    public function __construct($background){ 
        parent::__construct($background);

        $this->fontColor = new ImageOverColor();
    }

    
    public function setFont($fontPath, $maxSize = 30, $minSize = 8, $lineHeight = 1.7, $align = 'left', $valign = 'top', ImageOverColor $color = null){
        if (!(file_exists($fontPath) && is_file($fontPath))){
            throw new ImageOverlayException('Font file not found');
        }
        $this->font = $fontPath;
        $this->fontMaxSize = (int) $maxSize;
        $this->fontMinSize = (int) $minSize;
        $this->fontLineHeight = (int) $lineHeight;
        $this->align = $align;
        $this->valign = $valign;
        
        if ($color){
            $this->fontColor = $color;
        }
        
        return true;
    }
    
    public function setText($text){
        $this->text = $text;
        return true;
    }
    
   
    public function execute(&$source){
        if ($this->box_line_left < 0) return false;
        parent::execute($source);
        
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
            $lineHeight = intval($this->fontLineHeight * $fontsize * ( ( $this->fontLineHeightCorrector['added'] + $this->fontLineHeightCorrector['ratio'] * $fontsize) / $fontsize));
            $lines = explode("\n", $textLined);
			$totalHeight = count($lines) * $lineHeight;
            if ($fontsize - $fontsizeStep >= $this->fontMinSize){ // Это не последняя попытка
                if ($totalHeight > $this->box_line_bottom - $this->box_line_top){ // Не вмещается по высоте
                    $textLined = '';
                    continue;
                }
            }
            break;
        }
		
        $topStartOffset = 0;
		if ($this->valign == 'center'){
			$topStartOffset = intval(($this->box_line_bottom - $this->box_line_top - $totalHeight) / 2);
		} elseif ($this->valign == 'bottom'){
			$topStartOffset = intval(($this->box_line_bottom - $this->box_line_top - $totalHeight) - $lineHeight/3);
		}
		
        $lines = explode("\n", $textLined);
        $lineHeight = intval($this->fontLineHeight * $fontsize * ( ($this->fontLineHeightCorrector['added'] + $this->fontLineHeightCorrector['ratio'] * $fontsize) / $fontsize));
        
        $color	= imagecolorallocate($source, $this->fontColor->getRed(), $this->fontColor->getGreen(), $this->fontColor->getBlue());
        
        for($i = 0; $i < count($lines); $i++){
            $line = $lines[$i];
            
            $textbox = imagettfbbox($fontsize, 0, $this->font, $line);
            $leftedbox = $textbox[0];
            $bottomed = $textbox[1];
            $righted = $textbox[4];
            $topped = $textbox[5];
            
            $topOffset = ($i+1) * $lineHeight + $topStartOffset;
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
    

    
    
}