<?php
require_once 'vendor/autoload.php';
require_once 'DocumentReader.php';
require_once 'DocumentWriter.php';

if (isset($_POST['url'])) {
    $url = $_POST['url'];

    if ( ($url=='') || (stristr($url, 'immobiliare')!=true) )
    {
        header("Location: " . $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['HTTP_HOST']."/parser/index.php");
    }
    $document = new DocumentReader($url);

    $docWord = new DocumentWriter();
    $docWord->titleAndTable($document->getTitle(), $document->getAttributes(), $document->getPrice());
    $docWord->map($document->getMap());
    $docWord->description($document->getDescription());
    $docWord->images($document->getImages());
    $docWord->regards();
    $docWord->save();


    $newDoc = 'file.docx';

    header('Pragma: no-cache');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename=document.docx;');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: '.filesize($newDoc));

    readfile($newDoc);

    session_write_close();
}


?>