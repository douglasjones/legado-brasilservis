<?php

namespace App\Controller;

use App\Model\WebPontoWhatsApp;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class WebPontoWhatsAppController extends BaseController {


    public function webPontoWhatsApp(Request $request, Response $response, $args){
        try{

            $data = (object)$request->getParsedBody();
          
            $webPontoWhatsApp = new WebPontoWhatsApp($this->pdo);

            $from = $data->from;
            $textoRecebido = $data->texto_recebido;
            $telRecebido = $data->telRecebido;
            $tipoMensagem = $data->tipoMensagem;
            $ds_link = $data->ds_link;
            $latitude = $data->latitude;
            $longitude = $data->longitude;

            
            $query = $webPontoWhatsApp->tratarMensagem($from,$textoRecebido,$telRecebido,$tipoMensagem,$ds_link,$latitude,$longitude);
            

            $json_data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
            
             return $response->withJson((object)[
                'sucess' => $json_data
            ], 200, []);

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}
