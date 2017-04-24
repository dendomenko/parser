<?php
require_once 'vendor/autoload.php';

class DocumentWriter
{
    private $phpWord = null;
    private $body = null;

    public function __construct()
    {
        $this->phpWord = new \PhpOffice\PhpWord\PhpWord();
        $this->header();
        $this->body = $this->phpWord->addSection();
        $this->footer();
    }

    private function header()
    {
        $head_section = $this->phpWord->addSection();
        $header = $head_section->addHeader();
        $header->addImage(
            'header.png',
            array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                'width' => 102,
                'height' => 67,
                'marginTop' => 10,
                'marginLeft' => 0,
                'borderStyle' => 'solid'
            )
        );

    }

    private function footer()
    {
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setBold(false);
        $fontStyle->setName('Calibri');
        $fontStyle->setSize(9);
        $footer_section = $this->phpWord->addSection();
        $footer = $footer_section->addFooter();
        $footer->addText(
            'Via Romagnosi 4, Varese, 21100',


            $fontStyle,
            array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
            )
        );
        $footer->addText(
            '+39.0332.1690363',
            $fontStyle,
            array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
            )
        );
        $footer->addText(
            'info@mirafortis.com',
            $fontStyle,
            array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
            )
        );
        $footer->addText(
            'mirafortis.com',
            $fontStyle,
            array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
            )
        );
    }

    public function save()
    {
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->phpWord, 'Word2007');
        $objWriter->save('file.docx');
    }

    public function titleAndTable($title, $attributes, $price)
    {
        $fontTitle = new \PhpOffice\PhpWord\Style\Font();
        $fontTitle->setBold(true);
        $fontTitle->setName('Calibri');
        $fontTitle->setSize(18);
        $this->body->addText(
            "\n" . $title . "\n", $fontTitle, array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER)
        );
        $fancyTableStyleName = 'Fancy Table';
        $fancyTableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
        $fancyTableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $fancyTableCellStyle = array('valign' => 'center');
        $fancyTableFontStyle = array('bold' => false);
        $this->phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
        $table = $this->body->addTable($fancyTableStyleName);
//        $table->addRow();
//
//        $table->addCell(2000, $fancyTableCellStyle)->addText('Цена:', $fancyTableFontStyle);
//        $table->addCell(2000, $fancyTableCellStyle)->addText('333', $fancyTableFontStyle);
//        $table->addCell(2000, $fancyTableCellStyle)->addText('', $fancyTableFontStyle);
        foreach ($attributes as $key => $attribute) {
////            if ($i % 2 == 0) {
                $table->addRow();

            $table->addCell(2000, $fancyTableCellStyle)->addText($key=='EMPTY' ? ' ' : $key, $fancyTableFontStyle);
            $table->addCell(2000, $fancyTableCellStyle)->addText($attribute, $fancyTableFontStyle);
            $table->addCell(2000, $fancyTableCellStyle)->addText(' ', $fancyTableFontStyle);
//            $table->addRow();
        }
    }

    public function map($map)
    {
        $fontBold14 = new \PhpOffice\PhpWord\Style\Font();
        $fontBold14->setBold(true);
        $fontBold14->setName('Calibri');
        $fontBold14->setSize(14);
        $this->body->addText(
            "\n1. Расположение\n", $fontBold14, array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START)
        );
        $this->body->addImage($map, array(
            'width' => 650
        ));
    }

    public function description($description)
    {
        $fontBold14 = new \PhpOffice\PhpWord\Style\Font();
        $fontBold14->setBold(true);
        $fontBold14->setName('Calibri');
        $fontBold14->setSize(14);

        $this->body->addText(
            "\n2. Состояние и характеристики\n", $fontBold14, array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START)
        );

        $font12 = new \PhpOffice\PhpWord\Style\Font();
        $font12->setBold(false);
        $font12->setName('Calibri');
        $font12->setSize(12);

        $this->body->addText(
            $description, $font12, array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START)
        );
        $this->body->addPageBreak();
    }

    public function images($images)
    {
        $fontBold14 = new \PhpOffice\PhpWord\Style\Font();
        $fontBold14->setBold(true);
        $fontBold14->setName('Calibri');
        $fontBold14->setSize(14);

        $this->body->addText(
            "\n3. Фото\n", $fontBold14, array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START)
        );

        foreach ($images as $image) {
            $this->body->addImage($image, array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
            ));
            $this->body->addText(
                "\n", $fontBold14, array(
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER)
            );
        }
    }

    public function regards()
    {
        $font12 = new \PhpOffice\PhpWord\Style\Font();
        $font12->setBold(false);
        $font12->setName('Calibri');
        $font12->setSize(12);

        $this->body->addText(
            "\nС уважением,\n", $font12, array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START)
        );

        $this->body->addText(
            "\nКоманда Мирафортис\n", $font12, array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START)
        );

        setlocale(LC_ALL, "rus");
        $date = strftime(" %d/%m/%Y", time());

        $this->body->addText(
            "\nВарезе, " . $date, $font12, array(
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END)
        );
    }
}