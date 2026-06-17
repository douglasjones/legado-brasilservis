var tblAgenda;
function fcPesquisarGridAgenda() {
    tblAgenda.clear().destroy();
    fcCarregarGridEscala();
}

function fcCarregarGridEscala() {

    var v_leads_pk = "";
    var processo_pk = "";
    if ($("#leads_pk").val() != '') {
        v_leads_pk = $("#leads_pk").val();
    }
    if ($("#pk").val() != '') {
        processo_pk = $("#pk").val();
    }

    var objParametros = {
        "leads_pk_pesq": $("#leads_pk_pesq_agenda").val(),
        "colaborador_pk_pesq_agenda": $("#colaborador_pk_pesq_agenda").val(),
        "tipo_escala_pesq_agenda": $("#tipo_escala_pesq_agenda").val(),
        "escala_pesq_agenda": $("#escala_pesq_agenda").val(),
        "produtos_pesq_agenda": $("#produtos_pesq_agenda").val(),
        "ic_status_pesq_agenda": $("#ic_status_pesq_agenda").val(),
        "leads_pk": v_leads_pk,
        "processos_pk": processo_pk,
        "turno_base_pk_pesq": $("#turno_base_pk_pesq").val()
    };

    var v_url = routes_api("agenda_colaborador_padrao", "listarEscalasResPadrao", objParametros);

    //Trata a tabela
    tblAgenda = $('#tblAgenda').DataTable({
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
                    return full['t_ds_lead'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_turno'];
                },
                'orderable': true,
                'searchable': false,

            },{
                mRender: function (data, type, full) {
                    return full['t_ds_colaborador'];
                },
                'orderable': true,
                'searchable': false,

            },{
                mRender: function (data, type, full) {
                    return full['t_ds_pin'];
                },
                'orderable': true,
                'searchable': false,

            },{
                mRender: function (data, type, full) {
                    return full['t_ds_produto_servico'];
                },
                'orderable': true,
                'searchable': false,

            },{
                mRender: function (data, type, full) {
                    return full['t_n_qtde_dias_semana'];
                },
                'orderable': true,
                'searchable': false,

            },{
                mRender: function (data, type, full) {
                    return full['t_dt_periodo_escala'];
                },
                'orderable': true,
                'searchable': false,

            },{
                mRender: function (data, type, full) {
                    return full['t_dt_cancelamento'];
                },
                'orderable': true,
                'searchable': false,

            },{
                mRender: function (data, type, full) {
                    return full['t_ds_motivo_cancelamento'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_edit"><i class="bi bi-pencil-square" style="font-size:18px; color:blue" title="Editar"></i></a> ';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size:18px; color:blue" title="Excluir"></i></i></span></a> ';


                    return buttonPainel + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
            }
        ]
    });
    //Atribui os eventos na coluna ação.
    $('#tblAgenda tbody').on('click', '.function_edit', function () {
        var data;
        rLinhaSelecionada = null;
        if (tblAgenda.row($(this).parents('li')).data()) {
            data = tblAgenda.row($(this).parents('li')).data();
            rLinhaSelecionada = $(this).parents('li');
        }
        else if (tblAgenda.row($(this).parents('tr')).data()) {
            data = tblAgenda.row($(this).parents('tr')).data();
            rLinhaSelecionada = $(this).parents('tr');
        }
        fcEditarAgenda(data['t_pk']);
    });

    $('#tblAgenda tbody').on('click', '.function_delete', function () {
        var data;
        if (tblAgenda.row($(this).parents('li')).data()) {
            data = tblAgenda.row($(this).parents('li')).data();
        }
        else if (tblAgenda.row($(this).parents('tr')).data()) {
            data = tblAgenda.row($(this).parents('tr')).data();
        }

        if (data['t_pk'] != "") {
            fcExcluirAgenda(data['t_pk']);
        }
        tblAgenda.row($(this).parents('tr')).remove().draw();
    });
    return false;
}

function fcExcluirAgenda(v_pk){
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("agenda_colaborador_padrao", "excluir", objParametros);

            if (arrExcluir.status == true){
                utilsJS.toastNotify(true,arrExcluir.message)

                // Reload datable
                tblAgenda.ajax.reload();

            }else{

                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão ');
            }
        }
        else{
            sweetMensagem('warning', 'Código não encontrado');
        }
    });
}

function fcComboPesqLead() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listarTodos", objParametros);

    carregarComboAjax($("#leads_pk_pesq_agenda"), arrCarregar, " ", "pk", "ds_lead");

}

function fcCarregarTurnoPesq() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("agenda_colaborador_padrao", "listarTurno", objParametros);
    carregarComboAjax($("#turno_base_pk_pesq"), arrCarregar, " ", "pk", "ds_turno");

}


function fcComboPesqColaboradores() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("colaborador", "listarColaboradorLead", objParametros);

    carregarComboAjax($("#colaborador_pk_pesq_agenda"), arrCarregar, " ", "pk", "ds_colaborador");

}

function fcComboPesqProdutosServicos() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("produto_servico", "listarTodos", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#produtos_pesq_agenda"), arrCarregar, " ", "pk", "ds_produto_servico");

}

function fcIncluir(){
    var objParametros = {
        "pk":"",
        "local":$("#local").val()
    };
    sendPost('agenda_colaborador_padrao','cadFormEscala',objParametros)

}
function fcEditarAgenda(pk){
    var objParametros = {
        "pk":pk
    };
    sendPost('agenda_colaborador_padrao','cadFormEscala',objParametros)

}

function cmdVoltar(){
    sendPost('menu','operacional',{});
}


$(document).ready(function () {

    //Libera pesquisa
    if ($("#leads_pk").val() == '' || $("#pk").val() == '') {
        $("#exibir_pesquisa_agenda").show();
        $("#exibir_campos_pesq_hidden").hide();
        fcComboPesqLead();

        fcCarregarTurnoPesq();

        fcComboPesqColaboradores();

        $('#dt_periodo_ini_agenda_pesq').datepicker({
            defaultDate: "",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked",
            minDate: 0
        }).datepicker();
        $("#dt_periodo_ini_agenda_pesq").keypress(function () {
            mascara(this, mdata);
        });

        $('#dt_periodo_fim_agenda_pesq').datepicker({
            defaultDate: "",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked",
            minDate: 0
        }).datepicker();
        $("#dt_periodo_fim_agenda_pesq").keypress(function () {
            mascara(this, mdata);
        });

        fcComboPesqProdutosServicos();

        $(document).on('click', '#cmdPesquisar', fcPesquisarGridAgenda);
        $(document).on('click', '#cmdNovo', fcIncluir);
        $(document).on('click', '#cmdCancelar', cmdVoltar);

    } else {

        $("#exibir_pesquisa_agenda").hide();
        $("#exibir_campos_pesq_hidden").show();
    }

    //carregar table escala
    fcCarregarGridEscala();
    $(".chzn-select").chosen({allow_single_deselect: true});

});