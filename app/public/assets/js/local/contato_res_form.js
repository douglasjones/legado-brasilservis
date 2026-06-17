var tblContatos;

function fcExcluirContato(v_pk){
    var arrCarregar = permissao("contato", "del");
    if (arrCarregar.status != true){
        utilsJS.toastNotify(false, 'Você não tem permissão!');
        return false;
    }
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("contato", "excluir", objParametros);

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true, arrExcluir.message);
                // Reload datable
                tblContatos.ajax.reload();

            }
            else{
                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
            }
        }
        else{
            utilsJS.toastNotify(false, 'Código não encontrado.');
        }
    });
}

function fcCarregarGridContato(){
    var leads_pk = $("#leads_pk").val();

    var objParametros = {
        "leads_pk":leads_pk
    };
    var v_url = routes_api("lead", "listarContatoLead", objParametros);
    tblContatos = $("#tblContatos").DataTable({
        searching: true,
        scrollX: true,
        paging: true,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
        iDisplayLength: 10,
        processing: true,
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
                    return full['pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_contato'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_email'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_cel'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_whatsapp'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_tel'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_cargos_pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonEditar = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="Editar"></i></span></a> &nbsp;&nbsp;';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> &nbsp;&nbsp;';


                    return buttonEditar + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]

    });
    $('#tblContatos tbody').on('click', '.function_edit', function () {
        var data;

        rLinhaSelecionada = null;

        if(tblContatos.row( $(this).parents('li')).data()){
            data = tblContatos.row( $(this).parents('li')).data();
            rLinhaSelecionada = $(this).parents('li');
        }
        else if(tblContatos.row( $(this).parents('tr')).data()){
            data = tblContatos.row( $(this).parents('tr')).data();
            rLinhaSelecionada = $(this).parents('tr');
        }
        fcEditarContato(data);

    } );

    $('#tblContatos tbody').on('click', '.function_delete', function () {
        var data;

        if(tblContatos.row( $(this).parents('li') ).data()){
            data = tblContatos.row( $(this).parents('li') ).data();
        }
        else if(tblContatos.row( $(this).parents('tr') ).data()){
            data = tblContatos.row( $(this).parents('tr') ).data();
        }

        if(data['pk'] != ""){
            fcExcluirContato(data['pk']);
        }
    } );
    return false;

}

function fcAbrirFormNovoContato(){
    //limpa os dados de qualquer registro existe
    fcLimparFormContato();

    $("#janela_contatos").modal("show");
    $("#acao").val("ins");
    $("#contatos_pk").val("");
}

function fcEditarContato(objRegistro){
    var arrCarregar = permissao("contato", "upd");
    if (arrCarregar.status != true) {

        utilsJS.toastNotify(false, 'Você não tem permissão.');
        return false;
    }
    fcLimparFormContato();
    $("#janela_contatos").modal("show");
    $("#contatos_pk").val("");
    $("#acao").val("upd");

    //Carrega as informações da linha selecionada.
    $("#contatos_pk").val(objRegistro['pk']);
    $("#ds_contato").val(objRegistro['ds_contato']);
    $("#ds_email").val(objRegistro['ds_email']);
    $("#ds_cel").val(objRegistro['ds_cel']);
    $("#ic_whatsapp").val(objRegistro['ic_whatsapp']);
    $("#ds_tel_contato").val(objRegistro['ds_tel']);
    $("#cargos_pk").val(objRegistro['cargos_pk']);

}

function fcLimparFormContato(){
    $("#acao").val("");
    $("#contatos_pk").val("");
    $("#ds_contato").val("");
    $("#ds_email").val("");
    $("#ds_cel").val("");
    $("#ic_whatsapp").val("");
    $("#ds_tel_contato").val("");
    $("#cargos_pk").val("");
}

function fcCarregarInfoContatos(){
    if($("#leads_pk").val() > 0){
        var objParametros = {
            "pk": $("#leads_pk").val()
        };
        var arrCarregar = carregarController("lead", "listarPk", objParametros);

        $("#ds_lead_titulo_contatos").html("<b>"+arrCarregar.data[0]['ds_lead']+"</b>");
        $("#id_lead_contatos").html("Cód Lead: "+arrCarregar.data[0]['pk']);
        $("#dt_cadastro_lead_contatos").html("Dt de Cad: "+arrCarregar.data[0]['dt_cadastro']);
        $("#dt_ult_atualizacao_lead_contatos").html("Dt Utl atualização: "+arrCarregar.data[0]['dt_ult_atualizacao']);
        $("#ds_usuario_cadastro_contatos").html("Usuário de Cad: "+arrCarregar.data[0]['ds_usuario_cadastro']);
    }
}

$(document).ready(function(){

    $(document).on('click', '#cmdIncluirContato', fcAbrirFormNovoContato);

    //faz a carga inicial do grid.
    fcCarregarGridContato();
    fcCarregarInfoContatos();

    //Atribui os eventos dos demais controles

});


