<?php

require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Stichoza\GoogleTranslate\TranslateClient;

class DocumentReader
{
    private $detailsRu = null;
    private $clientRu = null;
    private $crawlerRu = null;
    private $detailsIt = null;
    private $clientIt = null;
    private $crawlerIt = null;
    private $link = null;
    private $translator = null;


    // Make table for semen
    private $fields = ['Цена', 'Площадь', 'Количество комнат',
        'Ванные комнаты', 'Состояние',
        'Этаж', 'Гараж', 'Сад / терраса / балкон',
        'Вид на воду', 'ПУСТАЯ СТРОКА', 'Отопление',
        'Отопление', 'Год постройки',
    ];

    public function __construct($link)
    {
        $this->link = substr($link, strrpos($link, '/') + 1);

        $this->clientRu = new Client(
            [
                'base_uri' => 'http://nedvizhimost-italii.immobiliare.it/'
            ]);
        $this->detailsRu = $this->clientRu->get($this->link);
        $this->detailsRu = $this->detailsRu->getBody()->getContents();
        $this->crawlerRu = new Crawler($this->detailsRu);

        $this->clientIt = new Client(
            [
                'base_uri' => 'http://www.immobiliare.it/'
            ]
        );
        $this->detailsIt = $this->clientIt->get($this->link);
        $this->detailsIt = $this->detailsIt->getBody()->getContents();
        $this->crawlerIt = new Crawler($this->detailsIt);
        $this->translator = new TranslateClient('it', 'ru');
    }

    public function getDetails()
    {
        return $this->detailsRu;
    }

    public function getTitle()
    {
        $titleElem = $this->crawlerRu->filter('div#sx  strong.h3')->text();
        $titleText = explode(' ', trim($titleElem))[0];

        $region = $this->crawlerIt->filter('h1.h4')->last()->text();
        return 'Объект № ' . $titleText . ' в ' . ucfirst($region);
    }

    public function getAttributes()
    {
//        $table = $this->crawlerRu->filter('div.details > table');
//        $result = [];
//        foreach ($table->children() as $i => $content) {
//            $crawlerTable = new Crawler($content);
//            $tr = $crawlerTable->filter('tr');
//            foreach ($tr->children() as $j => $td) {
//                $result[] = array(
//                    $td->attributes->length == 1 ? 'key' : 'value' =>
//                        trim($td->textContent)
//                );
//            }
//        }

        $price = $this->getPrice();
        $price = trim(ltrim($price, '€')) . ' €';

        $sqare = $this->crawlerIt->filter('div.feature-action__features > ul.list-inline > li > div > strong')->last();
        $sqare = $sqare->count() > 0 ? $sqare->text() : '' ;

        $room = $this->crawlerIt->filter('div.feature-action__features > ul.list-inline > li > div > i.rooms');
        $room = $room->count() > 0 ?  $room->previousAll()->filter('strong')->text() : '' ;

        $bath = $this->crawlerIt->filter('div.feature-action__features > ul.list-inline > li > div > i.bathrooms');
        $bath = $bath->count() > 0 ?  $bath->previousAll()->filter('strong')->text() : '' ;

        $month_costs = $this->crawlerIt->filter('div.section-data > dl.col-xs-12 > dt.col-sm-7');
        if ($month_costs->count() > 0 && $month_costs->text() == 'Spese condominio') {
            $costs = $month_costs->nextAll()->text();
        }
        $tables = $this->crawlerIt->filter('div.section-data > dl.col-xs-12');
//        print_r($tables);
        $tmp = [];
        $tables = $tables->each(function (Crawler $node, $i) {
            return $node->children()->each(function (Crawler $val, $i) {
                return strip_tags($val->text());
            });
        });

        foreach ($tables as $table) {
            foreach ($table as $i => $val) {
//            print_r($table->textContent);
                if ($val == 'Piano')
                    $floor = $this->translator->translate($table[$i + 1]);
                if ($val == 'Anno di costruzione')
                    $year = $this->translator->translate($table[$i + 1]);
                if ($val == 'Riscaldamento')
                    $warm = $this->translator->translate($table[$i + 1]);
                if ($val == 'Stato')
                    $state = $this->translator->translate($table[$i + 1]);
                if ($val == 'Climatizzatore')
                    $condi = $this->translator->translate($table[$i + 1]);
                if ($val == 'Box e posti auto')
                    $garage = $this->translator->translate($table[$i + 1]);
            }
        }

        $charact = $this->crawlerIt->filter('div.section-data > div.col-xs-12 >span.label-gray');

        $charact = $charact->each(function (Crawler $text) {
            return $this->translator->translate($text->text());
        });

//        print_r($charact);

        $result = array('Цена' => isset($price) ? $price : '',
            'Площадь' => isset($sqare)? $sqare . 'кв.м': '',
            'Комнаты' => isset($room)? $room : '',
            'Ванные' => isset($bath)? $bath : '',
            'Состояние' => isset($state) ? $state : '',
            'Этаж' => isset($floor)? $floor : '',
            'Гараж' => isset($garage)? $garage :'',
            'Сад / терраса / балкон' => '',
            'Вид на воду' => '',
            'EMPTY' => '',
            'Отопление' => isset($warm)? $warm : '',
            'Кондиционер' => isset($condi)? $condi : '',
            'Год постройки' => isset($year)? $year : '',
            'Жилищные расходы' =>isset($costs) ? $costs : ''
        );

        foreach ($charact as $key => $value) {
            $char = $this->mb_ucfirst($value);
            $result[$char]='Да';
        }

//        foreach ($table->children() as $i => $content) {
//            $crawlerTable = new Crawler($content);
//            $tr = $crawlerTable->filter('li')->first();
//            print_r($tr);
//        }

        return $result;
    }

    public function getPrice()
    {
        $titleElem = $this->crawlerRu->filter('div#prezzoImmobile strong.h3')->text();
        $price = explode(':', $titleElem);
        return trim($price[1]);
    }

    public function getDescription()
    {
        $textIt = $this->crawlerIt->filter('div.description-text')->text();
        $textRu = $this->translator->translate($textIt);
        $textRu = trim(preg_replace('/\s+/', ' ', $textRu));
        return $textRu;
    }

    public function getImages()
    {
        $images = [];
        $imageBlock = $this->crawlerRu->filter('div#thumbs-mask');
        foreach ($imageBlock->children() as $div) {
            $crawlerDiv = new Crawler($div);
            $imgId = $crawlerDiv->filter('div.box_thumb')->attr('data-idimg');
            if ($imgId != '')
                $images[] = "http://pic.im-cdn.it/image/$imgId/print.jpg";
        }
        return $images;
    }

    public function getMap()
    {
        $imageData = $this->crawlerIt->filter('div#maps-container > div.image-placeholder');
        $imageData->attr('data-background-image');
        return $imageData->attr('data-background-image');
    }

    private function mb_ucfirst($string, $encoding='UTF-8')
    {
        $strlen = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, $strlen - 1, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }

//    private function get_curl($url)
//    {
//        if (function_exists('curl_init')) {
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            curl_setopt($ch, CURLOPT_HEADER, 0);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ru,en-us'));
//            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//            $output = curl_exec($ch);
//            echo curl_error($ch);
//            curl_close($ch);
//            return $output;
//        } else {
//            return file_get_contents($url);
//        }
//    }
//
//    private function translate_text($text, $lan='it-ru')
//    {
//        $key = "trnsl.1.1.20170113T093134Z.41ceccae89b8f83a.c14cf30e732402dfae1377ccce68a162057f16c7";
//        $res = $this->get_curl("https://translate.yandex.net/api/v1.5/tr.json/translate?key=$key&text=$text&lang=$lan");
//        return json_decode($res)->text[0];
////        print_r($res);
//    }

}