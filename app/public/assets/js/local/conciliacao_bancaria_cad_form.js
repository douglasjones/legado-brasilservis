let financeiro_conciliacao_banco_itens_pk = "";
let lancamentos_pk ="";
function fcValidarForm(){

    $("#form").validate({
        rules :{
            /*ds_link_arquivo:{
                required:true,
                minlength:3
            },
            vl_saldo_conta:{
                required:true,
                minlength:3
            },
            dt_ini_periodo_saldo:{
                required:true,
                minlength:3
            },
            dt_fim_periodo_saldo:{
                required:true,
                minlength:3
            },
            ds_obs:{
                required:true,
                minlength:3
            },
            ic_status:{
                required:true,
                minlength:3
            },
            contas_bancarias_pk:{
                required:true,
                minlength:3
            }*/

        },
        messages:{
            /*ds_link_arquivo:{
                required:"Por favor, informe ",
                minlength:" deve ter pelo menos 3 caracteres"
            },
            vl_saldo_conta:{
                required:"Por favor, informe ",
                minlength:" deve ter pelo menos 3 caracteres"
            },
            dt_ini_periodo_saldo:{
                required:"Por favor, informe ",
                minlength:" deve ter pelo menos 3 caracteres"
            },
            dt_fim_periodo_saldo:{
                required:"Por favor, informe ",
                minlength:" deve ter pelo menos 3 caracteres"
            },
            ds_obs:{
                required:"Por favor, informe ",
                minlength:" deve ter pelo menos 3 caracteres"
            },
            ic_status:{
                required:"Por favor, informe ",
                minlength:" deve ter pelo menos 3 caracteres"
            },
            contas_bancarias_pk:{
                required:"Por favor, informe ",
                minlength:" deve ter pelo menos 3 caracteres"
            }*/

        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });

}
function fcEnviar(){

    if(qtdeArquivo==0){
        sweetMensagem('warning',"Por favor, inserir anexo OFX.");
        return false;
    }
    for(var x=0;x<$("#form").serializeArray().length;x++){
        formdata.append($('#form').serializeArray()[x].name,$('#form').serializeArray()[x].value)
    }
    $.ajax({
        type:'POST',
        url:"/api/conciliacao_bancaria/salvar",
        data:formdata,
        processData:false,
        contentType: false,
        complete: function(response){
           try{
               var log = JSON.parse(response.responseText);
               if(log.status==true){
                   sendPost("conciliacao_bancaria","receptivo",{});
               }
           }
           catch (e) {

           }
        }

    });
}

function fcCancelar(){
    sendPost("conciliacao_bancaria","receptivo",{});
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };

        var arrCarregar = carregarController("conciliacao_bancaria", "listarPk", objParametros);

        if (arrCarregar.status == true){

            $("#empresas_pk").val(arrCarregar.data[0]['empresas_pk']);
            $("#edit_empresas_pk").val(arrCarregar.data[0]['empresas_pk']);
            $("#vl_saldo_conta").val("R$"+float2moeda(arrCarregar.data[0]['vl_saldo_conta']));
            $("#periodo").val(arrCarregar.data[0]['dt_ini_periodo_saldo']+" - "+arrCarregar.data[0]['dt_fim_periodo_saldo']);
            $("#dt_periodo_fim").val(arrCarregar.data[0]['dt_fim_periodo_saldo']);
            $("#dt_periodo_ini").val(arrCarregar.data[0]['dt_ini_periodo_saldo']);
            $("#obs").val(arrCarregar.data[0]['ds_obs']);
            setTimeout(function(){
                $("#contas_pk").val(arrCarregar.data[0]['contas_bancarias_pk']);
                $("#edit_contas_pk").val(arrCarregar.data[0]['contas_bancarias_pk']);
            }, 1000);

        }
        else{
            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
        }

        $("#exibirAnexo").hide();
        $("#cmdIncluir").hide();
        $("#exibirContaCad").hide();
        $("#exibirEmpresaCad").hide();
        $("#exibirEmpresaEdit").show();
        $("#exibirContaEdit").show();
        $("#exibirObsCad").hide();
        $("#exibirObsEdit").show();
        $("#exibirDatatable").show();
        $("#exibirSaldo").show();

        setTimeout(function(){
            fcCarregarDatatable();
            fcCarregarDatatableReceitaDespesas();
        }, 1000);

    }
    else{
        $("#exibirAnexo").show();
        $("#cmdIncluir").show();
        $("#exibirContaCad").show();
        $("#exibirContaEdit").hide();
        $("#exibirEmpresaCad").show();
        $("#exibirEmpresaEdit").hide();
        $("#exibirObsCad").show();
        $("#exibirObsEdit").hide();
        $("#exibirDatatable").hide();
        $("#exibirSaldo").hide();

    }
}

function fcCarregarDatatable(){


    var objParametros = {
        "financeiro_conciliacao_banco_pk": $("#pk").val()
    };

    var v_url = routes_api("conciliacao_bancaria", "listarDataTableItens", objParametros);
    //Trata a tabela
    tblResultado = $('#tblResultado').DataTable({
        searching: false,
        paging: true,
        scrollX: true,
        scrollY: '450px',
        scrollCollapse: true,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
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
                    return "<a class='function_selecionar'><input type='checkbox' name='conciliacao[]' ></a>";
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
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
                    return full['t_dt_transacao'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_transacao'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_vl_transacao'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_lancamentos_pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_estabelecimento'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {

                    var buttonEdit = "<a class='function_edit'><span><i class='bi bi-pencil-square' style='font-size:18px; color:blue' title='Editar'></i></span></a>&nbsp;";
                    return buttonEdit;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ],
        "rowCallback": function( row, data, index ) {
            if(data.t_ic_tipo_transacao==1){
                $(row).css('background-color','#ffb4b4');

            }
            if(data.t_lancamentos_pk != null && data.t_ic_status == 1){
                $('td:eq(0)', row).html("<input type='checkbox' disabled name='checado' checked ></input>");
            }
        }
    });

    $('#tblResultado tbody').on('click', '.function_selecionar', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcSelecionarCheckBoxConciliacao(data['t_pk']);
    });

    $('#tblResultado tbody').on('click', '.function_edit', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcAbrirModal(data);

    });

}

function fcAbrirModal(objParametros){
    $("#janela_modal").modal("show");
    $("#financeiro_conciliacao_lancamentos_pk").text("");
    $("#codigo_modal").val("");
    $("#data_modal").text("");
    $("#tipo_modal").text("");
    $("#valor_modal").text("");
    $("#codigo_veri_modal").text("");
    $("#estabelecimento_modal").text("");
    $("#obs_modal").val("");

    if(objParametros['t_ic_status_fl']==null){
        $("#ic_status_modal").val(2);
    }
    else{
        $("#ic_status_modal").val(objParametros['t_ic_status_fl']);
    }

    $("#financeiro_conciliacao_lancamentos_pk").val(objParametros['t_financeiro_conciliacao_lancamentos_pk']);
    $("#codigo_modal").text(objParametros['t_pk']);
    $("#data_modal").text(objParametros['t_dt_transacao']);
    $("#tipo_modal").text(objParametros['t_ds_transacao']);
    $("#valor_modal").text(objParametros['t_vl_transacao']);
    $("#codigo_veri_modal").text(objParametros['t_lancamentos_pk']);
    $("#estabelecimento_modal").text(objParametros['t_ds_estabelecimento']);
    $("#obs_modal").val(objParametros['t_obs_fl']);

    lancamentos_pk = objParametros['t_lancamentos_pk'];
    financeiro_conciliacao_banco_itens_pk = objParametros['t_pk'];


}

function fecharModal(){
    $("#janela_modal").modal("hide");
}

function fcCarregarDatatableReceitaDespesas(){


    var v_empresas_pk = $("#empresas_pk").val();
    var v_contas_bancarias_pk = $("#contas_pk").val();
    var dt_periodo_ini = $("#dt_periodo_ini").val();
    var dt_periodo_fim = $("#dt_periodo_fim").val();

    var objParametros = {
        "empresas_pk": v_empresas_pk,
        "contas_bancarias_pk": v_contas_bancarias_pk,
        "dt_periodo_ini": dt_periodo_ini,
        "dt_periodo_fim": dt_periodo_fim,
    };

    var v_url = routes_api("lancamento", "listarDataTableReceitaDespesaConciliacao", objParametros);
    //Trata a tabela
    tblResultadoReceitaDespesa = $('#tblResultadoReceitaDespesa').DataTable({
        searching: false,
        paging: true,
        scrollX: true,
        scrollY: '450px',
        scrollCollapse: true,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
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
                    return "<a class='funcion_selecionar_lancamento'><input type='checkbox' name='conciliacao[]' ></a>";
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_vencimento'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_operacao'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['vl_lancamento'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['financeiro_conciliacao_banco_itens_pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_recebido_pago_origem'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            }
        ],
        "rowCallback": function( row, data, index ) {
            $("#saldo_lancamento").val(data.vl_total_saldo_dia)
            if(data.operacao_pk!=1){
                $(row).css('background-color','#ffb4b4');
            }
            if(data.financeiro_conciliacao_banco_itens_pk != null && data.ic_status_conciliacao == "1"){
                $('td:eq(0)', row).html("<input type='checkbox' disabled name='checado' checked ></input>");
            }

        }
    });

    $('#tblResultadoReceitaDespesa tbody').on('click', '.funcion_selecionar_lancamento', function () {
        var data;
        if (tblResultadoReceitaDespesa.row($(this).parents('li')).data()) {
            data = tblResultadoReceitaDespesa.row($(this).parents('li')).data();
        }
        else if (tblResultadoReceitaDespesa.row($(this).parents('tr')).data()) {
            data = tblResultadoReceitaDespesa.row($(this).parents('tr')).data();
        }
        fcSelecionarCheckBoxLancamentos(data['pk']);
    });
}


function fcSelecionarCheckBoxConciliacao(pk){

    var checado = $("input[name='conciliacao[]']:checked").length;

    if(checado == 1){
        financeiro_conciliacao_banco_itens_pk = pk;
        $("input[name='lancamentoReceitaDespesa[]']").prop('disabled', false);
        $("#alert_lancamento").css('display','inline');
        $("#alert_conciliacao").css('display','none');
    }
    else if(checado > 1){
        sweetMensagem('warning',"Selecione Apenas uma opção!!!");
        $("input[name='conciliacao[]']").prop('checked', false);
        $("input[name='lancamentoReceitaDespesa[]']").prop('disabled', true);
        $("input[name='lancamentoReceitaDespesa[]']").prop('checked', false);
        financeiro_conciliacao_banco_itens_pk = "";
        $("#alert_conciliacao").css('display','inline');
        $("#alert_lancamento").css('display','none');
    }
    else{
        $("input[name='lancamentoReceitaDespesa[]']").prop('disabled', true);
        financeiro_conciliacao_banco_itens_pk = "";
        $("#alert_lancamento").css('display','none');
        $("#alert_conciliacao").css('display','inline');
    }

}

function fcSelecionarCheckBoxLancamentos(pk){
    var checado = $("input[name='lancamentoReceitaDespesa[]']:checked").length;

    if(checado == 1){
        lancamentos_pk = pk;
        $("#alert_lancamento").css('display','none');
        $("#alert_conciliacao").css('display','none');
        $("#alert_button").css('display','inline');
    }
    else if(checado > 1){
        sweetMensagem('warning',"Selecione Apenas uma opção!!!");
        $("input[name='lancamentoReceitaDespesa[]']").prop('checked', false);
        lancamentos_pk = "";
        $("#alert_conciliacao").css('display','none');
        $("#alert_lancamento").css('display','inline');
        $("#alert_button").css('display','none');
    }
    else{
        $("input[name='conciliacao[]']").prop('disabled', true);
        lancamentos_pk = "";
        $("#alert_conciliacao").css('display','none');
        $("#alert_lancamento").css('display','inline');
        $("#alert_button").css('display','none');
    }

}

function fcSalvarConciliacaoLancamento(){
    var ic_status = "";
    if($("#ic_status_modal").val()==""){
        ic_status = 1;
    }
    else{
        ic_status = $("#ic_status_modal").val();
    }

    var objParametros = {
        "pk":$("#financeiro_conciliacao_lancamentos_pk").val(),
        "lancamentos_pk": lancamentos_pk,
        "financeiro_conciliacao_banco_itens_pk": (financeiro_conciliacao_banco_itens_pk),
        "obs":$("#obs_modal").val(),
        "ic_status":ic_status
    };

    var arrEnviar = carregarController("conciliacao_bancaria", "salvarConciliacaoLancamento", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        tblResultado.ajax.reload();
        tblResultadoReceitaDespesa.ajax.reload();
        $("input[name='conciliacao[]']").prop('disabled', false);
        $("input[name='conciliacao[]']").prop('checked', false);
        $("input[name='lancamentoReceitaDespesa[]']").prop('disabled', false);
        $("input[name='lancamentoReceitaDespesa[]']").prop('checked', false);
        $("#alert_conciliacao").css('display','inline');
        $("#alert_lancamento").css('display','none');
        $("#alert_button").css('display','none');
        lancamentos_pk = "";
        financeiro_conciliacao_banco_itens_pk = "";
        $("#janela_modal").modal("hide");
    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcCarregarComboEmpresas() {
    var objParametros = {

    };
    var arrCarregar = carregarController("conta_bancaria", "listarEmpresaContasAtivas", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#empresas_pk"), arrCarregar, "", "pk", "ds_conta");
    carregarComboAjax($("#edit_empresas_pk"), arrCarregar, "", "pk", "ds_conta");
}

function fcCarregarComboContas() {
    var objParametros = {
        "empresa_pk": $("#empresas_pk").val()
    };
    var arrCarregar = carregarController("conta_bancaria", "listaPorEmpresa", objParametros);
    carregarComboAjax($("#contas_pk"), arrCarregar, "", "pk", "ds_conta");
    carregarComboAjax($("#edit_contas_pk"), arrCarregar, "", "pk", "ds_conta");
}


$(function () {

    $('#fileupload').fileupload({

        dataType: 'json',
        done: function (e, data) {
            window.setTimeout('Reset()', 2000);
            $.each(data.files, function (index, file) {
                $("#ds_nome_original").text(file.name);
                utilsJS.toastNotify(true,"Sucesso ao subir o arquivo");

            });
        },
        fail: function (data) {
            utilsJS.toastNotify(false,"Falha ao subir o arquivo");
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css('width', progress + '%');
        }
    });
});

function Reset(){
    $('#progress .progress-bar').css('width', '0%');
}

function fsClean() {
    $('#progress .progress-bar').css('width', '0%');
}

var formdata = null;
let qtdeArquivo = 0;
$(document).ready(function()
    {
        $("#exibirAnexo").hide();
        $("#exibirContaCad").hide();
        $("#exibirContaEdit").hide();
        $("#exibirDatatable").hide();
        $("#exibirSaldo").hide();
        formdata = new FormData();

        fcCarregarComboEmpresas();
        fcCarregar();
        fcCarregarComboContas();
        //Atribui os eventos
        $(document).on('click', '#cmdCancelar', fcCancelar);

        $(document).on('click', '#salvarConciliacaoLancamento', fcSalvarConciliacaoLancamento);
        $(document).on('click', '#cmdEnviarModal', fcSalvarConciliacaoLancamento);
        $(document).on('click', '#cmdIncluir', fcEnviar);

        //Verifica se o registro é para alteracao e puxa os dados.
        //Combo de Contas
        $("#fileupload").change(function(){
            if($(this).prop('files').length > 0){
                //alert(JSON.stringify($(this).prop('files')));
                for(var x = 0;x < $(this).prop('files').length;x++){
                    files = $(this).prop('files')[x];
                    formdata.append(x, files);
                }
                qtdeArquivo++;
            }
        });

        $("#empresas_pk").change(function () {
            //$(".chzn-select").chosen('destroy');
            fcCarregarComboContas();
        });

    }
);
