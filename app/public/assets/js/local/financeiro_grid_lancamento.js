let arrCarregarGrid = [];
function fcCarregarComboEmpresasLancamento() {
    var objParametros = {};
    var arrCarregarCombo = carregarController("conta_bancaria", "listarEmpresaContasAtivas", objParametros);
    carregarComboAjax($("#empresa_pk"), arrCarregarCombo, " ", "pk", "ds_conta");
}

function fcCarregarComboContasLancamento() {
    var objParametros = {
        "empresas_pk": $("#empresa_pk").val()
    };
    var arrCarregarCombo = carregarController("conta_bancaria", "listarContasLancamento", objParametros);
    carregarComboAjax($("#contas_lancamento_pk"), arrCarregarCombo, " ", "pk", "ds_dados_conta");
}

function fcCarregarLeadsLancamento() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregarCombo = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#grupo_grid_lancamento_pk"), arrCarregarCombo, " ", "pk", "ds_lead");
}

function fcCarregarCnpjLeadsLancamento() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregarCombo = carregarController("lead", "listarCpfCnpjClientes", objParametros);
    carregarComboAjax($("#ds_cpf_cnpj_lancamento"), arrCarregarCombo, " ", "pk", "ds_cpf_cnpj");
}

function fcCarregarColaboradorLancamento() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregarCombo = carregarController("colaborador", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_grid_lancamento_pk"), arrCarregarCombo, " ", "pk", "ds_colaborador");
    carregarComboAjax($("#ds_cpf_cnpj_lancamento"), arrCarregarCombo, " ", "pk", "ds_cpf");
}

function fcCarregarFornecedorLancamento() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregarCombo = carregarController("fornecedor", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_grid_lancamento_pk"), arrCarregarCombo, " ", "pk", "ds_fornecedor");
}

function fcCarregarCpfCnpjFornecedorLancamento() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregarCombo = carregarController("fornecedor", "listarCpfCnpjFornecedor", objParametros);
    carregarComboAjax($("#ds_cpf_cnpj_lancamento"), arrCarregarCombo, " ", "pk", "ds_cpf_cnpj");
}

function fcListarGrupoLancamento() {
    if ($("#tipo_grupo_lancamento_pk").val() == 1){
        fcCarregarLeadsLancamento();
        fcCarregarCnpjLeadsLancamento();

    } else if($("#tipo_grupo_lancamento_pk").val() == 2 ){
        fcCarregarColaboradorLancamento();

    }else if($("#tipo_grupo_lancamento_pk").val() == 3){
        fcCarregarFornecedorLancamento();
        fcCarregarCpfCnpjFornecedorLancamento();
    } 

    $('#grupo_grid_lancamento_pk').select2();
    $('#ds_cpf_cnpj_lancamento').select2();
}

function fcExcluirLancamento(v_pk) {
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("lancamento", "excluir", objParametros);

            if (arrExcluir.status == true){
                fcCarregarGridLancamento();
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
localStorage.getItem('date_range_filter_cadastro') ? localStorage.getItem('date_range_filter_cadastro') : (localStorage.setItem('date_range_filter_cadastro', ''));
localStorage.getItem('date_range_filter_faturamento') ? localStorage.getItem('date_range_filter_faturamento') : (localStorage.setItem('date_range_filter_faturamento', ''));
localStorage.getItem('date_range_filter_vencimento') ? localStorage.getItem('date_range_filter_vencimento') : (localStorage.setItem('date_range_filter_vencimento', ''));
localStorage.getItem('date_range_filter_pagamento') ? localStorage.getItem('date_range_filter_pagamento') : (localStorage.setItem('date_range_filter_pagamento', ''));

function fcCarregarGridLancamento(){
    $("#tblLancamento tbody").remove();
    let grupo_grid_lancamento_pk = $('#grupo_grid_lancamento_pk').val();
    if(grupo_grid_lancamento_pk == ""){
        grupo_grid_lancamento_pk = $('#ds_cpf_cnpj_lancamento').val();
    }
    
    let objParametros = {
        'pk': $('#pk_lancamento_lancamento').val(),
        'ic_status_lancamento': $('#ic_status_pagamento_lancamento').val(),
        'usuario_cadastro_pk': $('#usuario_cadastro_lancamento_pk').val(),
        'ds_lancamento': $('#ds_lancamento_lancamento').val(),
        'ds_num_documento': $('#ds_num_documento_lancamento').val(),
        'ic_tipo_num_documento': $('#ic_tipo_num_documento_lancamento').val(),
        'tipo_grupo_lancamento_pk': $('#tipo_grupo_lancamento_pk').val(),
        'grupo_lancamento_pk': grupo_grid_lancamento_pk,
        'dt_cadastro': $.trim(localStorage.getItem('date_range_filter_cadastro')),
        'dt_faturamento': $.trim(localStorage.getItem('date_range_filter_faturamento')),
        'dt_vencimento': $.trim(localStorage.getItem('date_range_filter_vencimento')),
        'dt_pagamento': $.trim(localStorage.getItem('date_range_filter_pagamento')),
        'tipo_lancamento_pk': $('#tipo_lancamento_pk_lancamento').val(),
        'categorias_financeiras_pk': $('#categorias_financeiras_pk_lancamento').val(),
        'tipos_operacao_pk': $('#tipos_operacao_pk_lancamento').val(),
        'empresas_pk': $('#empresa_pk').val(),
        'contas_bancarias_pk': $('#contas_lancamento_pk').val()
    };
    arrCarregarGrid = carregarController("lancamento", "listarLancamento", objParametros);
    if(arrCarregarGrid.data.length > 0){
        $('#tblLancamento').append('<tbody></tbody>');
        let count = 1;
        let vl_lancamento = "";
        let vl_despeda = "";
        let vl_total = "";
        let vl_saldo_mes = "";

            vl_lancamento = arrCarregarGrid.data[0]['vl_total_lancamento'];
            vl_total_lancamento_pendente = arrCarregarGrid.data[0]['vl_total_lancamento_pendente'];
            vl_despeda = arrCarregarGrid.data[0]['vl_total_despesa'];
            vl_total = arrCarregarGrid.data[0]['vl_total'];
            vl_total_saldo_mes = arrCarregarGrid.data[0]['vl_total_saldo_mes'];
            vl_saldo_mes_anterior = arrCarregarGrid.data[0]['vl_saldo_mes_anterior'];
            vl_saldo_atual = arrCarregarGrid.data[0]['vl_saldo_atual'];
            for (i = 0; i < arrCarregarGrid.data[0].DadosLancamento.length; i++) {
                let v_pk = "";
                let v_dt_cadastro = "";
                let v_ds_usuario = "";
                let v_dt_faturamento = "";
                let v_dt_vencimento = "";
                let v_dt_pagamento = "";
                let v_ds_operacao = "";
                let v_ds_tipo_operacao = "";
                let v_ds_metodo_pagamento = "";
                let v_vl_lancamento = "";
                let v_ds_status_pagamento = "";
                let nfse_pk = "";

                v_pk = arrCarregarGrid.data[0].DadosLancamento[i].pk;
                v_proximo_dt_vencimento = arrCarregarGrid.data[0].DadosLancamento[i].proxima_data;
                nfse_pk = arrCarregarGrid.data[0].DadosLancamento[i].nfse_pk;

                v_dt_cadastro = arrCarregarGrid.data[0].DadosLancamento[i].dt_cadastro;
                v_dt_cadastro = v_dt_cadastro == null ? '' : v_dt_cadastro;
                
                v_ds_usuario = arrCarregarGrid.data[0].DadosLancamento[i].ds_usuario;
                v_ds_usuario = v_ds_usuario == null ? '' : v_ds_usuario;
                
                v_ds_empresa = arrCarregarGrid.data[0].DadosLancamento[i].ds_empresa;
                v_ds_empresa = v_ds_empresa == null ? '' : v_ds_empresa;
                
                v_ds_conta_bancaria = arrCarregarGrid.data[0].DadosLancamento[i].ds_conta_bancaria;
                v_ds_conta_bancaria = v_ds_conta_bancaria == null ? '' : v_ds_conta_bancaria;

                v_dt_faturamento = arrCarregarGrid.data[0].DadosLancamento[i].dt_faturamento;
                v_dt_faturamento = v_dt_faturamento == null ? '' : v_dt_faturamento;

                v_dt_vencimento = arrCarregarGrid.data[0].DadosLancamento[i].dt_vencimento;
                v_dt_vencimento = v_dt_vencimento == null ? '' : v_dt_vencimento;

                v_dt_pagamento = arrCarregarGrid.data[0].DadosLancamento[i].dt_pagamento;
                v_dt_pagamento = v_dt_pagamento == null ? '' : v_dt_pagamento;
                

                v_ds_operacao = arrCarregarGrid.data[0].DadosLancamento[i].ds_operacao;
                v_ds_operacao = v_ds_operacao == null ? '' : v_ds_operacao;
                
                v_ds_tipo_operacao = arrCarregarGrid.data[0].DadosLancamento[i].ds_tipo_operacao;
                v_ds_tipo_operacao = v_ds_tipo_operacao == null ? '' : v_ds_tipo_operacao;

                v_ds_tipo_grupo = arrCarregarGrid.data[0].DadosLancamento[i].ds_tipo_grupo;
                v_ds_tipo_grupo = v_ds_tipo_grupo == null ? '' : v_ds_tipo_grupo;

                v_ds_recebido_pago_origem = arrCarregarGrid.data[0].DadosLancamento[i].ds_recebido_pago_origem;
                v_ds_recebido_pago_origem = v_ds_recebido_pago_origem == null ? '' : v_ds_recebido_pago_origem;

                v_ds_metodo_pagamento = arrCarregarGrid.data[0].DadosLancamento[i].ds_metodo_pagamento;
                v_ds_metodo_pagamento = v_ds_metodo_pagamento == null ? '' : v_ds_metodo_pagamento;

                v_vl_lancamento = arrCarregarGrid.data[0].DadosLancamento[i].vl_lancamento;
                v_vl_lancamento = v_vl_lancamento == null ? '' : v_vl_lancamento;

                v_vl_lancamento_pendente_dia = arrCarregarGrid.data[0].DadosLancamento[i].vl_lancamento_pendente_dia;
                v_vl_lancamento_pendente_dia = v_vl_lancamento_pendente_dia == null ? '' : v_vl_lancamento_pendente_dia;

                v_tipo_lancamento_pk = arrCarregarGrid.data[0].DadosLancamento[i].tipo_lancamento_pk;
                v_tipo_lancamento_pk = v_tipo_lancamento_pk == null ? '' : v_tipo_lancamento_pk;

                v_ds_status_pagamento = arrCarregarGrid.data[0].DadosLancamento[i].ds_status_pagamento;
                v_ds_status_pagamento = v_ds_status_pagamento == null ? '' : v_ds_status_pagamento;

                total_dia = arrCarregarGrid.data[0].DadosLancamento[i].total_dia;
                total_dia = total_dia == null ? '' : total_dia;

                vl_lancamento_dia = arrCarregarGrid.data[0].DadosLancamento[i].vl_lancamento_dia;
                vl_lancamento_dia = vl_lancamento_dia == null ? '' : vl_lancamento_dia;

                vl_pendente = arrCarregarGrid.data[0].DadosLancamento[i].vl_pendente;
                vl_pendente = vl_pendente == null ? '' : vl_pendente;

                vl_total_saldo_dia = arrCarregarGrid.data[0].DadosLancamento[i].vl_total_saldo_dia;
                vl_total_saldo_dia = vl_total_saldo_dia == null ? '' : vl_total_saldo_dia;

                ds_lancamento = arrCarregarGrid.data[0].DadosLancamento[i].ds_lancamento;
                ds_lancamento = ds_lancamento == null ? '' : ds_lancamento;

                ds_cpf_cnpj = arrCarregarGrid.data[0].DadosLancamento[i].ds_cpf_cnpj;
                ds_cpf_cnpj = ds_cpf_cnpj == null ? '' : ds_cpf_cnpj;
                
                $('#tblLancamento tbody').append('<tr id="tblLancamentoTr'+i+'"></tr>');
                //$('#tblLancamentoTr'+i).append('<td>'+v_pk+'</td>');
                $('#tblLancamentoTr'+i).append('<td>'+v_pk+'</td>');
                $('#tblLancamentoTr'+i).append('<td>'+v_ds_status_pagamento+'</td>');
                $('#tblLancamentoTr'+i).append('<td>'+v_ds_tipo_operacao+'</td>');
                $('#tblLancamentoTr'+i).append('<td>'+ds_lancamento+'</td>');
                //$('#tblLancamentoTr'+i).append('<td>'+v_ds_usuario+'</td>');
                $('#tblLancamentoTr'+i).append('<td>'+v_ds_empresa+'</td>');
                $('#tblLancamentoTr'+i).append('<td>'+v_ds_conta_bancaria+'</td>');
                $('#tblLancamentoTr'+i).append('<td>'+v_dt_vencimento+'</td>');
                $('#tblLancamentoTr'+i).append('<td>'+v_dt_pagamento+'</td>');
                
                
                $('#tblLancamentoTr'+i).append('<td>'+v_ds_recebido_pago_origem+'</td>');
                $('#tblLancamentoTr'+i).append('<td>'+ds_cpf_cnpj+'</td>');
                //$('#tblLancamentoTr'+i).append('<td>'+v_ds_metodo_pagamento+'</td>');
                $('#tblLancamentoTr'+i).append('<td><font color="blue">'+v_vl_lancamento+'</font></td>');
                $('#tblLancamentoTr'+i).append('<td><font color="red">'+vl_pendente+'</font></td>');
                $('#tblLancamentoTr'+i).append('<td><div>\n\
                                                        <div class="row">\n\
                                                            <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-pencil-square" title="Editar Lançamento" onclick="fcAbrirCadastroLancamento(' + v_pk + ')" ></i></div>\n\
                                                            <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-file-earmark-arrow-up" title="Anexo de Documentos" onclick="fcAnexarDocumento(' + v_pk + ')" ></i></div>\n\
                                                        </div>\n\
                                                        <div class="row">\n\
                                                            <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdImprimir" class="bi bi-printer" title="Imprimir Lançamento" onclick="fcImprimirLancamento(' + v_pk + ')"></i></div>\n\
                                                            <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdExcluir" class="bi bi-x-circle" title="Excluir Lançamento" onclick="fcExcluirLancamento(' + v_pk + ')"></i></div>\n\
                                                            <div class="col-md-4" ' + (nfse_pk == null ? 'style="display: none;"' : '') + '><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-download" title="Download NFSE" onclick="fcDownloadNfseLancamento(' + nfse_pk + ')" ></i></div>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </td>'
                                                );

                if(v_dt_vencimento != v_proximo_dt_vencimento || v_proximo_dt_vencimento == ""){
                    $('#tblLancamento tbody').append('<tr id="totaisLancamento'+i+'"></tr>');
                    $('#totaisLancamento'+i).append('<td align="right" width="100" colspan=9 >&nbsp;<b>Saldo do dia '+ v_dt_vencimento +'</b></td>');
                    $('#totaisLancamento'+i).append('<td width="100" ><b>Totais R$<b/></td>');
                    $('#totaisLancamento'+i).append('<td align="center"><font color="blue">' + vl_lancamento_dia + '</font></td>');
                    $('#totaisLancamento'+i).append('<td align="center"><font color="red">' + v_vl_lancamento_pendente_dia + '</font></td>');
                    $('#totaisLancamento'+i).append('<td align="center"></td>');
                }

                $('#totalLancamento').html(vl_lancamento);
                $('#totalLancamentoPendente').html(vl_total_lancamento_pendente);


                
                
                count++;
            }

            filtrarDados($("#buscaGeralLancamento").val());

    }
}

function fcImprimirLancamento(pk) {
    var objParametros = {
        "pk": pk
    };
    sendPost('lancamento', 'impressaoLancamento' ,objParametros);
}

function fcDownloadNfseLancamento(notas_pk){
    var job = 'download_nfse_lancamento/'+notas_pk

    var url = routes_api('controle_nfse', job, {});
    window.open(url, '_blank');
}

function fcCarregarUsuariosCadastroLancamento() {
    var objParametros = {};
    var arrCarregarCombo = carregarController("usuario", "listarTodosSemAdm", objParametros);
    carregarComboAjax($("#usuario_cadastro_lancamento_pk"), arrCarregarCombo, " ", "pk", "ds_usuario");
}


function fcMiniDashboardLancamento(){
    let objParametros = {
        'pk': $('#pk_lancamento_lancamento').val(),
        'ic_status_lancamento': $('#ic_status_pagamento_lancamento').val(),
        'usuario_cadastro_pk': $('#usuario_cadastro_lancamento_pk').val(),
        'ds_lancamento': $('#ds_lancamento_lancamento').val(),
        'ds_num_documento': $('#ds_num_documento_lancamento').val(),
        'ic_tipo_num_documento': $('#ic_tipo_num_documento_lancamento').val(),
        'tipo_grupo_lancamento_pk': $('#tipo_grupo_lancamento_pk').val(),
        'grupo_lancamento_pk': grupo_grid_lancamento_pk,
        'dt_cadastro': $.trim(localStorage.getItem('date_range_filter_cadastro')),
        'dt_faturamento': $.trim(localStorage.getItem('date_range_filter_faturamento')),
        'dt_vencimento': $.trim(localStorage.getItem('date_range_filter_vencimento')),
        'dt_pagamento': $.trim(localStorage.getItem('date_range_filter_pagamento')),
        'tipo_lancamento_pk': $('#tipo_lancamento_pk_lancamento').val(),
        'categorias_financeiras_pk': $('#categorias_financeiras_pk_lancamento').val(),
        'tipos_operacao_pk': $('#tipos_operacao_pk_lancamento').val(),
        'empresas_pk': $('#empresa_pk').val(),
        'contas_bancarias_pk': $('#contas_lancamento_pk').val()
    };
    arrCarregarGrid = carregarController("lancamento", "listarMiniDashboard", objParametros);
}

function fcCarregarCategoriaOperacaoLancamento() {
    var objParametros = {};
    var arrCarregarCombo = carregarController("categoria_financeira", "listarTodos", objParametros);
    carregarComboAjax($("#categorias_financeiras_pk_lancamento"), arrCarregarCombo, " ", "pk", "ds_categoria");
}

function fcCarregarTipoPlanoNegocioLancamento() {
    var objParametros = {
        "categorias_financeiras_pk": $("#categorias_financeiras_pk_lancamento").val()
    };
    var arrCarregarCombo = carregarController("plano_contas", "listaPorCategoria", objParametros);
    carregarComboAjax($("#tipos_operacao_pk_lancamento"), arrCarregarCombo, " ", "pk", "ds_tipo_operacao");
}


function fcCarregarFuncoesLancamento(){
    //Combo
    fcCarregarComboEmpresasLancamento();
    //$('#empresa_pk').select2();

    fcCarregarComboContasLancamento();
    $("#empresa_pk").change(function(){
        fcCarregarComboContasLancamento();
        $('#contas_lancamento_pk').select2();
    });

    $("#tipo_grupo_lancamento_pk").change(function(){
        fcListarGrupoLancamento();
    });
    fcCarregarUsuariosCadastroLancamento();
    fcCarregarCategoriaOperacaoLancamento();
    $("#categorias_financeiras_pk_lancamento").change(function () {
        fcCarregarTipoPlanoNegocioLancamento();
        $("#tipos_operacao_pk_lancamento").select2();
    });

    /*let today = new Date();
    let dd = today.getDate();
    let mm = today.getMonth()+1; //January is 0!
    let yyyy = today.getFullYear();
    let ultimoDia = new Date(date.getFullYear(), date.getMonth() - 1, 0)

    //data
    if(dd<10) {dd = '0'+dd} 
    if(mm<10) {mm = '0'+mm} 

    today = '01' + '/' + mm + '/' + yyyy;
    menosUmMes = ultimoDia + '/' + mm + '/' + yyyy;*/

    let today = new Date();
    let day = String(today.getDate()).padStart(2, '0');
    let month = String(today.getMonth() + 1).padStart(2, '0'); 
    let year = today.getFullYear();
    
    let primeiroDia = '01/' + month + '/' + year;
    
    let ultimoDiaMesAnterior = new Date(year, today.getMonth() - 1, 0);
    let ultimoDiaMes = ultimoDiaMesAnterior.getDate() + '/' + month + '/' + year;



    $('#dt_cadastro_ini_lancamento').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_cadastro_ini_lancamento").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_cadastro_fim_lancamento').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_cadastro_fim_lancamento").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_faturamento_ini_lancamento').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_faturamento_ini_lancamento").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_faturamento_fim_lancamento').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_faturamento_fim_lancamento").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_vencimento_ini_lancamento').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_vencimento_ini_lancamento").keypress(function(){
        mascara(this,mdata);
    });
    //$("#dt_vencimento_ini_lancamento").val(primeiroDia);

    $('#dt_vencimento_fim_lancamento').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_vencimento_fim_lancamento").keypress(function(){
        mascara(this,mdata);
    });
    $("#dt_vencimento_fim_lancamento").val(ultimoDiaMes);

    $('#dt_pagamento_ini_lancamento').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_pagamento_ini_lancamento").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_pagamento_fim_lancamento').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_pagamento_fim_lancamento").keypress(function(){
        mascara(this,mdata);
    });

    $(document).on('click', '#cmdPesqLancamento', fcCarregarGridLancamento);
    fcCarregarGridLancamento();


    
    
}



function filtrarDados(termoPesquisa) {
    
    // Limpa a tabela antes de filtrar os dados
    $('#tblLancamento tbody').empty();

    if (arrCarregarGrid.data.length > 0) {
        let count = 1;
        let vl_lancamento = "";
        let vl_despeda = "";
        let vl_total = "";
        let vl_saldo_mes = "";
        vl_lancamento = arrCarregarGrid.data[0]['vl_total_lancamento'];
        vl_total_lancamento_pendente = arrCarregarGrid.data[0]['vl_total_lancamento_pendente'];
        vl_despeda = arrCarregarGrid.data[0]['vl_total_despesa'];
        vl_total = arrCarregarGrid.data[0]['vl_total'];
        vl_total_saldo_mes = arrCarregarGrid.data[0]['vl_total_saldo_mes'];
        vl_saldo_mes_anterior = arrCarregarGrid.data[0]['vl_saldo_mes_anterior'];
        vl_saldo_atual = arrCarregarGrid.data[0]['vl_saldo_atual'];
        for (i = 0; i < arrCarregarGrid.data[0].DadosLancamento.length; i++) {
            


            let v_pk = "";
            let v_dt_cadastro = "";
            let v_ds_usuario = "";
            let v_dt_faturamento = "";
            let v_dt_vencimento = "";
            let v_dt_pagamento = "";
            let v_ds_operacao = "";
            let v_ds_tipo_operacao = "";
            let v_ds_metodo_pagamento = "";
            let v_vl_lancamento = "";
            let v_ds_status_pagamento = "";
            let nfse_pk = "";

            v_pk = arrCarregarGrid.data[0].DadosLancamento[i].pk;
            v_proximo_dt_vencimento = arrCarregarGrid.data[0].DadosLancamento[i].proxima_data;
            nfse_pk = arrCarregarGrid.data[0].DadosLancamento[i].nfse_pk;

            v_dt_cadastro = arrCarregarGrid.data[0].DadosLancamento[i].dt_cadastro;
            v_dt_cadastro = v_dt_cadastro == null ? '' : v_dt_cadastro;
            
            v_ds_usuario = arrCarregarGrid.data[0].DadosLancamento[i].ds_usuario;
            v_ds_usuario = v_ds_usuario == null ? '' : v_ds_usuario;
            
            v_ds_empresa = arrCarregarGrid.data[0].DadosLancamento[i].ds_empresa;
            v_ds_empresa = v_ds_empresa == null ? '' : v_ds_empresa;
            
            v_ds_conta_bancaria = arrCarregarGrid.data[0].DadosLancamento[i].ds_conta_bancaria;
            v_ds_conta_bancaria = v_ds_conta_bancaria == null ? '' : v_ds_conta_bancaria;

            v_dt_faturamento = arrCarregarGrid.data[0].DadosLancamento[i].dt_faturamento;
            v_dt_faturamento = v_dt_faturamento == null ? '' : v_dt_faturamento;

            v_dt_vencimento = arrCarregarGrid.data[0].DadosLancamento[i].dt_vencimento;
            v_dt_vencimento = v_dt_vencimento == null ? '' : v_dt_vencimento;

            v_dt_pagamento = arrCarregarGrid.data[0].DadosLancamento[i].dt_pagamento;
            v_dt_pagamento = v_dt_pagamento == null ? '' : v_dt_pagamento;

            v_ds_operacao = arrCarregarGrid.data[0].DadosLancamento[i].ds_operacao;
            v_ds_operacao = v_ds_operacao == null ? '' : v_ds_operacao;
            
            v_ds_tipo_operacao = arrCarregarGrid.data[0].DadosLancamento[i].ds_tipo_operacao;
            v_ds_tipo_operacao = v_ds_tipo_operacao == null ? '' : v_ds_tipo_operacao;

            v_ds_tipo_grupo = arrCarregarGrid.data[0].DadosLancamento[i].ds_tipo_grupo;
            v_ds_tipo_grupo = v_ds_tipo_grupo == null ? '' : v_ds_tipo_grupo;

            v_ds_recebido_pago_origem = arrCarregarGrid.data[0].DadosLancamento[i].ds_recebido_pago_origem;
            v_ds_recebido_pago_origem = v_ds_recebido_pago_origem == null ? '' : v_ds_recebido_pago_origem;

            v_ds_metodo_pagamento = arrCarregarGrid.data[0].DadosLancamento[i].ds_metodo_pagamento;
            v_ds_metodo_pagamento = v_ds_metodo_pagamento == null ? '' : v_ds_metodo_pagamento;

            v_vl_lancamento = arrCarregarGrid.data[0].DadosLancamento[i].vl_lancamento;
            v_vl_lancamento = v_vl_lancamento == null ? '' : v_vl_lancamento;

            v_vl_lancamento_pendente_dia = arrCarregarGrid.data[0].DadosLancamento[i].vl_lancamento_pendente_dia;
            v_vl_lancamento_pendente_dia = v_vl_lancamento_pendente_dia == null ? '' : v_vl_lancamento_pendente_dia;

            v_tipo_lancamento_pk = arrCarregarGrid.data[0].DadosLancamento[i].tipo_lancamento_pk;
            v_tipo_lancamento_pk = v_tipo_lancamento_pk == null ? '' : v_tipo_lancamento_pk;

            v_ds_status_pagamento = arrCarregarGrid.data[0].DadosLancamento[i].ds_status_pagamento;
            v_ds_status_pagamento = v_ds_status_pagamento == null ? '' : v_ds_status_pagamento;

            total_dia = arrCarregarGrid.data[0].DadosLancamento[i].total_dia;
            total_dia = total_dia == null ? '' : total_dia;

            vl_lancamento_dia = arrCarregarGrid.data[0].DadosLancamento[i].vl_lancamento_dia;
            vl_lancamento_dia = vl_lancamento_dia == null ? '' : vl_lancamento_dia;

            vl_pendente = arrCarregarGrid.data[0].DadosLancamento[i].vl_pendente;
            vl_pendente = vl_pendente == null ? '' : vl_pendente;

            vl_total_saldo_dia = arrCarregarGrid.data[0].DadosLancamento[i].vl_total_saldo_dia;
            vl_total_saldo_dia = vl_total_saldo_dia == null ? '' : vl_total_saldo_dia;

            ds_lancamento = arrCarregarGrid.data[0].DadosLancamento[i].ds_lancamento;
            ds_lancamento = ds_lancamento == null ? '' : ds_lancamento;

            ds_cpf_cnpj = arrCarregarGrid.data[0].DadosLancamento[i].ds_cpf_cnpj;
            ds_cpf_cnpj = ds_cpf_cnpj == null ? '' : ds_cpf_cnpj;
            if (ds_lancamento.toLowerCase().includes(termoPesquisa.toLowerCase())||
                ds_cpf_cnpj.toLowerCase().includes(termoPesquisa.toLowerCase())||
                v_ds_status_pagamento.toLowerCase().includes(termoPesquisa.toLowerCase())||
                v_ds_recebido_pago_origem.toLowerCase().includes(termoPesquisa.toLowerCase())
                ) {
                    $('#tblLancamento tbody').append('<tr id="tblLancamentoTr'+i+'"></tr>');
                    //$('#tblLancamentoTr'+i).append('<td>'+v_pk+'</td>');
                    $('#tblLancamentoTr'+i).append('<td>'+v_pk+'</td>');
                    $('#tblLancamentoTr'+i).append('<td>'+v_ds_status_pagamento+'</td>');
                    $('#tblLancamentoTr'+i).append('<td>'+v_ds_tipo_operacao+'</td>');
                    $('#tblLancamentoTr'+i).append('<td>'+ds_lancamento+'</td>');
                    //$('#tblLancamentoTr'+i).append('<td>'+v_ds_usuario+'</td>');
                    $('#tblLancamentoTr'+i).append('<td>'+v_ds_empresa+'</td>');
                    $('#tblLancamentoTr'+i).append('<td>'+v_ds_conta_bancaria+'</td>');
                    $('#tblLancamentoTr'+i).append('<td>'+v_dt_vencimento+'</td>');
                    $('#tblLancamentoTr'+i).append('<td>'+v_dt_pagamento+'</td>');
                    
                    
                    $('#tblLancamentoTr'+i).append('<td>'+v_ds_recebido_pago_origem+'</td>');
                    $('#tblLancamentoTr'+i).append('<td>'+ds_cpf_cnpj+'</td>');
                    //$('#tblLancamentoTr'+i).append('<td>'+v_ds_metodo_pagamento+'</td>');
                    $('#tblLancamentoTr'+i).append('<td><font color="blue">'+v_vl_lancamento+'</font></td>');
                    $('#tblLancamentoTr'+i).append('<td><font color="red">'+vl_pendente+'</font></td>');
                    $('#tblLancamentoTr'+i).append('<td><div>\n\
                                                            <div class="row">\n\
                                                                <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-pencil-square" title="Editar Lançamento" onclick="fcAbrirCadastroLancamento(' + v_pk + ')" ></i></div>\n\
                                                                <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-file-earmark-arrow-up" title="Anexo de Documentos" onclick="fcAnexarDocumento(' + v_pk + ')" ></i></div>\n\
                                                            </div>\n\
                                                            <div class="row">\n\
                                                                <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdImprimir" class="bi bi-printer" title="Imprimir Lançamento" onclick="fcImprimirLancamento(' + v_pk + ')"></i></div>\n\
                                                                <div class="col-md-4"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdExcluir" class="bi bi-x-circle" title="Excluir Lançamento" onclick="fcExcluirLancamento(' + v_pk + ')"></i></div>\n\
                                                               <div class="col-md-4" ' + (nfse_pk == null ? 'style="display: none;"' : '') + '><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-download" title="Download NFSE" onclick="fcDownloadNfseLancamento(' + nfse_pk + ')" ></i></div>\n\
                                                            </div>\n\
                                                        </div>\n\
                                                    </td>'
                                                    );
    
                    if(v_dt_vencimento != v_proximo_dt_vencimento || v_proximo_dt_vencimento == ""){
                        $('#tblLancamento tbody').append('<tr id="totaisLancamento'+i+'"></tr>');
                        $('#totaisLancamento'+i).append('<td align="right" width="100" colspan=9 >&nbsp;<b>Saldo do dia '+ v_dt_vencimento +'</b></td>');
                        $('#totaisLancamento'+i).append('<td width="100" ><b>Totais R$<b/></td>');
                        $('#totaisLancamento'+i).append('<td align="center"><font color="blue">' + vl_lancamento_dia + '</font></td>');
                        $('#totaisLancamento'+i).append('<td align="center"><font color="red">' + v_vl_lancamento_pendente_dia + '</font></td>');
                        $('#totaisLancamento'+i).append('<td align="center"></td>');
                    }
    
                    $('#totalLancamento').html(vl_lancamento);
                    $('#totalLancamentoPendente').html(vl_total_lancamento_pendente);
    
    
                
                count++;
            }
            
        }
    } else {

    }
}