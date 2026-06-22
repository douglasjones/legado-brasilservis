<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Throwable;

class Colaborador {

    public $pdo;
    private $client; // Removido o tipo de retorno
    protected $history = []; // Removido o tipo de retorno
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $stack = HandlerStack::create(new CurlMultiHandler());

        $stack->push(Middleware::retry(
            (function () {
                return function (
                    $retries,
                    \GuzzleHttp\Psr7\Request $request,
                    \GuzzleHttp\Psr7\Response $response = null,
                    \GuzzleHttp\Exception\RequestException $exception = null
                ) {
                    if ($retries >= 5) {
                        return false;
                    }

                    if ($exception !== null) {
                        return true;
                    }

                    if ($response !== null) {
                        if ($response->getStatusCode() >= 500 && $response->getStatusCode() <= 599) {
                            return true;
                        }
                    }

                    return false;
                };
            })(),
            (function () {
                return function ($numberOfRetries) {
                    return 1000 * $numberOfRetries;
                };
            })() // Delay do retry
        ));

        $history = Middleware::history($this->history);
        $stack->push($history);

        $this->client = new Client([
            'http_errors'      => false,
            'connect_timeout'  => 10.0,
            'timeout'          => 60.0,
            'force_ip_resolve' => 'v4',
            'verify'           => false,
            'handler'          => $stack
        ]);
    }

    public function excluir($pk){
       /* $sql ="";
        $sql.="delete from movimentacao_estoque where pk in(select pk 
                from (select pk from movimentacao_estoque 
                WHERE colaborador_pk  =".$pk.")x)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        Util::execDelete('agenda_colaborador_pausa', ' colaboradores_pk='.$pk, $this->pdo);
        Util::execDelete('colaboradores_beneficios', ' colaborador_pk='.$pk, $this->pdo);
        Util::execDelete('agenda_colaborador_padrao', ' colaboradores_pk='.$pk, $this->pdo);
        Util::execDelete('colaboradores_curso', ' colaboradores_pk='.$pk, $this->pdo);
        Util::execDelete('colaboradores_produtos_servicos', ' colaboradores_pk='.$pk, $this->pdo);
        Util::execDelete('colaboradores_nome_filho', ' colaborador_pk='.$pk, $this->pdo);*/
        $fieldsA = array();
        $fieldsA['dt_cancelamento'] = "sysdate()";
        $fields = array();
        $fields['ic_status'] = 2;
        Util::execUpdate("agenda_colaborador_padrao", $fieldsA, " colaboradores_pk = ".$pk,$this->pdo);
        Util::execUpdate("colaboradores", $fields, " pk = ".$pk,$this->pdo);

        $this->salvarColaboradorServidor($pk);
    }
    public function excluirColaboradorCurso($colaborador_pk){
        Util::execDelete('colaboradores_curso', ' colaboradores_pk='.$colaborador_pk, $this->pdo);
    }
    public function excluirAfastamentoColaborador($colaborador_pk){
        Util::execDelete('afastamento_ferias_colaborador', ' colaborador_pk='.$colaborador_pk, $this->pdo);
    }
    public function excluirFilhoColaborador($colaborador_pk){
        Util::execDelete('colaboradores_nome_filho', ' colaborador_pk='.$colaborador_pk, $this->pdo);
    }
    public function excluirColaboradorBeneficio($colaborador_pk){
        Util::execDelete('colaboradores_beneficios', ' colaborador_pk='.$colaborador_pk, $this->pdo);
    }

    public function salvar($colaborador){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_colaborador'] = str_replace("'", " ", $colaborador['ds_colaborador']);
        $fields['ds_cel'] = $colaborador['ds_cel'];
        $fields['ic_whatsapp'] = $colaborador['ic_whatsapp'];
        $fields['ds_cel2'] = $colaborador['ds_cel2'];
        $fields['ic_whatsapp2'] = $colaborador['ic_whatsapp2'];
        $fields['ds_cel3'] = $colaborador['ds_cel3'];
        $fields['ic_whatsapp3'] = $colaborador['ic_whatsapp3'];
        $fields['ds_email'] = $colaborador['ds_email'];
        $fields['ds_rg'] = $colaborador['ds_rg'];
        $fields['ds_cpf'] = $colaborador['ds_cpf'];
        $fields['ds_senha_portal'] = $colaborador['ds_senha_portal'];
       
        $fields['dt_nascimento'] = Util::DataYMD($colaborador['dt_nascimento']);
       
        $fields['ds_endereco'] = str_replace("'", " ", $colaborador['ds_endereco']);
        $fields['ds_numero'] = $colaborador['ds_numero'];
        $fields['ds_complemento'] = $colaborador['ds_complemento'];
        $fields['ds_bairro'] = str_replace("'", " ", $colaborador['ds_bairro']);;
        $fields['ds_cep'] = $colaborador['ds_cep'];
        $fields['ds_cidade'] = $colaborador['ds_cidade'];
        $fields['ds_uf'] = $colaborador['ds_uf'];
        $fields['ic_status'] = $colaborador['ic_status'];

        $fields['ic_origem'] = $colaborador['ic_origem'];
        $fields['ic_funcionario'] = $colaborador['ic_funcionario'];
        $fields['generos_pk'] = $colaborador['generos_pk'];
        $fields['ds_re'] = $colaborador['ds_re'];
        $fields['ds_raca'] = $colaborador['ds_raca'];
        $fields['ds_deficiencia_fisica'] = $colaborador['ds_deficiencia_fisica'];
        $fields['estado_civil'] = $colaborador['estado_civil'];
        $fields['ds_nome_pai'] = $colaborador['ds_nome_pai'];
        $fields['ds_nome_mae'] = $colaborador['ds_nome_mae'];
        $fields['ds_nome_conjuge'] = $colaborador['ds_nome_conjuge'];
        $fields['ic_reserva'] = $colaborador['ic_reserva'];
        $fields['qtde_filho'] = $colaborador['qtde_filho'];
        $fields['empresas_pk'] = $colaborador['empresas_pk'];
        $fields['regime_contratacao_pk'] = $colaborador['regime_contratacao_pk'];
        $fields['ds_carga_horaria_semanal'] = $colaborador['ds_carga_horaria_semanal'];

        $fields['tipo_conta_bancaria'] = $colaborador['tipo_conta_bancaria'];
        $fields['ds_agencia'] = $colaborador['ds_agencia'];
        $fields['ds_conta'] = $colaborador['ds_conta'];
        $fields['ds_digito'] = $colaborador['ds_digito'];
        $fields['bancos_pk'] = $colaborador['bancos_pk'];
        $fields['vl_salario'] = $colaborador['vl_salario'];
        $fields['ds_pix'] = $colaborador['ds_pix'];
        $fields['ds_conta_favorecido'] = $colaborador['ds_conta_favorecido'];
        
        $fields['ds_n_sapato'] = $colaborador['ds_n_sapato'];
        $fields['ds_n_camisa'] = $colaborador['ds_n_camisa'];
        $fields['ds_n_calca'] = $colaborador['ds_n_calca'];
        $fields['ds_n_luva'] = $colaborador['ds_n_luva'];

        
        $fields['ic_tipo_sanguineo'] = $colaborador['ic_tipo_sanguineo'];
        $fields['ds_cartao_sus'] = $colaborador['ds_cartao_sus'];
        $fields['ic_tipo_sanguineo_conjuge'] = $colaborador['ic_tipo_sanguineo_conjuge'];
        $fields['ic_ds_cartao_sus_conjuge'] = $colaborador['ic_ds_cartao_sus_conjuge'];

        $fields['ic_experiencia'] = $colaborador['ic_experiencia'];

        if($colaborador['dt_nascimento_conjuge']!=""){
            $fields['dt_nascimento_conjuge'] = $colaborador['dt_nascimento_conjuge'];
        }
       
        if($colaborador['dt_admissao']!=""){
            $fields['dt_admissao']=Util::DataYMD($colaborador['dt_admissao']);
        }
        
        if($colaborador['dt_demissao']!=""){
            if($colaborador['dt_demissao']!="00/00/0000"){
                $fields['dt_demissao'] = Util::DataYMD($colaborador['dt_demissao']);
                $fields['ic_status'] =2;

                $fields['ic_funcionario'] = 2;
            }
            else if($colaborador['dt_demissao']=="00/00/0000"){
                $fields['dt_demissao'] = "null";
                $fields['ic_status'] =1;

                $fields['ic_funcionario'] = 1;
            }
            else if($colaborador['dt_demissao']==""){
                $fields['dt_demissao'] = "null";
                $fields['ic_status'] =1;

                $fields['ic_funcionario'] = 1;
            }
        }else{
            $fields['dt_demissao'] = "null";
            $fields['ic_status'] = $colaborador['ic_status'];
        
            $fields['ic_funcionario'] = 1;
        }
        
        $fields['ds_cpf_conjuge'] = $colaborador['ds_cpf_conjuge'];
        $fields['ds_tel_conjuge'] = $colaborador['ds_tel_conjuge'];
        $fields['regime_casamento'] = $colaborador['regime_casamento'];
        
        if($colaborador['dt_expedicao']!=""){
            $fields['dt_expedicao'] = $colaborador['dt_expedicao'];
        }

        $fields['ds_org_exp'] = $colaborador['ds_org_exp'];
        $fields['ds_pis'] = $colaborador['ds_pis'];
        $fields['ds_titulo_eleitoral'] = $colaborador['ds_titulo_eleitoral'];
        $fields['ds_zona_eleitoral'] = $colaborador['ds_zona_eleitoral'];
        $fields['ds_secao'] = $colaborador['ds_secao'];
        $fields['ds_certificado_reservista'] = $colaborador['ds_certificado_reservista'];
        $fields['ic_filho_menor_14'] = $colaborador['ic_filho_menor_14'];
        $fields['ds_uf_rg'] = $colaborador['ds_uf_rg'];
        $fields['ds_serie'] = $colaborador['ds_serie'];
        $fields['ds_ctps'] = $colaborador['ds_ctps'];
        $fields['ds_matricula'] = $colaborador['ds_matricula'];
        $fields['ds_nacionalidade'] = $colaborador['ds_nacionalidade'];
        $fields['grau_escolaridade_pk'] = $colaborador['grau_escolaridade_pk'];
        
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        $isNovoColaborador = ($colaborador['pk'] == "");

        if($isNovoColaborador){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
            $fields["ds_senha_portal"]   = "gepros";


            $pk = Util::execInsert("colaboradores", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;

            
        }
        else{

            Util::execUpdate("colaboradores", $fields, " pk = ".$colaborador['pk'],$this->pdo);
            $pk = $colaborador['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        $this->salvarColaboradorServidor($pk, $isNovoColaborador);
        return $retorno;

    }

    public function salvarDSPin($ds_pin,$colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_pin'] = $ds_pin;
        
        
        Util::execUpdate("colaboradores", $fields, " pk = ".$colaborador_pk,$this->pdo);

       
        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $colaborador_pk;
        
        return $retorno;

    }

    public function salvarFilhoColaborador($colaborador_nome_filho){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['colaborador_pk'] = $colaborador_nome_filho['colaborador_pk'];
        $fields['ds_nome_filho'] = $colaborador_nome_filho['ds_nome_filho'];
        $fields['ds_cpf_filho'] = $colaborador_nome_filho['ds_cpf_filho'];
        if($colaborador_nome_filho['dt_nascimento_filho']!=""){
            $fields['dt_nascimento_filho'] = Util::DataYMD($colaborador_nome_filho['dt_nascimento_filho']);
        }
        

        $fields['ds_tipo_sanguineo_dependente'] = $colaborador_nome_filho['ds_tipo_sanguineo_dependente'];
        $fields['ds_num_cartao_sus_dependente'] = $colaborador_nome_filho['ds_num_cartao_sus_dependente'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];


        $fields["dt_cadastro"] = "sysdate()";
        $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
        
        
        $pk = Util::execInsert("colaboradores_nome_filho", $fields,$this->pdo);

       
        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $pk;
        
        return $retorno;
    }

    public function salvarBeneficio($colaborador_beneficio){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['vl_beneficio'] = $colaborador_beneficio['vl_beneficio'];
        $fields['obs'] = $colaborador_beneficio['obs'];
        $fields['ic_status'] = $colaborador_beneficio['ic_status'];
        $fields['beneficios_pk'] = $colaborador_beneficio['beneficios_pk'];
        $fields['colaborador_pk'] = $colaborador_beneficio['colaborador_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];


        $fields["dt_cadastro"] = "sysdate()";
        $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
        
        
        $pk = Util::execInsert("colaboradores_beneficios", $fields,$this->pdo);

        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $pk;
        
        return $retorno;

    }

    public function salvarAfastamento($afastamento_ferias_colaborador){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['tipo_apontamento'] = $afastamento_ferias_colaborador['tipo_apontamento'];
        $fields['dt_inicio'] = $afastamento_ferias_colaborador['dt_inicio'];
        $fields['dt_fim'] = $afastamento_ferias_colaborador['dt_fim'];
        $fields['ds_obs'] = $afastamento_ferias_colaborador['ds_obs'];
        $fields['colaborador_pk'] = $afastamento_ferias_colaborador['colaborador_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];


        $fields["dt_cadastro"] = "sysdate()";
        $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];




        $pk = Util::execInsert("afastamento_ferias_colaborador", $fields,$this->pdo);

        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $pk;

        return $retorno;

    }

    public function salvarCurso($colaborador_curso){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();$fields = array();
        $fields['colaboradores_pk'] = $colaborador_curso['colaboradores_pk'];
        $fields['cursos_pk'] = $colaborador_curso['cursos_pk'];
        if($colaborador_curso['dt_execucao']!=""){
            $fields['dt_execucao'] = Util::DataYMD($colaborador_curso['dt_execucao']);
        }
        if($colaborador_curso['dt_validacao']!=""){
            $fields['dt_validacao'] = Util::DataYMD($colaborador_curso['dt_validacao']);
        }

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];


        $fields["dt_cadastro"] = "sysdate()";
        $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
        
        
        $pk = Util::execInsert("colaboradores_curso", $fields,$this->pdo);

        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $pk;
        
        return $retorno;

    }
    public function listarTodos($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="SELECT distinct(c.pk) pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,c.ds_colaborador ";
        $sql.="       ,c.ds_cel ";
        $sql.="       ,c.ic_whatsapp ";
        $sql.="       ,c.ic_whatsapp2 ";
        $sql.="       ,c.ic_whatsapp3 ";
        $sql.="       ,case c.ic_whatsapp when 1 then 'Sim' when 2 then 'Não' end ic_whatsapp ";
        $sql.="       ,c.ds_cel2 ";
        $sql.="       ,case c.ic_whatsapp2 when 1 then 'Sim' when 2 then 'Não' end ic_whatsapp2 ";
        $sql.="       ,c.ds_cel3 ";
        $sql.="       ,case c.ic_whatsapp3 when 1 then 'Sim' when 2 then 'Não' end ic_whatsapp3 ";
        $sql.="       ,c.ds_email ";
        $sql.="        ,c.ds_matricula";
        $sql.="       ,c.ds_rg ";
        $sql.="       ,c.ds_cpf ";
        $sql.="       ,date_format(c.dt_nascimento,'%d/%m/%Y')dt_nascimento ";
        $sql.="       ,c.ds_endereco ";
        $sql.="       ,c.ds_numero ";
        $sql.="       ,c.ds_complemento ";
        $sql.="       ,c.ds_cartao_sus ";
        $sql.="       ,c.ic_experiencia";
        $sql.="       ,c.ds_bairro ";
        $sql.="       ,c.ds_cep ";
        $sql.="       ,c.ds_cidade ";
        $sql.="       ,c.ic_tipo_sanguineo";
        $sql.="       ,c.ds_uf ";
        $sql.="       ,c.ic_reserva";
        $sql.="       ,case c.ic_status when 1 then 'Ativo' when 2 then 'Demitido' when 3 then 'Afastado' when 4 then 'Férias' end ic_status ";
        $sql.="       ,case c.ic_origem when 1 then 'Sistema' when 2 then 'Site' end ic_origem ";
        $sql.="       ,c.ic_funcionario";
        $sql.="       ,c.ds_pin";
        $sql.="       ,c.ds_re";
        $sql.="       ,c.ds_raca";
        $sql.="         ,date_format(c.dt_admissao,'%d/%m/%Y')dt_admissao";
        $sql.="         ,date_format(c.dt_demissao,'%d/%m/%Y')dt_demissao";
        $sql.="         ,c.ds_deficiencia_fisica";
        $sql.="         ,c.estado_civil";
        $sql.="         ,c.ds_nome_pai";
        $sql.="         ,c.ds_nome_mae";
        $sql.="         ,c.ds_nome_conjuge";
        $sql.="         ,c.dt_nascimento_conjuge";
        $sql.="         ,c.ds_cpf_conjuge";
        $sql.="         ,c.ds_tel_conjuge";
        $sql.="         ,c.regime_casamento";
        $sql.="         ,c.ds_ctps";
        $sql.="         ,c.ds_serie";
        $sql.="         ,c.dt_expedicao";
        $sql.="         ,c.ds_uf_rg";
        $sql.="         ,c.ds_org_exp";
        $sql.="         ,c.ds_pis";
        $sql.="         ,c.ds_titulo_eleitoral";
        $sql.="         ,c.ds_zona_eleitoral";
        $sql.="         ,c.ds_secao";
        $sql.="         ,c.ds_certificado_reservista";
        $sql.="         ,c.ic_filho_menor_14";
        $sql.="         ,c.qtde_filho";
        $sql.="       ,c.ds_nacionalidade";
        $sql.="       ,psl.ds_imagem";
        $sql.="       ,c.generos_pk ";
        $sql.="       ,g.ds_genero ";
        $sql.="       ,ps.ds_produto_servico";
        $sql.="	,c.empresas_pk";
        $sql.="	,c.regime_contratacao_pk";
        $sql.="	,c.ds_carga_horaria_semanal";
        $sql.="        ,case c.grau_escolaridade_pk when 1 then 'Educação infantil' when 2 then 'Ensino Fundamental' when 3 then 'Ensino Médio' when 4 then 'Superior (Graduação)' when 5 then 'Pós-graduação' when 6 then 'Mestrado' when 7 then 'Doutorado' end ds_escolaridade";
        
        $sql.="	,c.tipo_conta_bancaria";
        $sql.=" ,c.grau_escolaridade_pk";
        $sql.="	,c.ds_agencia";
        $sql.="	,c.ds_conta";
        $sql.="	,c.ds_digito";
        $sql.="	,c.bancos_pk";
        $sql.="	,c.ds_n_sapato";
        $sql.="	,c.ds_n_camisa";
        $sql.="	,c.ds_n_calca";
        $sql.="	,c.ds_n_luva";       
        $sql.="	,c.ic_status";
        $sql.="	,c.ds_conta_favorecido";
        $sql.="       ,case when psl.ic_status = 1 then 'Liberado' when psl.ic_status = 2 then 'Pendente' end ds_status_app  ";
        $sql.=" ,l.ds_lead";
        $sql.="  from colaboradores c";
        $sql.="     inner join generos g on c.generos_pk = g.pk";
        $sql.="     left join agenda_colaborador_padrao a  on c.pk = a.colaboradores_pk";
        $sql.="     left join processos p ON p.leads_pk = c.pk";
        $sql.="     left join colaboradores_produtos_servicos cps  on c.pk = cps.colaboradores_pk";
        $sql.="     left join produtos_servicos ps  on ps.pk = cps.produtos_servicos_pk";
        $sql.="     LEFT JOIN ponto_solicitacao_liberacao_app psl ON c.pk = psl.colaborador_pk";
        $sql.="     LEFT JOIN leads l ON l.pk = a.leads_pk";
        $sql.=" where 1=1 ";
        if($pk!=""){
            $sql.=" and c.pk = ".$pk;
        }
        $sql.=" group by c.pk";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarTodosAtivo($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="SELECT distinct(c.pk) pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,c.ds_colaborador ";
        $sql.="       ,c.ds_cel ";
        $sql.="       ,c.ic_whatsapp ";
        $sql.="       ,c.ic_whatsapp2 ";
        $sql.="       ,c.ic_whatsapp3 ";
        $sql.="       ,case c.ic_whatsapp when 1 then 'Sim' when 2 then 'Não' end ic_whatsapp ";
        $sql.="       ,c.ds_cel2 ";
        $sql.="       ,case c.ic_whatsapp2 when 1 then 'Sim' when 2 then 'Não' end ic_whatsapp2 ";
        $sql.="       ,c.ds_cel3 ";
        $sql.="       ,case c.ic_whatsapp3 when 1 then 'Sim' when 2 then 'Não' end ic_whatsapp3 ";
        $sql.="       ,c.ds_email ";
        $sql.="        ,c.ds_matricula";
        $sql.="       ,c.ds_rg ";
        $sql.="       ,c.ds_cpf ";
        $sql.="       ,date_format(c.dt_nascimento,'%d/%m/%Y')dt_nascimento ";
        $sql.="       ,c.ds_endereco ";
        $sql.="       ,c.ds_numero ";
        $sql.="       ,c.ds_complemento ";
        $sql.="       ,c.ds_cartao_sus ";
        $sql.="       ,c.ic_experiencia";
        $sql.="       ,c.ds_bairro ";
        $sql.="       ,c.ds_cep ";
        $sql.="       ,c.ds_cidade ";
        $sql.="       ,c.ic_tipo_sanguineo";
        $sql.="       ,c.ds_uf ";
        $sql.="       ,c.ic_reserva";
        $sql.="       ,case c.ic_status when 1 then 'Ativo' when 2 then 'Demitido' when 3 then 'Afastado' when 4 then 'Férias' end ic_status ";
        $sql.="       ,case c.ic_origem when 1 then 'Sistema' when 2 then 'Site' end ic_origem ";
        $sql.="       ,c.ic_funcionario";
        $sql.="       ,c.ds_pin";
        $sql.="       ,c.ds_re";
        $sql.="       ,c.ds_raca";
        $sql.="         ,date_format(c.dt_admissao,'%d/%m/%Y')dt_admissao";
        $sql.="         ,date_format(c.dt_demissao,'%d/%m/%Y')dt_demissao";
        $sql.="         ,c.ds_deficiencia_fisica";
        $sql.="         ,c.estado_civil";
        $sql.="         ,c.ds_nome_pai";
        $sql.="         ,c.ds_nome_mae";
        $sql.="         ,c.ds_nome_conjuge";
        $sql.="         ,c.dt_nascimento_conjuge";
        $sql.="         ,c.ds_cpf_conjuge";
        $sql.="         ,c.ds_tel_conjuge";
        $sql.="         ,c.regime_casamento";
        $sql.="         ,c.ds_ctps";
        $sql.="         ,c.ds_serie";
        $sql.="         ,c.dt_expedicao";
        $sql.="         ,c.ds_uf_rg";
        $sql.="         ,c.ds_org_exp";
        $sql.="         ,c.ds_pis";
        $sql.="         ,c.ds_titulo_eleitoral";
        $sql.="         ,c.ds_zona_eleitoral";
        $sql.="         ,c.ds_secao";
        $sql.="         ,c.ds_certificado_reservista";
        $sql.="         ,c.ic_filho_menor_14";
        $sql.="         ,c.qtde_filho";
        $sql.="       ,c.ds_nacionalidade";
        $sql.="       ,psl.ds_imagem";
        $sql.="       ,c.generos_pk ";
        $sql.="       ,g.ds_genero ";
        $sql.="       ,ps.ds_produto_servico";
        $sql.="	,c.empresas_pk";
        $sql.="	,c.regime_contratacao_pk";
        $sql.="	,c.ds_carga_horaria_semanal";
        $sql.="        ,case c.grau_escolaridade_pk when 1 then 'Educação infantil' when 2 then 'Ensino Fundamental' when 3 then 'Ensino Médio' when 4 then 'Superior (Graduação)' when 5 then 'Pós-graduação' when 6 then 'Mestrado' when 7 then 'Doutorado' end ds_escolaridade";
        
        $sql.="	,c.tipo_conta_bancaria";
        $sql.=" ,c.grau_escolaridade_pk";
        $sql.="	,c.ds_agencia";
        $sql.="	,c.ds_conta";
        $sql.="	,c.ds_digito";
        $sql.="	,c.bancos_pk";
        $sql.="	,c.ds_n_sapato";
        $sql.="	,c.ds_n_camisa";
        $sql.="	,c.ds_n_calca";
        $sql.="	,c.ds_n_luva";       
        $sql.="	,c.ic_status";
        $sql.="	,c.ds_conta_favorecido";
        $sql.="       ,case when psl.ic_status = 1 then 'Liberado' when psl.ic_status = 2 then 'Pendente' end ds_status_app  ";
        $sql.=" ,l.ds_lead";
        $sql.=" ,l.pk leads_pk";
        $sql.=" ,a.pk agenda_colaborador_pk";
        $sql.=" ,t.ds_turno";
        $sql.="        ,date_format(a.dt_inicio_agenda,'%d/%m/%Y')dt_ini_escala";
        $sql.="        ,date_format(a.dt_fim_agenda,'%d/%m/%Y')dt_fim_escala";
        $sql.="        ,date_format(pt.dt_hora_ponto, '%d/%m/%Y %H:%i') dt_hora_ponto";
        $sql.="  from colaboradores c";
        $sql.="     inner join generos g on c.generos_pk = g.pk";
        $sql.="     left join agenda_colaborador_padrao a  on c.pk = a.colaboradores_pk and a.dt_cancelamento is null";
        $sql.="     LEFT JOIN turnos t ON a.turnos_pk = t.pk";
        $sql.="     left join processos p ON p.leads_pk = c.pk";
        $sql.="     left join colaboradores_produtos_servicos cps  on c.pk = cps.colaboradores_pk";
        $sql.="     left join produtos_servicos ps  on ps.pk = cps.produtos_servicos_pk";
        $sql.="     LEFT JOIN ponto_solicitacao_liberacao_app psl ON c.pk = psl.colaborador_pk";
        $sql.="     LEFT JOIN ponto pt ON c.pk = pt.colaborador_pk";
        $sql.="     LEFT JOIN leads l ON l.pk = a.leads_pk";
        $sql.=" where 1=1 ";
        if($pk!=""){
            $sql.=" and c.pk = ".$pk;
        }
        $sql.=" and c.ic_status=1";
        $sql.=" group by c.pk";
        $sql.=" ORDER by pt.dt_hora_ponto desc";
      
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarColaboradorLeadCalendario($leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,c.ds_colaborador ";
        $sql.="  from colaboradores c";
        $sql.="     left join agenda_colaborador_padrao a on a.colaboradores_pk = c.pk";
        $sql.="     left join processos p ON p.leads_pk = c.pk";
        $sql.=" where 1=1 ";
        if($leads_pk!=""){
            $sql.=" and a.leads_pk = ".$leads_pk;
        }
        $sql.=" and a.dt_cancelamento is null";
        $sql.=" group by c.pk,a.colaboradores_pk";
        $sql.=" order by TRIM(c.ds_colaborador) asc";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarColaboradoresQualificacao($ds_produtos_servicos, $colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql="";
        $sql.="SELECT c.pk, c.ds_colaborador";
        $sql.=" FROM colaboradores c";
        $sql.="     INNER JOIN colaboradores_produtos_servicos cps  ON c.pk = cps.colaboradores_pk";
        $sql.="     INNER JOIN produtos_servicos ps ON cps.produtos_servicos_pk = ps.pk";
        $sql.=" WHERE ps.ds_produto_servico = '".$ds_produtos_servicos."'";
        $sql.="   AND c.pk NOT IN (".$colaborador_pk.")";
        $sql.="   AND c.ic_status = 1";
        $sql.=" GROUP BY c.pk";
        $sql.=" ORDER BY c.ds_colaborador ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarColaboradorLead($leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,c.ds_colaborador ";
        $sql.="  from colaboradores c";
        $sql.="     left join agenda_colaborador_padrao a on a.colaboradores_pk = c.pk";
        $sql.="     left join processos_etapas pe ON pe.pk = a.processos_etapas_pk";
        $sql.="     left join processos p ON pe.processos_pk = p.pk";
        $sql.=" where 1=1 ";
        if($leads_pk!=""){
            $sql.=" and a.leads_pk = ".$leads_pk;
        }
        //$sql.=" and a.dt_cancelamento is null";
        $sql.=" group by c.pk,a.colaboradores_pk";
        $sql.=" order by TRIM(c.ds_colaborador) asc";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarColaboradoresQualidicacaoContrato($contratos_pk,$produtos_servicos_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="SELECT c.pk, c.ds_colaborador";
        $sql.=" FROM colaboradores c";
        $sql.="     INNER JOIN colaboradores_produtos_servicos cps  ON c.pk = cps.colaboradores_pk";
        $sql.="     INNER JOIN contratos_itens ci ON cps.produtos_servicos_pk = ci.produtos_servicos_pk";
        $sql.=" WHERE cps.produtos_servicos_pk = ".$produtos_servicos_pk;
        $sql.=" AND ci.contratos_pk =".$contratos_pk;
        //$sql.=" AND c.ic_status = 1";
        $sql.=" GROUP BY c.pk";
        $sql.=" ORDER BY c.ds_colaborador ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function RelatorioDadosColaborador($pk,$ic_status){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select ";
        $sql.="        c.pk";
        $sql.="        ,c.ds_colaborador ";
        $sql.="        ,date_format(c.dt_nascimento,'%d/%m/%Y')dt_nascimento";
        $sql.="        ,case c.grau_escolaridade_pk when 1 then 'Educação infantil' when 2 then 'Ensino Fundamental' when 3 then 'Ensino Médio' when 4 then 'Superior (Graduação)' when 5 then 'Pós-graduação' when 6 then 'Mestrado' when 7 then 'Doutorado' end ds_escolaridade";
        $sql.="        ,c.ds_rg";
        $sql.="        ,c.ds_cpf";
        $sql.="        ,ct.ds_razao_social";
        $sql.="        ,c.empresas_pk";
        $sql.="        ,case c.regime_contratacao_pk when 1 then 'Mensalista' when 2 then 'Horista' end ds_regime_contratacao";
        $sql.="        ,date_format(c.dt_admissao,'%d/%m/%Y')dt_admissao";
        $sql.="        ,date_format(c.dt_demissao,'%d/%m/%Y')dt_demissao";
        $sql.="        ,c.ds_carga_horaria_semanal";
        $sql.="        ,c.vl_salario";
        $sql.="        ,c.ds_cel";
        $sql.="        ,c.ds_endereco";
        $sql.="        ,c.ds_numero";
        $sql.="        ,c.ds_complemento";
        $sql.="        ,c.ds_bairro";
        $sql.="        ,c.ds_cep";
        $sql.="        ,c.ds_cidade";
        $sql.="        ,c.ds_uf";
        $sql.="        ,c.ds_pin";
        $sql.="        ,c.ds_matricula";
        $sql.="  from colaboradores c ";
        $sql.="  left join contas ct on ct.pk = c.empresas_pk ";
        $sql.=" where 1=1 ";
        if($pk!=""){
            $sql.=" and c.pk =".$pk;
        }
        if(empty($ic_status)){
            if($ic_status==1){
                $sql." and c.dt_demissão is not null";
            }elseif($ic_status==2){
                $sql." and c.dt_demissão is null";
            }

        }

        $sql.=" group by c.pk";
        $sql.=" order by c.ds_colaborador";
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        for($i=0;$i<count($query);$i++){

            if($query[$i]['ds_endereco']==""){
                $ds_endereco = "";
            }
            else{
                $ds_endereco = $query[$i]['ds_endereco']." - ".$query[$i]['ds_numero'].", ".$query[$i]['ds_complemento']." / ".$query[$i]['ds_bairro']." / ".$query[$i]['ds_cidade']." - ".$query[$i]['ds_uf'];
            }

            $sqlItensGrupos ="";
            $sqlItensGrupos = "";
            $sqlItensGrupos.="select ";
            $sqlItensGrupos.="   ps.ds_produto_servico ";
            $sqlItensGrupos.="  from colaboradores_produtos_servicos c ";
            $sqlItensGrupos.="  inner join produtos_servicos ps on ps.pk = c.produtos_servicos_pk";
            $sqlItensGrupos.=" where 1=1 ";
            if($query[$i]['pk']!=""){
                $sqlItensGrupos.=" and c.colaboradores_pk = ".$query[$i]['pk'];
            }
            $sqlItensGrupos.=" group by c.colaboradores_pk";
            $stmt = $this->pdo->prepare( $sqlItensGrupos );
            $stmt->execute();
            $queryItens = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ds_qualificacao = "";
            if(count($queryItens) > 0){
                
                for($j = 0; $j < count($queryItens); $j++){
                    $ds_qualificacao = $queryItens[$j]['ds_produto_servico'].",";
                }
            }
            $result[] = array(
                "ds_colaborador" => $query[$i]["ds_colaborador"],
                "dt_nascimento"=>$query[$i]['dt_nascimento'],
                "ds_qualificacao"=>$ds_qualificacao,
                "ds_escolaridade"=>$query[$i]['ds_escolaridade'],
                "ds_rg"=>$query[$i]['ds_rg'],
                "ds_cpf"=>$query[$i]['ds_cpf'],
                "ds_razao_social"=>$query[$i]['ds_razao_social'],
                "dt_admissao"=>$query[$i]['dt_admissao'],
                "ds_regime_contratacao"=>$query[$i]['ds_regime_contratacao'],
                "ds_carga_horaria_semanal"=>$query[$i]['ds_carga_horaria_semanal'],
                "vl_salario"=>$query[$i]['vl_salario'],
                "ds_cel"=>$query[$i]['ds_cel'],
                "dt_demissao"=>$query[$i]['dt_demissao'],
                "ds_pin"=>$query[$i]['ds_pin'],
                "ds_matricula"=>$query[$i]['ds_matricula'],
                "ds_endereco"=>$ds_endereco
            );
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;

        return $retorno;
    }
    public function listarPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk  ";
        $sql.="       ,c.ds_colaborador ";
        $sql.="       ,c.ds_cel ";
        $sql.="       ,c.ic_whatsapp ";
        $sql.="       ,c.ds_cel2 ";
        $sql.="       ,c.ic_whatsapp2 ";
        $sql.="       ,c.ds_cel3 ";
        $sql.="       ,c.ic_whatsapp3 ";
        $sql.="       ,c.ds_email ";
        $sql.="       ,c.ds_rg ";
        $sql.="       ,c.ds_cpf ";
        $sql.="       ,date_format(c.dt_nascimento,'%d/%m/%Y')dt_nascimento ";
        $sql.="       ,c.ds_endereco ";
        $sql.="       ,c.ds_numero ";
        $sql.="       ,c.ds_complemento ";
        $sql.="       ,c.ds_bairro ";
        $sql.="       ,c.ds_cep ";
        $sql.="       ,c.ds_cidade ";
        $sql.="       ,c.ds_uf ";
        $sql.="       ,c.ic_status ";
        $sql.="       ,c.ic_funcionario ";
        $sql.="       ,c.generos_pk ";
        $sql.="       ,c.ds_pin";
        $sql.="       ,c.ds_raca";
        $sql.="         ,c.ds_deficiencia_fisica";
        $sql.="         ,c.estado_civil";
        $sql.="         ,case c.estado_civil when 1 then 'Solteiro' when 2 then 'Casado' when 3 then 'Separado' when 4 then 'Divorciado' when 5 then 'Viuvo' end ds_estado_civil";
        $sql.="         ,c.ds_nome_pai";
        $sql.="         ,c.ds_nome_mae";
        $sql.="         ,c.ds_nome_conjuge";
        $sql.="         ,c.dt_nascimento_conjuge ";
        $sql.="         ,c.ds_cpf_conjuge";
        $sql.="         ,c.ds_tel_conjuge";
        $sql.="         ,c.regime_casamento";
        $sql.="         ,c.ds_ctps";
        $sql.="         ,c.ds_serie";
        $sql.="       ,date_format(c.dt_expedicao,'%d/%m/%Y')dt_expedicao ";
        $sql.="         ,c.ds_uf_rg";
        $sql.="         ,c.ds_org_exp";
        $sql.="         ,c.ds_pis";
        $sql.="         ,c.ds_titulo_eleitoral";
        $sql.="         ,c.ds_zona_eleitoral";
        $sql.="         ,c.ds_secao";
        $sql.="         ,c.ds_certificado_reservista";
        $sql.="         ,c.ic_filho_menor_14";
        $sql.="         ,c.ic_reserva";
        $sql.="         ,date_format(c.dt_admissao,'%d/%m/%Y')dt_admissao";
        $sql.="         ,date_format(c.dt_demissao,'%d/%m/%Y')dt_demissao";
        $sql.="       ,cps.produtos_servicos_pk ";
        $sql.="       ,up.pk colaborador_ponto_pk";
        $sql.="       ,up.ic_registrar_ponto";
        $sql.="       ,psl.pk liberar_acesso_ponto";
        $sql.="       ,psl.img_colaborador_cadastro ds_imagem";
        $sql.="       ,date_format(psl.dt_liberacao,'%d/%m/%Y %H:%i:%s') dt_liberacao";
        $sql.="       ,case";
        $sql.="            when psl.ic_status = 1 then 'Liberado'";
        $sql.="            when psl.ic_status = 2 then 'Pendente'";
        $sql.="            else 'Não solicitado'";
        $sql.="        end ds_status_app";
        $sql.="       ,c.ds_re";
        $sql.="       ,c.ds_matricula";
        $sql.="       ,c.ds_nacionalidade";
        $sql.="       ,case c.grau_escolaridade_pk when 1 then 'Educação infantil' when 2 then 'Ensino Fundamental' when 3 then 'Ensino Médio' when 4 then 'Superior (Graduação)' when 5 then 'Pós-graduação' when 6 then 'Mestrado' when 7 then 'Doutorado' end ds_escolaridade";
        $sql.="       ,c.grau_escolaridade_pk";
        $sql.="       ,c.qtde_filho";
        $sql.="       ,c.empresas_pk";
        $sql.="       ,c.regime_contratacao_pk";
        $sql.="       ,c.ds_carga_horaria_semanal";
        $sql.="        ,c.tipo_conta_bancaria";
        $sql.="	,c.ds_agencia";
        $sql.="	,c.ds_conta";
        $sql.="	,c.ds_digito";
        $sql.="	,c.bancos_pk";
        $sql.="	,c.vl_salario";
        $sql.="	,g.ds_genero";
        $sql.="	,b.ds_banco";
        $sql.="	,c.ds_pix";
        $sql.="	,c.ds_conta_favorecido";

        $sql.="	,c.ds_n_sapato";
        $sql.="	,c.ds_n_camisa";
        $sql.="	,c.ds_n_calca";
        $sql.="	,c.ds_n_luva";

        $sql.="	,c.ds_senha_portal";

        $sql.="	,ps.ds_produto_servico";
        $sql.=" ,co.ds_razao_social ds_razao_social_empresa";
        $sql.=" ,co.ds_cpf_cnpj ds_cpf_cnpj_empresa";
        $sql.=" ,co.ds_tel ds_tel_empresa";
        $sql.=" ,co.ds_email ds_email_empresa";
        $sql.=" ,co.ds_cel ds_cel_empresa";
        $sql.=" ,co.ds_cep ds_cep_empresa";
        $sql.=" ,co.ds_endereco ds_endereco_empresa";
        $sql.=" ,co.ds_numero ds_numero_empresa";
        $sql.=" ,co.ds_complemento ds_complemento_empresa";
        $sql.=" ,co.ds_bairro ds_bairro_empresa";
        $sql.=" ,co.ds_cidade ds_cidade_empresa";
        $sql.=" ,co.ds_uf ds_uf_empresa";

        $sql.="	,c.ic_tipo_sanguineo";
        $sql.="	,c.ds_cartao_sus";
        $sql.="	,c.ic_tipo_sanguineo_conjuge";
        $sql.="	,c.ic_ds_cartao_sus_conjuge";

        $sql.="	,c.ic_experiencia";
        $sql.="	,a.pk agenda_colaborador_pk";

        $sql.="  from colaboradores c ";
        $sql.="     left join generos g  on g.pk = c.generos_pk";
        $sql.="     left join bancos b  on b.pk = c.bancos_pk";
        $sql.="     left join colaboradores_produtos_servicos cps  on c.pk = cps.colaboradores_pk";
        $sql.="     left join agenda_colaborador_padrao a  on a.colaboradores_pk = c.pk";
        $sql.="     left join produtos_servicos ps  on cps.produtos_servicos_pk = ps.pk";
        $sql.="     left join usuario_ponto up on c.pk = up.colaborador_pk";
        $sql.="     left join ponto_solicitacao_liberacao_app psl on c.pk = psl.colaborador_pk";
        $sql.="     left join contas co on co.pk = c.empresas_pk";
        $sql.=" where c.pk = $pk ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['ds_imagem'] = $row['ds_imagem'] ?? null;
            $row['dt_liberacao'] = $row['dt_liberacao'] ?? null;
            $row['ds_status_app'] = $row['ds_status_app'] ?? 'Não solicitado';
            $row['liberar_acesso_ponto'] = $row['liberar_acesso_ponto'] ?? null;
        }


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function relatorioAniversariantesMes($mes,$grid){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select c.ds_colaborador";
        $sql.="       ,date_format(c.dt_nascimento, '%d/%m/%Y')dt_nascimento ";
        $sql.="       ,l.ds_lead ";
        $sql.="       ,a.n_qtde_dias_semana ";
        $sql.="  from colaboradores c";
        $sql.="  left join agenda_colaborador_padrao a on a.colaboradores_pk = c.pk";
        $sql.="  left join leads l on l.pk = a.leads_pk";
        $sql.=" where 1=1 ";
        if($mes!=""){
            $sql.=" and MONTH(dt_nascimento) = ".$mes;
            $sql.=" and c.ic_status = 1";
        }
        $sql.=" order by c.ds_colaborador asc ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rows);
        $retorno->iTotalRecords = count($rows);
        if($grid==1){
            echo json_encode($retorno);
            exit(0);
        }
        else{
            return $retorno;
        }
        
    }

    public function RelatorioColaboradorCurso($colaboradores_pk,$cursos_pk,$dt_execucao_ini,$dt_execucao_fim,$dt_validacao_ini,$dt_validacao_fim,$grid){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select cc.pk, cc.dt_cadastro, cc.usuario_cadastro_pk, cc.dt_ult_atualizacao, cc.usuario_ult_atualizacao_pk ";
        $sql.="       ,cc.colaboradores_pk ";
        $sql.="       ,cl.ds_colaborador";
        $sql.="       ,cc.cursos_pk ";
        $sql.="       ,c.ds_curso";
        $sql.="       ,date_format(cc.dt_execucao,'%d/%m/%Y')dt_execucao";
        $sql.="       ,date_format(cc.dt_validacao,'%d/%m/%Y')dt_validacao";

        $sql.="  from colaboradores_curso cc";
        $sql.="       inner join colaboradores cl on cl.pk = cc.colaboradores_pk";
        $sql.="       inner join cursos c on c.pk = cc.cursos_pk";
        $sql.=" where 1=1 ";
        if($colaboradores_pk!=""){
            $sql.=" and cc.colaboradores_pk=".$colaboradores_pk;
        }
        if($cursos_pk!=""){
            $sql.=" and cc.cursos_pk=".$cursos_pk;
        }
        if($dt_execucao_ini!=""){
            $sql.=" and cc.dt_execucao between '".Util::DataYMD($dt_execucao_ini)."' and '".Util::DataYMD($dt_execucao_fim)."'";
        }
        if($dt_validacao_ini!=""){
            $sql.=" and cc.dt_validacao between '".Util::DataYMD($dt_validacao_ini)."' and '".Util::DataYMD($dt_validacao_fim)."'";
        }
        $sql.=" order by cc.colaboradores_pk asc ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rows);
        $retorno->iTotalRecords = count($rows);
        if($grid==1){
            echo json_encode($retorno);
            exit(0);
        }
        else{
            return $retorno;
        }
        
    }


    public function listarDsPin($colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select c.pk";
        $sql.="       ,c.ds_colaborador ";
        $sql.="       ,c.ds_pin ";
        $sql.="  from colaboradores c";
        $sql.=" where 1=1 ";
        if($colaborador_pk!=""){
            $sql.=" and c.pk = ".$colaborador_pk;
        }
        $sql.=" order by c.ds_colaborador asc ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    
	public function listarColaboradorPorLead( $leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select c.pk";
        $sql.="       ,c.ds_colaborador ";
        $sql.="  from colaboradores c";
        $sql.="     inner join generos g on c.generos_pk = g.pk";
        $sql.="     left join agenda_colaborador_padrao a  on c.pk = a.colaboradores_pk and a.dt_cancelamento is null";
        $sql.="     left join processos_etapas pe ON a.processos_etapas_pk = pe.pk";
        $sql.="     left join processos p ON pe.processos_pk = p.pk";
        $sql.="     inner JOIN leads l ON l.pk = a.leads_pk";
        $sql.=" where 1=1 ";

        if($leads_pk != ""){
            $sql.=" and a.leads_pk=".$leads_pk;
        }
       

        $sql.=" group by c.pk";
        $sql.=" order by c.ds_colaborador asc ";
    
        

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        //if($leads_pk!=""){
            $retorno->data = $rows;
            $retorno->iTotalDisplayRecords = count($rows);
            $retorno->iTotalRecords = count($rows);
        /*}
        else{
            $retorno->data = [];
            $retorno->iTotalDisplayRecords = 0;
            $retorno->iTotalRecords = 0;
        }*/
        
        
        echo json_encode($retorno);
        exit(0);
    }
    public function listarGrid($colaborador_pk, $ic_status, $leads_pk, $ic_origem, $ds_pin, $ds_cpf, $generos_pk, $ds_re, $ic_status_app, $ic_reserva, $ds_produto_servico){
        $retorno = new \StdClass;
        $retorno->status = false;
        $retorno->data = [];

        $displayStart = (isset($_GET['start']) && is_numeric($_GET['start'])) ? intval($_GET['start']) : 0;
        $displayRange = (isset($_GET['length']) && is_numeric($_GET['length'])) ? intval($_GET['length']) : 10;
        $lengthSql = " LIMIT {$displayRange} OFFSET {$displayStart}";

        $search = "";
        if (isset($_GET['search']['value']) && $_GET['search']['value'] != '') {
            $pesq = $_GET['search']['value'];
            $search .= " AND (
                l.ds_lead LIKE '%{$pesq}%' OR 
                c.pk LIKE '%{$pesq}%' OR 
                c.ds_colaborador LIKE '%{$pesq}%' OR 
                c.ds_pin LIKE '%{$pesq}%' OR 
                c.ds_cpf LIKE '%{$pesq}%' OR 
                c.ds_re LIKE '%{$pesq}%'
            )";
        }

        // Consulta externa do status do app desabilitada para não bloquear a listagem.
        
        $sql = "SELECT 
                    c.pk t_pk,
                    c.ds_colaborador t_ds_colaborador,
                    c.ds_cpf,
                    c.ds_cel t_ds_cel,
                    a.leads_pk,
                    a.pk agenda_colaborador_pk,
                    CASE c.ic_status 
                        WHEN 1 THEN 'Ativo' 
                        WHEN 2 THEN 'Demitido' 
                        WHEN 3 THEN 'Afastado' 
                        WHEN 4 THEN 'Férias' 
                    END t_ic_status,
                    c.ds_pin t_ds_pin,
                    c.ds_re t_ds_re,
                    ps.ds_produto_servico t_ds_funcao,
                    t.ds_turno,
                    concat(a.hr_inicio_expediente,' - ',a.hr_termino_expediente)escala,
                    case
                        when psl.ic_status = 1 then 'Liberado'
                        when psl.ic_status = 2 then 'Pendente'
                        else 'Não fez o novo cadastro'
                    end ds_status_app,
                    l.ds_lead
                FROM colaboradores c
                INNER JOIN generos g ON c.generos_pk = g.pk
                LEFT JOIN agenda_colaborador_padrao a 
                    ON a.colaboradores_pk = c.pk
                    AND a.pk = (
                        SELECT ap.pk
                        FROM agenda_colaborador_padrao ap
                        WHERE ap.colaboradores_pk = c.pk
                        ORDER BY (ap.dt_cancelamento IS NOT NULL), ap.dt_cadastro DESC
                        LIMIT 1
                    ) 
                LEFT JOIN turnos t ON a.turnos_pk = t.pk
                LEFT JOIN processos_etapas pe ON a.processos_etapas_pk = pe.pk
                LEFT JOIN processos p ON pe.processos_pk = p.pk
                LEFT JOIN colaboradores_produtos_servicos cps ON c.pk = cps.colaboradores_pk
                LEFT JOIN produtos_servicos ps ON ps.pk = cps.produtos_servicos_pk
                LEFT JOIN ponto_solicitacao_liberacao_app psl ON c.pk = psl.colaborador_pk
                LEFT JOIN leads l ON l.pk = a.leads_pk
                WHERE 1=1 
                {$search}";

        // Filtros opcionais
        if (!empty($colaborador_pk)) $sql .= " AND c.pk = " . intval($colaborador_pk);
        if($ic_status_app==3 && empty($ic_status)){
            $sql .= " AND c.ic_status = 2" ;
        }
        if (!empty($ic_status)) $sql .= " AND c.ic_status = " . intval($ic_status);
        if (!empty($leads_pk)) $sql .= " AND p.leads_pk = " . intval($leads_pk);
        if (!empty($ic_origem)) $sql .= " AND c.ic_origem = " . intval($ic_origem);
        if (!empty($ds_pin)) $sql .= " AND c.ds_pin LIKE '%{$ds_pin}%'";
        if (!empty($ds_cpf)) $sql .= " AND c.ds_cpf = '{$ds_cpf}'";
        if (!empty($generos_pk)) $sql .= " AND c.generos_pk = " . intval($generos_pk);
        if (!empty($ds_re)) $sql .= " AND c.ds_re = '{$ds_re}'";
        if (!empty($ic_status_app) && $ic_status_app != 3) $sql .= " AND psl.ic_status = " . intval($ic_status_app);
        if (!empty($ic_reserva)) $sql .= " AND c.ic_reserva = " . intval($ic_reserva);
        if (!empty($ds_produto_servico)) $sql .= " AND ps.pk = " . intval($ds_produto_servico);

        $sql .= " GROUP BY c.pk ORDER BY c.ds_colaborador ASC";

        $stmt = $this->pdo->prepare($sql . $lengthSql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmtCount = $this->pdo->prepare($sql);
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


    public function listarGridCliente($colaborador_pk, $ic_status, $leads_pk, $ic_origem, $ds_pin, $ds_cpf, $generos_pk, $ds_re, $ic_status_app, $ic_reserva, $ds_produto_servico){
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
        } $search = "";

        if (isset($_GET['search']['value']) and $_GET['search']['value'] != '') {

            $pesq = $_GET['search']['value'];
            $search .= " AND (
                            l.ds_lead LIKE '%".$pesq."%' OR 
                            c.pk LIKE '%".$pesq."%' OR 
                            c.ds_colaborador LIKE '%".$pesq."%' OR 
                            c.ds_pin LIKE '%".$pesq."%' OR 
                            c.ds_re LIKE '%".$pesq."%'
                            )";
        }

        $sql ="";
        $sql.="select c.pk t_pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,c.ds_colaborador t_ds_colaborador";
        $sql.="       ,c.ds_cel t_ds_cel";
        $sql.="       ,case c.ic_whatsapp when 1 then 'Sim' when 2 then 'Não' end ic_whatsapp ";
        $sql.="       ,c.ds_cel2 t_ds_cel2";
        $sql.="       ,case c.ic_whatsapp2 when 1 then 'Sim' when 2 then 'Não' end ic_whatsapp2 ";
        $sql.="       ,c.ds_cel3 ";
        $sql.="       ,case c.ic_whatsapp3 when 1 then 'Sim' when 2 then 'Não' end ic_whatsapp3 ";
        $sql.="       ,c.ds_email ";
        $sql.="       ,c.ds_rg ";
        $sql.="       ,c.ds_cpf ";
        $sql.="       ,date_format(c.dt_nascimento,'%d/%m/%Y')dt_nascimento ";
        $sql.="       ,c.ds_endereco ";
        $sql.="       ,c.ds_numero ";
        $sql.="       ,c.ds_complemento ";
        $sql.="       ,c.ds_bairro ";
        $sql.="       ,c.ds_cep ";
        $sql.="       ,c.ds_cidade ";
        $sql.="       ,c.ds_uf ";
        $sql.="       ,c.ic_reserva ";
        $sql.="       ,case c.ic_status when 1 then 'Ativo' when 2 then 'Demitido' when 3 then 'Afastado' when 4 then 'Férias' end t_ic_status ";
        $sql.="       ,case c.ic_origem when 1 then 'Sistema' when 2 then 'Site' end t_ic_origem ";
        $sql.="       ,c.ic_funcionario";
        $sql.="       ,c.ds_pin t_ds_pin";
        $sql.="       ,c.ds_re t_ds_re";
        $sql.="       ,c.ds_raca";
        $sql.="         ,date_format(c.dt_admissao,'%d/%m/%Y')dt_admissao";
        $sql.="         ,date_format(c.dt_demissao,'%d/%m/%Y')dt_demissao";
        $sql.="         ,c.ds_deficiencia_fisica";
        $sql.="         ,c.estado_civil";
        $sql.="         ,c.ds_nome_pai";
        $sql.="         ,c.ds_nome_mae";
        $sql.="         ,c.ds_nome_conjuge";
        $sql.="         ,c.dt_nascimento_conjuge";
        $sql.="         ,c.ds_cpf_conjuge";
        $sql.="         ,c.ds_tel_conjuge";
        $sql.="         ,c.regime_casamento";
        $sql.="         ,c.ds_ctps";
        $sql.="         ,c.ds_serie";
        $sql.="         ,c.dt_expedicao";
        $sql.="         ,c.ds_uf_rg";
        $sql.="         ,c.ds_org_exp";
        $sql.="         ,c.ds_pis";
        $sql.="         ,c.ds_titulo_eleitoral";
        $sql.="         ,c.ds_zona_eleitoral";
        $sql.="         ,c.ds_secao";
        $sql.="         ,c.ds_certificado_reservista";
        $sql.="         ,c.ic_filho_menor_14";
        $sql.="         ,c.qtde_filho";
        //$sql.="       ,c.generos_pk ";
        $sql.="       ,g.ds_genero generos_pk ";
        $sql.="       ,ps.ds_produto_servico t_ds_funcao";
        $sql.="	,c.empresas_pk";
        $sql.="	,c.regime_contratacao_pk";
        $sql.="	,c.ds_carga_horaria_semanal";

        $sql.="	,c.tipo_conta_bancaria";
        $sql.="	,c.ds_agencia";
        $sql.="	,c.ds_conta";
        $sql.="	,c.ds_digito";
        $sql.="	,c.bancos_pk";
        $sql.="       ,case when psl.ic_status = 1 then 'Liberado' when psl.ic_status = 2 then 'Pendente' end ds_status_app  ";
        $sql.=" ,l.ds_lead";
        $sql.="  from colaboradores c";
        $sql.="     inner join generos g on c.generos_pk = g.pk";
        $sql.="     inner join agenda_colaborador_padrao a  on c.pk = a.colaboradores_pk";
        $sql.="     left join processos_etapas pe  on a.processos_etapas_pk = pe.pk";
        $sql.="     left join processos p  on pe.processos_pk = p.pk";
        $sql.="     left join colaboradores_produtos_servicos cps  on c.pk = cps.colaboradores_pk";
        $sql.="     left join produtos_servicos ps  on ps.pk = cps.produtos_servicos_pk";
        $sql.="     LEFT JOIN ponto_solicitacao_liberacao_app psl ON c.pk = psl.colaborador_pk";
        $sql.="     inner JOIN leads l ON l.pk = a.leads_pk";
        $sql.=" where 1=1 ";
        $sql.= $search;


        if($colaborador_pk != ""){
            $sql.=" and c.pk =".$colaborador_pk;
        }
        if($ic_status != ""){
            $sql.=" and c.ic_status =".$ic_status;
        }
        if($leads_pk != ""){
            $sql.=" and l.pk=".$leads_pk;
        }
        else{
            $sql.=" and (l.pk =".$_SESSION['session_user']['par6']."  or l.leads_pai_pk =".$_SESSION['session_user']['par6'].")";
        }
        if($ic_origem != ""){
            $sql.=" and c.ic_origem=".$ic_origem;
        }
        if($ds_pin != ""){
            $sql.=" and c.ds_pin like '%".$ds_pin."%' ";
        }
        if($ds_cpf != ""){
            $sql.=" and c.ds_cpf = '".$ds_cpf."' ";
        }
        if($generos_pk != ""){
            $sql.=" and c.generos_pk=".$generos_pk;
        }
        if($ds_re != ""){
            $sql.=" and c.ds_re='".$ds_re."'";
        }
        if($ic_status_app != ""){
            $sql.=" and psl.ic_status=".$ic_status_app;
        }
        if($ic_reserva != ""){
            $sql.=" and c.ic_reserva=".$ic_reserva;
        }
        if($ds_produto_servico != ""){
            $sql.=" and ps.pk=".$ds_produto_servico;
        }

       // $sql.=" and c.ic_status = 1";
        $sql.=" group by c.pk";
        $sql.=" order by c.ds_colaborador asc ";




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
    public function listarFormulario(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select f.pk, f.dt_cadastro, f.usuario_cadastro_pk, f.dt_ult_atualizacao, f.usuario_ult_atualizacao_pk,f.ds_formulario,f.ds_link  ";
        $sql.="  from formulario f ";
        $sql.=" where f.ic_status = 1";





        $stmt = $this->pdo->prepare( $sql );
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

    public function listarColaboradorPkPrint($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk  ";
        $sql.="       ,c.ds_colaborador ";
        $sql.="       ,c.ds_cel ";
        $sql.="       ,c.ic_whatsapp ";
        $sql.="       ,c.ds_cel2 ";
        $sql.="       ,c.ic_whatsapp2 ";
        $sql.="       ,c.ds_cel3 ";
        $sql.="       ,c.ic_whatsapp3 ";
        $sql.="       ,c.ds_email ";
        $sql.="       ,c.ds_rg ";
        $sql.="       ,c.ds_cpf ";
        $sql.="       ,date_format(c.dt_nascimento,'%d/%m/%Y')dt_nascimento ";
        $sql.="       ,c.ds_endereco ";
        $sql.="       ,c.ds_numero ";
        $sql.="       ,c.ds_complemento ";
        $sql.="       ,c.ds_bairro ";
        $sql.="       ,c.ds_cep ";
        $sql.="       ,c.ds_cidade ";
        $sql.="       ,c.ds_uf ";
        $sql.="       ,c.ic_status ";
        $sql.="       ,c.ic_funcionario ";
        $sql.="       ,c.generos_pk ";
        $sql.="       ,c.ds_pin";
        $sql.="       ,c.ds_raca";
        $sql.="         ,c.ds_deficiencia_fisica";
        $sql.="         ,c.estado_civil";
        $sql.="         ,case c.estado_civil when '1' then 'Solteiro' when '2' then 'Casado' when '3' then 'Separado' when '4' then 'Divorciado' when '5' then 'Viuvo' when '6' then 'União Estável' end ds_estado_civil";
        $sql.="         ,c.ds_nome_pai";
        $sql.="         ,c.ds_nome_mae";
        $sql.="         ,c.ds_nome_conjuge";
        $sql.="       ,date_format(c.dt_nascimento_conjuge,'%d/%m/%Y')dt_nascimento_conjuge ";
        $sql.="         ,c.ds_cpf_conjuge";
        $sql.="         ,c.ds_tel_conjuge";
        $sql.="         ,c.regime_casamento";
        $sql.="         ,c.ds_ctps";
        $sql.="         ,c.ds_serie";
        $sql.="       ,date_format(c.dt_expedicao,'%d/%m/%Y')dt_expedicao ";
        $sql.="         ,c.ds_uf_rg";
        $sql.="         ,c.ds_org_exp";
        $sql.="         ,c.ds_pis";
        $sql.="         ,c.ds_titulo_eleitoral";
        $sql.="         ,c.ds_zona_eleitoral";
        $sql.="         ,c.ds_secao";
        $sql.="         ,c.ds_certificado_reservista";
        $sql.="         ,c.ic_filho_menor_14";
        $sql.="         ,c.ic_reserva";
        $sql.="         ,date_format(c.dt_admissao,'%d/%m/%Y')dt_admissao";
        $sql.="         ,date_format(c.dt_demissao,'%d/%m/%Y')dt_demissao";
        $sql.="       ,cps.produtos_servicos_pk ";
        $sql.="        ,case c.regime_contratacao_pk when 1 then 'M' when 2 then 'H' end ds_regime_contratacao";
        $sql.="       ,c.ds_re";
        $sql.="       ,c.ds_matricula";
        $sql.="       ,c.ds_nacionalidade";
        $sql.="       ,case c.grau_escolaridade_pk when 1 then 'Educação infantil' when 2 then 'Ensino Fundamental' when 3 then 'Ensino Médio' when 4 then 'Superior (Graduação)' when 5 then 'Pós-graduação' when 6 then 'Mestrado' when 7 then 'Doutorado' end ds_escolaridade";
        $sql.="       ,c.grau_escolaridade_pk";
        $sql.="       ,c.qtde_filho";        
        
        $sql.="       ,c.empresas_pk";
        $sql.="       ,c.regime_contratacao_pk";
        $sql.="       ,c.ds_carga_horaria_semanal";
        
        $sql.="        ,c.tipo_conta_bancaria";
        $sql.="	,c.ds_agencia";
        $sql.="	,c.ds_conta";
        $sql.="	,c.ds_digito";
        $sql.="	,c.bancos_pk";
        $sql.="	,c.vl_salario";
        $sql.="	,g.ds_genero";
        $sql.="	,b.ds_banco";
        $sql.="	,c.ds_pix";
        $sql.="	,c.ds_conta_favorecido";       
        
        $sql.="	,c.ds_n_sapato";
        $sql.="	,c.ds_n_camisa";
        $sql.="	,c.ds_n_calca";
        $sql.="	,c.ds_n_luva";        
        $sql.="	,a.ic_dom";        
        $sql.="	,a.ic_seg";        
        $sql.="	,a.ic_ter";        
        $sql.="	,a.ic_qua";        
        $sql.="	,a.ic_qui";        
        $sql.="	,a.ic_sex";        
        $sql.="	,a.ic_sab";        
        $sql.="	,a.hr_inicio_expediente";        
        $sql.="	,a.hr_termino_expediente";        
        $sql.="	,a.hr_saida_intervalo";        
        $sql.="	,a.hr_retorno_intervalo";        
        $sql.="	,ps.ds_produto_servico";
        $sql.="	,ct.tipo_conta_pk";
        $sql.="  from colaboradores c ";
        $sql.="     left join agenda_colaborador_padrao a  on a.colaboradores_pk = c.pk";
        
        $sql.="     left join generos g  on g.pk = c.generos_pk";
        $sql.="     left join bancos b  on b.pk = c.bancos_pk";
        $sql.="     left join colaboradores_produtos_servicos cps  on c.pk = cps.colaboradores_pk";
        $sql.="     left join produtos_servicos ps  on cps.produtos_servicos_pk = ps.pk";
        $sql.="     left join colaboradores_nome_filho cnf on c.pk = cnf.colaborador_pk";
        $sql.="  left join contas ct on ct.pk = c.empresas_pk ";
        $sql.=" where c.pk=".$pk;
    
        $sql.=" order by ds_colaborador asc ";

        
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $Colaboradoresquery = $stmt->fetchAll(\PDO::FETCH_ASSOC);



        if(count($Colaboradoresquery)){
            /// Filhos
            $sql ="select pk ";
            $sql.="      , date_format(dt_cadastro,'%d/%m/%Y') dt_cadastro ";
            $sql.="      , usuario_cadastro_pk ";
            $sql.="      , date_format(dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao ";
            $sql.="      , usuario_ult_atualizacao_pk ";

            $sql.="       ,colaborador_pk ";
            $sql.="       ,ds_nome_filho ";
            $sql.="       ,ds_cpf_filho ";
            $sql.="       ,date_format(dt_nascimento_filho,'%d/%m/%Y')dt_nascimento_filho ";


            $sql.="  from colaboradores_nome_filho ";
            $sql.=" where colaborador_pk = $pk ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $colaboradoresfilhosquery = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if(count($colaboradoresfilhosquery)>0){
                for($i = 0;$i < count($colaboradoresfilhosquery); $i++){
                    
                    $DadosColaboradorFilhos[] = array(
                        'pk'=>$colaboradoresfilhosquery[$i]['pk'],
                        'colaborador_pk'=>$colaboradoresfilhosquery[$i]['colaborador_pk'],
                        'ds_nome_filho'=>$colaboradoresfilhosquery[$i]['ds_nome_filho'],
                        'ds_cpf_filho'=>$colaboradoresfilhosquery[$i]['ds_cpf_filho'],
                        'dt_nascimento_filho'=>$colaboradoresfilhosquery[$i]['dt_nascimento_filho'],
                    );
                }
            }
            else{
                $DadosColaboradorFilhos = array();
            }
            
           
             /// Empresa
             if($Colaboradoresquery[0]['empresas_pk']!= null){
                $sql ="select pk ";
                $sql.="      , date_format(dt_cadastro,'%d/%m/%Y') dt_cadastro ";
                $sql.="      , usuario_cadastro_pk ";
                $sql.="      , date_format(dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao ";
                $sql.="      , usuario_ult_atualizacao_pk ";
    
                $sql.="       ,ds_conta ";
    
    
                $sql.="  from contas ";
                $sql.=" where pk = ".$Colaboradoresquery[0]['empresas_pk'] ;
               
                
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $Empresasquery = $stmt->fetchAll(\PDO::FETCH_ASSOC);
             }
             else{
                 $Empresasquery[0]['ds_conta'] = "";
             }
             /// Serviço
             $sql ="select cps.colaboradores_pk "; 
             $sql.="       ,cps.produtos_servicos_pk";
             $sql.="       ,cps.ic_possui_treinamento";
             $sql.="       ,cps.ic_possui_certificado";
             $sql.="       ,ps.ds_produto_servico";
             
 
             $sql.="  from colaboradores_produtos_servicos cps ";
             $sql .= "LEFT JOIN produtos_servicos ps ON cps.produtos_servicos_pk = ps.pk ";
             $sql.=" where colaboradores_pk = ".$pk;
            

             $stmt = $this->pdo->prepare($sql);
             $stmt->execute();
             $ColaboradoresServicoquery = $stmt->fetchAll(\PDO::FETCH_ASSOC);
             if(count($ColaboradoresServicoquery)>0){
                for($i = 0;$i < count($ColaboradoresServicoquery); $i++){
                    $DadosColaboradorServico[] = array(
                        'ic_possui_certificado'=>$ColaboradoresServicoquery[$i]['ic_possui_certificado'],
                        'ic_possui_treinamento'=>$ColaboradoresServicoquery[$i]['ic_possui_treinamento'],
                        'ds_produto_servico'=>$ColaboradoresServicoquery[$i]['ds_produto_servico'],
                    );
                }
             }
             else{
                $DadosColaboradorServico = array();
             }
             

             /// Beneficio
            $sql = "SELECT cb.vl_beneficio, cb.obs, cb.beneficios_pk, b.ds_beneficio ";
            $sql .= "FROM colaboradores_beneficios cb ";
            $sql .= "LEFT JOIN beneficios b ON cb.beneficios_pk = b.pk ";
            $sql .= "WHERE cb.colaborador_pk = ".$pk;          
             

             $stmt = $this->pdo->prepare($sql);
             $stmt->execute();
             $ColaboradorBeneficosquery = $stmt->fetchAll(\PDO::FETCH_ASSOC);
             if(count($ColaboradorBeneficosquery)>0){
                for($i = 0;$i < count($ColaboradorBeneficosquery); $i++){
                    $DadosColaboradorBeneficio[] = array(
                        'vl_beneficio'=>$ColaboradorBeneficosquery[$i]['vl_beneficio'],
                        'obs'=>$ColaboradorBeneficosquery[$i]['obs'],
                        'ds_beneficio'=>$ColaboradorBeneficosquery[$i]['ds_beneficio'],
                    );
                }
             }
             else{
                $DadosColaboradorBeneficio = array();
             }
            

        }

        $result[] = array (
            'pk'=>$pk,
            'ds_colaborador'=>$Colaboradoresquery[0]['ds_colaborador'],
            'ds_cel'=>$Colaboradoresquery[0]['ds_cel'],
            'ic_whatsapp'=>$Colaboradoresquery[0]['ic_whatsapp'], 
            'ds_cel2'=>$Colaboradoresquery[0]['ds_cel2'],
            'ic_whatsapp2'=>$Colaboradoresquery[0]['ic_whatsapp2'],
            'ds_cel3'=>$Colaboradoresquery[0]['ds_cel3'],
            'ic_whatsapp3'=>$Colaboradoresquery[0]['ic_whatsapp3'],
            'ds_email'=>$Colaboradoresquery[0]['ds_email'],
            'ds_rg'=>$Colaboradoresquery[0]['ds_rg'],
            'ds_cpf'=>$Colaboradoresquery[0]['ds_cpf'],
            'dt_nascimento'=>$Colaboradoresquery[0]['dt_nascimento'],
            'ds_endereco'=>$Colaboradoresquery[0]['ds_endereco'],
            'ds_numero'=>$Colaboradoresquery[0]['ds_numero'],
            'ds_complemento'=>$Colaboradoresquery[0]['ds_complemento'],
            'ds_bairro'=>$Colaboradoresquery[0]['ds_bairro'],
            'ds_cep'=>$Colaboradoresquery[0]['ds_cep'],
            'ds_cidade'=>$Colaboradoresquery[0]['ds_cidade'],
            'ds_uf'=>$Colaboradoresquery[0]['ds_uf'],
            'ic_status'=>$Colaboradoresquery[0]['ic_status'],
            'ic_funcionario'=>$Colaboradoresquery[0]['ic_funcionario'],
            'ds_regime_contratacao'=>$Colaboradoresquery[0]['ds_regime_contratacao'],
            'generos_pk'=>$Colaboradoresquery[0]['generos_pk'],
            'ds_pin'=>$Colaboradoresquery[0]['ds_pin'],
            'ds_raca'=>$Colaboradoresquery[0]['ds_raca'],
            'ds_deficiencia_fisica'=>$Colaboradoresquery[0]['ds_deficiencia_fisica'],
            'estado_civil'=>$Colaboradoresquery[0]['estado_civil'],
            'ds_nome_pai'=>$Colaboradoresquery[0]['ds_nome_pai'],
            'ds_nome_mae' => $Colaboradoresquery[0]['ds_nome_mae'],
            'ds_nome_conjuge' => $Colaboradoresquery[0]['ds_nome_conjuge'],
            'dt_nascimento_conjuge' => $Colaboradoresquery[0]['dt_nascimento_conjuge'],
            'ds_cpf_conjuge' => $Colaboradoresquery[0]['ds_cpf_conjuge'],
            'ds_tel_conjuge' => $Colaboradoresquery[0]['ds_tel_conjuge'],
            'regime_casamento' => $Colaboradoresquery[0]['regime_casamento'],
            'ds_ctps' => $Colaboradoresquery[0]['ds_ctps'],
            'ds_serie' => $Colaboradoresquery[0]['ds_serie'],
            'dt_expedicao' => $Colaboradoresquery[0]['dt_expedicao'],
            'ds_uf_rg' => $Colaboradoresquery[0]['ds_uf_rg'],
            'ds_org_exp' => $Colaboradoresquery[0]['ds_org_exp'],
            'ds_pis' => $Colaboradoresquery[0]['ds_pis'],
            'ds_titulo_eleitoral' => $Colaboradoresquery[0]['ds_titulo_eleitoral'],
            'ds_zona_eleitoral' => $Colaboradoresquery[0]['ds_zona_eleitoral'],
            'ds_secao' => $Colaboradoresquery[0]['ds_secao'],
            'ds_certificado_reservista' => $Colaboradoresquery[0]['ds_certificado_reservista'],
            'ic_filho_menor_14' => $Colaboradoresquery[0]['ic_filho_menor_14'],
            'ic_reserva' => $Colaboradoresquery[0]['ic_reserva'],
            'dt_admissao' => $Colaboradoresquery[0]['dt_admissao'],
            'dt_demissao' => $Colaboradoresquery[0]['dt_demissao'],
            'ds_re' => $Colaboradoresquery[0]['ds_re'],
            'ds_matricula' => $Colaboradoresquery[0]['ds_matricula'],
            'ds_nacionalidade' => $Colaboradoresquery[0]['ds_nacionalidade'],
            'ds_escolaridade' => $Colaboradoresquery[0]['ds_escolaridade'],
            'grau_escolaridade_pk' => $Colaboradoresquery[0]['grau_escolaridade_pk'],
            'qtde_filho' => $Colaboradoresquery[0]['qtde_filho'],
            'empresas_pk' => $Colaboradoresquery[0]['empresas_pk'],
            'regime_contratacao_pk' => $Colaboradoresquery[0]['regime_contratacao_pk'],
            'ds_carga_horaria_semanal' => $Colaboradoresquery[0]['ds_carga_horaria_semanal'],
            'tipo_conta_bancaria' => $Colaboradoresquery[0]['tipo_conta_bancaria'],
            'ds_agencia' => $Colaboradoresquery[0]['ds_agencia'],
            'ds_conta' => $Colaboradoresquery[0]['ds_conta'],
            'ds_digito' => $Colaboradoresquery[0]['ds_digito'],
            'bancos_pk' => $Colaboradoresquery[0]['bancos_pk'],
            'vl_salario' => $Colaboradoresquery[0]['vl_salario'],
            'ds_genero' => $Colaboradoresquery[0]['ds_genero'],
            'ds_banco' => $Colaboradoresquery[0]['ds_banco'],
            'ds_pix' => $Colaboradoresquery[0]['ds_pix'],
            'ds_conta_favorecido' => $Colaboradoresquery[0]['ds_conta_favorecido'],
            'ds_n_sapato' => $Colaboradoresquery[0]['ds_n_sapato'],
            'ds_estado_civil' => $Colaboradoresquery[0]['ds_estado_civil'],
            'ds_n_camisa' => $Colaboradoresquery[0]['ds_n_camisa'],
            'ds_n_calca' => $Colaboradoresquery[0]['ds_n_calca'],
            'ds_n_luva' => $Colaboradoresquery[0]['ds_n_luva'],
            'ic_dom' => $Colaboradoresquery[0]['ic_dom'],
            'ic_seg' => $Colaboradoresquery[0]['ic_seg'],
            'ic_ter' => $Colaboradoresquery[0]['ic_ter'],
            'ic_qua' => $Colaboradoresquery[0]['ic_qua'],
            'ic_qui' => $Colaboradoresquery[0]['ic_qui'],
            'ic_sex' => $Colaboradoresquery[0]['ic_sex'],
            'ic_sab' => $Colaboradoresquery[0]['ic_sab'],
            'tipo_conta_pk' => $Colaboradoresquery[0]['tipo_conta_pk'],
            'hr_inicio_expediente' => $Colaboradoresquery[0]['hr_inicio_expediente'],
            'hr_termino_expediente' => $Colaboradoresquery[0]['hr_termino_expediente'],
            'hr_saida_intervalo' => $Colaboradoresquery[0]['hr_saida_intervalo'],
            'hr_retorno_intervalo' => $Colaboradoresquery[0]['hr_retorno_intervalo'],
            'DadosColaboradorFilhos'=> $DadosColaboradorFilhos, 
            'DadosColaboradorBeneficio'=> $DadosColaboradorBeneficio, 
            'DadosColaboradorServico'=> $DadosColaboradorServico, 
        );


        $retorno->data = $result[0];
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarCursoColaboradores($colaboradores_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select cc.pk, cc.dt_cadastro, cc.usuario_cadastro_pk, cc.dt_ult_atualizacao, cc.usuario_ult_atualizacao_pk ";
        $sql.="       ,cc.colaboradores_pk ";
        $sql.="       ,cc.cursos_pk ";
        $sql.="       ,date_format(cc.dt_execucao,'%d/%m/%Y')dt_execucao";
        $sql.="       ,date_format(cc.dt_validacao,'%d/%m/%Y')dt_validacao";

        $sql.="  from colaboradores_curso cc";
        
        $sql.=" where 1=1 ";
        if($colaboradores_pk!=""){
            $sql.=" and cc.colaboradores_pk=".$colaboradores_pk;
        }
        $sql.=" order by cc.colaboradores_pk asc ";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarBeneficioColaboradores($colaboradores_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = true; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        if($colaboradores_pk!=""){
        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,vl_beneficio ";
        $sql.="       ,obs";
        $sql.="       ,ic_status";
        $sql.="       ,beneficios_pk";
        $sql.="       ,colaborador_pk";

        $sql.="  from colaboradores_beneficios ";
        $sql.=" where 1=1 ";

        $sql.=" and colaborador_pk = ".$colaboradores_pk;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
    }



        return $retorno;
    }

    public function listarNomeFilhoColaboradorPk($colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,colaborador_pk ";
        $sql.="       ,ds_nome_filho ";
        $sql.="       ,ds_cpf_filho ";
        $sql.="       ,ds_tipo_sanguineo_dependente ";
        $sql.="       ,ds_num_cartao_sus_dependente ";
        $sql.="       ,date_format(dt_nascimento_filho,'%d/%m/%Y')dt_nascimento_filho ";
        $sql.="  from colaboradores_nome_filho ";
        $sql.=" where 1=1 ";
        if($colaborador_pk != ""){
            $sql.=" and colaborador_pk = ".$colaborador_pk;
        }
        $sql.=" order by colaborador_pk asc ";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarAfastamentoColaboradores($colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = true; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        if($colaborador_pk!=""){
            $sql ="";
            $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
            $sql.="       ,tipo_apontamento ";
            $sql.="       ,date_format(dt_inicio,'%d/%m/%Y')dt_inicio";
            $sql.="       ,date_format(dt_fim,'%d/%m/%Y')dt_fim";
            $sql.="       ,ds_obs ";
            $sql.="       ,colaborador_pk ";
            $sql.="       ,leads_pk";

            $sql.="  from afastamento_ferias_colaborador ";
            $sql.=" where colaborador_pk = $colaborador_pk ";


            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            $retorno->data = $rows;
            $retorno->status = true;
            $retorno->message = 'Dados Salvos com sucesso !';
        }

        return $retorno;
    }

    public function listarDadosBancarios($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = true; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="	,c.ds_agencia";
        $sql.="	,CONCAT(c.ds_conta,'-',c.ds_digito)ds_conta";
        $sql.="	,c.ds_digito";
        $sql.="	,c.bancos_pk";
        $sql.="	,b.ds_banco";
        $sql.="	,c.ds_pix";
        $sql.="	,c.ds_conta_favorecido";

        $sql.="  from colaboradores c";
        $sql.="  left join bancos b on c.bancos_pk = b.pk";
        $sql.=" where 1=1 ";
        $sql.=" and c.pk  = ".$pk;
        $sql.=" group by c.pk ";
        $sql.=" order by c.ds_colaborador asc ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';

        return $retorno;
    }
	public function listarColaboradorFolha($empresas_pk, $leads_pk, $ic_escala){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql.=" SELECT c.pk colaborador_pk,";
        $sql.="        c.ds_colaborador,";
        $sql.="        a.leads_pk,";
        $sql.="        l.ds_lead,";
        $sql.="        prs.ds_produto_servico,";
        $sql.="        a.n_qtde_dias_semana,";
        $sql.="        a.hr_inicio_expediente,";
        $sql.="        a.hr_termino_expediente,";
        $sql.="        CASE WHEN c.dt_demissao IS NULL THEN 'Ativo' ELSE 'Desligado' END  ds_status_colaborador,";
        $sql.="        CASE WHEN a.dt_cancelamento IS NULL THEN 'Ativa' ELSE 'Cancelada' END  ds_status_escala,";
        $sql.="        date_format(a.dt_cancelamento,'%d/%m/%Y')dt_cancelamento,";
        $sql.="        date_format(a.dt_inicio_agenda,'%d/%m/%Y')dt_ini_escala,";
        $sql.="        date_format(a.dt_fim_agenda,'%d/%m/%Y')dt_fim_escala,";
        $sql.="        a.pk agenda_colaborador_padrao_pk";
        $sql.=" FROM colaboradores c";
        $sql.="      INNER JOIN agenda_colaborador_padrao a ON c.pk = a.colaboradores_pk";
        $sql.="      INNER JOIN colaboradores_produtos_servicos cps ON c.pk = cps.colaboradores_pk";
        $sql.="      INNER JOIN produtos_servicos prs ON cps.produtos_servicos_pk = prs.pk";
        $sql.="      INNER JOIN leads l ON a.leads_pk = l.pk";
        $sql.=" WHERE 1 = 1";
        if($ic_escala == 1){
            $sql.=" and a.dt_cancelamento IS NULL";
            $sql.=" and c.ic_status = 1";
        }else if($ic_escala == 2){
            $sql.=" and a.dt_cancelamento IS NOT NULL";
        }
        if(!empty($empresas_pk)){
            //$sql.=" AND ct.empresas_pk=".$empresas_pk; 
        }
        if(!empty($leads_pk)){
            $sql.=" AND a.leads_pk in (".$leads_pk.",0)"; 
        }
        
        $sql.=" GROUP BY a.pk";
        $sql.=" ORDER BY l.ds_lead,c.ds_colaborador";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarColaboradorEscala(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql.=" SELECT c.pk ,";
        $sql.="        c.ds_colaborador";
        $sql.=" FROM colaboradores c";
        $sql.="      INNER JOIN agenda_colaborador_padrao a ON c.pk = a.colaboradores_pk";
        $sql.="      INNER JOIN colaboradores_produtos_servicos cps ON c.pk = cps.colaboradores_pk";
        $sql.="      INNER JOIN produtos_servicos prs ON cps.produtos_servicos_pk = prs.pk";
        $sql.=" WHERE 1 = 1";
        
        $sql.=" GROUP BY a.pk";
        $sql.=" ORDER BY c.ds_colaborador";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }


    public function webPontoRetornaDadosColaboradorRegistroPonto($dados){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        try{
            $sql ="";
            $sql.="     SELECT c.pk colaborador_pk,";
            $sql.="        c.ds_colaborador,";
            $sql.="        c.ds_pin,";
            $sql.="        date_format(c.dt_admissao, '%d/%m/%Y' ) dt_admissao,";
            $sql.="        co.pk cliente_pk,";
            $sql.="        co.id_cliente,";
            $sql.="        co.ds_conta,";
            $sql.="        co.ds_razao_social,";
            $sql.="        co.ds_cpf_cnpj,";
            $sql.="        psa.pk solicitacao_liberacao_pk,";
            $sql.="        psa.ic_status ic_status_solicitacao_app";
            $sql.="     FROM colaboradores c ";
            $sql.="         INNER JOIN contas co ON c.empresas_pk = co.pk";
            $sql.="         LEFT JOIN ponto_solicitacao_liberacao_app psa on c.pk = psa.colaborador_pk";
            $sql.="     WHERE c.pk = ".$dados['colaborador_pk'];
            $sql.="     AND c.ic_status = 1";
            $sql.="     AND co.ic_status = 1";
            $sql.="     AND c.dt_demissao is null";


            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ic_status = "";
            $ic_status_solicitacao_app = "";
            $solicitacao_liberacao_pk = "";
            $ds_colaborador = "";
            $DadosEscalaPostoTrabalho = [];
            $ds_msg = "";
            $ds_pin = "";
            $ds_conta = "";
            $dt_admisao = "";
            if(count($query) > 0 ){
                $ic_status_solicitacao_app = $query[0]['ic_status_solicitacao_app'];
                $solicitacao_liberacao_pk  = $query[0]['solicitacao_liberacao_pk'];
                //VERIFICA SE EXITE A SOLICITAÇÃO DE LIBERAÇÃO E SE FOI LIBERADA
                if($query[0]['solicitacao_liberacao_pk']!=''){
                    if($query[0]['ic_status_solicitacao_app']==1) {
                        //Escala Postos de Trabalho
                        $sql = "";
                        $sql.= "Select";
                        $sql.="     acp.pk agenda_colaborador_padaro_pk ";
                        $sql.="     ,l.pk leads_pk ";
                        $sql.="     ,l.ds_lead ";
                        $sql.="     ,acp.produtos_servicos_pk ";
                        $sql.="     ,ps.ds_produto_servico  ";
                        $sql.=" from agenda_colaborador_padrao acp ";
                        $sql.="      inner join leads l on acp.leads_pk = l.pk";
                        $sql.="      inner join produtos_servicos ps on acp.produtos_servicos_pk = ps.pk";
                        $sql.=" where acp.colaboradores_pk =".$dados['colaborador_pk'];
                        $sql.=" and acp.dt_inicio_agenda <= sysdate() ";
                        $sql.=" and acp.dt_fim_agenda >= sysdate()";
                        $sql.=" and acp.dt_cancelamento is null";

                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute();
                        $queryPostoTrabalho = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        if($queryPostoTrabalho[0]['agenda_colaborador_padaro_pk']!=''){
                            //Dados Colaborador
                            for($i = 0;$i < count($queryPostoTrabalho); $i++){
                                $DadosEscalaPostoTrabalho[] = array(
                                    'agenda_colaborador_padaro_pk'=>$queryPostoTrabalho[$i]['agenda_colaborador_padaro_pk'],
                                    'leads_pk'=>$queryPostoTrabalho[$i]['leads_pk'],
                                    'ds_lead'=>$queryPostoTrabalho[$i]['ds_lead'],
                                    'produtos_servicos_pk'=>$queryPostoTrabalho[$i]['produtos_servicos_pk'],
                                    'ds_produto_servico'=>$queryPostoTrabalho[$i]['ds_produto_servico']
                                );
                            }


                            //Dados Colaborador
                            $ds_colaborador = $query[0]['ds_colaborador'];
                            $ds_pin = $query[0]['ds_pin'];
                            $ds_conta = $query[0]['ds_conta'];
                            $dt_admisao = $query[0]['ds_admissao'];

                            $ic_status = 1;
                            $ds_msg = 'sucesso';
                        }
                    }else{
                        $ic_status = 4;
                        $ds_msg = 'A liberação do cadastro esta pendente, entre em contato com a base!';
                    }
                }else{
                    $ic_status = 3;
                    $ds_msg = 'O Novo Registro não foi feito, retorne ao início e clique no Botão Novo Registro!';
                }
            }else{
                $ic_status = 2;
                $ds_msg = 'Pin do Colaborador não localizado, confirme e repita o processo !';
            }
            $result[] = array (
                'ic_status'=>$ic_status,
                'ic_status_solicitacao_app'=>$ic_status_solicitacao_app,
                'ds_msg'=>$ds_msg,
                'DadosEscalaPostoTrabalho'=>$DadosEscalaPostoTrabalho,
                'ds_colaborador'=>$ds_colaborador,
                'ds_pin'=>$ds_pin,
                'ds_conta'=>$ds_conta,
                'dt_admissao'=>$dt_admisao
            );

            $retorno->data = $result;
            $retorno->status = $ic_status;
            $retorno->message = $ds_msg;


            return $retorno;
        }
        catch(\Throwable $e){
            return $retorno;
        }

    }

    public function webPontoIdeintificaColaborador($dados){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        try{
            $sql ="";
            $sql.="     SELECT c.pk colaborador_pk,";
            $sql.="        c.ds_colaborador,";
            $sql.="        c.ds_rg,";
            $sql.="        c.ds_cpf,";
            $sql.="        c.ds_pin,";
            $sql.="        co.pk cliente_pk,";
            $sql.="        co.id_cliente,";
            $sql.="        co.ds_conta,";
            $sql.="        co.ds_razao_social,";
            $sql.="        co.ds_cpf_cnpj,";
            $sql.="        psa.ic_status ic_status_solicitacao_app,";
            $sql.="        psa.pk novo_cadastro_pk,";
            $sql.="        psa.ds_link_imagem_cadastro";
            $sql.="     FROM colaboradores c ";
            $sql.="         INNER JOIN contas co ON c.empresas_pk = co.pk";
            $sql.="         LEFT JOIN ponto_solicitacao_liberacao_app psa on c.pk = psa.colaborador_pk";
            $sql.="     WHERE c.pk = ".$dados['colaborador_pk'];
            $sql.="     AND c.ic_status = 1";
            $sql.="     AND co.ic_status = 1";


            $stmt = $this->pdo->prepare($sql);

            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $retorno->data = $rows;
            $retorno->status = true;
            $retorno->message = 'Dados Salvos com sucesso !';

            return $retorno;
        }
        catch(\Throwable $e){
            return $retorno;
        }


    }
    public function pegarColaboradorPorCpf($ds_cpf){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        try{
            $sql ="";
            $sql.="     SELECT * ";
            $sql.="     FROM colaboradores c ";
            $sql.="     WHERE c.ds_cpf = '".$ds_cpf."'";


            $stmt = $this->pdo->prepare($sql);

            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $retorno->data = $rows;
            $retorno->status = true;
            $retorno->message = 'Dados Salvos com sucesso !';

            return count($rows);
        }
        catch(\Throwable $e){
            return 1;
        }


    }

    public function buscarColaboradorPorCpfApp($ds_cpf){
        $retorno = new \StdClass;
        $retorno->status = false;
        $retorno->data = [];
        $retorno->message = '';

        try{
            $cpfNormalizado = preg_replace('/\D+/', '', (string)$ds_cpf);

            if ($cpfNormalizado === '') {
                $retorno->message = 'CPF do colaborador não informado.';
                return $retorno;
            }

            $sql ="";
            $sql.=" SELECT c.pk, c.ds_colaborador, c.ds_cpf";
            $sql.=" FROM colaboradores c";
            $sql.=" WHERE REPLACE(REPLACE(REPLACE(REPLACE(c.ds_cpf, '.', ''), '-', ''), '/', ''), ' ', '') = :ds_cpf";
            $sql.=" ORDER BY c.pk ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':ds_cpf', $cpfNormalizado);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (count($rows) > 0) {
                $retorno->status = true;
                $retorno->data = $rows;
                $retorno->message = 'Dados carregados com sucesso';
                return $retorno;
            }

            $retorno->message = 'Colaborador não encontrado para o CPF informado.';
            return $retorno;
        }
        catch(\Throwable $e){
            $retorno->message = $e->getMessage();
            return $retorno;
        }
    }

	public function RelatorioAcompanhamentoFerias($colaboradores_pk,$dt_inicio, $dt_fim){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select acp.pk, acp.dt_cadastro, acp.usuario_cadastro_pk, acp.dt_ult_atualizacao, acp.usuario_ult_atualizacao_pk ";
        $sql.="       ,min(date_format(acp.dt_inicio_pausa,'%d/%m/%Y'))dt_inicio_pausa ";
        $sql.="       ,max(date_format(acp.dt_fim_pausa,'%d/%m/%Y'))dt_fim_pausa ";
        $sql.="       ,c.ds_colaborador ds_colaborador";

        $sql.="  from agenda_colaborador_pausa acp";
        $sql.="      inner join colaboradores c on acp.colaboradores_pk = c.pk";
        $sql.=" where 1=1 ";
        if($dt_inicio != ""){
            $sql.=" and acp.dt_inicio_pausa >= '".Util::DataYMD($dt_inicio)."' and  acp.dt_fim_pausa <= '".Util::DataYMD($dt_fim)."'";
        }
        if($colaboradores_pk!=""){
            $sql.=" and acp.colaboradores_pk =".$colaboradores_pk;
        }
        $sql.=" and acp.ds_agenda_colaborador_pausa like '%Férias%'";
        $sql.=" group by c.pk";
       

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        $retorno->iTotalDisplayRecords = count($rows);
        $retorno->iTotalRecords = count($rows);
    
        echo json_encode($retorno);
        exit(0);
    }


    public function verificarColaboradorAtivoParaBaseWebPonto($colaborador_pk){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            $sql ="";
            $sql.="            Select";
            $sql.="                c.ds_colaborador,";
            $sql.="                c.ds_cel,";
            $sql.="                c.ic_status status_colaborador,";
            $sql.="                IF(acp.dt_cancelamento IS NULL, 1, 2) AS status_agenda,";
            $sql.="                ct.ds_dominio";
            $sql.="                from colaboradores c "; 
            $sql.="                LEFT join agenda_colaborador_padrao acp on acp.colaboradores_pk = c.pk";
            $sql.="                left join ponto_solicitacao_liberacao_app psl on psl.colaborador_pk = c.pk";
            $sql.="                left join contas ct on c.empresas_pk = ct.pk";
            $sql.="                left join leads l on acp.leads_pk = l.pk";
            $sql.="                left join produtos_servicos ps on acp.produtos_servicos_pk = ps.pk";
            $sql.="                where c.pk=".$colaborador_pk;
        /* $sql.="                 and acp.dt_inicio_agenda <= sysdate()"; 
            $sql.="                 and acp.dt_fim_agenda >= sysdate()";
            $sql.="                 and psl.ic_status = 1";
            $sql.="                 and c.ds_cel != ''";*/
            $sql.="                 order by c.ds_colaborador";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

            // Obter o nome do host (ex: www.example.com)
            $host = $_SERVER['HTTP_HOST'];
            // Montar a URL completa
            $currentUrl = $protocol . $host;
    
        
            $body = [
                'ds_colaborador'  => $rows[0]['ds_colaborador'],
                'ds_dominio' =>$currentUrl,
                'ds_telefone' =>$rows[0]['ds_cel'],
                'status_colaborador' =>$rows[0]['status_colaborador'],
                'status_agenda' =>$rows[0]['status_agenda'],
            ];

            $request = $this->client->request('POST','https://www.gpros.com.br/wb/index.php/verificarColaboradorAtivoParaBaseWebPonto', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);
            
            return true;
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        
       
    } 


    public function salvarColaboradorServidor($pk, $isNovoColaborador = false){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        // Obter o nome do host (ex: www.example.com)
        $host = $_SERVER['HTTP_HOST'];
        // Montar a URL completa
        $currentUrl = $protocol . $host;
        if ($isNovoColaborador) {
            $url = "https://webservice.gepros6.com.br/work/action.php?action=registraColaboradorApp";
            $action = "registraColaboradorApp";
        } else {
            $url = "https://webservice.gepros6.com.br/work/action.php?action=atualizarColaboradorApp";
            $action = "atualizarColaboradorApp";
        }

        $sql = "";
        $sql .= "select c.pk, c.ds_colaborador, c.ds_cel, c.ds_cpf, c.ds_rg, c.ds_email, ";
        $sql .= "       date_format(c.dt_admissao, '%d/%m/%Y') dt_admissao, ";
        $sql .= "       date_format(c.dt_nascimento, '%d/%m/%Y') dt_nascimento, ";
        $sql .= "       c.ic_status, c.empresas_pk ";
        $sql .= "  from colaboradores c ";
        $sql .= " where c.pk = ".$pk;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($rows) === 0) {
            return false;
        }

        $colaborador = $rows[0];
        $conta = (new Conta($this->pdo))->listarPorPk($colaborador['empresas_pk']);
        $dsCnpjConta = "";
        if (isset($conta->data[0]['ds_cpf_cnpj'])) {
            $dsCnpjConta = $conta->data[0]['ds_cpf_cnpj'];
        }

        $body = [
            'action'  => $action,
            'ds_colaborador'  => str_replace("'", " ", $colaborador['ds_colaborador']),
            'ds_cel'  => $colaborador['ds_cel'],
            'ds_cpf'  => $colaborador['ds_cpf'],
            'ds_rg'  => $colaborador['ds_rg'],
            'ds_email'  => $colaborador['ds_email'],
            'dt_contratacao' => $colaborador['dt_admissao'] ? Util::DataYMD($colaborador['dt_admissao']) : "",
            'ic_status'  => $colaborador['ic_status'],
            'contas_pk'  => $colaborador['empresas_pk'],
            'dt_nascimento'  => $colaborador['dt_nascimento'] ? Util::DataYMD($colaborador['dt_nascimento']) : "",
            'colaborador_pk'  => $pk,
            'ds_cnpj_conta'  => $dsCnpjConta,
            'ds_link' =>$currentUrl,
        ];

        $request = $this->client->request('POST',$url, [
            'json'=>$body
        ]);

        return $request->getStatusCode() >= 200 && $request->getStatusCode() < 300;
    }
    public function excluirColaboradorServidor($pk){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        // Obter o nome do host (ex: www.example.com)
        $host = $_SERVER['HTTP_HOST'];
        // Montar a URL completa
        $currentUrl = $protocol . $host;
        $url = "https://webservice.gepros6.com.br/work/action.php?action=excluirColaboradorApp";
        $action = "excluirColaboradorApp";

    
        $body = [
            'action'  => $action,
            'colaborador_pk'  => $pk,
            'ds_link' =>$currentUrl,
        ];

        $request = $this->client->request('POST',$url, [
            /*'headers' => [
                'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
            ],*/
            'json'=>$body
        ]);
    }

    public function relatorioStatusColaborador($ic_status_app,$leads_pk,$colaborador_pk,$ic_status){
        $retorno = new \StdClass;
        $retorno->status = false;
        $retorno->data = [];
        $cpfFiltrados = [];

        // Se o filtro ic_status_app vier preenchido, busca na API
        if (!empty($ic_status_app) && $ic_status_app!=3) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $currentUrl = $protocol . $host;

            $body = [
                'ds_link' => $currentUrl,
                'ic_status' => intval($ic_status_app)
            ];

            try {
                $request = $this->client->request('POST', 'https://webservice.gepros6.com.br/work/action.php?action=consultaAcessoApp', [
                    'json' => $body
                ]);

                if ($request->getStatusCode() === 200) {
                    $response = $request->getBody()->getContents();
                    $data = json_decode($response);
                    $registros = $data->data->registros ?? [];

                    foreach ($registros as $registro) {
                        if (!empty($registro->ds_cpf)) {
                            $cpfFiltrados[] = "'" . $registro->ds_cpf . "'";
                        }
                    }
                    
                    // Se não veio nenhum CPF, retorna vazio direto
                    if (empty($cpfFiltrados)) {
                        $retorno->status = true;
                        $retorno->message = 'Nenhum resultado encontrado com este status.';
                        $retorno->data = [];
                        $retorno->iTotalDisplayRecords = 0;
                        $retorno->iTotalRecords = 0;
                        echo json_encode($retorno);
                        exit(0);
                    }
                }
            } catch (\Exception $e) {
                $retorno->status = false;
                $retorno->message = 'Erro ao consultar API';
                echo json_encode($retorno);
                exit(0);
            }
        }
        
        $sql = "SELECT 
                    c.pk,
                    c.ds_colaborador,
                    c.ds_re,
                    c.ic_status,
                    case c.ic_status when 1 then 'Ativo' when 2 then 'Demitido' when 3 then 'Afastado' when 4 then 'Férias' end ds_status_colaborador ,
                    date_format(c.dt_admissao,'%d/%m/%Y')dt_admissao,
                    c.ds_cpf,
                    l.ds_lead
                FROM colaboradores c
                LEFT JOIN agenda_colaborador_padrao a 
                    ON a.colaboradores_pk = c.pk
                    AND a.pk = (
                        SELECT ap.pk
                        FROM agenda_colaborador_padrao ap
                        WHERE ap.colaboradores_pk = c.pk
                        ORDER BY (ap.dt_cancelamento IS NOT NULL), ap.dt_cadastro DESC
                        LIMIT 1
                    ) 
                LEFT JOIN leads l ON l.pk = a.leads_pk
                WHERE 1=1 ";

        // Aplica filtro por CPF retornado da API se ic_status_app foi usado
        if (!empty($ic_status_app) && !empty($cpfFiltrados)) {
            $sql .= " AND c.ds_cpf IN (" . implode(',', $cpfFiltrados) . ")";
        }
        if (!empty($colaborador_pk) && !empty($colaborador_pk)) {
            $sql .= " AND c.pk =".$colaborador_pk;
        }
        if (!empty($leads_pk) && !empty($leads_pk)) {
            $sql .= " AND l.pk =".$leads_pk;
        }
        if (!empty($ic_status)) {
            $sql .= " AND  c.ic_status =".$ic_status;
        }

        // Filtros opcionais
        if (!empty($colaborador_pk)) $sql .= " AND c.pk = " . intval($colaborador_pk);
        if($ic_status_app==3){
            $sql .= " AND c.ic_status = 2" ;
        }

        $sql .= " GROUP BY c.pk ORDER BY c.ds_colaborador ASC";
      

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Se não filtrou por ic_status_app, faz enriquecimento
        if (empty($ic_status_app)) {
            foreach ($rows as &$row) {
                $ds_cpf_api = $row['ds_cpf'];

                $body = [
                    'ds_link' => $currentUrl,
                    'ds_cpf' => $ds_cpf_api,
                    'ic_status' => ""
                ];

                try {
                    $request = $this->client->request('POST', 'https://webservice.gepros6.com.br/work/action.php?action=consultaAcessoApp', [
                        'json' => $body
                    ]);

                    if ($request->getStatusCode() === 200) {
                        $response = $request->getBody()->getContents();
                        $data = json_decode($response);
                        $queryApi = $data->data->registros;
                        $row['ds_status_app'] = isset($queryApi[0]->ic_status) ? $queryApi[0]->ic_status : "Não fez o novo cadastro";
                        $row['arrArray'] = $queryApi[0];
                    } else {
                        $row['ds_status_app'] = null;
                    }
                } catch (\Exception $e) {
                    $row['ds_status_app'] = null;
                }
            }
        } else {
            // Quando a API já retornou os dados, podemos enriquecer diretamente com o status recebido
            foreach ($rows as &$row) {
                foreach ($registros as $registro) {
                    if ($registro->ds_cpf == $row['ds_cpf']) {
                        $row['ds_status_app'] = $registro->ic_status ?? "Desconhecido";
                        break;
                    }
                }
            }
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return ($rows);
    }


    


}
