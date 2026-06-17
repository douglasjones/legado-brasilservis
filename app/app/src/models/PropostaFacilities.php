<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class PropostaFacilities {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function excluir($pk){
        Util::execDelete('propostas_facilities_itens'," propostas_facilities_pk = ".$pk,$this->pdo);
        Util::execDelete('propostas_facilities'," pk = ".$pk,$this->pdo);
    }
    public function salvar($propostas_facilities, $ic_versao){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_numero_proposta'] = $propostas_facilities['ds_numero_proposta'];
        $fields['leads_pk'] = $propostas_facilities['leads_pk'];
        $fields['ic_tipo_proposta'] = $propostas_facilities['ic_tipo_proposta'];
        $fields['produtos_servicos_pk'] = $propostas_facilities['produtos_servicos_pk'];
        $fields['ds_qtde_efetivo'] = $propostas_facilities['ds_qtde_efetivo'];
        $fields['ds_qtde_hr_semanais'] = $propostas_facilities['ds_qtde_hr_semanais'];
        $fields['ic_escala'] = $propostas_facilities['ic_escala'];
        $fields['convencao_coletiva_pk'] = $propostas_facilities['convencao_coletiva_pk'];
        if($propostas_facilities['dt_base_categoria']!=""){
            $fields['dt_base_categoria'] = Util::DataYMD($propostas_facilities['dt_base_categoria']);
        }
        $fields['ds_num_registro_mte'] = $propostas_facilities['ds_num_registro_mte'];
        $fields['vl_salario_piso_categoria'] = floatval($propostas_facilities['vl_salario_piso_categoria']);
        $fields['vl_total_proposta'] = floatval($propostas_facilities['vl_total_proposta']);
        $fields['vl_total_percentual_proposta'] = $propostas_facilities['vl_total_percentual_proposta'];
        $fields['usuario_responsavel_comercial_pk'] = $propostas_facilities['usuario_responsavel_comercial_pk'];
        $fields['dt_envio_da_proposta'] = $propostas_facilities['dt_envio_da_proposta'];
        $fields['dt_previsao_fechamento'] = $propostas_facilities['dt_previsao_fechamento'];
        $fields['dt_fechamento'] = $propostas_facilities['dt_fechamento'];
        $fields['dt_cancelamento'] = $propostas_facilities['dt_cancelamento'];
        $fields['obs_motivo_cancelamento'] = $propostas_facilities['obs_motivo_cancelamento'];
        $fields['obs_proposta'] = $propostas_facilities['obs_proposta'];
        $fields['contratos_pk'] = $propostas_facilities['contratos_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($propostas_facilities['pk']  == ""){
            if($ic_versao != ''){

                $ds_versao = $this->verificarVersao($propostas_facilities['proposta_facilities_pai_pk']);

                $fields['ds_versao'] = $ds_versao;
                $fields['proposta_facilities_pai_pk'] = $propostas_facilities['proposta_facilities_pai_pk'];
            }else{
                $fields['ds_versao'] = 0;
            }


            $fields['ic_status'] = 1;
            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("propostas_facilities", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            $fields['ic_status'] = $propostas_facilities['ic_status'];
            Util::execUpdate("propostas_facilities", $fields, " pk = ".$propostas_facilities['pk'],$this->pdo);
            $pk = $propostas_facilities['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }
    public function verificarVersao($pk){

        $sql ="select pk ";
        $sql.="       ,ds_versao ";
        $sql.="  from propostas_facilities ";
        $sql.=" where proposta_facilities_pai_pk = $pk order by ds_versao desc";
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $ds_versao = count($rows)+1;
        return $ds_versao;
    }

    public function listarPropostaDetalhada(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql="";
        $sql.="Select  pk";
        $sql.="       ,ic_tipo_grupo";
        $sql.="       ,ds_nome_grupo ";
        $sql.="       ,ic_status ";
        $sql.="  from propostas_facilities_grupos_subgrupos ";
        $sql.=" where ic_tipo_grupo = 1";
        $sql.=" order by pk asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        for($i=0;$i<count($rows);$i++){
            $subGrupos = [];
            $sqlItensGrupos ="";
            $sqlItensGrupos.="Select pfl.pk";
            $sqlItensGrupos.="      ,pfl.ds_label";
            $sqlItensGrupos.="      ,pfl.ic_ordem";
            $sqlItensGrupos.="      ,pfl.ic_status";
            $sqlItensGrupos.="      ,pfl.propostas_facilities_grupos_subgrupos_pk";
            $sqlItensGrupos.="      ,pfl.subgrupo_pk";
            $sqlItensGrupos.="  from propostas_facilities_label pfl";
            $sqlItensGrupos.="  inner join propostas_facilities_grupos_subgrupos pfgs on pfgs.pk = pfl.propostas_facilities_grupos_subgrupos_pk";
            $sqlItensGrupos.=" where pfgs.ic_tipo_grupo = 1";
            $sqlItensGrupos.="   and pfl.propostas_facilities_grupos_subgrupos_pk = ".$rows[$i]['pk'];
            $sqlItensGrupos.="   and (pfl.subgrupo_pk is null or pfl.subgrupo_pk = 0)";
            $sqlItensGrupos.=" order by pfl.ic_ordem asc ";
            $stmt = $this->pdo->prepare( $sqlItensGrupos );
            $stmt->execute();
            $queryItens = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            $sqlSubgrupo = "";
            $sqlSubgrupo.= "select pk";
            $sqlSubgrupo.= "      ,ic_tipo_grupo";
            $sqlSubgrupo.= "      ,ds_nome_grupo";
            $sqlSubgrupo.="       ,grupo_pai_pk ";
            $sqlSubgrupo.="       ,ic_status ";
            $sqlSubgrupo.="  from propostas_facilities_grupos_subgrupos ";
            $sqlSubgrupo.=" where ic_tipo_grupo = 2 and grupo_pai_pk =".$rows[$i]['pk'];
            $sqlSubgrupo.=" order by grupo_pai_pk, pk asc ";
            $stmt = $this->pdo->prepare( $sqlSubgrupo );
            $stmt->execute();
            $querySubgrupo = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            for($l=0;$l<count($querySubgrupo);$l++){

                $sqlItensSubGrupos ="";
                $sqlItensSubGrupos.="Select pfl.pk";
                $sqlItensSubGrupos.="      ,pfl.ds_label";
                $sqlItensSubGrupos.="      ,pfl.ic_ordem";
                $sqlItensSubGrupos.="      ,pfl.ic_status";
                $sqlItensSubGrupos.="      ,pfl.propostas_facilities_grupos_subgrupos_pk";
                $sqlItensSubGrupos.="      ,pfl.subgrupo_pk";
                $sqlItensSubGrupos.="  from propostas_facilities_label pfl";
                $sqlItensSubGrupos.="  left join propostas_facilities_grupos_subgrupos pfgs on pfgs.pk = pfl.subgrupo_pk";
                $sqlItensSubGrupos.=" where pfgs.ic_tipo_grupo = 2";
                $sqlItensSubGrupos.="   and pfl.propostas_facilities_grupos_subgrupos_pk = ".$rows[$i]['pk'];
                $sqlItensSubGrupos.="   and pfl.subgrupo_pk = ".$querySubgrupo[$l]['pk'];
                $sqlItensSubGrupos.=" order by pfl.ic_ordem asc ";

                $stmt = $this->pdo->prepare( $sqlItensSubGrupos );
                $stmt->execute();
                $queryItensSubGrupos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $subGrupos[] = array(
                    "pk" => $querySubgrupo[$l]["pk"],
                    "ic_tipo_grupo" => $querySubgrupo[$l]["ic_tipo_grupo"],
                    "ds_nome_grupo" => $querySubgrupo[$l]["ds_nome_grupo"],
                    "grupo_pai_pk" => $querySubgrupo[$l]["grupo_pai_pk"],
                    "ic_status" => $querySubgrupo[$l]["ic_status"],
                    "ItensSubGrupos" => $queryItensSubGrupos
                );

            }
            $result[] = array(
                "pk" => $rows[$i]["pk"],
                "ic_tipo_grupo" => $rows[$i]["ic_tipo_grupo"],
                "ds_nome_grupo" => $rows[$i]["ds_nome_grupo"],
                "ic_status" => $rows[$i]["ic_status"],
                "Itens" => $queryItens,
                "SubGrupos" => $subGrupos
            );
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;

        return $retorno;
    }
    public function listarDadosPropostaDetalhada($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql="";
        $sql.=" select pf.pk,";
        $sql.="        pf.leads_pk,";
        $sql.="        pf.ic_tipo_proposta,";
        $sql.="        pf.produtos_servicos_pk,";
        $sql.="        pf.ds_qtde_efetivo,";
        $sql.="        pf.ds_qtde_hr_semanais,";
        $sql.="        pf.ic_escala,";
        $sql.="        pf.convencao_coletiva_pk,";
        $sql.="        date_format(pf.dt_base_categoria, '%d/%m/%Y') dt_base_categoria,";
        $sql.="        pf.ds_num_registro_mte,";
        $sql.="        pf.vl_salario_piso_categoria,";
        $sql.="        pf.vl_total_percentual_proposta,";
        $sql.="        pf.vl_total_proposta,";
        $sql.="        pf.ic_status";
        $sql.="   from propostas_facilities pf";
        $sql.="  where 1=1";
        $sql.="    and pk = $pk";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $sqlItens = "";
        $sqlItens .= "select pfi.pk,";
        $sqlItens .= "       pfi.ds_percentual,";
        $sqlItens .= "       pfi.ds_valor,";
        $sqlItens .= "       pfi.ic_status,";
        $sqlItens .= "       pfi.propostas_facilities_label_pk,";
        $sqlItens .= "       pfi.propostas_facilities_grupos_subgrupos_pk,";
        $sqlItens .= "       pfgs.grupo_pai_pk,";
        $sqlItens .= "       pfi.propostas_facilities_pk";
        $sqlItens .= "  from propostas_facilities_itens pfi";
        $sqlItens .= "       inner join propostas_facilities_grupos_subgrupos pfgs ON pfgs.pk = pfi.propostas_facilities_grupos_subgrupos_pk";
        $sqlItens .= " where 1 = 1 ";
        $sqlItens .= "  and pfi.propostas_facilities_pk =".$pk;

        $stmt = $this->pdo->prepare( $sqlItens );
        $stmt->execute();
        $queryItens = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $result[] = array(
            "pk" => $query[0]["pk"],
            "leads_pk"=>$query[0]['leads_pk'],
            "ic_tipo_proposta"=>$query[0]['ic_tipo_proposta'],
            "produtos_servicos_pk"=>$query[0]['produtos_servicos_pk'],
            "ds_qtde_efetivo"=>$query[0]['ds_qtde_efetivo'],
            "ds_qtde_hr_semanais"=>$query[0]['ds_qtde_hr_semanais'],
            "ic_escala"=>$query[0]['ic_escala'],
            "convencao_coletiva_pk"=>$query[0]['convencao_coletiva_pk'],
            "dt_base_categoria"=>$query[0]['dt_base_categoria'],
            "ds_num_registro_mte"=>$query[0]['ds_num_registro_mte'],
            "vl_salario_piso_categoria"=>$query[0]['vl_salario_piso_categoria'],
            "vl_total_percentual_proposta"=>$query[0]['vl_total_percentual_proposta'],
            "vl_total_proposta"=>$query[0]['vl_total_proposta'],
            "ic_status"=>$query[0]['ic_status'],
            "dadosItens"=>$queryItens
        );

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;

        return $retorno;
    }

    public function listarImpressaoProposta($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql="select ds_conta, ds_img_cliente, tipo_conta_pk ";
        $sql.=" from contas ";
        $sql.="where tipo_conta_pk = 1 ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $sqlProposta="";
        $sqlProposta.=" select pf.pk,";
        $sqlProposta.="        pf.leads_pk,";
        $sqlProposta.="        l.ds_lead,";
        $sqlProposta.="        pf.ic_tipo_proposta,";
        $sqlProposta.="        case when pf.ic_tipo_proposta = 1 then 'Mão de Obra'
                                    when pf.ic_tipo_proposta = 2 then 'Mão de Obra e Equipamento'
                                    when pf.ic_tipo_proposta = 3 then 'Mão de Obra e Produtos'
                                    when pf.ic_tipo_proposta = 3 then 'Mão de Obra, Equipamento e Produtos'
                                end ds_tipo_proposta,";
        $sqlProposta.="        pf.produtos_servicos_pk,";
        $sqlProposta.="        pf.ds_qtde_efetivo,";
        $sqlProposta.="        pf.ds_qtde_hr_semanais,";
        $sqlProposta.="        pf.ic_escala,";
        $sqlProposta.="        pf.convencao_coletiva_pk,";
        $sqlProposta.="        date_format(pf.dt_base_categoria, '%d/%m/%Y') dt_base_categoria,";
        $sqlProposta.="        pf.ds_num_registro_mte,";
        $sqlProposta.="        pf.vl_salario_piso_categoria,";
        $sqlProposta.="        pf.vl_total_percentual_proposta,";
        $sqlProposta.="        pf.vl_total_proposta,";
        $sqlProposta.="        pf.ic_status";
        $sqlProposta.="   from propostas_facilities pf";
        $sqlProposta.="   inner join leads l on l.pk = pf.leads_pk";
        $sqlProposta.="  where 1=1";
        $sqlProposta.="    and pf.pk = $pk";

        $stmt = $this->pdo->prepare( $sqlProposta );
        $stmt->execute();
        $queryProposta = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $sqlGrupos ="";
        $sqlGrupos.="Select  pk";
        $sqlGrupos.="       ,ic_tipo_grupo";
        $sqlGrupos.="       ,ds_nome_grupo ";
        $sqlGrupos.="       ,ic_status ";
        $sqlGrupos.="  from propostas_facilities_grupos_subgrupos ";
        $sqlGrupos.=" where ic_tipo_grupo = 1";
        $sqlGrupos.=" order by pk asc ";

        $stmt = $this->pdo->prepare( $sqlGrupos );
        $stmt->execute();
        $queryGrupos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        for($i=0; $i<count($queryGrupos); $i++){
            $sqlItens = "";
            $sqlItens .= "select pfi.pk,";
            $sqlItens .= "       sum(pfi.ds_valor) ds_valor,";
            $sqlItens .= "       sum(pfi.ds_valor) ds_valor,";
            $sqlItens .= "       pfi.ic_status,";
            $sqlItens .= "       pfi.propostas_facilities_label_pk,";
            $sqlItens .= "       pfi.propostas_facilities_grupos_subgrupos_pk,";
            $sqlItens .= "       pfgs.grupo_pai_pk,";
            $sqlItens .= "       pfi.propostas_facilities_pk";
            $sqlItens .= "  from propostas_facilities_itens pfi";
            $sqlItens .= "       inner join propostas_facilities_grupos_subgrupos pfgs ON pfgs.pk = pfi.propostas_facilities_grupos_subgrupos_pk";
            $sqlItens .= " where pfi.propostas_facilities_pk =".$pk;
            $sqlItens .= "  and (propostas_facilities_grupos_subgrupos_pk = ".$queryGrupos[$i]['pk']." or grupo_pai_pk = ".$queryGrupos[$i]['pk'].")";

            $stmt = $this->pdo->prepare( $sqlItens );
            $stmt->execute();
            $queryItens = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $result[] = array(
                "ds_nome_grupo"=>$queryGrupos[$i]['ds_nome_grupo'],
                "dadosItens"=>$queryItens
            );
        }


        $arrDadosImpressaoProposta[] = array(
            "ds_img_cliente" => $query[0]["ds_img_cliente"],
            "ds_conta" => $query[0]["ds_conta"],
            "pk" => $queryProposta[0]["pk"],
            "leads_pk"=>$queryProposta[0]['leads_pk'],
            "ds_lead"=>$queryProposta[0]['ds_lead'],
            "ic_tipo_proposta"=>$queryProposta[0]['ic_tipo_proposta'],
            "ds_tipo_proposta"=>$queryProposta[0]['ds_tipo_proposta'],
            "produtos_servicos_pk"=>$queryProposta[0]['produtos_servicos_pk'],
            "ds_qtde_efetivo"=>$queryProposta[0]['ds_qtde_efetivo'],
            "ds_qtde_hr_semanais"=>$queryProposta[0]['ds_qtde_hr_semanais'],
            "ic_escala"=>$queryProposta[0]['ic_escala'],
            "convencao_coletiva_pk"=>$queryProposta[0]['convencao_coletiva_pk'],
            "dt_base_categoria"=>$queryProposta[0]['dt_base_categoria'],
            "ds_num_registro_mte"=>$queryProposta[0]['ds_num_registro_mte'],
            "vl_salario_piso_categoria"=>$queryProposta[0]['vl_salario_piso_categoria'],
            "vl_total_percentual_proposta"=>$queryProposta[0]['vl_total_percentual_proposta'],
            "vl_total_proposta"=>$queryProposta[0]['vl_total_proposta'],
            "ic_status"=>$queryProposta[0]['ic_status'],
            "dados_proposta"=>$result
        );
        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $arrDadosImpressaoProposta;

        return $retorno;
    }
    public function pegarDadosItens(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql.="Select  pk";
        $sql.="       ,ic_tipo_grupo";
        $sql.="       ,ic_status ";
        $sql.="  from propostas_facilities_grupos_subgrupos ";
        $sql.=" where ic_tipo_grupo = 1";
        $sql.=" order by pk asc ";
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        for($i=0;$i<count($query);$i++){
            $sqlItensGrupos ="";
            $sqlItensGrupos.="Select pfl.pk";
            $sqlItensGrupos.="      ,pfl.propostas_facilities_grupos_subgrupos_pk";
            $sqlItensGrupos.="  from propostas_facilities_label pfl";
            $sqlItensGrupos.="       inner join propostas_facilities_grupos_subgrupos pfgs on pfgs.pk = pfl.propostas_facilities_grupos_subgrupos_pk";
            $sqlItensGrupos.=" where pfgs.ic_tipo_grupo = 1";
            $sqlItensGrupos.="   and pfl.propostas_facilities_grupos_subgrupos_pk = ".$query[$i]['pk'];
            $sqlItensGrupos.="   and pfl.subgrupo_pk is null";
            $sqlItensGrupos.=" order by pfl.ic_ordem asc ";

            $stmt = $this->pdo->prepare( $sqlItensGrupos );
            $stmt->execute();
            $queryItens = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sqlSubgrupo = "";
            $sqlSubgrupo.= "select pk";
            $sqlSubgrupo.= "      ,ic_tipo_grupo";
            $sqlSubgrupo.="       ,grupo_pai_pk ";
            $sqlSubgrupo.="       ,ic_status ";
            $sqlSubgrupo.="  from propostas_facilities_grupos_subgrupos ";
            $sqlSubgrupo.=" where ic_tipo_grupo = 2 and grupo_pai_pk =".$query[$i]['pk'];
            $sqlSubgrupo.=" order by grupo_pai_pk, pk asc ";

            $stmt = $this->pdo->prepare( $sqlSubgrupo );
            $stmt->execute();
            $querySubgrupo = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $grupos = [];
            $subGrupos = [];
            for($l=0;$l<count($querySubgrupo);$l++){

                $sqlItensSubGrupos ="";
                $sqlItensSubGrupos.="Select pfl.pk";
                $sqlItensSubGrupos.="      ,pfl.propostas_facilities_grupos_subgrupos_pk";
                $sqlItensSubGrupos.="      ,pfl.subgrupo_pk";
                $sqlItensSubGrupos.="  from propostas_facilities_label pfl";
                $sqlItensSubGrupos.="       left join propostas_facilities_grupos_subgrupos pfgs on pfgs.pk = pfl.subgrupo_pk";
                $sqlItensSubGrupos.=" where pfgs.ic_tipo_grupo = 2";
                $sqlItensSubGrupos.="   and pfl.propostas_facilities_grupos_subgrupos_pk = ".$query[$i]['pk'];
                $sqlItensSubGrupos.="   and pfl.subgrupo_pk = ".$querySubgrupo[$l]['pk'];
                $sqlItensSubGrupos.=" order by pfl.ic_ordem asc ";

                $stmt = $this->pdo->prepare( $sqlItensSubGrupos );
                $stmt->execute();
                $queryItensSubGrupos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                if(!isset($querySubgrupo[$l]["ic_status"])){
                    $statusSubGrupo = "";
                }
                else{
                    $statusSubGrupo = $query[$i]["ic_status"];
                }
                $subGrupos[] = array(
                    "pk" => $querySubgrupo[$l]["pk"],
                    "ic_tipo_grupo" => $querySubgrupo[$l]["ic_tipo_grupo"],
                    "grupo_pai_pk" => $querySubgrupo[$l]["grupo_pai_pk"],
                    "ic_status" => $statusSubGrupo,
                    "ItensSubGrupos" => $queryItensSubGrupos
                );

            }

            if(!isset($query[$i]["ic_status"])){
                $statusGrupo = "";
            }
            else{
                $statusGrupo = $query[$i]["ic_status"];
            }
            $grupos[] = array(
                "pk" => $query[$i]["pk"],
                "ic_tipo_grupo" => $query[$i]["ic_tipo_grupo"],
                "ic_status" => $statusGrupo,
                "Itens" => $queryItens,
            );

            $result[] = array(
                "grupos" => $grupos,
                "SubGrupos" => $subGrupos
            );

        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;

        return $retorno;
    }
    public function listar_por_ds_versao_pk($leads_pk,$ic_status,$usuario_cadastro_pk,$usuario_responsavel_comercial_pk,$dt_cadastro){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        //PAGINAÇÃO
        if(isset($_GET['start']) && $_GET['start']!=0){
            $displayStart = $_GET['start'];
        }
        else{
            $displayStart = 0;
        }
        $search = "";
        if (isset($_GET['search']['value']) and $_GET['search']['value'] != '') {
            $pesq = $_GET['search']['value'];
            $search .= " AND (
                            pf.pk LIKE '%".$pesq."%' OR
                            l.ds_lead LIKE '%".$pesq."%'
                        )";
        }

        if(isset($_GET['length'])){
            $displayRange = $_GET['length'];
            $lengthSql = " LIMIT ".intval($displayRange)." OFFSET ".intval($displayStart);
        }
        else{
            $lengthSql = " ";
        }

        $sql ="";
        $sql.="select pf.pk t_pk,date_format(pf.dt_cadastro,'%d/%m/%Y'), pf.usuario_cadastro_pk, pf.dt_ult_atualizacao, pf.usuario_ult_atualizacao_pk ";
        $sql.="       ,pf.ds_versao  t_ds_versao";
        $sql.="       ,pf.proposta_facilities_pai_pk t_proposta_facilities_pai_pk ";
        $sql.="       ,pf.ds_numero_proposta t_ds_numero_proposta ";
        $sql.="       ,pf.leads_pk t_leads_pk ";
        $sql.="       ,case WHEN l.leads_pai_pk is null  THEN
                            l.ds_lead 
                        ELSE 
                            concat(ll.ds_lead,' - Posto:',l.ds_lead) 
                        END t_ds_lead";
        $sql.="       ,pf.ic_tipo_proposta t_ic_tipo_proposta";
        $sql.="       ,pf.produtos_servicos_pk t_produtos_servicos_pk";
        $sql.="       ,pf.ds_qtde_efetivo t_ds_qtde_efetivo";
        $sql.="       ,pf.ds_qtde_hr_semanais t_ds_qtde_hr_semanais";
        $sql.="       ,pf.ic_escala t_ic_escala";
        $sql.="       ,pf.convencao_coletiva_pk t_convencao_coletiva_pk";
        $sql.="       ,pf.dt_base_categoria t_dt_base_categoria";
        $sql.="       ,pf.ds_num_registro_mte t_ds_num_registro_mte";
        $sql.="       ,pf.vl_salario_piso_categoria t_vl_salario_piso_categoria";
        $sql.="       ,pf.vl_total_proposta t_vl_total_proposta";
        $sql.="       ,pf.usuario_responsavel_comercial_pk t_usuario_responsavel_comercial_pk";
        $sql.="       ,u.ds_usuario t_ds_usuario_responsavel_comercial ";
        $sql.="       ,us.ds_usuario t_ds_usuario_cadastro ";
        $sql.="       ,pf.dt_envio_da_proposta t_dt_envio_da_proposta";
        $sql.="       ,date_format(pf.dt_previsao_fechamento,'%d/%m/%Y') t_dt_previsao_fechamento ";
        $sql.="       ,pf.dt_fechamento t_dt_fechamento";
        $sql.="       ,pf.dt_cancelamento t_dt_cancelamento";
        $sql.="       ,pf.obs_motivo_cancelamento t_obs_motivo_cancelamento";
        $sql.="       ,pf.obs_proposta t_obs_proposta";
        $sql.="       ,case when pf.ic_status = 1 then 'Cadastrada' ";
        $sql.="             when pf.ic_status = 2 then 'Enviada para o Cliente' ";
        $sql.="             when pf.ic_status = 3 then 'Previsão de Fechamento' ";
        $sql.="             when pf.ic_status = 4 then 'Proposta Aprovada' ";
        $sql.="             when pf.ic_status = 5 then 'Cancelada' ";
        $sql.="        end t_ds_status ";
        $sql.="       ,pf.ic_status t_ic_status";
        $sql.="       ,pf.contratos_pk t_contratos_pk";

        $sql.="  from propostas_facilities pf";
        $sql.="  inner join leads l on l.pk = pf.leads_pk ";
        $sql.="  left join leads ll on l.leads_pai_pk = ll.pk";
        $sql.="  inner join usuarios us on us.pk = pf.usuario_cadastro_pk ";
        $sql.="  left join usuarios u on u.pk = pf.usuario_responsavel_comercial_pk ";
        $sql.=" where 1=1 ";
        if($leads_pk != ""){
            $sql.=" and pf.leads_pk = ".$leads_pk;
        }
        if($ic_status != ""){
            $sql.=" and pf.ic_status = ".$ic_status;
        }
        if($usuario_cadastro_pk != ""){
            $sql.=" and pf.usuario_cadastro_pk = ".$usuario_cadastro_pk;
        }
        if($usuario_responsavel_comercial_pk != ""){
            $sql.=" and pf.usuario_responsavel_comercial_pk = ".$usuario_responsavel_comercial_pk;
        }
        if($dt_cadastro != ""){
            $sql.=" and date_format(pf.dt_cadastro,'%d/%m/%Y') = '".$dt_cadastro."'";
        }
        $sql.= $search;
        $sql.=" order by l.ds_lead asc ";


        $stmtCount = $this->pdo->prepare( $sql.$lengthSql );
        $stmtCount->execute();
        $rows = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);


        $stmtCount = $this->pdo->prepare( $sql );
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);


        echo json_encode($retorno);
        exit(0);
    }

    public function RelatorioProposta($leads_clientes_pk, $leads_pk, $usuario_cadastro_pk, $dt_ini, $dt_fim, $ic_status){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select p.pk,ll.ds_lead ds_cliente";
        $sql.="       ,l.ds_lead ds_posto_trabalho ";
        $sql.="       ,u.ds_usuario";
        $sql.="       ,p.vl_total_proposta ";
        $sql.="       ,date_format(p.dt_cadastro, '%d/%m/%Y')dt_cadastro";
        $sql.="       ,case p.ic_status when 1 then 'Em negociação' when 2 then 'Proposta Fechada' end ds_status";
        $sql.="       from propostas_facilities p";
        $sql.="       inner join leads l on p.leads_pk = l.pk";
        $sql.="       left join leads ll on l.leads_pai_pk = ll.pk";
        $sql.="       inner join usuarios u on p.usuario_cadastro_pk = u.pk";
        $sql.=" where 1=1 ";
        if($leads_clientes_pk!=""){
            $sql .= " and (l.pk = " . $leads_clientes_pk . " OR l.leads_pai_pk = " . $leads_clientes_pk . ")";
        }
        if($leads_pk!=""){
            $sql .= " and l.pk = " . $leads_pk ;
        }
        if($usuario_cadastro_pk!=""){
            $sql .= " and p.usuario_cadastro_pk = " . $usuario_cadastro_pk ;
        }
        if($dt_ini!=""){
            $sql .= " and date_format(p.dt_cadastro,'%Y-%m-%d') between '" . Util::DataYMD($dt_ini)."' and '". Util::DataYMD($dt_fim)."'" ;
        }
        if($ic_status!=""){
            $sql .= " and p.ic_status = " . $ic_status ;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rows);
        $retorno->iTotalRecords = count($rows);


        
        echo json_encode($retorno);
        exit(0);
        
    }

}
