var tblResultado;
function fcPesquisar(){
	
    tblResultado.clear().destroy();
    fcCarregarGrid();
    
}

function fcExcluir(v_pk){
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){
            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("compra_solicitacao", "excluir", objParametros);

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

function fcIncluir(){    
    var objParametros = {
        "compra_solicitacao_pk":"",
        "usuario_aprovacao_pk":""  
      };
      sendPost('compra_solicitacao','cadForm',objParametros)
}


function fcEditar(v_pk,v_usuario_aprovacao){
    var objParametros = {
        "compra_solicitacao_pk":v_pk,
        "usuario_aprovacao_pk":v_usuario_aprovacao
      };
      sendPost('compra_solicitacao','cadForm',objParametros)
}

function fcAprovarSolicitacao(objRegistro){
    if(objRegistro['t_dt_aprovacao']==null){
        var objParametros = {
            "compra_solicitacao_pk":objRegistro['t_pk'],
            "usuario_aprovacao_pk":objRegistro['t_usuario_aprovacao_pk']
        };

        sendPost('compra_solicitacao','cadForm',objParametros)
    }else{
        sweetMensagem('warning','Solicitação de Compra aprovado, não pode ser editada!');
    }
}

//combos
function fcComboEmpresas(){    
    var objParametros = {
        "pk": ""
    };          
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);    
    
    carregarComboAjax($("#empresa_pk"), arrCarregar, " ", "pk", "ds_conta");        
    //carregarComboAjax($("#empresa_pk), arrCarregar, " ", "pk", "ds_conta");        
}

function fcComboSolicitante(){
   var objParametros = {

    };       
    var arrCarregar = carregarController("usuario", "listarTodosSemAdm", objParametros)    

    carregarComboAjax($("#solicitante_pk"), arrCarregar, " ", "pk", "ds_usuario");
}

function fcComboAprovador(){
   var objParametros = {
       "solicitante_pk":$('#solicitante_pk').val()
    };       
 
    var arrCarregar = carregarController("equipe", "listarResponsavelEquipe", objParametros)    
  
    if(arrCarregar.data[0]['usuario_aprovacao_pk']==0){//Se o usuario não estiver em nenhuma equipe lista os ADM dos sistema para a aprovação
        var arrCarregarADM = carregarController("usuario", "listarAdmSistema", objParametros) 

        carregarComboAjax($("#usuario_aprovacao_pk"), arrCarregarADM, " ", "usuario_aprovacao_pk", "ds_usuaario_aprovacao");
        
    }else{    
        carregarComboAjax($("#usuario_aprovacao_pk"), arrCarregar, " ", "usuario_aprovacao_pk", "ds_usuaario_aprovacao");
    }
}

function fcComboGruposCentroCusto(){           
    var objParametros = {
        "tipo_grupo_pk": ""
    };          
    if($("#tipo_grupo_centro_custo_pk").val()==1){
        var arrCarregar = carregarController("lancamento", "listaItensGrupoLeads", objParametros);       
        carregarComboAjax($("#grupo_lancamento_centrocusto_pk"), arrCarregar, " ", "pk", "ds_lead");         
    }else if($("#tipo_grupo_centro_custo_pk").val()==2){        
        var arrCarregar = carregarController("lancamento", "listaItensGrupoColaboradores", objParametros);    
        carregarComboAjax($("#grupo_lancamento_centrocusto_pk"), arrCarregar, " ", "pk", "ds_colaborador");           
    }else if($("#tipo_grupo_centro_custo_pk").val()==4){

        var arrCarregar = carregarController("equipe", "listarTodos", objParametros);    
        carregarComboAjax($("#grupo_lancamento_centrocusto_pk"), arrCarregar, " ", "pk", "ds_equipe");   
    }
}  

//grids
function fcCarregarGrid(){
    
    var objParametros = {
        "empresa_pk": $("#empresa_pk").val(),
        "solicitante_pk": $("#solicitante_pk").val(),
        "usuario_aprovacao_pk": $("#usuario_aprovacao_pk").val(),
        "tipo_grupo_centro_custo_pk": $("#tipo_grupo_centro_custo_pk").val(),
        "grupo_lancamento_centrocusto_pk": $("#grupo_lancamento_centrocusto_pk").val(),        
        "ic_status": $("#ic_status").val(),
        "dt_solicitacao_ini": $("#dt_solicitacao_ini").val(),
        "dt_solicitacao_fim": $("#dt_solicitacao_fim").val(),
        "dt_aprovacao_ini": $("#dt_aprovacao_ini").val(),
        "dt_aprovacao_ini": $("#dt_aprovacao_fim").val()
    };         
    var v_url = routes_api("compra_solicitacao", "listarGrid", objParametros);

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
                        return full['t_ds_empresa'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_solicitante'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_solicitante_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_usuario_aprovacao'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_usuario_aprovacao_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_dt_solicitacao'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_status'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_compra_solicitacao'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_dt_aprovacao'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_tipo_grupo_centro_custo'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_grupo_lancamento_centrocusto'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                       var buttonAprovacao = '<a class="function_aprovar"><span><i class="fa fa-check-circle" style="font-size=18px;color:blue" title="Aprovar"></i></span></a> ';
                        var buttonPainel = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="Editar"></i></span></a> ';
                        var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';
                    
    
                        return buttonPainel +buttonAprovacao+ buttonDelete ;
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
        fcEditar(data['t_pk'],data['t_usuario_aprovacao_pk']);
        
    } );

    $('#tblResultado tbody').on('click', '.function_aprovar', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcAprovarSolicitacao(data);
    } );

    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['t_pk']);
    } ); 
}

function fcVoltar(){
    var objParametros = {};
    sendPost('menu','compra_estoque' ,objParametros);
}

$(document).ready(function(){
    //Grids
    fcComboEmpresas();
    fcComboSolicitante();
    $("#solicitante_pk").change(function(){ 
        $(".chzn-select").chosen('destroy');        
        fcComboAprovador();
        $(".chzn-select").chosen({allow_single_deselect: true});
    });
    
    $("#tipo_grupo_centro_custo_pk").change(function(){ 
        $(".chzn-select").chosen('destroy');        
        fcComboGruposCentroCusto()//combo de centros de custo  
        $(".chzn-select").chosen({allow_single_deselect: true});
    });    
    $(".chzn-select").chosen({allow_single_deselect: true});
    
    //mascaras de campos
    $('#dt_solicitacao_ini').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate",  ); 

    $("#dt_solicitacao_ini").keypress(function(){
       mascara(this,mdata);
    }); 
    
    $('#dt_solicitacao_fim').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate",  ); 

    $("#dt_solicitacao_fim").keypress(function(){
       mascara(this,mdata);
    });   
    
    $('#dt_aprovacao_ini').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate",  ); 

    $("#dt_aprovacao_ini").keypress(function(){
       mascara(this,mdata);
    });   
    
    $('#dt_aprovacao_fim').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate",  ); 

    $("#dt_aprovacao_fim").keypress(function(){
       mascara(this,mdata);
    }); 
 
    //faz a carga inicial do grid.
    fcCarregarGrid();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);

});


