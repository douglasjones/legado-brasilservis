function fcLimparVariavelProdutos(){
    $("#produto_compra_pk").val("");
    $("#categorias_ins_prod_pk").val("");
    $("#produtos_ins_prod_pk").val("");
    $("#vl_item_produto").on('keyup', function () {
        mascara(this,moeda);
    });
    $("#vl_item_produto").val("");
    $("#qtde_produto").val("");
    $("#acao").val("ins")

    if($("#categoria_pk_ins").val()!=""){
        $("#categorias_ins_prod_pk").val($("#categoria_pk_ins").val());
    }

    $('#ic_entrega').prop('checked', true);

}

function fcCarregarProdutosProd(categorias_produto_pk){
    var objParametros = {
        "categorias_produto_pk": categorias_produto_pk
    };
    var arrCarregar = carregarController("produto", "listarPorCategoria", objParametros);

    carregarComboAjax($("#produtos_ins_prod_pk"), arrCarregar, " ", "pk", "ds_produto");
}

function fcCarregarCategoriasProd(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros);
    carregarComboAjax($("#categorias_ins_prod_pk"), arrCarregar, " ", "pk", "ds_categoria");
}

function fcIncluirProduto(){
    $(".chzn-select").chosen('destroy');
    fcLimparVariavelProdutos();
    fcCarregarCategoriasProd();
    $("#categorias_ins_prod_pk").change(function(){
        $(".chzn-select").chosen('destroy');
        fcCarregarProdutosProd($("#categorias_ins_prod_pk").val());
        $(".chzn-select").chosen({allow_single_deselect: true});

    });
    $("#vl_item_produto").on('keyup', function () {
        mascara(this,moeda);
    });
    $("#qtde_produto").on('keyup', function () {
        mascara(this,soNumeros);
    });
    $(".chzn-select").chosen({allow_single_deselect: true});
    $("#janela_produto").modal("show");

}

function fcExcluirProduto(v_pk){

    if(v_pk != ""){
        var objParametros = {
            "pk": v_pk
        };

        var arrExcluir = carregarController("produto_item", "excluir", objParametros);

        if (arrExcluir.status == true){

            //Exibe a mensagem
            utilsJS.toastNotify(true,arrExcluir.message);
            //fcRecarregarGridMateriais();
        }
        else{
            utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
        }
    }
    else{
        sweetMensagem('warning',"Código não encontrado");
    }
}

function fcEditarProduto(objRegistro){
    fcLimparVariavelProdutos();
    fcCarregarCategoriasProd();
    //Lista fornecedor e Produtos conforme a categoria
    fcCarregarProdutosProd(objRegistro['categorias_produto_pk']);

    $("#acao").val("upd");
    $("#produto_compra_pk").val(objRegistro['pk']);
    $("#categorias_ins_prod_pk").val(objRegistro['categorias_produto_pk']);
    $("#produtos_ins_prod_pk").val(objRegistro['produtos_pk']);
    $("#vl_item_produto").val((objRegistro['vl_item']));
    $("#qtde_produto").val(objRegistro['qtde']);
    $('#categorias_ins_prod_pk').select2();
    $('#produtos_ins_prod_pk').select2();
    if(objRegistro['ic_entrega']==2){
        $('#ic_entrega').prop('checked', true);
    }
    else{
        $('#ic_entrega').prop('checked', false);
    }


    $("#janela_produto").modal("show");


}

function fcEnviarProduto(){

    if($("#categorias_ins_prod_pk").val()==""){
        $("#alert_categoria_prod").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_categoria_prod").slideUp(500);
        });
        $('#categorias_ins_prod_pk').focus();
        return false;
    }
    if($("#produtos_ins_prod_pk").val()==""){
        $("#alert_produto_prod").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_produto_prod").slideUp(500);
        });
        $('#produtos_ins_prod_pk').focus();
        return false;
    }
    if($("#vl_item_produto").val()==""){
        $("#alert_vl_prod").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_vl_prod").slideUp(500);
        });
        $('#vl_item_produto').focus();
        return false;
    }
    if($("#qtde_produto").val()==""){
        $("#alert_qtde_prod").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_qtde_prod").slideUp(500);
        });
        $('#qtde_produto').focus();
        return false;
    }
    if($("#compras_pk").val() == ""){
        if($("#acao").val() == "ins"){
            fcIncluirProdutoSemPk();
        }
        else if($("#acao").val() == "upd"){
            fcEditarProdutoSemPk();
        }
    }else{
        fcSalvarProduto();
    }
    $("#janela_produto").modal("hide");
}

function fcEditarProdutoSemPk(){
    fcIncluirProdutoSemPk();
    tblCompraProduto.row(rLinhaSelecionadaProd).remove().draw();
    return false;
}

function fcIncluirProdutoSemPk(){
    var v_ic_entrega = 1;
    var ds_ic_entrega = "";
    if($('#ic_entrega').is(":checked")){
        v_ic_entrega = 2;
        ds_ic_entrega = "Sim";
    }
    else{
        v_ic_entrega = 1;
        ds_ic_entrega = "Não";
    }

    tblCompraProduto.row.add( [
        "<td></td>",
        "<td><input type='hidden' id='categorias_produto_pk[]' value ='"+$("#categorias_ins_prod_pk").val()+"'>"+ $("#categorias_ins_prod_pk option:selected").text()+"</td>",
        "<td><input type='hidden' id='produtos_pk[]' value ='"+$("#produtos_ins_prod_pk").val()+"'>"+ $("#produtos_ins_prod_pk option:selected").text()+"</td>",
        "<td><input type='hidden' id='qtde[]' value ='"+$("#qtde_produto").val()+"'>"+ $("#qtde_produto").val()+"</td>",
        "<td><input type='hidden' id='ic_entrega[]' value ='"+v_ic_entrega+"'>"+ ds_ic_entrega+"</td>",
        "<td><input type='hidden' id='vl_item[]' value ='"+$("#vl_item_produto").val()+"'>"+ $("#vl_item_produto").val()+"</td>",
        "<td><a class='function_edit' style='margin-right: 12px;'><i class='fa fa-pencil-alt'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a class='function_delete'style='margin-right: 12px;font-size: 18px;color:blue'><i class='bi bi-x-circle'></i></a></td>"
    ] ).draw().node();

    return false;
}

function fcRecarregarGridProduto(){
    tblCompraProduto.clear().destroy();
    fcCarregarGridProduto();
}

function fcSalvarProduto(){

    var v_ic_entrega = 1;
    if($('#ic_entrega').is(":checked")){
        v_ic_entrega = 2;
    }
    else{
        v_ic_entrega = 1;
    }
    console.log($("#fornecedor_pk_ins").val())
    //atualiza o registro no DB, pois já existe uma PK para contatos no banco.
    var objParametros = {
        "pk": $("#produto_compra_pk").val(),
        "compras_pk": $("#compras_pk").val(),
        "produtos_pk": $("#produtos_ins_prod_pk").val(),
        "fornecedor_pk": $("#fornecedor_pk_ins").val(),
        "qtde": $("#qtde_produto").val(),
        "ic_status": $("#ic_status").val(),
        "vl_item": moeda2float($("#vl_item_produto").val()),
        "ic_entrega": v_ic_entrega
    };
    var arrEnviar = carregarController("compra", "salvarProduto", objParametros);

    if (arrEnviar.status == true){
        fcRecarregarGridProduto();
    }
    else{
        utilsJS.toastNotify(false,arrEnviar.result);
    }

}


function fecharModalProduto(){
    $("#janela_produto").modal("hide");
}
