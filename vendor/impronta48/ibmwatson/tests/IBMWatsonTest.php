<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use impronta48\IBMWatson;
use PHPUnit\Framework\TestCase;

final class IBMWatsonTest extends TestCase
{
    protected $apikey;
    protected $url;

    protected function setUp(): void
    {
        global $settings;
        $this->apikey = $settings['apikey'];
        $this->url = $settings['url'];
    }

    public function testCanTranslateHello(): void
    {
        $w = new IBMWatson($this->apikey, $this->url);
        $resJson = $w->translateSentence('hello', 'en', 'it');
        $res = json_decode($resJson);
        $this->assertEquals($res->translations[0]->translation, 'ciao');
        $this->assertEquals($res->word_count, 1);
        $this->assertEquals($res->character_count, 5);
    }

    public function testCanIdentifyItalian(): void
    {
        $w = new IBMWatson($this->apikey, $this->url);
        $lang = $w->identifyLanguage('Mi chiamo Massimo e scrivo molto bene in italiano');
        $this->assertEquals($lang, 'it');
    }

    public function testTranslateDocWithFile(): void
    {
        $w = new impronta48\IBMWatson($this->apikey, $this->url);
        $inputFile = __DIR__ . '/test_it.docx';
        $outputFile = __DIR__ . '/test_en.docx';

        $this->assertFileExists($inputFile);
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $translatedDoc = $w->translateDoc($inputFile, 'it', 'en');

        $dest = fopen($outputFile, 'w');
        stream_copy_to_stream($translatedDoc, $dest);

        fclose($dest);
        fclose($translatedDoc);

        $this->assertFileExists($outputFile);
    }

    public function testTranslateDocWithResource(): void
    {
        $w = new impronta48\IBMWatson($this->apikey, $this->url);
        $inputFile = __DIR__ . '/test_it.docx';
        $outputFile = __DIR__ . '/test_fr.docx';

        $this->assertFileExists($inputFile);
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }
        $inputResource = fopen($inputFile, 'r');
        $translatedDoc = $w->translateDoc($inputResource, 'it', 'fr');

        $dest = fopen($outputFile, 'w');
        stream_copy_to_stream($translatedDoc, $dest);

        fclose($dest);
        fclose($translatedDoc);

        $this->assertFileExists($outputFile);
    }
}
