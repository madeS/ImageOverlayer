<?

header('Content-Type: text/html; charset=utf-8');

require_once './../vendors/ImageOverlayer/ImageOverlayer.php';

$background = rtrim($img = $_SERVER['DOCUMENT_ROOT'],'/') . '/imgs/'. 'test1.png';
$dirSave = rtrim($img = $_SERVER['DOCUMENT_ROOT'],'/') . '/imgs/output/';
$fontFile = rtrim($img = $_SERVER['DOCUMENT_ROOT'],'/') . '/fonts/'.'georgia-italic.ttf';
$text = 'Что это за запах в этой комнате? Разве ты не заметил, Брик? Разве ты не заметил сильный и неприятный запах лживости в этой комнате? Нет ни одного запаха сильнее, чем запах лживости. Ты можешь его почувствовать. Он пахнет смертью.'
;
try {
    
    $overlayer = new ImageOverlayer();
    $overlayer->setCanvas($background);
    
    $textTask1 = $overlayer->createTextTask();
    $textTask1->setBox('20%','30%','60%','40%');
    $textTask1->setFont($fontFile,30,8,2.7,'left');
    $textTask1->setText($text);
    
    $textTask1->activeDebug();
    
    $overlayer->overlay(array(
        $textTask1
    ));
    $overlayer->save($dirSave, 'out-{#time}-{#rand}', array('out_ext' => 'jpg'));
    
    $overlayer->destroy();
    //unset($overlayer);
    
} catch(ImageOverlayException $ex){
    echo 'ERROR:'. $ex->getMessage();
}

echo '<pre>'.print_r($overlayer,true).'</pre>';

