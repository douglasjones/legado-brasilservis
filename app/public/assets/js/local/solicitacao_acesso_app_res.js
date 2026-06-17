var tblResultado;
function fcPesquisar(){
	
    tblResultado.clear().destroy();
    fcCarregarGrid();
    
}

function fcExcluir(v_pk, v_ds_pin){

    utilsJS.jqueryConfirm('Excluir ?', 'Deseja excluir o registro '+v_ds_pin+'?',function(){
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };              
            
            var arrExcluir = carregarController("solicitacao_acesso_app", "excluir", objParametros);   

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

function fcVoltar(){
    var objParametros = {};
    sendPost('menu','rh' ,objParametros);
}

function fcEditar(){
    if($('#ic_status_modal').is(":checked")){  
        var ic_status = 1;
    }else{
        $("#alert_liberacao").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_liberacao").slideUp(500);
        });
        return false;
    }
    
    var objParametros = {
        "pk": $("#ponto_solicitacao_liberacao_app_pk").val(),
        "ic_status": (ic_status) 
    }; 

    var arrEnviar = carregarController("solicitacao_acesso_app", "liberarAcesso", objParametros);

    if (arrEnviar.status == true){
        utilsJS.toastNotify(true, arrEnviar.message);
       var objParametros = {};
       tblResultado.ajax.reload();
    }else{
        utilsJS.toastNotify(false, arrEnviar.result);
    }
    
    $("#janela_liberacao").modal("hide");    
    return false;
    
}

function fcFecharModal(){
    $("#janela_liberacao").modal("hide");    
}

function fcAbrirFormLiberacao(objRegistro){
    if(objRegistro['t_status']=='Liberado'){
        utilsJS.sweetMensagem(false,'Colaborador já liberado!');
        return false;
    }

    //limpa os dados de qualquer registro existe
    fcLimparFormLiberacao();
    
    $("#janela_liberacao").modal("show");
    $("#acao").val("upd");
    
   //Carrega as informações da linha selecionada.
    $("#ponto_solicitacao_liberacao_app_pk").val(objRegistro['t_pk']);
    $("#ds_colaborador_modal").html(objRegistro['t_ds_colaborador']);
    $("#ds_imagem_modal").html(objRegistro['t_ds_imagem']);
    $("#dt_solit_liberacao_modal").html(objRegistro['t_dt_solit_liberacao']);
    
}

function fcLimparFormLiberacao(){
    $("#acao").val("");
    $("#ponto_solicitacao_liberacao_app_pk").val("");
    $("#ds_colaborador").val("");
    $("#ds_imagem").val("");
    $("#dt_solit_liberacao").val("");
    $("#ic_status").prop('checked', false);
}

function fcIncluir(){
    var objParametros = {
        
    };      
    sendPost('solicitacao_acesso_app', 'cadForm', objParametros);
}

function fcCarregarGrid(){

    var objParametros = {
        "colaborador_pk": $("#colaboradores_pk").val(),
        "ds_pin": $("#ds_pin").val(),
        "ds_re": $("#ds_re").val(),
        "ic_status": $("#ic_status").val()
    };     
    
    var v_url = routes_api("solicitacao_acesso_app", "listar_solicitacoes", objParametros);
    //NewWindow(v_last_url)
    //Trata a tabela
        tblResultado = $('#tblResultado').DataTable({
            searching: true,
            paging: true,
            scrollX: false,
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
                        return full['t_ds_colaborador'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_imagem'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_dt_solit_liberacao'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_status'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_dt_liberacao'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_usuario'];
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
        fcAbrirFormLiberacao(data);        
    } );   
    
    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['t_pk'], data['t_ds_pin']);
    } );            
    
}

function fcCarregarColaborador(){
    
    var objParametros = {
        "pk": ""
    };      
    
    var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);    
    carregarComboAjax($("#colaboradores_pk"), arrCarregar, " ", "pk", "ds_colaborador");
        
}

$(document).ready(function(){
    
    //faz a carga inicial do grid.
    fcCarregarGrid();
    fcCarregarColaborador();
    $(document).on('mouseover', '#tblResultado td:nth-child(3) img', function() {
        $(this).addClass('expanded-image'); // Adiciona a classe ao passar o mouse sobre a imagem
      });
      
      $(document).on('mouseout', '#tblResultado td:nth-child(3) img', function() {
        $(this).removeClass('expanded-image'); // Remove a classe ao remover o mouse da imagem
      });
    $(".chzn-select").chosen({allow_single_deselect: true}); 

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdCancelar', fcVoltar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdEnviarAprovacaoSolicitacao', fcEditar);

});


