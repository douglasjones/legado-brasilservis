function fcValidarForm(){

    $("#form").validate({
        rules :{
            produtos_pk:{
                required:true
            },
            fornecedor_pk:{
                required:true
            },
            qtde:{
                required:true
            }
            ,vl_unitario:{
                required:true
            }

        },
        messages:{
            fornecedor_pk:{
                required:"Por favor, informe o fornecedor!"
            },
            produtos_pk:{
                required:"Por favor, informe o produto "               
            },
            qtde:{
                required:"Por favor, informe a quantidade"               
            }
            ,vl_unitario:{
                required:"Por favor, informe o Valor Unitário"               
            }
        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });

}
function fcEnviar(){
    var v_pk = $("#pk").val();
    var v_ds_n_ordem = $("#ds_n_ordem").val();
    var v_obs_entrada_estoque = $("#obs_entrada_estoque").val();
    var v_fornecedor_pk = $("#fornecedor_pk").val();
    var v_produtos_pk = $("#produtos_pk").val();
    var v_qtde = $("#qtde").val();
    var v_vl_unitario = $("#vl_unitario").val();
    var strProdutoItens = fcFormatarDados();
    


    var objParametros = {
        "pk": (v_pk),
        "ds_n_ordem": (v_ds_n_ordem),
        "obs_entrada_estoque": (v_obs_entrada_estoque),
        "fornecedor_pk": (v_fornecedor_pk),
        "produtos_pk": (v_produtos_pk),
        "qtde": (v_qtde),
        "vl_unitario": moeda2float(v_vl_unitario),
        "produtos_itens": strProdutoItens
    };    

    var arrEnviar = carregarController("entrada_estoque", "salvar", objParametros);           
   
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true,arrEnviar.message);
        setTimeout(function(){
            sendPost('entrada_estoque','receptivo' ,objParametros);
        }, 800);
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost('entrada_estoque','receptivo' ,objParametros);
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("entrada_estoque", "listarPk", objParametros);

        if (arrCarregar.status == true){
            $(".chzn-select").chosen('destroy');
            $("#ds_n_ordem").val(arrCarregar.data[0]['ds_n_ordem']);
            $("#obs_entrada_estoque").val(arrCarregar.data[0]['obs_entrada_estoque']);
            $("#fornecedor_pk").val(arrCarregar.data[0]['fornecedor_pk']);
            $("#produtos_pk").val(arrCarregar.data[0]['produtos_pk']);
            $("#vl_unitario").val(float2moeda(arrCarregar.data[0]['vl_unitario']));
            $("#categorias_produto_pk").val(arrCarregar.data[0]['categorias_produto_pk']);
            $("#qtde").val(arrCarregar.data[0]['qtde']);
            $("#qtde_registro").val(arrCarregar.data[0]['qtde']);
             $(".chzn-select").chosen({allow_single_deselect: true});
             
             if(fcCarregarQtdeProdutosItens() > 0){
                   $('#ic_listar_itens').prop('checked', true);
                   $('#ic_listar_itens').prop('disabled', true);
                   $('#qtde').prop('disabled', true);
                   fcAtualizarDadosGrid();
               }
               else{
                   $('#ic_listar_itens').prop('checked', false);
                   $('#ic_listar_itens').prop('disabled', false);
                   $('#qtde').prop('disabled', true);
                   $('#exibir_grid_produto_itens').hide();
               } 

        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}
function fcCarregarCategorias(){    
    var objParametros = {
        "pk": ""
    };          
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros);    
    carregarComboAjax($("#categorias_produto_pk"), arrCarregar, " ", "pk", "ds_categoria");        
}
function fcCarregarFornecedor(categorias_produto_pk){    
    var objParametros = {
        "categorias_produto_pk": categorias_produto_pk
    };          
    var arrCarregar = carregarController("fornecedor", "listarPorCategoria", objParametros);    
    carregarComboAjax($("#fornecedor_pk"), arrCarregar, " ", "pk", "ds_fornecedor");        
}
function fcCarregarProdutos(categorias_produto_pk){    
    var objParametros = {
        "categorias_produto_pk": categorias_produto_pk
    };          
    var arrCarregar = carregarController("produto", "listarPorCategoria", objParametros);  

    carregarComboAjax($("#produtos_pk"), arrCarregar, " ", "pk", "ds_produto");        
}



function fcFormatarGrid(){
        
    tblProdutoItens = $("#tblProdutoItens").DataTable({
        responsive: true,
        scrollX: true, 
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },       
    });
    return false;
}
function fcIncluirLinha(){
    
    tblProdutoItens.row.add(
            ["<input type='text'  id='produtos_itens_pk' class='form-control form-control-sm' disabled/>",
             "<input type='text' class='form-control form-control-sm' id='ds_n_serie' />"
            ]
    ).draw( false );

    
    return false;
}

function fcFormatarDados(){

    var produtos_itens_pk = $("input[id='produtos_itens_pk']");
    var ds_n_serie = $("input[id='ds_n_serie']");
    
    var arrKeys = [];
    arrKeys[0] = "produtos_itens_pk";
    arrKeys[1] = "ds_n_serie";
    
    var arrDados = [];
   
    for(i = 0; i < produtos_itens_pk.length; i++){
        if(produtos_itens_pk.get(i).value!="" || ds_n_serie.get(i).value!=""){
            arrDados[i] = [produtos_itens_pk.get(i).value, ds_n_serie.get(i).value];
        }
        
        
    }
    
    return arrayToJson(arrKeys, arrDados);
    
}

function fcAtualizarDadosGrid(){
    if($("#pk").val()!=""){
        var objParametros = {
            "pk": $("#pk").val()
        };

        var arrCarregar = carregarController("produto_item", "listarProdutoEstoque", objParametros);

            if (arrCarregar.status == true){
                for(i = 0; i < arrCarregar.data.length; i++){
                    $('#exibir_grid_produto_itens').show();
                    //Adiciona a linha.
                    fcIncluirLinha();

                    //Pega as variaveis 
                    var produtos_itens_pk = $("input[id='produtos_itens_pk']");
                    var ds_n_serie = $("input[id='ds_n_serie']");

                    produtos_itens_pk.get(i).value = arrCarregar.data[i]['pk'];
                    ds_n_serie.get(i).value = arrCarregar.data[i]['ds_n_serie'];
                }
            }
            else{
                utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
            }
    }
    
    
}
function fcCarregarQtdeProdutosItens(){
    if($("#pk").val()!=""){
        var objParametros = {
            "pk": $("#pk").val()
        };          
        var arrCarregar = carregarController("produto_item", "listarProdutoEstoqueNSerie", objParametros);  

        return arrCarregar.data.length ;
    }
}

function fcMostrarGridProdutosItens(){
    if($("#pk").val()!=""){
         if($('#ic_listar_itens').is(":checked")){
            tblProdutoItens.clear().destroy();
            fcFormatarGrid();
            fcAtualizarDadosGrid();
            for(i=0; i< ($("#qtde").val() - $("#qtde_registro").val());i++){
                fcIncluirLinha();
            } 
        }
        else{
            tblProdutoItens.clear().destroy();
            fcFormatarGrid();

           $('#exibir_grid_produto_itens').hide();
        }       
    }
    else{
        if($('#ic_listar_itens').is(":checked")){
            tblProdutoItens.clear().destroy();
            fcFormatarGrid();
            fcAtualizarDadosGrid();
            for(i=0; i< ($("#qtde").val());i++){
                fcIncluirLinha();
            } 
        }
        else{
            tblProdutoItens.clear().destroy();
            fcFormatarGrid();

           $('#exibir_grid_produto_itens').hide();
        } 
    }
    
}

$(document).ready(function()
    {
        //Atribui os eventos
        $(document).on('click', '#cmdCancelar', fcCancelar);
        $(document).on('click', '#cmdCancelar2', fcCancelar);
        $(document).on('click', '#ic_listar_itens', fcMostrarGridProdutosItens);
        
        $("#vl_unitario").keypress(function(){
            mascara(this,moeda);
        });
        
        //Combo Categorias        
        fcCarregarCategorias();
        //Combo Fornecedores
        fcCarregarFornecedor("");       
        //Combo  Produtos
        fcCarregarProdutos("");
        $(".chzn-select").chosen({allow_single_deselect: true});
        //Lista fornecedor e Produtos conforme a categoria        
        $("#categorias_produto_pk").change(function(){     
            if($("#categorias_produto_pk").val()!=''){
                $(".chzn-select").chosen('destroy');
                fcCarregarFornecedor($("#categorias_produto_pk").val());
                fcCarregarProdutos($("#categorias_produto_pk").val());
                $(".chzn-select").chosen({allow_single_deselect: true});
            }            
        });
        
        
        
        //Atribui a validação do formulário dos campos obrigatórios
        fcValidarForm();

        //Verifica se o registro é para alteracao e puxa os dados.
        
        
        fcFormatarGrid();
        
        $("#qtde").change(function(){
            if($("#pk").val()!=""){
                
                tblProdutoItens.clear().destroy();
                fcFormatarGrid();
                fcAtualizarDadosGrid();
                for(i=0; i< ($("#qtde").val() - $("#qtde_registro").val());i++){
                    fcIncluirLinha();
                }
            }
            else{
                tblProdutoItens.clear().destroy();
                fcFormatarGrid();
                fcAtualizarDadosGrid();
                for(i=0; i< $("#qtde").val();i++){
                    fcIncluirLinha();
                }
            }
            
        });
        
        fcCarregar();
    }
);
