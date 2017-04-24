<html>
<head>
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<?php
    session_start();
    $password = 123456;
    if (isset($_POST['pass']) && ($_POST['pass'] == $password)):
        $_SESSION['pass'] = $_POST['pass'];
        if ($_SESSION['pass'] == $_POST['pass']): ?>
            <body>
            <div class="container">
                <div class="row">
                    <div class="col s12 m6 offset-m3">
                        <div class="card">
                            <div class="card-content">
                                <span class="card-title black-text">Get file from http://immobiliare.it/</span>
                                <form action="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' .  $_SERVER['HTTP_HOST'] ?>/parser/generator.php" method="post">
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="url" placeholder="Set Link" type="text" name="url"
                                                   value="<?php isset($_POST['url']) ? $_POST['url'] : '' ?>">
                                            <label for="url">URL</label>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <input type="submit" class="btn" value="GENERATE">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;
    else : ?>
        <body>
        <div class="container">
            <div class="row">
                <div class="col s12 m6 offset-m3">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title black-text">Protection:</span>
                            <form action="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' .  $_SERVER['HTTP_HOST'] ?>/parser/index.php" method="post">
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="pass" placeholder="Password" type="password" name="pass">
                                    </div>
                                </div>
                                <div class="card-action">
                                    <input type="submit" class="btn" value="Enter">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/css/materialize.min.css">
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/js/materialize.min.js"></script>
        </body>
</html>


<pre>
<?php
//require_once 'vendor/autoload.php';
//require_once 'DocumentReader.php';
//require_once 'DocumentWriter.php';
//
//$url = 'https://www.immobiliare.it/61071538-Vendita-Trilocale-via-della-Chiesa-22-Laglio.html';
//
//$document = new DocumentReader($url);
//
//print_r($document->getAttributes());
//
//$docWord = new DocumentWriter();
//$docWord->titleAndTable($document->getTitle(), $document->getAttributes(), $document->getPrice());
//$docWord->map($document->getMap());
//$docWord->description($document->getDescription());
//$docWord->images($document->getImages());
//$docWord->regards();
//$docWord->save();
//?>
<!--</pre>-->