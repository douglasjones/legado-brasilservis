var tbParcelas = "";
var tblDocumentosLancamento = "";
var tblHistoricoParcial = "";
function fcAbrirCadastroLancamento(pk) {
    try {
    
    fclimpaFormLancamento();
   
    $('#lancamento_pk').val(pk)

    //Permissões
    $("#label_lancamento").html('Lançar Para:');
    
    $("#exibir_tbl_parcial").hide()

    $("#ic_status_lancamento").change(function(){
       
        if ($(this).val() == 1) {
            $("#exibir_dt").show();
            $("#exibir_vl_parcial").hide();
            $("#dt_pagamento").val("");
        } else if($("#ic_status_lancamento").val() == 6){
            $("#exibir_dt").show();
            $("#exibir_vl_parcial").show();
        }else{
            $("#exibir_dt").hide();
            $("#exibir_vl_parcial").hide();
            $("#dt_pagamento").val("");
        }
    });



    var arrCarregar = permissao("lancamento_empresa", "cons");

    if (arrCarregar.status != true){
        $("#div_lancar_empresa").hide();
        return false;
    }

    
    var arrCarregar1 = permissao("lancamento_contabancaria", "cons");
    if (arrCarregar1.status != true){
        $("#div_conta_bancarias").hide();
        return false;
    }

    //Combos
    $("#tipo_lancamento_pk").off('change').on('change', function () {
        fcGerenciaLabel();
    });

    fcCarregarTipoPlanoNegocio();


    //Função de parcelas
    fcQtdeParcelas();

    $("#qtde_parcelas_pk").off('change').on('change', function () {
        fcArrayDatasVlPagamento();
    });

    $("#div_cliente_lancamento_pk").hide();
    $("#mostrar_btn_add_fornecedor").hide();
    $("#exibir_input_fornecedor").hide();
    $("#exibir_combo_fornecedor").show();

    $("#tipo_grupo_pk").off('change').on('change', function () {
       
        fcSelecionaGrupo();
    });
    $("#btn_add_fornecedor").click(function () {
        $("#exibir_input_fornecedor").show();
        $("#exibir_combo_fornecedor").hide();
    });

    
    carregarComboAjax($("#metodos_pagamento_pk"), arrMetodoPagamento, " ", "pk", "ds_metodo_pagamento");

    
    $("#vl_parcial").on('keypress', function () {
        mascara(this,moeda);
    });

    $("#exibir_dt").hide();
    $("#exibir_vl_parcial").hide();
    
    //Esta função deixar show apenas para o cliente ECol
    $("#divExibirCentroCustol").hide();

    var arrCarregar = permissao("lancar_dt_atual_retroativa", "ins");
    if (arrCarregar.status == true){
        $('#dt_faturamento1').datepicker({
            //startDate: "+0d",
            defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: false,
            todayBtn: "linked",
            minDate: new Date()
        });

        $("#dt_faturamento1").keypress(function () {
            mascara(this, mdata);
        });

        $('#dt_vencimento1').datepicker({
            //startDate: "+0d",
            defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: false,
            todayBtn: "linked",
            minDate: new Date()
        }).datepicker();

        $("#dt_vencimento1").keypress(function () {
            mascara(this, mdata);
        });

        $('#dt_pagamento').datepicker({
            //startDate: "+0d",
            defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: false,
            todayBtn: "linked",
            minDate: new Date()
        }).datepicker();

        $("#dt_pagamento").keypress(function () {
            mascara(this, mdata);
        });
    }else{
        $('#dt_faturamento1').datepicker({
            startDate: "",
            defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: false,
            todayBtn: "linked",
            minDate: new Date()
        });

        $("#dt_faturamento1").keypress(function () {
            mascara(this, mdata);
        });

        $('#dt_vencimento1').datepicker({
            startDate: "+4d",
            defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: false,
            todayBtn: "linked",
            minDate: new Date()
        }).datepicker();

        $("#dt_vencimento1").keypress(function () {
            mascara(this, mdata);
        });

        $('#dt_pagamento').datepicker({
            startDate: "",
            defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: false,
            todayBtn: "linked",
            minDate: new Date()
        }).datepicker();

        $("#dt_pagamento").keypress(function () {
            mascara(this, mdata);
        });
    }

    $("#form_cad_nfse").hide();
    $("#listar_combo_nfse").hide();

    

    $("#vl_lancamento1").keypress(function () {
        mascara(this, moeda);
    });


    carregarComboAjax($("#empresa_lancamento_pk"), arrConta, " ", "pk", "ds_conta");

    $("#tipos_operacao_pk").off('change').on('change', function () {
        $(".chzn-select").chosen('destroy');
        
        fcCarregarCategoriaOperacao();
        $(".chzn-select").chosen({ allow_single_deselect: true });
    });

    $("#empresa_lancamento_pk").off('change').on('change', function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarContasBancariasLancamento();
        $(".chzn-select").chosen({ allow_single_deselect: true });
    });

    tblDocumentosLancamento.clear().destroy();
    fcCarregarGridArquivos();

    $('#fileupload').off('change').on('change', function() {
        // On change event
        if($(this).prop('files').length > 0) {
            var formdata = new FormData();
            var files = $(this).prop('files');

            $.each(files, function (index, file) {
                formdata.append(index, file);
                $("#ds_nome_original").val(file.name);

            });
            // Move a chamada de fcSalvarDocumentos para fora do loop
            fcSalvarDocumentos(formdata);

            fcAlterarNomeArquivo($("#ds_nome_original").val());
            fcIncluirLinhaArquivo($("#ds_nome_original").val());
        }
    });
    /*tblHistoricoParcial.clear().destroy();*/
    fcCarregarTblHistoricoParcial();
    if(pk!=""){
        fcCarregarFormLancamento();
    }
    
    /*$("#tipo_lancamento_pk").select2();
    $("#categorias_financeiras_pk").select2();
    $("#empresa_lancamento_pk").select2();*/
    
    if(pk==""){
        $("#grupo_lancamento_pk").val("").trigger("change.select2");
        $("#grupo_lancamento_pk").val("");
        //$("#grupo_lancamento_pk").select2();

        $("#tipos_operacao_pk").val("").trigger("change.select2");
        $("#tipos_operacao_pk").val("");
        //$("#tipos_operacao_pk").select2();

        $("#cliente_lancamento_pk").val("").trigger("change.select2");
        $("#cliente_lancamento_pk").val("");
        //$("#cliente_lancamento_pk").select2();

        $("#posto_trabalho_lancamento_pk").val("").trigger("change.select2");
        $("#posto_trabalho_lancamento_pk").val("");
        //$("#posto_trabalho_lancamento_pk").select2();

        $("#contratos_pk").val("").trigger("change.select2");
        $("#contratos_pk").val("");
        //$("#contratos_pk").select2();

        $("#contas_bancarias_pk").val("").trigger("change.select2");
        $("#contas_bancarias_pk").val("");
        //$("#contas_bancarias_pk").select2();

        
    }


    fcCarregarContaPrincipal();
    /*listarPrestador();
    listaDiscriminacaoServico();
    $("#prestador_pk").change(function () {
        
        fcCarregarListarServicos();
    });*/
    $('#event-modal').modal("show");


    setTimeout(function() {
        $(".chzn-select").chosen('destroy');
        $(".chzn-select").chosen({ allow_single_deselect: true });
    }, 1500);
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
    
}
function fcCarregarContaPrincipal(){
    
    var arrCarregar = carregarController("conta", "verificaContaPrincipal",  '');
    $('#contas_lead_pk').val(arrCarregar.data[0]['pk'])
}

function listarPrestador(){
    
    var objParametros = {
        'contas_origem_pk': $('#contas_lead_pk').val()
    };        
    var arrCarregar = carregarController("certificados_empresas", "contaConfigListarEmpresas", objParametros);
    carregarComboAjax($("#prestador_pk"), arrCarregar, " ", "pk", "razaoSocial");
}

// NFSE

function fcCarregarDadosEmpresa(){

    $("#cnpj").val("");
    $("#cep").val("");
    $("#ds_uf").val("");
    $("#ds_cidade").val("");
    $("#ds_bairro").val("");
    $("#ds_endereco").val("");
    $("#numero").val("");
    $("#complemento").val("");
    if($("#grupo_lancamento_pk").val()!=""){
        var objParametros = {
            'pk': $("#grupo_lancamento_pk").val()
        };
        var arrCarregar = carregarController("lead", "listarPk", objParametros);
        $("#cnpj").val(arrCarregar.data[0]['ds_cpf_cnpj']);
        $("#cep").val(arrCarregar.data[0]['ds_cep']);
        $("#ds_uf").val(arrCarregar.data[0]['ds_uf']);
        
        $("#razaoSocialParaEnviarNaNota").val(arrCarregar.data[0]['pk']);
        $("#ds_cidade").val(arrCarregar.data[0]['ds_cidade']);
        $("#ds_bairro").val(arrCarregar.data[0]['ds_bairro']);
        $("#ds_endereco").val(arrCarregar.data[0]['ds_endereco']);
        $("#numero").val(arrCarregar.data[0]['ds_numero']);
        $("#complemento").val(arrCarregar.data[0]['ds_complemento']);
        $("#email").val(arrCarregar.data[0]['ds_email']);
    
        fcCarregarCep(arrCarregar.data[0]['ds_cep']);
    }
   
    
    
}
function fcListarComboNfse(){
    $("#form_cad_nfse").hide();
    $("#listar_combo_nfse").hide();

    
    //let exibir_nota_fiscal = permissao("exibir_nota_fiscal", "ins");
    //let nota_fiscal = carregarController("conta", "configModulo", '');

    let exibir_nota_fiscal = true;
    let nota_fiscal ='1';
    if(nota_fiscal == '1' 
        && $("#tipo_lancamento_pk").val() == '1' 
        && $("#tipo_grupo_pk").val() == '1'
        && $("#qtde_parcelas_pk").val() == 1
        && $("#vl_lancamento1").val() != ''
        && $("#dt_vencimento1").val() != ''
        && $("#empresa_lancamento_pk").val() != ''
        && $("#grupo_lancamento_pk").val() != ''
        && exibir_nota_fiscal == true
    ){
        
        /*var objParametros = {
            "pk": $("#empresa_lancamento_pk").val(),
        }
        var arrCarregar = carregarController("certificados_empresas", "contaConfigConsultaPk", objParametros)
        if(arrCarregar.data['ic_status'] == 1){*/
            $("#listar_combo_nfse").show();
            fcListarFormCadNfse();
        //}
        
    }
}

function fcListarFormCadNfse(){
    $("#form_cad_nfse").hide();
    if($("#ic_gerar_nfse").val() == 1){
        $("#servico_pk").val("");
        $("#codigo_servico_pk").val("");
        $("#ds_descricao_servico").val("");
        $("#vl_aliquota").val("");
        $("#iss_retido1").prop("checked", false);
        $("#iss_retido2").prop("checked", false);
        $("#iss_aliquota").val("");
        $("#iss_valor").val("");
        $("#inss_aliquota").val("");
        $("#inss_valor").val("");
        $("#pis_aliquota").val("");
        $("#pis_valor").val("");
        $("#cofins_aliquota").val("");
        $("#cofins_valor").val("");
        $("#ir_aliquota").val("");
        $("#ir_valor").val("");
        $("#csll_aliquota").val("");
        $("#csll_valor").val("");
        $("#descricao_nfse").val("");


        $("#form_cad_nfse").show();
        $("#cmdInformacoesSevicos").click(function(){
            fcInformacoesSevicos();
        })
        
        let valor_total = moeda2float($("#vl_lancamento1").val());
        let vl_iss = $('#iss_valor').val();
        let vl_inss = $('#inss_valor').val();
        let vl_pis = $('#pis_valor').val();
        let vl_cofins = $('#cofins_valor').val();
        let vl_ir = $('#ir_valor').val();
        let vl_csll = $('#csll_valor').val();
        let dtVencimento = $("#dt_vencimento1").val();
        let detalhamento_servico = "";


        $("#iss_valor").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#inss_valor").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#pis_valor").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#cofins_valor").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#ir_valor").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#csll_valor").keypress(function(){
            mascara(this,moeda);
        });

        $("#iss_aliquota").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#inss_aliquota").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#pis_aliquota").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#cofins_aliquota").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#ir_aliquota").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#csll_aliquota").keypress(function(){
            mascara(this,moeda);
        });
        
        $("#iss_aliquota").off('change').on('change', function () {
            vl_iss = moeda2float($("#vl_lancamento1").val()) * (moeda2float($("#iss_aliquota").val()) / 100) ;
            $("#iss_valor").val(float2moeda(vl_iss));
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#iss_valor").off('change').on('change', function () {
            vl_iss = $("#iss_valor").val();
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#inss_aliquota").off('change').on('change', function () {
            vl_inss = (moeda2float($("#vl_lancamento1").val()) * 0.7) * (moeda2float($("#inss_aliquota").val()) / 100);
            $("#inss_valor").val(float2moeda(vl_inss));
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#inss_valor").off('change').on('change', function () {
            vl_inss = $("#inss_valor").val();
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#pis_aliquota").off('change').on('change', function () {
            vl_pis = moeda2float($("#vl_lancamento1").val()) * (moeda2float($("#pis_aliquota").val()) / 100);
            $("#pis_valor").val(float2moeda(vl_pis));
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#pis_valor").off('change').on('change', function () {
            vl_pis = $("#pis_valor").val();
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#cofins_aliquota").off('change').on('change', function () {
            vl_cofins = moeda2float($("#vl_lancamento1").val()) * (moeda2float($("#cofins_aliquota").val()) / 100);
            $("#cofins_valor").val(float2moeda(vl_cofins));
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#cofins_valor").off('change').on('change', function () {
            vl_cofins = $("#cofins_valor").val();
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#ir_aliquota").off('change').on('change', function () {
            vl_ir = moeda2float($("#vl_lancamento1").val()) * (moeda2float($("#ir_aliquota").val()) / 100);
            $("#ir_valor").val(float2moeda(vl_ir));
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#ir_valor").off('change').on('change', function () {
            vl_ir = $("#ir_valor").val();
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#csll_aliquota").off('change').on('change', function () {
            vl_csll = moeda2float($("#vl_lancamento1").val()) * (moeda2float($("#csll_aliquota").val()) / 100);
            $("#csll_valor").val(float2moeda(vl_csll));
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#csll_valor").off('change').on('change', function () {
            vl_csll = $("#csll_valor").val();
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $("#cmdInformacoesSevicos").click(function(){
            fcInformacoesSevicos(contratos_pk);
            detalhamento_servico = "";//$("#ds_descricao_servico").val() 
            fcArrumarTextarea(  
                detalhamento_servico,
                dtVencimento,
                vl_iss,
                vl_inss,
                vl_pis,
                vl_cofins,
                vl_ir,
                vl_csll,
                valor_total
            )
        })
        
        $("#servico_pk").off('change').on('change', function () {
            var servico_pk = $("#servico_pk").val();
         
            strValor = servico_pk.split("-")

     
            $('#codigo_servico_pk').val(strValor[0]);
            fcInformacoesSevicos();
            detalhamento_servico = ""; $("#servico_pk option:selected").text();
            $("#descricaoServico").val(detalhamento_servico);
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        
        $('#dt_vencimento1').off('change').on('change', function () {
            dtVencimento = $(this).val(); 
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });
        $('#vl_lancamento1').off('change').on('change', function () {
            valor_total = moeda2float($("#vl_lancamento1").val())
            fcArrumarTextarea(  
                                detalhamento_servico,
                                dtVencimento,
                                vl_iss,
                                vl_inss,
                                vl_pis,
                                vl_cofins,
                                vl_ir,
                                vl_csll,
                                valor_total
                            );
        });

        $("#listaDiscriminacaoServico").off('change').on('change', function () {
            fcArrumarTextarea(
                detalhamento_servico,
                dtVencimento,
                vl_iss,
                vl_inss,
                vl_pis,
                vl_cofins,
                vl_ir,
                vl_csll,
                valor_total
            )
        })
    }
}


function fcArrumarTextarea(
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        ){

    $("#descricao_nfse").val(" ");

    let texto = "";
    if(detalhamento_servico != ''){
        //texto += $('#descricaoServico').val() + "\n\n\n";
        //texto += $('#descricaoServico').val() + "|||";
    }

    if($('#listaServicoConsulta').val() != ''){
        $("#listaServicoConsulta option:selected").text();
    }

	if($('#listaDiscriminacaoServico').val() != ''){
        texto += $("#listaDiscriminacaoServico option:selected").text() + "|||";
    }


    if(dtVencimento != '' && dtVencimento != '00/00/0000' && dtVencimento != 'undefined'){
        dt_vencimento = dtVencimento;
        dtVencimento = DataYMD(dtVencimento);
        data = new Date(dtVencimento);
        numeroMes = data.getMonth()+1;
        switch (numeroMes) {
            case 1:
                ds_mes = "JANEIRO";
                break;
            case 2:
                ds_mes = "FEVEREIRO";
                break;
            case 3:
                ds_mes = "MARÇO";
                break;
            case 4:
                ds_mes = "ABRIL";
                break;
            case 5:
                ds_mes = "MAIO";
                break;
            case 6:
                ds_mes = "JUNHO";
                break;
            case 7:
                ds_mes = "JULHO";
                break;
            case 8:
                ds_mes = "AGOSTO";
                break;
            case 9:
                ds_mes = "SETEMBRO";
                break;
            case 10:
                ds_mes = "OUTUBRO";
                break;
            case 11:
                ds_mes = "NOVEMBRO";
                break;
            case 12:
                ds_mes = "DEZEMBRO";
                break;
            default:
                ds_mes = "MÊS INVÁLIDO";
                break;
        }

        texto += "COMPETENCIA  " + ds_mes + "||";
        texto += "DATA DE VENCIMENTO " + dt_vencimento + "||";
    }

    texto += "VALOR DA NOTA FISCAL " + $("#vl_lancamento1").val() + "||";

    if(vl_iss != '' && vl_iss != '0,00'){
        texto += "ISS RETIDO R$ " + parseFloat(vl_iss).toFixed(2) + "||";
        valor_total = parseFloat(valor_total) - parseFloat(vl_iss);
    }

    if(vl_inss != '' && vl_inss != '0,00'){
        texto += "INSS RETIDO R$ " + parseFloat(vl_inss).toFixed(2) + "||";
        valor_total = parseFloat(valor_total) - parseFloat(vl_inss);
    }

    if(vl_pis != '' && vl_pis != '0,00'){
        texto += "PIS R$ " + parseFloat(vl_pis).toFixed(2) + "||";
        valor_total = parseFloat(valor_total) - parseFloat(vl_pis);
    }

    if(vl_cofins != '' && vl_cofins != '0,00'){
        texto += "COFINS R$ " + parseFloat(vl_cofins).toFixed(2) + "||";
        valor_total = parseFloat(valor_total) - parseFloat(vl_cofins);
    }

    if(vl_ir != '' && vl_ir != '0,00'){
        texto += "IR R$ " + parseFloat(vl_ir).toFixed(2) + "||";
        valor_total = parseFloat(valor_total) - parseFloat(vl_ir);
    }

    if(vl_csll != '' && vl_csll != '0,00'){
        texto += "CSLL R$ " + parseFloat(vl_csll).toFixed(2) + "||";
        valor_total = parseFloat(valor_total) - parseFloat(vl_csll);
    }

    texto += "VALOR LIQUIDO R$ " + parseFloat(valor_total).toFixed(2) + "||";

    $("#descricao_nfse").val(texto);
}



function fcMontarArrNfse(){
    let dadosNfse = {};
    let v_tomador_pk = $("#tomador_pk").val();

    let v_prestador_pk = $("#prestador_pk").val();
    let v_servico_pk = $("#servico_pk").val();
    let v_codigo_servico_pk = $("#codigo_servico_pk").val();
    let v_descricao_nfse = $("#descricao_nfse").val();
    let v_iss_aliquota = moeda2float($("#iss_aliquota").val());
    let v_iss_valor = moeda2float($("#iss_valor").val());
    let v_inss_aliquota = moeda2float($("#inss_aliquota").val());
    let v_inss_valor = moeda2float($("#inss_valor").val());
    let v_pis_aliquota = moeda2float($("#pis_aliquota").val());
    let v_pis_valor = moeda2float($("#pis_valor").val());
    let v_cofins_aliquota = moeda2float($("#cofins_aliquota").val());
    let v_cofins_valor = moeda2float($("#cofins_valor").val());
    let v_ir_aliquota = moeda2float($("#ir_aliquota").val());
    let v_ir_valor = moeda2float($("#ir_valor").val());
    let v_csll_aliquota = moeda2float($("#csll_aliquota").val());
    let v_csll_valor = moeda2float($("#csll_valor").val());
    let v_vl_lancamento = moeda2float($("#vl_lancamento1").val());
    let aliquota = $("#vl_aliquota").val();


    let listaServicoConsulta = $("#ds_descricao_servico").val();
    let cpfCnpj = $("#cnpj").val();
    let cep = $("#cep").val();
    let ds_uf = $("#ds_uf").val();
    let ds_cidade = $("#ds_cidade").val();
    let ds_bairro = $("#ds_bairro").val();
    let tipoLogradouro = $("#tipoLogradouro").val();
    let ds_endereco = $("#ds_endereco").val();
    let numero = $("#numero").val();
    let complemento = $("#complemento").val();
    let inscricaoMunicipal = $("#inscricaoMunicipal").val();
    let inscricaoEstadual = $("#inscricaoEstadual").val();
    let email = $("#email").val();
    let naturezaOperacao = $("#naturezaOperacao").val();

    let descricaoServico =  $("#descricaoServico").val();

    let retidoTomador = $("#iss_retido1").is(':checked')==true?1:2;
    let codigo_tributacao = $("#codigo_tributacao").val();
    dadosNfse[0] = {
        "tomador_pk": v_tomador_pk,    
        "prestador_pk": v_prestador_pk,    
        "servico_pk": v_servico_pk,    
        "codigo_servico_pk": v_codigo_servico_pk,    
        "descricao_nfse": v_descricao_nfse,    
        "iss_aliquota": v_iss_aliquota,    
        "iss_valor": v_iss_valor,    
        "inss_aliquota": v_inss_aliquota,    
        "inss_valor": v_inss_valor,    
        "pis_aliquota": v_pis_aliquota,    
        "pis_valor": v_pis_valor,    
        "cofins_aliquota": v_cofins_aliquota,    
        "cofins_valor": v_cofins_valor,    
        "ir_aliquota": v_ir_aliquota,    
        "csll_aliquota": v_csll_aliquota,  
        "aliquota":aliquota,  
        "csll_valor": v_csll_valor,    
        "vl_lancamento": v_vl_lancamento ,
        "listaServicoConsulta":listaServicoConsulta,
        "cpfCnpj":cpfCnpj,
        "cep":cep,
        "ds_uf":ds_uf,
        "ds_cidade":ds_cidade,
        "ds_bairro":ds_bairro,
        "tipoLogradouro":tipoLogradouro,
        "ds_endereco":ds_endereco,
        "numero":numero,
        "complemento":complemento,
        "inscricaoMunicipal": inscricaoMunicipal,
        "inscricaoEstadual":inscricaoEstadual,
        "email":email,
        "naturezaOperacao":naturezaOperacao,
        "retidoTomador":retidoTomador,
        "descricaoServico":descricaoServico,
        "codigo_tributacao":codigo_tributacao,
        "ir_valor":v_ir_valor
    }
    
    return JSON.stringify(dadosNfse);
}

function fcInformacoesSevicos(){
    var objParametros = {
        'codigoServico': $('#codigo_servico_pk').val()
    };        
    var arrCarregar = carregarController("certificados_empresas", "listarDadosServico", objParametros);
    $("#ds_descricao_servico").val(arrCarregar.data[0]['ds_servico'])
    $("#vl_aliquota").val(arrCarregar.data[0]['vl_aliquota'])
    $("#codigo_tributacao").val(arrCarregar.data[0]['codigo_tributacao'])
}

function fcCarregarListarServicos(){
    var objParametros = {
        'contas_pk':$('#prestador_pk').val()
    };        
    var arrCarregar = carregarController("certificados_empresas", "listarNfeServico", objParametros);
    carregarComboAjax($("#servico_pk"), arrCarregar, " ", "num_codigo_servico", "ds_servico");

}

var formdata = null;
function fcCarregarFormLancamento(){
    var v_pk = $('#lancamento_pk').val();

    if(v_pk > 0){
        var objParametros = {
            "pk": v_pk,
        }
        var arrCarregar = carregarController("lancamento", "listarLancamentoPk", objParametros)
        if(arrCarregar.status == true){
            //Combos
            
            $("#ds_lancamento").val(arrCarregar.data['ds_lancamento'])
            $("#ds_num_documento").val(arrCarregar.data['ds_num_documento'])
            $("#ic_tipo_num_documento").val(arrCarregar.data['ic_tipo_num_documento'])
            $("#tipo_grupo_pk").val(arrCarregar.data['tipo_grupo_lancamento_pk'])
            $("#tipo_lancamento_pk").val(arrCarregar.data['tipo_lancamento_pk'])
            fcCarregarCategoriaOperacao();
            $("#categorias_financeiras_pk").val(arrCarregar.data['categorias_financeiras_pk'])
            fcCarregarTipoPlanoNegocio();
            $("#tipos_operacao_pk").val(arrCarregar.data['tipos_operacao_pk'])
            //$("#tipos_operacao_pk").select2();
            $("#metodos_pagamento_pk").val(arrCarregar.data['metodos_pagamento_pk'])
            $("#dt_faturamento1").val(arrCarregar.data['dt_faturamento'])
            //$("#qtde_parcelas_pk").val(arrCarregar.data['ic_parcela'])
            $("#dt_vencimento1").val(arrCarregar.data['dt_vencimento'])
            $("#vl_lancamento1").val(float2moeda(arrCarregar.data['vl_lancamento']))
            $("#empresa_lancamento_pk").val(arrCarregar.data['empresa_lancamento_pk'])
            fcCarregarContasBancariasLancamento();
            $("#contas_bancarias_pk").val(arrCarregar.data['contas_bancarias_pk'])
            $("#ic_status_lancamento").val(arrCarregar.data['ic_status_lancamento'])
            $("#dt_pagamento").val(arrCarregar.data['dt_pagamento'])
            $("#obs_lancamento").val(arrCarregar.data['obs_lancamento'])
            $("#totalAPagar").html(float2moeda(arrCarregar.data['vl_lancamento'] - arrCarregar.data['vl_total_baixa']))
           
            count_baixa_parcial = arrCarregar.data['count_baixa_parcial'];
            $("#vl_parcial").prop("disabled", false);
            $("#ic_status_lancamento").change(function(){
                if ($("#ic_status_lancamento").val() == 1) {
                    if(count_baixa_parcial > 0){
                        $("#exibir_parcial").show();
                        $("#exibir_vl_parcial").show();
                        $("#vl_parcial").val(float2moeda(arrCarregar.data['vl_lancamento'] - arrCarregar.data['vl_total_baixa']));
                        
                        $("#vl_parcial").prop("disabled", true);
                    }
                } 
            });

            if(arrCarregar.data['count_baixa_parcial'] > 0){
                $("#exibir_tbl_parcial").show()
            }
            
            if ($("#tipo_grupo_pk").val() == 1){
                $("#div_cliente_lancamento_pk").hide();
                carregarComboAjax($("#grupo_lancamento_pk"), arrLeads, " ", "pk", "ds_lead");
                $("#grupo_lancamento_pk").val(arrCarregar.data['grupo_lancamento_pk'])
                //$("#grupo_lancamento_pk").select2();

                fcCarregarLeadsPostosTrabalho();
                
                fcCarregarDadosEmpresa();
                $("#posto_trabalho_lancamento_pk").val(arrCarregar.data['posto_trabalho_lancamento_pk'])
                //$("#posto_trabalho_lancamento_pk").select2();
            

                fcCarregarLeadsContratos();
                $("#contratos_pk").val(arrCarregar.data['contratos_pk'])
                //$("#contratos_pk").select2();
                
        
            } else if($("#tipo_grupo_pk").val() == 2 ){
                $("#div_cliente_lancamento_pk").show();
                carregarComboAjax($("#grupo_lancamento_pk"), arrColaborador, " ", "pk", "ds_colaborador");

                $("#grupo_lancamento_pk").val(arrCarregar.data['grupo_lancamento_pk'])
                //$("#grupo_lancamento_pk").select2();

                fcCarregarDadosBancariosColaborador();
            
                fcCarregarColaboradoresClientes();
                $("#cliente_lancamento_pk").val(arrCarregar.data['cliente_lancamento_pk'])
                //$("#cliente_lancamento_pk").select2();

                fcCarregarColaboradorPostosTrabalho();
                $("#posto_trabalho_lancamento_pk").val(arrCarregar.data['posto_trabalho_lancamento_pk'])
                //$("#posto_trabalho_lancamento_pk").select2();
                
                fcCarregarColaboradorContratos();
                $("#contratos_pk").val(arrCarregar.data['contratos_pk'])
                //$("#contratos_pk").select2();
                
        
            }else if($("#tipo_grupo_pk").val() == 3){
                $("#div_cliente_lancamento_pk").show();
                fcCarregarFornecedor();
                $("#grupo_lancamento_pk").val(arrCarregar.data['grupo_lancamento_pk'])
                //$("#grupo_lancamento_pk").select2();

                fcCarregarClientesFornecedor();
                $("#cliente_lancamento_pk").val(arrCarregar.data['cliente_lancamento_pk'])
                //$("#cliente_lancamento_pk").select2();

                fcCarregarFornecedorPostosTrabalho();
                $("#posto_trabalho_lancamento_pk").val(arrCarregar.data['posto_trabalho_lancamento_pk'])
                //$("#posto_trabalho_lancamento_pk").select2();

                fcCarregarFornecedorContratos();
                $("#contratos_pk").val(arrCarregar.data['contratos_pk'])
                //$("#contratos_pk").select2();
            } 

            //VERIFICAÇÃO DE PERFIL CONTROLLER/FINANCEIRO/ ADM PARA EDITAR STATUS
            
            if (arrCarregar.data['grupos_pk'] == 9 ||arrCarregar.data['grupos_pk'] == 15 || arrCarregar.data['grupos_pk'] == 1) {
             
                $("#ic_status_lancamento").prop('disabled', false);
                $("#empresa_lancamento_pk").prop('disabled', false);
                $("#dt_pagamento").prop('disabled', false);
                $("#vl_parcial").prop('disabled', false);
            }
            else{
                $("#ic_status_lancamento").prop('disabled', true);
                $("#empresa_lancamento_pk").prop('disabled', true);
                $("#dt_pagamento").prop('disabled', true);
                $("#vl_parcial").prop('disabled', true);
            }

            
            var arrCarregar = permissao("atualizar_itens_pagos", "upd");

            $("#btnSalvarLancamento").prop("disabled", false);
            $("#btnSalvarLancamento2").prop("disabled", false);
            

            if($("#ic_status_lancamento").val() == 6){
                $("#exibir_vl_parcial").show();
                $("#exibir_dt").show();
                
            }else if($("#ic_status_lancamento").val() == 1 && arrCarregar.status != true){
                /*$("#exibir_parcial").hide();
                $("#btnSalvarLancamento").prop("disabled", true);
                $("#btnSalvarLancamento2").prop("disabled", true);*/
            }


            
            
            

        }
    }
}

function fcCarregarTblHistoricoParcial(){
    try {
        $("#tblHistoricoParcial tbody").empty(); // Limpar o conteúdo existente do tbody
        var objParametros = {
            "lancamentos_financeiros_pk": $("#lancamento_pk").val()
        };

        let arrCarregar = carregarController("lancamento", "listarHistoricoParcial", objParametros);

        let vl_baixa_parcial = 0;
        let ds_conta_bancaria = '';
        let ds_empresa = '';

        for(let i = 0; i < arrCarregar.data.length; i++){
            ds_conta_bancaria = arrCarregar.data[i]['ds_conta_bancaria'] == null ? "" : arrCarregar.data[i]['ds_conta_bancaria'];
            ds_empresa = arrCarregar.data[i]['ds_empresa'] == null ? "" : arrCarregar.data[i]['ds_empresa'];

            let tbodyTr = $('<tr></tr>');
            $('#tblHistoricoParcial tbody').append(tbodyTr);
            $(tbodyTr).append('<td>'+arrCarregar.data[i]['pk']+'</td>');
            $(tbodyTr).append('<td>'+arrCarregar.data[i]['dt_baixa_parcial']+'</td>');
            $(tbodyTr).append('<td>'+ds_conta_bancaria+'</td>');
            $(tbodyTr).append('<td>'+ds_empresa+'</td>');
            $(tbodyTr).append('<td>'+float2moeda(arrCarregar.data[i]['vl_baixa_parcial'])+'</td>');

            vl_baixa_parcial += arrCarregar.data[i]['vl_baixa_parcial'];
        }
        
        $("#totalParcial").html(float2moeda(vl_baixa_parcial));

        

    } catch (error) {
        utilsJS.toastNotify(false,error);
    }

}

function fclimpaFormLancamento() {
    $("#lancamento_pk").val("");
    $("#ds_lancamento").val("");
    $("#tipo_lancamento_pk").val("");
    $("#categorias_financeiras_pk").val("");
    $("#tipos_operacao_pk").val("");
    $("#tipo_grupo_pk").val("");
    $("#contratos_pk").val("");
    $("#grupo_lancamento_pk").val("");
    $("#grupo_lancamento_fornecedor_pk").val("");
    $("#cliente_lancamento_pk").val("");
    $("#posto_trabalho_lancamento_pk").val("");
    $("#tipo_grupo_centro_custo_pk_receita").val("");
    $("#grupo_lancamento_centro_custo_pk_receita").val("");
    $("#dt_faturamento1").val("");
    $("#dt_vencimento1").val("");
    $("#vl_lancamento1").val("");
    $("#metodos_pagamento_pk").val("");
    $("#empresa_lancamento_pk").val("");
    $("#contas_bancarias_pk").val("");
    $("#ic_status_lancamento").val("");
    $("#dt_pagamento").val("");
    $("#obs_lancamento").val("");
    $("#qtde_parcelas_pk").val("1");
    $("#ds_parcela").text("1");
    $("#div_datas_valores_pagamento").append("");
    $("#ds_num_documento").val("");
    $("#ic_tipo_num_documento").val("");
    $("#totalParcial").val("");
    $("#vl_parcial").val("");
    $("#div_datas_valores_pagamento").empty();
    

    //nfse
    $("#ic_gerar_nfse").val(2);
    $("#iss_retido1").prop("checked", false);
    $("#iss_retido2").prop("checked", false);
    $("#pk").val("");
    $("#tomador_pk").val("");
    $("#prestador_pk").val("");
    $("#servico_pk").val("");
    $("#codigo_servico_pk").val("");
    $("#ds_descricao_servico").val("");
    $("#vl_aliquota").val("");
    $("#iss_retido1").val("");
    $("#iss_retido2").val("");
    $("#iss_aliquota").val("");
    $("#iss_valor").val("");
    $("#inss_aliquota").val("");
    $("#inss_valor").val("");
    $("#pis_aliquota").val("");
    $("#pis_valor").val("");
    $("#cofins_aliquota").val("");
    $("#cofins_valor").val("");
    $("#ir_aliquota").val("");
    $("#ir_valor").val("");
    $("#csll_aliquota").val("");
    $("#csll_valor").val("");
    $("#descricao_nfse").val("");

}

function fcGerenciaLabel() {
    if ($("#tipo_lancamento_pk").val() == 1) {
        $("#label_lancamento").html('Lançar Receita De ?:');
    } else if ($("#tipo_lancamento_pk").val() != 1) {
        $("#label_lancamento").html('Lançar Despesa Para ?:');
    } else if ($("#tipo_lancamento_pk").val() == "") {
        $("#label_lancamento").html('Lançar Para:');
    }
}

function fcCarregarCategoriaOperacao() {
    
    var objParametros = {
        "tipos_operacao_pk": $("#tipos_operacao_pk").val()
    };
    var arrCarregar = carregarController("categoria_financeira", "listarPorPlano", objParametros);
    carregarComboAjax($("#categorias_financeiras_pk"), arrCarregar, "", "pk", "ds_categoria");
}


function fcCarregarTipoPlanoNegocio() {
    var objParametros = {};
    var arrCarregar = carregarController("plano_contas", "listarTodos", objParametros);
    carregarComboAjax($("#tipos_operacao_pk"), arrCarregar, " ", "pk", "ds_tipo_operacao");
}

function fcCarregarContasBancariasLancamento() {
    var objParametros = {
        "empresa_pk": $("#empresa_lancamento_pk").val()
    };
    var arrCarregar = carregarController("conta_bancaria", "listaPorEmpresa", objParametros);
    carregarComboAjax($("#contas_bancarias_pk"), arrCarregar, " ", "pk", "ds_conta");
}

function fcSelecionaGrupo() {
    if ($("#tipo_grupo_pk").val() == 1){
        $("#div_cliente_lancamento_pk").hide();
        $("#mostrar_btn_add_fornecedor").hide();
        $("#grupo_lancamento_fornecedor_pk").val("");
        $("#exibir_input_fornecedor").hide();
        $("#exibir_combo_fornecedor").show();
        $(".chzn-select").chosen('destroy');
        carregarComboAjax($("#grupo_lancamento_pk"), arrLeads, " ", "pk", "ds_lead");
        
        setTimeout(function() {
            $(".chzn-select").chosen({ allow_single_deselect: true });
        }, 1500);
        $("#grupo_lancamento_pk").off('change').on('change', function () {
           
            $(".chzn-select").chosen('destroy');
            fcCarregarLeadsPostosTrabalho();
            
            //fcCarregarDadosEmpresa();

            setTimeout(function() {
                $(".chzn-select").chosen({ allow_single_deselect: true });
            }, 1500);
            //$("#posto_trabalho_lancamento_pk").select2();
        });
        $("#posto_trabalho_lancamento_pk").off('change').on('change', function () {
            $(".chzn-select").chosen('destroy');
            fcCarregarLeadsContratos();
            setTimeout(function() {
                $(".chzn-select").chosen({ allow_single_deselect: true });
            }, 1500);
        });

    } else if($("#tipo_grupo_pk").val() == 2 ){
        $("#div_cliente_lancamento_pk").show();
        $("#mostrar_btn_add_fornecedor").hide();
        $("#grupo_lancamento_fornecedor_pk").val("");
        $("#exibir_input_fornecedor").hide();
        $("#exibir_combo_fornecedor").show();

       
        $(".chzn-select").chosen('destroy');

        carregarComboAjax($("#grupo_lancamento_pk"), arrColaborador, " ", "pk", "ds_colaborador");


        

        
       //$("#grupo_lancamento_pk").select2();
        $("#grupo_lancamento_pk").off('change').on('change', function () {
            $(".chzn-select").chosen('destroy');
            fcCarregarDadosBancariosColaborador();
            setTimeout(function() {
                $(".chzn-select").chosen({ allow_single_deselect: true });
            }, 1500);
        });
        $(".chzn-select").chosen('destroy');
        fcCarregarColaboradoresClientes();
        setTimeout(function() {
            $(".chzn-select").chosen({ allow_single_deselect: true });
        }, 1500);
        $("#cliente_lancamento_pk").off('change').on('change', function () {
            
            $(".chzn-select").chosen('destroy');
            fcCarregarColaboradorPostosTrabalho();
            setTimeout(function() {
                $(".chzn-select").chosen({ allow_single_deselect: true });
            }, 1500);
        });
        $("#posto_trabalho_lancamento_pk").off('change').on('change', function () {
            $(".chzn-select").chosen('destroy');
            fcCarregarColaboradorContratos();
            setTimeout(function() {
                $(".chzn-select").chosen({ allow_single_deselect: true });
            }, 1500);
            
            //$("#contratos_pk").select2();
        });

    }else if($("#tipo_grupo_pk").val() == 3){
        $("#div_cliente_lancamento_pk").show();
        $("#mostrar_btn_add_fornecedor").show();
        $("#exibir_input_fornecedor").hide();
        $("#exibir_combo_fornecedor").show();

        $(".chzn-select").chosen('destroy');            
        fcCarregarFornecedor();
        //$("#grupo_lancamento_pk").select2();
        fcCarregarClientesFornecedor();
        setTimeout(function() {
            $(".chzn-select").chosen({ allow_single_deselect: true });
        }, 1500);
        $("#cliente_lancamento_pk").off('change').on('change', function () {
            $(".chzn-select").chosen('destroy');
            fcCarregarFornecedorPostosTrabalho();
            setTimeout(function() {
                $(".chzn-select").chosen({ allow_single_deselect: true });
            }, 1500);
        });
        $("#posto_trabalho_lancamento_pk").off('change').on('change', function () {
            $(".chzn-select").chosen('destroy');
            fcCarregarFornecedorContratos();
            setTimeout(function() {
                $(".chzn-select").chosen({ allow_single_deselect: true });
            }, 1500);
        });
    } 
}


function fcCarregarLeadsPostosTrabalho() {

    var objParametros = {
        "leads_pk": $("#grupo_lancamento_pk").val()
    };
    var arrCarregar = carregarController("lead", "listaLeadsPostosTrabalho", objParametros);
    carregarComboAjax($("#posto_trabalho_lancamento_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarLeadsContratos() {
    var objParametros = {
        "leads_pk": $("#posto_trabalho_lancamento_pk").val()
    };
    var arrCarregar = carregarController("contrato", "listarLeadsPk", objParametros);

    carregarComboAjax($("#contratos_pk"), arrCarregar, " ", "pk", "ds_combo_contrato");
}

//Combos Colaborador

function fcCarregarColaboradoresClientes() {

    var objParametros = {
        "pk": ''
    };
    var arrCarregar = carregarController("lead", "listarClienteColaborador", objParametros);
    carregarComboAjax($("#cliente_lancamento_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarColaboradorPostosTrabalho() {
    var objParametros = {
        "leads_pk": $("#cliente_lancamento_pk").val()
    };
    var arrCarregar = carregarController("lead", "listaColaboradorPostosTrabalho", objParametros);
    carregarComboAjax($("#posto_trabalho_lancamento_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarColaboradorContratos() {
    if($("#posto_trabalho_lancamento_pk").val()!=""){
        var objParametros = {
            "leads_pk": $("#posto_trabalho_lancamento_pk").val()
        };
        var arrCarregar = carregarController("contrato", "listaColaboradorContratos", objParametros);
        carregarComboAjax($("#contratos_pk"), arrCarregar, " ", "pk", "ds_contrato");
    }
}

//Combos Fornecedores
function fcCarregarFornecedor() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("fornecedor", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_lancamento_pk"), arrCarregar, " ", "pk", "ds_fornecedor");
}

function fcCarregarClientesFornecedor() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#cliente_lancamento_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarFornecedorPostosTrabalho() {

    var objParametros = {
        "leads_pk": $("#cliente_lancamento_pk").val()
    };
    var arrCarregar = carregarController("lead", "listaFornecedorPostosTrabalho", objParametros);
    carregarComboAjax($("#posto_trabalho_lancamento_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarFornecedorContratos() {
    if($("#posto_trabalho_lancamento_pk").val()!=""){
        var objParametros = {
            "leads_pk": $("#posto_trabalho_lancamento_pk").val()
        };
        var arrCarregar = carregarController("contrato", "listaLeadContratos", objParametros);

        carregarComboAjax($("#contratos_pk"), arrCarregar, " ", "pk", "ds_contrato");
    }
}


function fcValidar() {

    if ($('#ds_lancamento').val() == "") {
        $("#alert_ds_lancamento").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_ds_lancamento").slideUp(500);
        });
        $('#alert_ds_lancamento').focus();
        return false;
    }
    if ($('#tipo_lancamento_pk').val() == "") {
        $("#alert_tipo_lancamento_pk").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_tipo_lancamento_pk").slideUp(500);
        });
        $('#alert_tipo_lancamento_pk').focus();
        return false;
    }
    if ($('#categorias_financeiras_pk').val() == "") {
        $("#alert_categorias_financeiras_pk").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_categorias_financeiras_pk").slideUp(500);
        });
        $('#alert_categorias_financeiras_pk').focus();
        return false;
    }
    if ($('#tipos_operacao_pk').val() == "") {
        $("#alert_tipos_operacao_pk").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_tipos_operacao_pk").slideUp(500);
        });
        $('#alert_tipos_operacao_pk').focus();
        return false;
    }
    if ($('#tipo_grupo_pk').val() == "") {
        $("#alert_tipo_grupo_pk").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_tipo_grupo_pk").slideUp(500);
        });
        $('#alert_tipo_grupo_pk').focus();
        return false;
    }
    if ($('#grupo_lancamento_pk').val() == "" && $("#grupo_lancamento_fornecedor_pk").val() == "") {
        $("#alert_grupo_lancamento_pk").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_grupo_lancamento_pk").slideUp(500);
        });
        $('#alert_grupo_lancamento_pk').focus();
        return false;
    }
    if ($('#dt_vencimento').val() == "") {
        $("#alert_dt_vencimento").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_dt_vencimento").slideUp(500);
        });
        $('#alert_dt_vencimento').focus();
        return false;
    }
    if ($('#vl_lancamento').val() == "") {
        $("#alert_vl_lancamento").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_vl_lancamento").slideUp(500);
        });
        $('#alert_vl_lancamento').focus();
        return false;
    }
    if ($('#metodos_pagamento_pk').val() == "") {
        $("#alert_metodos_pagamento_pk").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_metodos_pagamento_pk").slideUp(500);
        });
        $('#alert_metodos_pagamento_pk').focus();
        return false;
    }
    if ($('#ic_status_lancamento').val() == "1") {
        if ($('#empresa_lancamento_pk').val() == "") {
            $("#alert_empresa_lancamento_pk").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_empresa_lancamento_pk").slideUp(500);
            });
            $('#alert_empresa_lancamento_pk').focus();
            return false;
        }
        if ($('#contas_bancarias_pk').val() == "") {
            $("#alert_contas_bancarias_pk").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_contas_bancarias_pk").slideUp(500);
            });
            $('#alert_contas_bancarias_pk').focus();
            return false;
        }
        if ($('#dt_pagamento').val() == "") {
            $("#alert_dt_pagamento").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_dt_pagamento").slideUp(500);
            });
            $('#alert_dt_pagamento').focus();
            return false;
        }
    } else if ($('#ic_status_lancamento').val() == "") {
        if ($('#ic_status_lancamento').val() == "") {
            $("#alert_ic_status_lancamento").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_ic_status_lancamento").slideUp(500);
            });
            $('#alert_ic_status_lancamento').focus();
            return false;
        }
    }else if($('#ic_status_lancamento').val() == "6"){
        if ($('#dt_pagamento').val() == "") {
            $("#alert_dt_pagamento").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_dt_pagamento").slideUp(500);
            });
            $('#alert_dt_pagamento').focus();
            return false;
        }
        if ($('#vl_parcial').val() == "") {
            $("#alert_vl_parcial").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_vl_parcial").slideUp(500);
            });
            $('#alert_vl_parcial').focus();
            return false;
        }
    }
    ///return false;
    if((moeda2float($("#vl_parcial").val()) + moeda2float($("#totalParcial").html())) > moeda2float($("#vl_lancamento1").val())){
        sweetMensagem('warning',"Valor parcial excede o valor total");
        return false;
    }

        
    fcEnviarLancamento();
}
var formdata = null;
function fcEnviarLancamento() {
    formdata = new FormData();
    let doc_lancamento = fcFormatarDadosArquivos();
    let arrParcelas = fcMontarArrParcelas();
    let arrNfse = [];
    /*let ic_gerar_nfse = $("#ic_gerar_nfse").val();
    
    if(ic_gerar_nfse == 1){
        arrNfse = fcMontarArrNfse();
    }*/
    /*console.log(arrNfse)
    return;*/

    let v_pk = $("#lancamento_pk").val();
    let v_ds_lancamento = $("#ds_lancamento").val();
    let v_tipo_lancamento_pk = $("#tipo_lancamento_pk").val();
    let v_categorias_financeiras_pk = $("#categorias_financeiras_pk").val();
    let v_tipos_operacao_pk = $("#tipos_operacao_pk").val();
    let v_tipo_grupo_pk = $("#tipo_grupo_pk").val();
    let v_grupo_lancamento_pk = $("#grupo_lancamento_pk").val();
    let v_grupo_lancamento_fornecedor_pk = $("#grupo_lancamento_fornecedor_pk").val();
    let v_cliente_lancamento_pk = $("#cliente_lancamento_pk").val();
    let v_posto_trabalho_lancamento_pk = $("#posto_trabalho_lancamento_pk").val();
    let v_contratos_pk = $("#contratos_pk").val();

    let v_metodos_pagamento_pk = $("#metodos_pagamento_pk").val();
    let v_empresa_lancamento_pk = $("#empresa_lancamento_pk").val();
    let v_contas_bancarias_pk = $("#contas_bancarias_pk").val();
    let v_ic_status_lancamento = $("#ic_status_lancamento").val();
    let v_dt_pagamento = $("#dt_pagamento").val();
    let v_obs_lancamento = $("#obs_lancamento").val();
    let v_ds_num_documento = $("#ds_num_documento").val();
    let v_ic_tipo_num_documento = $("#ic_tipo_num_documento").val();
    let v_qtde_parcelas_pk = $("#qtde_parcelas_pk").val();
    let v_vl_parcial = 0;
    if($("#vl_parcial").val()!=""){
        v_vl_parcial = moeda2float($("#vl_parcial").val());
    }
    
    

    formdata.append("pk",v_pk);
    formdata.append("ds_lancamento",v_ds_lancamento);
    formdata.append("tipo_lancamento_pk",v_tipo_lancamento_pk);
    formdata.append("categorias_financeiras_pk",v_categorias_financeiras_pk);
    formdata.append("tipos_operacao_pk",v_tipos_operacao_pk);
    formdata.append("tipo_grupo_pk",v_tipo_grupo_pk);
    formdata.append("grupo_lancamento_pk",v_grupo_lancamento_pk);
    formdata.append("grupo_lancamento_fornecedor_pk",v_grupo_lancamento_fornecedor_pk);
    formdata.append("cliente_lancamento_pk",v_cliente_lancamento_pk);
    formdata.append("posto_trabalho_lancamento_pk",v_posto_trabalho_lancamento_pk);
    formdata.append("contratos_pk",v_contratos_pk);
    formdata.append("metodos_pagamento_pk",v_metodos_pagamento_pk);
    formdata.append("empresa_lancamento_pk",v_empresa_lancamento_pk);
    formdata.append("contas_bancarias_pk",v_contas_bancarias_pk);
    formdata.append("ic_status_lancamento",v_ic_status_lancamento);
    formdata.append("dt_pagamento",v_dt_pagamento);
    formdata.append("obs_lancamento",v_obs_lancamento);
    formdata.append("ds_num_documento",v_ds_num_documento);
    formdata.append("ic_tipo_num_documento",v_ic_tipo_num_documento);
    formdata.append("qtde_parcelas_pk",v_qtde_parcelas_pk);
    formdata.append("vl_parcial",v_vl_parcial);
    formdata.append("arrParcelas",arrParcelas);
    formdata.append("doc_lancamento",doc_lancamento);
    formdata.append("arrNfse",arrNfse);
   
    utilsJS.loading('Salvando...');
    $.ajax({
        type: 'POST',
        url: '/api/lancamento/salvar',
        data: formdata,
        processData: false,
        contentType: false,
        complete: function (response) {
            try {
                utilsJS.loaded();
                var log = JSON.parse(response.responseText);
                if(log.status==true){
                    utilsJS.toastNotify(true,log.message);
                    utilsJS.sweetMensagem(true, log.data);
                    fclimpaFormLancamento();
                    
                    $("#event-modal").modal("hide");
                }
                else{
                    utilsJS.toastNotify(log.status,log.message);
                }

            } catch (e) {
                utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
            }
        }
    });
    

}

function listaDiscriminacaoServico(){    
    var objParametros = {
        'pk': ''
    };        
    var arrCarregar = carregarController("discriminacao_servicos", "listarDiscriminacao", objParametros);
    carregarComboAjax($("#listaDiscriminacaoServico"), arrCarregar, " ", "pk", "ds_discriminacao_servico");
}
function fcListarItensGruposCentroCustoReceita() {

    var objParametros = {
        "tipo_grupo_pk": ""
    };
    if ($("#tipo_grupo_centro_custo_pk_receita").val() == 1) {
        var arrCarregar = carregarController("lancamento", "listaItensGrupoLeads", objParametros);

        carregarComboAjax($("#grupo_lancamento_centro_custo_pk_receita"), arrCarregar, " ", "pk", "ds_lead");

    } else if ($("#tipo_grupo_centro_custo_pk_receita").val() == 2) {
        var arrCarregar = carregarController("lancamento", "listaItensGrupoColaboradores", objParametros);
        carregarComboAjax($("#grupo_lancamento_centro_custo_pk_receita"), arrCarregar, " ", "pk", "ds_colaborador");

    } else if ($("#tipo_grupo_centro_custo_pk_receita").val() == 3) {
        var arrCarregar = carregarController("lancamento", "listaItensGrupoFornecedores", objParametros);
        carregarComboAjax($("#grupo_lancamento_centro_custo_pk_receita"), arrCarregar, " ", "pk", "ds_fornecedor");

    }
    else if ($("#tipo_grupo_centro_custo_pk_receita").val() == 4) {
        var arrCarregar = carregarController("equipe", "listarTodos", objParametros);
        carregarComboAjax($("#grupo_lancamento_centro_custo_pk_receita"), arrCarregar, " ", "pk", "ds_equipe");
    }
}

function fcCarregarDadosBancariosColaborador() {
    $("#listar_dados_bancarios_colaborador").html("");
    $("#listar_dados_bancarios_colaborador").append("");

    var strRetorno = "";

    var objParametros = {
        pk: $("#grupo_lancamento_pk").val()
    };

    var arrCarregar = carregarController("colaborador", "listarDadosBancarios", objParametros);

    if (arrCarregar.status == true) {

        if (arrCarregar.data.length > 0) {
            strRetorno += "<br>";
            strRetorno += "<table id='tabela' class='table table-striped table-bordered ' style='width:100%'>";
            strRetorno += "     <thead class='fixed-content'>";
            strRetorno += "         <tr align='center'>";
            strRetorno += "             <th width='10%' class='menu_fixo'><font style='font-size: 12px'>Banco</font></th>";
            strRetorno += "             <th width='5%' class='menu_fixo'><font style='font-size: 12px'>Agência</font></th>";
            strRetorno += "             <th width='5%' class='menu_fixo'><font style='font-size: 12px'>Conta</font></th>";
            strRetorno += "             <th width='5%' class='menu_fixo'><font style='font-size: 12px'>Pix</font></th>";
            strRetorno += "             <th width='5%' class='menu_fixo'><font style='font-size: 12px'>Favorecido</font></th>";
            strRetorno += "         </tr>";
            strRetorno += "     </thead>";

            strRetorno += "     <tbody >";

            for (i = 0; i < arrCarregar.data.length; i++) {

                strRetorno += "<tr>";
                if (arrCarregar.data[i]['ds_banco'] != null) {
                    strRetorno += "<th width='10%' align='center'><font style='font-size: 13px'>" + arrCarregar.data[i]['ds_banco'] + "</font></th>";
                }
                else {
                    strRetorno += "<th width='10%' align='center'><font style='font-size: 13px'></font></th>";
                }

                if (arrCarregar.data[i]['ds_pix'] == null) {
                    var v_ds_pis = "";
                } else {
                    var v_ds_pis = arrCarregar.data[i]['ds_pix'];
                }

                if (arrCarregar.data[i]['ds_conta_favorecido'] == null) {
                    var v_ds_conta_favorecido = "";
                } else {
                    var v_ds_conta_favorecido = arrCarregar.data[i]['ds_conta_favorecido'];
                }

                strRetorno += "         <th width='5%' align='center'><font style='font-size: 13px'>" + arrCarregar.data[i]['ds_agencia'] + "</font></th>";
                strRetorno += "         <th width='5%' align='center'><font style='font-size: 13px'>" + arrCarregar.data[i]['ds_conta'] + "</font></th>";
                strRetorno += "         <th width='5%' align='center'><font style='font-size: 13px'>" + v_ds_pis + "</font></th>";
                strRetorno += "         <th width='5%' align='center'><font style='font-size: 13px'>" + v_ds_conta_favorecido + "</font></th>";
                strRetorno += "     </tr>";
            }
        }
    }

    strRetorno += "     </tbody>";

    strRetorno += "</table>";

    $("#listar_dados_bancarios_colaborador").html(strRetorno);
}

function fcQtdeParcelas(){
    $("#combo_qtde_parcelas_pk").append("");
    $("#combo_qtde_parcelas_pk").empty();
    
    var str = "";
    str += "<select class='form-control form-control-sm'  id='qtde_parcelas_pk' name='qtde_parcelas_pk' onchange='fcGridContratoDadosFatura(0)'>";
    for (i = 1; i < 72; i++) {
        str += "<option value='" + i + "'>" + i + " </option>";
    }
    str += " </select>";
    $("#combo_qtde_parcelas_pk").append(str);
}

function fcArrayDatasVlPagamento() {
    // Limpa a tabela antes de começar
    $("#div_datas_valores_pagamento").empty();

    // Verifica se a quantidade de parcelas é maior que 1
    if ($("#qtde_parcelas_pk").val() > 1) {
        var parcelas = parseInt($("#qtde_parcelas_pk").val());
        var str = "";

        // Valida os campos obrigatórios
        if ($("#dt_faturamento1").val() === '') {
            sweetMensagem('warning', 'Preencha o campo Dt Faturamento');
            $("#qtde_parcelas_pk").val("1");
            return false;
        }

        if ($("#dt_vencimento1").val() === '') {
            sweetMensagem('warning', 'Preencha o campo Dt Vencimento');
            $("#qtde_parcelas_pk").val("1");
            return false;
        }

        if ($("#vl_lancamento1").val() === '') {
            sweetMensagem('warning', 'Preencha o campo Vl Lancamento!');
            $("#qtde_parcelas_pk").val("1");
            return false;
        }

        // Obter os valores iniciais
        var dt_faturamento = $("#dt_faturamento1").val();
        var dt_vencimento = $("#dt_vencimento1").val();
        var vl_lancamento = $("#vl_lancamento1").val();

        // Converter data de vencimento inicial para objeto Date
        var partesData = dt_vencimento.split("/");
        var dataVencimento = new Date(partesData[2], partesData[1] - 1, partesData[0]); // Ano, Mês (0-11), Dia

        // Loop para gerar as parcelas
        for (let i = 2; i <= parcelas; i++) {
            // Formatar a data de vencimento para DD/MM/YYYY
            // Incrementar a data de vencimento apenas para as próximas parcelas
            dataVencimento.setMonth(dataVencimento.getMonth() + 1);
            var dia = String(dataVencimento.getDate()).padStart(2, '0');
            var mes = String(dataVencimento.getMonth() + 1).padStart(2, '0'); // Mês começa em 0
            var ano = dataVencimento.getFullYear();
            var dataFormatada = `${dia}/${mes}/${ano}`;

            

            // Construir linha da tabela
            str += `<tr>
                        <td>Parcela ${i}
                            <input type="hidden" id="parcela_pk${i}" value="${i}" />
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" id="dt_faturamento${i}" name="dt_faturamento" onkeypress="mascara(this,mdata)" maxlength="10" value="${dt_faturamento}" />
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" id="dt_vencimento${i}" name="dt_vencimento" onkeypress="mascara(this,mdata)" maxlength="10" value="${dataFormatada}" />
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" id="vl_lancamento${i}" name="vl_lancamento" onkeypress="mascara(this,moeda)" value="${vl_lancamento}" />
                        </td>
                    </tr>`;

            
        }

        // Adicionar as linhas à tabela
        $("#div_datas_valores_pagamento").append(str);
    }
}



function fcMontarArrParcelas(){
    let dadosParcelas = {};
    let v_qtde_parcelas_pk = $("#qtde_parcelas_pk").val();
    for (i = 0; i < v_qtde_parcelas_pk; i++){     
        let l = i + 1;

        let v_dt_faturamento = $("#dt_faturamento"+l).val();
        let v_dt_vencimento = $("#dt_vencimento"+l).val();
        let v_vl_lancamento = moeda2float($("#vl_lancamento"+l).val());

        dadosParcelas[i] = {
            "dt_faturamento": v_dt_faturamento,    
            "dt_vencimento": v_dt_vencimento,    
            "vl_lancamento": v_vl_lancamento,    
            "parcelas_pk": l  
        }
    }
    return JSON.stringify(dadosParcelas);
}

function fcCarregarGridArquivos(){
    try {
        if($("#lancamento_pk").val()!=""){
            var objParametros = {
                "lancamentos_pk": $("#lancamento_pk").val()
            };

            var v_url = routes_api("documento", "listarDocumentosLancamentos", objParametros);
            //Trata a tabela
            tblDocumentosLancamento = $('#tblDocumentosLancamento').DataTable({
                searching: false,
                paging: false,
                pageLength: 10,
                aLengthMenu: [10, 25, 50, 100],
                iDisplayLength: 10,
                processing: false,
                serverSide: false,
                ajax: v_url,
                responsive: true,
                scrollX: true,
                language: {
                    emptyTable: "Não existem Dados cadastrados"
                },
                order: [
                    [0, "asc"]
                ],
                columns: [
                    {
                        mRender: function (data, type, full) {
                            return full['pk_doc_bd'];
                        },
                        'orderable': true,
                        'searchable': false,
                        width: '80px'

                    },
                    {
                        mRender: function (data, type, full) {
                            return full['t_ds_documento'];
                        },
                        'orderable': true,
                        'searchable': false,
                        width: '80px'

                    },
                    {
                        mRender: function (data, type, full) {
                            return full['t_ds_nome_original'];
                        },
                        'orderable': true,
                        'searchable': false,
                        width: '80px'

                    },
                    {
                        mRender: function (data, type, full) {
                            var buttonDelete = "<i class='fa fa-download function_download' style='font-size:18px; color:blue' title='DOWNLOAD DOCUMENTO'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='bi bi-x-circle function_delete' style='font-size:18px; color:blue' title='EXCLUIR O DOCUMENTO'></i>";
                            return  buttonDelete;
                        },
                        'orderable': false,
                        'searchable': false,
                        width: '80px'
                    }
                ]

            });
            $('#tblDocumentosLancamento tbody').on('click', '.function_download', function () {
                var data;

                if(tblDocumentosLancamento.row( $(this).parents('li') ).data()){
                    data = tblDocumentosLancamento.row( $(this).parents('li') ).data();
                }
                else if(tblDocumentosLancamento.row( $(this).parents('tr') ).data()){
                    data = tblDocumentosLancamento.row( $(this).parents('tr') ).data();
                }

                fcDownloadDocumentoLancCad(data['t_ds_documento'],data['pk_doc_bd']);

            });
            $('#tblDocumentosLancamento tbody').on('click', '.function_delete', function () {
                var data;

                if(tblDocumentosLancamento.row( $(this).parents('li') ).data()){
                    data = tblDocumentosLancamento.row( $(this).parents('li') ).data();
                }
                else if(tblDocumentosLancamento.row( $(this).parents('tr') ).data()){
                    data = tblDocumentosLancamento.row( $(this).parents('tr') ).data();
                }

                if(data['t_pk'] != ""){
                    fcExcluirDocumentoLancCad(data['t_pk'],data['pk_doc_bd']);
                }
            });
        }
        else{
            tblDocumentosLancamento = $("#tblDocumentosLancamento").DataTable(
                {
                    "searching": false,
                    "paging": false,
                    "scrollX": true,
                    "columnDefs" : [{
                        orderable: false,
                        targets: [0,1,2,3]
                    }]
                }
            );
            return false;
        }


    } catch (error) {
        utilsJS.toastNotify(false,error);
    }
}
//COMEÇO DOCUMENTOS UPLOAD
function fcDownloadDocumentoLancCad(ds_documento,pk_doc_bd){
    var arrCarregar = permissao("documento", "ins");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false,'Você não tem permissão');
        return false;
    }

    //var url_documento = (window.location.protocol+"//"+window.location.host+"/app/src/docs/"+ds_documento)

    //DOWNLOAD
    var v_url = "/documento/download?pk_doc_bd="+pk_doc_bd+"&ds_documento="+ds_documento;

    window.open(v_url, '_blank');
}

function fcExcluirDocumentoLancCad(v_pk,v_pk_doc){
    var arrCarregar = permissao("documento", "del");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false,'Você não tem permissão');
        return false;
    }
    if(v_pk != ""){

        var objParametros = {
            "pk": v_pk,
            "pk_doc_bd":v_pk_doc
        };

        var arrExcluir = carregarController("documento", "excluir", objParametros);

        if (arrExcluir.status == true){

            //Exibe a mensagem
            utilsJS.toastNotify(true,arrExcluir.message);
            tblDocumentosLancamento.clear().destroy();
            fcCarregarGridArquivos();
        }
        else{
            utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
        }
    }
    else{
        utilsJS.toastNotify(false,'Código não encontrado');
    }
}
function fcAlterarNomeArquivo(v_arquivo){

    var objParametros = {
        "lancamento_pk": $("#lancamento_pk").val(),
        "ds_arquivo": v_arquivo
    };

    var arrEnviar = carregarController("documento", "renomearArquivoLancamento", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        $("#ds_documento").text(arrEnviar.data);


    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcIncluirLinhaArquivo(nome_original){
    if($("#lancamento_pk").val()!=""){
        var objParametros = {
            "pk": "",
            "lancamentos_pk": $("#lancamento_pk").val(),
            "ds_documento": $("#ds_documento").text(),
            "pk_doc_bd": $("#pk_documento_bd").text(),
            "ds_nome_original": nome_original
        };

        var arrEnviar = carregarController("documento", "salvarLancamentos", objParametros);

        if (arrEnviar.status == true){
            // Reload datable
            tblDocumentosLancamento.clear().destroy();
            fcCarregarGridArquivos();

        }
        else{
            utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
        }
    }
    else{
        tblDocumentosLancamento.row.add(
            [   $("#pk_documento_bd").text(),
                $("#ds_documento").text(),
                nome_original,
                "<a class='function_delete'><span><i class='fa fa-trash' style='width: 13px'></i></span></a>"
            ]
        ).draw( false );

        //Adiciona o evento click na linha que acabou de ser adicionada.
        $(".function_delete").on("click",fcApagarArquivo);
        return false;
    }

}


function fcFormatarDadosArquivos(){

    var dsDocumento = "";
    var dsNomeOriginal = "";

    var arrKeys = [];
    arrKeys[0] = "ds_documento";
    arrKeys[1] = "ds_nome_original";
    arrKeys[2] = "pk_doc_bd";

    var arrDados = [];
    var i = 0;
    $('#tblDocumentosLancamento tbody tr').each(function () {
        var colunas = $(this).children();
        pkDocBd = $(colunas[0]).text();
        dsDocumento =  $(colunas[1]).text();
        dsNomeOriginal = $(colunas[2]).text();



        arrDados[i] = [dsDocumento, dsNomeOriginal,pkDocBd];
        i++;
    });

    return arrayToJson(arrKeys, arrDados);

}
function fcSalvarDocumentos(formdata){

    var url = "";


    url = "/documento/salvarDocumento";


    var arrRetornoCarregarControle;

    var request = $.ajax({
        url:          url,
        data:         formdata,
        processData:  false,
        cache:        false,
        async:        false,
        dataType:     'json',
        contentType:  false,
        type:         'post'
    });
    request.done(function(output){
        if (output.status == true){
            $("#pk_documento_bd").text(output.data);
        }else{
            utilsJS.toastNotify(false, 'Falhou a requisição: '+output.message);
        }
    });
    request.fail(function(jqXHR, textStatus){
        utilsJS.toastNotify(false, 'Falhou a requisição: '+textStatus);
    });

}