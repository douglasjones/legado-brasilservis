var tblResultado;
function fcPesquisar(){

    tblResultado.clear().destroy();
    fcCarregarGrid();

}

function fcIncluir(){
    sendPost('conciliacao_bancaria','cadForm',{pk:""});
}

function fcExcluir(v_pk, v_ds_link_arquivo){

    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_ds_link_arquivo+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("conciliacao_bancaria", "excluir", objParametros);

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true,arrExcluir.message);

                // Reload datable
                tblResultado.ajax.reload();

            }
            else{
                utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
            }
        }
        else{
            utilsJS.toastNotify(false,"Código não encontrado");
        }
    });
}

function fcEditar(v_pk){

    sendPost('conciliacao_bancaria','cadForm',{pk:v_pk});
}
function fcCarregarGrid(){


    var objParametros = {
    };

    var v_url = routes_api("conciliacao_bancaria", "listarGrid", objParametros);
    //NewWindow(v_last_url)
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
                    return full['t_ds_conta'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_banco'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_agencia'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_conta_bancaria'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_ini_periodo_saldo']+" - "+full['t_dt_fim_periodo_saldo'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {

                    var buttonEdit = "<a class='function_edit'><span><i class='bi bi-pencil-square' style='font-size:18px; color:blue' title='EDITAR'></i></span></a>&nbsp;";
                    var buttonDelete = "<a class='function_delete'><span><i class='bi bi-x-circle' style='font-size:18px; color:blue' title='EXCLUIR'></i></span></a>&nbsp;";
                    return buttonEdit + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]
    });


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

    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['t_pk'], data['t_ds_banco']);
    } );

}

function fcCarregarBancos(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("banco", "listarTodos", objParametros);
    carregarComboAjax($("#bancos_pk"), arrCarregar, " ", "pk", "ds_banco");
}
function carregarComboEmpresa(){
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("conta", "listarPk", objParametros);

    carregarComboAjax($("#empresas_pk"), arrCarregar, " ", "pk", "ds_razao_social");
}

$(document).ready(function(){

    fcCarregarBancos();
    carregarComboEmpresa();

    $(".chzn-select").chosen({allow_single_deselect: true});

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);

    //faz a carga inicial do grid.
    fcCarregarGrid();


});


