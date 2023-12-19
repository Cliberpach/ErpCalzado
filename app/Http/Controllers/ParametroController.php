<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ParametroController extends Controller
{
    public function apiRuc($ruc)
    {
        // $parametro = consultaRuc();
        // $http = $parametro->http.$ruc.$parametro->token;
        // $request = Http::get($http);
        // $resp = $request->json();

        $url = "https://apiperu.dev/api/ruc/".$ruc;
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = 'c36358c49922c564f035d4dc2ff3492fbcfd31ee561866960f75b79f7d645d7d';
        $response = $client->get($url, [
            'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => "Bearer {$token}"
                    ]
        ]);
        $estado = $response->getStatusCode();
        $data = $response->getBody()->getContents();
        // $arreglo = [
        //     'success' => true,
        //     'data' => $data,
        // ];

        // return response()->json($arreglo);
        return $data;
    }
    public function apiDni($dni)
    {
        // $parametro = consultaDni();
        // $http = $parametro->http.$dni.$parametro->token;
        // $request = Http::get($http);
        // $resp = $request->json();
        // return $resp;

        $url = "https://apiperu.dev/api/dni/".$dni;
            $client = new \GuzzleHttp\Client(['verify'=>false]);
            $token = 'c36358c49922c564f035d4dc2ff3492fbcfd31ee561866960f75b79f7d645d7d';
            $response = $client->get($url, [
                'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'Authorization' => "Bearer {$token}"
                        ]
            ]);
        $estado = $response->getStatusCode();
        $data = $response->getBody()->getContents();

        // $arreglo = [
        //     'success' => true,
        //     'data' => $data,
        // ];

        // return response()->json($arreglo);
        return $data;
    }

    public function notifications()
    {
        refreshNotifications();
        $notifications = Auth::user()->unreadNotifications;

        foreach($notifications as $notify)
        {
            $data = $notify->data;
            $data['time'] = timeago($data['body']['updated_at']);
            $notify->data = $data;
        }
        return response()->json([
            'notifications' => $notifications
        ]);
    }
}
