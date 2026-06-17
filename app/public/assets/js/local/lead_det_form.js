var tblContatos;
var rLinhaSelecionada = null;

function fcValidarForm(){

    if($('#ic_tipo_lead').val()==""){
        $("#alert_tipo_lead").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_tipo_lead").slideUp(500);
        });
        $('#ic_tipo_lead').focus();
        return false;
    }
    if($('#ds_lead').val()==""){
        $("#alert_ds_lead").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_lead").slideUp(500);
        });
        $('#ds_lead').focus();
        return false;
    }
    if($("#ic_tipo_lead").val()==1){
        if($('#ds_cpf_cnpj').val()==""){
            $("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_cnpj").slideUp(500);
            });
            $('#ds_cpf_cnpj').focus();
            return false;
        }
        else  if($('#ds_cpf_cnpj').val()!=""){
            var ds_cpf_cnpj = $('#ds_cpf_cnpj').val();
            if(ds_cpf_cnpj.length < 14 ){
                $("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                    $("#alert_cnpj").slideUp(500);
                });
                $('#ds_cpf_cnpj').focus();
                return false;
            }
            else if(ds_cpf_cnpj.length > 14 && ds_cpf_cnpj.length < 18 ){
                $("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                    $("#alert_cnpj").slideUp(500);
                });
                $('#ds_cpf_cnpj').focus();
                return false;
            }
        }
    }

    if($("#ic_tipo_lead").val()==2 && $("#leads_pai_pk").val()==""){

        if($('#ds_cpf_cnpj').val()==""){
            $("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_cnpj").slideUp(500);
            });
            $('#ds_cpf_cnpj').focus();
            return false;
        }
        else  if($('#ds_cpf_cnpj').val()!=""){

            var ds_cpf_cnpj = $('#ds_cpf_cnpj').val();
            if(ds_cpf_cnpj.length < 14 ){

                $("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                    $("#alert_cnpj").slideUp(500);
                });
                $('#ds_cpf_cnpj').focus();
                return false;
            } else if(ds_cpf_cnpj.length > 14 && ds_cpf_cnpj.length < 18 ){

                /*$("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                    $("#alert_cnpj").slideUp(500);
                });
                $('#ds_cpf_cnpj').focus();
                return false;*/
            }
        }
    }

    if($('#ds_cep').val()==""){
        $("#alert_ds_cep").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_cep").slideUp(500);
        });
        $('#ds_cep').focus();
        return false;
    }
    if($('#ds_endereco').val()==""){
        $("#alert_ds_endereco").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_endereco").slideUp(500);
        });
        $('#ds_endereco').focus();
        return false;
    }
    if($('#ds_numero').val()==""){
        $("#alert_ds_numero").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_numero").slideUp(500);
        });
        $('#ds_numero').focus();
        return false;
    }
    if($('#ds_bairro').val()==""){
        $("#alert_cidade_bairro").show();
        $("#alert_ds_bairro").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_bairro").slideUp(500);
        });
        $('#ds_bairro').focus();
        return false;
    }
    if($('#ds_cidade').val()==""){
        $("#alert_cidade_bairro").show();
        $("#alert_ds_cidade").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_cidade").slideUp(500);
        });
        $('#ds_cidade').focus();
        return false;
    }
    if($('#ds_uf').val()==""){
        $("#alert_ds_uf").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_uf").slideUp(500);
        });
        $('#ds_uf').focus();
        return false;
    }
    if($('#ic_cliente').val()==""){
        $("#alert_ic_cliente").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ic_cliente").slideUp(500);
        });
        $('#ic_cliente').focus();
        return false;
    }
    if($("#leads_pk").val()==""){
        if(fcVerificarCNPJ()){
            return false;
        }
    }


    fcEnviar();
}


function fcEnviar(){
    try {
        var v_pk = $("#leads_pk").val();
        var v_ds_lead = $("#ds_lead").val();
        var v_ds_endereco = $("#ds_endereco").val();
        var v_ds_numero = $("#ds_numero").val();
        var v_ds_complemento = $("#ds_complemento").val();
        var v_ds_cep = $("#ds_cep").val();
        var v_ds_bairro = $("#ds_bairro").val();
        var v_ds_cidade = $("#ds_cidade").val();
        var v_ds_uf = $("#ds_uf").val();
        var v_ic_cliente = $("#ic_cliente").val();
        var v_n_qtde_torres = $("#n_qtde_torres").val();
        var v_ds_obs = $("#ds_obs").val();
        var v_ds_razao_social = $("#ds_razao_social").val();
        var v_ds_cpf_cnpj = $("#ds_cpf_cnpj").val();
        var v_ds_ie = $("#ds_ie").val();
        var v_ds_tel_lead = $("#ds_tel_fixo").val();
        var v_ds_fax = $("#ds_tel_fixo1").val();
        var v_ds_site = $("#ds_site").val();
        var v_ds_email_lead = $("#ds_email_contato_receita").val();
        var v_supervisores_pk = $("#supervisores_pk").val();
        var v_supervisor1_pk = $("#supervisor1_pk").val();
        var v_supervisor2_pk = $("#supervisor2_pk").val();
        var v_responsavel_pk = $("#responsavel_pk").val();
        var v_segmentos_pk = $("#segmentos_pk").val();
        var v_dia_faturamento = $("#dia_faturamento").val();
        var v_leads_pai_pk = $("#leads_pai_pk").val();
        var v_ic_tipo_lead = $("#ic_tipo_lead").val();
        var v_ds_tipo_lead = $("#ds_tipo_lead").val();
        var v_ds_porte = $("#ds_porte").val();
        var t_dt_abertura = $("#dt_abertura").val();
        var v_ds_atividade_principal_receita = $("#ds_atividade_principal_receita").val();
        var v_ds_atividade_secundaria_receita = $("#ds_atividade_secundaria_receita").val();
        var v_ds_socio1 = $("#ds_socio1").val();
        var v_ds_socio2 = $("#ds_socio2").val();
        var v_ds_socio3 = $("#ds_socio3").val();
        var ic_iss_retido_tomador="";
        var selected_ic_iss_retido_tomador = document.querySelector('input[name="ic_iss_retido_tomador"]:checked');
        if(selected_ic_iss_retido_tomador!=null){
            ic_iss_retido_tomador = selected_ic_iss_retido_tomador.value;
        }        
        var objParametros = {
            "pk":v_pk,
            "ds_lead": (v_ds_lead),
            "ds_endereco": (v_ds_endereco),
            "ds_numero": (v_ds_numero),
            "ds_complemento": (v_ds_complemento),
            "ds_cep": (v_ds_cep),
            "ds_bairro": (v_ds_bairro),
            "ds_cidade": (v_ds_cidade),
            "ds_uf": (v_ds_uf),
            "ic_cliente": (v_ic_cliente),
            "n_qtde_torres": (v_n_qtde_torres),
            "ds_obs": (v_ds_obs),
            "ds_razao_social": (v_ds_razao_social),
            "ds_cpf_cnpj": (v_ds_cpf_cnpj),
            "ds_ie": (v_ds_ie),
            "ds_tel": (v_ds_tel_lead),
            "ds_fax": (v_ds_fax),
            "ds_site": (v_ds_site),
            "leads_pai_pk": (v_leads_pai_pk),
            "ic_tipo_lead": (v_ic_tipo_lead),
            "supervisores_pk": (v_supervisores_pk),
            "supervisor1_pk": (v_supervisor1_pk),
            "supervisor2_pk": (v_supervisor2_pk),
            "responsavel_pk": (v_responsavel_pk),
            "ds_email": (v_ds_email_lead),
            "segmentos_pk": (v_segmentos_pk),
            "dia_faturamento": (v_dia_faturamento),
            "ic_iss_retido_tomador": ic_iss_retido_tomador,
            "ic_inss_aplicacao": $("#ic_inss_aplicacao").val(),
            "ds_tipo": (v_ds_tipo_lead),
            "ds_porte": (v_ds_porte),
            "dt_abertura": (t_dt_abertura),
            "ds_atividade_principal": (v_ds_atividade_principal_receita),
            "ds_atividade_secundaria": (v_ds_atividade_secundaria_receita),
            "ds_socio1": (v_ds_socio1),
            "ds_socio2": (v_ds_socio2),
            "ds_socio3": (v_ds_socio3),
        };
        var arrEnviar = carregarController("lead", "salvar", objParametros);
        //NewWindow(v_last_url)
        if (arrEnviar.status == true){
            // Reload datable
            utilsJS.toastNotify(true, arrEnviar.message);
            if($("#ic_processo_comercial").val() != 1){
                var objParametros = {
                    "ic_abertura":1,
                    "pk":$("#leads_pk").val(),
                    "local":$("#local").val()
                };
                sendPost('lead','leadMainPainel' ,objParametros);
            }else{
                //sendPost("comercial_painel_res_form.php", {token: token, ic_abertura: 2});
            }
        }
        else{

            utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
        }
    } catch (error) {

        utilsJS.toastNotify(false, error);
    }

}
function fcCarregarSupervisor(){

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("usuario", "listarSupervisor", objParametros);
    carregarComboAjax($("#supervisores_pk"), arrCarregar, " ", "pk", "ds_usuario");
    carregarComboAjax($("#supervisor1_pk"), arrCarregar, " ", "pk", "ds_usuario");
    carregarComboAjax($("#supervisor2_pk"), arrCarregar, " ", "pk", "ds_usuario");

}
function fcCarregarResponsavel(){
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("usuario", "listarTodos", objParametros);
    carregarComboAjax($("#responsavel_pk"), arrCarregar, " ", "pk", "ds_usuario");
}
function fcVerificarCNPJ(){
    var ds_cpf_cnpj = $("#ds_cpf_cnpj").val();
    if(ds_cpf_cnpj.length == 14 || ds_cpf_cnpj.length == 18){
        var objParametros = {
            "ds_cpf_cnpj": $("#ds_cpf_cnpj").val()
        };

        var arrCarregar = carregarController("lead", "verificarCNPJ", objParametros);

        if (arrCarregar.status == true){

            if(arrCarregar.data.length > 0){

                sweetMensagem('warning', "Já existe um Lead com esse CNPJ");
                $("#ds_lead").val("");
                $("#ds_cpf_cnpj").val("");
                $("#ds_cidade").val("");
                $("#ds_endereco").val("");
                $("#ds_bairro").val("");
                $("#ds_uf").val("");

                return false;

            }
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
    return true;

}


function fcCarregarLeadPai(){
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("lead", "listarLeadPai", objParametros);

    carregarComboAjax($("#leads_pai_pk"), arrCarregar, " ", "pk", "ds_lead");
}


function fcCarregar(){
        var objParametros = {
            "pk": $("#leads_pk").val()
        };

        colaborador_pk = "";
        var arrCarregar = carregarController("lead", "listarPk", objParametros);

        if (arrCarregar.status == true){
            $("#ds_lead").val(arrCarregar.data[0]['ds_lead']);
            $("#ds_endereco").val(arrCarregar.data[0]['ds_endereco']);
            $("#ds_numero").val(arrCarregar.data[0]['ds_numero']);
            $("#ds_complemento").val(arrCarregar.data[0]['ds_complemento']);
            $("#ds_cep").val(arrCarregar.data[0]['ds_cep']);
            $("#ds_bairro").val(arrCarregar.data[0]['ds_bairro']);
            $("#ds_cidade").val(arrCarregar.data[0]['ds_cidade']);
            $("#ds_uf").val(arrCarregar.data[0]['ds_uf']);
            $("#ic_cliente").val(arrCarregar.data[0]['ic_cliente']);
            $("#n_qtde_torres").val(arrCarregar.data[0]['n_qtde_torres']);
            $("#ds_obs").val(arrCarregar.data[0]['ds_obs']);
            $("#ds_razao_social").val(arrCarregar.data[0]['ds_razao_social']);
            $("#ds_cpf_cnpj").val(arrCarregar.data[0]['ds_cpf_cnpj']);
            $("#ds_ie").val(arrCarregar.data[0]['ds_ie']);
            $("#ds_tel_fixo").val(arrCarregar.data[0]['ds_tel']);
            $("#ds_tel_fixo1").val(arrCarregar.data[0]['ds_fax']);
            $("#ds_site").val(arrCarregar.data[0]['ds_site']);
            $("#ds_email_contato_receita").val(arrCarregar.data[0]['ds_email']);
            $("#supervisores_pk").val(arrCarregar.data[0]['supervisores_pk']);
            $("#supervisor1_pk").val(arrCarregar.data[0]['supervisor1_pk']);
            $("#supervisor2_pk").val(arrCarregar.data[0]['supervisor2_pk']);
            $("#responsavel_pk").val(arrCarregar.data[0]['responsavel_pk']);
            $("#segmentos_pk").val(arrCarregar.data[0]['segmentos_pk']);
            $("#dia_faturamento").val(arrCarregar.data[0]['dia_faturamento']);
            $("#ic_inss_aplicacao").val(arrCarregar.data[0]['ic_inss_aplicacao']);
            $('input[name="ic_iss_retido_tomador"][value="' + arrCarregar.data[0]['ic_iss_retido_tomador'] + '"]').prop('checked', true);
            $("#leads_pai_pk").val(arrCarregar.data[0]['leads_pai_pk']);
            $("#ic_tipo_lead").val(arrCarregar.data[0]['ic_tipo_lead']);
            $("#ds_tipo_lead").val(arrCarregar.data[0]['ds_tipo']);
            $("#ds_porte").val(arrCarregar.data[0]['ds_porte']);
            $("#dt_abertura").val(arrCarregar.data[0]['dt_abertura']);
            $("#ds_atividade_principal_receita").val(arrCarregar.data[0]['ds_atividade_principal']);
            $("#ds_atividade_secundaria_receita").val(arrCarregar.data[0]['ds_atividade_secundaria']);
            $("#ds_socio1").val(arrCarregar.data[0]['ds_socio1']);
            $("#ds_socio2").val(arrCarregar.data[0]['ds_socio2']);
            $("#ds_socio3").val(arrCarregar.data[0]['ds_socio3']);
            $("#ds_lead_titulo").html("<b>"+arrCarregar.data[0]['ds_lead']+"</b>");
            $("#id_lead").html("Cód Lead: "+arrCarregar.data[0]['pk']);
            $("#dt_cadastro_lead").html("Dt de Cad: "+arrCarregar.data[0]['dt_cadastro']);
            $("#dt_ult_atualizacao_lead").html("Dt Utl atualização: "+arrCarregar.data[0]['dt_ult_atualizacao']);
            $("#ds_usuario_cadastro").html("Usuário de Cad: "+arrCarregar.data[0]['ds_usuario_cadastro']);
            $("#exibir_dados_cadastrado").show();
            $("#exibir_linha_dados_cadastrado").show();
            if(arrCarregar.data[0]['ic_tipo_lead']==1){
                $("#lead_pai").hide();
            }
            else if(arrCarregar.data[0]['ic_tipo_lead']==2){
                $("#lead_pai").show();
            }
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }

}

function fcCancelar(){
    var objParametros = {
        "local":$("#local").val()
    };
    sendPost('lead','receptivo' ,objParametros);
}
$(document).ready(function(){

    var arrCarregar = permissao("lead", "ins");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false, 'Você não tem permissão para acessar essa pagina!');
        setTimeout(function() {
            sendPost('menu','principal',{})
        }, 2000);
        return false;
    }

    $(document).on('click', '#cmdEnviarLead', fcValidarForm);
    $(document).on('click', '#cmdEnviarLead1', fcValidarForm);
    $(document).on('click', '#cmdEnviarLead2', fcValidarForm);
    $(document).on('click', '#cmdVoltarLead2', fcCancelar);
    $(document).on('click', '#cmdVoltarLead', fcCancelar);

    colaborador_pk ="";
    $("#exibir_material").hide();
    //Atribui os eventos - Leads




    $('#dt_abertura').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_abertura").keypress(function(){
        mascara(this,mdata);
    });

    $("#ds_cep").keypress(function(){
        mascara(this,cep);
    });
    $("#n_qtde_torres").keypress(function(){
        mascara(this,soNumeros);
    });
    $("#ds_cpf_cnpj").keypress(function(){
        chama_mascara(this);
    });

    $("#ds_tel_fixo").keypress(function(){
        mascara(this,mascaraTelefone);
    });
    $("#ds_tel_fixo1").keypress(function(){
        mascara(this,mascaraTelefone);
    });
    $("#ds_ie").keypress(function(){
        mascara(this,soNumeros);
    });
    //Carrega os dados cadastrais do lead
    fcCarregarSupervisor();

    fcCarregarResponsavel();

    $("#lead_pai").hide();
    fcCarregarLeadPai();
    $(".chzn-select").chosen({allow_single_deselect: true});

    fcCarregar();
    $("#leads_pai_pk").select2();




    if($("#ic_cliente").val()==""){
        $("#ic_cliente").val(2);
    }
    $("#ds_cep").change(function(){
        fcCarregarCep($("#ds_cep").val());
    });
    $("#ds_cpf_cnpj").change(function(){
        fcVerificarCNPJ();
    });

    $("#ic_tipo_lead").change(function(){
        if($("#ic_tipo_lead").val()==1){
            $("#lead_pai").hide();
            $("#lead_pai_pk").val("");
        }
        else if($("#ic_tipo_lead").val()==2){
            $(".chzn-select").chosen('destroy');
            $("#lead_pai").show();
            $(".chzn-select").chosen({allow_single_deselect: true});
        }
    });
    $(".chzn-select").chosen({width: "200%"});

    //Carrega os dados do campo de Cargo na tela modal dos contatos
    fcCarregarCargo();
});
