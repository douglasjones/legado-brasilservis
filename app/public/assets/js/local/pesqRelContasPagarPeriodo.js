var tblResultado;
var click_id = 0;


function fcCarregarGrid(){
    var tipo_lancamento = 2;
    if(dt_vencimento_ini==""){
        
        sweetMensagem('warning', "Preencha o período corretamente.");
        return true;
    }
    if(dt_vencimento_fim==""){
        sweetMensagem('warning', "Preencha o período corretamente.");
        return true;
    }
        
    var dt_vencimento_ini = $("#dt_vencimento_ini").val();
    var dt_vencimento_fim = $("#dt_vencimento_fim").val();
    var ds_empresa = $("#empresas_pk option:selected").text();
    var contas_bancarias_pk = $("#contas_bancarias_pk").val();
    var ds_tipo_grupo = $("#tipo_grupo_pk option:selected").text();
    var ds_grupo_leancamento = $("#grupo_leancamento_pk option:selected").text();
    var tipos_operacao_pk_receita = $("#tipos_operacao_pk_receita").val();
    
    objParametros = {
        dt_vencimento_ini:dt_vencimento_ini,
        dt_vencimento_fim:dt_vencimento_fim,
        tipo_lancamento_pk:tipo_lancamento,
        ds_empresa:ds_empresa,
        contas_bancarias_pk:contas_bancarias_pk,
        tipos_operacao_pk_receita:tipos_operacao_pk_receita,
        empresas_pk:$("#empresas_pk").val(),
        ds_tipo_grupo:ds_tipo_grupo,
        tipo_grupo_pk:$("#tipo_grupo_pk").val(),
        ds_grupo_leancamento:ds_grupo_leancamento,
        grupo_leancamento_pk:$("#grupo_leancamento_pk").val(),
    }
    sendPost('relatorio', 'receptivoPlanoContasPagarPeriodo', objParametros);
}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','financeiro' ,objParametros);
}

function carregarComboEmpresaPk(){
    var objParametros = {
        "pk": ""
    };      
    
    var arrCarregar = carregarController("conta", "listarPk", objParametros);   
   
    carregarComboAjax($("#empresas_pk"), arrCarregar, " ", "pk", "ds_razao_social");
}
function carregarComboUsuarioCadastro(){
    var objParametros = {
        "pk": ""
    };      
    
    var arrCarregar = carregarController("usuario", "listarTodos", objParametros);   
   
    carregarComboAjax($("#usuario_cadastro_pk"), arrCarregar, " ", "pk", "ds_usuario");
}


function fcListarItensGrupos(){

    var objParametros = {
        "tipo_grupo_pk": ""
    };          
    if($("#tipo_grupo_pk").val()==1){
        var arrCarregar = carregarController("lancamento", "listaItensGrupoLeads", objParametros); 
       
        carregarComboAjax($("#grupo_leancamento_pk"), arrCarregar, " ", "pk", "ds_lead");    
    }else if($("#tipo_grupo_pk").val()==2){
        var arrCarregar = carregarController("lancamento", "listaItensGrupoColaboradores", objParametros);    
        carregarComboAjax($("#grupo_leancamento_pk"), arrCarregar, " ", "pk", "ds_colaborador");   
    }else if($("#tipo_grupo_pk").val()==3){
        var arrCarregar = carregarController("lancamento", "listaItensGrupoFornecedores", objParametros);    
        carregarComboAjax($("#grupo_leancamento_pk"), arrCarregar, " ", "pk", "ds_fornecedor");   
    }
}

function fcListarContaBancariaReceita(){
    
    var objParametros = {
        "empresas_pk": $("#empresas_pk").val()
    };          
    var arrCarregar = carregarController("conta_bancaria", "listarContasLancamento", objParametros);  

    carregarComboAjax($("#contas_bancarias_pk"), arrCarregar, " ", "pk", "ds_dados_conta");
}
function fcListarTipoCategoriaReceita(){

    var objParametros = {
      "categorias_financeiras_pk":4
    };         
   
    var arrCarregar = carregarController("plano_contas", "listaPorCategoria", objParametros);   

    carregarComboAjax($("#tipos_operacao_pk_receita"), arrCarregar, " ", "pk", "ds_tipo_operacao");       
}


$(document).ready(function(){    
           
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);
    $(document).on('click', '#cmdCancelar', fcCancelar);
    fcListarTipoCategoriaReceita();
    carregarComboEmpresaPk();
    carregarComboUsuarioCadastro();
    $(".chzn-select").chosen({allow_single_deselect: true});
    
    
    $("#tipo_grupo_pk").change(function(){ 
        $(".chzn-select").chosen('destroy');
        fcListarItensGrupos();   
        $(".chzn-select").chosen({allow_single_deselect: true});
    });
    
     $("#empresas_pk").change(function(){ 
        $("#contas_bancarias_pk").text('') 
        $(".chzn-select").chosen('destroy');
        fcListarContaBancariaReceita();   
        $(".chzn-select").chosen({allow_single_deselect: true});
    });
  
        
    $('#dt_vencimento_ini').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_vencimento_ini").keypress(function(){
       mascara(this,mdata);
    });
    $('#dt_vencimento_fim').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_vencimento_fim").keypress(function(){
       mascara(this,mdata);
    });
    
   
    
    
});


