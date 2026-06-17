var tblResultado;
function fcPesquisar() {
    tblResultado.clear().destroy();
    fcCarregarGrid();

}

function fcIncluir() {
    var objParametros = {};
    sendPost('analise_financeira','cadForm' ,objParametros);
}

function fcEditar(v_pk, lancamento_old_pk) {
    var objParametros = {
        "pk":v_pk,
        "lancamento_old_pk": lancamento_old_pk
    };
    sendPost('analise_financeira','cadForm' ,objParametros);
}

function fcCancelar() {
    var objParametros = {};
    sendPost('menu','financeiro' ,objParametros);
}

function fcExcluir(v_pk){
    utilsJS.jqueryConfirm('Excluir ?', 'Deseja excluir o registro '+v_pk+'?',function(){
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };              
            
            var arrExcluir = carregarController("analise_financeira", "excluir", objParametros);   

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true, arrExcluir.message);

                // Reload datable
                tblResultado.ajax.reload();

            }
            else{
                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
            }
        }
        else{
            sweetMensagem('warning', 'Código não encontrado');
        }
    });
}




function fcCarregarGrid() {
    var objParametros = {
        "ic_status": $("#ic_status").val(),
        "lancamento_pk": $("#lancamento_pk").val(),
        "dt_cadastro_ini": $("#dt_cadastro_ini").val(),
        "dt_cadastro_fim": $("#dt_cadastro_fim").val(),
        "dt_aprovacao_ini": $("#dt_aprovacao_ini").val(),
        "dt_aprovacao_fim": $("#dt_aprovacao_fim").val(),
        "dt_correcao_ini": $("#dt_correcao_ini").val(),
        "dt_correcao_fim": $("#dt_correcao_fim").val(),
        "dt_recusa_ini": $("#dt_recusa_ini").val(),
        "dt_recusa_fim": $("#dt_recusa_fim").val(),
        "dt_vencimento_ini": $("#dt_vencimento_ini").val(),
        "dt_vencimento_fim": $("#dt_vencimento_fim").val(),
        "usuario_cadastro_lancamento_pk": $("#usuario_cadastro_lancamento_pk").val(),
        "usuario_cadastro_analista_pk": $("#usuario_cadastro_analista_pk").val(),
        "usuario_cadastro_gestor_pk": $("#usuario_cadastro_gestor_pk").val()

    };

    var v_url = routes_api("analise_financeira", "listarGrid", objParametros);

    //Trata a tabela
    tblResultado = $('#tblResultado').DataTable({
        searching: true,
        paging: true,
        scrollX: true,
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
                    return full['t_pk'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['t_lancamentos_pk'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_usuario_cadastro_lancamento_pk'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_cadastro_lancamento'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['ic_status'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_aprovacao'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_correcao'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_recusa'];
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
        fcEditar(data['t_pk']);

    });

    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcExcluir(data['t_pk'], data['ds_lead']);
    });


}

function fcCarregarSolicitante() {

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("usuario", "listarTodos", objParametros);

    carregarComboAjax($("#usuario_cadastro_lancamento_pk"), arrCarregar, " ", "pk", "ds_usuario");
}

function fcCarregarAnalista() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("usuario", "listarTodosAnalistas", objParametros);
    carregarComboAjax($("#usuario_cadastro_analista_pk"), arrCarregar, " ", "pk", "ds_usuario");
}

function fcCarregarGestor() {
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("usuario", "listarTodosGestores", objParametros);
    carregarComboAjax($("#usuario_cadastro_gestor_pk"), arrCarregar, " ", "pk", "ds_usuario");
}

$(document).ready(function () {

    fcCarregarGestor();
    fcCarregarAnalista();
    fcCarregarSolicitante();
    fcCarregarGrid();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdNovo', fcIncluir);
    $(document).on('click', '#cmdCancelar', fcCancelar);

    $('#dt_cadastro_ini').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_cadastro_ini").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_cadastro_fim').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_cadastro_fim").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_aprovacao_ini').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_aprovacao_ini").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_aprovacao_fim').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_aprovacao_fim").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_correcao_ini').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_correcao_ini").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_correcao_fim').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_correcao_fim").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_recusa_ini').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_recusa_ini").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_recusa_fim').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_recusa_fim").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_vencimento_ini').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_vencimento_ini").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_vencimento_fim').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_vencimento_fim").keypress(function () {
        mascara(this, mdata);
    });

});





