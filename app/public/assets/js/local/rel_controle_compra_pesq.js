var tblResultado;

function fcCarregarGrid(){
    
    var ds_empresa = $("#empresa_pk :selected").text();
    var ds_fornecedor = $("#fornecedor_pk :selected").text();
    var ds_categoria = $("#categoria_pk :selected").text();
    var ds_grupo = $("#tipo_grupo_centro_custo_pk :selected").text();
    var ds_centro_custo = $("#grupo_lancamento_centro_custo_pk :selected").text();
    var ds_status = $("#ic_status :selected").text();

    objParametros = {
        ds_empresa: ds_empresa,
        empresa_pk: $("#empresa_pk").val(),
        fornecedor_pk: $("#fornecedor_pk").val(),
        ds_fornecedor: ds_fornecedor,
        categoria_pk: $("#categoria_pk").val(),
        ds_categoria: ds_categoria,
        tipo_grupo_centro_custo_pk: $("#tipo_grupo_centro_custo_pk").val(),
        ds_grupo: ds_grupo,
        grupo_lancamento_centro_custo_pk: $("#grupo_lancamento_centro_custo_pk").val(),
        ds_centro_custo: ds_centro_custo,
        dt_ini_cad: $("#dt_ini_cad").val(),
        dt_fim_cad: $("#dt_fim_cad").val(),
        dt_ini_compra: $("#dt_ini_compra").val(),
        dt_fim_compra: $("#dt_fim_compra").val(),
        ic_status: $("#ic_status").val(),
        ds_status: ds_status
    }
    sendPost("relatorio","receptivoControleCompra",objParametros);
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

function fcCarregarFornecedor(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("fornecedor", "listarPorCategoria", objParametros);
    carregarComboAjax($("#fornecedor_pk"), arrCarregar, " ", "pk", "ds_fornecedor");
}

function fcCarregarCategorias(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros);
    carregarComboAjax($("#categoria_pk"), arrCarregar, " ", "pk", "ds_categoria");
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


$(document).ready(function(){
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);

    fcCarregarEmpresa();
    fcCarregarFornecedor();
    fcCarregarCategorias();
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
    $('#dt_ini_compra').datepicker({
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
        
    //carrega cadastro fim
    $('#dt_fim_compra').datepicker({
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

    
    

});
