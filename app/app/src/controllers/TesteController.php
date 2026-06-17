<?php

namespace App\Controller;

use App\Utils\Json;
use App\Model\Log;
use App\Model\TipoOcorrencia;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class TesteController extends BaseController {

    public function teste(Request $request, Response $response, $args){
        try{
            // Endereços para teste
            $endereco1 = "Assaí Atacadista, 1697, Avenida Santo Amaro, Solar de Caldas Novas, Caldas Novas, Região Geográfica Imediata de Caldas Novas-Morrinhos, Região Geográfica Intermediária de Itumbiara, Goiás, Região Centro-Oeste, 75696-058, Brasil";
            $endereco2 = "Atacadao, Rua das Dracenas- Araguaína";

            // Obter as coordenadas dos dois endereços
            $location1 = $this->getCoordinates($endereco1);
            $location2 = $this->getCoordinates($endereco2);
            

            // Verifica se as coordenadas foram obtidas com sucesso
            if ($location1 && $location2) {
                // Calcula a distância entre os dois pontos
                $distancia = $this->calcularDistancia($location1['lat'], $location1['lon'], $location2['lat'], $location2['lon']);
                echo "A distância entre $endereco1 e $endereco2 é de " . round($distancia, 2) . " km.";
            } else {
                echo "Não foi possível obter as coordenadas de um ou ambos os endereços.";
            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function getCoordinates($address) {
        $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json&limit=1";
    
        // Define um cabeçalho User-Agent obrigatório pelo Nominatim
        $options = array(
            'http' => array(
                'header' => "User-Agent: Mozilla/5.0\r\n" // User-Agent necessário para Nominatim
            )
        );
        $context = stream_context_create($options);
    
        // Faz a requisição à API Nominatim
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
    
        // Se a resposta contiver dados válidos, retorna a latitude e longitude
        if (!empty($data)) {
            return [
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon']
            ];
        } else {
            return false;
        }
    }
    public function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
        $R = 6371; // Raio da Terra em km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distancia = $R * $c; // Distância em km
        return $distancia;
    }    

    
}