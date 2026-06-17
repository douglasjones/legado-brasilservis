var tblResultadoColaborador;
function fcPesquisarColaborador() {

    tblResultadoColaborador.clear().destroy();
    fcCarregarGridColaborador();

}

function fcEditar(v_pk) {
    var objParametros = {
        "colaborador_pk":v_pk
    };
    sendPost('colaborador','cadFormCliente',objParametros)
}

function fcImpressao(v_pk) {
    var objParametros = {
        "pk":v_pk
    };
    sendPost('colaborador','print',objParametros)
}

function fcCarregarGridColaborador() {
    var ic_reserva = "";
    if ($('#ic_reserva_colaborador').is(":checked")) {
        ic_reserva = 1;
    }
    var objParametros = {
        "pk": $("#colaborador_pk_colaborador").val(),
        "ic_status": $("#ic_status_colaborador").val(),
        "leads_pk": $("#leads_pk_colaborador").val(),
        "ic_origem": $("#ic_origem_colaborador").val(),
        "ds_pin": $("#ds_pin_colaborador").val(),
        "ds_cpf": $("#ds_cpf_colaborador").val(),
        "generos_pk": $("#generos_pk_colaborador").val(),
        "ds_re": $("#ds_re_colaborador").val(),
        "ic_status_app": $("#ic_status_app_colaborador").val(),
        "ic_reserva": ic_reserva,
        "ds_produto_servico": $("#ds_produto_servico_colaborador").val()
    };

    var v_url = routes_api("colaborador", "listarGridCliente", objParametros);

    //Trata a tabela
    tblResultadoColaborador = $('#tblResultadoColaborador').DataTable({
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
                    return full['ds_lead'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_colaborador'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_pin'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_re'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_cel'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_status_app'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ic_origem'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ic_status'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_cel2'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_funcao'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    var buttonEdit = '<a class="function_edit"><i class="bi bi-pencil-square" style="font-size:18px; color:blue" title="Editar"></i></a> ';
                    var buttonPainel = '<a class="function_painel"><i class="bi bi-whatsapp" style="font-size:18px; color:blue"></i></a> ';
                    return buttonEdit + buttonPainel ;
                },
                'orderable': false,
                'searchable': false,
            }
        ]
    });


    //Atribui os eventos na coluna ação.
    $('#tblResultadoColaborador tbody').on('click', '.function_edit', function () {
        var data;
        if (tblResultadoColaborador.row($(this).parents('li')).data()) {
            data = tblResultadoColaborador.row($(this).parents('li')).data();
        }
        else if (tblResultadoColaborador.row($(this).parents('tr')).data()) {
            data = tblResultadoColaborador.row($(this).parents('tr')).data();
        }
        fcEditar(data['t_pk']);

    });

    $('#tblResultadoColaborador tbody').on('click', '.function_painel', function () {
        var data;

        if (tblResultadoColaborador.row($(this).parents('li')).data()) {
            data = tblResultadoColaborador.row($(this).parents('li')).data();
        }
        else if (tblResultadoColaborador.row($(this).parents('tr')).data()) {
            data = tblResultadoColaborador.row($(this).parents('tr')).data();
        }
        if (data['ic_whatsapp'] == "Sim") {
            fcAbrirMensagemWhatsAppTel(data);
        }


    });

}

function fcAbrirGridForulario(pk) {
    sendPost('colaborador','fcAbrirGridForulario', { "colaborador_pk": pk,"local":1 });
}
function fcAbrirMensagemWhatsAppTel(objRegistro) {
    var str = objRegistro['t_ds_cel'];
    var telefone = str.replace(/[^\d]+/g, '');
    var url = "https://api.whatsapp.com/send?phone=55" + telefone + "&text=Olá"
    window.open(url, '_blank');
}

function fcCarregarGenero() {
    //Carrega os grupos

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("genero", "listarTodos", objParametros);
    carregarComboAjax($("#generos_pk_colaborador"), arrCarregar, " ", "pk", "ds_genero");

}

function fcCarregarQualificacao() {
    //Carrega os grupos

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("produto_servico", "listarTodos", objParametros);
    // NewWindow(v_last_url);
    carregarComboAjax($("#ds_produto_servico_colaborador"), arrCarregar, " ", "pk", "ds_produto_servico");
    //alert(1);
}

function fcCarregarLeadsColaborador() {
    //Carrega os grupos

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("lead", "listarTodosClientes", objParametros);

    carregarComboAjax($("#leads_pk_colaborador"), arrCarregar, " ", "pk", "ds_lead");

}

function fcCarregarColaborador() {
    //Carrega os grupos

    var objParametros = {
        "leads_pk": $("#leads_pk_colaborador").val()
    };

    var arrCarregar = carregarController("colaborador", "listarColaboradorLead", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#colaborador_pk_colaborador"), arrCarregar, " ", "pk", "ds_colaborador");

}

$(document).ready(function () {

    $("#leads_pk_colaborador").change(function () {

        $(".chzn-select").chosen('destroy');
        fcCarregarColaborador();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });
    //faz a carga inicial do grid.
    fcCarregarGenero();

    fcCarregarLeadsColaborador();
    fcCarregarColaborador();
    fcCarregarQualificacao();


    $("#ds_cpf_colaborador").keypress(function(){
        chama_mascara(this);
    });
    fcCarregarGridColaborador();
    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisarColaborador', fcPesquisarColaborador);


});
