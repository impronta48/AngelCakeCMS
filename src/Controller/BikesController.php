<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\View\Exception\MissingTemplateException;
use Psr\Log\LogLevel;
use Cake\Utility\Text;
use Cake\Http\Exception\NotFoundException;
use Cake\Core\Configure;
use Cake\Routing\Router;

/**
 * Destinations Controller
 *
 * @property \App\Model\Table\DestinationsTable $Destinations
 *
 * @method \App\Model\Entity\Destination[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BikesController extends AppController
{

  	public function initialize(): void {
      parent::initialize();
      $this->Authentication->allowUnauthenticated(['sales', 'checkout']);
    }

  public $paginate = [
    'limit' => 50,
  ];


  /**
   * Index method
   *
   * @return \Cake\Http\Response|void
   */
  public function index()
  {
    
  }

  public function sales() {
    
    if ($this->request->is('get') && $this->request->is('json')) {
      
      $prenotaUrl = Configure::read('prenotaUrl');
      $this->set('prenotaUrl', $prenotaUrl);
      $this->viewBuilder()->setOption('serialize', 'prenotaUrl');
      return;
    }

    
  }

  public function checkout() {
    if ($this->request->is('get') && $this->request->is('json')) {
      
      $prenotaUrl = Configure::read('prenotaUrl');
      /*
      $appPagoMerchantId = Configure::read('APPPAGO_MERCHANT_ID');
      $motifSessionToken = Configure::read('MOTIF-SESSION-TOKEN');
      $appPagoServerApi = Configure::read('APPPAGO_SERVER_API');
      $callbackUrl = Router::url("/bikes/bikes_sales_result", true);
      */
      $this->set('prenotaUrl', $prenotaUrl);
      //$this->set('appPagoMerchantId', $appPagoMerchantId);
      //$this->set('motifSessionToken', $motifSessionToken);
      //$this->set('appPagoServerApi', $appPagoServerApi);
      //$this->set('callbackUrl', $callbackUrl);
      //$this->viewBuilder()->setOption('serialize', ['prenotaUrl', 'appPagoMerchantId', 'motifSessionToken', 'appPagoServerApi', 'callbackUrl']);
      $this->viewBuilder()->setOption('serialize', ['prenotaUrl']);
      return;
    }
  }

  public function bikes_sales_result() {
    $data = $this->request->getData();
  }
}
