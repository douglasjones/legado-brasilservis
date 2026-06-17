var tblResultado;
var click_id = 0;


function fcCarregarGrid(){
    
    /*if($("#leads_pk").val()==""){
        alert("Por favor, selecione o Posto de Trabalho");
        return false;
    }*/
    
    var ds_leads = $("#leads_pk option:selected").text();
    var ds_categoria = $("#categorias_pk option:selected").text();
    var ds_produto = $("#produtos_pk option:selected").text();
    
    
    objParametros = {
        categorias_pk: $("#categorias_pk").val(),
        produtos_pk: $("#produtos_pk").val(),
        leads_pk: $("#leads_pk").val(),
        ds_lead:ds_leads,
        ds_produto:ds_produto,
        ds_categoria:ds_categoria 
    }
    sendPost("relatorio","receptivoEstoqueSintetico",objParametros);
}


function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','compra_estoque' ,objParametros);
}
function fcCarregarCategorias(){    
    var objParametros = {
        "pk": ""
    };          
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros);    
    carregarComboAjax($("#categorias_pk"), arrCarregar, " ", "pk", "ds_categoria");        
}
function fcCarregarLead(){    
    var objParametros = {
        "pk": ""
    };          
    var arrCarregar = carregarController("lead", "listarTodos", objParametros);    
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");        
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
    
 
    fcCarregarLead();
    
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


