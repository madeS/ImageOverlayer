<?

header('Content-Type: text/html; charset=utf-8');

require_once './../vendors/ImageOverlayer/ImageOverlayer.php';

$background = rtrim($img = $_SERVER['DOCUMENT_ROOT'],'/') . '/imgs/'. 'test1.png';
$dirSave = rtrim($img = $_SERVER['DOCUMENT_ROOT'],'/') . '/imgs/output/';
$fontFile = rtrim($img = $_SERVER['DOCUMENT_ROOT'],'/') . '/fonts/'.'georgia-italic.ttf';
$text = 'Что это за запах в этой комнате? Разве ты не заметил, Брик? Разве ты не заметил сильный и неприятный запах лживости в этой комнате? Нет ни одного запаха сильнее, чем запах лживости. Ты можешь его почувствовать. Он пахнет смертью.'
;
$quoteImage = rtrim($img = $_SERVER['DOCUMENT_ROOT'],'/') . '/imgs/'. 'profile_nopicture.png';
$author = 'Чья-то пословица';
try {
	$overlayer = new ImageOverlayer();
	$overlayer->setCanvas($background);
	$overlayArr = array();
	

	$rectTask1 = $overlayer->createRectTask();
	$rectTask1->setBox('0%', '70%', '100%', '30%');
	$rectTask1->setColor(new ImageOverColor(0x00,0x00,0x00,70));
	$overlayArr[] = $rectTask1;			
	

	$textTask1 = $overlayer->createTextTask();
	$textTask1->setBox('15%','20%','70%','45%');
	$textTask1->setFont($fontFile,26,6,2.5,'left','center', new ImageOverColor(0x41,0x41,0x41));
	$textTask1->setText($text);
	//$textTask1->activeDebug();
	$overlayArr[] = $textTask1;
	
	if ($quoteImage){
		$imageTask1 = $overlayer->createImageTask();
		$imageTask1->setBox('8%', '70%', '20%', '30%');
		$imageTask1->setImageInBox($quoteImage, 'cut', 'cut', null);
		//$imageTask1->activeDebug();
		//$imageTask1->addImageBorder(2, new ImageOverColor(0x88,0x88,0x88));
		//$imageTask1->addImageBorder(4, new ImageOverColor(0xaa,0xaa,0xaa));
		//$imageTask1->addImageBorder(8, new ImageOverColor(0x22,0x22,0x22));
		$overlayArr[] = $imageTask1;
	}
	if ($author){
		$textTask3 = $overlayer->createTextTask();
		$textTask3->setBox('32%','70%','60%','15%');
		$textTask3->setFont($fontFile,23,10,2.5,'center','top', new ImageOverColor(0xce,0xce,0xce));
		$textTask3->setText($author);
		//$textTask3->activeDebug();
		$overlayArr[] = $textTask3;
	}

	
	$overlayer->overlay($overlayArr);
	$resultImage = $overlayer->save($dirSave, 'out-{#time}-{#rand6}', array('out_ext' => 'jpg'));

	$overlayer->destroy();
	unset($overlayer);

} catch(ImageOverlayException $ex){
	$error = 'ERROR:'. $ex->getMessage();
	echo $error;
}



echo '<pre>'.print_r($overlayer,true).'</pre>';

