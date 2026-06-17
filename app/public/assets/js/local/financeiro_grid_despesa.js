let arrCarregarDespesa = [];
function fcCarregarComboEmpresasDespesa() {
    var objParametros = {};
    var arrCarregar = carregarController("conta_bancaria", "listarEmpresaContasAtivas", objParametros);
    carregarComboAjax($("#empresa_despesa_pk"), arrCarregar, " ", "pk", "ds_conta");
}

function fcCarregarComboContasDespesa() {
    var objParametros = {
        "empresas_pk": $("#empresa_despesa_pk").val()
    };
    var arrCarregar = carregarController("conta_bancaria", "listarContasLancamento", objParametros);
    carregarComboAjax($("#contas_despesa_pk"), arrCarregar, " ", "pk", "ds_dados_conta");
}

function fcCarregarLeadsDespesa() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#grupo_lancamento_despesa_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarCnpjLeadsDespesa() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listarCpfCnpjClientes", objParametros);
    carregarComboAjax($("#ds_cpf_cnpj_despesa"), arrCarregar, " ", "pk", "ds_cpf_cnpj");
}

function fcCarregarColaboradorDespesa() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_lancamento_despesa_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    carregarComboAjax($("#ds_cpf_cnpj_despesa"), arrCarregar, " ", "pk", "ds_cpf");
}

function fcCarregarFornecedorDespesa() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("fornecedor", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_lancamento_despesa_pk"), arrCarregar, " ", "pk", "ds_fornecedor");
}

function fcCarregarCpfCnpjFornecedorDespesa() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("fornecedor", "listarCpfCnpjFornecedor", objParametros);
    carregarComboAjax($("#ds_cpf_cnpj_despesa"), arrCarregar, " ", "pk", "ds_cpf_cnpj");
}

function fcListarGrupoDespesa() {
    if ($("#tipo_grupo_despesa_pk").val() == 1){
        fcCarregarLeadsDespesa();        
        fcCarregarCnpjLeadsDespesa();

    } else if($("#tipo_grupo_despesa_pk").val() == 2 ){
        fcCarregarColaboradorDespesa();

    }else if($("#tipo_grupo_despesa_pk").val() == 3){
        fcCarregarFornecedorDespesa();    
        fcCarregarCnpjFornecedorDespesa();
    } 

    $('#grupo_lancamento_despesa_pk').select2();
    $('#ds_cpf_cnpj_despesa').select2();
}

function fcExcluirLancamentoDespesa(v_pk) {
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("lancamento", "excluir", objParametros);

            if (arrExcluir.status == true){
                fcCarregarGridDespesa();
                utilsJS.toastNotify(true,arrExcluir.message)

            }else{

                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão ');
            }
        }
        else{
            utilsJS.toastNotify(false, 'Código não encontrado');
        }
    });
}

localStorage.getItem('date_range_filter_cadastro_despesa') ? localStorage.getItem('date_range_filter_cadastro_despesa') : (localStorage.setItem('date_range_filter_cadastro_despesa', ''));
localStorage.getItem('date_range_filter_faturamento_despesa') ? localStorage.getItem('date_range_filter_faturamento_despesa') : (localStorage.setItem('date_range_filter_faturamento_despesa', ''));
localStorage.getItem('date_range_filter_vencimento_despesa') ? localStorage.getItem('date_range_filter_vencimento_despesa') : (localStorage.setItem('date_range_filter_vencimento_despesa', ''));
localStorage.getItem('date_range_filter_pagamento_despesa') ? localStorage.getItem('date_range_filter_pagamento_despesa') : (localStorage.setItem('date_range_filter_pagamento_despesa', ''));
function fcListarMiniDashboardDespesa(){

    let objParametros = {
        'dt_vencimento': $.trim(localStorage.getItem('date_range_filter_vencimento_despesa')),
        "ic_receita_despesa":2
    };
    let arrCarregar = carregarController("lancamento", "listarDashboard", objParametros);
    $("#vencidosDespesa").text(float2moeda(arrCarregar.data['vencidos']));
    $("#vencidosHojeDespesa").text(float2moeda(arrCarregar.data['vencidosHoje']));
    $("#aVencerDespesa").text(float2moeda(arrCarregar.data['aVencer']));
    $("#recebidosDespesa").text(float2moeda(arrCarregar.data['recebidos']));
    $("#valorTotalDespesa").text(float2moeda(arrCarregar.data['valorTotal']));
    
}
function fcCarregarGridDespesa(){
    arrCarregarDespesa = [];
    fcListarMiniDashboardDespesa();
    $("#tblDespesa tbody").remove();
    let grupo_lancamento_despesa_pk = $('#grupo_lancamento_despesa_pk').val();
    if(grupo_lancamento_despesa_pk == ""){
        grupo_lancamento_despesa_pk = $('#ds_cpf_cnpj_despesa').val();
    }
    let objParametros = {
        'pk': $('#pk_lancamento_despesa').val(),
        'ic_status_lancamento': $('#ic_status_pagamento_despesa').val(),
        'usuario_cadastro_pk': $('#usuario_cadastro_despesa_pk').val(),
        'ds_lancamento': $('#ds_lancamento_despesa').val(),
        'ds_num_documento': $('#ds_num_documento_despesa').val(),
        'ic_tipo_num_documento': $('#ic_tipo_num_documento_despesa').val(),
        'tipo_grupo_lancamento_pk': $('#tipo_grupo_despesa_pk').val(),
        'grupo_lancamento_pk': grupo_lancamento_despesa_pk,
        'dt_cadastro': $.trim(localStorage.getItem('date_range_filter_cadastro_despesa')),
        'dt_faturamento': $.trim(localStorage.getItem('date_range_filter_faturamento_despesa')),
        'dt_vencimento': $.trim(localStorage.getItem('date_range_filter_vencimento_despesa')),
        'dt_pagamento': $.trim(localStorage.getItem('date_range_filter_pagamento_despesa')),
        'empresas_pk': $('#empresa_despesa_pk').val(),
        'tipo_lancamento_pk': $('#tipo_lancamento_pk_despesa').val(),
        'categorias_financeiras_pk': $('#categorias_financeiras_pk_despesa').val(),
        'tipos_operacao_pk': $('#tipos_operacao_pk_despesa').val(),
        'contas_bancarias_pk': $('#contas_despesa_pk').val()
    };
    arrCarregarDespesa = carregarController("lancamento", "listarDespesa", objParametros);
    let count = 1;
    let vl_despesa = 0.00;
    let vl_total_despesa_pendente = 0.00;
    if(arrCarregarDespesa.data.length > 0){
        $('#tblDespesa').append('<tbody></tbody>');

            vl_despesa = arrCarregarDespesa.data[0]['vl_total_despesa'];
            vl_total_despesa_pendente = arrCarregarDespesa.data[0]['vl_total_despesa_pendente'];
            vl_total = arrCarregarDespesa.data[0]['vl_total'];
            vl_total_saldo_mes = arrCarregarDespesa.data[0]['vl_total_saldo_mes'];
            vl_saldo_mes_anterior = arrCarregarDespesa.data[0]['vl_saldo_mes_anterior'];
            vl_saldo_atual = arrCarregarDespesa.data[0]['vl_saldo_atual'];
            for (i = 0; i < arrCarregarDespesa.data[0].DadosDespesa.length; i++) {
                let v_pk = "";
                let v_dt_cadastro = "";
                let v_ds_usuario = "";
                let v_dt_faturamento = "";
                let v_dt_vencimento = "";
                let v_dt_pagamento = "";
                let v_ds_operacao = "";
                let v_ds_tipo_operacao = "";
                let v_tipos_operacao_pk = "";
                let v_ds_metodo_pagamento = "";
                let v_vl_despesa = "";
                let v_vl_lancamento = "";
                let v_ds_status_pagamento = "";
                let nfse_pk = "";

                v_pk = arrCarregarDespesa.data[0].DadosDespesa[i].pk;
                v_proximo_dt_vencimento = arrCarregarDespesa.data[0].DadosDespesa[i].proxima_data;
                nfse_pk = arrCarregarDespesa.data[0].DadosDespesa[i].nfse_pk;

                v_dt_cadastro = arrCarregarDespesa.data[0].DadosDespesa[i].dt_cadastro;
                v_dt_cadastro = v_dt_cadastro == null ? '' : v_dt_cadastro;
                
                v_ds_usuario = arrCarregarDespesa.data[0].DadosDespesa[i].ds_usuario;
                v_ds_usuario = v_ds_usuario == null ? '' : v_ds_usuario;
                
                v_ds_empresa = arrCarregarDespesa.data[0].DadosDespesa[i].ds_empresa;
                v_ds_empresa = v_ds_empresa == null ? '' : v_ds_empresa;
                
                v_ds_conta_bancaria = arrCarregarDespesa.data[0].DadosDespesa[i].ds_conta_bancaria;
                v_ds_conta_bancaria = v_ds_conta_bancaria == null ? '' : v_ds_conta_bancaria;

                v_dt_faturamento = arrCarregarDespesa.data[0].DadosDespesa[i].dt_faturamento;
                v_dt_faturamento = v_dt_faturamento == null ? '' : v_dt_faturamento;

                v_dt_vencimento = arrCarregarDespesa.data[0].DadosDespesa[i].dt_vencimento;
                v_dt_vencimento = v_dt_vencimento == null ? '' : v_dt_vencimento;

                v_dt_pagamento = arrCarregarDespesa.data[0].DadosDespesa[i].dt_pagamento;
                v_dt_pagamento = v_dt_pagamento == null ? '' : v_dt_pagamento;

                v_ds_operacao = arrCarregarDespesa.data[0].DadosDespesa[i].ds_operacao;
                v_ds_operacao = v_ds_operacao == null ? '' : v_ds_operacao;
                
                v_ds_tipo_operacao = arrCarregarDespesa.data[0].DadosDespesa[i].ds_tipo_operacao;
                v_ds_tipo_operacao = v_ds_tipo_operacao == null ? '' : v_ds_tipo_operacao;

                v_ds_tipo_grupo = arrCarregarDespesa.data[0].DadosDespesa[i].ds_tipo_grupo;
                v_ds_tipo_grupo = v_ds_tipo_grupo == null ? '' : v_ds_tipo_grupo;

                v_ds_recebido_pago_origem = arrCarregarDespesa.data[0].DadosDespesa[i].ds_recebido_pago_origem;
                v_ds_recebido_pago_origem = v_ds_recebido_pago_origem == null ? '' : v_ds_recebido_pago_origem;

                v_ds_metodo_pagamento = arrCarregarDespesa.data[0].DadosDespesa[i].ds_metodo_pagamento;
                v_ds_metodo_pagamento = v_ds_metodo_pagamento == null ? '' : v_ds_metodo_pagamento;

                v_vl_lancamento = arrCarregarDespesa.data[0].DadosDespesa[i].vl_lancamento;
                v_vl_lancamento = v_vl_lancamento == null ? '' : v_vl_lancamento;

                v_tipos_operacao_pk = arrCarregarDespesa.data[0].DadosDespesa[i].tipos_operacao_pk;
                v_tipos_operacao_pk = v_tipos_operacao_pk == null ? '' : v_tipos_operacao_pk;
                
                if(v_vl_lancamento != null && v_tipos_operacao_pk != 1){
                    v_vl_despesa = v_vl_lancamento;
                }

                v_ds_status_pagamento = arrCarregarDespesa.data[0].DadosDespesa[i].ds_status_pagamento;
                v_ds_status_pagamento = v_ds_status_pagamento == null ? '' : v_ds_status_pagamento;

                total_dia = arrCarregarDespesa.data[0].DadosDespesa[i].total_dia;
                total_dia = total_dia == null ? '' : total_dia;

                vl_despesa_dia = arrCarregarDespesa.data[0].DadosDespesa[i].vl_despesa_dia;
                vl_despesa_dia = vl_despesa_dia == null ? '' : vl_despesa_dia;
                total_dia = total_dia == null ? '' : total_dia;

                vl_despesa_pendente_dia = arrCarregarDespesa.data[0].DadosDespesa[i].vl_despesa_pendente_dia;
                vl_despesa_pendente_dia = vl_despesa_pendente_dia == null ? '' : vl_despesa_pendente_dia;

                vl_lancamento_pendente = arrCarregarDespesa.data[0].DadosDespesa[i].vl_lancamento_pendente;
                vl_lancamento_pendente = vl_lancamento_pendente == null ? '' : vl_lancamento_pendente;

                vl_total_saldo_dia = arrCarregarDespesa.data[0].DadosDespesa[i].vl_total_saldo_dia;
                vl_total_saldo_dia = vl_total_saldo_dia == null ? '' : vl_total_saldo_dia;

                ds_lancamento = arrCarregarDespesa.data[0].DadosDespesa[i].ds_lancamento;
                ds_lancamento = ds_lancamento == null ? '' : ds_lancamento;

                ds_cpf_cnpj = arrCarregarDespesa.data[0].DadosDespesa[i].ds_cpf_cnpj;
                ds_cpf_cnpj = ds_cpf_cnpj == null ? '' : ds_cpf_cnpj;

                $('#tblDespesa tbody').append('<tr id="tblDespesaTr'+i+'"></tr>');
                //$('#tblDespesaTr'+i).append('<td>'+v_pk+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+ds_lancamento+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_dt_cadastro+'</td>');
                // $('#tblDespesaTr'+i).append('<td>'+v_ds_usuario+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_ds_empresa+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_ds_conta_bancaria+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_dt_faturamento+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_dt_vencimento+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_dt_pagamento+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_ds_status_pagamento+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_ds_operacao+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_ds_tipo_operacao+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_ds_tipo_grupo+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+v_ds_recebido_pago_origem+'</td>');
                $('#tblDespesaTr'+i).append('<td>'+ds_cpf_cnpj+'</td>');
                //$('#tblDespesaTr'+i).append('<td>'+v_ds_metodo_pagamento+'</td>');
                $('#tblDespesaTr'+i).append('<td><font color="blue">'+v_vl_despesa+'</font></td>');
                $('#tblDespesaTr'+i).append('<td><font color="red">'+vl_lancamento_pendente+'</font></td>');
                $('#tblDespesaTr'+i).append('<td><div>\n\
                                                <div class="row">\n\
                                                    <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-pencil-square" title="Editar Lançamento" onclick="fcAbrirCadastroLancamento(' + v_pk + ')" ></i></div>\n\
                                                    <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-file-earmark-arrow-up" title="Anexo de Documentos" onclick="fcAnexarDocumento(' + v_pk + ')" ></i></div>\n\
                                                </div>\n\
                                                <div class="row">\n\
                                                    <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdImprimir" class="bi bi-printer" title="Imprimir Lançamento" onclick="fcImprimirLancamento(' + v_pk + ')"></i></div>\n\
                                                    <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdExcluir" class="bi bi-x-circle" title="Excluir Lançamento" onclick="fcExcluirLancamentoDespesa(' + v_pk + ')"></i></div>\n\
                                                    <div class="col-md-4" ' + (nfse_pk == null ? 'style="display: none;"' : '') + '><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-download" title="Download NFSE" onclick="fcDownloadNfseLancamento(' + nfse_pk + ')" ></i></div>\n\
                        </div></div></td>');                              
                

                if(v_dt_vencimento != v_proximo_dt_vencimento || v_proximo_dt_vencimento == ""){
                    console.log(v_proximo_dt_vencimento)
                    $('#tblDespesa tbody').append('<tr id="totaisDespesa'+i+'"></tr>');
                    $('#totaisDespesa'+i).append('<td align="right" width="100" colspan=12 >&nbsp;<b>Saldo do dia '+ v_dt_vencimento +'</b></td>');
                    $('#totaisDespesa'+i).append('<td width="100" ><b>Totais R$<b/></td>');
                    $('#totaisDespesa'+i).append('<td align="center"><font color="blue">' + vl_despesa_dia + '</font></td>');
                    $('#totaisDespesa'+i).append('<td align="center"><font color="red">' + vl_despesa_pendente_dia + '</font></td>');
                    $('#totaisDespesa'+i).append('<td align="center"></td>');
                }
                
                count++;
            }

            filtrarDadosDespesa($("#buscaGeralDespesa").val());
    }
    $('#totalDespesaLancamento').html(vl_despesa);
    $('#totalDespesaPendente').html(vl_total_despesa_pendente);
    }

    function fcCarregarUsuariosCadastroDespesa() {
    var objParametros = {};
    var arrCarregar = carregarController("usuario", "listarTodosSemAdm", objParametros);
    carregarComboAjax($("#usuario_cadastro_despesa_pk"), arrCarregar, " ", "pk", "ds_usuario");
    }


    function fcCarregarCategoriaOperacaoDespesa() {
    var objParametros = {};
    var arrCarregar = carregarController("categoria_financeira", "listarTodos", objParametros);
    carregarComboAjax($("#categorias_financeiras_pk_despesa"), arrCarregar, " ", "pk", "ds_categoria");
    }

    function fcCarregarTipoPlanoNegocioDespesa() {
    var objParametros = {
        "categorias_financeiras_pk": $("#categorias_financeiras_pk_despesa").val()
    };
    var arrCarregar = carregarController("plano_contas", "listaPorCategoria", objParametros);
    carregarComboAjax($("#tipos_operacao_pk_despesa"), arrCarregar, " ", "pk", "ds_tipo_operacao");
    }

    function fcCarregarFuncoesDespesa(){

    $(document).on('click', '#cmdPesqDespesa', fcCarregarGridDespesa);
    //Combo
    fcCarregarComboEmpresasDespesa();
    

    fcCarregarComboContasDespesa();
    $("#empresa_despesa_pk").change(function(){
        fcCarregarComboContasDespesa();
        $('#contas_despesa_pk').select2();
    });

    $("#tipo_grupo_despesa_pk").change(function(){
        fcListarGrupoDespesa();
    });

    fcCarregarUsuariosCadastroDespesa();
    fcCarregarCategoriaOperacaoDespesa();
    $("#categorias_financeiras_pk_despesa").change(function () {
        fcCarregarTipoPlanoNegocioDespesa();
        $("#tipos_operacao_pk_despesa").select2();
    });

    let today = new Date();
    let dd = today.getDate();
    let mm = today.getMonth()+1; //January is 0!
    let yyyy = today.getFullYear();
    //data
    if(dd<10) {dd = '0'+dd} 
    if(mm<10) {mm = '0'+mm} 

    today = dd + '/' + mm + '/' + yyyy;

    $('#dt_cadastro_ini_despesa').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_cadastro_ini_despesa").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_cadastro_fim_despesa').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_cadastro_fim_despesa").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_faturamento_ini_despesa').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_faturamento_ini_despesa").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_faturamento_fim_despesa').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_faturamento_fim_despesa").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_vencimento_ini_despesa').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_vencimento_ini_despesa").keypress(function(){
        mascara(this,mdata);
    });
    $("#dt_vencimento_ini_despesa").val(today);

    $('#dt_vencimento_fim_despesa').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_vencimento_fim_despesa").keypress(function(){
        mascara(this,mdata);
    });
    $("#dt_vencimento_fim_despesa").val(today);

    $('#dt_pagamento_ini_despesa').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_pagamento_ini_despesa").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_pagamento_fim_despesa').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_pagamento_fim_despesa").keypress(function(){
        mascara(this,mdata);
    });

    fcCarregarGridDespesa();
    
    

}


    function filtrarDadosDespesa(termoPesquisa) {

    // Limpa a tabela antes de filtrar os dados
    $('#tblDespesa tbody').empty();

    if (arrCarregarDespesa.data.length > 0) {
        let count =1;
        vl_despesa = arrCarregarDespesa.data[0]['vl_total_despesa'];
            vl_total_despesa_pendente = arrCarregarDespesa.data[0]['vl_total_despesa_pendente'];
            vl_total = arrCarregarDespesa.data[0]['vl_total'];
            vl_total_saldo_mes = arrCarregarDespesa.data[0]['vl_total_saldo_mes'];
            vl_saldo_mes_anterior = arrCarregarDespesa.data[0]['vl_saldo_mes_anterior'];
            vl_saldo_atual = arrCarregarDespesa.data[0]['vl_saldo_atual'];
            for (i = 0; i < arrCarregarDespesa.data[0].DadosDespesa.length; i++) {
                let v_pk = "";
                let v_dt_cadastro = "";
                let v_ds_usuario = "";
                let v_dt_faturamento = "";
                let v_dt_vencimento = "";
                let v_dt_pagamento = "";
                let v_ds_operacao = "";
                let v_ds_tipo_operacao = "";
                let v_tipos_operacao_pk = "";
                let v_ds_metodo_pagamento = "";
                let v_vl_despesa = "";
                let v_vl_lancamento = "";
                let v_ds_status_pagamento = "";
                let nfse_pk  = "";


                v_pk = arrCarregarDespesa.data[0].DadosDespesa[i].pk;
                v_proximo_dt_vencimento = arrCarregarDespesa.data[0].DadosDespesa[i].proxima_data;
                nfse_pk  = arrCarregarDespesa.data[0].DadosDespesa[i].nfse_pk ;

                v_dt_cadastro = arrCarregarDespesa.data[0].DadosDespesa[i].dt_cadastro;
                v_dt_cadastro = v_dt_cadastro == null ? '' : v_dt_cadastro;
                
                v_ds_usuario = arrCarregarDespesa.data[0].DadosDespesa[i].ds_usuario;
                v_ds_usuario = v_ds_usuario == null ? '' : v_ds_usuario;
                
                v_ds_empresa = arrCarregarDespesa.data[0].DadosDespesa[i].ds_empresa;
                v_ds_empresa = v_ds_empresa == null ? '' : v_ds_empresa;
                
                v_ds_conta_bancaria = arrCarregarDespesa.data[0].DadosDespesa[i].ds_conta_bancaria;
                v_ds_conta_bancaria = v_ds_conta_bancaria == null ? '' : v_ds_conta_bancaria;

                v_dt_faturamento = arrCarregarDespesa.data[0].DadosDespesa[i].dt_faturamento;
                v_dt_faturamento = v_dt_faturamento == null ? '' : v_dt_faturamento;

                v_dt_vencimento = arrCarregarDespesa.data[0].DadosDespesa[i].dt_vencimento;
                v_dt_vencimento = v_dt_vencimento == null ? '' : v_dt_vencimento;

                v_dt_pagamento = arrCarregarDespesa.data[0].DadosDespesa[i].dt_pagamento;
                v_dt_pagamento = v_dt_pagamento == null ? '' : v_dt_pagamento;

                v_ds_operacao = arrCarregarDespesa.data[0].DadosDespesa[i].ds_operacao;
                v_ds_operacao = v_ds_operacao == null ? '' : v_ds_operacao;
                
                v_ds_tipo_operacao = arrCarregarDespesa.data[0].DadosDespesa[i].ds_tipo_operacao;
                v_ds_tipo_operacao = v_ds_tipo_operacao == null ? '' : v_ds_tipo_operacao;

                v_ds_tipo_grupo = arrCarregarDespesa.data[0].DadosDespesa[i].ds_tipo_grupo;
                v_ds_tipo_grupo = v_ds_tipo_grupo == null ? '' : v_ds_tipo_grupo;

                v_ds_recebido_pago_origem = arrCarregarDespesa.data[0].DadosDespesa[i].ds_recebido_pago_origem;
                v_ds_recebido_pago_origem = v_ds_recebido_pago_origem == null ? '' : v_ds_recebido_pago_origem;

                v_ds_metodo_pagamento = arrCarregarDespesa.data[0].DadosDespesa[i].ds_metodo_pagamento;
                v_ds_metodo_pagamento = v_ds_metodo_pagamento == null ? '' : v_ds_metodo_pagamento;

                v_vl_lancamento = arrCarregarDespesa.data[0].DadosDespesa[i].vl_lancamento;
                v_vl_lancamento = v_vl_lancamento == null ? '' : v_vl_lancamento;

                v_tipos_operacao_pk = arrCarregarDespesa.data[0].DadosDespesa[i].tipos_operacao_pk;
                v_tipos_operacao_pk = v_tipos_operacao_pk == null ? '' : v_tipos_operacao_pk;
                
                if(v_vl_lancamento != null && v_tipos_operacao_pk != 1){
                    v_vl_despesa = v_vl_lancamento;
                }

                v_ds_status_pagamento = arrCarregarDespesa.data[0].DadosDespesa[i].ds_status_pagamento;
                v_ds_status_pagamento = v_ds_status_pagamento == null ? '' : v_ds_status_pagamento;

                total_dia = arrCarregarDespesa.data[0].DadosDespesa[i].total_dia;
                total_dia = total_dia == null ? '' : total_dia;

                vl_despesa_dia = arrCarregarDespesa.data[0].DadosDespesa[i].vl_despesa_dia;
                vl_despesa_dia = vl_despesa_dia == null ? '' : vl_despesa_dia;
                total_dia = total_dia == null ? '' : total_dia;

                vl_despesa_pendente_dia = arrCarregarDespesa.data[0].DadosDespesa[i].vl_despesa_pendente_dia;
                vl_despesa_pendente_dia = vl_despesa_pendente_dia == null ? '' : vl_despesa_pendente_dia;

                vl_lancamento_pendente = arrCarregarDespesa.data[0].DadosDespesa[i].vl_lancamento_pendente;
                vl_lancamento_pendente = vl_lancamento_pendente == null ? '' : vl_lancamento_pendente;

                vl_total_saldo_dia = arrCarregarDespesa.data[0].DadosDespesa[i].vl_total_saldo_dia;
                vl_total_saldo_dia = vl_total_saldo_dia == null ? '' : vl_total_saldo_dia;

                ds_lancamento = arrCarregarDespesa.data[0].DadosDespesa[i].ds_lancamento;
                ds_lancamento = ds_lancamento == null ? '' : ds_lancamento;

                ds_cpf_cnpj = arrCarregarDespesa.data[0].DadosDespesa[i].ds_cpf_cnpj;
                ds_cpf_cnpj = ds_cpf_cnpj == null ? '' : ds_cpf_cnpj;

                if (ds_lancamento.toLowerCase().includes(termoPesquisa.toLowerCase())||
                ds_cpf_cnpj.toLowerCase().includes(termoPesquisa.toLowerCase())||
                v_ds_status_pagamento.toLowerCase().includes(termoPesquisa.toLowerCase())||
                v_ds_recebido_pago_origem.toLowerCase().includes(termoPesquisa.toLowerCase())
                ) {
                    $('#tblDespesa tbody').append('<tr id="tblDespesaTr'+i+'"></tr>');
                    //$('#tblDespesaTr'+i).append('<td>'+v_pk+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+ds_lancamento+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_dt_cadastro+'</td>');
                // $('#tblDespesaTr'+i).append('<td>'+v_ds_usuario+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_ds_empresa+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_ds_conta_bancaria+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_dt_faturamento+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_dt_vencimento+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_dt_pagamento+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_ds_status_pagamento+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_ds_operacao+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_ds_tipo_operacao+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_ds_tipo_grupo+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+v_ds_recebido_pago_origem+'</td>');
                    $('#tblDespesaTr'+i).append('<td>'+ds_cpf_cnpj+'</td>');
                    //$('#tblDespesaTr'+i).append('<td>'+v_ds_metodo_pagamento+'</td>');
                    $('#tblDespesaTr'+i).append('<td><font color="blue">'+v_vl_despesa+'</font></td>');
                    $('#tblDespesaTr'+i).append('<td><font color="red">'+vl_lancamento_pendente+'</font></td>');
                    $('#tblDespesaTr'+i).append('<td><div>\n\
                                                    <div class="row">\n\
                                                        <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-pencil-square" title="Editar Lançamento" onclick="fcAbrirCadastroLancamento(' + v_pk + ')" ></i></div>\n\
                                                        <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-file-earmark-arrow-up" title="Anexo de Documentos" onclick="fcAnexarDocumento(' + v_pk + ')" ></i></div>\n\
                                                    </div>\n\
                                                    <div class="row">\n\
                                                        <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdImprimir" class="bi bi-printer" title="Imprimir Lançamento" onclick="fcImprimirLancamento(' + v_pk + ')"></i></div>\n\
                                                        <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdExcluir" class="bi bi-x-circle" title="Excluir Lançamento" onclick="fcExcluirLancamentoDespesa(' + v_pk + ')"></i></div>\n\
                                                        <div class="col-md-4" ' + (nfse_pk == null ? 'style="display: none;"' : '') + '><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-download" title="Download NFSE" onclick="fcDownloadNfseLancamento(' + nfse_pk + ')" ></i></div>\n\
                            </div></div></td>');    

                    if(v_dt_vencimento != v_proximo_dt_vencimento || v_proximo_dt_vencimento == ""){
                        console.log(v_proximo_dt_vencimento)
                        $('#tblDespesa tbody').append('<tr id="totaisDespesa'+i+'"></tr>');
                        $('#totaisDespesa'+i).append('<td align="right" width="100" colspan=12 >&nbsp;<b>Saldo do dia '+ v_dt_vencimento +'</b></td>');
                        $('#totaisDespesa'+i).append('<td width="100" ><b>Totais R$<b/></td>');
                        $('#totaisDespesa'+i).append('<td align="center"><font color="blue">' + vl_despesa_dia + '</font></td>');
                        $('#totaisDespesa'+i).append('<td align="center"><font color="red">' + vl_despesa_pendente_dia + '</font></td>');
                        $('#totaisDespesa'+i).append('<td align="center"></td>');
                    }
                
                count++;
            }
            
        }
    } else {

    }
}