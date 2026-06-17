<?php
namespace App\Controller;

use App\Model\Documento;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class SalvarDocumentoController extends BaseController {

    public function salvarDocumento(Request $request, Response $response, $args) {
        try{
            $diretorio = __DIR__ . '/../docs/';


            if (isset($_FILES) && isset($_FILES[0])){
                
                move_uploaded_file($_FILES[0]['tmp_name'], $diretorio. $_FILES[0]['name']);
                $retorno = (new Documento($this->pdo))->salvarDocumentoBd($_FILES,$diretorio);
                
                Json::run(true, $retorno->data, "Inserido com sucesso!");
            }
            else{
                Json::run(false, [], "Erro ao inserir!");
            }
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}