<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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


    public function restaurarStock(){
        DB::beginTransaction();

        try {
            DB::update('UPDATE producto_color_tallas 
                        SET stock_logico = stock');

            DB::commit();
            return response()->json(['success'=>true,'message'=>'STOCK EMPAREJADO']);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['success'=>false,'message'=>'ERROR EN EL SERVIDOR','exception'=>$th->getMessage()]);
        }

    }

    public function descargarBD(){
        try {
            
            $fechaActual = Carbon::now()->format('Y-m-d');
            $ubicacionArchivoTemporal = getcwd() . DIRECTORY_SEPARATOR . "Respaldo_" . uniqid(date("Y-m-d") . "_", true) . ".sql";
    
            $rutaMysqldump = '/usr/bin/mysqldump'; 
            //$rutaMysqldump = 'D:\xampp\mysql\bin\mysqldump.exe'; 
            $salida = "";
            $codigoSalida = 0;

            $comando = sprintf("%s --user=\"%s\" --password=\"%s\" %s > %s", 
            $rutaMysqldump, 
            env("DB_USERNAME"), 
            env("DB_PASSWORD"), 
            env("DB_DATABASE"), 
            $ubicacionArchivoTemporal);

            exec($comando, $salida, $codigoSalida);
            if ($codigoSalida !== 0) {
                return back()->with('toastrError', "Código de salida distinto de 0, se obtuvo código (" . $codigoSalida .' - '.$salida. "). Revise los ajustes e intente de nuevo");
            }

            $fechaActual            = now()->format('Y-m-d');
            $nombreBaseDatos        = env('DB_DATABASE');
            $nombreArchivo          = $fechaActual . '_' . $nombreBaseDatos . '.sql';
                    
            return response()->download($ubicacionArchivoTemporal, $nombreArchivo)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            return back()->with('toastrError', 'Error al descargar la copia de seguridad: ' . $e->getMessage());
        }

    }
}
