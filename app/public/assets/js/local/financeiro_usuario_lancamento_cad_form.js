var tbParcelas = "";
var tblDocumentosLancamento = "";
function fcAbrirCadastroLancamento(pk) {
    try {
    fclimpaFormLancamento();
    $('#lancamento_pk').val(pk)

    var arrCarregar = permissao("lancamento_contabancaria", "cons");  
    if (arrCarregar.status != true) {
        $("#div_conta_bancarias").hide();
    }

    
    var arrCarregar = permissao("lancamento_empresa", "cons");  
    if (arrCarregar.status != true) {
        $("#div_lancar_empresa").hide();
    }

    var arrCarregar = permissao("lancar_dt_atual_retroativa", "ins");
    if (arrCarregar.result == 'success'){
        $('#dt_faturamento1').datepicker({
            
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

    var arrCarregar = permissao("status_finaceiro", "upd");  
    if (arrCarregar.status != true) {
        $("#exibir_pago").hide();
    }else {
        $("#exibir_pago").show();
    }

    //Combos
    $("#tipo_lancamento_pk").change(function () {
        fcGerenciaLabel();
    });

    fcCarregarCategoriaOperacao();


    //Função de parcelas
    fcQtdeParcelas();

    $("#qtde_parcelas_pk").change(function () {
        fcArrayDatasVlPagamento();
    });

    $("#div_cliente_lancamento_pk").hide();
    $("#tipo_grupo_pk").change(function () {
        fcSelecionaGrupo();
    });

    fcCarregarMetodosPagamentoReceita();

    $("#exibir_dt").hide();

    $("#ic_status_lancamento").change(function () {
        if ($("#ic_status_lancamento").val() == 1) {
            $("#exibir_dt").show();
        } else {
            $("#exibir_dt").hide();
            $("#dt_pagamento").val("");
        }
    });

    //Esta função deixar show apenas para o cliente ECol
    $("#divExibirCentroCustol").hide();

    //Eventos
   

    $('#dt_faturamento1').datepicker({
        
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

    $("#vl_lancamento1").keypress(function () {
        mascara(this, moeda);
    });

    fcCarregarEmpresaContaLancamento();

    $("#categorias_financeiras_pk").change(function () {
        fcCarregarTipoPlanoNegocio();
        $("#tipos_operacao_pk").select2();
    });

    $("#empresa_lancamento_pk").change(function () {
        fcCarregarContasBancariasLancamento();
        $("#contas_bancarias_pk").select2();
    });


    //Documentos

    tblDocumentosLancamento.clear().destroy();
    fcCarregarGridArquivos();

        formdata = new FormData();
        $('#fileuploadUsuario').off('change').on('change', function() {
            var arrCarregar = permissao("documento", "ins");
            if (arrCarregar.status != true) {
                utilsJS.toastNotify(false, 'Você não tem permissão para cadastrar um documento');
                return false;
            }
            //on change event
            if($(this).prop('files').length > 0){
                $.each($(this).prop('files'), function (index, file) {
                    formdata.append(index, file);

                    fcSalvarDocumentos(formdata);

                    $("#ds_nome_original").html(file.name);

                    fcAlterarNomeArquivo(file.name);
                    if($("#lancamento_pk").val()==""){
                        fcIncluirLinhaArquivo(file.name);
                    }
                    else{
                        fcSalvarDocumentosLancamento()
                    }

                });

            }
        });




    
    fcCarregarFormLancamento();
    $("#tipo_lancamento_pk").select2();
    $("#categorias_financeiras_pk").select2();
    $("#empresa_lancamento_pk").select2();
    $('#event-modal').modal("show");
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
    
}

function fcSalvarDocumentosLancamento(){
    var objParametros = {
        "pk_doc_bd": $("#pk_documento_bd").text(),
        "ds_nome_original": $("#ds_nome_original").html(),
        "ds_documento": $("#ds_documento").text(),
        "lancamentos_pk": $("#lancamento_pk").val(),
    };


    var arrEnviar = carregarController("documento", "salvarLancamentos", objParametros);
    if (arrEnviar.status == true){
        tblDocumentosLancamento.clear().destroy();
        fcCarregarGridArquivos();
    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
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
            $("#metodos_pagamento_pk").val(arrCarregar.data['metodos_pagamento_pk'])
            $("#dt_faturamento1").val(arrCarregar.data['dt_faturamento'])
            $("#dt_vencimento1").val(arrCarregar.data['dt_vencimento'])
            $("#vl_lancamento1").val(float2moeda(arrCarregar.data['vl_lancamento']))
            $("#empresa_lancamento_pk").val(arrCarregar.data['empresa_lancamento_pk'])
            fcCarregarContasBancariasLancamento();
            $("#contas_bancarias_pk").val(arrCarregar.data['contas_bancarias_pk'])
            $("#ic_status_lancamento").val(arrCarregar.data['ic_status_lancamento'])
            $("#dt_pagamento").val(arrCarregar.data['dt_pagamento'])
            $("#obs_lancamento").val(arrCarregar.data['obs_lancamento'])
            
            if ($("#tipo_grupo_pk").val() == 1){
                $("#div_cliente_lancamento_pk").hide();
                fcCarregarLeads();
                $("#grupo_lancamento_pk").val(arrCarregar.data['grupo_lancamento_pk'])
                $("#grupo_lancamento_pk").select2();

                fcCarregarLeadsPostosTrabalho();
                $("#posto_trabalho_lancamento_pk").val(arrCarregar.data['posto_trabalho_lancamento_pk'])
                $("#posto_trabalho_lancamento_pk").select2();
            

                fcCarregarLeadsContratos();
                $("#contratos_pk").val(arrCarregar.data['contratos_pk'])
                $("#contratos_pk").select2();
                
        
            } else if($("#tipo_grupo_pk").val() == 2 ){
                $("#div_cliente_lancamento_pk").show();
                fcCarregarColaborador();

                $("#grupo_lancamento_pk").val(arrCarregar.data['grupo_lancamento_pk'])
                $("#grupo_lancamento_pk").select2();

                fcCarregarDadosBancariosColaborador();
            
                fcCarregarColaboradoresClientes();
                $("#cliente_lancamento_pk").val(arrCarregar.data['cliente_lancamento_pk'])
                $("#cliente_lancamento_pk").select2();

                fcCarregarColaboradorPostosTrabalho();
                $("#posto_trabalho_lancamento_pk").val(arrCarregar.data['posto_trabalho_lancamento_pk'])
                $("#posto_trabalho_lancamento_pk").select2();
                
                fcCarregarColaboradorContratos();
                $("#contratos_pk").val(arrCarregar.data['contratos_pk'])
                $("#contratos_pk").select2();
                
        
            }else if($("#tipo_grupo_pk").val() == 3){
                $("#div_cliente_lancamento_pk").show();
                fcCarregarFornecedor();
                $("#grupo_lancamento_pk").val(arrCarregar.data['grupo_lancamento_pk'])
                $("#grupo_lancamento_pk").select2();

                fcCarregarClientesFornecedor();
                $("#cliente_lancamento_pk").val(arrCarregar.data['cliente_lancamento_pk'])
                $("#cliente_lancamento_pk").select2();

                fcCarregarFornecedorPostosTrabalho();
                $("#posto_trabalho_lancamento_pk").val(arrCarregar.data['posto_trabalho_lancamento_pk'])
                $("#posto_trabalho_lancamento_pk").select2();
                fcCarregarFornecedorContratos();
                $("#contratos_pk").val(arrCarregar.data['contratos_pk'])
                $("#contratos_pk").select2();
            } 
        }
    }
}

function fcpermissaoStatusPago() {
    var objParametros = {
        "ds_dominio_modulo": "status_finaceiro",
        "ic_acao": "upd"
    };
    var arrCarregar = permissaoAtualizada("usuario", "verificarPermissao", objParametros);
    
    if (arrCarregar.result != 'success') {
        $("#exibir_pago").hide();
    }
    else {
        $("#exibir_pago").show();
    }
}

function fclimpaFormLancamento() {
    $("#lancamento_pk").val("");
    $("#ds_lancamento").val("");
    $("#tipo_lancamento_pk").val("");
    $("#categorias_financeiras_pk").val("");
    $("#tipos_operacao_pk").val("");
    $("#tipo_grupo_pk").val("");
    $("#leads_clientes_pk").val("");
    $("#contratos_pk").val("");
    $("#colaborador_pk").val("");
    $("#colaborador_posto_trabalho_pk").val("");
    $("#fornecedor_pk").val("");
    $("#fornecedor_posto_trabalho_pk").val("");
    $("#fornecedor_contratos_pk").val("");
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
    $("#grupo_lancamento_centro_custo_fornecedor_pk").val("");
    $("#grupo_lancamento_centro_custo_colaborador_pk").val("");   
    $("#qtde_parcelas_pk").val("1");
    $("#ds_parcela").text("1");
    $("#div_datas_valores_pagamento").append("");
    $("#ds_num_documento").val("");
    $("#ic_tipo_num_documento").val("");
    $("#div_datas_valores_pagamento").empty();
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
    var objParametros = {};
    var arrCarregar = carregarController("categoria_financeira", "listarTodos", objParametros);
    carregarComboAjax($("#categorias_financeiras_pk"), arrCarregar, " ", "pk", "ds_categoria");
}

function fcCarregarEmpresaContaLancamento() {
    var objParametros = {};
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);
    carregarComboAjax($("#empresa_lancamento_pk"), arrCarregar, " ", "pk", "ds_conta");
}

function fcCarregarTipoPlanoNegocio() {
    var objParametros = {
        "categorias_financeiras_pk": $("#categorias_financeiras_pk").val()
    };
    var arrCarregar = carregarController("plano_contas", "listaPorCategoria", objParametros);
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
        fcCarregarLeads();
        
        $("#grupo_lancamento_pk").select2();
        $("#grupo_lancamento_pk").change(function () {
            fcCarregarLeadsPostosTrabalho();
            $("#posto_trabalho_lancamento_pk").select2();
        });
        $("#posto_trabalho_lancamento_pk").change(function () {
            fcCarregarLeadsContratos();
            $("#contratos_pk").select2();
        });

    } else if($("#tipo_grupo_pk").val() == 2 ){
        $("#div_cliente_lancamento_pk").show();
        fcCarregarColaborador();
        $("#grupo_lancamento_pk").select2();
        $("#grupo_lancamento_pk").change(function () {
            fcCarregarDadosBancariosColaborador();
        });

        fcCarregarColaboradoresClientes();
        $("#cliente_lancamento_pk").select2();
        $("#cliente_lancamento_pk").change(function () {
            fcCarregarColaboradorPostosTrabalho();
            $("#posto_trabalho_lancamento_pk").select2();
        });
        $("#posto_trabalho_lancamento_pk").change(function () {
            fcCarregarColaboradorContratos();
            $("#contratos_pk").select2();
        });

    }else if($("#tipo_grupo_pk").val() == 3){
        $("#div_cliente_lancamento_pk").show();
        fcCarregarFornecedor();
        $("#grupo_lancamento_pk").select2();
        fcCarregarClientesFornecedor();
        $("#cliente_lancamento_pk").select2();
        $("#cliente_lancamento_pk").change(function () {
            fcCarregarFornecedorPostosTrabalho();
            $("#posto_trabalho_lancamento_pk").select2();
        });
        $("#posto_trabalho_lancamento_pk").change(function () {
            fcCarregarFornecedorContratos();
            $("#contratos_pk").select2();
        });
    } 
}

//Combos clientes
function fcCarregarLeads() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#grupo_lancamento_pk"), arrCarregar, " ", "pk", "ds_lead");
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
function fcCarregarColaborador() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_lancamento_pk"), arrCarregar, " ", "pk", "ds_colaborador");
}

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
    var objParametros = {
        "leads_pk": $("#posto_trabalho_lancamento_pk").val()
    };
    var arrCarregar = carregarController("contrato", "listaLeadContratos", objParametros);
    
    carregarComboAjax($("#contratos_pk"), arrCarregar, " ", "pk", "ds_contrato");
}

function fcCarregarMetodosPagamentoReceita() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("metodo_pagamento", "listarTodos", objParametros);
    carregarComboAjax($("#metodos_pagamento_pk"), arrCarregar, " ", "pk", "ds_metodo_pagamento");
}

function fcValidarLancamentoUsuario() {

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
    if ($('#grupo_lancamento_pk').val() == "") {
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
    }
    fcEnviarLancamento();
}

function fcEnviarLancamento() {
    let doc_lancamento = fcFormatarDadosArquivos();
    let arrParcelas = fcMontarArrParcelas();

    let v_pk = $("#lancamento_pk").val();
    let v_ds_lancamento = $("#ds_lancamento").val();
    let v_tipo_lancamento_pk = $("#tipo_lancamento_pk").val();
    let v_categorias_financeiras_pk = $("#categorias_financeiras_pk").val();
    let v_tipos_operacao_pk = $("#tipos_operacao_pk").val();
    let v_tipo_grupo_pk = $("#tipo_grupo_pk").val();
    let v_grupo_lancamento_pk = $("#grupo_lancamento_pk").val();
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
    

    var objParametros = {
        "pk": v_pk,    
        "ds_lancamento": v_ds_lancamento,    
        "tipo_lancamento_pk": v_tipo_lancamento_pk,    
        "categorias_financeiras_pk": v_categorias_financeiras_pk,    
        "tipos_operacao_pk": v_tipos_operacao_pk,    
        "tipo_grupo_pk": v_tipo_grupo_pk,    
        "grupo_lancamento_pk": v_grupo_lancamento_pk,    
        "cliente_lancamento_pk": v_cliente_lancamento_pk,    
        "posto_trabalho_lancamento_pk": v_posto_trabalho_lancamento_pk,    
        "contratos_pk": v_contratos_pk,    
        "metodos_pagamento_pk": v_metodos_pagamento_pk,    
        "empresa_lancamento_pk": v_empresa_lancamento_pk,    
        "contas_bancarias_pk": v_contas_bancarias_pk,    
        "ic_status_lancamento": v_ic_status_lancamento,    
        "dt_pagamento": v_dt_pagamento,    
        "obs_lancamento": v_obs_lancamento,    
        "ds_num_documento": v_ds_num_documento,    
        "ic_tipo_num_documento": v_ic_tipo_num_documento,    
        "qtde_parcelas_pk": v_qtde_parcelas_pk,  
        "arrParcelas": arrParcelas,
        "doc_lancamento": doc_lancamento
    };

    var arrEnviar = carregarController("lancamento", "salvar", objParametros);
    // Reload datable
    if (arrEnviar.status == true){
        let pk =  arrEnviar.data;
        utilsJS.toastNotify(true,arrEnviar.message)
        utilsJS.sweetMensagem(true, pk);

        $("#event-modal").modal("hide");

    }
    

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

function fcArrayDatasVlPagamento(){
    $("#div_datas_valores_pagamento").append("");
    $("#div_datas_valores_pagamento").empty();

    if($("#qtde_parcelas_pk").val()>1){

        var li = $("#qtde_parcelas_pk").val();
        var v_linha = 1;
        var str = "";


        if($("#dt_faturamento1").val()==''){
            sweetMensagem('warning','Preencha o campo Dt Faturamento');
            $("#qtde_parcelas_pk").val("1")
            return false;
        }   
        
        if($("dt_vencimento1").val()==''){
            sweetMensagem('warning','Preencha o campo Dt Vencimento');
            $("#qtde_parcelas_pk").val("1")
            return false;
        }       
        if($("#vl_lancamento1").val()==''){
            sweetMensagem('warning','Preencha o campo Vl Lancamento!');
            $("#qtde_parcelas_pk").val("1")
            return false;
        }
        //Separa a data do valor de faturamento      
        var dt_faturamento = $("#dt_faturamento1").val()  

        //Separa a data do valor de vencimento 
        var dt_vencimento = $("#dt_vencimento1").val().split("/")     
        var d_vencimento = new Date(dt_vencimento[2],dt_vencimento[1],dt_vencimento[0]);// 31 de janeiro de 2016
        var v_dia_vencimento = d_vencimento.getDate();
            v_dia_vencimento = v_dia_vencimento<10?"0"+v_dia_vencimento:v_dia_vencimento;

        if(d_vencimento.getMonth()=="11"){
            var v_mes_vencimento = "1";    
            var v_ano_vencimento = d_vencimento.getFullYear()+1;
        }else{
            var v_mes_vencimento = d_vencimento.getMonth()+1;
            var v_ano_vencimento = d_vencimento.getFullYear();
        }
        
        var vl_lancamento = float2moeda($("#vl_lancamento1").val());

        for (i = 1; i < li; i++) {  

            v_mes_vencimento = v_mes_vencimento<10?"0"+v_mes_vencimento:v_mes_vencimento;
            var v_dt_vencimento_meses =v_dia_vencimento+"/"+v_mes_vencimento+"/"+v_ano_vencimento;
            v_linha = i + 1;

            str += "    <tr>";
            str += "        <td >";
            str += "            Parcela " +v_linha; 
            str += "            <input type='hidden' id='parcela_pk"+v_linha+"' value='"+v_linha+"' />"; 
            str += "        </td>";
            str += "        <td>";
            str += "            <input type='text' class='form-control form-control-sm' id='dt_faturamento"+v_linha+"' name='dt_faturamento' onkeypress='mascara(this,mdata)' maxlength='10' value="+dt_faturamento+" />";
            str += "        </td>";
            str += "        <td>";
            str += "            <input type='text' class='form-control form-control-sm' id='dt_vencimento"+v_linha+"' name='dt_vencimento' onkeypress='mascara(this,mdata)' maxlength='10' value="+v_dt_vencimento_meses+" />";
            str += "        </td>";
            str += "        <td>";
            str += "            <input type='text' class='form-control form-control-sm' id='vl_lancamento"+v_linha+"' name='vl_lancamento' onkeypress='mascara(this,moeda)' value='"+vl_lancamento+"'/>";
            str += "        </td>";
            str += "    </tr>";
            v_linha ++;


            if(v_mes_vencimento==12){
                v_mes_vencimento ="1";
                v_ano_vencimento ++;
            }else{
                v_mes_vencimento++;
            }
            
        }   
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

//Documentos
function fcCarregarGridArquivos(){
    try {
        if($('#lancamento_pk').val()!=""){

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
                            return full['t_pk'];
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
                            var buttonDelete = "<i class='fa fa-download function_download' style='font-size:12px; color:blue' title='DOWNLOAD DOCUMENTO'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='fa fa-trash function_delete' style='font-size:12px; color:black' title='EXCLUIR O DOCUMENTO'></i>";
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
                fcDownloadDocumento(data['pk_doc_bd'],data['t_ds_documento']);
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
                    fcExcluirDocumentoGridLancamento(data['t_pk'],data['pk_doc_bd']);
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

        }
    return false;
    } catch (error) {
        utilsJS.toastNotify(false,error);
    }
}

function fcExcluirDocumentoGridLancamento(v_pk,v_pk_doc){
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
            //fcExcluirArquivo(v_ds_documento);
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
//COMEÇO DOCUMENTOS UPLOAD

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
function fcApagarArquivo(){

    fcExcluirArquivo($("#pk_documento_bd").text())
    tblDocumentosLancamento.row($(this).parents('tr')).remove().draw();
}
function fcExcluirArquivo(v_nome_arquivo){
    var objParametros = {
        "pk_doc_bd": v_nome_arquivo
    };
    carregarController("documento", "excluirDocBd", objParametros);
}
function fecharModal(){
    $("#event-modal").modal("hide");
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

