var tblResultado;
var click_id = 0;


function fcCarregarGrid(){
    var tipo_lancamento = 0;
    if($("#tipo_lancamento_receita").is(":checked") == true && $("#tipo_lancamento_despesa").is(":checked") == true){
        tipo_lancamento = 0;
    }
    else if($("#tipo_lancamento_receita").is(":checked") == true){
        tipo_lancamento = 1;
    }
    else if($("#tipo_lancamento_despesa").is(":checked") == true){
        tipo_lancamento = 2;
    }
    else{
        tipo_lancamento = 0;
    }
        
    var dt_vencimento_ini = $("#dt_vencimento_ini").val();
    var dt_vencimento_fim = $("#dt_vencimento_fim").val();
    var dt_pagamento_ini = $("#dt_pagamento_ini").val();
    var dt_pagamento_fim = $("#dt_pagamento_fim").val();
    var ds_empresa = $("#empresas_pk option:selected").text();
    var contas_bancarias_pk = $("#contas_bancarias_pk").val();
    var ds_tipo_grupo = $("#tipo_grupo_pk option:selected").text();
    var ds_grupo_leancamento = $("#grupo_leancamento_pk option:selected").text();
    var ds_ic_status = $("#ic_status option:selected").text();
    var ds_usuario_cadastro = $("#usuario_cadastro_pk option:selected").text();
    var ic_status = $("#ic_status").val();
    var tipos_operacao_pk_receita = $("#tipos_operacao_pk_receita").val();
    var dt_faturamento_ini = $("#dt_faturamento_ini").val();
    var dt_faturamento_fim = $("#dt_faturamento_fim").val();
    
    objParametros = {
        dt_vencimento_ini:dt_vencimento_ini,
        dt_vencimento_fim:dt_vencimento_fim,
        dt_pagamento_ini:dt_pagamento_ini,
        dt_pagamento_fim:dt_pagamento_fim,
        tipo_lancamento_pk:tipo_lancamento,
        ds_empresa:ds_empresa,
        contas_bancarias_pk:contas_bancarias_pk,
        ic_statusk:ic_status,
        tipos_operacao_pk_receita:tipos_operacao_pk_receita,
        empresas_pk:$("#empresas_pk").val(),
        ds_tipo_grupo:ds_tipo_grupo,
        tipo_grupo_pk:$("#tipo_grupo_pk").val(),
        ds_grupo_leancamento:ds_grupo_leancamento,
        grupo_leancamento_pk:$("#grupo_leancamento_pk").val(),
        ds_ic_status:ds_ic_status,
        ic_status:$("#ic_status").val(),
        ds_usuario_cadastro:ds_usuario_cadastro,
        usuario_cadastro_pk:$("#usuario_cadastro_pk").val(),
        dt_faturamento_ini:dt_faturamento_ini,
        dt_faturamento_fim:dt_faturamento_fim
    }
    sendPost('relatorio', 'receptivoPlanoContas', objParametros);
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
      
    };         
   
    var arrCarregar = carregarController("plano_contas", "listaPorCategoria", objParametros);   

    carregarComboAjax($("#tipos_operacao_pk_receita"), arrCarregar, " ", "pk", "ds_tipo_operacao");       
}

function fcSelecionaStatus(){
    if($("#dt_pagamento_ini").val()!=''){
        $("#ic_status").val('1')
    }else{      
        $("#ic_status option:selected").text('')        
    }
    
}

$(document).ready(function(){    
           
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);
    $(document).on('click', '#cmdCancelar', fcCancelar);
    
    $(".chzn-select").chosen({allow_single_deselect: true});
    fcListarTipoCategoriaReceita();
    carregarComboEmpresaPk();
    carregarComboUsuarioCadastro();
    
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
    
    $("#dt_pagamento_ini").change(function(){ 
        fcSelecionaStatus();   
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
    
    $('#dt_pagamento_ini').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_pagamento_ini").keypress(function(){
       mascara(this,mdata);
    });
    
     $('#dt_pagamento_fim').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_pagamento_fim").keypress(function(){
       mascara(this,mdata);
    });
    
        $('#dt_faturamento_ini').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_faturamento_ini").keypress(function(){
       mascara(this,mdata);
    });
    
     $('#dt_faturamento_fim').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_faturamento_fim").keypress(function(){
       mascara(this,mdata);
    });
    
    
});


