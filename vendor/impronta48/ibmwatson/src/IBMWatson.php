<?php
declare(strict_types=1);

namespace impronta48;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * IBMWatson.
 *
 * @author  massimoi
 * @since   v0.0.1
 * @version v1.0.1 2024-09-10.
 * @global
 */
class IBMWatson
{
    private $apikey;
    private $url;
    private $http;
    private $client;

    /**
     * __construct.
     *
     * @author massimoi@impronta48.it
     * @since   v0.0.1
     * @version v1.0.0 2024-09-10.
     * @access  public
     * @param   string  $apikey - get the apikey on IBM Watson
     * @param   string  $url - get your personal URL on IBM Watson
     * @return  void
     */
    public function __construct($apikey, $url)
    {
        $this->apikey = $apikey;
        $this->url = $url;
        //We use Guzzle to create a default auth, valid for every call
        $this->client = new \GuzzleHttp\Client([
            'base_uri' =>  $url, 
            'auth' => ['apikey', $apikey],
        ]);
    }

    /**
     * Calls IBM Watson in order to Translate a full document
     *
     * @author  massimoi@impronta48.it
     * @since   v0.0.1
     * @version v1.0.0 2024-09-10.
     * @access  public
     * @param   string  $inputFile - name of the original file, or stream or resource
     * @param   string  $source - source language
     * @param   string  $target - target language
     * @return  Stream
     */
    public function translateDoc($inputFile, $source = 'it', $target = 'en')
    {
        //If I receive a string I try to open a file, otherwise I simply use the resource or stream
        if (is_string($inputFile)) {
            if (!file_exists($inputFile)) {
                throw new \Exception("File not found");
            }
            $stream = fopen($inputFile, 'r');
        } else {
            $stream = $inputFile;
        }

        $form_data = [
            [
                'name' => 'file',
                'contents' => $stream,
            ],
            [
                'name' => 'source',
                'contents' => $source,

            ],
            [
                'name' => 'target',
                'contents' => $target,
            ],
        ];

        //Con questa chiamata invio il documento a IBM
        $response = $this->client->post(
            '/v3/documents?version=2018-05-01',
            [
                'multipart' => $form_data,
            ]
        );

        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody()->getContents());
            $docId = $result->document_id;

            //Fa le chiamate in polling per vedere se la traduzione è pronta
            $this->waitForTranslation($docId);

            //Quando la traduzione è pronta la scarico al posto giusto e restituisco il nome
            return $this->downloadTranslation($docId, $target);
        } else {
            throw new \Exception($response->getReasonPhrase());
        }
    }

    /**
     * waitForTranslation.
     *
     * @author massimoi@impronta48.it
     * @since   v0.0.1
     * @version v1.0.0 2024-09-10.
     * @access  private
     * @param   mixed   $docId - id of the document returned by the translate API
     * @return  void
     */
    private function waitForTranslation($docId)
    {
        //Polling finchè la risorsa non è pronta o fallisce la traduzione
        do {
            $response = $this->client->get("/v3/documents/$docId?version=2018-05-01");

            if ($response->getStatusCode() == 200) {
                $result = json_decode($response->getBody()->getContents());
                $status = $result->status;
                if ($status == 'failed') {
                    throw new \Exception('Cannot translate this document, because: ' . $result->error);
                }
                if ($status != 'available') {
                    //Aspetta 20 centesimi di secondo prima di fare un'altra chiamata
                    usleep(200000);
                }
            }
        } while ($status != 'available');
    }

    /**
     * downloadTranslation.
     *
     * @author massimoi@impronta48.it
     * @since   v0.0.1
     * @version v1.0.0 2024-09-10.
     * @access  private
     * @param   mixed   $docId  - id of the document returned by the translate API
     * @return  resource
     */
    private function downloadTranslation($docId)
    {
        //Non scarico il documento per non avere problemi di dimensioni del file, ma genero uno stream
        $response = $this->client->get(
            "/v3/documents/$docId/translated_document?version=2018-05-01",
            [
                'stream' => true,
            ]
        );

        if ($response->getStatusCode() == 200) {
            //Preparo lo stream e lo scrivo nel filesystem di default
            //Ispirazione qui https://github.com/laravel/ideas/issues/1252
            $stream = $response->getBody();
            $resource = $stream->detach(); //chiude lo stream

            //Cancello il documento da tradurre
            $response = $this->client->delete("/v3/documents/$docId?version=2018-05-01");

            return $resource;
        } else {
            throw new \Exception($response->getReasonPhrase());
        }
    }

    /**
     * Calls IBM Watson in order to Translate Sentence
     *
     * @author  massimoi@impronta48.it
     * @since   v0.0.1
     * @version v1.0.0 2024-09-10.
     * @access  public
     * @param   mixed   $sentences  can be a single array or a string
     * @param   string  $source     source language
     * @param   string  $target     target language
     * @return  string   json result from the api (translated strings + wordcound + char count)
     */
    public function translateSentence($sentences, $source, $target): string
    {
            $data = [
                'text' => $sentences,
                //'model_id' => 'en-it',        //Questo si può usare se abbiamo delle translation memories
                'source' => $source,
                'target' => $target,
            ];
            $response = $this->client->post(
                '/v3/translate?version=2018-05-01',
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => json_encode($data),
                    //'debug' => true,
                ]
            );

            return (string)$response->getBody();
    }

    /**
     * Chiama IBM Watson Translate Itendify Language
     *
     * @author massimoi@impronta48.it
     * @since   v0.0.1
     * @version v1.0.0 2024-09-10.
     * @access  public
     * @param   string   $data - the string to be identified
     * @return  string
     */
    public function identifyLanguage(string $data): string
    {
        if (!is_string($data)) {
            throw  new \Exception('Data must be a string');
        }

        $response = $this->client->post(
            '/v3/identify?version=2018-05-01',
            [
              'headers' => ['Content-Type' => 'text/plain'],
              'body' => $data,
            ]
        );

        if ($response->getStatusCode() == 200) {
            $body = json_decode((string)$response->getBody(), true);
            if ($body['languages'][0]['confidence'] > 0.8) {
                return $body['languages'][0]['language'];
            }
        } else {
            return __('Impossibile determinare');
        }
    }
}
