var tblResultado;
function fcPesquisar(){
	
    tblResultado.clear().destroy();
    fcCarregarGrid();
    
}

function fcIncluir(){
    var objParametros = {};
    sendPost('plano_contas','cadForm' ,objParametros);
}

function fcVoltar(){
    var objParametros = {};
    sendPost('menu','financeiro' ,objParametros);
}

function fcExcluir(v_pk, v_ds_tipo_operacao){
    if(v_pk==1000){
        sweetMensagem('warning',v_ds_tipo_operacao+" Não pode ser excluido.");
        return false;
    }
    else if(v_pk==1001){
        sweetMensagem('warning',v_ds_tipo_operacao+" Não pode ser excluido.");
        return false;
    }
    else if(v_pk==1002){
        sweetMensagem('warning',v_ds_tipo_operacao+" Não pode ser excluido.");
        return false;
    }
    else{
        utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_ds_tipo_operacao+'?', function () {
            if(v_pk != ""){
                var objParametros = {
                    "pk": v_pk
                };
    
                var arrExcluir = carregarController("plano_contas", "excluir", objParametros);
    
                if (arrExcluir.status == true){
    
                    //Exibe a mensagem
                    utilsJS.toastNotify(true, arrExcluir.message);
                    // Reload datable
                    tblResultado.ajax.reload();
    
                }else{
    
                    utilsJS.toastNotify(false, "Falhou a requisição de exclusão.");
                }
            }
            else{
                utilsJS.toastNotify(false, "Código não encontrado.");
            }
        });
    }
    
}

function fcEditar(v_pk){
    var objParametros = {
        "pk": v_pk
    };
    sendPost('plano_contas','cadForm' ,objParametros);
}

function fcCarregarGrid(){
    var objParametros = {
        "ds_tipo_operacao": $("#ds_tipo_operacao").val(),
        "ic_status": $("#ic_status").val()
    };     
    
    var v_url = routes_api("plano_contas", "listarGrid", objParametros);
  
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
                    return full['t_ds_tipo_operacao'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_categoria'];
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
        fcExcluir(data['t_pk'], data['t_ds_tipo_operacao']);
    } );            
    
}



$(document).ready(function(){

    //faz a carga inicial do grid.
    fcCarregarGrid();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);

});


