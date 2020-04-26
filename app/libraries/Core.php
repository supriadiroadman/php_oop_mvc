<?php
/* 
* App Core Class
* Creates URL & loads core controller
* URL FORMAT - /controller/method/params
*/

class Core
{
  protected $currentController = 'Pages';
  protected $currentMethod = 'index';
  protected $params = [];

  public function __construct()
  {
    // print_r($this->getUrl()); //Array ( [0] => posts [1] => index [2] => params ) 
    $url = $this->getUrl();

    // Untuk Controller
    // Apakah ada file controller di folder app/controllers yang akan diakses
    if (file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
      // Jika ada, timpa currentController dengan file controller yang baru
      $this->currentController = ucwords($url[0]);
      //Hapus array ke 0
      unset($url[0]);
    }
    // Require controller
    require_once '../app/controllers/' . $this->currentController . '.php';

    // Instansiasi class currentController
    $this->currentController = new $this->currentController;

    // Untuk Methhod
    // Cek index 1 dari url
    if (isset($url[1])) {
      // Apakah ada method di objek currentController yang di instansiasi diatas (new $this->controller)
      if (method_exists($this->currentController, $url[1])) {
        // Jika methodnya ada timpa method dengan method baru
        $this->currentMethod = $url[1];

        // Hapus array ke 1
        unset($url[1]);
      }
    }

    // Untuk Parameter
    // Ambil paremeter dari url
    $this->params = $url ? array_values($url) : [];

    //Jalankan controller & method, serta kirimkan params jika ada
    call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
  }


  public function getUrl()
  {
    //parameter url di proses oleh public/.htaccess
    if (isset($_GET['url'])) {
      $url = rtrim($_GET['url'], '/');
      $url = filter_var($url, FILTER_SANITIZE_URL);
      $url = explode('/', $url);
      return $url;
    }
  }
}
