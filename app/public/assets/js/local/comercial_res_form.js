var tblResultadoComercial;
function fcPesquisar(){
    tblResultadoComercial.clear().destroy();
    fcCarregarGridProposta();

}

function fcIncluir(){
    var objParametros = {
        "pk": '',
        "ic_versao": '',
        "ic_abertura": 1,
        "leads_pk": $("#leads_pk").val(),
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
                tblResultadoComercial.ajax.reload();

            }
            else{;

                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
            }
        }
        else{

            sweetMensagem('warning', 'Código não encontrado.');
        }
    });
}

function fcEditar(v_pk){
    var objParametros = {
        "pk": v_pk,
        "ic_versao": '',
        "ic_abertura": 1,
        "leads_pk":$("#leads_pk").val()
    };
    sendPost('propostas_facilities','abrirPropostaDetalhada',objParametros);
}

function fcNovaVersao(v_pk){
    var objParametros = {
        "pk": v_pk,
        "ic_versao": 1,
        "ic_abertura": 1,
        "leads_pk":$("#leads_pk").val()
    };
    sendPost('propostas_facilities','abrirPropostaDetalhada',objParametros);
}

function fcCarregarGridProposta(){
    try {
        var objParametros = {
            "leads_pk": $("#leads_pk").val()
        };

        var v_url = routes_api("propostas_facilities", "listarDataTablePk", objParametros);
        //NewWindow(v_last_url)
        //Trata a tabela
        tblResultadoComercial = $('#tblResultadoComercial').DataTable({
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
                            var buttonDelete = "<a class='function_delete'><span><i class='bi bi-x-circle' style='font-size:18px; color:blue' title='EXCLUIR'></i></span></a>&nbsp;";
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
        $('#tblResultadoComercial tbody').on('click', '.function_edit', function () {
            var data;
            if(tblResultadoComercial.row( $(this).parents('li')).data()){
                data = tblResultadoComercial.row( $(this).parents('li')).data();
            }
            else if(tblResultadoComercial.row( $(this).parents('tr')).data()){
                data = tblResultadoComercial.row( $(this).parents('tr')).data();
            }
            fcEditar(data['t_pk']);

        } );

        $('#tblResultadoComercial tbody').on('click', '.function_NovaVersao', function () {
            var data;
            if(tblResultadoComercial.row( $(this).parents('li')).data()){
                data = tblResultadoComercial.row( $(this).parents('li')).data();
            }
            else if(tblResultadoComercial.row( $(this).parents('tr')).data()){
                data = tblResultadoComercial.row( $(this).parents('tr')).data();
            }
            fcNovaVersao(data['t_pk']);

        } );

        $('#tblResultadoComercial tbody').on('click', '.function_delete', function () {
            var data;
            if(tblResultadoComercial.row( $(this).parents('li') ).data()){
                data = tblResultadoComercial.row( $(this).parents('li') ).data();
            }
            else if(tblResultadoComercial.row( $(this).parents('tr') ).data()){
                data = tblResultadoComercial.row( $(this).parents('tr') ).data();
            }
            fcExcluir(data['t_pk'], data['t_ds_usuario']);
        } );

        $('#tblResultadoComercial tbody').on('click', '.function_impressao', function () {
            var data;
            if(tblResultadoComercial.row( $(this).parents('li') ).data()){
                data = tblResultadoComercial.row( $(this).parents('li') ).data();
            }
            else if(tblResultadoComercial.row( $(this).parents('tr') ).data()){
                data = tblResultadoComercial.row( $(this).parents('tr') ).data();
            }
            fcImpressao(data['t_pk']);
        } );
    } catch (error) {
        utilsJS.toastNotify(false, error);
    }

}

function fcImpressao(pk){
    var objParametros = {
        "pk": pk,
        "leads_pk":$("#leads_pk").val(),
        "ic_abertura":1
    };
    sendPost('propostas_facilities','abrirImpressao',objParametros);
}

function fcVoltar(){
    if($("#ic_abertura").val() != 1){
        var objParametros = {

        };
        sendPost('propostas_facilities','receptivo',objParametros);
    }
    else{
        var objParametros = {
            "ic_abertura":1,
            "pk":$("#leads_pk").val()
        };
        sendPost('lead','leadMainPainel' ,objParametros);
    }
}



function fcCarregarInfoComercial(){
    if($("#leads_pk").val() > 0){
        var objParametros = {
            "pk": $("#leads_pk").val()
        };
        var arrCarregar = carregarController("lead", "listarPk", objParametros);

        $("#ds_lead_titulo_comercial").html("<b>"+arrCarregar.data[0]['ds_lead']+"</b>");
        $("#id_lead_comercial").html("Cód Lead: "+arrCarregar.data[0]['pk']);
        $("#dt_cadastro_lead_comercial").html("Dt de Cad: "+arrCarregar.data[0]['dt_cadastro']);
        $("#dt_ult_atualizacao_lead_comercial").html("Dt Utl atualização: "+arrCarregar.data[0]['dt_ult_atualizacao']);
        $("#ds_usuario_cadastro_comercial").html("Usuário de Cad: "+arrCarregar.data[0]['ds_usuario_cadastro']);
    }
}



$(document).ready(function(){
    //faz a carga inicial do grid.
    fcCarregarInfoComercial();
    fcCarregarGridProposta();
    //Atribui os eventos dos demais controles
    //$(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);

});


