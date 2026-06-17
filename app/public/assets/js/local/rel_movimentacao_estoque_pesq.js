var tblResultado;
var click_id = 0;

function fcCarregarGrid(){
    if($("#dt_troca_ini").val()==""){
        sweetMensagem('warning','Por favor, informe o dia da escala!');
        return false;
    }
    if($("#dt_troca_fim").val()==""){
        sweetMensagem('warning','Por favor, informe o dia da escala!');
        return false;
    }

    var ds_lead = $("#leads_pk option:selected").text();
    var ds_colaborador = $("#colaboradores_pk option:selected").text();
    var ds_produto = $("#produtos_pk option:selected").text();
    var ds_categoria = $("#categorias_pk option:selected").text();
    
    objParametros = {
        colaboradores_pk: $("#colaboradores_pk").val(),
        leads_pk: $("#leads_pk").val(),
        categorias_pk: $("#categorias_pk").val(),
        produtos_pk:$("#produtos_pk").val(),
        dt_troca_ini:$("#dt_troca_ini").val(),
        dt_troca_fim:$("#dt_troca_fim").val(),
        ds_lead:ds_lead,
        ds_colaborador:ds_colaborador,
        ds_produto:ds_produto,
        ds_categoria:ds_categoria 
    }
    sendPost("relatorio","receptivoMovimentacaoEstoque",objParametros);
}

function fcCarregarColaborador(){
    
    var objParametros = {
        "pk": ""
    };      
    
    var arrCarregar = carregarController("colaborador", "listarTodosRel", objParametros);    
    carregarComboAjax($("#colaboradores_pk"), arrCarregar, " ", "pk", "ds_colaborador");
        
}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','compra_estoque' ,objParametros);
}

function fcCarregarLeads(){    
    var objParametros = {
        "pk": ""
    };         
    var arrCarregar = carregarController("lead", "listarTodos", objParametros);    
    
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");         
}
function fcCarregarColaboradores(){    
    var objParametros = {
        "leads_pk": $("#leads_pk").val()
    };         
    var arrCarregar = carregarController("colaborador", "listarColaboradorLeadCalendario", objParametros);
   
    carregarComboAjax($("#colaboradores_pk"), arrCarregar, " ", "pk", "ds_colaborador");       
}

function fcCarregarCategorias(){    
    var objParametros = {
        "pk": ""
    };          
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros);    
    carregarComboAjax($("#categorias_pk"), arrCarregar, " ", "pk", "ds_categoria");        
}

function fcCarregarProdutos(categorias_produto_pk){    
    var objParametros = {
        "categorias_produto_pk": categorias_produto_pk
    };          
    var arrCarregar = carregarController("produto", "listarPorCategoria", objParametros);  

    carregarComboAjax($("#produtos_pk"), arrCarregar, " ", "pk", "ds_produto");        
}

$(document).ready(function(){    
    /*var arrCarregar = permissao("rel_colaborador", "cons");        

    if (arrCarregar.result != 'success'){            
        alert('Falhar ao carregar o registro');
        return false;
    }*/
           
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);
    $(document).on('click', '#cmdCancelar', fcCancelar);
    
        //Datas
    $('#dt_troca_ini').datepicker({

        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()       
    });
    $("#dt_troca_ini").keypress(function(){
        mascara(this,mdata);      
        //$('#sandbox-container input').datepicker({ minDate: 0});
    });
    $('#dt_troca_fim').datepicker({

        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()       
    });
    $("#dt_troca_fim").keypress(function(){
        mascara(this,mdata);      
        //$('#sandbox-container input').datepicker({ minDate: 0});
    });
    
   
    
   
    
    
    $("#leads_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarColaboradores();
        $(".chzn-select").chosen({allow_single_deselect: true});
    });
    fcCarregarColaboradores();
    fcCarregarLeads();


    //Categorias
    fcCarregarCategorias("");
    //Produtos
    fcCarregarProdutos("");
       
    $("#categorias_pk").change(function(){         
        $(".chzn-select").chosen('destroy');
        fcCarregarProdutos($("#categorias_pk").val());
        $(".chzn-select").chosen({allow_single_deselect: true});
    });  

    $(".chzn-select").chosen({allow_single_deselect: true});
});


