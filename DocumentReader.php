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
    }

    public function getDetails()
    {
        return $this->detailsRu;
    }

    public function getTitle()
    {
        $titleElem = $this->crawlerRu->filter('div#sx  strong.h3')->text();
        $titleText = explode(' ',trim($titleElem))[0];
        $region = $this->crawlerRu->filter('div.sm_a_dettaglio')->last()->text();
        $region = explode(' ', trim($region));
        $region = array_pop($region);
        $code= '';
        $attrs = $this->getAttributes();
        foreach ($attrs as $i => $attribute)
        {
            if(isset($attribute['key']) && stristr($attribute['key'], 'Код'))
            {
                $code = $attrs[$i+1]['value'];
                break;
            }

        }
        return 'Объект ' . $code . '. ' . $titleText . ' в ' . ucfirst($region);
    }

    public function getAttributes()
    {
        $table = $this->crawlerRu->filter('div.details > table');
        $result = [];
        foreach ($table->children() as $i => $content) {
            $crawlerTable = new Crawler($content);
            $tr = $crawlerTable->filter('tr');
            foreach ($tr->children() as $j => $td) {
                $result[] = array(
                    $td->attributes->length == 1 ? 'key' : 'value' =>
                        trim($td->textContent)
                );
            }
        }

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
        $translator = new TranslateClient('it', 'ru');
        $textRu = $translator->translate($textIt);
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

}