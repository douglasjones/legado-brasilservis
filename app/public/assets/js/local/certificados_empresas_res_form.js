var tblResultado;
function fcPesquisar(){
	
    tblResultado.clear().destroy();
    fcCarregarGrid();
    
}

function fcIncluir(){
    var objParametros = {};
    sendPost('certificados_empresas','cadForm' ,objParametros);
}

function fcExcluir(v_pk, v_ds_categoria){

    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_ds_categoria+'?', function () {
        if(v_pk != ""){
            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("certificados_empresas", "excluir", objParametros);

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
            sweetMensagem('warning', "Código não encontrado.");
        }
    });
}

function fcVoltar(){
    var objParametros = {};
    sendPost('menu','financeiro' ,objParametros);
}

function fcEditar(v_pk){
    var objParametros = {
        "pk": v_pk
    };
    sendPost('certificados_empresas','cadForm' ,objParametros);
}

function fcCarregarGrid(){
   
    var objParametros = {
        "contas_pk": $("#contas_pk").val(),
        "ic_status": $("#ic_status").val()
    };     
    
    var v_url = routes_api("certificados_empresas", "contaConfigConsulta", objParametros);

    tblResultado = $('#tblResultado').DataTable({
        searching: true,
        paging: true,
        scrollX: true,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
        iDisplayLength: 10,
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
                    return full['nomeFantasia'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['razaoSocial'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['cpfCNPJ'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_criacao_certificado'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_status'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_edit"><i class="bi bi-pencil-square" style="font-size:18px; color:blue" title="Editar"></i></a> ';
                    //var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';


                    return buttonPainel;
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
        
    } );   
    
    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['pk']);
    } );            
    
}

function fcCarregarEmpresa() {
    var objParametros = {};
    var arrCarregar = carregarController("conta", "listarEmpresasCnpj", objParametros);
    carregarComboAjax($("#contas_pk"), arrCarregar, " ", "pk", "ds_conta");
}


$(document).ready(function(){
    //faz a carga inicial do grid.
    fcCarregarGrid();
    fcCarregarEmpresa();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);

});


