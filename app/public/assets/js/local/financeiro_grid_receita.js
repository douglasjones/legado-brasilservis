let arrCarregarReceita = [];
function fcCarregarComboEmpresasReceita() {
    var objParametros = {};
    var arrCarregar = carregarController("conta_bancaria", "listarEmpresaContasAtivas", objParametros);
    carregarComboAjax($("#empresa_receita_pk"), arrCarregar, " ", "pk", "ds_conta");
}

function fcCarregarComboContasReceita() {
    var objParametros = {
        "empresas_pk": $("#empresa_receita_pk").val()
    };
    var arrCarregar = carregarController("conta_bancaria", "listarContasLancamento", objParametros);
    carregarComboAjax($("#contas_receita_pk"), arrCarregar, " ", "pk", "ds_dados_conta");
}

function fcCarregarLeadsReceita() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#grupo_lancamento_receita_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarCnpjLeadsReceita() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listarCpfCnpjClientes", objParametros);
    carregarComboAjax($("#ds_cpf_cnpj_receita"), arrCarregar, " ", "pk", "ds_cpf_cnpj");
}

function fcCarregarColaboradorReceita() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_lancamento_receita_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    carregarComboAjax($("#ds_cpf_cnpj_receita"), arrCarregar, " ", "pk", "ds_cpf");
}

function fcCarregarFornecedorReceita() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("fornecedor", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_lancamento_receita_pk"), arrCarregar, " ", "pk", "ds_fornecedor");
}

function fcCarregarCpfCnpjFornecedorReceita() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("fornecedor", "listarCpfCnpjFornecedor", objParametros);
    carregarComboAjax($("#ds_cpf_cnpj_receita"), arrCarregar, " ", "pk", "ds_cpf_cnpj");
}


function fcListarGrupoReceita() {
    if ($("#tipo_grupo_receita_pk").val() == 1){
        fcCarregarLeadsReceita();
        fcCarregarCnpjLeadsReceita();

    } else if($("#tipo_grupo_receita_pk").val() == 2 ){
        fcCarregarColaboradorReceita();

    }else if($("#tipo_grupo_receita_pk").val() == 3){
        fcCarregarFornecedorReceita();
        fcCarregarCpfCnpjFornecedorReceita();
    } 

    $('#grupo_lancamento_receita_pk').select2();
    $('#ds_cpf_cnpj_receita').select2();
}

function fcExcluirLancamentoReceita(v_pk) {
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("lancamento", "excluir", objParametros);

            if (arrExcluir.status == true){
                fcCarregarGridReceita();
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
function fcListarMiniDashboardReceita(){

    let objParametros = {
        'dt_vencimento': $.trim(localStorage.getItem('date_range_filter_vencimento_receita')),
        "ic_receita_despesa":1
    };
    let arrCarregar = carregarController("lancamento", "listarDashboard", objParametros);
    $("#vencidosReceita").text(float2moeda(arrCarregar.data['vencidos']));
    $("#vencidosHojeReceita").text(float2moeda(arrCarregar.data['vencidosHoje']));
    $("#aVencerReceita").text(float2moeda(arrCarregar.data['aVencer']));
    $("#recebidosReceita").text(float2moeda(arrCarregar.data['recebidos']));
    $("#valorTotalReceita").text(float2moeda(arrCarregar.data['valorTotal']));
    
}
localStorage.getItem('date_range_filter_cadastro_receita') ? localStorage.getItem('date_range_filter_cadastro_receita') : (localStorage.setItem('date_range_filter_cadastro_receita', ''));
localStorage.getItem('date_range_filter_faturamento_receita') ? localStorage.getItem('date_range_filter_faturamento_receita') : (localStorage.setItem('date_range_filter_faturamento_receita', ''));
localStorage.getItem('date_range_filter_vencimento_receita') ? localStorage.getItem('date_range_filter_vencimento_receita') : (localStorage.setItem('date_range_filter_vencimento_receita', ''));
localStorage.getItem('date_range_filter_pagamento_receita') ? localStorage.getItem('date_range_filter_pagamento_receita') : (localStorage.setItem('date_range_filter_pagamento_receita', ''));

function fcCarregarGridReceita(){
    arrCarregarReceita = [];
    fcListarMiniDashboardReceita();
    $("#tblReceita tbody").remove();
    let grupo_lancamento_receita_pk = $('#grupo_lancamento_receita_pk').val();
    if(grupo_lancamento_receita_pk == ""){
        grupo_lancamento_receita_pk = $('#grupo_lancamento_receita_pk').val();
    }
    let objParametros = {
        'pk': $('#pk_lancamento_receita').val(),
        'ic_status_lancamento': $('#ic_status_pagamento_receita').val(),
        'usuario_cadastro_pk': $('#usuario_cadastro_receita_pk').val(),
        'ds_lancamento': $('#ds_lancamento_receita').val(),
        'ds_num_documento': $('#ds_num_documento_receita').val(),
        'ic_tipo_num_documento': $('#ic_tipo_num_documento_receita').val(),
        'tipo_grupo_lancamento_pk': $('#tipo_grupo_receita_pk').val(),
        'grupo_lancamento_pk': grupo_lancamento_receita_pk,
        'dt_cadastro': $.trim(localStorage.getItem('date_range_filter_cadastro_receita')),
        'dt_faturamento': $.trim(localStorage.getItem('date_range_filter_faturamento_receita')),
        'dt_vencimento': $.trim(localStorage.getItem('date_range_filter_vencimento_receita')),
        'dt_pagamento': $.trim(localStorage.getItem('date_range_filter_pagamento_receita')),
        'empresas_pk': $('#empresa_receita_pk').val(),
        'tipo_lancamento_pk': $('#tipo_lancamento_pk_receita').val(),
        'categorias_financeiras_pk': $('#categorias_financeiras_pk_receita').val(),
        'tipos_operacao_pk': $('#tipos_operacao_pk_receita').val(),
        'contas_bancarias_pk': $('#contas_receita_pk').val()
    };
    arrCarregarReceita = carregarController("lancamento", "listarReceita", objParametros);
    let count = 1;
    let vl_receita = 0.00;
    let vl_total_receita_pendente = 0.00;
    
    if(arrCarregarReceita.data.length > 0){
        $('#tblReceita').append('<tbody></tbody>');
            

            vl_receita = arrCarregarReceita.data[0]['vl_total_receita'];
            vl_despeda = arrCarregarReceita.data[0]['vl_total_despesa'];
            vl_total = arrCarregarReceita.data[0]['vl_total'];
            vl_total_saldo_mes = arrCarregarReceita.data[0]['vl_total_saldo_mes'];
            vl_saldo_mes_anterior = arrCarregarReceita.data[0]['vl_saldo_mes_anterior'];
            vl_saldo_atual = arrCarregarReceita.data[0]['vl_saldo_atual'];
            vl_total_receita_pendente = arrCarregarReceita.data[0]['vl_total_receita_pendente'];

            for (i = 0; i < arrCarregarReceita.data[0].DadosReceita.length; i++) {
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

                v_pk = arrCarregarReceita.data[0].DadosReceita[i].pk;
                v_proximo_dt_vencimento = arrCarregarReceita.data[0].DadosReceita[i].proxima_data;
                nfse_pk = arrCarregarReceita.data[0].DadosReceita[i].nfse_pk;

                v_dt_cadastro = arrCarregarReceita.data[0].DadosReceita[i].dt_cadastro;
                v_dt_cadastro = v_dt_cadastro == null ? '' : v_dt_cadastro;
                
                v_ds_usuario = arrCarregarReceita.data[0].DadosReceita[i].ds_usuario;
                v_ds_usuario = v_ds_usuario == null ? '' : v_ds_usuario;
                
                v_ds_empresa = arrCarregarReceita.data[0].DadosReceita[i].ds_empresa;
                v_ds_empresa = v_ds_empresa == null ? '' : v_ds_empresa;
                
                v_ds_conta_bancaria = arrCarregarReceita.data[0].DadosReceita[i].ds_conta_bancaria;
                v_ds_conta_bancaria = v_ds_conta_bancaria == null ? '' : v_ds_conta_bancaria;

                v_dt_faturamento = arrCarregarReceita.data[0].DadosReceita[i].dt_faturamento;
                v_dt_faturamento = v_dt_faturamento == null ? '' : v_dt_faturamento;

                v_dt_vencimento = arrCarregarReceita.data[0].DadosReceita[i].dt_vencimento;
                v_dt_vencimento = v_dt_vencimento == null ? '' : v_dt_vencimento;

                v_dt_pagamento = arrCarregarReceita.data[0].DadosReceita[i].dt_pagamento;
                v_dt_pagamento = v_dt_pagamento == null ? '' : v_dt_pagamento;

                v_ds_operacao = arrCarregarReceita.data[0].DadosReceita[i].ds_operacao;
                v_ds_operacao = v_ds_operacao == null ? '' : v_ds_operacao;
                
                v_ds_tipo_operacao = arrCarregarReceita.data[0].DadosReceita[i].ds_tipo_operacao;
                v_ds_tipo_operacao = v_ds_tipo_operacao == null ? '' : v_ds_tipo_operacao;

                v_ds_tipo_grupo = arrCarregarReceita.data[0].DadosReceita[i].ds_tipo_grupo;
                v_ds_tipo_grupo = v_ds_tipo_grupo == null ? '' : v_ds_tipo_grupo;

                v_ds_recebido_pago_origem = arrCarregarReceita.data[0].DadosReceita[i].ds_recebido_pago_origem;
                v_ds_recebido_pago_origem = v_ds_recebido_pago_origem == null ? '' : v_ds_recebido_pago_origem;

                v_ds_metodo_pagamento = arrCarregarReceita.data[0].DadosReceita[i].ds_metodo_pagamento;
                v_ds_metodo_pagamento = v_ds_metodo_pagamento == null ? '' : v_ds_metodo_pagamento;

                v_vl_lancamento = arrCarregarReceita.data[0].DadosReceita[i].vl_lancamento;
                v_vl_lancamento = v_vl_lancamento == null ? '' : v_vl_lancamento;

                v_tipos_operacao_pk = arrCarregarReceita.data[0].DadosReceita[i].tipos_operacao_pk;
                v_tipos_operacao_pk = v_tipos_operacao_pk == null ? '' : v_tipos_operacao_pk;
                
                if (v_vl_lancamento != null && v_tipos_operacao_pk == 1) {
                    v_vl_receita = v_vl_lancamento;
                }

                v_ds_status_pagamento = arrCarregarReceita.data[0].DadosReceita[i].ds_status_pagamento;
                v_ds_status_pagamento = v_ds_status_pagamento == null ? '' : v_ds_status_pagamento;

                total_dia = arrCarregarReceita.data[0].DadosReceita[i].total_dia;
                total_dia = total_dia == null ? '' : total_dia;

                vl_receita_dia = arrCarregarReceita.data[0].DadosReceita[i].vl_receita_dia;
                vl_receita_dia = vl_receita_dia == null ? '' : vl_receita_dia;

                vl_receita_pendente_dia = arrCarregarReceita.data[0].DadosReceita[i].vl_receita_pendente_dia;
                vl_receita_pendente_dia = vl_receita_pendente_dia == null ? '' : vl_receita_pendente_dia;

                vl_lancamento_pendente = arrCarregarReceita.data[0].DadosReceita[i].vl_lancamento_pendente;
                vl_lancamento_pendente = vl_lancamento_pendente == null ? '' : vl_lancamento_pendente;

                vl_total_saldo_dia = arrCarregarReceita.data[0].DadosReceita[i].vl_total_saldo_dia;
                vl_total_saldo_dia = vl_total_saldo_dia == null ? '' : vl_total_saldo_dia;

                ds_lancamento = arrCarregarReceita.data[0].DadosReceita[i].ds_lancamento;
                ds_lancamento = ds_lancamento == null ? '' : ds_lancamento;

                ds_cpf_cnpj = arrCarregarReceita.data[0].DadosReceita[i].ds_cpf_cnpj;
                ds_cpf_cnpj = ds_cpf_cnpj == null ? '' : ds_cpf_cnpj;

                $('#tblReceita tbody').append('<tr id="tblReceitaTr'+i+'"></tr>');
                //$('#tblReceitaTr'+i).append('<td>'+v_pk+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+ds_lancamento+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_dt_cadastro+'</td>');
               // $('#tblReceitaTr'+i).append('<td>'+v_ds_usuario+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_empresa+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_conta_bancaria+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_dt_faturamento+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_dt_vencimento+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_dt_pagamento+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_status_pagamento+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_operacao+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_tipo_operacao+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_tipo_grupo+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_recebido_pago_origem+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+ds_cpf_cnpj+'</td>');
                $('#tblReceitaTr'+i).append('<td><font color="blue">'+v_vl_lancamento+'</font></td>');
                $('#tblReceitaTr'+i).append('<td><font color="red">'+vl_lancamento_pendente+'</font></td>');
                $('#tblReceitaTr'+i).append('<td><div>\n\
                                    <div class="row">\n\
                                        <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-pencil-square" title="Editar Lançamento" onclick="fcAbrirCadastroLancamento(' + v_pk + ')" ></i></div>\n\
                                        <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-file-earmark-arrow-up" title="Anexo de Documentos" onclick="fcAnexarDocumento(' + v_pk + ')" ></i></div>\n\
                                    </div>\n\
                                    <div class="row">\n\
                                        <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdImprimir" class="bi bi-printer" title="Imprimir Lançamento" onclick="fcImprimirLancamento(' + v_pk + ')"></i></div>\n\
                                        <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdExcluir" class="bi bi-x-circle" title="Excluir Lançamento" onclick="fcExcluirLancamentoReceita(' + v_pk + ')"></i></div>\n\
                                        <div class="col-md-4" ' + (nfse_pk== null ? 'style="display: none;"' : '') + '><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-download" title="Download NFSE" onclick="fcDownloadNfseLancamento(' + nfse_pk + ')" ></i></div>\n\
                                    </div></div></td>');

                if(v_dt_vencimento != v_proximo_dt_vencimento || v_proximo_dt_vencimento == ""){
                    $('#tblReceita tbody').append('<tr id="totaisReceita'+i+'"></tr>');
                    $('#totaisReceita'+i).append('<td align="right" width="100" colspan=12 >&nbsp;<b>Saldo do dia '+ v_dt_vencimento +'</b></td>');
                    $('#totaisReceita'+i).append('<td width="100" ><b>Totais R$<b/></td>');
                    $('#totaisReceita'+i).append('<td align="center"><font color="blue">' + vl_receita_dia + '</font></td>');
                    $('#totaisReceita'+i).append('<td align="center"><font color="red">' + vl_receita_pendente_dia + '</font></td>');
                    $('#totaisReceita'+i).append('<td align="center"></td>');
                }
                
                
                count ++;
            }

            filtrarDadosReceita($("#buscaGeralReceita").val());
    }
    
    $('#totalLancamentoReceita').html(vl_receita);
    $('#totalPendenteReceita').html(vl_total_receita_pendente);
}

function fcCarregarUsuariosCadastroReceita() {
    var objParametros = {};
    var arrCarregar = carregarController("usuario", "listarTodosSemAdm", objParametros);
    carregarComboAjax($("#usuario_cadastro_receita_pk"), arrCarregar, " ", "pk", "ds_usuario");
}


function fcCarregarCategoriaOperacaoReceita() {
    var objParametros = {};
    var arrCarregar = carregarController("categoria_financeira", "listarTodos", objParametros);
    carregarComboAjax($("#categorias_financeiras_pk_receita"), arrCarregar, " ", "pk", "ds_categoria");
}

function fcCarregarTipoPlanoNegocioReceita() {
    var objParametros = {
        "categorias_financeiras_pk": $("#categorias_financeiras_pk_receita").val()
    };
    var arrCarregar = carregarController("plano_contas", "listaPorCategoria", objParametros);
    carregarComboAjax($("#tipos_operacao_pk_receita"), arrCarregar, " ", "pk", "ds_tipo_operacao");
}

function fcCarregarFuncoesReceita(){
    //Combo
    fcCarregarComboEmpresasReceita();
    //$('#empresa_receita_pk').select2();

    fcCarregarComboContasReceita();
    $("#empresa_receita_pk").change(function(){
        fcCarregarComboContasReceita();
        $('#contas_receita_pk').select2();
    });

    $("#tipo_grupo_receita_pk").change(function(){
        fcListarGrupoReceita();
    });
    fcCarregarUsuariosCadastroReceita();
    
    fcCarregarCategoriaOperacaoReceita();
    $("#categorias_financeiras_pk_receita").change(function () {
        fcCarregarTipoPlanoNegocioReceita();
        $("#tipos_operacao_pk_receita").select2();
    });

    let today = new Date();
    let dd = today.getDate();
    let mm = today.getMonth()+1; //January is 0!
    let yyyy = today.getFullYear();
    //data
    if(dd<10) {dd = '0'+dd} 
    if(mm<10) {mm = '0'+mm} 

    today = dd + '/' + mm + '/' + yyyy;

    $('#dt_cadastro_ini_receita').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_cadastro_ini_receita").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_cadastro_fim_receita').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_cadastro_fim_receita").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_faturamento_ini_receita').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_faturamento_ini_receita").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_faturamento_fim_receita').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_faturamento_fim_receita").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_vencimento_ini_receita').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_vencimento_ini_receita").keypress(function(){
        mascara(this,mdata);
    });
    $("#dt_vencimento_ini_receita").val(today);

    $('#dt_vencimento_fim_receita').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_vencimento_fim_receita").keypress(function(){
        mascara(this,mdata);
    });
    $("#dt_vencimento_fim_receita").val(today);

    $('#dt_pagamento_ini_receita').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_pagamento_ini_receita").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_pagamento_fim_receita').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_pagamento_fim_receita").keypress(function(){
        mascara(this,mdata);
    });

    fcCarregarGridReceita();
    $(document).on('click', '#cmdPesqReceita', fcCarregarGridReceita);
    
}

function filtrarDadosReceita(termoPesquisa) {
    
    // Limpa a tabela antes de filtrar os dados
    $('#tblReceita tbody').empty();
    
    if (arrCarregarReceita.data.length > 0) {
        let count = 1;
        vl_receita = arrCarregarReceita.data[0]['vl_total_receita'];
        vl_despeda = arrCarregarReceita.data[0]['vl_total_despesa'];
        vl_total = arrCarregarReceita.data[0]['vl_total'];
        vl_total_saldo_mes = arrCarregarReceita.data[0]['vl_total_saldo_mes'];
        vl_saldo_mes_anterior = arrCarregarReceita.data[0]['vl_saldo_mes_anterior'];
        vl_saldo_atual = arrCarregarReceita.data[0]['vl_saldo_atual'];
        vl_total_receita_pendente = arrCarregarReceita.data[0]['vl_total_receita_pendente'];

        for (i = 0; i < arrCarregarReceita.data[0].DadosReceita.length; i++) {
            


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

                v_pk = arrCarregarReceita.data[0].DadosReceita[i].pk;
                v_proximo_dt_vencimento = arrCarregarReceita.data[0].DadosReceita[i].proxima_data;
                nfse_pk = arrCarregarReceita.data[0].DadosReceita[i].nfse_pk;

                

                v_dt_cadastro = arrCarregarReceita.data[0].DadosReceita[i].dt_cadastro;
                v_dt_cadastro = v_dt_cadastro == null ? '' : v_dt_cadastro;
                
                v_ds_usuario = arrCarregarReceita.data[0].DadosReceita[i].ds_usuario;
                v_ds_usuario = v_ds_usuario == null ? '' : v_ds_usuario;
                
                v_ds_empresa = arrCarregarReceita.data[0].DadosReceita[i].ds_empresa;
                v_ds_empresa = v_ds_empresa == null ? '' : v_ds_empresa;
                
                v_ds_conta_bancaria = arrCarregarReceita.data[0].DadosReceita[i].ds_conta_bancaria;
                v_ds_conta_bancaria = v_ds_conta_bancaria == null ? '' : v_ds_conta_bancaria;

                v_dt_faturamento = arrCarregarReceita.data[0].DadosReceita[i].dt_faturamento;
                v_dt_faturamento = v_dt_faturamento == null ? '' : v_dt_faturamento;

                v_dt_vencimento = arrCarregarReceita.data[0].DadosReceita[i].dt_vencimento;
                v_dt_vencimento = v_dt_vencimento == null ? '' : v_dt_vencimento;

                v_dt_pagamento = arrCarregarReceita.data[0].DadosReceita[i].dt_pagamento;
                v_dt_pagamento = v_dt_pagamento == null ? '' : v_dt_pagamento;

                v_ds_operacao = arrCarregarReceita.data[0].DadosReceita[i].ds_operacao;
                v_ds_operacao = v_ds_operacao == null ? '' : v_ds_operacao;
                
                v_ds_tipo_operacao = arrCarregarReceita.data[0].DadosReceita[i].ds_tipo_operacao;
                v_ds_tipo_operacao = v_ds_tipo_operacao == null ? '' : v_ds_tipo_operacao;

                v_ds_tipo_grupo = arrCarregarReceita.data[0].DadosReceita[i].ds_tipo_grupo;
                v_ds_tipo_grupo = v_ds_tipo_grupo == null ? '' : v_ds_tipo_grupo;

                v_ds_recebido_pago_origem = arrCarregarReceita.data[0].DadosReceita[i].ds_recebido_pago_origem;
                v_ds_recebido_pago_origem = v_ds_recebido_pago_origem == null ? '' : v_ds_recebido_pago_origem;

                v_ds_metodo_pagamento = arrCarregarReceita.data[0].DadosReceita[i].ds_metodo_pagamento;
                v_ds_metodo_pagamento = v_ds_metodo_pagamento == null ? '' : v_ds_metodo_pagamento;

                v_vl_lancamento = arrCarregarReceita.data[0].DadosReceita[i].vl_lancamento;
                v_vl_lancamento = v_vl_lancamento == null ? '' : v_vl_lancamento;

                v_tipos_operacao_pk = arrCarregarReceita.data[0].DadosReceita[i].tipos_operacao_pk;
                v_tipos_operacao_pk = v_tipos_operacao_pk == null ? '' : v_tipos_operacao_pk;
                
                if (v_vl_lancamento != null && v_tipos_operacao_pk == 1) {
                    v_vl_receita = v_vl_lancamento;
                }

                v_ds_status_pagamento = arrCarregarReceita.data[0].DadosReceita[i].ds_status_pagamento;
                v_ds_status_pagamento = v_ds_status_pagamento == null ? '' : v_ds_status_pagamento;

                total_dia = arrCarregarReceita.data[0].DadosReceita[i].total_dia;
                total_dia = total_dia == null ? '' : total_dia;

                vl_receita_dia = arrCarregarReceita.data[0].DadosReceita[i].vl_receita_dia;
                vl_receita_dia = vl_receita_dia == null ? '' : vl_receita_dia;

                vl_receita_pendente_dia = arrCarregarReceita.data[0].DadosReceita[i].vl_receita_pendente_dia;
                vl_receita_pendente_dia = vl_receita_pendente_dia == null ? '' : vl_receita_pendente_dia;

                vl_lancamento_pendente = arrCarregarReceita.data[0].DadosReceita[i].vl_lancamento_pendente;
                vl_lancamento_pendente = vl_lancamento_pendente == null ? '' : vl_lancamento_pendente;

                vl_total_saldo_dia = arrCarregarReceita.data[0].DadosReceita[i].vl_total_saldo_dia;
                vl_total_saldo_dia = vl_total_saldo_dia == null ? '' : vl_total_saldo_dia;

                ds_lancamento = arrCarregarReceita.data[0].DadosReceita[i].ds_lancamento;
                ds_lancamento = ds_lancamento == null ? '' : ds_lancamento;

                ds_cpf_cnpj = arrCarregarReceita.data[0].DadosReceita[i].ds_cpf_cnpj;
                ds_cpf_cnpj = ds_cpf_cnpj == null ? '' : ds_cpf_cnpj;

                if (ds_lancamento.toLowerCase().includes(termoPesquisa.toLowerCase())||
                ds_cpf_cnpj.toLowerCase().includes(termoPesquisa.toLowerCase())||
                v_ds_status_pagamento.toLowerCase().includes(termoPesquisa.toLowerCase())||
                v_ds_recebido_pago_origem.toLowerCase().includes(termoPesquisa.toLowerCase())
                ) {
                    $('#tblReceita tbody').append('<tr id="tblReceitaTr'+i+'"></tr>');
                //$('#tblReceitaTr'+i).append('<td>'+v_pk+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+ds_lancamento+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_dt_cadastro+'</td>');
               // $('#tblReceitaTr'+i).append('<td>'+v_ds_usuario+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_empresa+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_conta_bancaria+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_dt_faturamento+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_dt_vencimento+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_dt_pagamento+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_status_pagamento+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_operacao+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_tipo_operacao+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_tipo_grupo+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+v_ds_recebido_pago_origem+'</td>');
                $('#tblReceitaTr'+i).append('<td>'+ds_cpf_cnpj+'</td>');
               // $('#tblReceitaTr'+i).append('<td>'+v_ds_metodo_pagamento+'</td>');
                $('#tblReceitaTr'+i).append('<td><font color="blue">'+v_vl_lancamento+'</font></td>');
                $('#tblReceitaTr'+i).append('<td><font color="red">'+vl_lancamento_pendente+'</font></td>');
                $('#tblReceitaTr'+i).append('<td><div>\n\
                    <div class="row">\n\
                        <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-pencil-square" title="Editar Lançamento" onclick="fcAbrirCadastroLancamento(' + v_pk + ')" ></i></div>\n\
                        <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-file-earmark-arrow-up" title="Anexo de Documentos" onclick="fcAnexarDocumento(' + v_pk + ')" ></i></div>\n\
                    </div>\n\
                    <div class="row">\n\
                        <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdImprimir" class="bi bi-printer" title="Imprimir Lançamento" onclick="fcImprimirLancamento(' + v_pk + ')"></i></div>\n\
                        <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdExcluir" class="bi bi-x-circle" title="Excluir Lançamento" onclick="fcExcluirLancamentoReceita(' + v_pk + ')"></i></div>\n\
                        <div class="col-md-4" ' + (nfse_pk == null ? 'style="display: none;"' : '') + '><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-download" title="Download NFSE" onclick="fcDownloadNfseLancamento(' + nfse_pk + ')" ></i></div>\n\
                    </div></div></td>');

                if(v_dt_vencimento != v_proximo_dt_vencimento || v_proximo_dt_vencimento == ""){
                    $('#tblReceita tbody').append('<tr id="totaisReceita'+i+'"></tr>');
                    $('#totaisReceita'+i).append('<td align="right" width="100" colspan=12 >&nbsp;<b>Saldo do dia '+ v_dt_vencimento +'</b></td>');
                    $('#totaisReceita'+i).append('<td width="100" ><b>Totais R$<b/></td>');
                    $('#totaisReceita'+i).append('<td align="center"><font color="blue">' + vl_receita_dia + '</font></td>');
                    $('#totaisReceita'+i).append('<td align="center"><font color="red">' + vl_receita_pendente_dia + '</font></td>');
                    $('#totaisReceita'+i).append('<td align="center"></td>');
                }
                
                
                count++;   
                
            }
            
        }
    } else {

    }
}