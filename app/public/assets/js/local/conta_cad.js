function fcMascaraFormConta(){
    $("#ds_cpf_cnpj").keypress(function(){
        chama_mascara(this);
    });    

    $("#ds_tel_fixo").keypress(function(){
        mascara(this, mascaraTelefone);
    });

    $("#ds_tel_fixo1").keypress(function(){
        mascara(this, mascaraTelefone);
    });

    $("#ds_cep").keypress(function(){
        mascara(this,cep);
    });

    $("#ds_cep").change(function(){
        fcCarregarCep($("#ds_cep").val());
    });

    $('#dt_cancelamento').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $('#dt_ativacao').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

}

function fcValidarForm(){
    $("#form").validate({
        rules :{
            ds_tipo_pessoa:{
                required:true
            },
            ds_conta:{
                required:true
            },
            ds_cep:{
                required:true
            },
            ds_endereco:{
                required:true
            },
            ds_numero:{
                required:true
            },
            ds_bairro:{
                required:true
            },
            ds_cidade:{
                required:true
            },
            ds_uf:{
                required:true
            },
            tipo_conta_pk:{
                required:true
            },
            ic_status_nota:{
                required:true
            },
        },
        messages:{
            ds_tipo_pessoa:{
                required:"Por favor, informe o Tipo Pessoa"    
            },
            ds_conta:{
                required:"Por favor, informe o Nome da Conta"                
            },
            ds_cep:{
                required:"Por favor, informe o Cep ",
                minlength:"Por favor, informe um Cep valido"
            },
            ds_endereco:{
                required:"Por favor, informe o Endereço "
            },
            ds_numero:{
                required:"Por favor, informe o Número"
            },
            ds_bairro:{
                required:"Por favor, informe o Bairro "
            },
            ds_cidade:{
                required:"Por favor, informe a Cidade "
            },
            ds_uf:{
                required:"Por favor, informe o UF "
            },
            tipo_conta_pk:{
                required:"Por favor, informe o Tipo Conta"
            },
            ic_status_nota:{
                required:"Por favor, informe o Status Nota Fiscal"
            }

        },
        submitHandler: function(form){
            if(fcVerificarConta() != 0){
                fcEnviarConta();
            }		   //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });
}

function fcVerificarConta(){
    try {
        var v_tipo_conta_pk = $("#tipo_conta_pk").val();

        if(v_tipo_conta_pk == 1){
            let pk = $('#pk').val()
            var objParametros = {};
            var arrTipoConta = carregarController("conta", "verificarConta", objParametros);
            
            for(i=0; i < arrTipoConta.data.length; i++){
                if(arrTipoConta.data[i]['tipo_conta_pk'] == 1 && pk != arrTipoConta.data[i]['pk']){
                    utilsJS.toastNotify(true, "Apenas uma conta pode ser Principal");
                    return 0;
                    break;
                }
            }
        }
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
}

function fcEnviarConta(){
    try {

        var v_pk = $('#pk').val()
        var v_ds_tipo_pessoa = $("#ds_tipo_pessoa").val();
        var v_ds_conta = $("#ds_conta").val();
        var v_ds_razao_social = $("#ds_razao_social").val();
        var v_ds_cpf_cnpj = $("#ds_cpf_cnpj").val();
        var v_ds_cnae = $("#ds_cnae").val();
        var v_ds_rg = $("#ds_rg").val();
        var v_ds_tel_fixo = $("#ds_tel_fixo").val();
        var v_ds_email_contato_receita = $("#ds_email_contato_receita").val();
        var v_ds_tel_fixo1 = $("#ds_tel_fixo1").val();
        var v_ds_cep = $("#ds_cep").val();
        var v_ds_endereco = $("#ds_endereco").val();
        var v_ds_numero = $("#ds_numero").val();
        var v_ds_complemento = $("#ds_complemento").val();
        var v_ds_bairro = $("#ds_bairro").val();
        var v_ds_cidade = $("#ds_cidade").val();
        var v_ds_uf = $("#ds_uf").val();
        var v_dt_ativacao = $("#dt_ativacao").val();
        var v_dt_cancelamento = $("#dt_cancelamento").val();
        var v_ic_status = $("#ic_status").val();    
        var v_id_cliente = $("#id_cliente").val();  
        var v_ds_img_cliente = $("#ds_img_cliente").val();
        var v_tipo_conta_pk = $("#tipo_conta_pk").val();
        var v_ic_preencher_folha = $("#ic_preencher_folha").val();
        var v_ic_teto_gastos = $("#ic_teto_gastos").val();
        var v_ic_analise_financeira = $("#ic_analise_financeira").val();
        var v_ic_faturamento = $("#ic_faturamento").val();
        var v_ic_nf_gerar = $("#ic_nf_gerar").val();
        var v_ic_boleto = $("#ic_boleto").val();
        var v_ds_dominio = $("#ds_dominio").val();
        var v_ds_cei = $("#ds_cei").val();


        var objParametros = {
            "pk": (v_pk),
            "ds_tipo_pessoa": (v_ds_tipo_pessoa),
            "ds_conta": (v_ds_conta),
            "ds_razao_social": (v_ds_razao_social),
            "ds_cpf_cnpj": (v_ds_cpf_cnpj),
            "ds_cnae": (v_ds_cnae),
            "ds_rg": (v_ds_rg),
            "ds_tel": (v_ds_tel_fixo),
            "ds_email": (v_ds_email_contato_receita),
            "ds_cel": (v_ds_tel_fixo1),
            "ds_cep": (v_ds_cep),
            "ds_endereco": (v_ds_endereco),
            "ds_numero": (v_ds_numero),
            "ds_complemento": (v_ds_complemento),
            "ds_bairro": (v_ds_bairro),
            "ds_cidade": (v_ds_cidade),
            "ds_uf": (v_ds_uf),
            "dt_ativacao": (v_dt_ativacao),
            "dt_cancelamento": (v_dt_cancelamento),
            "id_cliente": (v_id_cliente),
            "ic_status": (v_ic_status),
            "ds_img_cliente": (v_ds_img_cliente),
            "tipo_conta_pk": (v_tipo_conta_pk), 
            "ic_preencher_folha": (v_ic_preencher_folha),
            "ic_teto_gastos": (v_ic_teto_gastos),
            "ic_analise_financeira": (v_ic_analise_financeira),
            "ic_faturamento": (v_ic_faturamento),
            "ic_nf_gerar": (v_ic_nf_gerar),
            "ic_boleto": (v_ic_boleto),
            "ds_dominio": (v_ds_dominio),
            "ds_cei":v_ds_cei
            
        };    
        var arrEnviar = carregarController("conta", "salvar", objParametros);

        if (arrEnviar.status == true){
            // Reload datable
            utilsJS.toastNotify(true, arrEnviar.message);
            //setTimeout(function(){
                sendPost('conta', 'receptivo', '');
            //}, 1000);

        }
        else{
            utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
        }
    } catch (error) {
        console.log(error)
    }

}

function fcCancelar(){
    sendPost('conta', 'receptivo', '');
}

function fcCarregarConta(){
    try {
        let pk = $('#pk').val()
        if(pk > 0){
    
            var objParametros = {
                "pk": pk
            };        
            
            var arrCarregar = carregarController("conta", "listarPk", objParametros);
           // NewWindow(v_last_url)
            
            if (arrCarregar.status == true){


                $("#ds_tipo_pessoa").val(arrCarregar.data[0]['ds_tipo_pessoa']);
                $("#ds_conta").val(arrCarregar.data[0]['ds_conta']);
                $("#ds_razao_social").val(arrCarregar.data[0]['ds_razao_social']);
                $("#ds_cpf_cnpj").val(arrCarregar.data[0]['ds_cpf_cnpj']);
                $("#ds_cnae").val(arrCarregar.data[0]['ds_cnae']);
                $("#ds_rg").val(arrCarregar.data[0]['ds_rg']);
                $("#ds_tel_fixo").val(arrCarregar.data[0]['ds_tel']);
                $("#ds_email_contato_receita").val(arrCarregar.data[0]['ds_email']);
                $("#ds_tel_fixo1").val(arrCarregar.data[0]['ds_cel']);
                $("#ds_cep").val(arrCarregar.data[0]['ds_cep']);
                $("#ds_endereco").val(arrCarregar.data[0]['ds_endereco']);
                $("#ds_numero").val(arrCarregar.data[0]['ds_numero']);
                $("#ds_complemento").val(arrCarregar.data[0]['ds_complemento']);
                $("#ds_bairro").val(arrCarregar.data[0]['ds_bairro']);
                $("#ds_cidade").val(arrCarregar.data[0]['ds_cidade']);
                $("#ds_uf").val(arrCarregar.data[0]['ds_uf']);
                $("#segmentos_pk").val(arrCarregar.data[0]['segmentos_pk']);
                $("#dt_ativacao").val(arrCarregar.data[0]['dt_ativacao']);
                $("#dt_cancelamento").val(arrCarregar.data[0]['dt_cancelamento']);
                $("#ic_status").val(arrCarregar.data[0]['ic_status']);
                $("#id_cliente").val(arrCarregar.data[0]['id_cliente']);
                $("#ds_img_cliente").val(arrCarregar.data[0]['ds_img_cliente']);
                $("#tipo_conta_pk").val(arrCarregar.data[0]['tipo_conta_pk']);
                $("#ds_dominio").val(arrCarregar.data[0]['ds_dominio']);
                $("#ic_preencher_folha").val(arrCarregar.data[0]['ic_preencher_folha']==0?'2':arrCarregar.data[0]['ic_preencher_folha']);
                $("#ic_teto_gastos").val(arrCarregar.data[0]['ic_teto_gastos']==null?'2':arrCarregar.data[0]['ic_teto_gastos']);
                $("#ic_analise_financeira").val(arrCarregar.data[0]['ic_analise_financeira']==null?'2':arrCarregar.data[0]['ic_analise_financeira']);
                $("#ic_faturamento").val(arrCarregar.data[0]['ic_faturamento']==null?'2':arrCarregar.data[0]['ic_faturamento']);
                $("#ic_nf_gerar").val(arrCarregar.data[0]['ic_nf_gerar']==null?'2':arrCarregar.data[0]['ic_nf_gerar']);
                $("#ic_boleto").val(arrCarregar.data[0]['ic_boleto']==null?'2':arrCarregar.data[0]['ic_boleto']);
                $("#contas_config_notas_pk").val(arrCarregar.data[0]['contas_config_notas_pk']==null?'':arrCarregar.data[0]['contas_config_notas_pk']);
                $("#ds_inscricao_estatual").val(arrCarregar.data[0]['ds_inscricao_estatual']==null?'':arrCarregar.data[0]['ds_inscricao_estatual']);
                $("#ic_regime_tributacao").val(arrCarregar.data[0]['ic_regime_tributacao']==null?'':arrCarregar.data[0]['ic_regime_tributacao']);
                $("#ic_regime_tributacao_especial").val(arrCarregar.data[0]['ic_regime_tributacao_especial']==null?'':arrCarregar.data[0]['ic_regime_tributacao_especial']);
                $("#ic_incentivo_cultural").val(arrCarregar.data[0]['ic_incentivo_cultural']==null?'':$("#ic_incentivo_cultural").attr('checked', true));
                $("#ic_incentivo_fiscal").val(arrCarregar.data[0]['ic_incentivo_fiscal']==null?'':$("#ic_incentivo_fiscal").attr('checked', true));
                $("#ds_ddd_nota").val(arrCarregar.data[0]['ds_ddd']==null?'':arrCarregar.data[0]['ds_ddd']);
                $("#ds_tel_nota").val(arrCarregar.data[0]['ds_tel']==null?'':arrCarregar.data[0]['ds_tel']);
                $("#ds_email_nota").val(arrCarregar.data[0]['ds_email']==null?'':arrCarregar.data[0]['ds_email']);
                $("#ds_nome_arquivo_certificado").val(arrCarregar.data[0]['ds_nome_arquivo_certificado']==null?'':arrCarregar.data[0]['ds_nome_arquivo_certificado']);
                $("#ds_link_arquivo_certificado").val(arrCarregar.data[0]['ds_link_arquivo_certificado']==null?'':arrCarregar.data[0]['ds_link_arquivo_certificado']);
                $("#ds_nome_certificado").val(arrCarregar.data[0]['ds_nome_certificado']==null?'':arrCarregar.data[0]['ds_nome_certificado']);
                $("#ds_id_nota").val(arrCarregar.data[0]['ds_id']==null?'':arrCarregar.data[0]['ds_id']);
                $("#dt_criacao_certificado").val(arrCarregar.data[0]['dt_criacao_certificado']==null?'':arrCarregar.data[0]['dt_criacao_certificado']);
                $("#dt_vencimento_certificado").val(arrCarregar.data[0]['dt_vencimento_certificado']==null?'':arrCarregar.data[0]['dt_vencimento_certificado']);
                $("#ds_ult_numero_nota").val(arrCarregar.data[0]['ds_ult_numero_nota']==null?'':arrCarregar.data[0]['ds_ult_numero_nota']);
                $("#ds_serie_nota").val(arrCarregar.data[0]['ds_serie_nota']==null?'':arrCarregar.data[0]['ds_serie_nota']);
                $("#ds_obs_nota").val(arrCarregar.data[0]['ds_obs']==null?'':arrCarregar.data[0]['ds_obs']);
                $("#ic_status_nota").val(arrCarregar.data[0]['ic_status_config']==null?'':arrCarregar.data[0]['ic_status_config']);
                $("#ds_cei").val(arrCarregar.data[0]['ds_cei']==null?'':arrCarregar.data[0]['ds_cei']);
    
            }
            else{
                utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
            }
        }
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
   
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

            }
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}

//Notas

function fcMascarasNotaFiscal(){
    $("#ds_tel_nota").keypress(function(){
        mascara(this, mascaraTelefone);
    });
    $('#dt_criacao_certificado').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_criacao_certificado").keypress(function(){
        mascara(this, mdata);
    });
    $('#dt_vencimento_certificado').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_vencimento_certificado").keypress(function(){
        mascara(this, mdata);
    });
}

$(document).ready(function(){
    //Dados Cadastrais
        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregarConta();

        //mascaras
        fcMascaraFormConta();

        //Atribui a validação do formulário dos campos obrigatórios
        fcValidarForm();  

        //Atribui os eventos
        $(document).on('click', '#cmdVoltarConta', fcCancelar);
        $(document).on('click', '#cmdEnviarConta', fcValidarForm);

        $("#cmdConsultarCNPJ").click(function(){
            fcVerificarCNPJ($("#ds_cpf_cnpj").val());
        });

    //Notas 
        //mascaras
        fcMascarasNotaFiscal();

        //Atribuir eventos 
        $(document).on('click', '#cmdEnviarConta', fcValidarForm);
});

