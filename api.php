
<?php


class removify{
  public $companies = [];
  public $response = [];


  public function callApi($params){
    $api_url ="https://service-dev.rmvfy.com/interview?{$params}";

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $api_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'x-token: removify',
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);


    return json_decode($response,true);

  }


  public function storeData($data){
    $this->response[] = $data;

  }

  public function getResponse(){
      return json_encode($this->response,true);
  }


  public function getCompanies(){
      return $this->companies;
  }

  public function setCompanies($arr){
      $companies = [];
      foreach($arr as $index=>$info){
        $companies[$info['company_id']] = $info['company_name'];
      }
      $this->companies = $companies;
  }


  public function __construct(){

    $companies = $this->callApi("source=companies");

    if(isset($companies['success']) && !empty($companies['data'])){
      $this->setCompanies($companies['data']);
    }
    $this->run();
  }

  public function run(){
    $db = $this->callApi("source=db");
    $companies = $this->getCompanies();
    if(isset($db['success']) && !empty($db['data'])){
        foreach($db['data'] as $key=>$value){
          $companyName = $companies[$value['company_id']] ?? null;
          $this->storeData([
            $value['id'],
            $value['name'],
            $value['title'],
            $companyName
          ]);

        }
    }
  }

}



$a = new removify;

print_r($a->getResponse());
