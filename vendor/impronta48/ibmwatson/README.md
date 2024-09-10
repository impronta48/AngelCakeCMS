# IBM Watson PHP Interface
This library allows easy interfacing with IBM Watson tranlsation services.

## Requirements
- PHP 7.1+
- GuzzleHttp 6+

This library is well integrated in CakePHP

## Getting Started (Installation)
1) Create a (free) account on IBM Watson (https://www.ibm.com/watson/services/language-translator/)
2) Get your **API KEY** and your **URL**
3) Include the library in your project with composer
```
    composer require impronta48/IBMWatson
```
4) Use the library in your project

## Usage
This library offers 4 functionalities
- Translate a set of sentences
- Identify the language of a text
- Translate a full document (such as Word, ODT, Excel, PowerPoint, PDF, etc.) - see here for the full list https://cloud.ibm.com/docs/services/language-translator/translating-documents.html


### Translate a set of sentences
```php
<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use impronta48\IBMWatson;

  $w = new IBMWatson($this->apikey, $this->url);
  $resJson = $w->translateSentence('hello', 'en', 'it');
  $resJson = $w->translateSentence(['hello','goodbye'], 'en', 'it');

  //Convert the Json String in a Object
  $res = json_decode($resJson);

  echo $res->translations[0]->translation;
  // returns ciao
  echo $res->word_count;
  // returns 1
  echo $res->character_count;
  // returns 5
```

### Identify the language of a string
```php
  $w = new IBMWatson($this->apikey, $this->url);
  $lang = $w->identifyLanguage('Mi chiamo Massimo e scrivo molto bene in italiano');
  echo $lang;
  // returns 'it'
```

### Tranlsate a full file
```php
  $w = new impronta48\IBMWatson($this->apikey, $this->url);
  $inputFile = __DIR__ . '/test_it.docx';
  $outputFile = __DIR__ . '/test_en.docx';
  $translatedDoc = $w->translateDoc($inputFile, 'it', 'en');
  $dest = fopen($outputFile, 'w');
  stream_copy_to_stream($translatedDoc, $dest);
```

## Support
Massimo INFUNTI
http://impronta48.it/
