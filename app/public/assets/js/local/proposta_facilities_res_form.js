var tblResultado;
function fcPesquisar(){
    tblResultado.clear().destroy();
    fcCarregarGrid();
}

function fcIncluir(){
    var objParametros = {
        "pk": '',
        "ic_versao": '',
        "ic_abertura": 2,
        "leads_pk": "",
    };
    sendPost('propostas_facilities','abrirPropostaSelecao',objParametros);
}

function fcExcluir(v_pk){

    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("propostas_facilities", "excluir", objParametros);

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true, arrExcluir.message);

                // Reload datable
                tblResultado.ajax.reload();

            }
            else{;

                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
            }
        }
        else{

            utilsJS.toastNotify(false, 'Código não encontrado.');
        }
    });
}

function fcEditar(v_pk){
    var objParametros = {
        "pk": v_pk,
        "ic_versao": ''
    };
    sendPost('propostas_facilities','abrirPropostaDetalhada',objParametros);
}

function fcNovaVersao(v_pk){
    var objParametros = {
        "pk": v_pk,
        "ic_versao": 1
    };
    sendPost('propostas_facilities','abrirPropostaDetalhada',objParametros);
}

function fcCarregarGrid(){
    try {
        var objParametros = {
            "leads_pk": $("#leads_pk").val(),
            "ic_status": $("#ic_status").val(),
            "usuario_cadastro_pk": $("#usuario_cadastro_pk").val(),
            "usuario_responsavel_comercial_pk": $("#usuario_responsavel_comercial_pk").val(),
            "dt_cadastro": $("#dt_cadastro").val()
        };

        var v_url = routes_api("propostas_facilities", "listarDataTablePk", objParametros);
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
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_versao'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_proposta_facilities_pai_pk'];
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
                        return full['t_ds_status'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_usuario_cadastro'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_usuario_responsavel_comercial'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_dt_fechamento'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return float2moeda(full['t_vl_total_proposta']);
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {

                        var buttonVerao = "<a class='function_NovaVersao'><span><i class='fa fa-file-pdf' style='font-size:18px; color:blue' title='NOVA VERSÃO'></i></span></a>&nbsp;";
                        var buttonEdit = "<a class='function_edit'><span><i class='bi bi-pencil-square' style='font-size:18px; color:blue' title='EDITAR'></i></span></a>&nbsp;";
                        var buttonDelete = "<a class='function_delete'><span><i class='fa fa-trash' style='font-size:18px; color:blue' title='EXCLUIR'></i></span></a>&nbsp;";
                        var buttonImpr = "<a class='function_impressao'><span><i class='fa fa-print' style='font-size:18px; color:blue' title='IMPRIMIR PROPOSTA'></i></span></a>&nbsp;";
                        return buttonVerao +buttonEdit + buttonImpr + buttonDelete;
                    },
                    'orderable': false,
                    'searchable': false,
                    width: '80px'
                }
            ]
        });


        //Atribui os eventos na coluna ação.
        //Atribui os eventos na coluna ação.
        $('#tblResultado tbody').on('click', '.function_edit', function () {
            var data;
            if(tblResultado.row( $(this).parents('li')).data()){
                data = tblResultado.row( $(this).parents('li')).data();
            }
            else if(tblResultado.row( $(this).parents('tr')).data()){
                data = tblResultado.row( $(this).parents('tr')).data();
            }
            fcEditar(data['t_pk']);

        } );

        $('#tblResultado tbody').on('click', '.function_NovaVersao', function () {
            var data;
            if(tblResultado.row( $(this).parents('li')).data()){
                data = tblResultado.row( $(this).parents('li')).data();
            }
            else if(tblResultado.row( $(this).parents('tr')).data()){
                data = tblResultado.row( $(this).parents('tr')).data();
            }
            fcNovaVersao(data['t_pk']);

        } );

        $('#tblResultado tbody').on('click', '.function_delete', function () {
            var data;
            if(tblResultado.row( $(this).parents('li') ).data()){
                data = tblResultado.row( $(this).parents('li') ).data();
            }
            else if(tblResultado.row( $(this).parents('tr') ).data()){
                data = tblResultado.row( $(this).parents('tr') ).data();
            }
            fcExcluir(data['t_pk'], data['t_ds_usuario']);
        } );

        $('#tblResultado tbody').on('click', '.function_impressao', function () {
            var data;
            if(tblResultado.row( $(this).parents('li') ).data()){
                data = tblResultado.row( $(this).parents('li') ).data();
            }
            else if(tblResultado.row( $(this).parents('tr') ).data()){
                data = tblResultado.row( $(this).parents('tr') ).data();
            }
            fcImpressao(data['t_pk']);
        } );
    } catch (error) {
        utilsJS.toastNotify(false, error);
    }

}

function fcImpressao(pk){
    var objParametros = {
        "pk": pk
    };
    sendPost('propostas_facilities','abrirImpressao',objParametros);
}

function fcVoltar(){
    var objParametros = {
    };
    sendPost('menu','comercial',objParametros);
}

function fcCarregarLeads(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarUsuarios(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("usuario", "listarTodosSemAdm", objParametros);
    carregarComboAjax($("#usuario_cadastro_pk"), arrCarregar, " ", "pk", "ds_usuario");
}

function fcCarregarUsuariosResponsaveisComerciais(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("usuario", "listarTodosSemAdm", objParametros);
    carregarComboAjax($("#usuario_responsavel_comercial_pk"), arrCarregar, " ", "pk", "ds_usuario");
}

$(document).ready(function(){

    $('#dt_cadastro').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked"
    }).datepicker();
    $("#dt_cadastro").keypress(function(){
        mascara(this,mdata);
    });


    //faz a carga inicial do grid.
    fcCarregarGrid();
    fcCarregarLeads();
    fcCarregarUsuarios();
    fcCarregarUsuariosResponsaveisComerciais();
    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);
    $(".chzn-select").chosen({allow_single_deselect: true});
});


