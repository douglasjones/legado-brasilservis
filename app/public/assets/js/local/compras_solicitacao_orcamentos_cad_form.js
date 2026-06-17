var tblProdutosItens;
function fcEnviar(){

    //validação de campos
    var v_vl_toral = new Number(0);
    var  data = tblProdutosItens.rows().data();

    if(data.length==0){
        sweetMensagem('warning','Inclua ao menos um Produto / Item para salvar o orçamento!'); 
       return false; 
    }

    if($("#pk").val()==""){
        $("#tblProdutosItens").find('tbody tr').each(function () { //calcula o valor total
            var vl_item = $(this).find('td:nth-child(5) input').val().replace(',','.');
            v_vl_toral +=(vl_item * $(this).find('td:nth-child(4) input').val())

        });
    }
    else{
        for(i = 0; i< data.length; i++){//calcula o valor total
            var vl_item = data[i]['vl_unitario'].replace(',','.');
            v_vl_toral +=(vl_item * data[i]['qtde_produto'])
        }
    }


    var v_fornecedor_pk = $("#fornecedor_pk").val();
    var v_dt_pevisao_entrega = $("#dt_pevisao_entrega").val();
    var v_vl_frete = $("#vl_frete").val().replace(',','.');
    var v_vl_total = v_vl_toral;
    var v_obs_orcamento = $("#obs_orcamento").val();
    var v_compra_solicitacao_pk = "";
    var v_obs_aprovacao= $("#obs_aprovacao").val();
    if($("#compra_solicitacao_pk").val() > 0){
        v_compra_solicitacao_pk = $("#compra_solicitacao_pk").val();
    }
    if($("#usuario_aprovacao_pk").val() > 0){       
        if($("#ic_status_orcamento").val()==2){
            utilsJS.jqueryConfirm('Validar', 'Todos os dados do Orçamento foram conferidos, estão corretos ? Deseja realmente aprovar este Orçamento ?',function(){
                return true;
            });
        }              
        var v_ic_status = $("#ic_status_orcamento").val(); 
        var v_ds_status = $("#ic_status_orcamento option:selected").text() 
        $("#dt_aprovacao").val("sysdate()");
        $("#obs_aprovacao").val($("#obs_aprovacao_orcamento").val());   
        $("#ic_status").val($("#ic_status_orcamento").val());  
    }else{
        var v_ic_status = 1; 
        var v_ds_status = "Em Analise"; 
        $("#ic_status").val(1);
    }
    var objParametros = {
        "pk": $("#pk").val(),
        "fornecedor_pk": (v_fornecedor_pk),
        "dt_pevisao_entrega": (v_dt_pevisao_entrega),
        "vl_frete": (v_vl_frete),
        "vl_total": (v_vl_total),
        "obs_orcamento": (v_obs_orcamento),
        "ic_status": (v_ic_status),
        "obs_aprovacao": (v_obs_aprovacao),
        "compra_solicitacao_pk": (v_compra_solicitacao_pk)
    };    

    var arrEnviar = carregarController("compra_solicitacao_orcamento", "salvar", objParametros);           

    if (arrEnviar.status == true){

        if($("#pk").val()==""){
            if(arrEnviar.data>0){ //Excluir Itens
                var objParametros01 = {
                    "compras_solicitacao_orcamentos_pk": arrEnviar.data
                };
                var arrEnviarItens0 = carregarController("compra_solicitacao_orcamento_item", "excluirPorSolicitacaoOrcamento", objParametros01);
            }

            //cadastra os itens dos produtos
            $("#tblProdutosItens").find('tbody tr').each(function () {
                var objParametros0 = {
                    "categorias_produto_pk": $(this).find('td:nth-child(2) input').val(),
                    "produtos_pk": "",
                    "ds_produto": $(this).find('td:nth-child(3) input').val(),
                    "qtde_produto":$(this).find('td:nth-child(4) input').val(),
                    "vl_unitario": ($(this).find('td:nth-child(5) input').val().replace(',','.')),
                    "compras_solicitacao_orcamentos_pk": arrEnviar.data
                };
                var arrEnviarItens = carregarController("compra_solicitacao_orcamento_item", "salvar", objParametros0);
            });
            $("#tblOrcamento").dataTable().fnDestroy();//destroi o grid
        }

       sendPost("compra_solicitacao","cadForm",{compra_solicitacao_pk:$("#compra_solicitacao_pk").val()})

    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}


function fcCarregar(){
    if($("#pk").val() > 0){
        var objParametros = {
            "pk": $("#pk").val()
        };

        var arrCarregar = carregarController("compra_solicitacao_orcamento", "listarPk", objParametros);
        if (arrCarregar.status == true){
            $("#fornecedor_pk").val(arrCarregar.data[0]['fornecedor_pk']);
            $("#dt_pevisao_entrega").val(arrCarregar.data[0]['dt_pevisao_entrega']);
            $("#vl_frete").val(arrCarregar.data[0]['vl_frete']);
            $("#vl_total").val(arrCarregar.data[0]['vl_total']);
            $("#obs_orcamento").val(arrCarregar.data[0]['obs_orcamento']);
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);
            $("#compra_solicitacao_pk").val(arrCarregar.data[0]['compra_solicitacao_pk']);
        }
        else{
            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
        }
    }
}

//COMBOS
function  fcComboFornecedor(){
    var objParametros = {
    };       
    var arrCarregar = carregarController("fornecedor", "listarTodos", objParametros)
    carregarComboAjax($("#fornecedor_pk"), arrCarregar, " ", "pk", "ds_fornecedor");
}

function fcComboCategoriasProduto(){
    var objParametros = {
    };       
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros)

    carregarComboAjax($("#categorias_produto_pk"), arrCarregar, " ", "pk", "ds_categoria");
}

function fcComboProdutos(categoria_pk){
    var objParametros = {
        "categorias_produto_pk":categoria_pk
    };       
    var arrCarregar = carregarController("produto", "listarPorCategoria", objParametros)

    carregarComboAjax($("#produtos_pk"), arrCarregar, " ", "pk", "ds_produto");
}


//Inclusoes e grids
function fcGridItensProduto(){  

    
    if($("#pk").val()==""){
        tblProdutosItens = $('#tblProdutosItens').DataTable( {
            responsive: true,
            scrollX: true,
        });
        $('#tblProdutosItens tbody').on('click', '.function_delete', function () {
            tblProdutosItens.row($(this).parents('tr')).remove().draw();
        } );
    }
    else{
        var objParametros = {
            "compras_solicitacao_orcamentos_pk": $("#pk").val()
        };     

        var v_url = routes_api("compra_solicitacao_orcamento_item", "listarItensOrcamentoPk", objParametros);

        //Trata a tabela
        tblProdutosItens = $('#tblProdutosItens').DataTable({
                searching: false,
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
                            return full['pk'];
                        },
                        'orderable': true,
                        'searchable': false
        
                    },
                    {
                        mRender: function (data, type, full) {
                            return full['ds_categoria'];
                        },
                        'orderable': true,
                        'searchable': false,
        
                    },
                    {
                        mRender: function (data, type, full) {
                            return full['ds_produto_itens'];
                        },
                        'orderable': true,
                        'searchable': false,
        
                    },
                    {
                        mRender: function (data, type, full) {
                            return full['qtde_produto'];
                        },
                        'orderable': true,
                        'searchable': false,
        
                    },
                    {
                        mRender: function (data, type, full) {
                            return full['vl_unitario'];
                        },
                        'orderable': true,
                        'searchable': false,
        
                    },
                    {
                        mRender: function (data, type, full) {
                            var buttonDelete = '<a class="function_delete_itens"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';
                        
        
                            return buttonDelete;
                        },
                        'orderable': false,
                        'searchable': false,
                    }
                ]
            });    
        $('#tblProdutosItens tbody').on('click', '.function_delete_itens', function () {
            var data;

            if(tblProdutosItens.row( $(this).parents('li') ).data()){
                data = tblProdutosItens.row( $(this).parents('li') ).data();
            } else if(tblProdutosItens.row( $(this).parents('tr') ).data()){
                data = tblProdutosItens.row( $(this).parents('tr') ).data();
            }
            fcExcluirItens(data['pk']);
        } );       
    }
    
}
function fcExcluirItens(v_pk){
    
    utilsJS.jqueryConfirm('Excluir ?', 'Deseja excluir o registro '+v_pk+'?',function(){
        if(v_pk != ""){
            var objParametros = {
                "pk": v_pk
            };   
            var arrExcluir = carregarController("compra_solicitacao_orcamento_item", "excluir", objParametros);  

            if (arrExcluir.status == true){
                //Exibe a mensagem
                utilsJS.toastNotify(true,arrExcluir.message);
                // Reload datable
                tblProdutosItens.ajax.reload();
            }
            else{
                utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
            }
        }
        else{
            utilsJS.toastNotify(false,"Código não encontrado");
        }
    });
}

function fcIncluirItem(){       
    if($("#categorias_produto_pk").val()==""){
        $("#alert_categorias_produto_pk").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_fornecedor").slideUp(500);
        });
        $('#categorias_produto_pk').focus();
        return false;
    }

    if($("#produtos_pk").val()==""){
        $("#alert_produtos_pk").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_produtos_pk").slideUp(500);
        });
        $('#produtos_pk').focus();
        return false;
    }
    
    if($("#qtde_produto").val()==""){
        $("#alert_qtde_produto").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_qtde_produto").slideUp(500);
        });
        $('#qtde_produto').focus();
        return false;
    }
    
    if($("#vl_item_produto").val()==""){
        $("#alert_vl_item_produto").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_vl_item_produto").slideUp(500);
        });
        $('#vl_item_produto').focus();
        return false;
    }
    if($("#pk").val()!=""){
        var vl_item_li = $("#vl_item_produto").val().replace(',','.');
        var objParametros0 = {
            "categorias_produto_pk": $("#categorias_produto_pk").val(),
            "produtos_pk": $("#produtos_pk").val(),
            "ds_produto": $("#produtos_pk").val(),
            "qtde_produto":$("#qtde_produto").val(),
            "vl_unitario": float2moeda(vl_item_li),
            "compras_solicitacao_orcamentos_pk": $("#pk").val()
        };
        var arrEnviarItens = carregarController("compra_solicitacao_orcamento_item", "salvar", objParametros0);
        $("#tblProdutosItens").dataTable().fnDestroy();//destroi o grid
        fcGridItensProduto();//recarrega o grid
    }
    else{
        var counter = 1;
        tblProdutosItens.row.add( [
            "<td></td>",
            "<td><input type='hidden' id='categorias_produto_pk_grid[]' value ='"+$("#categorias_produto_pk").val()+"'>"+ $("#categorias_produto_pk option:selected").text()+"</td>",
            "<td><input type='hidden' id='ds_produto_grid[]' value ='"+$("#produtos_pk").val()+"'>"+ $("#produtos_pk option:selected").text()+"</td>",
            "<td><input type='hidden' id='qtde_produto_grid[]' value ='"+$("#qtde_produto").val()+"'>"+ $("#qtde_produto").val()+"</td>",
            "<td><input type='hidden' id='vl_unitario_grid[]' value ='"+$("#vl_item_produto").val()+"'>"+ $("#vl_item_produto").val()+"</td>",
            "<td><a class='function_delete' style='font-size=18px;color:blue'><i class='bi bi-x-circle'></i></a></td>"
        ] ).draw().node();
    }



    
  
    
    $("#categorias_produto_pk").val('');
    $("#produtos_pk").val('');
    $("#ds_produto").val('');
    $("#qtde_produto").val('');
    $("#vl_item_produto").val('');
    
    return false;
}

function fcVoltar(){
    var objParametros = {
        "compra_solicitacao_pk":$("#compra_solicitacao_pk").val(),
        "usuario_aprovacao_pk":$("#usuario_aprovacao_pk").val()
    };

    sendPost('compra_solicitacao','cadForm',objParametros)
}
function fecharModal(){
    $("#janela_orcamentos").modal("hide");
}
$(document).ready(function(){
    //combos
    fcComboFornecedor();
    fcComboCategoriasProduto();
    $("#categorias_produto_pk").change(function(){
        //$(".chzn-select").chosen('destroy');
        fcComboProdutos($("#categorias_produto_pk").val())//combo de centros de custo
    });
    //Grids
    fcGridItensProduto()

    //mascaras de campos
    $('#dt_pevisao_entrega').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate",  );

    $("#vl_frete").keypress(function(){
        mascara(this,moeda);
    });

    $("#dt_pevisao_entrega").keypress(function(){
        mascara(this,mdata);
    });

    $("#qtde_produto").on('keyup', function () {
        mascara(this,soNumeros);
    });

    //Incluir Produtos e itens
    $(document).on('click', '#cmdIncluirItem', fcIncluirItem);
    $(document).on('click', '#cmdIncluirItem', fcIncluirItem);
    $(document).on('click', '#cmdVoltar', fcVoltar);
    $(document).on('click', '#cmdVoltar1', fcVoltar);

    //Atribui os eventos
    //$(document).on('click', '#cmdEnviarOrcamento', fcEnviar);
    $(document).on('click', '#cmdAddProduto', fcAbrirModalAdicionarProduto);

    //Verifica se o registro é para alteracao e puxa os dados.
    fcCarregar();

    $(".chzn-select").chosen('destroy');

    if($("#usuario_aprovacao_pk").val() > 0){//libera a aprovação do orçamento
        $("#div_titulo_aprovacao").show();
        $("#div_aprovacao_status").show();
        $("#div_aprovacao_obs").show();
        //desabilita os campos

        $("#fornecedor_pk").prop("disabled", true);
        $("#dt_pevisao_entrega").prop("disabled", true);
        $("#vl_frete").prop("disabled", true);
        $("#obs_orcamento").prop("disabled", true);
        $("#div_incluir_item").hide();
        $("#div_incluir_produto").hide();

        $("#obs_aprovacao_orcamento").val('');
        $(".chzn-select").chosen('destroy');
        $("#ic_status_orcamento").val('');
    }else{
        $("#div_titulo_arovacao").hide();
        $("#div_aprovacao_status").hide();
        $("#div_aprovacao_obs").hide();
        $("#div_incluir_item").show();
        $("#div_incluir_produto").show();
    }

    $("#tblProdutosItens").dataTable().fnDestroy();//destroi o grid
    fcGridItensProduto();//recarrega o grid
     
});
