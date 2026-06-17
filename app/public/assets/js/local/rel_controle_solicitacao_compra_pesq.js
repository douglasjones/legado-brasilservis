var tblResultado;

function fcCarregarGrid(){
    
    var ds_empresa = $("#empresa_pk :selected").text();
    var ds_solicitante = $("#solicitante_pk :selected").text();
    var ds_usuario_aprovacao = $("#usuario_aprovacao_pk :selected").text();
    var ds_grupo = $("#tipo_grupo_centro_custo_pk :selected").text();
    var ds_centro_custo = $("#grupo_lancamento_centro_custo_pk :selected").text();
    var ds_status = $("#ic_status :selected").text();

    objParametros = {
        ds_empresa: ds_empresa,
        empresa_pk: $("#empresa_pk").val(),
        solicitante_pk: $("#solicitante_pk").val(),
        ds_solicitante: ds_solicitante,
        usuario_aprovacao_pk: $("#usuario_aprovacao_pk").val(),
        ds_usuario_aprovacao: ds_usuario_aprovacao,
        tipo_grupo_centro_custo_pk: $("#tipo_grupo_centro_custo_pk").val(),
        ds_grupo: ds_grupo,
        grupo_lancamento_centro_custo_pk: $("#grupo_lancamento_centro_custo_pk").val(),
        ds_centro_custo: ds_centro_custo,
        dt_ini_cad: $("#dt_ini_cad").val(),
        dt_fim_cad: $("#dt_fim_cad").val(),
        dt_ini_aprov: $("#dt_ini_aprov").val(),
        dt_fim_aprov: $("#dt_fim_aprov").val(),
        ic_status: $("#ic_status").val(),
        ds_status: ds_status
    }
    sendPost("relatorio","receptivoControleSolicitacaoCompra",objParametros);
}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','compra_estoque' ,objParametros);
}

function fcCarregarEmpresa(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);
    carregarComboAjax($("#empresa_pk"), arrCarregar, " ", "pk", "ds_conta");
}

function fcListarItensGruposCentroCustoReceita(){
    var objParametros = {
        "tipo_grupo_pk": ""
    };
    if($("#tipo_grupo_centro_custo_pk").val()==1){
        var arrCarregar = carregarController("lancamento", "listaItensGrupoLeads", objParametros);
        carregarComboAjax($("#grupo_lancamento_centro_custo_pk"), arrCarregar, " ", "pk", "ds_lead");

    }else if($("#tipo_grupo_centro_custo_pk").val()==2){

        var arrCarregar = carregarController("lancamento", "listaItensGrupoColaboradores", objParametros);
        carregarComboAjax($("#grupo_lancamento_centro_custo_pk"), arrCarregar, " ", "pk", "ds_colaborador");

    }else if($("#tipo_grupo_centro_custo_pk").val()==3){
        var arrCarregar = carregarController("lancamento", "listaItensGrupoFornecedores", objParametros);
        carregarComboAjax($("#grupo_lancamento_centro_custo_pk"), arrCarregar, " ", "pk", "ds_fornecedor");

    }
    else if($("#tipo_grupo_centro_custo_pk").val()==4){
        var arrCarregar = carregarController("equipe", "listarTodos", objParametros);
        carregarComboAjax($("#grupo_lancamento_centro_custo_pk"), arrCarregar, " ", "pk", "ds_equipe");
    }
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

 function fcComboSolicitante(){
    var objParametros = {
 
     };       
     var arrCarregar = carregarController("usuario", "listarTodosSemAdm", objParametros)    
 
     carregarComboAjax($("#solicitante_pk"), arrCarregar, " ", "pk", "ds_usuario");
 }

$(document).ready(function(){
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);

    fcComboSolicitante();
    fcComboAprovador();
    fcCarregarEmpresa();
    $("#tipo_grupo_centro_custo_pk").change(function(){
        $(".chzn-select").chosen('destroy');
        fcListarItensGruposCentroCustoReceita();
        $(".chzn-select").chosen({allow_single_deselect: true});
    });
    $(".chzn-select").chosen('destroy');

    //carrega cadastro ini
    $('#dt_ini_cad').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 
    $("#dt_ini_cad").keypress(function(){
       mascara(this,mdata);
    });
        
    //carrega cadastro fim
    $('#dt_fim_cad').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 
    $("#dt_fim_cad").keypress(function(){
       mascara(this,mdata);
    });
    //carrega cadastro ini
    $('#dt_ini_aprov').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 
    $("#dt_ini_aprov").keypress(function(){
       mascara(this,mdata);
    });
        
    //carrega cadastro fim
    $('#dt_fim_aprov').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 
    $("#dt_fim_aprov").keypress(function(){
       mascara(this,mdata);
    });

    
    

});
