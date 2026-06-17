var tblResultado;
function fcPesquisar(){
    tblResultado.clear().destroy();
    fcCarregarGrid();
}

function fcExcluir(v_pk, v_ds_discriminacao_servico){
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_ds_discriminacao_servico+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };              
            
            var arrExcluir = carregarController("discriminacao_servicos", "excluir", objParametros);  

            if (arrExcluir.status == true){
                //Exibe a mensagem
                utilsJS.toastNotify(true, 'Registro excluído com sucesso');
                // Reload datable
                tblResultado.ajax.reload();
            }
            else{
                utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
            }
        }
        else{
            utilsJS.toastNotify(false ,"Código não encontrado");
        }
    });
}


function fcIncluir(){
    var objParametros = {
        "pk":""  
    };
    sendPost('iss_municipio','cadForm',objParametros)    
}


function fcVoltar(){
    var objParametros = {};
    sendPost('menu','financeiro' ,objParametros);
}

function fcEditar(v_pk){
    var objParametros = {
        "pk":v_pk
    }
    sendPost('iss_municipio', 'cadForm' ,objParametros);
}

function fcCarregarGrid(){
    var objParametros = {
        "ds_uf": $("#ds_uf").val(),
        "ds_cidade": $("#ds_cidade").val(),
        "ic_status": $("#ic_status").val(),
    };     
    
    var v_url = routes_api("iss_municipio", "listarGrid", objParametros);

    //Trata a tabela
    tblResultado = $('#tblResultado').DataTable({
        searching: true,
        paging: true,
        scrollX: true,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
        iDisplayLength: 10,
        processing: false,
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
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_uf'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_cidade'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['vl_aliquota_iss'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_status'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="editar"></i></span></a> ';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';
                

                    return buttonPainel ;//buttonDelete;
                },
                'orderable': false,
                'searchable': false,
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
        
    } );   
    
    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['t_pk'], data['t_ds_discriminacao_servico']);
    } );            
    
}

function listarCidade(){
    
    var objParametros = {
        'ds_uf': $('#ds_uf').val()
    };        
    var arrCarregar = carregarController("iss_municipio", "listarCidade", objParametros);
    carregarComboAjax($("#ds_cidade"), arrCarregar, " ", "cidade", "cidade");
}



$(document).ready(function(){
    //faz a carga inicial do grid.
    fcCarregarGrid();
    
    $(".chzn-select").chosen({ allow_single_deselect: true });
    $("#ds_uf").change(function () {
        utilsJS.loading('Buscando Cidades');
        $(".chzn-select").chosen('destroy');
        listarCidade();
        $(".chzn-select").chosen({ allow_single_deselect: true });
        utilsJS.loaded();
    });

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);

});


