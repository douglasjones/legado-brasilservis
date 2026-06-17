function fcListarCombo(){
    let today = new Date();
    let year = today.getFullYear();

    for(let i=20; i<31; i++){
        $('#ds_ano').append('<option value="20'+i+'">20'+i+'</option>')
    }
    $('#ds_ano option[value='+year+']').prop('selected', true);
}

function fcCarregarComboEmpresas() {
    var objParametros = {};
    var arrCarregar = carregarController("conta_bancaria", "listarEmpresaContasAtivas", objParametros);
    carregarComboAjax($("#empresas_pk_extrato_mes"), arrCarregar, "", "pk", "ds_conta");
}

function fcCarregarComboContas() {
    var objParametros = {
        "empresas_pk": $("#empresas_pk_extrato_mes").val()
    };
    var arrCarregar = carregarController("conta_bancaria", "listarContasLancamento", objParametros);
    carregarComboAjax($("#contas_pk_extrato_mes"), arrCarregar, "", "pk", "ds_dados_conta");
}

function fcExcluirLancamentoExtrato(v_pk) {
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("lancamento", "excluir", objParametros);

            if (arrExcluir.status == true){

                fcCarregarGridExtrato();
                utilsJS.toastNotify(true, arrExcluir.message)

            }else{
                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão ');
            }
        }
        else{
            utilsJS.toastNotify(false, 'Código não encontrado');
        }
    });
}

function fcDatasExtratoMes(mes) {
    let today = new Date();
    let mm = mes == "" ? today.getMonth() + 1 : mes;

    if (mm == '1') {
        $("#ic_mes").val(1);
        fcRemoverClassActiveButton();
        $('#ic_jan').addClass('active');
    } else if (mm == '2') {
        $("#ic_mes").val(2);
        fcRemoverClassActiveButton();
        $('#ic_fev').addClass('active');
    } else if (mm == '3') {
        $("#ic_mes").val(3);
        fcRemoverClassActiveButton();
        $('#ic_mar').addClass('active');
    } else if (mm == '4') {
        $("#ic_mes").val(4);
        fcRemoverClassActiveButton();
        $('#ic_abr').addClass('active');
    } else if (mm == '5') {
        $("#ic_mes").val(5);
        fcRemoverClassActiveButton();
        $('#ic_mai').addClass('active');
    } else if (mm == '6') {
        $("#ic_mes").val(6);
        fcRemoverClassActiveButton();
        $('#ic_jun').addClass('active');
    } else if (mm == '7') {
        $("#ic_mes").val(7);
        fcRemoverClassActiveButton();
        $('#ic_jul').addClass('active');
    } else if (mm == '8') {
        $("#ic_mes").val(8);
        fcRemoverClassActiveButton();
        $('#ic_ago').addClass('active');
    } else if (mm == '9') {
        $("#ic_mes").val(9);
        fcRemoverClassActiveButton();
        $('#ic_set').addClass('active');
    } else if (mm == '10') {
        $("#ic_mes").val(10);
        fcRemoverClassActiveButton();
        $('#ic_out').addClass('active');
    } else if (mm == '11') {
        $("#ic_mes").val(11);
        fcRemoverClassActiveButton();
        $('#ic_nov').addClass('active');
    } else if (mm == '12') {
        $("#ic_mes").val(12);
        fcRemoverClassActiveButton();
        $('#ic_dez').addClass('active');
    }
    
    fcCarregarGridExtrato();
}

function fcRemoverClassActiveButton() {
    $('#ic_jan').removeClass('active');
    $('#ic_fev').removeClass('active');
    $('#ic_mar').removeClass('active');
    $('#ic_abr').removeClass('active');
    $('#ic_mai').removeClass('active');
    $('#ic_jun').removeClass('active');
    $('#ic_jul').removeClass('active');
    $('#ic_ago').removeClass('active');
    $('#ic_set').removeClass('active');
    $('#ic_out').removeClass('active');
    $('#ic_nov').removeClass('active');
    $('#ic_dez').removeClass('active');
}

function fcEventosExtratoMes(){
    let today = new Date();
    let mes = today.getMonth() + 1;
    fcDatasExtratoMes(mes);
    $(document).on('click', '#ic_jan', function () {
        fcDatasExtratoMes('1', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_fev', function () {
        fcDatasExtratoMes('2', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_mar', function () {
        fcDatasExtratoMes('3', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_abr', function () {
        fcDatasExtratoMes('4', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_mai', function () {
        fcDatasExtratoMes('5', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_jun', function () {
        fcDatasExtratoMes('6', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_jul', function () {
        fcDatasExtratoMes('7', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_ago', function () {
        fcDatasExtratoMes('8', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_set', function () {
        fcDatasExtratoMes('9', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_out', function () {
        fcDatasExtratoMes('10', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_nov', function () {
        fcDatasExtratoMes('11', $("#ds_ano").val());
    });
    $(document).on('click', '#ic_dez', function () {
        fcDatasExtratoMes('12', $("#ds_ano").val());
    });
}

function fcCarregarGridExtrato(){
    $("#tblExtrato tbody").remove();
    let objParametros = {
        'empresas_pk': $('#empresas_pk_extrato_mes').val(),
        'contas_bancarias_pk': $('#contas_pk_extrato_mes').val(),
        'dt_cadastro_ini': $('#dt_cadastro_ini_extrato').val(),
        'dt_cadastro_fim': $('#dt_cadastro_fim_extrato').val(),
        'dt_faturamento_ini': $('#dt_faturamento_ini_extrato').val(),
        'dt_faturamento_fim': $('#dt_faturamento_fim_extrato').val(),
        'dt_pagamento_ini': $('#dt_pagamento_ini_extrato').val(),
        'dt_pagamento_fim': $('#dt_pagamento_fim_extrato').val(),
        /*'dt_vencimento_ini': $('#dt_vencimento_ini_receita').val(),
        'dt_vencimento_fim': $('#dt_vencimento_fim_receita').val(),*/
        'ds_ano': $('#ds_ano').val(),
        'ds_mes': $('#ic_mes').val()
    };
    let arrCarregar = carregarController("lancamento", "listarExtratoMes", objParametros);
    if(arrCarregar.data.length > 0){
        $('#tblExtrato').append('<tbody></tbody>');
            let count = 1;
            let vl_receita = 0.00;
            let vl_despesa = 0.00;
            let vl_total = "";
            let vl_saldo_mes = "";

            vl_receita = arrCarregar.data[0]['vl_total_receita'];
            vl_despesa = arrCarregar.data[0]['vl_total_despesa'];
            vl_total = arrCarregar.data[0]['vl_total'];
            vl_total_saldo_mes = arrCarregar.data[0]['vl_total_saldo_mes'];
            vl_saldo_mes_anterior = arrCarregar.data[0]['vl_saldo_mes_anterior'];
            vl_saldo_atual = arrCarregar.data[0]['vl_saldo_atual'];
            for (i = 0; i < arrCarregar.data[0].DadosExtrato.length; i++) {
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
                let v_vl_receita = 0.00;
                let v_vl_despesa = 0.00;
                let v_vl_lancamento = 0.00;
                let v_ic_status_pagamento = "";

                v_pk = arrCarregar.data[0].DadosExtrato[i].pk;
                v_proximo_dt_vencimento = arrCarregar.data[0].DadosExtrato[i].proxima_data;

                v_dt_cadastro = arrCarregar.data[0].DadosExtrato[i].dt_cadastro;
                v_dt_cadastro = v_dt_cadastro == null ? '' : v_dt_cadastro;
                
                v_ds_usuario = arrCarregar.data[0].DadosExtrato[i].ds_usuario;
                v_ds_usuario = v_ds_usuario == null ? '' : v_ds_usuario;

                v_dt_faturamento = arrCarregar.data[0].DadosExtrato[i].dt_faturamento;
                v_dt_faturamento = v_dt_faturamento == null ? '' : v_dt_faturamento;

                v_dt_vencimento = arrCarregar.data[0].DadosExtrato[i].dt_vencimento;
                v_dt_vencimento = v_dt_vencimento == null ? '' : v_dt_vencimento;

                v_dt_pagamento = arrCarregar.data[0].DadosExtrato[i].dt_pagamento;
                v_dt_pagamento = v_dt_pagamento == null ? '' : v_dt_pagamento;

                v_ds_operacao = arrCarregar.data[0].DadosExtrato[i].ds_operacao;
                v_ds_operacao = v_ds_operacao == null ? '' : v_ds_operacao;
                
                v_ds_tipo_operacao = arrCarregar.data[0].DadosExtrato[i].ds_tipo_operacao;
                v_ds_tipo_operacao = v_ds_tipo_operacao == null ? '' : v_ds_tipo_operacao;

                v_ds_tipo_grupo = arrCarregar.data[0].DadosExtrato[i].ds_tipo_grupo;
                v_ds_tipo_grupo = v_ds_tipo_grupo == null ? '' : v_ds_tipo_grupo;

                v_ds_recebido_pago_origem = arrCarregar.data[0].DadosExtrato[i].ds_recebido_pago_origem;
                v_ds_recebido_pago_origem = v_ds_recebido_pago_origem == null ? '' : v_ds_recebido_pago_origem;

                v_ds_metodo_pagamento = arrCarregar.data[0].DadosExtrato[i].ds_metodo_pagamento;
                v_ds_metodo_pagamento = v_ds_metodo_pagamento == null ? '' : v_ds_metodo_pagamento;

                v_vl_lancamento = arrCarregar.data[0].DadosExtrato[i].vl_lancamento;
                v_vl_lancamento = v_vl_lancamento == null ? '' : v_vl_lancamento;

                v_tipo_lancamento_pk = arrCarregar.data[0].DadosExtrato[i].tipo_lancamento_pk;
                v_tipo_lancamento_pk = v_tipo_lancamento_pk == null ? '' : v_tipo_lancamento_pk;

                v_vl_baixa_parcial = arrCarregar.data[0].DadosExtrato[i].v_vl_baixa_parcial;
                v_vl_baixa_parcial = v_vl_baixa_parcial == null ? '' : v_vl_baixa_parcial;
                
                if (v_vl_lancamento != null && v_tipo_lancamento_pk == 1) {
                    v_vl_receita = v_vl_lancamento;
                }else if(v_vl_lancamento != null && v_tipo_lancamento_pk != 1){
                    v_vl_despesa = v_vl_lancamento;
                }

                total_dia = arrCarregar.data[0].DadosExtrato[i].total_dia;
                total_dia = total_dia == null ? '' : total_dia;

                receita_dia = arrCarregar.data[0].DadosExtrato[i].receita_dia;
                receita_dia = receita_dia == null ? '' : receita_dia;

                despesa_dia = arrCarregar.data[0].DadosExtrato[i].despesa_dia;
                despesa_dia = despesa_dia == null ? '' : despesa_dia;

                vl_lancamento = arrCarregar.data[0].DadosExtrato[i].vl_lancamento;
                vl_lancamento = vl_lancamento == null ? '' : vl_lancamento;

                vl_receita_pendente_dia = arrCarregar.data[0].DadosExtrato[i].vl_receita_pendente_dia;
                vl_receita_pendente_dia = vl_receita_pendente_dia == null ? '' : vl_receita_pendente_dia;

                vl_total_saldo_dia = arrCarregar.data[0].DadosExtrato[i].vl_total_saldo_dia;
                vl_total_saldo_dia = vl_total_saldo_dia == null ? '' : vl_total_saldo_dia;

                ds_lancamento = arrCarregar.data[0].DadosExtrato[i].ds_lancamento;
                ds_lancamento = ds_lancamento == null ? '' : ds_lancamento;

                ds_cpf_cnpj = arrCarregar.data[0].DadosExtrato[i].ds_cpf_cnpj;
                ds_cpf_cnpj = ds_cpf_cnpj == null ? '' : ds_cpf_cnpj;

                $('#tblExtrato tbody').append('<tr id="tblExtratoTr'+i+'"></tr>');
                //$('#tblExtratoTr'+i).append('<td>'+v_pk+'</td>');
                $('#tblExtratoTr'+i).append('<td>'+ds_lancamento+'</td>');
                $('#tblExtratoTr'+i).append('<td>'+v_dt_cadastro +'</td>');
                //$('#tblExtratoTr'+i).append('<td>'+v_ds_usuario +'</td>');
                $('#tblExtratoTr'+i).append('<td>'+v_dt_faturamento +'</td>');
                $('#tblExtratoTr'+i).append('<td>'+v_dt_vencimento +'</td>');
                $('#tblExtratoTr'+i).append('<td>'+v_dt_pagamento +'</td>');
                $('#tblExtratoTr'+i).append('<td>'+v_ds_operacao +'</td>');
                $('#tblExtratoTr'+i).append('<td>'+v_ds_tipo_operacao +'</td>');
                $('#tblExtratoTr'+i).append('<td>'+v_ds_tipo_grupo +'</td>');
                $('#tblExtratoTr'+i).append('<td>'+v_ds_recebido_pago_origem +'</td>');
                $('#tblExtratoTr'+i).append('<td>'+ds_cpf_cnpj +'</td>');
                //$('#tblExtratoTr'+i).append('<td>'+v_ds_metodo_pagamento +'</td>');
                $('#tblExtratoTr'+i).append('<td><font color="blue">'+v_vl_receita +'</font></td>');
                $('#tblExtratoTr'+i).append('<td><font color="red">'+v_vl_despesa +'</font></td>');
                $('#tblExtratoTr'+i).append('<td><font color="red">'+vl_total_saldo_dia +'</font></td>');
                $('#tblExtratoTr'+i).append('<td><div>\n\
                                                        <div class="row">\n\
                                                            <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-pencil-square" title="Editar Lançamento" onclick="fcAbrirCadastroLancamento(' + v_pk + ')" ></i></div>\n\
                                                            <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" class="bi bi-file-earmark-arrow-up" title="Anexo de Documentos" onclick="fcAnexarDocumento(' + v_pk + ')" ></i></div>\n\
                                                        </div>\n\
                                                        <div class="row">\n\
                                                            <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdImprimir" class="bi bi-printer" title="Imprimir Lançamento" onclick="fcImprimirLancamento(' + v_pk + ')"></i></div>\n\
                                                            <div class="col-md-6"><i style="font-size: 18px;color: blue;cursor: pointer" id="cmdExcluir" class="bi bi-x-circle" title="Excluir Lançamento" onclick="fcExcluirLancamentoExtrato(' + v_pk + ')"></i></div>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </td>'
                                                );

                if(v_dt_vencimento != v_proximo_dt_vencimento || v_proximo_dt_vencimento == ""){
                    $('#tblExtrato tbody').append('<tr id="totaisExtrato'+i+'"></tr>');
                    $('#totaisExtrato'+i).append('<td align="right" width="100" colspan=9>&nbsp;<b>Saldo do dia '+ v_dt_vencimento +'</b></td>');
                    $('#totaisExtrato'+i).append('<td width="100" ><b>Totais R$<b/></td>');
                    $('#totaisExtrato'+i).append('<td align="center"><font color="blue">' + receita_dia + '</font></td>');
                    $('#totaisExtrato'+i).append('<td align="center"><font color="red">' + despesa_dia + '</font></td>');
                    $('#totaisExtrato'+i).append('<td align="center">'+vl_total_saldo_dia+'</td>');
                    $('#totaisExtrato'+i).append('<td align="center">&nbsp; </td>');
                }
                
                count++;
            }

            
            $('#totalExtratoReceita').html(vl_receita);
            $('#totalExtratoDespesa').html(vl_despesa);
            $('#totalExtratoSaldo').html(vl_saldo_atual);
            $('#totalExtratoSaldoMesAnterior').html(vl_saldo_mes_anterior);
            $('#totalExtrato').html(vl_total_saldo_mes);

    }
}


function fcCarregarFuncoesExtrato(){
    //Combo
    fcListarCombo();
    fcCarregarComboEmpresas();
    $('#empresas_pk_extrato_mes').select2();

    fcCarregarComboContas();
    $('#contas_pk_extrato_mes').select2();

    $("#empresas_pk_extrato_mes").change(function(){
        fcCarregarComboContas();
        $('#contas_pk_extrato_mes').select2();
        fcCarregarGridExtrato();
    });

    $("#contas_pk_extrato_mes").change(function () {
        fcCarregarGridExtrato();
    });

    $("#ds_ano").change(function () {
        fcCarregarGridExtrato();
    });

    $('#dt_cadastro_ini_extrato').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_cadastro_ini_extrato").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_cadastro_fim_extrato').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_cadastro_fim_extrato").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_faturamento_ini_extrato').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_faturamento_ini_extrato").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_faturamento_fim_extrato').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_faturamento_fim_extrato").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_pagamento_ini_extrato').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_pagamento_ini_extrato").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_pagamento_fim_extrato').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_pagamento_fim_extrato").keypress(function(){
        mascara(this,mdata);
    });

    
    /*$('#dt_vencimento_ini_receita').datepicker({
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
    });*/

    //Eventos
    fcEventosExtratoMes();
    $(document).on('click', '#cmdPesqExtrato', fcCarregarGridExtrato);
}