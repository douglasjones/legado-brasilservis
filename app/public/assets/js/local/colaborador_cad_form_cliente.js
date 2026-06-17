var formdata = null;

function fcCancelar(){
    sendPost("menu","principal" ,{});
}

function fcCarregar(){

    if($("#colaborador_pk").val() > 0){

        var objParametros = {
            "pk": $("#colaborador_pk").val()
        };
        var arrCarregar = carregarController("colaborador", "listarPk", objParametros);
        //NewWindow(v_last_url)
        if (arrCarregar.status == true){
            if(arrCarregar.data[0]['ds_pin']!=null){
                $("#ds_pin").html("<h6>Pin: " + arrCarregar.data[0]['ds_pin']+"</h6>");
            }
            else{
                $("#ds_pin").html("");
            }

            $("#ds_colaborador").val(arrCarregar.data[0]['ds_colaborador']);

            $("#ds_nacionalidade").val(arrCarregar.data[0]['ds_nacionalidade']);
            $("#ds_cel").val(arrCarregar.data[0]['ds_cel']);
            $("#ic_whatsapp").val(arrCarregar.data[0]['ic_whatsapp']);
            $("#ds_cel2").val(arrCarregar.data[0]['ds_cel2']);
            $("#ic_whatsapp2").val(arrCarregar.data[0]['ic_whatsapp2']);
            $("#ds_cel3").val(arrCarregar.data[0]['ds_cel3']);
            $("#ic_whatsapp3").val(arrCarregar.data[0]['ic_whatsapp3']);
            $("#ds_email").val(arrCarregar.data[0]['ds_email']);
            $("#ds_rg").val(arrCarregar.data[0]['ds_rg']);
            $("#ds_cpf").val(arrCarregar.data[0]['ds_cpf']);
            $("#dt_nascimento").val(arrCarregar.data[0]['dt_nascimento']);
            $("#ds_endereco").val(arrCarregar.data[0]['ds_endereco']);
            $("#ds_numero").val(arrCarregar.data[0]['ds_numero']);
            $("#ds_complemento").val(arrCarregar.data[0]['ds_complemento']);
            $("#ds_bairro").val(arrCarregar.data[0]['ds_bairro']);
            $("#ds_cep").val(arrCarregar.data[0]['ds_cep']);
            $("#ds_cidade").val(arrCarregar.data[0]['ds_cidade']);
            $("#ds_uf").val(arrCarregar.data[0]['ds_uf']);
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);
            $("#generos_pk").val(arrCarregar.data[0]['generos_pk']);
            $("#ic_funcionario").val(arrCarregar.data[0]['ic_funcionario']);
            $("#ds_re").val(arrCarregar.data[0]['ds_re']);
            $("#ds_raca").val(arrCarregar.data[0]['ds_raca']);
            $("#ds_deficiencia_fisica").val(arrCarregar.data[0]['ds_deficiencia_fisica']);
            $("#estado_civil").val(arrCarregar.data[0]['estado_civil']);
            $("#ds_nome_pai").val(arrCarregar.data[0]['ds_nome_pai']);
            $("#ds_nome_mae").val(arrCarregar.data[0]['ds_nome_mae']);
            $("#ds_nome_conjuge").val(arrCarregar.data[0]['ds_nome_conjuge']);
            $("#dt_nascimento_conjuge").val(arrCarregar.data[0]['dt_nascimento_conjuge']);
            $("#ds_cpf_conjuge").val(arrCarregar.data[0]['ds_cpf_conjuge']);
            $("#ds_tel_conjuge").val(arrCarregar.data[0]['ds_tel_conjuge']);
            $("#regime_casamento").val(arrCarregar.data[0]['regime_casamento']);
            $("#ds_ctps").val(arrCarregar.data[0]['ds_ctps']);
            $("#ds_serie").val(arrCarregar.data[0]['ds_serie']);
            $("#dt_expedicao").val(arrCarregar.data[0]['dt_expedicao']);
            $("#ds_uf_rg").val(arrCarregar.data[0]['ds_uf_rg']);
            $("#ds_org_exp").val(arrCarregar.data[0]['ds_org_exp']);
            $("#ds_pis").val(arrCarregar.data[0]['ds_pis']);
            $("#ds_titulo_eleitoral").val(arrCarregar.data[0]['ds_titulo_eleitoral']);
            $("#ds_zona_eleitoral").val(arrCarregar.data[0]['ds_zona_eleitoral']);
            $("#ds_secao").val(arrCarregar.data[0]['ds_secao']);
            $("#ds_certificado_reservista").val(arrCarregar.data[0]['ds_certificado_reservista']);
            $("#ds_matricula").val(arrCarregar.data[0]['ds_matricula']);
            $("#grau_escolaridade_pk").val(arrCarregar.data[0]['grau_escolaridade_pk']);
            $("#dt_demissao").val(arrCarregar.data[0]['dt_demissao']);
            $("#dt_admissao").val(arrCarregar.data[0]['dt_admissao']);

            $("#empresas_pk").val(arrCarregar.data[0]['empresas_pk']);
            $("#regime_contratacao_pk").val(arrCarregar.data[0]['regime_contratacao_pk']);
            $("#ds_carga_horaria_semanal").val(arrCarregar.data[0]['ds_carga_horaria_semanal']);

            $("#ds_n_sapato").val(arrCarregar.data[0]['ds_n_sapato']);
            $("#ds_n_camisa").val(arrCarregar.data[0]['ds_n_camisa']);
            $("#ds_n_calca").val(arrCarregar.data[0]['ds_n_calca']);
            $("#ds_n_luva").val(arrCarregar.data[0]['ds_n_luva']);

            $("#tipo_conta_bancaria").val(arrCarregar.data[0]['tipo_conta_bancaria']);
            $("#ds_agencia").val(arrCarregar.data[0]['ds_agencia']);
            $("#ds_conta").val(arrCarregar.data[0]['ds_conta']);
            $("#ds_digito").val(arrCarregar.data[0]['ds_digito']);
            $("#bancos_pk").val(arrCarregar.data[0]['bancos_pk']);
            $("#vl_salario").val(float2moeda(arrCarregar.data[0]['vl_salario']));
            $("#ds_pix").val(arrCarregar.data[0]['ds_pix'])
            $("#ds_conta_favorecido").val(arrCarregar.data[0]['ds_conta_favorecido'])

            $("#ic_tipo_sanguineo").val(arrCarregar.data[0]['ic_tipo_sanguineo'])
            $("#ds_cartao_sus").val(arrCarregar.data[0]['ds_cartao_sus'])
            $("#ic_tipo_sanguineo_conjuge").val(arrCarregar.data[0]['ic_tipo_sanguineo_conjuge'])
            $("#ds_cartao_sus_conjuge").val(arrCarregar.data[0]['ic_ds_cartao_sus_conjuge'])
            $("#ic_experiencia").val(arrCarregar.data[0]['ic_experiencia'])

            if(arrCarregar.data[0]['ds_imagem']!=null){
                var link = arrCarregar.data[0]['ds_imagem'];
                var linkDesescapado = link.replaceAll('\\/', '/');

                $("#ds_imagem").html("<img width=100 height=120 src='" + linkDesescapado + "'>");
            }else{
                $("#ds_imagem").html(' <img src="/assets/img/profile/avatar.jpg" width="100" height="100">');
            }

            $("#dt_liberado").html(arrCarregar.data[0]['dt_liberado']);

            if(arrCarregar.data[0]['ds_status_app']!= null){
                $("#ds_status_app").html("Status liberação acesso App Ponto :<b>"+arrCarregar.data[0]['ds_status_app']+"</b>");
            }else{

                $("#ds_status_app").html("Status liberação acesso App Ponto: <b>Não solicitado</b>");
            }

            if(arrCarregar.data[0]['ds_status_app']=='Liberado'){
                $("#dt_liberacao").html("Data da Liberação acesso App Ponto <b>"+arrCarregar.data[0]['dt_liberacao']+"</b>");
            }

            if(arrCarregar.data[0]['ic_filho_menor_14']==1){
                $("#exibir_qtde_filho").show();
                $("input[id=ic_filho_menor_14]").prop("checked", "true");
                $("#qtde_filho").val(arrCarregar.data[0]['qtde_filho']);
                $("#exibir_nome_filho").show();
                setTimeout(function(){
                    fcAtualizarDadosGridNomeFilho();
                }, 500);
            }
            if(arrCarregar.data[0]['ic_reserva']==1){
                $("input[id=ic_reserva]").prop("checked", "true");
            }
        }
        else{
            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
        }

    }
}

function fcCarregarTurno(){

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("turno", "listarTodos", objParametros);

    carregarComboAjax($("#dom_turnos_pk"), arrCarregar, " ", "pk", "ds_turno");
    carregarComboAjax($("#seg_turnos_pk"), arrCarregar, " ", "pk", "ds_turno");
    carregarComboAjax($("#ter_turnos_pk"), arrCarregar, " ", "pk", "ds_turno");
    carregarComboAjax($("#qua_turnos_pk"), arrCarregar, " ", "pk", "ds_turno");
    carregarComboAjax($("#qui_turnos_pk"), arrCarregar, " ", "pk", "ds_turno");
    carregarComboAjax($("#sex_turnos_pk"), arrCarregar, " ", "pk", "ds_turno");
    carregarComboAjax($("#sab_turnos_pk"), arrCarregar, " ", "pk", "ds_turno");

}

function fcLimparPonto(){

    $("#ic_registrar_ponto").prop("checked", false);
    $("#ic_dom").prop("checked", false);
    $("#ic_seg").prop("checked", false);
    $("#ic_ter").prop("checked", false);
    $("#ic_qua").prop("checked", false);
    $("#ic_qui").prop("checked", false);
    $("#ic_sex").prop("checked", false);
    $("#ic_sab").prop("checked", false);

    $("#colaborador_ponto_pk").val("");
    $("#dom_turnos_pk").val("");
    $("#seg_turnos_pk").val("");
    $("#ter_turnos_pk").val("");
    $("#qua_turnos_pk").val("");
    $("#qui_turnos_pk").val("");
    $("#sex_turnos_pk").val("");
    $("#sab_turnos_pk").val("");
    $("#hr_entrada_dom").val("");
    $("#hr_saida_dom").val("");
    $("#hr_entrada_seg").val("");
    $("#hr_saida_seg").val("");
    $("#hr_entrada_ter").val("");
    $("#hr_saida_ter").val("");
    $("#hr_entrada_qua").val("");
    $("#hr_saida_qua").val("");
    $("#hr_entrada_qui").val("");
    $("#hr_saida_qui").val("");
    $("#hr_entrada_sex").val("");
    $("#hr_saida_sex").val("");
    $("#hr_entrada_sab").val("");
    $("#hr_saida_sab").val("");

}

function fcCarregarGenero(){
    //Carrega os grupos

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("genero", "listarTodos", objParametros);

    carregarComboAjax($("#generos_pk"), arrCarregar, " ", "pk", "ds_genero");

}


function fcVerificarCNPJ(){
    var ds_cpf_cnpj = $("#ds_cpf").val();
    if(ds_cpf_cnpj.length == 14){
        var objParametros = {
            "ds_cpf": $("#ds_cpf").val()
        };

        var arrCarregar = carregarController("colaborador", "verificarCPF", objParametros);

        if (arrCarregar.result == 'success'){

            if(arrCarregar.data.length > 0){
                sweetMensagem('warning',"Já existe um Colaborador com esse CPF");
                $("#ds_colaborador").val("");
                $("#generos_pk").val("");
                $("#ds_cel").val("");
                $("#ds_cel2").val("");
                $("#dt_nascimento").val("");
                $("#ds_cel3").val("");
                $("#ds_rg").val("");
                $("#ds_cpf").val("");
                $("#ic_whatsapp").val("");
                $("#ds_email").val("");
            }
        }
        else{
            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
        }
    }


}


//--------------------------------------------------------------NOME FILHO-----------------------------------------------------
function fcFormatarGridNomeFilho(){
    tblNomeFilho = $("#tblNomeFilho").DataTable(
        {
            "searching": false,
            "paging": false,
            "columnDefs" : [{
                orderable: false,
                targets: [0,1,2,3,4]
            }]
        }
    );
    return false;

}

function fcAtualizarDadosGridNomeFilho(){

    var objParametros = {
        "colaborador_pk":$("#colaborador_pk").val()
    };

    var arrCarregar = carregarController("colaborador", "listarNomeFilhoColaboradorPk", objParametros);

    if (arrCarregar.status == true){
        if(arrCarregar.data.length>0){
            for(i = 0; i < arrCarregar.data.length; i++){

                //Adiciona a linha.
                fcIncluirNomeFilho();

                //Pega as variaveis
                var ds_nome_filho = $("input[id='ds_nome_filho']");
                var ds_cpf_filho = $("input[id='ds_cpf_filho']");
                var dt_nascimento_filho = $("input[id='dt_nascimento_filho']");
                var ds_tipo_sanguineo_dependente = $("input[id='ds_tipo_sanguineo_dependente']");
                var ds_num_cartao_sus_dependente = $("input[id='ds_num_cartao_sus_dependente']");


                ds_nome_filho.get(i).value = arrCarregar.data[i]['ds_nome_filho'];
                ds_cpf_filho.get(i).value = arrCarregar.data[i]['ds_cpf_filho'];
                dt_nascimento_filho.get(i).value = arrCarregar.data[i]['dt_nascimento_filho'];
                ds_tipo_sanguineo_dependente.get(i).value = arrCarregar.data[i]['ds_tipo_sanguineo_dependente'];
                ds_num_cartao_sus_dependente.get(i).value = arrCarregar.data[i]['ds_num_cartao_sus_dependente'];

            }
        }

    }
    else{

        utilsJS.toastNotify(false,'Falhar ao carregar o registro');
    }

}



function fcCarregarEmpresa(){

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("conta", "listarTodos", objParametros);
    carregarComboAjax($("#empresas_pk"), arrCarregar, " ", "pk", "ds_conta");

}
function fcCarregarBancos(){

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("banco", "listarTodos", objParametros);
    carregarComboAjax($("#bancos_pk"), arrCarregar, " ", "pk", "ds_banco");

}



$(document).ready(function()
    {

        $(document).on('click', '#cmdCancelar', fcCancelar);
        $(document).on('click', '#cmdCancelar1', fcCancelar);


        $('#dt_nascimento').datepicker({defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked",
            minDate: 0
        });
        $('#dt_nascimento_conjuge').datepicker({defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked",
            minDate: 0
        });
        $('#dt_expedicao').datepicker({defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked",
            minDate: 0
        });
        $('#dt_admissao').datepicker({defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked",
            minDate: 0
        });
        $('#dt_demissao').datepicker({defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked",
            minDate: 0
        });

        $("#ds_agencia").keypress(function(){
            mascara(this,soNumeros);
        });
        $("#ds_conta").keypress(function(){
            mascara(this,soNumeros);
        });
        $("#ds_agencia").keypress(function(){
            mascara(this,soNumeros);
        });

        $("#vl_salario").keypress(function(){
            mascara(this,moeda);
        });

        $("#dt_admissao").keypress(function(){
            mascara(this,mdata);
        });
        $("#dt_demissao").keypress(function(){
            mascara(this,mdata);
        });
        $("#dt_nascimento").keypress(function(){
            mascara(this,mdata);
        });
        $("#dt_nascimento_conjuge").keypress(function(){
            mascara(this,mdata);
        });
        $("#dt_expedicao").keypress(function(){
            mascara(this,mdata);
        });
        $("#ds_cep").keypress(function(){
            mascara(this,cep);
        });
        $("#ds_cpf").keypress(function(){
            chama_mascara(this);
        });
        $("#ds_cpf_conjuge").keypress(function(){
            chama_mascara(this);
        });
        $("#ds_cel").keypress(function(){
            mascara(this,mascaraTelefone);
        });
        $("#ds_tel_conjuge").keypress(function(){
            mascara(this,mascaraTelefone);
        });
        $("#ds_cel2").keypress(function(){
            mascara(this,mascaraTelefone);
        });
        $("#ds_cel3").keypress(function(){
            mascara(this,mascaraTelefone);
        });

        $("#ds_cep").change(function(){
            fcCarregarCep($("#ds_cep").val());
        });

        $("#exibir_qtde_filho").hide();
        $("#exibir_nome_filho").hide();

        fcFormatarGridNomeFilho();


        $("#ic_filho_menor_14").change(function(){
            if($('#ic_filho_menor_14').is(":checked")){
                $("#exibir_qtde_filho").show();
            }
            else{
                $("#exibir_qtde_filho").hide();
                $("#qtde_filho").val("");
                $("#exibir_nome_filho").hide();
            }
        });

        $("#qtde_filho").change(function(){
            if($('#qtde_filho').val()!=""){
                tblNomeFilho.clear().destroy();
                fcFormatarGridNomeFilho();
                //fcAtualizarDadosGridNomeFilho();
                $("#exibir_nome_filho").show();

                for(i=0;i<$('#qtde_filho').val();i++){
                    fcIncluirNomeFilho();
                }

            }
            else{
                tblNomeFilho.clear().destroy();
                fcFormatarGridNomeFilho();
                //fcAtualizarDadosGridNomeFilho();
                $("#exibir_nome_filho").hide();
            }
        });

        //----------------------FINAL GRID------------------




        $("#hr_entrada_dom").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_saida_dom").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_entrada_seg").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_saida_seg").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_entrada_ter").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_saida_ter").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_entrada_qua").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_saida_qua").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_entrada_qui").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_saida_qui").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_entrada_sex").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_saida_sex").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_entrada_sab").keypress(function(){
            mascara(this,horamask);
        });
        $("#hr_saida_sab").keypress(function(){
            mascara(this,horamask);
        });

        if($("#colaborador_pk").val()!=""){
            $("#exibir_materiais").show();
            $("#exibir_afastamento").show();

        };

        //Atribui a validação do formulário dos campos obrigatórios
        //fcValidarFormColaborador();

        fcCarregarGenero();

        fcCarregarEmpresa();

        fcCarregarBancos();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();





        $("#dados-tab").click(function(){
            $("#dados-tab").removeClass();
            $("#dados-tab").addClass('nav-link active');

            $("#qualificacao-tab").removeClass();
            $("#qualificacao-tab").addClass('nav-link');

            $("#cursos-tab").removeClass();
            $("#cursos-tab").addClass('nav-link');

            $("#controleescalas-tab").removeClass();
            $("#controleescalas-tab").addClass('nav-link');

            $("#beneficios-tab").removeClass();
            $("#beneficios-tab").addClass('nav-link');

            $("#afastamento-tab").removeClass();
            $("#afastamento-tab").addClass('nav-link');

            $("#documentacao-tab").removeClass();
            $("#documentacao-tab").addClass('nav-link');

            $("#materiais-tab").removeClass();
            $("#materiais-tab").addClass('nav-link');


            $("#dados").addClass('tab-pane fade show active');

            $("#qualificacao").removeClass();
            $("#qualificacao").addClass('tab-pane fade');

            $("#cursos").removeClass();
            $("#cursos").addClass('tab-pane fade');

            $("#controleescalas").removeClass();
            $("#controleescalas").addClass('tab-pane fade')

            $("#beneficios").removeClass();
            $("#beneficios").addClass('tab-pane fade');

            $("#afastamento").removeClass();
            $("#afastamento").addClass('tab-pane fade');

            $("#documentacao").removeClass();
            $("#documentacao").addClass('tab-pane fade');

            $("#materiais").removeClass();
            $("#materiais").addClass('tab-pane fade');

        });
        $("#qualificacao-tab").click(function(){
            $("#dados-tab").removeClass();
            $("#dados-tab").addClass('nav-link ');

            $("#qualificacao-tab").removeClass();
            $("#qualificacao-tab").addClass('nav-link active');

            $("#cursos-tab").removeClass();
            $("#cursos-tab").addClass('nav-link');

            $("#controleescalas-tab").removeClass();
            $("#controleescalas-tab").addClass('nav-link');

            $("#beneficios-tab").removeClass();
            $("#beneficios-tab").addClass('nav-link');

            $("#afastamento-tab").removeClass();
            $("#afastamento-tab").addClass('nav-link');

            $("#documentacao-tab").removeClass();
            $("#documentacao-tab").addClass('nav-link');

            $("#materiais-tab").removeClass();
            $("#materiais-tab").addClass('nav-link');

            $("#dados").removeClass();
            $("#dados").addClass('tab-pane fade ');

            $("#qualificacao").removeClass();
            $("#qualificacao").addClass('tab-pane fade show active');

            $("#cursos").removeClass();
            $("#cursos").addClass('tab-pane fade');

            $("#controleescalas").removeClass();
            $("#controleescalas").addClass('tab-pane fade')

            $("#beneficios").removeClass();
            $("#beneficios").addClass('tab-pane fade');

            $("#afastamento").removeClass();
            $("#afastamento").addClass('tab-pane fade');

            $("#documentacao").removeClass();
            $("#documentacao").addClass('tab-pane fade');

            $("#materiais").removeClass();
            $("#materiais").addClass('tab-pane fade');

        });
        $("#controleescalas-tab").click(function(){
            $("#dados-tab").removeClass();
            $("#dados-tab").addClass('nav-link ');

            $("#qualificacao-tab").removeClass();
            $("#qualificacao-tab").addClass('nav-link ');

            $("#controleescalas-tab").removeClass();
            $("#controleescalas-tab").addClass('nav-link active');

            $("#cursos-tab").removeClass();
            $("#cursos-tab").addClass('nav-link');

            $("#beneficios-tab").removeClass();
            $("#beneficios-tab").addClass('nav-link');

            $("#afastamento-tab").removeClass();
            $("#afastamento-tab").addClass('nav-link');

            $("#documentacao-tab").removeClass();
            $("#documentacao-tab").addClass('nav-link');

            $("#materiais-tab").removeClass();
            $("#materiais-tab").addClass('nav-link');

            $("#dados").removeClass();
            $("#dados").addClass('tab-pane fade ');

            $("#qualificacao").removeClass();
            $("#qualificacao").addClass('tab-pane');

            $("#cursos").removeClass();
            $("#cursos").addClass('tab-pane fade ');

            $("#controleescalas").removeClass();
            $("#controleescalas").addClass('tab-pane fade show active')

            $("#beneficios").removeClass();
            $("#beneficios").addClass('tab-pane fade');

            $("#afastamento").removeClass();
            $("#afastamento").addClass('tab-pane fade');

            $("#documentacao").removeClass();
            $("#documentacao").addClass('tab-pane fade');

            $("#materiais").removeClass();
            $("#materiais").addClass('tab-pane fade');

        });
        $("#cursos-tab").click(function(){
            $("#dados-tab").removeClass();
            $("#dados-tab").addClass('nav-link ');

            $("#qualificacao-tab").removeClass();
            $("#qualificacao-tab").addClass('nav-link ');

            $("#controleescalas-tab").removeClass();
            $("#controleescalas-tab").addClass('nav-link ');

            $("#cursos-tab").removeClass();
            $("#cursos-tab").addClass('nav-link active');

            $("#beneficios-tab").removeClass();
            $("#beneficios-tab").addClass('nav-link');

            $("#afastamento-tab").removeClass();
            $("#afastamento-tab").addClass('nav-link');

            $("#documentacao-tab").removeClass();
            $("#documentacao-tab").addClass('nav-link');

            $("#materiais-tab").removeClass();
            $("#materiais-tab").addClass('nav-link');

            $("#dados").removeClass();
            $("#dados").addClass('tab-pane fade ');

            $("#qualificacao").removeClass();
            $("#qualificacao").addClass('tab-pane');

            $("#cursos").removeClass();
            $("#cursos").addClass('tab-pane fade show active');

            $("#controleescalas").removeClass();
            $("#controleescalas").addClass('tab-pane fade ')

            $("#beneficios").removeClass();
            $("#beneficios").addClass('tab-pane fade');

            $("#afastamento").removeClass();
            $("#afastamento").addClass('tab-pane fade');

            $("#documentacao").removeClass();
            $("#documentacao").addClass('tab-pane fade');

            $("#materiais").removeClass();
            $("#materiais").addClass('tab-pane fade');

        });
        $("#beneficios-tab").click(function(){
            $("#dados-tab").removeClass();
            $("#dados-tab").addClass('nav-link ');

            $("#qualificacao-tab").removeClass();
            $("#qualificacao-tab").addClass('nav-link ');

            $("#controleescalas-tab").removeClass();
            $("#controleescalas-tab").addClass('nav-link ');

            $("#cursos-tab").removeClass();
            $("#cursos-tab").addClass('nav-link ');

            $("#beneficios-tab").removeClass();
            $("#beneficios-tab").addClass('nav-link active');

            $("#afastamento-tab").removeClass();
            $("#afastamento-tab").addClass('nav-link');

            $("#documentacao-tab").removeClass();
            $("#documentacao-tab").addClass('nav-link');

            $("#materiais-tab").removeClass();
            $("#materiais-tab").addClass('nav-link');

            $("#dados").removeClass();
            $("#dados").addClass('tab-pane fade ');

            $("#qualificacao").removeClass();
            $("#qualificacao").addClass('tab-pane');

            $("#cursos").removeClass();
            $("#cursos").addClass('tab-pane fade ');

            $("#controleescalas").removeClass();
            $("#controleescalas").addClass('tab-pane fade ')

            $("#beneficios").removeClass();
            $("#beneficios").addClass('tab-pane fade show active');

            $("#afastamento").removeClass();
            $("#afastamento").addClass('tab-pane fade');

            $("#documentacao").removeClass();
            $("#documentacao").addClass('tab-pane fade');

            $("#materiais").removeClass();
            $("#materiais").addClass('tab-pane fade');

        });
        $("#afastamento-tab").click(function(){
            $("#dados-tab").removeClass();
            $("#dados-tab").addClass('nav-link ');

            $("#qualificacao-tab").removeClass();
            $("#qualificacao-tab").addClass('nav-link ');

            $("#controleescalas-tab").removeClass();
            $("#controleescalas-tab").addClass('nav-link ');

            $("#cursos-tab").removeClass();
            $("#cursos-tab").addClass('nav-link ');

            $("#beneficios-tab").removeClass();
            $("#beneficios-tab").addClass('nav-link ');

            $("#afastamento-tab").removeClass();
            $("#afastamento-tab").addClass('nav-link active');

            $("#documentacao-tab").removeClass();
            $("#documentacao-tab").addClass('nav-link');

            $("#materiais-tab").removeClass();
            $("#materiais-tab").addClass('nav-link');

            $("#dados").removeClass();
            $("#dados").addClass('tab-pane fade ');

            $("#qualificacao").removeClass();
            $("#qualificacao").addClass('tab-pane');

            $("#cursos").removeClass();
            $("#cursos").addClass('tab-pane fade ');

            $("#controleescalas").removeClass();
            $("#controleescalas").addClass('tab-pane fade ')

            $("#beneficios").removeClass();
            $("#beneficios").addClass('tab-pane fade ');

            $("#afastamento").removeClass();
            $("#afastamento").addClass('tab-pane fade show active');

            $("#documentacao").removeClass();
            $("#documentacao").addClass('tab-pane fade');

            $("#materiais").removeClass();
            $("#materiais").addClass('tab-pane fade');

        });
        $("#documentacao-tab").click(function(){
            $("#dados-tab").removeClass();
            $("#dados-tab").addClass('nav-link ');

            $("#qualificacao-tab").removeClass();
            $("#qualificacao-tab").addClass('nav-link ');

            $("#controleescalas-tab").removeClass();
            $("#controleescalas-tab").addClass('nav-link ');

            $("#cursos-tab").removeClass();
            $("#cursos-tab").addClass('nav-link ');

            $("#beneficios-tab").removeClass();
            $("#beneficios-tab").addClass('nav-link ');

            $("#afastamento-tab").removeClass();
            $("#afastamento-tab").addClass('nav-link ');

            $("#documentacao-tab").removeClass();
            $("#documentacao-tab").addClass('nav-link active');

            $("#materiais-tab").removeClass();
            $("#materiais-tab").addClass('nav-link');

            $("#dados").removeClass();
            $("#dados").addClass('tab-pane fade ');

            $("#qualificacao").removeClass();
            $("#qualificacao").addClass('tab-pane');

            $("#cursos").removeClass();
            $("#cursos").addClass('tab-pane fade ');

            $("#controleescalas").removeClass();
            $("#controleescalas").addClass('tab-pane fade ')

            $("#beneficios").removeClass();
            $("#beneficios").addClass('tab-pane fade ');

            $("#afastamento").removeClass();
            $("#afastamento").addClass('tab-pane fade ');

            $("#documentacao").removeClass();
            $("#documentacao").addClass('tab-pane fade show active');

            $("#materiais").removeClass();
            $("#materiais").addClass('tab-pane fade');

        });
        $("#materiais-tab").click(function(){
            $("#dados-tab").removeClass();
            $("#dados-tab").addClass('nav-link ');

            $("#qualificacao-tab").removeClass();
            $("#qualificacao-tab").addClass('nav-link ');

            $("#controleescalas-tab").removeClass();
            $("#controleescalas-tab").addClass('nav-link ');

            $("#cursos-tab").removeClass();
            $("#cursos-tab").addClass('nav-link ');

            $("#beneficios-tab").removeClass();
            $("#beneficios-tab").addClass('nav-link ');

            $("#afastamento-tab").removeClass();
            $("#afastamento-tab").addClass('nav-link ');

            $("#documentacao-tab").removeClass();
            $("#documentacao-tab").addClass('nav-link ');

            $("#materiais-tab").removeClass();
            $("#materiais-tab").addClass('nav-link active');

            $("#dados").removeClass();
            $("#dados").addClass('tab-pane fade ');

            $("#qualificacao").removeClass();
            $("#qualificacao").addClass('tab-pane');

            $("#cursos").removeClass();
            $("#cursos").addClass('tab-pane fade ');

            $("#controleescalas").removeClass();
            $("#controleescalas").addClass('tab-pane fade ')

            $("#beneficios").removeClass();
            $("#beneficios").addClass('tab-pane fade ');

            $("#afastamento").removeClass();
            $("#afastamento").addClass('tab-pane fade ');

            $("#documentacao").removeClass();
            $("#documentacao").addClass('tab-pane fade ');

            $("#materiais").removeClass();
            $("#materiais").addClass('tab-pane fade show active');

        });
    }
);
