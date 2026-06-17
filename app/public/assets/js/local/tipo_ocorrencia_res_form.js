var tblResultado;
function fcPesquisar(){
	
    tblResultado.clear().destroy();
    fcCarregarGrid();
    
}

function fcIncluir(){
    var objParametros = {

    };
    sendPost('tipo_ocorrencia','cadForm',objParametros);
}

function fcExcluir(v_pk, v_ds_tipo_ocorrencia){
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_ds_tipo_ocorrencia+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };              
            
            var arrExcluir = carregarController("tipo_ocorrencia", "excluir", objParametros);  

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

function fcEditar(v_pk){
    var objParametros = {
        "pk":v_pk
    };
    sendPost('tipo_ocorrencia','cadForm' ,objParametros);
}

function fcCarregarGrid(){

    
    var objParametros = {
        "ds_tipo_ocorrencia": $("#t_ds_tipo_ocorrencia").val(),
        "ic_fechar_ocorrencia_auto": $("#t_ic_fechar_ocorrencia_auto").val()
    };     
    
    var v_url = routes_api("tipo_ocorrencia", "listarGrid", objParametros);

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
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_tipo_ocorrencia'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ic_fechar_ocorrencia_auto'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="editar"></i></span></a> ';
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
        
        fcExcluir(data['t_pk'], data['t_ds_tipo_ocorrencia']);
    } );            
    
}

function fcVoltar(){
    var objParametros = {};
    sendPost('menu','administracao' ,objParametros);
}



$(document).ready(function(){
    var arrCarregar = permissao("tipo_ocorrencia", "cons");        

    if (arrCarregar.status != true){   
        utilsJS.toastNotify(false, 'Falhar ao carregar o registro');         
        return false;
    }
    //faz a carga inicial do grid.
    fcCarregarGrid();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);


});


