<?php
/**
 * Created by PhpStorm.
 * User: Surya
 * Date: 30/06/2017
 * Time: 9:10
 */

namespace App;

use Illuminate\Support\Facades\Input;

class Simsdm {
    protected $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    public function userAuth($username, $password)
    {
        $params = [
            'form_params' => [
                'nip'      => $username,
                'password' => $password
            ]
        ];
        $response = $this->client->post('http://api.usu.ac.id/1.0/users/auth', $params);
        $json = json_decode($response->getBody());
        foreach ($json as $key => $item)
        {
            $item = json_decode(json_encode($item), true);
            $json[$key] = $item;
        }

        return $json;
    }

    public function facultyAll()
    {
        $response = $this->client->get('http://api.usu.ac.id/1.0/faculties');
        $json = json_decode($response->getBody());
        foreach ($json as $key => $item)
        {
            $item = json_decode(json_encode($item), true);
            $json[$key] = $item;
        }

        return $json;
    }

    public function unitAll()
    {
        $response = $this->client->get('http://api.usu.ac.id/1.0/units');
        $json = json_decode($response->getBody());
        foreach ($json as $key => $item)
        {
            $item = json_decode(json_encode($item), true);
            $json[$key] = $item;
        }

        return $json;
    }

    public function studyProgram($faculty)
    {
        $response = $this->client->get('http://api.usu.ac.id/1.0/faculties/' . $faculty . '/study_programs');
        $json = json_decode($response->getBody());
        foreach ($json as $key => $item)
        {
            $item = json_decode(json_encode($item), true);
            $json[$key] = $item;
        }

        return $json;
    }

    public function getEmployee($identity)
    {
        $response = $this->client->get('http://api.usu.ac.id/1.0/users/' . $identity);
        $json = json_decode($response->getBody());
        return $json;
    }

    public function searchEmployee($input, $limit)
    {
        $response = $this->client->get('http://api.usu.ac.id/1.0/users/search?query=' . $input . '&limit=' . $limit);
        $json = json_decode($response->getBody());

        return $json;
    }
}