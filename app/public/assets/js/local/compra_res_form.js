var tblResultado;
var tblCompraProduto;
var rLinhaSelecionadaProd;

function fcVoltar(){
    var objParametros = {};
    sendPost('menu','compra_estoque' ,objParametros);
}

function fcCarregarGrid(){
    var objParametros = {
        "fornecedor_pk": $("#fornecedor_pk").val(),
        "categorias_pk": $("#categorias_pk").val(),
        "ds_numero_nota": $("#ds_numero_nota").val(),
        "contas_pk": $("#empresas_pk").val(),
        "dt_cadastro_ini": $("#dt_cadastro_ini").val(),
        "dt_cadastro_fim": $("#dt_cadastro_fim").val()
    };
    var v_url = routes_api("compra", "listarGrid", objParametros);

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
                    return full['pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_fornecedor'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_categoria'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },{
                mRender: function (data, type, full) {
                    return full['ds_numero_nota'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },{
                mRender: function (data, type, full) {
                    return full['ds_conta'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },{
                mRender: function (data, type, full) {
                    return full['dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },{
                mRender: function (data, type, full) {
                    return full['vl_pagamento'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="editar"></i></span></a> ';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';

                    return buttonPainel + buttonDelete;
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
        fcEditar(data['pk']);

    });

    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['pk']);
    });
}

function fcEditar(v_pk){
    sendPost('compra', 'cadForm',{pk: v_pk});
}

function fcIncluir(){
    sendPost('compra', 'cadForm',{});
}

function fcPesquisar(){
    tblResultado.clear().destroy();
    fcCarregarGrid();
}

function fcExcluir(v_pk){

    utilsJS.jqueryConfirm('Excluir ?', 'Deseja excluir o registro '+v_pk+'?',function(){
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("compra", "excluir", objParametros);

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

function fcCarregarCategorias(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros);
    carregarComboAjax($("#categorias_pk"), arrCarregar, " ", "pk", "ds_categoria");
}

function fcCarregarFornecedor(categorias_produto_pk){
    var objParametros = {
        "categorias_produto_pk": categorias_produto_pk
    };
    var arrCarregar = carregarController("fornecedor", "listarPorCategoria", objParametros);
    carregarComboAjax($("#fornecedor_pk"), arrCarregar, " ", "pk", "ds_fornecedor");
}


function fcCarregarEmpresa(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);

    carregarComboAjax($("#empresas_pk"), arrCarregar, " ", "pk", "ds_conta");
}

$(document).ready(function(){

    //faz a carga inicial do grid.
    fcCarregarGrid();

    //Carregar Combos
    fcCarregarCategorias();
    fcCarregarFornecedor("");
    fcCarregarEmpresa();

    //Formatar Campos
    /*$('#categorias_pk').select2();
    $('#fornecedor_pk').select2();
    $('#empresas_pk').select2();*/

    $(".chzn-select").chosen({allow_single_deselect: true});

    $('#dt_cadastro_ini').datepicker({defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_cadastro_ini").on('keyup', function () {
        mascara(this,mdata);
    });

    $('#dt_cadastro_fim').datepicker({defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_cadastro_fim").on('keyup', function () {
        mascara(this,mdata);
    });

    //Atribui os eventos
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);



});


