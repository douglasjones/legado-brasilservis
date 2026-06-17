var tblResultado;


function fcCarregarUsuarioGridLancamento() {
    let v_cod = $("#pk_lancamento_lancamento_pesq").val();
    let v_ic_status = $("#ic_status_pagamento_lancamento_pesq").val();
    let v_usuario_cadastro_pk = $("#usuario_cadastro_lancamento_pk_pesq").val();
    let v_empresa_pk = $("#empresa_lancamento_pk_pesq").val();
    let v_contas_pk = $("#contas_lancamento_pk_pesq").val();
    let v_tipo_grupo_pk = $("#tipo_grupo_lancamento_pk_pesq").val();
    let v_grupo_lancamento_pk = $("#grupo_lancamento_pk_pesq").val();
    let v_dt_cadastro_ini = $("#dt_cadastro_ini_lancamento_pesq").val();
    let v_dt_cadastro_fim = $("#dt_cadastro_fim_lancamento_pesq").val();
    let v_dt_faturamento_ini = $("#dt_faturamento_ini_lancamento_pesq").val();
    let v_dt_faturamento_fim = $("#dt_faturamento_fim_lancamento_pesq").val();

    let v_dt_vencimento_ini = $("#dt_vencimento_ini_lancamento_pesq").val();
    let v_dt_vencimento_fim = $("#dt_vencimento_fim_lancamento_pesq").val();
    let v_dt_pagamento_ini = $("#dt_pagamento_ini_lancamento_pesq").val();
    let v_dt_pagamento_fim = $("#dt_pagamento_fim_lancamento_pesq").val();
    let v_ic_status_analise = $("#ic_status_analise_pesq").val();
    let v_ds_num_documento_gridUsuario = $("#ds_num_documento_pesq").val();
    let v_ic_tipo_num_documento_gridUsuario = $("#ic_tipo_num_documento_pesq").val();


    /*let hoje = new Date();
    let dia = hoje.getDate();
    let mes = hoje.getMonth() + 1;
    let ano = hoje.getFullYear();
    let data = dia + '/' + mes + '/' + ano;

    let v_ano = ano;
    let v_mes = mes;*/

    let objParametros = {
        "pk": v_cod,
        "ic_status_pagamento": v_ic_status,
        "usuario_cadastro_pk": v_usuario_cadastro_pk,
        "empresas_pk": v_empresa_pk,
        "contas_pk": v_contas_pk,
        "tipo_grupo_pk": v_tipo_grupo_pk,
        "grupo_lancamento_pk": v_grupo_lancamento_pk,
        "dt_cadastro_ini": v_dt_cadastro_ini,
        "dt_cadastro_fim": v_dt_cadastro_fim,
        "dt_faturamento_ini": v_dt_faturamento_ini,
        "dt_faturamento_fim": v_dt_faturamento_fim,
        "dt_vencimento_ini": v_dt_vencimento_ini,
        "dt_vencimento_fim": v_dt_vencimento_fim,
        "dt_pagamento_ini": v_dt_pagamento_ini,
        "dt_pagamento_fim": v_dt_pagamento_fim,
        "ic_status_analise": v_ic_status_analise,
        "ds_num_documento": v_ds_num_documento_gridUsuario,
        "ic_tipo_num_documento": v_ic_tipo_num_documento_gridUsuario
    };

    var v_url = routes_api("lancamento", "listarLancamentosUsuarios", objParametros);

    //Trata a tabela
    tblResultado = $("#tblResultado").DataTable({
        searching: true,
        paging: true,
        scrollX: true,
        iDisplayLength: 10,
        processing: false,
        serverSide: true,
        ajax: v_url,
        responsive: true,
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },
        order: [
            [0, "asc"]
        ],
        columns: [
            {
                mRender: function (data, type, full) {
                    return full['pk'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_lancamento'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_usuario'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_empresa'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_conta_bancaria'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_faturamento'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_vencimento'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_pagamento'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_status_pagamento'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_operacao'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_tipo_operacao'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_tipo_grupo'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_recebido_pago_origem'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_metodo_pagamento'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return float2moeda(full['vl_lancamento']);
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return float2moeda(full['vl_lancamento_pendente']);
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_status_analise'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="editar"></i></span></a> ';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';
                

                    return buttonPainel + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '60px'
            }
        ]
    });

    //Atribui os eventos na coluna ação.

    $('#tblResultado tbody').on('click', '.function_edit', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcAbrirCadastroLancamento(data['pk']);

    });

    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcExcluirLancamentoUsuario(data['pk']);
    });


}

/*function fcCarregarUsuarioGridLancamento() {
    try {
        let v_cod = $("#pk_lancamento_lancamento_pesq").val();
        let v_ic_status = $("#ic_status_pagamento_lancamento_pesq").val();
        let v_usuario_cadastro_pk = $("#usuario_cadastro_lancamento_pk_pesq").val();
        let v_empresa_pk = $("#empresa_lancamento_pk_pesq").val();
        let v_contas_pk = $("#contas_lancamento_pk_pesq").val();
        let v_tipo_grupo_pk = $("#tipo_grupo_lancamento_pk_pesq").val();
        let v_grupo_lancamento_pk = $("#grupo_lancamento_pk_pesq").val();
        let v_dt_cadastro_ini = $("#dt_cadastro_ini_lancamento_pesq").val();
        let v_dt_cadastro_fim = $("#dt_cadastro_fim_lancamento_pesq").val();
        let v_dt_faturamento_ini = $("#dt_faturamento_ini_lancamento_pesq").val();
        let v_dt_faturamento_fim = $("#dt_faturamento_fim_lancamento_pesq").val();
    
        let v_dt_vencimento_ini = $("#dt_vencimento_ini_lancamento_pesq").val();
        let v_dt_vencimento_fim = $("#dt_vencimento_fim_lancamento_pesq").val();
        let v_dt_pagamento_ini = $("#dt_pagamento_ini_lancamento_pesq").val();
        let v_dt_pagamento_fim = $("#dt_pagamento_fim_lancamento_pesq").val();
        let v_ic_status_analise = $("#ic_status_analise_pesq").val();
        let v_ds_num_documento_gridUsuario = $("#ds_num_documento_pesq").val();
        let v_ic_tipo_num_documento_gridUsuario = $("#ic_tipo_num_documento_pesq").val();
    
    
        /*let hoje = new Date();
        let dia = hoje.getDate();
        let mes = hoje.getMonth() + 1;
        let ano = hoje.getFullYear();
        let data = dia + '/' + mes + '/' + ano;
    
        let v_ano = ano;
        let v_mes = mes;*/
    
        /*let objParametros = {
            "pk": v_cod,
            "ic_status_pagamento": v_ic_status,
            "usuario_cadastro_pk": v_usuario_cadastro_pk,
            "empresas_pk": v_empresa_pk,
            "contas_pk": v_contas_pk,
            "tipo_grupo_pk": v_tipo_grupo_pk,
            "grupo_lancamento_pk": v_grupo_lancamento_pk,
            "dt_cadastro_ini": v_dt_cadastro_ini,
            "dt_cadastro_fim": v_dt_cadastro_fim,
            "dt_faturamento_ini": v_dt_faturamento_ini,
            "dt_faturamento_fim": v_dt_faturamento_fim,
            "dt_vencimento_ini": v_dt_vencimento_ini,
            "dt_vencimento_fim": v_dt_vencimento_fim,
            "dt_pagamento_ini": v_dt_pagamento_ini,
            "dt_pagamento_fim": v_dt_pagamento_fim,
            "ic_status_analise": v_ic_status_analise,
            "ds_num_documento": v_ds_num_documento_gridUsuario,
            "ic_tipo_num_documento": v_ic_tipo_num_documento_gridUsuario
        };
    
        var arrCarregar = carregarController("lancamento", "listarLancamentosUsuarios", objParametros);
        if(arrCarregar.data.length > 0){


            $('#tblResultado').append("<tbody></tbody>");
            for(let i=0; i<arrCarregar.data.length; i++){

                $('#tr'+i).html("");
                $('#tabela'+i).html("");

                $('#tblResultado tbody').append("<tr id='tr"+i+"'></tr>");
                let pk = arrCarregar.data[i].pk;
                
                let ds_empresa = arrCarregar.data[i].ds_empresa;
                    ds_empresa = ds_empresa == null?'':ds_empresa;

                let ds_conta_bancaria = arrCarregar.data[i].ds_conta_bancaria;
                    ds_conta_bancaria = ds_conta_bancaria == null?'':ds_conta_bancaria;

                let ds_recebido_pago_origem = arrCarregar.data[i].ds_recebido_pago_origem;
                    ds_recebido_pago_origem = ds_recebido_pago_origem == null?'':ds_recebido_pago_origem;
                    
                let ic_status_pagamento = arrCarregar.data[i].ic_status_pagamento;

                let dt_pagamento = arrCarregar.data[i].dt_pagamento;
                    dt_pagamento = dt_pagamento == null?'':dt_pagamento;

                let ds_operacao = arrCarregar.data[i].ds_operacao;
                    ds_operacao = ds_operacao == null?'':ds_operacao;

                let ds_status_analise = arrCarregar.data[i].ds_status_analise;
                    ds_status_analise = ds_status_analise == null?'':ds_status_analise;

                $('#tr'+i).append("<td>"+pk+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].ds_lancamento+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].dt_cadastro+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].ds_usuario+"</td>");  
                $('#tr'+i).append("<td>"+ds_empresa+"</td>");  
                $('#tr'+i).append("<td>"+ds_conta_bancaria+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].dt_faturamento+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].dt_vencimento+"</td>");  
                $('#tr'+i).append("<td>"+dt_pagamento+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].ds_status_pagamento+"</td>");  
                $('#tr'+i).append("<td>"+ds_operacao+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].ds_tipo_operacao+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].ds_tipo_grupo+"</td>");  
                $('#tr'+i).append("<td>"+ds_recebido_pago_origem+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].ds_metodo_pagamento+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].vl_lancamento_dia+"</td>");  
                $('#tr'+i).append("<td>"+arrCarregar.data[i].vl_lancamento_pendente_dia+"</td>");  
                $('#tr'+i).append("<td>"+ds_status_analise+"</td>");  
                $('#tr'+i).append("<td id='acao"+i+"'><div id='tabela"+i+"'></div></td>");  

                if(ds_status_analise == "Aprovado Analista" || ds_status_analise == "Aprovado Gestor"){
                    $('#tabela'+i+' tr').append("<td></td><i style='font-size: 15px;' id='cmdEditar' class='bi bi-pencil-square' title='Editar Lançamento' onclick='alert(&apos;Lançamento Já foi Aprovado e não pode ser alterado!&apos;); return false;'></i></td>");
                }else if(ds_status_analise == "Recusado"){
                    $('#tabela'+i+' tr').append("<td><i style='font-size: 15px;' id='cmdEditar' class='bi bi-pencil-square' title='Editar Lançamento' onclick='alert(&apos;Lançamento Já foi Recusado e não pode ser alterado!&apos;); return false;'></i></td>");
                }else{
                    $('#tabela'+i).append("<div class='row'><div class='col-md-4'><i style='font-size: 18px;color:blue;cursor:pointer' id='cmdEditar' class='bi bi-pencil-square' title='Editar Lançamento' onclick='fcAbrirCadastroLancamento(" + pk + ")' ></i></div>");
                } 
                //permissao de impressao do lançamento
                //var arrCarregarImpressao = permissao("lancamento_acesso_impressao", "cons");            
                //if (arrCarregarImpressao.result == 'success'){  
                    //$('#tabela'+i).append("<div><i style='font-size: 15px;' id='cmdImprimir' class='bi bi-printer' title='Imprimir Lançamento' onclick='fcImprimirLancamentoUsuario(" + pk + ")'></i></div>");
                //}
                $('#tabela'+i).append("<div class='col-md-4'><i style='font-size: 18px;color: blue;cursor: pointer' class='bi bi-file-earmark-arrow-up' title='Anexo de Documentos' onclick='fcAnexarDocumento(" + pk + ")' ></i></div>");
                $('#tabela'+i).append("<div class='col-md-4'><i style='font-size: 18px;color:blue;cursor:pointer' id='cmdExcluir' class='bi bi-x-circle' title='Excluir Lançamento' onclick='fcExcluirLancamentoUsuario(" + pk + "," + ic_status_pagamento + ")'></i></div></div>");

			    if(ds_status_analise == "Correção Solicitada" || ds_status_analise == "Recusado"){
                    $(' #tabela'+i+' tr').append("<td><i style='font-size: 15px;' id='cmdObservacaoFinanceira' class='bi bi-card-text' title='Observação Financeira' onclick='fcObservacaoAnaliseFinanceira(&apos;"+pk+"&apos;, &apos;"+ds_status_analise+"&apos;)'></i></td>");
                }
            }
        }
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
}*/

function fcExcluirLancamentoUsuario(v_pk) {

    var arrCarregar = permissao("excluir_lancamentos", "del");
    if (arrCarregar.result != true) {
        utilsJS.toastNotify(false,'Você não tem permissão');
        return false;
    }

    if (confirm("Deseja realmente excluir o registro '" + v_pk + "'?")) {
        if (v_pk != "") {

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("lancamento", "excluir", objParametros);

            if (arrExcluir.status == true) {

                //Exibe a mensagem
                utilsJS.toastNotify(true,arrExcluir.message);
                location.reload();
            }
            else {
                utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
            }
        }
        else {
            utilsJS.toastNotify(false,"Código não encontrado");
        }

    }
    return false;
}


function fcPesquisarLancamento() {
    tblResultado.clear().destroy();
    fcCarregarUsuarioGridLancamento();
}

function fcCarregarEmpresaContaPesquisa() {
    var objParametros = {};
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);
    carregarComboAjax($("#empresa_lancamento_pk_pesq"), arrCarregar, " ", "pk", "ds_conta");
}


function fcCarregarContasBancariasPesquisa() {
    var objParametros = {
        "empresas_pk": $("#empresa_lancamento_pk_pesq").val()
    };
    var arrCarregar = carregarController("conta_bancaria", "listaPorEmpresa", objParametros);
    carregarComboAjax($("#contas_lancamento_pk_pesq"), arrCarregar, " ", "pk", "ds_conta");
}

function fcListarGrupo() {
    if ($("#tipo_grupo_lancamento_pk_pesq").val() == 1){
        fcCarregarLeadsPesq();

    } else if($("#tipo_grupo_lancamento_pk_pesq").val() == 2 ){
        fcCarregarColaboradorPesq();

    }else if($("#tipo_grupo_lancamento_pk_pesq").val() == 3){
        fcCarregarFornecedorPesq();
    } 
}

function fcCarregarLeadsPesq() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_lancamento_pk_pesq"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarColaboradorPesq() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_lancamento_pk_pesq"), arrCarregar, " ", "pk", "ds_colaborador");
}

function fcCarregarFornecedorPesq() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("fornecedor", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_lancamento_pk_pesq"), arrCarregar, " ", "pk", "ds_fornecedor");
}

function fcCarregarUsuariosCadastro() {
    var usuario_logado_pk = $("#usuario_pk").val();

    var arrCarregarP = permissao("listar_usuarios_lancamentos", "cons");            
    if (arrCarregarP.status == true) {  
        var objParametros = {
            "pk": usuario_logado_pk
        };
        var arrCarregar = carregarController("usuario", "listarTodos", objParametros);
        carregarComboAjax($("#usuario_cadastro_lancamento_pk_pesq"), arrCarregar, "", "pk", "ds_usuario");
    }
    else{
        var objParametros = {
            "pk": usuario_logado_pk
        };
        var arrCarregar = carregarController("usuario", "listarPk", objParametros);
        carregarComboAjax($("#usuario_cadastro_lancamento_pk_pesq"), arrCarregar, "", "pk", "ds_usuario");
    
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost('menu','financeiro' ,objParametros);
}

$(document).ready(function () {

    //Combos
    fcCarregarEmpresaContaPesquisa();
    fcCarregarUsuariosCadastro();
    $("#empresa_lancamento_pk_pesq").select2();
    $("#empresa_lancamento_pk_pesq").change(function () {
        fcCarregarContasBancariasPesquisa();
        $("#contas_lancamento_pk_pesq").select2();
    });
    $("#ic_status_analise_pesq").select2();
    $("#ic_status_pagamento_lancamento_pesq").select2();

    $("#tipo_grupo_lancamento_pk_pesq").change(function () {
        fcListarGrupo();
        $("#grupo_lancamento_pk_pesq").select2();
    });

    //Mascaras 
    $('#dt_cadastro_ini_lancamento_pesq').datepicker({
        //startDate: "+id",
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()
    });
    $("#dt_cadastro_ini_lancamento_pesq").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_cadastro_fim_lancamento_pesq').datepicker({
        //startDate: "+0d",
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()
    });
    $("#dt_cadastro_fim_lancamento_pesq").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_faturamento_ini_lancamento_pesq').datepicker({
        //startDate: "+0d",
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()
    });
    $("#dt_faturamento_ini_lancamento_pesq").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_faturamento_fim_lancamento_pesq').datepicker({
        //startDate: "+0d",
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()
    });
    $("#dt_faturamento_fim_lancamento_pesq").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_vencimento_ini_lancamento_pesq').datepicker({
        //startDate: "+0d",
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()
    });
    $("#dt_vencimento_ini_lancamento_pesq").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_vencimento_fim_lancamento_pesq').datepicker({
        //startDate: "+0d",
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()
    });
    $("#dt_vencimento_fim_lancamento_pesq").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_pagamento_ini_lancamento_pesq').datepicker({
        //startDate: "+0d",
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()
    });
    $("#dt_pagamento_ini_lancamento_pesq").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_pagamento_fim_lancamento_pesq').datepicker({
        //startDate: "+0d",
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()
    });
    $("#dt_pagamento_fim_lancamento_pesq").keypress(function () {
        mascara(this, mdata);
    });

    fcCarregarUsuarioGridLancamento();
    fcCarregarGridArquivos();


    
    //Ações
    $("#cmdNovoLancamento").click(function () {
        fcAbrirCadastroLancamento('');
    });

    $(document).on('click', '#btnSalvarLancamentoUsuario', fcValidarLancamentoUsuario);
    $(document).on('click', '#btnSalvarLancamentoUsuario2', fcValidarLancamentoUsuario);
    $(document).on('click', '#cmdPesquisarLancamentosUsuarios', fcPesquisarLancamento);
    $(document).on('click', '#cmdVoltar', fcCancelar);
})