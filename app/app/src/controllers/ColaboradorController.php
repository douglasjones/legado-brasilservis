<?php

namespace App\Controller;

use App\Model\AgendaColaboradorPadrao;
use App\Model\Colaborador;
use App\Model\Compra;
use App\Model\Conta;
use App\Model\Documento;
use App\Model\Log;
use App\Model\ProdutoServico;
use App\Utils\Json;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ColaboradorController extends BaseController {
    public function listarColaboradorPkPrint(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
          
            $pk = isset($data['pk']) ? $data['pk'] : "";
            
            $retorno = (new Colaborador($this->pdo))->listarColaboradorPkPrint($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function salvar(Request $request, Response $response, $args)
    {
        try {
            $data = $_POST;
          
            $produtos_servicos_colaboradores = isset($data['produtos_servicos_colaboradores']) ? $data['produtos_servicos_colaboradores'] : "";
            if ($produtos_servicos_colaboradores != "")
                $arrProdutosServicosColaboradores = json_decode($produtos_servicos_colaboradores, true);

            $materiais_lead = isset($data['materiais_lead']) ? $data['materiais_lead'] : "";
            if ($materiais_lead != "")
                $arrMateriaisLead = json_decode($materiais_lead, true);

            $colaborador_escala = isset($data['colaborador_escala']) ? $data['colaborador_escala'] : "";
            if ($colaborador_escala != "")
                $arrColaboradorEscala = json_decode($colaborador_escala, true);

            $colaborador_beneficios = isset($data['colaborador_beneficios']) ? $data['colaborador_beneficios'] : "";
            if ($colaborador_beneficios != "")
                $arrColaboradorBeneficio = json_decode($colaborador_beneficios, true);

            $colaboradores_curso = isset($data['colaboradores_curso']) ? $data['colaboradores_curso'] : "";
            if ($colaboradores_curso != "")
                $arrColaboradorCurso = json_decode($colaboradores_curso, true);

            $colaborador_nome_filho = isset($data['colaborador_nome_filho']) ? $data['colaborador_nome_filho'] : "";
            if ($colaborador_nome_filho != "")
                $arrColaboradorNomeFilho = json_decode($colaborador_nome_filho, true);

            $colaborador_afastamento = isset($data['colaborador_afastamento']) ? $data['colaborador_afastamento'] : "";
            $arrColaboradorAfastamento=[];
            if ($colaborador_afastamento != "")
                $arrColaboradorAfastamento = json_decode($colaborador_afastamento, true);

            $documentos_pk = isset($data['documentos_pk']) ? $data['documentos_pk'] : "";
            if ($documentos_pk != "")
                $arrDocs = json_decode($documentos_pk, true);

                  
            /**********************/
            $pk = isset($data['pk']) ? $data['pk'] : "";
            $ds_colaborador = isset($data['ds_colaborador']) ? $data['ds_colaborador'] : "";
            $ds_cel = isset($data['ds_cel']) ? $data['ds_cel'] : "";
            $ic_whatsapp = isset($data['ic_whatsapp']) ? $data['ic_whatsapp'] : "";
            $ds_cel2 = isset($data['ds_cel2']) ? $data['ds_cel2'] : "";
            $ic_whatsapp2 = isset($data['ic_whatsapp2']) ? $data['ic_whatsapp2'] : "";
            $ds_cel3 = isset($data['ds_cel3']) ? $data['ds_cel3'] : "";
            $ic_whatsapp3 = isset($data['ic_whatsapp3']) ? $data['ic_whatsapp3'] : "";
            $ds_email = isset($data['ds_email']) ? $data['ds_email'] : "";
            $ds_rg = isset($data['ds_rg']) ? $data['ds_rg'] : "";
            $ds_cpf = isset($data['ds_cpf']) ? $data['ds_cpf'] : "";
            $dt_nascimento = isset($data['dt_nascimento']) ? ($data['dt_nascimento']) : "";
            $ds_endereco = isset($data['ds_endereco']) ? $data['ds_endereco'] : "";
            $ds_numero = isset($data['ds_numero']) ? $data['ds_numero'] : "";
            $ds_complemento = isset($data['ds_complemento']) ? $data['ds_complemento'] : "";
            $ds_bairro = isset($data['ds_bairro']) ? $data['ds_bairro'] : "";
            $ds_cep = isset($data['ds_cep']) ? $data['ds_cep'] : "";
            $ds_cidade = isset($data['ds_cidade']) ? $data['ds_cidade'] : "";
            $ds_uf = isset($data['ds_uf']) ? $data['ds_uf'] : "";
            $ic_status = isset($data['ic_status']) ? $data['ic_status'] : "";

            $ic_origem = isset($data['ic_origem']) ? $data['ic_origem'] : "";
            $ic_funcionario = isset($data['ic_funcionario']) ? $data['ic_funcionario'] : "";
            $generos_pk = isset($data['generos_pk']) ? $data['generos_pk'] : "";
            $ds_re = isset($data['ds_re']) ? $data['ds_re'] : "";
            $ds_raca = isset($data['ds_raca']) ? $data['ds_raca'] : "";
            $ds_deficiencia_fisica = isset($data['ds_deficiencia_fisica']) ? $data['ds_deficiencia_fisica'] : "";
            $estado_civil = isset($data['estado_civil']) ? $data['estado_civil'] : "";
            $ds_nome_pai = isset($data['ds_nome_pai']) ? $data['ds_nome_pai'] : "";
            $ds_nome_mae = isset($data['ds_nome_mae']) ? $data['ds_nome_mae'] : "";
            $ds_nome_conjuge = isset($data['ds_nome_conjuge']) ? $data['ds_nome_conjuge'] : "";
            $dt_nascimento_conjuge = isset($data['dt_nascimento_conjuge']) ? $data['dt_nascimento_conjuge'] : "";
            $ds_cpf_conjuge = isset($data['ds_cpf_conjuge']) ? $data['ds_cpf_conjuge'] : "";
            $ds_tel_conjuge = isset($data['ds_tel_conjuge']) ? $data['ds_tel_conjuge'] : "";
            $regime_casamento = isset($data['regime_casamento']) ? $data['regime_casamento'] : "";
            $ds_ctps = isset($data['ds_ctps']) ? $data['ds_ctps'] : "";
            $ds_serie = isset($data['ds_serie']) ? $data['ds_serie'] : "";
            $dt_expedicao = isset($data['dt_expedicao']) ? $data['dt_expedicao'] : "";
            $ds_uf_rg = isset($data['ds_uf_rg']) ? $data['ds_uf_rg'] : "";
            $ds_org_exp = isset($data['ds_org_exp']) ? $data['ds_org_exp'] : "";
            $ds_pis = isset($data['ds_pis']) ? $data['ds_pis'] : "";
            $ds_titulo_eleitoral = isset($data['ds_titulo_eleitoral']) ? $data['ds_titulo_eleitoral'] : "";
            $ds_zona_eleitoral = isset($data['ds_zona_eleitoral']) ? $data['ds_zona_eleitoral'] : "";
            $ds_secao = isset($data['ds_secao']) ? $data['ds_secao'] : "";
            $ds_certificado_reservista = isset($data['ds_certificado_reservista']) ? $data['ds_certificado_reservista'] : "";
            $ic_filho_menor_14 = isset($data['ic_filho_menor_14']) ? $data['ic_filho_menor_14'] : "";
            $ds_nacionalidade = isset($data['ds_nacionalidade']) ? $data['ds_nacionalidade'] : "";
            $ds_matricula = isset($data['ds_matricula']) ? $data['ds_matricula'] : "";
            $grau_escolaridade_pk = isset($data['grau_escolaridade_pk']) ? $data['grau_escolaridade_pk'] : "";
            $ic_reserva = isset($data['ic_reserva']) ? $data['ic_reserva'] : "";
            $dt_demissao = isset($data['dt_demissao']) ? $data['dt_demissao'] : "";
            $dt_admissao = isset($data['dt_admissao']) ? $data['dt_admissao'] : "";
            $qtde_filho = isset($data['qtde_filho']) ? $data['qtde_filho'] : "";
            $empresas_pk = isset($data['empresas_pk']) ? $data['empresas_pk'] : "";
            $regime_contratacao_pk = isset($data['regime_contratacao_pk']) ? $data['regime_contratacao_pk'] : "";
            $ds_carga_horaria_semanal = isset($data['ds_carga_horaria_semanal']) ? $data['ds_carga_horaria_semanal'] : "";
            $tipo_conta_bancaria = isset($data['tipo_conta_bancaria']) ? $data['tipo_conta_bancaria'] : "";
            $ds_agencia = isset($data['ds_agencia']) ? $data['ds_agencia'] : "";
            
            $ds_n_sapato = isset($data['ds_n_sapato']) ? $data['ds_n_sapato'] : "";
            $ds_n_camisa = isset($data['ds_n_camisa']) ? $data['ds_n_camisa'] : "";
            $ds_n_calca = isset($data['ds_n_calca']) ? $data['ds_n_calca'] : "";
            $ds_n_luva = isset($data['ds_n_luva']) ? $data['ds_n_luva'] : "";

            $ds_conta = isset($data['ds_conta']) ? $data['ds_conta'] : "";
            $ds_digito = isset($data['ds_digito']) ? $data['ds_digito'] : "";
            $bancos_pk = isset($data['bancos_pk']) ? $data['bancos_pk'] : "";
            $vl_salario = isset($data['vl_salario']) ? $data['vl_salario'] : "";
            $ds_pix = isset($data['ds_pix']) ? $data['ds_pix'] : "";
            $ds_conta_favorecido = isset($data['ds_conta_favorecido']) ? $data['ds_conta_favorecido'] : "";

            $ic_tipo_sanguineo = isset($data['ic_tipo_sanguineo']) ? $data['ic_tipo_sanguineo'] : "";
            $ds_cartao_sus = isset($data['ds_cartao_sus']) ? $data['ds_cartao_sus'] : "";
            $ic_tipo_sanguineo_conjuge = isset($data['ic_tipo_sanguineo_conjuge']) ? $data['ic_tipo_sanguineo_conjuge'] : "";
            $ic_ds_cartao_sus_conjuge = isset($data['ic_ds_cartao_sus_conjuge']) ? $data['ic_ds_cartao_sus_conjuge'] : "";
            $ic_experiencia = isset($data['ic_experiencia']) ? $data['ic_experiencia'] : "";
            $ds_senha_portal = isset($data['ds_senha_portal']) ? $data['ds_senha_portal'] : "";
            /**************VERIFICA SE EXISTE UM CPF CADASTRADO*********************/
           /* if($pk==""){
                $countCpf = (new Colaborador($this->pdo))->pegarColaboradorPorCpf($ds_cpf);
                
                if($countCpf>=1){
                    Json::run("warning", [], "Esse CPF já está cadastrado em outro colaborador ! ");
                    die();
                }
            }     */ 
                $colaborador = [
                    "pk" => $pk,
                    "ds_colaborador" => $ds_colaborador,
                    "ds_cel" => $ds_cel, 
                    "ic_whatsapp" => $ic_whatsapp, 
                    "ds_cel2" => $ds_cel2,
                    "ic_whatsapp2" => $ic_whatsapp2,
                    "ds_cel3" => $ds_cel3,
                    "ic_whatsapp3" => $ic_whatsapp3,
                    "ds_email" => $ds_email,
                    "ds_rg" => $ds_rg,
                    "ds_cpf" => $ds_cpf,
                    "dt_nascimento" => $dt_nascimento,
                    "ds_endereco" => $ds_endereco,
                    "ds_numero" => $ds_numero,
                    "ds_complemento" => $ds_complemento,
                    "ds_bairro" => $ds_bairro,
                    "ds_cep" => $ds_cep,
                    "ds_cidade" => $ds_cidade,
                    "ds_uf" => $ds_uf,
                    "ic_status" => $ic_status,
                    "ic_origem" => $ic_origem,
                    "ic_funcionario" => $ic_funcionario,
                    "generos_pk" => $generos_pk,
                    "ds_re" => $ds_re,
                    "ds_raca" => $ds_raca,
                    "ds_deficiencia_fisica" => $ds_deficiencia_fisica,
                    "estado_civil" => $estado_civil,
                    "ds_nome_pai" => $ds_nome_pai,
                    "ds_nome_mae" => $ds_nome_mae,
                    "ds_nome_conjuge" => $ds_nome_conjuge,
                    "dt_nascimento_conjuge" => $dt_nascimento_conjuge,
                    "ds_cpf_conjuge" => $ds_cpf_conjuge,
                    "ds_tel_conjuge" => $ds_tel_conjuge,
                    "regime_casamento" => $regime_casamento,
                    "ds_ctps" => $ds_ctps,
                    "ds_serie" => $ds_serie,
                    "dt_expedicao" => $dt_expedicao,
                    "ds_uf_rg" => $ds_uf_rg,
                    "ds_org_exp" => $ds_org_exp,
                    "ds_pis" => $ds_pis,
                    "ds_titulo_eleitoral" => $ds_titulo_eleitoral,
                    "ds_zona_eleitoral" => $ds_zona_eleitoral,
                    "ds_secao" => $ds_secao,
                    "ds_certificado_reservista" => $ds_certificado_reservista,
                    "ic_filho_menor_14" => $ic_filho_menor_14,
                    "ds_nacionalidade" => $ds_nacionalidade,
                    "ds_matricula" => $ds_matricula,
                    "grau_escolaridade_pk" => $grau_escolaridade_pk,
                    "ic_reserva" => $ic_reserva,
                    "dt_demissao" => $dt_demissao,
                    "dt_admissao" => $dt_admissao,
                    "qtde_filho" => $qtde_filho,
                    "empresas_pk" => $empresas_pk,
                    "regime_contratacao_pk" => $regime_contratacao_pk,
                    "ds_carga_horaria_semanal" => $ds_carga_horaria_semanal,
                    "tipo_conta_bancaria" => $tipo_conta_bancaria,
                    "ds_agencia" => $ds_agencia,
                    "ds_conta" => $ds_conta,
                    "ds_digito" => $ds_digito,
                    "bancos_pk" => $bancos_pk,
                    "vl_salario" => $vl_salario,
                    "ds_pix" => $ds_pix,
                    "ds_conta_favorecido" => $ds_conta_favorecido,
                    "ds_n_sapato" => $ds_n_sapato,
                    "ds_n_camisa" => $ds_n_camisa,
                    "ds_n_calca" => $ds_n_calca,
                    "ds_n_luva" => $ds_n_luva,
                    "ic_tipo_sanguineo" => $ic_tipo_sanguineo,
                    "ds_cartao_sus" => $ds_cartao_sus,
                    "ic_tipo_sanguineo_conjuge" => $ic_tipo_sanguineo_conjuge,
                    "ic_ds_cartao_sus_conjuge" => $ic_ds_cartao_sus_conjuge,
                    "ds_senha_portal" => $ds_senha_portal,
                    "ic_experiencia" => $ic_experiencia
                ];
               
                $retorno = (new Colaborador($this->pdo))->salvar($colaborador);
                //(new Colaborador($this->pdo))->verificarColaboradorAtivoParaBaseWebPonto($retorno->data);
    
                //SALVAR PIN
                $id_cliente_conta = (new Conta($this->pdo))->listarTodos();
                (new Colaborador($this->pdo))->salvarDSPin($id_cliente_conta->data[0]['id_cliente'] . "-" . $retorno->data, $retorno->data);
    
                if(count($arrDocs) > 0){
                    
                    for($i = 0; $i < count($arrDocs); $i++){
                        (new Documento($this->pdo))->updateDocColaboradores($retorno->data,$arrDocs[$i]['pk']);
                    }
                }
                
                (new ProdutoServico($this->pdo))->excluirProdutosServicosColaboradoresPk($retorno->data);
                
                if(count($arrProdutosServicosColaboradores) > 0){
                    for($i = 0; $i < count($arrProdutosServicosColaboradores); $i++){
                        (new ProdutoServico($this->pdo))->adicionarProdutosServicosColaboradores($retorno->data, $arrProdutosServicosColaboradores[$i]['produtos_servicos_pk'], $arrProdutosServicosColaboradores[$i]["ic_possui_treinamento"],$arrProdutosServicosColaboradores[$i]["ic_possui_certificado"]);
                    }
                }
                
               
                //Nome Filho
                if(count($arrColaboradorNomeFilho) > 0){
    
                    (new Colaborador($this->pdo))->excluirFilhoColaborador($retorno->data);
                    for($i = 0; $i < count($arrColaboradorNomeFilho); $i++){
    
    
                        $colaborador_filho = [
                            "pk"=>"",
                            "colaborador_pk"=>$retorno->data,
                            "ds_nome_filho"=>$arrColaboradorNomeFilho[$i]['ds_nome_filho'],
                            "ds_cpf_filho"=>$arrColaboradorNomeFilho[$i]['ds_cpf_filho'],
                            "dt_nascimento_filho"=>$arrColaboradorNomeFilho[$i]['dt_nascimento_filho'],
                            "ds_tipo_sanguineo_dependente"=>$arrColaboradorNomeFilho[$i]['ds_tipo_sanguineo_dependente'],
                            "ds_num_cartao_sus_dependente"=>$arrColaboradorNomeFilho[$i]['ds_num_cartao_sus_dependente']
                        ];
    
    
                        (new Colaborador($this->pdo))->salvarFilhoColaborador($colaborador_filho);
    
                    }
                }
    
                //BENEFICIOS
                if(count($arrColaboradorBeneficio) > 0){
                    (new Colaborador($this->pdo))->excluirColaboradorBeneficio($retorno->data);
                    for($i = 0; $i < count($arrColaboradorBeneficio); $i++){
    
                        $colaborador_beneficio = [
                            "pk"=>"",
                            "colaborador_pk"=>$retorno->data,
                            "vl_beneficio"=>$arrColaboradorBeneficio[$i]['vl_beneficio'],
                            "obs"=>$arrColaboradorBeneficio[$i]['obs'],
                            "ic_status"=>"",
                            "beneficios_pk"=>$arrColaboradorBeneficio[$i]['beneficios_pk']
                        ];
    
                        (new Colaborador($this->pdo))->salvarBeneficio($colaborador_beneficio);
                    }
                }
    
                
                //CURSO
                if(count($arrColaboradorCurso) > 0){
                    (new Colaborador($this->pdo))->excluirColaboradorCurso($retorno->data);
                    for($i = 0; $i < count($arrColaboradorCurso); $i++){
    
                        $colaborador_curso = [
                            "pk"=>"",
                            "colaboradores_pk"=>$retorno->data,
                            "cursos_pk"=>$arrColaboradorCurso[$i]['cursos_pk'],
                            "dt_execucao"=> $arrColaboradorCurso[$i]['dt_execucao'],
                            "dt_validacao"=>$arrColaboradorCurso[$i]['dt_validacao']
                        ];
    
                        (new Colaborador($this->pdo))->salvarCurso($colaborador_curso);
                    }
                }
    
                if($pk==""){
                    
                    //ESCALA
                    if(count($arrColaboradorEscala) > 0){
                        for($i = 0; $i < count($arrColaboradorEscala); $i++){
    
                            $agenda_colaborador_padrao = [
                                "pk"=>"",
                                "leads_pk"=>$arrColaboradorEscala[$i]['leads_pk'],
                                "contratos_pk"=>$arrColaboradorEscala[$i]['contratos_pk'],
                                "dt_inicio_agenda"=>Util::DataYMD($arrColaboradorEscala[$i]['dt_inicio_agenda']),
                                "dt_fim_agenda"=>Util::DataYMD($arrColaboradorEscala[$i]['dt_fim_agenda']),
                                "produtos_servicos_pk"=>$arrColaboradorEscala[$i]['produtos_servicos_pk'],
                                "colaboradores_pk"=>$retorno->data,
                                "processos_etapas_pk"=>$arrColaboradorEscala[$i]['processos_etapas_pk'],
                                "contratos_itens_pk"=>$arrColaboradorEscala[$i]['contratos_itens_pk'],
                                "turnos_pk"=>$arrColaboradorEscala[$i]['turnos_pk'],
                                "hr_inicio_expediente"=>$arrColaboradorEscala[$i]['hr_inicio_expediente'],
                                "hr_termino_expediente"=>$arrColaboradorEscala[$i]['hr_termino_expediente'],
                                "hr_saida_intervalo"=>$arrColaboradorEscala[$i]['hr_saida_intervalo'],
                                "hr_retorno_intervalo"=>$arrColaboradorEscala[$i]['hr_retorno_intervalo'],
                                "ic_folga_inverter"=>$arrColaboradorEscala[$i]['ic_folga_inverter'],
                                "tipo_escala"=>$arrColaboradorEscala[$i]['tipo_escala'],
                                "ic_intrajornada"=>$arrColaboradorEscala[$i]['ic_intrajornada'],
                                "ic_dom"=>$arrColaboradorEscala[$i]['ic_dom'],
                                "ic_seg" => $arrColaboradorEscala[$i]['ic_seg'],
                                "ic_ter"=>$arrColaboradorEscala[$i]['ic_ter'],
                                "ic_qua"=>$arrColaboradorEscala[$i]['ic_qua'],
                                "ic_qui"=>$arrColaboradorEscala[$i]['ic_qui'],
                                "ic_sex"=>$arrColaboradorEscala[$i]['ic_sex'],
                                "ic_sab"=>$arrColaboradorEscala[$i]['ic_sab'],
                                "ic_dom_folga"=>$arrColaboradorEscala[$i]['ic_dom_folga'],
                                "ic_seg_folga"=>$arrColaboradorEscala[$i]['ic_seg_folga'],
                                "ic_ter_folga"=>$arrColaboradorEscala[$i]['ic_ter_folga'],
                                "ic_qua_folga"=>$arrColaboradorEscala[$i]['ic_qua_folga'],
                                "ic_qui_folga"=>$arrColaboradorEscala[$i]['ic_qui_folga'],
                                "ic_sex_folga"=>$arrColaboradorEscala[$i]['ic_sex_folga'],
                                "ic_sab_folga"=>$arrColaboradorEscala[$i]['ic_sab_folga'],
                                "dom_turnos_pk"=>$arrColaboradorEscala[$i]['dom_turnos_pk'],
                                "seg_turnos_pk"=>$arrColaboradorEscala[$i]['seg_turnos_pk'],
                                "ter_turnos_pk"=>$arrColaboradorEscala[$i]['ter_turnos_pk'],
                                "qua_turnos_pk"=>$arrColaboradorEscala[$i]['qua_turnos_pk'],
                                "qui_turnos_pk"=>$arrColaboradorEscala[$i]['qui_turnos_pk'],
                                "sex_turnos_pk"=>$arrColaboradorEscala[$i]['sex_turnos_pk'],
                                "sab_turnos_pk"=>$arrColaboradorEscala[$i]['sab_turnos_pk'],
                                "hr_turno_dom"=>$arrColaboradorEscala[$i]['hr_turno_dom'],
                                "hr_turno_seg"=>$arrColaboradorEscala[$i]['hr_turno_seg'],
                                "hr_turno_ter"=>$arrColaboradorEscala[$i]['hr_turno_ter'],
                                "hr_turno_qua"=>$arrColaboradorEscala[$i]['hr_turno_qua'],
                                "hr_turno_qui"=>$arrColaboradorEscala[$i]['hr_turno_qui'],
                                "hr_turno_sex"=>$arrColaboradorEscala[$i]['hr_turno_sex'],
                                "hr_turno_sab"=>$arrColaboradorEscala[$i]['hr_turno_sab'],
                                "hr_turno_dom_saida"=>$arrColaboradorEscala[$i]['hr_turno_dom_saida'],
                                "hr_turno_seg_saida"=>$arrColaboradorEscala[$i]['hr_turno_seg_saida'],
                                "hr_turno_ter_saida"=>$arrColaboradorEscala[$i]['hr_turno_ter_saida'],
                                "hr_turno_qua_saida"=>$arrColaboradorEscala[$i]['hr_turno_qua_saida'],
                                "hr_turno_qui_saida"=>$arrColaboradorEscala[$i]['hr_turno_qui_saida'],
                                "hr_turno_sex_saida"=>$arrColaboradorEscala[$i]['hr_turno_sex_saida'],
                                "hr_turno_sab_saida"=>$arrColaboradorEscala[$i]['hr_turno_sab_saida'],
                                "hr_intervalo_dom"=>$arrColaboradorEscala[$i]['hr_intervalo_dom'],
                                "hr_intervalo_seg"=>$arrColaboradorEscala[$i]['hr_intervalo_seg'],
                                "hr_intervalo_ter"=>$arrColaboradorEscala[$i]['hr_intervalo_ter'],
                                "hr_intervalo_qua"=>$arrColaboradorEscala[$i]['hr_intervalo_qua'],
                                "hr_intervalo_qui"=>$arrColaboradorEscala[$i]['hr_intervalo_qui'],
                                "hr_intervalo_sex"=>$arrColaboradorEscala[$i]['hr_intervalo_sex'],
                                "hr_intervalo_sab"=>$arrColaboradorEscala[$i]['hr_intervalo_sab'],
                                "hr_intervalo_saida_dom"=>$arrColaboradorEscala[$i]['hr_intervalo_saida_dom'],
                                "hr_intervalo_saida_seg"=>$arrColaboradorEscala[$i]['hr_intervalo_saida_seg'],
                                "hr_intervalo_saida_ter"=>$arrColaboradorEscala[$i]['hr_intervalo_saida_ter'],
                                "hr_intervalo_saida_qua"=>$arrColaboradorEscala[$i]['hr_intervalo_saida_qua'],
                                "hr_intervalo_saida_qui"=>$arrColaboradorEscala[$i]['hr_intervalo_saida_qui'],
                                "hr_intervalo_saida_sex"=>$arrColaboradorEscala[$i]['hr_intervalo_saida_sex'],
                                "hr_intervalo_saida_sab"=>$arrColaboradorEscala[$i]['hr_intervalo_saida_sab'],
                                "dt_cancelamento"=>$arrColaboradorEscala[$i]['dt_cancelamento'],
                                "ds_motivo_cancelamento"=>$arrColaboradorEscala[$i]['ds_motivo_cancelamento'],
                                "n_qtde_dias_semana"=>$arrColaboradorEscala[$i]['dias_escala_servico'],
                                "dias_escala_servico"=>$arrColaboradorEscala[$i]['dias_escala_servico'],
                                "ic_preenchimento_automatico"=>$arrColaboradorEscala[$i]['ic_preenchimento_automatico'],
                                "ic_nao_repetir"=>$arrColaboradorEscala[$i]['ic_nao_repetir'],
                                "ic_ponto_fora_horario"=>$arrColaboradorEscala[$i]['ic_ponto_fora_horario'],
                                "ic_tempo_antes_ponto"=>$arrColaboradorEscala[$i]['ic_tempo_antes_ponto'],
                            ];
    
    
                            $retornoAgenda = (new AgendaColaboradorPadrao($this->pdo))->salvar($agenda_colaborador_padrao);
                            
                            $retornoEscala = (new AgendaColaboradorPadrao($this->pdo))->escalaDadosColaborador(
                                $retorno->data, 
                                ($arrColaboradorEscala[$i]['dt_inicio_agenda']), 
                                ($arrColaboradorEscala[$i]['dt_fim_agenda']), 
                                $arrColaboradorEscala[$i]['dias_escala_servico'], 
                                $arrColaboradorEscala[$i]['leads_pk'], 
                                $retornoAgenda->data, 
                                $arrColaboradorEscala[$i]['tipo_escala'],
                                0,
                                $arrColaboradorEscala[$i]['dias_escala_servico']
                            );
    
                        }
                    }
                }
    
                //AFASTAMENTO
                if(count($arrColaboradorAfastamento) > 0){
                    (new Colaborador($this->pdo))->excluirAfastamentoColaborador($retorno->data);
                    for($i = 0; $i < count($arrColaboradorAfastamento); $i++){
                        $dt_fim = "";
                        if($arrColaboradorAfastamento[$i]['dt_fim']!=""){
                            $dt_fim = (Util::DataYMD($arrColaboradorAfastamento[$i]['dt_fim']));
                        }
                        $afastamento_ferias_colaborador = [
                            "tipo_apontamento"=>$arrColaboradorAfastamento[$i]['tipo_apontamento'],
                            "dt_inicio"=>Util::DataYMD($arrColaboradorAfastamento[$i]['dt_inicio']),
                            "dt_fim"=>$dt_fim,
                            "ds_obs"=>$arrColaboradorAfastamento[$i]['obs'],
                            "colaborador_pk"=>$retorno->data
                        ];
    
    
                        (new Colaborador($this->pdo))->salvarAfastamento($afastamento_ferias_colaborador);
                    }
                }

                
    
                //Materiais
               /* if(count($arrMateriaisLead) > 0){
    
                    for($i = 0; $i < count($arrMateriaisLead); $i++){
    
                        $movimentacao_estoque = $movimentacao_estoquedao->carregarPorPk($arrMateriaisLead[$i]['movimentacao_estoque_pk']);
    
                        $movimentacao_estoque->setcolaborador_pk($pk);
    
                        $movimentacao_estoque->setprodutos_itens_pk($arrMateriaisLead[$i]['produtos_itens_pk']);
                        $movimentacao_estoque->setqtde("1");
                        $movimentacao_estoque->setdt_entrega(DataYMD($arrMateriaisLead[$i]['dt_entrega']));
    
                        if($arrMateriaisLead[$i]['dt_devolucao']!=""){
                            $movimentacao_estoque->setdt_devolucao(DataYMD($arrMateriaisLead[$i]['dt_devolucao']));
                        }
                        $movimentacao_estoque->setobs_movimentacao($arrMateriaisLead[$i]['obs_movimentacao']);
    
                        $pk = $movimentacao_estoquedao->salvar($movimentacao_estoque);
                    }
                }
    
                
    
    
    
    
                */
    
    
                Json::run($retorno->status, $retorno->data, $retorno->message);
            

            
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

	public function receptivo(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $local = isset($data['local'])? $data['local']: "";
            $this->view->render($response, 'colaborador/colaborador_res_form.twig',array(
                "local"=>$local
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function print(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: "";
            $retorno = (new Colaborador($this->pdo))->listarColaboradorPkPrint($pk);

            //var_dump($retorno->data);
            //die();
            $this->view->render($response, 'colaborador/colaborador_print_form.twig',array(
                "arrDados"=>$retorno->data
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function fcAbrirGridForulario(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $local = isset($data['local'])? $data['local']: "";
            $this->view->render($response, 'colaborador/colaborador_formulario_contrato_res_form.twig',array(
                "pk"=>$pk,
                "local"=>$local
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function painel(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
           
            $arrColaborador = (new Colaborador($this->pdo))->listarTodosAtivo($pk);
            
           
            $local = isset($data['local'])? $data['local']: "";
            $this->view->render($response, 'colaborador/painel.twig',array(
                "colaborador_pk"=>$pk,
                "arrColaborador"=>$arrColaborador->data[0],
                "local"=>$local
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function cadForm(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            
           
            $local = isset($data['local'])? $data['local']: "";
            $this->view->render($response, 'colaborador/colaborador_cad_form.twig',array(
                "colaborador_pk"=>$pk,
                "local"=>$local
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function cadFormCliente(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $local = isset($data['local'])? $data['local']: "";
            $this->view->render($response, 'partials/cliente/colaborador_cad_form_cliente.twig',array(
                "colaborador_pk"=>$pk,
                "local"=>$local
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";

            if($pk!=""){
                (new Log($this->pdo))->salvar('colaborador', $pk);
                
                (new Colaborador($this->pdo))->excluir($pk);
                
                Json::run(true, [], 'Registro excluído com sucesso!');
            }else{
                Json::run(false, [], 'Falha ao excluir registro!');
            }
        }catch(Throwable $th){
            return $response->withJson((object)[
                'error'=>$th->getMessage()
            ],500,[]);
        }
    }

    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            
            $pk = isset($data['pk'])?$data['pk'] : "";
            $retorno = (new Colaborador($this->pdo))->listarTodos($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodosAtivo(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            
            $pk = isset($data['pk'])?$data['pk'] : "";
            $retorno = (new Colaborador($this->pdo))->listarTodosAtivo($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarColaboradorEscala(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            
            $pk = isset($data['pk'])?$data['pk'] : "";
            $retorno = (new Colaborador($this->pdo))->listarColaboradorEscala();
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    
    public function listarColaboradoresQualificacao(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ds_produtos_servicos = isset($data['ds_produtos_servicos'])?$data['ds_produtos_servicos'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])?$data['colaborador_pk'] : "";
            $retorno = (new Colaborador($this->pdo))->listarColaboradoresQualificacao($ds_produtos_servicos, $colaborador_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarColaboradorLead(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])?$data['leads_pk'] : "";
            $retorno = (new Colaborador($this->pdo))->listarColaboradorLead($leads_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarColaboradoresQualidicacaoContrato(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $contratos_pk = isset($data['contratos_pk'])?$data['contratos_pk'] : "";
            $produtos_servicos_pk = isset($data['produtos_servicos_pk'])?$data['produtos_servicos_pk'] : "";
            $retorno = (new Colaborador($this->pdo))->listarColaboradoresQualidicacaoContrato($contratos_pk,$produtos_servicos_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function RelatorioDadosColaborador(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])?$data['pk'] : "";
            $ic_status = isset($data['ic_status'])?$data['ic_status'] : "";
            $retorno = (new Colaborador($this->pdo))->RelatorioDadosColaborador($pk,$ic_status);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarColaboradorLeadCalendario(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])?$data['leads_pk'] : "";
            $retorno = (new Colaborador($this->pdo))->listarColaboradorLeadCalendario($leads_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarPk(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
           
            $retorno = (new Colaborador($this->pdo))->listarPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDsPin(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new Colaborador($this->pdo))->listarDsPin($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoRelAniversarianteMes(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/rel_colaboradores_aniversariante_res.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function relAniversariantes(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $mes_aniversario_pk = isset($data['mes_aniversario_pk'])? $data['mes_aniversario_pk']: "";
            $ds_mes_aniversario = isset($data['ds_mes_aniversario'])? $data['ds_mes_aniversario']: "";
            $this->view->render($response, 'relatorio/rel_colaboradores_aniversariantes_cad_form.twig',array(
                "mes_aniversario_pk"=>$mes_aniversario_pk,
                "ds_mes_aniversario" =>$ds_mes_aniversario
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function relatorioAniversariantesMes(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $mes_pk = isset($data['mes_pk'])? $data['mes_pk'] : "";
            (new Colaborador($this->pdo))->relatorioAniversariantesMes($mes_pk,1);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function exportRelAniversarianteMes(Request $request, Response $response,$args){
        try{
            $data = $request->getQueryParams();
            $mes_pk = isset($data['mes_pk'])? $data['mes_pk'] : "";
            $anexo = (new Colaborador($this->pdo))->relatorioAniversariantesMes($mes_pk,0);
            
            header("Expires: Mon, 18 Nov 1985 18:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/x-msexcel");
            header("Content-Disposition: attachment; filename=RelAniversariosColaboradores.xls");
            header("Content-Type: text/html; charset=UTF-8");

            $str = '';
            $str .= '<table>';
            $str .= '<thead>';
            $str .= '<tr>';
            $str .= '<th>Nome</th>';
            $str .= '<th>Posto de Trabalho</th>';
            $str .= '<th>Dt. de Nascimento</th>';
            $str .= '<th>Escala</th>';
            $str .= '</tr>';
            $str .= '</thead>';
            $str .= '<tbody>';
            for ($i = 0; $i < count($anexo->data); $i++) {
                $str .= '<tr>';
                $str .= '<th>' . $anexo->data[$i]['ds_colaborador'] . '</th>';
                $str .= '<th>' . $anexo->data[$i]['ds_lead'] . '</th>';
                $str .= '<th>' . $anexo->data[$i]['dt_nascimento'] . '</th>';
                $str .= '<th>' . $anexo->data[$i]['n_qtde_dias_semana'] . '</th>';
                $str .= '</tr>';
            }
            $str .= '</tbody>';
            $str .= '</table>';

            echo "\xEF\xBB\xBF"; //UTF-8 BOM
            echo $str;
            
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    
    public function receptivoRelCursos(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/rel_colaboradores_curso_res.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function relCursos(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $cursos_pk = isset($data['cursos_pk'])? $data['cursos_pk']: "";
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk']: "";
            $dt_execucao_ini = isset($data['dt_execucao_ini'])? $data['dt_execucao_ini']: "";
            $dt_execucao_fim = isset($data['dt_execucao_fim'])? $data['dt_execucao_fim']: "";
            $dt_validacao_ini = isset($data['dt_validacao_ini'])? $data['dt_validacao_ini']: "";
            $dt_validacao_fim = isset($data['dt_validacao_fim'])? $data['dt_validacao_fim']: "";
            $ds_curso = isset($data['ds_curso'])? $data['ds_curso']: "";
            $ds_colaborador = isset($data['ds_colaborador'])? $data['ds_colaborador']: "";

            
            $this->view->render($response, 'relatorio/rel_colaboradores_curso_cad.twig',array(
                "cursos_pk"=>$cursos_pk,
                "colaboradores_pk"=>$colaboradores_pk,
                "dt_execucao_ini"=>$dt_execucao_ini,
                "dt_execucao_fim"=>$dt_execucao_fim,
                "dt_validacao_ini"=>$dt_validacao_ini,
                "dt_validacao_fim"=>$dt_validacao_fim,
                "ds_curso"=>$ds_curso,
                "ds_colaborador"=>$ds_colaborador,   
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function RelatorioColaboradorCurso(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $cursos_pk = isset($data['cursos_pk'])? $data['cursos_pk'] : "";
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";
            $dt_execucao_ini = isset($data['dt_execucao_ini'])? $data['dt_execucao_ini'] : "";
            $dt_execucao_fim = isset($data['dt_execucao_fim'])? $data['dt_execucao_fim'] : "";
            $dt_validacao_ini = isset($data['dt_validacao_ini'])? $data['dt_validacao_ini'] : "";
            $dt_validacao_fim = isset($data['dt_validacao_fim'])? $data['dt_validacao_fim'] : "";
            (new Colaborador($this->pdo))->RelatorioColaboradorCurso($colaboradores_pk,$cursos_pk,$dt_execucao_ini,$dt_execucao_fim,$dt_validacao_ini,$dt_validacao_fim,1);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function exportRelCurso(Request $request, Response $response,$args){
        try{
            $data = $request->getQueryParams();
            $cursos_pk = isset($data['cursos_pk'])? $data['cursos_pk'] : "";
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";
            $dt_execucao_ini = isset($data['dt_execucao_ini'])? $data['dt_execucao_ini'] : "";
            $dt_execucao_fim = isset($data['dt_execucao_fim'])? $data['dt_execucao_fim'] : "";
            $dt_validacao_ini = isset($data['dt_validacao_ini'])? $data['dt_validacao_ini'] : "";
            $dt_validacao_fim = isset($data['dt_validacao_fim'])? $data['dt_validacao_fim'] : "";
            $anexo = (new Colaborador($this->pdo))->RelatorioColaboradorCurso($colaboradores_pk,$cursos_pk,$dt_execucao_ini,$dt_execucao_fim,$dt_validacao_ini,$dt_validacao_fim,0);
            
            
            header("Expires: Mon, 18 Nov 1985 18:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/x-msexcel");
            header("Content-Disposition: attachment; filename=RelCursoColaboradores.xls");
            header("Content-Type: text/html; charset=UTF-8");

            $str = '';
            $str .= '<table>';
            $str .= '<thead>';
            $str .= '<tr>';
            $str .= '<th>Curso</th>';
            $str .= '<th>Colaborador</th>';
            $str .= '<th>Data Execução</th>';
            $str .= '<th>Data Vencimento</th>';
            $str .= '</tr>';
            $str .= '</thead>';
            $str .= '<tbody>';
            for ($i = 0; $i < count($anexo->data); $i++) {
                $str .= '<tr>';
                $str .= '<th>' . $anexo->data[$i]['ds_curso'] . '</th>';
                $str .= '<th>' . $anexo->data[$i]['ds_colaborador'] . '</th>';
                $str .= '<th>' . $anexo->data[$i]['dt_execucao'] . '</th>';
                $str .= '<th>' . $anexo->data[$i]['dt_validacao'] . '</th>';
                $str .= '</tr>';
            }
            $str .= '</tbody>';
            $str .= '</table>';

            echo "\xEF\xBB\xBF"; //UTF-8 BOM
            echo $str;
            
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarColaboradorPorLead(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
          
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            (new Colaborador($this->pdo))->listarColaboradorPorLead( $leads_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
	public function listarGrid(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
          
            $colaborador_pk = isset($data['pk'])? $data['pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $ic_origem = isset($data['ic_origem'])? $data['ic_origem'] : "";
            $ds_pin = isset($data['ds_pin'])? $data['ds_pin'] : "";
            $ds_cpf = isset($data['ds_cpf'])? $data['ds_cpf'] : "";
            $generos_pk = isset($data['generos_pk'])? $data['generos_pk'] : "";
            $ds_re = isset($data['ds_re'])? $data['ds_re'] : "";
            $ic_status_app = isset($data['ic_status_app'])? $data['ic_status_app'] : "";
            $ic_reserva = isset($data['ic_reserva'])? $data['ic_reserva'] : "";
            $ds_produto_servico = isset($data['ds_produto_servico'])? $data['ds_produto_servico'] : "";

            (new Colaborador($this->pdo))->listarGrid($colaborador_pk, $ic_status, $leads_pk, $ic_origem, $ds_pin, $ds_cpf, $generos_pk, $ds_re, $ic_status_app, $ic_reserva, $ds_produto_servico);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarGridCliente(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();

            $colaborador_pk = isset($data['pk'])? $data['pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $ic_origem = isset($data['ic_origem'])? $data['ic_origem'] : "";
            $ds_pin = isset($data['ds_pin'])? $data['ds_pin'] : "";
            $ds_cpf = isset($data['ds_cpf'])? $data['ds_cpf'] : "";
            $generos_pk = isset($data['generos_pk'])? $data['generos_pk'] : "";
            $ds_re = isset($data['ds_re'])? $data['ds_re'] : "";
            $ic_status_app = isset($data['ic_status_app'])? $data['ic_status_app'] : "";
            $ic_reserva = isset($data['ic_reserva'])? $data['ic_reserva'] : "";
            $ds_produto_servico = isset($data['ds_produto_servico'])? $data['ds_produto_servico'] : "";

            (new Colaborador($this->pdo))->listarGridCliente($colaborador_pk, $ic_status, $leads_pk, $ic_origem, $ds_pin, $ds_cpf, $generos_pk, $ds_re, $ic_status_app, $ic_reserva, $ds_produto_servico);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarFormulario(Request $request, Response $response, $args) {

        try{

            (new Colaborador($this->pdo))->listarFormulario();

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarCursoColaboradores(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";

            $retorno = (new Colaborador($this->pdo))->listarCursoColaboradores($colaborador_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarBeneficioColaboradores(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";

            $retorno = (new Colaborador($this->pdo))->listarBeneficioColaboradores($colaborador_pk);
            json::run($retorno->status,$retorno->data,"Dados Carregado com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarNomeFilhoColaboradorPk(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";

            $retorno = (new Colaborador($this->pdo))->listarNomeFilhoColaboradorPk($colaborador_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarAfastamentoColaboradores(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";

            $retorno = (new Colaborador($this->pdo))->listarAfastamentoColaboradores($colaborador_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDadosBancarios(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new Colaborador($this->pdo))->listarDadosBancarios($colaborador_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarColaboradorFolha(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            
            $empresas_pk = isset($data['empresas_pk'])?$data['empresas_pk'] : "";
            $leads_pk = isset($data['leads_pk'])?$data['leads_pk'] : "";
            $ic_escala = isset($data['ic_escala'])?$data['ic_escala'] : "";

            

            $retorno = (new Colaborador($this->pdo))->listarColaboradorFolha($empresas_pk, $leads_pk, $ic_escala);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
	public function RelatorioAcompanhamentoFerias(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            
            $colaboradores_pk = isset($data['colaboradores_pk'])?$data['colaboradores_pk'] : "";
            $dt_ini_ferias = isset($data['dt_ini_ferias'])?$data['dt_ini_ferias'] : "";
            $dt_fim_ferias = isset($data['dt_fim_ferias'])?$data['dt_fim_ferias'] : "";

            $retorno = (new Colaborador($this->pdo))->RelatorioAcompanhamentoFerias($colaboradores_pk, $dt_ini_ferias, $dt_fim_ferias);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
	public function verificarCpf(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            
            $ds_cpf = isset($data['ds_cpf'])?$data['ds_cpf'] : "";

            $count = (new Colaborador($this->pdo))->pegarColaboradorPorCpf($ds_cpf);
            
            if($count >= 1){

                Json::run(true, [], "Esse CPF já está cadastrado em outro colaborador !");
            }
            else{
                Json::run(false, [], "");
            }
            
        } catch (Throwable $th) {
           
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}



