var tblContratos;
function fcPesquisarContrato() {

    tblContratos.clear().destroy();
    fcCarregarGridContrato();
}


function fcCarregarLeadsContratoPesq() {
    //Carrega os grupos

    var objParametros = {
        "ic_tipo_lead": 2,
        "leads_pai_pk": $("#leads_clientes_pk").val(),
        "ic_cliente": 1
    };

    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#leads_postotrabalho_pk"), arrCarregar, " ", "pk", "ds_lead");


}
function fcCarregarClientes() {
    //Carrega os grupos

    var objParametros = {
        "ic_tipo_lead": 1,
        "ic_cliente": 1
    };

    var arrCarregar = carregarController("lead", "listarTodosClientes", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#leads_clientes_pk"), arrCarregar, " ", "pk", "ds_lead");

}

function fcCarregarGridContrato() {


    var objParametros = {
        "leads_postotrabalho_pk": $("#leads_postotrabalho_pk").val(),
        "ic_tipo_contrato": $("#ic_tipo_contrato").val(),
        "dt_inicio_contrato": $("#dt_inicio").val(),
        "dt_fim_contrato": $("#dt_fim").val(),
        "dt_recisao_contrato_ini": $("#dt_recisao_ini").val(),
        "dt_recisao_contrato_fim": $("#dt_recisao_fim").val(),
        "dt_cancelamento_ini": $("#dt_cancelamento_ini").val(),
        "dt_cancelamento_fim": $("#dt_cancelamento_fim").val(),
        "leads_clientes_pk": $("#leads_clientes_pk").val()
    };

    var v_url = routes_api("contrato", "listarContratoOperacional", objParametros);
    //NewWindow(v_last_url)
    //Trata a tabela
    tblContratos = $('#tblContratos').DataTable({
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
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_tipo_lead'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_lead'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_identificacao_area'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_tipo_contrato'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_inicio_contrato'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_fim_contrato'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_vl_total'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_vl_total_mao_obra'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_vl_contrato'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonEditar = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="Painel"></i></span></a> &nbsp;&nbsp;';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> &nbsp;&nbsp;';


                    return buttonEditar + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '85px'
            }
        ]
    });


    //Atribui os eventos na coluna ação.
    $('#tblContratos tbody').on('click', '.function_edit', function () {
        var data;

        rLinhaSelecionada = null;

        if (tblContratos.row($(this).parents('li')).data()) {
            data = tblContratos.row($(this).parents('li')).data();
            rLinhaSelecionada = $(this).parents('li');
        }
        else if (tblContratos.row($(this).parents('tr')).data()) {
            data = tblContratos.row($(this).parents('tr')).data();
            rLinhaSelecionada = $(this).parents('tr');
        }

        fcEditarContrato(data['t_pk']);

    });

    $('#tblContratos tbody').on('click', '.function_delete', function () {
        var data;

        if (tblContratos.row($(this).parents('li')).data()) {
            data = tblContratos.row($(this).parents('li')).data();
        }
        else if (tblContratos.row($(this).parents('tr')).data()) {
            data = tblContratos.row($(this).parents('tr')).data();
        }

        if (data['t_pk'] != "") {
            fcExcluirContrato(data['t_pk']);
        }
    });

    return false;
}
function fcEditarContrato(pk){
    sendPost('contrato','cadForm',{"pk":pk});
}
function fcExcluirContrato(v_pk) {
    var arrCarregar = permissao("contrato", "del");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false,'Você não tem permissão');
        return false;
    }
   
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if (v_pk != "") {

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("contrato", "excluir", objParametros);

            if (arrExcluir.status == true) {
                utilsJS.toastNotify(true, arrExcluir.message);

                // Reload datable
                tblContratos.ajax.reload();

            }
            else {
                utilsJS.toastNotify(false, "Falhou a requisição");
            }
        }
        else {
            utilsJS.toastNotify(false, "Código não encontrado");
        }
    });
    return false;

}

function fcIncluirContrato(){
    sendPost('contrato','cadForm',{"pk":""});
}

function fcRecarregarGridContratosProcessos() {
    tblContratos.ajax.reload();
    //fcCarregarGridContrato();
}

//abre o formulario para a inclusao de um novo contrato.



function fcCancelar() {
    sendPost('menu','comercial',{});
}


$(document).ready(function () {
    //carregar combo de elads
    fcCarregarLeadsContratoPesq();
    fcCarregarClientes();
    $(".chzn-select").chosen({ allow_single_deselect: true });



    $("#leads_clientes_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarLeadsContratoPesq();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisarContratos', fcPesquisarContrato);

    $(document).on('click', '#cmdIncluir', fcIncluirContrato);

    $('#dt_prazo_execucao').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", "");
    $("#dt_prazo_execucao").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_inicio').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", "");
    $("#dt_inicio").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_fim').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", "");
    $("#dt_fim").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_recisao_ini').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", "");
    $("#dt_recisao_ini").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_recisao_fim').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", "");
    $("#dt_recisao_fim").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_cancelamento_ini').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", "");
    $("#dt_cancelamento_ini").keypress(function () {
        mascara(this, mdata);
    });
    $('#dt_cancelamento_fim').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", "");
    $("#dt_cancelamento_fim").keypress(function () {
        mascara(this, mdata);
    });

    fcCarregarGridContrato();
    $(document).on('click', '#cmdCancelar', fcCancelar);
});





