var tblResultado;
function fcPesquisar(){
	
    tblResultado.clear().destroy();
    fcCarregarGrid();
    
}

function fcCarregarGrid() {
    var objParametros = {
        "ic_status": $("#ic_status").val(),
        "ds_categoria": $("#ds_categoria").val()
    };

    var v_url = routes_api("auditoria_categoria", "listarGrid", objParametros);

    var tblResultado = $("#tblResultado").DataTable({
        searching: false,
        paging: false,
        scrollX: true,
        pageLength: 30,
        aLengthMenu: [30, 50, 100],
        iDisplayLength: 30,
        processing: false,
        serverSide: false,
        ajax: v_url,
        responsive: true,
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },
        columns: [
            { data: 't_pk', orderable: false, searchable: false },
            { data: 't_ds_categoria', orderable: false, searchable: false },
            { data: 't_ic_status', orderable: false, searchable: false },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_edit"><i class="bi bi-pencil-square" style="font-size:18px; color:blue" title="Editar"></i></a> ';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';
                

                    return buttonPainel + buttonDelete;
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
        fcExcluir(data['t_pk'], data['t_ds_categoria']);
    } );          
}

function fcVoltar(){
    sendPost('menu','administracao',{});
}

function fcEditar(v_pk){
    var objParametros = {
        "pk":v_pk
    };
    sendPost('auditoria_categoria','cadForm' ,objParametros);
}

function fcIncluir(){
    sendPost('auditoria_categoria','cadForm',{});
}

$(document).ready(function(){    
    fcCarregarGrid();
    
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
        
});