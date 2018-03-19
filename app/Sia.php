<?php
/**
 * Created by PhpStorm.
 * User: Surya
 * Date: 30/06/2017
 * Time: 9:10
 */

namespace App;

use Illuminate\Support\Facades\Input;

class Sia {
    protected $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    public function getSubject($faculty, $nim)
    {
        $response = $this->client->get('http://dirmahasiswa.usu.ac.id/index.php/mahasiswa/getmhs/'.$faculty.'/'.$nim);
        $json = json_decode($response->getBody());

        return $json;
    }

    
}