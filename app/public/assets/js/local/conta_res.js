var tblResultado;
function fcPesquisar(){	
    tblResultado.clear().destroy();
    fcCarregarGridConta();    
}

function fcIncluir() {
    var objParametros = {
        "pk":''
    };
    sendPost('conta', 'editarConta' ,objParametros);
}

function fcExcluir(v_pk, v_ds_conta){

    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_ds_conta+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };              
            
            var arrExcluir = carregarController("conta", "excluir", objParametros);  

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
    sendPost('conta', 'editarConta' ,objParametros);
}

function fcCarregarGridConta(){
    try {
        var objParametros = {
            "ic_tipo_lead": $("#ic_tipo_lead").val(),
            "ds_conta": $("#ds_conta").val(),
            "ds_razao_social": $("#ds_razao_social").val(),
            "ds_cpf_cnpj": $("#ds_cpf_cnpj").val(),
            "ic_status": $("#ic_status").val()  
        };     
        
        var v_url = routes_api("conta", "listarDataTable", objParametros);
       
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
                        return full['ds_tipo_pessoa'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_conta'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_cpf_cnpj'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_tipo_conta'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['dt_ativacao'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['ic_status'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'
    
                },
                {
                    mRender: function (data, type, full) {
                        var buttonEdit = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="editar"></i></span></a> ';
                        var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';

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
            fcExcluir(data['pk'], data['ds_conta']);
        } );     
    } catch (error) {
        utilsJS.toastNotify(false,error);
    }
           
    
}

function fcVoltar(){
    sendPost('menu', 'cpainel' ,'');
}

function fcAtivar(){

    utilsJS.jqueryConfirm('Ativar?', 'Deseja ativar TODAS as contas ?', function () {

            var objParametros = {
                "pk": ""
            };              
            
            var arrExcluir = carregarController("conta", "ativar", objParametros);  

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true, 'Contas Ativadas');

                // Reload datable
                tblResultado.ajax.reload();

            }
            else{
                utilsJS.toastNotify(false,'Falhou a requisição.');
            }
    });
}

function fcDesativar(){

    utilsJS.jqueryConfirm('Desativar?', 'Deseja desativar TODAS as contas ?', function () {

            var objParametros = {
                "pk": ""
            };              
            
            var arrExcluir = carregarController("conta", "desativar", objParametros);  

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true, 'Contas Desativadas');

                // Reload datable
                tblResultado.ajax.reload();

            }
            else{
                utilsJS.toastNotify(false,'Falhou a requisição.');
            }
    });
}
$(document).ready(function(){

    //faz a carga inicial do grid.
    fcCarregarGridConta();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisarConta', fcPesquisar);
    $(document).on('click', '#cmdVoltarConta', fcVoltar);
    $(document).on('click', '#cmdIncluirConta', fcIncluir);
    $(document).on('click', '#cmdAtivar', fcAtivar);
    $(document).on('click', '#cmdDesativar', fcDesativar);

});



