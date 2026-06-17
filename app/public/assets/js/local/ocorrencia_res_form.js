function fcExcluirOcorrencia(v_pk){
    var arrCarregar = permissao("ocorrencia", "del");
    if (arrCarregar.status != true) {
        utilsJS.toastNotify(false, "Você não tem permissão");
        return false;
    }

    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){
            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("ocorrencia", "excluir", objParametros);

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true, arrExcluir.message);
                // Reload datable
                tblOcorrencia.ajax.reload();

            }else{

                utilsJS.toastNotify(false, "Falhou a requisição de exclusão.");
            }
        }
        else{
            utilsJS.toastNotify(false, "Código não encontrado.");
        }
    });
}

function fcCarregarGridOcorrencia(){
    var objParametros = {
        "leads_pk": $("#leads_pk").val()
    };
    var v_url = routes_api("ocorrencia", "listarOcorrenciasLeadPk", objParametros);

    //Trata a tabela
    tblOcorrencia = $('#tblOcorrencia').DataTable({
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
                    return full['t_dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_tipo_ocorrencia'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_tipos_ocorrencias_pk'];
                },
                'orderable': true,
                'searchable': false,
                'visible': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_ocorrencia'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_nome_usuario_cadastro'];
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
                    return full['t_agendado_para'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_retorno'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_retorno'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_termino_retorno'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonEdit = "<a class='function_edit'><span><i class='bi bi-pencil-square' style='font-size:18px; color:blue' title='EDITAR O OCORRÊNCIA'></i></span></a>&nbsp;&nbsp;";
                    var buttonDelete = "<a class='function_delete'><span><i class='fa fa-trash' style='font-size:18px; color:blue' title='EXCLUIR O LEAD'></i></span></a>";
                    return buttonEdit + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]

    });

    $('#tblOcorrencia tbody').on('click', '.function_delete', function () {
        var data;

        if(tblOcorrencia.row( $(this).parents('li') ).data()){
            data = tblOcorrencia.row( $(this).parents('li') ).data();
        }
        else if(tblOcorrencia.row( $(this).parents('tr') ).data()){
            data = tblOcorrencia.row( $(this).parents('tr') ).data();
        }

        if(data['t_pk'] != ""){
            fcExcluirOcorrencia(data['t_pk']);
        }
    } );

    $('#tblOcorrencia tbody').on('click', '.function_edit', function () {
        var data;

        rLinhaSelecionada = null;

        if(tblOcorrencia.row( $(this).parents('li')).data()){
            data = tblOcorrencia.row( $(this).parents('li')).data();
            rLinhaSelecionada = $(this).parents('li');
        }
        else if(tblOcorrencia.row( $(this).parents('tr')).data()){
            data = tblOcorrencia.row( $(this).parents('tr')).data();
            rLinhaSelecionada = $(this).parents('tr');
        }
        fcEditarOcorrencia(data);
    } );
}

function fcEditarOcorrencia(objRegistro){
    var arrCarregar = permissao("ocorrencia", "upd");
    if (arrCarregar.status != true) {
        utilsJS.toastNotify(false, "Você não tem permissão");
        return false;
    }
    fcAbrirFormNovaOcorrencia();
    $("#contatos_pk").val("");
    $("#acao").val("upd");

    $("#ocorrencias_pk").val(objRegistro['t_pk']);
    fcCarregarOcorrencia(objRegistro['t_pk'])
}


function fcAbrirFormNovoOcorrencia(){
    fcAbrirFormNovaOcorrencia();
    $("#acao").val("ins");
    $("#ocorrencias_pk").val("");
}

function fcCarregarInfoOcorrencias(){
    if($("#leads_pk").val() > 0){
        var objParametros = {
            "pk": $("#leads_pk").val()
        };
        var arrCarregar = carregarController("lead", "listarPk", objParametros);

        $("#ds_lead_titulo_ocorrencia").html("<b>"+arrCarregar.data[0]['ds_lead']+"</b>");
        $("#id_lead_ocorrencia").html("Cód Lead: "+arrCarregar.data[0]['pk']);
        $("#dt_cadastro_lead_ocorrencia").html("Dt de Cad: "+arrCarregar.data[0]['dt_cadastro']);
        $("#dt_ult_atualizacao_lead_ocorrencia").html("Dt Utl atualização: "+arrCarregar.data[0]['dt_ult_atualizacao']);
        $("#ds_usuario_cadastro_ocorrencia").html("Usuário de Cad: "+arrCarregar.data[0]['ds_usuario_cadastro']);
    }
}

$(document).ready(function(){
    var arrCarregar = permissao("ocorrencia", "cons");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false, "Você não tem permissão");
        return false;
    }

    //faz a carga inicial do grid.
    fcCarregarGridOcorrencia();
    fcCarregarInfoOcorrencias();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdIncluirOcorrencia', fcAbrirFormNovoOcorrencia);

});


