<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class ProdutoServico {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function listarFuncaoColaborador($colaboradores_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select cps.produtos_servicos_pk, cps.colaboradores_pk, cps.ic_possui_treinamento, cps.ic_possui_certificado";
        $sql.="     ,group_concat(ps.ds_produto_servico)ds_produto_servico";
        $sql.="  from colaboradores_produtos_servicos cps ";
        $sql.="     inner join produtos_servicos ps on cps.produtos_servicos_pk = ps.pk";
        $sql.=" where 1=1 ";
        if($colaboradores_pk!=""){
            $sql.=" and colaboradores_pk = ".$colaboradores_pk;
        }
        $sql.=" order by produtos_servicos_pk asc ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows[0];
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listar_por_ds_produto_servico($ds_produto_servico,$ds_cbo){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_produto_servico ";
        $sql.="       ,ds_cbo ";
        $sql.="  from produtos_servicos ";
        $sql.=" where 1=1 ";
        if($ds_produto_servico != ""){
            $sql.=" and ds_produto_servico like '%".$ds_produto_servico."%' ";
        }
        if($ds_cbo != ""){
            $sql.=" and ds_cbo =".$ds_cbo;
        }
        $sql.=" order by ds_produto_servico asc ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarProdutosContrato($contratos_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="SELECT ps.pk,";
        $sql.=" ps.ds_produto_servico";
        $sql.=" FROM produtos_servicos ps";
        $sql.="     INNER JOIN contratos_itens ci ON ci.produtos_servicos_pk = ps.pk";
        $sql.=" WHERE ci.contratos_pk =".$contratos_pk;
        //$sql.=" AND ps.ic_status = 1";
        $sql.=" ORDER by  ps.ds_produto_servico";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarQualificacaoColaboradores($colaboradores_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = true; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        if($colaboradores_pk!=""){
            $sql ="";
            $sql.="select c.produtos_servicos_pk t_produtos_servicos_pk, c.colaboradores_pk t_colaboradores_pk, c.ic_possui_treinamento t_ic_possui_treinamento, c.ic_possui_certificado t_ic_possui_certificado,a.pk agenda_colaborador_padrao_pk";
            $sql.="  from colaboradores_produtos_servicos c ";
            $sql.="  LEFT JOIN agenda_colaborador_padrao a on c.produtos_servicos_pk = a.produtos_servicos_pk AND c.colaboradores_pk = a.colaboradores_pk";
            $sql.=" where 1=1 ";
            if($colaboradores_pk!=""){
                $sql.=" and c.colaboradores_pk = ".$colaboradores_pk;
            }
            $sql.=" group by c.produtos_servicos_pk ";
            $sql.=" order by c.produtos_servicos_pk asc ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            $retorno->data = $rows;
            $retorno->status = true;
        }

        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function adicionarProdutosServicosColaboradores($colaboradores_pk, $produtos_servicos_pk, $ic_possui_treinamento,$ic_possui_certificado){

        $fields = array();
        $fields['colaboradores_pk'] = $colaboradores_pk;
        $fields['produtos_servicos_pk'] = $produtos_servicos_pk;
        $fields['ic_possui_treinamento'] = $ic_possui_treinamento;
        $fields['ic_possui_certificado'] = $ic_possui_certificado;
        Util::execInsert("colaboradores_produtos_servicos", $fields,$this->pdo);

    }

    function excluirProdutosServicosColaboradoresPk($colaboradores_pk){
        Util::execDelete('colaboradores_produtos_servicos', ' colaboradores_pk='.$colaboradores_pk, $this->pdo);
    }


}
