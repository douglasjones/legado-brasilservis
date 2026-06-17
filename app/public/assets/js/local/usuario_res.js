var tblResultado;
function fcPesquisar(){
    tblResultado.clear().destroy();
    fcCarregarGrid();
    
}

function fcIncluir(){
    var objParametros = {
      "pk":""  
    };
    sendPost('usuario','cadForm',objParametros)

}

function fcExcluir(v_pk, v_ds_usuario){
    utilsJS.jqueryConfirm('Excluir ?', 'Deseja excluir o registro '+v_ds_usuario+'?',function(){
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };              
            
            var arrExcluir = carregarController("usuario", "excluir", objParametros);   

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true, arrExcluir.message);

                // Reload datable
                tblResultado.ajax.reload();

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

function fcEditar(v_pk){
    var objParametros = {
        "pk": v_pk
    };
    sendPost('usuario', 'cadForm' ,objParametros);
}

function fcCarregarGrid(){

    var objParametros = {
        "ds_usuario": $("#ds_usuario").val(),
        "contas_pk": $("#contas_pk").val(),
        "grupos_pk": $("#grupos_pk").val(),
        "ic_status": $("#ic_status").val()
    };     
    
    var v_url = routes_api("usuario", "listarGrid", objParametros);

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
                    return full['t_ds_usuario'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_login'];
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
                    return full['t_ds_grupo'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_email'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_cel'];
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
                    var buttonPainel = '<a class="function_edit"><i class="bi bi-pencil-square" style="font-size:18px; color:blue" title="Editar"></i></a>';
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
        fcExcluir(data['t_pk'], data['t_ds_usuario']);
    } );            
    
}


function fcVoltar(){
    var objParametros = {};
    sendPost('menu','administracao' ,objParametros);
}

function fcCarregarContas(){
    var objParametros = {
        "pk": ""
    };         
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);    
    carregarComboAjax($("#contas_pk"), arrCarregar, " ", "pk", "ds_conta");        
}


function fcCarregarGrupos(){
    var objParametros = {
        "pk": ""
    };         
    var arrCarregar = carregarController("grupo", "listarTodos", objParametros);    
    carregarComboAjax($("#grupos_pk"), arrCarregar, " ", "pk", "ds_grupo");        
}

$(document).ready(function(){
    //faz a carga inicial do grid.
    fcCarregarGrid();
    fcCarregarContas();
    fcCarregarGrupos();
    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);

});


