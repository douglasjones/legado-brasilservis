<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use SimpleXMLElement;

class ConciliacaoBancaria {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        util::execDelete("financeiro_conciliacao_banco_itens"," financeiro_conciliacao_banco_pk=".$pk,$this->pdo);
        util::execDelete("financeiro_conciliacao_banco"," pk=".$pk,$this->pdo);
    }

    public function salvar($financeiro_conciliacao_banco){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_link_arquivo'] = "SalvonoBanco";
        $fields['vl_saldo_conta'] = $financeiro_conciliacao_banco['vl_saldo_conta'];
        $fields['dt_ini_periodo_saldo'] = Util::DataYMD($financeiro_conciliacao_banco['dt_ini_periodo_saldo']);
        $fields['dt_fim_periodo_saldo'] = Util::DataYMD($financeiro_conciliacao_banco['dt_fim_periodo_saldo']);
        $fields['ds_obs'] = $financeiro_conciliacao_banco['ds_obs'];
        $fields['ic_status'] = $financeiro_conciliacao_banco['ic_status'];
        $fields['contas_bancarias_pk'] = $financeiro_conciliacao_banco['contas_bancarias_pk'];
        $fields['empresas_pk'] = $financeiro_conciliacao_banco['empresas_pk'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];


        if($financeiro_conciliacao_banco['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("financeiro_conciliacao_banco", $fields,$this->pdo);

            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("financeiro_conciliacao_banco", $fields, " pk = ".$financeiro_conciliacao_banco['pk'],$this->pdo);
            $pk = $financeiro_conciliacao_banco['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }

        return $retorno;

    }
    public function salvarConciliacaoLancamento($financeiro_conciliacao_lancamentos){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['lancamentos_pk'] = $financeiro_conciliacao_lancamentos['lancamentos_pk'];
        $fields['financeiro_conciliacao_banco_itens_pk'] = $financeiro_conciliacao_lancamentos['financeiro_conciliacao_banco_itens_pk'];
        $fields['obs'] = $financeiro_conciliacao_lancamentos['obs'];
        $fields['ic_status'] = $financeiro_conciliacao_lancamentos['ic_status'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];


        if($financeiro_conciliacao_lancamentos['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("financeiro_conciliacao_lancamentos", $fields,$this->pdo);

            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("financeiro_conciliacao_lancamentos", $fields, " pk = ".$financeiro_conciliacao_lancamentos['pk'],$this->pdo);
            $pk = $financeiro_conciliacao_lancamentos['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }

        return $retorno;

    }
    public function salvarItens($financeiro_import_lancamento_itens){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ic_tipo_transacao'] = $financeiro_import_lancamento_itens['ic_tipo_transacao'];
        $fields['dt_transacao'] = Util::DataYMD($financeiro_import_lancamento_itens['dt_transacao']);
        $fields['vl_transacao'] = $financeiro_import_lancamento_itens['vl_transacao'];
        $fields['cod_verificacao_transacao'] = $financeiro_import_lancamento_itens['cod_verificacao_transacao'];
        $fields['ds_estabelecimento'] = $financeiro_import_lancamento_itens['ds_estabelecimento'];
        $fields['financeiro_conciliacao_banco_pk'] = $financeiro_import_lancamento_itens['financeiro_conciliacao_banco_pk'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] =  $_SESSION['session_user']['par1'];


        $pk = Util::execInsert("financeiro_conciliacao_banco_itens", $fields,$this->pdo);

        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;


        return $retorno;

    }


    public function listarGrid(){

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

        if(isset($_GET['length'])){
            $displayRange = $_GET['length'];
            $lengthSql = " LIMIT ".intval($displayRange)." OFFSET ".intval($displayStart);
        }
        else{
            $lengthSql = " ";
        }
        $search = "";
        if (isset($_GET['search']['value']) and $_GET['search']['value'] != '') {
            $pesq = $_GET['search']['value'];
            $search .= " AND (
                            f.pk LIKE '%".$pesq."%' OR
                            c.ds_conta LIKE '%".$pesq."%' OR
                            b.ds_banco LIKE '%".$pesq."%' OR
                            cb.ds_conta LIKE '%".$pesq."%' 
                        )";
        }
        $sql="";
        $sql.=" SELECT  f.pk t_pk,";
        $sql.="         c.ds_conta t_ds_conta,";
        $sql.="         b.ds_banco t_ds_banco,";
        $sql.="         cb.ds_agencia t_ds_agencia,";
        $sql.="         cb.ds_conta t_ds_conta_bancaria,";
        $sql.="         DATE_FORMAT(f.dt_ini_periodo_saldo, '%d/%m/%Y')t_dt_ini_periodo_saldo,";
        $sql.="         DATE_FORMAT(f.dt_fim_periodo_saldo, '%d/%m/%Y')t_dt_fim_periodo_saldo";
        $sql.=" FROM financeiro_conciliacao_banco f ";
        $sql.=" INNER JOIN contas_bancarias cb on f.contas_bancarias_pk = cb.pk";
        $sql.=" INNER JOIN contas c on f.empresas_pk = c.pk";
        $sql.=" INNER JOIN bancos b on cb.bancos_pk = b.pk";
        $sql.=" WHERE 1=1";
        $sql.=$search;


        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
    public function listarDataTableItens($financeiro_conciliacao_banco_pk){

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

        if(isset($_GET['length'])){
            $displayRange = $_GET['length'];
            $lengthSql = " LIMIT ".intval($displayRange)." OFFSET ".intval($displayStart);
        }
        else{
            $lengthSql = " ";
        }
        $sql ="";
        $sql.="SELECT distinct(f.pk)t_pk,";
        $sql.="       DATE_FORMAT(f.dt_transacao,'%d/%m/%Y')t_dt_transacao,";
        $sql.="       CASE f.ic_tipo_transacao WHEN 1 THEN 'Crédito' when 2 then 'Débito' end t_ds_transacao,";
        $sql.="       f.vl_transacao t_vl_transacao,";
        $sql.="       f.ic_tipo_transacao t_ic_tipo_transacao,";
        $sql.="       f.cod_verificacao_transacao t_cod_verificacao_transacao,";
        $sql.="       f.ds_estabelecimento t_ds_estabelecimento, ";
        $sql.="       fl.lancamentos_pk t_lancamentos_pk, ";
        $sql.="       fl.ic_status t_ic_status_fl, ";
        $sql.="       fl.obs t_obs_fl ,";
        $sql.="       fl.pk t_financeiro_conciliacao_lancamentos_pk";
        $sql.="  from financeiro_conciliacao_banco_itens f";
        $sql.="  left join financeiro_conciliacao_lancamentos fl on fl.financeiro_conciliacao_banco_itens_pk = f.pk";
        $sql.=" where 1=1 ";
        if($financeiro_conciliacao_banco_pk != ""){
            $sql.=" and f.financeiro_conciliacao_banco_pk =".$financeiro_conciliacao_banco_pk;
        }
        $sql.=" order by f.dt_transacao asc ";




        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
    public function listarPk($pk){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.=" SELECT  f.pk,";
        $sql.="         f.vl_saldo_conta,";
        $sql.="         f.ds_obs,";
        $sql.="         f.empresas_pk,";
        $sql.="         f.contas_bancarias_pk,";
        $sql.="         DATE_FORMAT(f.dt_ini_periodo_saldo, '%d/%m/%Y')dt_ini_periodo_saldo,";
        $sql.="         DATE_FORMAT(f.dt_fim_periodo_saldo, '%d/%m/%Y')dt_fim_periodo_saldo";
        $sql.=" FROM financeiro_conciliacao_banco f ";
        $sql.=" where f.pk = $pk ";


        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function abrirArquivoOfx($file){

        $content = file_get_contents($file['tmp_name']);

        $line = strpos($content, "<OFX>");
        $ofx = substr($content, $line - 1);
        $buffer = $ofx;

        $count = 0;

        while ($pos = strpos($buffer, '<'))
        {

            $count++; $pos2 = strpos($buffer, '>');
            $element = substr($buffer, $pos + 1, $pos2 - $pos - 1);

            if (substr($element, 0, 1) == '/')
                $sla[] = substr($element, 1);
            else $als[] = $element;
            $buffer = substr($buffer, $pos2 + 1);
        }
        $adif = array_diff($als, $sla);



        $adif = array_unique($adif);
        $ofxy = $ofx;

        foreach ($adif as $dif)
        {
            $dpos = 0;
            while ($dpos = strpos($ofxy, $dif, $dpos + 1))
            {
                $npos = strpos($ofxy, '<', $dpos + 1);
                $ofxy = substr_replace($ofxy, "</$dif>".chr(10)."<", $npos, 1);
                $dpos = $npos + strlen($element) + 3;
            }
        }
        $ofxy = str_replace('&', '&', $ofxy);

        $buffer = '';
        $source = fopen($file['tmp_name'], 'r') or die("Unable to open file!");
        while(!feof($source)) {

            $line = trim(fgets($source));
            if ($line === '') continue;

            if (substr($line, -1, 1) !== '>') {
                list($tag) = explode('>', $line, 2);
                $line .= '</' . substr($tag, 1) . '>';
            }
            $buffer .= $line ."\n";
        }


        $xmlOut =   explode("<OFX>", $buffer);

        $params = isset($xmlOut[1])?"<OFX>".$xmlOut[1]:$buffer;

        $retorno =  new SimpleXMLElement(utf8_encode($params));

        $codBanco = $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->BANKID;
        $agenciaEConta =$retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->ACCTID;
        $dtPeridoIniExtrato = $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->DTSTART;
        if($dtPeridoIniExtrato){
            $yyyyIni = substr($dtPeridoIniExtrato,0,4);
            $mmIni= substr($dtPeridoIniExtrato,4,2);
            $ddIni =  substr($dtPeridoIniExtrato,6,2);

            $dtPeridoIniExtratoFormat = $ddIni.'/'.$mmIni.'/'.$yyyyIni;
        }
        $dtPeriodoFimExtrato = $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->DTEND;
        if($dtPeriodoFimExtrato){
            $yyyyFim = substr($dtPeriodoFimExtrato,0,4);
            $mmFim= substr($dtPeriodoFimExtrato,4,2);
            $ddFim =  substr($dtPeriodoFimExtrato,6,2);

            $dtPeriodoFimExtratoFormat = $ddFim.'/'.$mmFim.'/'.$yyyyFim;
        }

        $arrDadosAnalitico = $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->STMTTRN;
        $arrDados = [];

        foreach($arrDadosAnalitico as $v ){

            $tipoTransacao = $v->TRNTYPE;
            $dtTransacao = $v->DTPOSTED;

            if($dtTransacao){
                $yyyyT = substr($dtTransacao,0,4);
                $mmT= substr($dtTransacao,4,2);
                $ddT =  substr($dtTransacao,6,2);

                $dtTransacaoFormat = $ddT.'/'.$mmT.'/'.$yyyyT;
            }
            $valor = $v->TRNAMT;
            $codTransacao = $v->CHECKNUM;
            $NomeEstabelcimentoPessoa = $v->MEMO;

            $arrExtrato[] = [
                "tipoTransacao"=>strval($tipoTransacao),
                "dtTransacao"=>strval($dtTransacaoFormat),
                "valor"=>strval($valor),
                "nomeEstabelecimento"=>strval($NomeEstabelcimentoPessoa),
                "codTransacao"=>strval($codTransacao)
            ];
        }
        $saldo = $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->BALAMT;
        $dataSaldo = $retorno->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->DTASOF;


        if($dataSaldo){
            $yyyyS = substr($dataSaldo,0,4);
            $mmS= substr($dataSaldo,4,2);
            $ddS =  substr($dataSaldo,6,2);

            $dataSaldoFormat = $ddS.'/'.$mmS.'/'.$yyyyS;
        }

        $arrDados = [
            "dtPeriodoInicio"=> strval($dtPeridoIniExtratoFormat),
            "dtPeriodoFim"=>strval($dtPeriodoFimExtratoFormat),
            "saldoConta"=>strval($saldo),
            "dataEmissaoExtrato"=>strval($dataSaldoFormat),
            "arrExtratoItens"=>$arrExtrato
        ];

        return $arrDados;
    }



}
