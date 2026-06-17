function fcCarregarLeads() {
    var objParametros = {};
    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarCategorias(){    
    var objParametros = {
        "pk": ""
    };          
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros);    
    carregarComboAjax($("#categorias_produto_pk"), arrCarregar, " ", "pk", "ds_categoria");        
}

function fcCarregarProdutos(categorias_produto_pk){    
    var objParametros = {
        "categorias_produto_pk": categorias_produto_pk
    };          
    var arrCarregar = carregarController("produto", "listarPorCategoria", objParametros);  
    carregarComboAjax($("#produtos_pk"), arrCarregar, " ", "pk", "ds_produto");        
}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','compra_estoque' ,objParametros);
}


function fcCarregarGrid(){
    var ds_posto_trabalho = $("#leads_pk option:selected").text();
    var ds_categorias_produto = $("#categorias_produto_pk option:selected").text();
    var ds_produto = $("#produtos_pk option:selected").text();
    var ds_tipo_operacao = $("#tipo_operacao_pk option:selected").text();


    objParametros = {
        categorias_produto_pk:$("#categorias_produto_pk").val(),
        leads_pk:$("#leads_pk").val(),
        usuario_cadastro_pk:$("#usuario_cadastro_pk").val(),
        produtos_pk:$("#produtos_pk").val(),
        tipo_operacao_pk:$("#tipo_operacao_pk").val(),
        dt_ini_compra:$("#dt_ini_compra").val(),
        dt_fim_compra:$("#dt_fim_compra").val(),
        ds_posto_trabalho:ds_posto_trabalho,
        ds_categorias_produto:ds_categorias_produto,
        ds_produto:ds_produto,
        ds_tipo_operacao:ds_tipo_operacao
        //ds_usuario_cadastro:ds_usuario_cadastro
    }
    
    sendPost('relatorio', 'receptivoCompraMovimentacaoLead',objParametros);
}

$(document).ready(function () {
    fcCarregarLeads();
    fcCarregarCategorias();
    $("#categorias_produto_pk").change(function(){     
        if($("#categorias_produto_pk").val()!=''){
            fcCarregarProdutos($("#categorias_produto_pk").val());
            $("#produtos_pk").select2();
        }            
    });
    
    $("#categorias_produto_pk").select2();
    $("#leads_pk").select2();

    $('#dt_ini_compra').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 
    $("#dt_ini_compra").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_fim_compra').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 
    $("#dt_fim_compra").keypress(function(){
        mascara(this,mdata);
    });
    
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);
    $(document).on('click', '#cmdCancelar', fcCancelar);
});