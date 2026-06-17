var tblResultado;
function fcPesquisar(){
    tblResultado.clear().destroy();
    fcCarregarGrid();    
}

function fcExcluir(v_pk, v_fornecedor_pk){
    utilsJS.jqueryConfirm('Excluir ?', 'Deseja excluir o registro '+v_fornecedor_pk+'?',function(){
        if(v_pk != ""){
            var objParametros = {
                "pk": v_pk
            };   
            var arrExcluir = carregarController("compra_solicitacao_orcamento", "excluir", objParametros);  

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
function fcCarregarGrid(){  
    var v_compra_solicitacao_pk =""    
    if($("#compra_solicitacao_pk").val() > 0){
       v_compra_solicitacao_pk =  $("#compra_solicitacao_pk").val();
    }

    var objParametros = {
        "compra_solicitacao_pk": v_compra_solicitacao_pk
    };         
    var v_url = routes_api("compra_solicitacao_orcamento", "listarGrid", objParametros);

    //Trata a tabela
    tblResultado = $('#tblResultado').DataTable({
        searching: true,
            paging: true,
            scrollX: true,
            pageLength: 100,
            aLengthMenu: [100],
            iDisplayLength: 100,
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
                        return full['t_compras_solicitacao_orcamentos_pk'];
                    },
                    'orderable': true,
                    'searchable': false
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_fornecedor'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_dt_pevisao_entrega'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_vl_frete'];
                    },
                    'orderable': true,
                    'searchable': false    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_vl_total'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_status'];
                    },
                    'orderable': true,
                    'searchable': false    
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
        //fcEditarOrcamento(data['t_pk']); 
        fcEditarOrcamento(data['t_compras_solicitacao_orcamentos_pk']);
    } );   
    
    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['t_compras_solicitacao_orcamentos_pk'], data['t_ds_fornecedor']);
    } );            
    $('#tblResultado tbody').on('click', '.function_impressao', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcImprimir(data['t_compras_solicitacao_orcamentos_pk'], data['t_compra_solicitacao_pk']);
    } );            
    
}

function fcEditarOrcamento(compras_solicitacao_orcamentos_pk){
    sendPost('compra_solicitacao_orcamento','cadForm', { pk: compras_solicitacao_orcamentos_pk, compra_solicitacao_pk: $("#compra_solicitacao_pk").val(), usuario_aprovacao_pk: $("#usuario_aprovacao_pk").val()})
}

function fcImprimir(compras_solicitacao_orcamentos_pk, compra_solicitacao_pk){
    sendPost('impressao_compras_solicitacao_orcamentos_res_form.php', { token: token, compra_solicitacao_pk: compra_solicitacao_pk,  compras_solicitacao_orcamentos_pk: compras_solicitacao_orcamentos_pk});

}

$(document).ready(function(){
    //faz a carga inicial do grid.
    fcCarregarGrid();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#btnNewOrcamento', fcEnviarSolicitacaoCompras);
});


