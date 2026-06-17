var tblAgenda;

function fcExcluirAgenda(v_pk){
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("agenda", "excluir", objParametros);

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

function fcCarregarGridAgenda(){

    try {
        var objParametros = {
            "leads_pk": $("#leads_pk").val()
        };
        var v_url = routes_api("agenda", "listarDataTable", objParametros);
        //NewWindow(v_last_url)
        //Trata a tabela
        tblAgenda = $('#tblAgenda').DataTable({
            searching: false,
            paging: false,
            scrollX: true,
            pageLength: 10,
            aLengthMenu: [10, 25, 50, 100],
            iDisplayLength: 10,
            processing: false,
            serverSide: false,
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
                        return full['pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_tipo_agendas'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['dt_ini_agenda_ini'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_usuario'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['dt_cadastro'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_status'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_obs'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        var buttonDelete = "<a class='function_edit'><span><i class=bi bi-pencil-square style=font-size:18px; color:blue title=Editar></i></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a class='function_delete'><span><i class='fa fa-trash' style='font-size:18px; color:blue' title='EXCLUIR'></i></span></a>";
                        return  buttonDelete;
                    },
                    'orderable': false,
                    'searchable': false,
                    width: '80px'
                }
            ]

        });
        $('#tblAgenda tbody').on('click', '.function_delete', function () {
            var data;

            if(tblAgenda.row( $(this).parents('li') ).data()){
                data = tblAgenda.row( $(this).parents('li') ).data();
            }
            else if(tblAgenda.row( $(this).parents('tr') ).data()){
                data = tblAgenda.row( $(this).parents('tr') ).data();
            }

            if(data['pk'] != ""){
                fcExcluirAgenda(data['pk']);
            }
        } );

        $('#tblAgenda tbody').on('click', '.function_edit', function () {
            var data;

            rLinhaSelecionada = null;

            if(tblAgenda.row( $(this).parents('li')).data()){
                data = tblAgenda.row( $(this).parents('li')).data();
                rLinhaSelecionada = $(this).parents('li');
            }
            else if(tblAgenda.row( $(this).parents('tr')).data()){
                data = tblAgenda.row( $(this).parents('tr')).data();
                rLinhaSelecionada = $(this).parents('tr');
            }
            fcAbrirFormAgenda(data['pk'], "", "inserir", $("#leads_pk").val());
        } );
    } catch (error) {
        utilsJS.toastNotify(false, error);
    }

}



function fcCarregarInfoAgenda(){
    if($("#leads_pk").val() > 0){
        var objParametros = {
            "pk": $("#leads_pk").val()
        };
        var arrCarregar = carregarController("lead", "listarPk", objParametros);

        $("#ds_lead_titulo_agenda").html("<b>"+arrCarregar.data[0]['ds_lead']+"</b>");
        $("#id_lead_agenda").html("Cód Lead: "+arrCarregar.data[0]['pk']);
        $("#dt_cadastro_lead_agenda").html("Dt de Cad: "+arrCarregar.data[0]['dt_cadastro']);
        $("#dt_ult_atualizacao_lead_agenda").html("Dt Utl atualização: "+arrCarregar.data[0]['dt_ult_atualizacao']);
        $("#ds_usuario_cadastro_agenda").html("Usuário de Cad: "+arrCarregar.data[0]['ds_usuario_cadastro']);
    }
}

$(document).ready(function(){

    //faz a carga inicial do grid.
    fcCarregarGridAgenda();
    fcCarregarInfoAgenda();

    //Atribui os eventos dos demais controles
    $("#cmdIncluirAgenda").click(function(){
        fcAbrirFormAgenda("", "", "inserir", $("#leads_pk").val());
    });

});


