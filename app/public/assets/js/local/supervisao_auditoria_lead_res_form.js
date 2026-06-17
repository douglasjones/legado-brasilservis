var tblResultado;
function fcPesquisar(){
	
    tblResultado.clear().destroy();
    fcCarregarGrid();
    
}

function fcCarregarGrid() {
    var objParametros = {
        "leads_pk": $("#leads_pk").val(),
        "auditoria_categorias_pk": $("#auditoria_categorias_pk").val(),
        "auditoria_categoria_tipos_pk": $("#auditoria_categoria_tipos_pk").val()
    };

    var v_url = routes_api("supervisao_auditoria_lead", "listarGrid", objParametros);

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
            { data: 't_ds_tipos_categoria', orderable: false, searchable: false },
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
        fcExcluir(data['t_pk'], data['t_ds_tipos_categoria']);
    } );          
}

function fcExcluir(v_pk, v_ds_tipos_categoria){
    utilsJS.jqueryConfirm('Excluir ?', 'Deseja excluir o registro '+v_ds_tipos_categoria+'?',function(){
        if(v_pk != ""){
            var objParametros = {
                "pk": v_pk
            };              
            
            var arrExcluir = carregarController("supervisao_auditoria_lead", "excluir", objParametros);   
            if (arrExcluir.status == true){
                //Exibe a mensagem
                utilsJS.toastNotify(true, arrExcluir.message);
                // Reload datable
                tblResultado.clear().destroy();
                fcCarregarGrid();
            }
            else{
                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
            }
        }
        else{
            utilsJS.toastNotify(false, 'Código não encontrado');
        }
    });
}

function fcVoltar(){
    sendPost('menu','operacional',{});
}

function fcEditar(v_pk){
    var objParametros = {
        "pk":v_pk
    };
    sendPost('supervisao_auditoria_lead','cadForm' ,objParametros);
}

function fcIncluir(){
    sendPost('supervisao_auditoria_lead','cadForm',{});
}

$(document).ready(function(){    
    fcCarregarGrid();
    
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
        
});