function fcSalvarCompra(){


    var strDocs = fcFormatarDadosDocumentos();

    if($("#fornecedor_pk_ins").val()==""){
        $("#alert_fornecedor").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_fornecedor").slideUp(500);
        });
        $('#fornecedor_pk_ins').focus();
        return false;
    }
    if($("#categoria_pk_ins").val()==""){
        $("#alert_categoria").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_categoria").slideUp(500);
        });
        $('#categoria_pk_ins').focus();
        return false;
    }
    if($("#empresa_pk_ins").val()==""){
        $("#alert_empresa").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_empresa").slideUp(500);
        });
        $('#empresa_pk_ins').focus();
        return false;
    }
    if($("#ds_numero_nota_ins").val()==""){
        $("#alert_n_doc").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_n_doc").slideUp(500);
        });
        $('#ds_numero_nota_ins').focus();
        return false;
    }
    if($("#dt_pagamento").val()==""){
        $("#alert_dt_pag").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_pag").slideUp(500);
        });
        $('#dt_pagamento').focus();
        return false;
    }
    if($("#metodos_pagamento_pk").val()==""){
        $("#alert_forma_apg").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_forma_apg").slideUp(500);
        });
        $('#metodos_pagamento_pk').focus();
        return false;
    }
    if($("#vl_notafiscal").val()==""){
        $("#alert_vl_n_doc").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_vl_n_doc").slideUp(500);
        });
        $('#vl_notafiscal').focus();
        return false;
    }

    var vl_frete = "";
    if($("#vl_frete").val()!=""){
        var vl_frete = moeda2float($("#vl_frete").val());
    }
    //atualiza o registro no DB, pois já existe uma PK para contatos no banco.
    var objParametros = {
        "pk": $("#compras_pk").val(),
        "fornecedor_pk": $("#fornecedor_pk_ins").val(),
        "categoria_pk": $("#categoria_pk_ins").val(),
        "conta_pk": $("#empresa_pk_ins").val(),
        "dt_pagamento": $("#dt_pagamento").val(),
        "metodos_pagamento_pk": $("#metodos_pagamento_pk").val(),
        "qtde_parcelas": $("#qtde_parcelas").val(),
        "ds_numero_nota": $("#ds_numero_nota_ins").val(),
        "vl_notafiscal": moeda2float($("#vl_notafiscal").val()),
        "vl_frete":vl_frete,
        "dt_entrega": $("#dt_entrega").val(),
        "grupo_lancamento_centro_custo_pk": $("#grupo_lancamento_centro_custo_pk").val(),
        "centro_custo_pk": $("#tipo_grupo_centro_custo_pk").val(),
        "ic_status": $("#ic_status").val(),
        "documentos_pk": strDocs
    };
    var arrEnviar = carregarController("compra", "salvar", objParametros);

    if (arrEnviar.status == true){
        if($("#compras_pk").val()==""){
            fcSalvarProdutoAposSalvarCompra(arrEnviar.data);
        }
        utilsJS.toastNotify(true,arrEnviar.message);

        setTimeout(function(){
            sendPost("compra","receptivo", {});
        }, 800);



    }
    else{
        utilsJS.toastNotify(false,arrEnviar.result);
    }

}


function fcSalvarProdutoAposSalvarCompra(compras_pk){

    $("#tblCompraProduto").find('tbody tr').each(function () {
        var objParametros = {
            "pk": "",
            "compras_pk": compras_pk,
            "produtos_pk": $(this).find('td:nth-child(3) input').val(),
            "qtde": $(this).find('td:nth-child(4) input').val(),
            "ic_status": $("#ic_status").val(),
            "fornecedor_pk": $("#fornecedor_pk_ins").val(),
            "vl_item": moeda2float($(this).find('td:nth-child(6) input').val()),
            "ic_entrega":$(this).find('td:nth-child(5) input').val()
        };
        var arrEnviar = carregarController("compra", "salvarProduto", objParametros);

        if (arrEnviar.status == true){

        }
        else{
            utilsJS.toastNotify(false,arrEnviar.result);
        }
    });
}


function fcCarregarGridProduto(){



    if($("#compras_pk").val()==""){
        tblCompraProduto = $('#tblCompraProduto').DataTable( {
            responsive: true,
            scrollX: true,
        });

        //Atribui os eventos na coluna ação.
        $('#tblCompraProduto tbody').on('click', '.function_edit', function (e) {
            e.preventDefault();
            let element = $(this);
            $("#categorias_ins_prod_pk").val(element.parents('tr').find("td:nth-child(2) input").val());
            $("#produtos_ins_prod_pk").val(element.parents('tr').find("td:nth-child(3) input").val());
            $("#qtde_produto").val(element.parents('tr').find("td:nth-child(4) input").val());
            $("#vl_item_produto").val(element.parents('tr').find("td:nth-child(6) input").val());
            $("#janela_produto").modal("show");

            tblCompraProduto.row($(this).parents('tr')).remove().draw();
        } );

        $('#tblCompraProduto tbody').on('click', '.function_delete', function () {
            tblCompraProduto.row($(this).parents('tr')).remove().draw();
        } );
    }
    else{
        var objParametros = {
            "compras_pk": $("#compras_pk").val()
        };

        var v_url = routes_api("produto_item", "listarPorCompra", objParametros);

        //Trata a tabela
        tblCompraProduto = $('#tblCompraProduto').DataTable({
            searching: true,
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
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_categoria'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_produto'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },{
                    mRender: function (data, type, full) {
                        return full['qtde'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },{
                    mRender: function (data, type, full) {
                        return full['ds_entrega'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },{
                    mRender: function (data, type, full) {
                        return full['vl_item'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        var buttonPainel = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="editar"></i></span></a> ';
                        var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';

                        return buttonPainel + buttonDelete;
                    },
                    'orderable': false,
                    'searchable': false,
                    width: '80px'
                }
            ]
        });


        //Atribui os eventos na coluna ação.
        $('#tblCompraProduto tbody').on('click', '.function_edit', function () {
            var data;
            rLinhaSelecionadaProd = null;
            if(tblCompraProduto.row( $(this).parents('li')).data()){
                data = tblCompraProduto.row( $(this).parents('li')).data();
                rLinhaSelecionadaProd = $(this).parents('li');
            }
            else if(tblCompraProduto.row( $(this).parents('tr')).data()){
                data = tblCompraProduto.row( $(this).parents('tr')).data();
                rLinhaSelecionadaProd = $(this).parents('tr');
            }
            fcEditarProduto(data);

        } );

        $('#tblCompraProduto tbody').on('click', '.function_delete', function () {
            var data;
            if(tblCompraProduto.row( $(this).parents('li') ).data()){
                data = tblCompraProduto.row( $(this).parents('li') ).data();
            }
            else if(tblCompraProduto.row( $(this).parents('tr') ).data()){
                data = tblCompraProduto.row( $(this).parents('tr') ).data();
            }
            if(data['pk'] != ""){
                fcExcluirProduto(data['pk']);
            }
            tblCompraProduto.row($(this).parents('tr')).remove().draw();

        } );
    }


}

function fcCarregarGridDocumentos(){
    var objParametros = {
        "compras_pk": $("#compras_pk").val()
    };

    var v_url = routes_api("documento", "listarDocumentosCompra", objParametros);
    //Trata a tabela
    tblDocumentos = $('#tblDocumentos').DataTable({
        searching: false,
        paging: false,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
        iDisplayLength: 10,
        processing: false,
        serverSide: false,
        ajax: v_url,
        responsive: true,
        scrollX: true,
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },
        order: [
            [0, "asc"]
        ],
        columns: [
            {
                mRender: function (data, type, full) {
                    return full['t_pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_documento'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_obs'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_nome_original'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonDelete = "<i class='fa fa-download function_download' style='font-size:18px; color:blue' title='DOWNLOAD DOCUMENTO'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='fa fa-trash function_delete' style='font-size:18px; color:blue' title='EXCLUIR O DOCUMENTO'></i>";
                    return  buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]
    });
    $('#tblDocumentos tbody').on('click', '.function_download', function () {
        var data;

        if(tblDocumentos.row( $(this).parents('li') ).data()){
            data = tblDocumentos.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentos.row( $(this).parents('tr') ).data()){
            data = tblDocumentos.row( $(this).parents('tr') ).data();
        }
        fcDownloadDocumento(data['pk_doc_bd'],data['t_ds_documento']);
    });
    $('#tblDocumentos tbody').on('click', '.function_delete', function () {
        var data;

        if(tblDocumentos.row( $(this).parents('li') ).data()){
            data = tblDocumentos.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentos.row( $(this).parents('tr') ).data()){
            data = tblDocumentos.row( $(this).parents('tr') ).data();
        }

        if(data['t_pk'] != ""){
            fcExcluirDocumento(data['t_pk'],data['pk_doc_bd']);
        }
    });


}

function fcDownloadDocumento(pk_doc_bd,ds_documento){
    var arrCarregar = permissao("documento", "ins");

    if (arrCarregar.status != true){
        sweetMensagem('warning','Você não tem permissão');
        return false;
    }

    //var url_documento = (window.location.protocol+"//"+window.location.host+"/app/src/docs/"+ds_documento)

    //DOWNLOAD
    var v_url = "/documento/download?pk_doc_bd="+pk_doc_bd+"&ds_documento="+ds_documento;

    window.open(v_url, '_blank');
}

function fcExcluirDocumento(v_pk,v_pk_doc){
    var arrCarregar = permissao("documento", "del");

    if (arrCarregar.status != true){
        sweetMensagem('warning','Você não tem permissão');
        return false;
    }
    if(v_pk != ""){

        var objParametros = {
            "pk": v_pk,
            "pk_doc_bd":v_pk_doc
        };

        var arrExcluir = carregarController("documento", "excluir", objParametros);

        if (arrExcluir.status == true){

            //Exibe a mensagem
            utilsJS.toastNotify(true,arrExcluir.message);
            //fcExcluirArquivo(v_ds_documento);
            tblDocumentos.clear().destroy();
            fcCarregarGridDocumentos();
        }
        else{
            utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
        }
    }
    else{
        utilsJS.toastNotify(false,'Código não encontrado');
    }
}

function fcCarregarCategorias(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros);
    carregarComboAjax($("#categoria_pk_ins"), arrCarregar, " ", "pk", "ds_categoria");
}

function fcCarregarFornecedor(categorias_produto_pk){
    var objParametros = {
        "categorias_produto_pk": categorias_produto_pk
    };
    var arrCarregar = carregarController("fornecedor", "listarPorCategoria", objParametros);
    carregarComboAjax($("#fornecedor_pk_ins"), arrCarregar, " ", "pk", "ds_fornecedor");
}

function fcCarregarProdutos(categorias_produto_pk){
    var objParametros = {
        "categorias_produto_pk": categorias_produto_pk
    };
    var arrCarregar = carregarController("produto", "listarPorCategoria", objParametros);

    carregarComboAjax($("#produtos_pk"), arrCarregar, " ", "pk", "ds_produto");
}

function fcLimparVariavelCompras(){
    $("#fornecedor_pk_ins").val("");
    $("#categoria_pk_ins").val("");
    $("#empresa_pk_ins").val("");
    $("#tipo_grupo_centro_custo_pk").val("");
    $("#grupo_lancamento_centro_custo_pk").val("");
    $("#ds_numero_nota_ins").val("");
    $("#dt_pagamento").val("");
    $("#dt_entrega").val("");
    $("#metodos_pagamento_pk").val("");
    $("#qtde_parcelas").val("");
    $("#vl_notafiscal").val("");
    $("#vl_frete").val("");
    $("#compras_pk").val("");
}

function fcListarOptionParcela(){
    $("#qtde_parcela_combo").append("");
    $("#qtde_parcela_combo").empty();
    var str ="";
    str += "<select class='form-control form-control-sm'  id='qtde_parcelas' name='qtde_parcelas' >";
    str += "    <option ></option>";
    for(i=1;i<73;i++){
        str += "<option value='"+i+"'>"+i+" Parcela(s)</option>";
    }
    str += "</select>";

    $("#qtde_parcela_combo").append(str);
}

function fcListarMetodosPagamentoReceita(){
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("metodo_pagamento", "listarTodos", objParametros);
    carregarComboAjax($("#metodos_pagamento_pk"), arrCarregar, " ", "pk", "ds_metodo_pagamento");
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

function fcSelecionarCategoriaFornecedor(){
    var objParametros = {
        "pk": $("#fornecedor_pk_ins").val()
    };
    var arrCarregar = carregarController("fornecedor", "listarPk", objParametros);
    if(arrCarregar.data[0]['categorias_produto_pk']!=null){
        $("#categoria_pk_ins").val(arrCarregar.data[0]['categorias_produto_pk']);
    }

}

function fcCarregar(){
    if($("#compras_pk").val()>0){
        var objParametros = {
            "pk": $("#compras_pk").val()
        };
        var arrCarregar = carregarController("compra", "listarPk", objParametros);
        $("#categoria_pk_ins").val(arrCarregar.data[0]['categoria_pk'])
        fcCarregarFornecedor('');
        fcCarregarProdutos($("#categoria_pk_ins").val());
        $("#fornecedor_pk_ins").val(arrCarregar.data[0]['fornecedor_pk'])
        $("#empresa_pk_ins").val(arrCarregar.data[0]['conta_pk'])
        $("#tipo_grupo_centro_custo_pk").val(arrCarregar.data[0]['centro_custo_pk'])

        fcListarItensGruposCentroCustoReceita();
        $("#grupo_lancamento_centro_custo_pk").val(arrCarregar.data[0]['grupo_lancamento_centro_custo_pk'])
        $("#metodos_pagamento_pk").val(arrCarregar.data[0]['metodos_pagamento_pk'])
        $("#ds_numero_nota_ins").val(arrCarregar.data[0]['ds_numero_nota'])
        $("#dt_pagamento").val(arrCarregar.data[0]['dt_pagamento'])
        $("#dt_entrega").val(arrCarregar.data[0]['dt_entrega'])
        $("#vl_notafiscal").val(arrCarregar.data[0]['vl_notafiscal'])
        $("#vl_frete").val(arrCarregar.data[0]['vl_frete'])
        $("#qtde_parcelas").val(arrCarregar.data[0]['qtde_parcelas'])
        $("#ic_status").val(arrCarregar.data[0]['ic_status'])
        $("#compras_pk").val(arrCarregar.data[0]['pk'])

        $(".chzn-select").chosen('destroy');
    }
}

function fcCarregarEmpresa(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);
    carregarComboAjax($("#empresa_pk_ins"), arrCarregar, " ", "pk", "ds_conta");
}

function fcVoltar(){
    sendPost("compra","receptivo", {});
}

function fcCarregarInformacoesXml(file){
    let formDataXml = new FormData();
    formDataXml.append("arquivo_xml", file); // vai aparecer no PHP como $_FILES['arquivo_xml']
    $.ajax({
        url: '/api/compra/lerXml',
        type: "POST",
        data: formDataXml,
        contentType: false,
        processData: false,
        success: function(res){
            if(res.status){
                let dados = res.result;
                $(".chzn-select").chosen('destroy');
        
                // Fornecedor
                fcCarregarFornecedor('');
                $("#fornecedor_pk_ins").val(dados.emitente.pk);
                $("#categoria_pk_ins").val(99);


                fcCarregarEmpresa();
                $("#empresa_pk_ins").val(dados.destinatario.pk);

                fcListarMetodosPagamentoReceita();
                // Método de Pagamento
                $("#metodos_pagamento_pk").val(dados.pagamento.pk);


                $("#ds_numero_nota_ins").val(dados.nota.numero);
                let data = new Date(dados.nota.emissao);

                // Formata para dd/mm/yyyy
                let emissaoFormatada = ("0" + data.getDate()).slice(-2) + "/" +
                                    ("0" + (data.getMonth() + 1)).slice(-2) + "/" +
                                    data.getFullYear();

                $("#dt_pagamento").val(emissaoFormatada);
                $("#vl_notafiscal").val(dados.total.valor_nota);
                $("#vl_frete").val(dados.total.valor_frete);

                $(".chzn-select").chosen({allow_single_deselect: true});
                // Produtos
                if(dados.produtos && dados.produtos.length > 0){
                    dados.produtos.forEach(function(produto){
                        // Formata quantidade e valor
                        let quantidade = produto.quantidade.split('.')[0]; // pega só a parte inteira

                        tblCompraProduto.row.add([
                            "<td></td>",
                            "<td><input type='hidden' name='categorias_produto_pk[]' value='"+produto.categoria+"'>"+ produto.descricaoCategoria +"</td>",
                            "<td><input type='hidden' name='produtos_pk[]' value='"+produto.pk+"'>"+ produto.descricao +"</td>",
                            "<td><input type='hidden' name='qtde[]' value='"+quantidade+"'>"+ quantidade +"</td>",
                            "<td><input type='hidden' name='ic_entrega[]' value='2'>Sim</td>",
                            "<td><input type='hidden' name='vl_item[]' value='"+produto.valor_unitario+"'>"+ produto.valor_unitario +"</td>",
                            "<td><a class='function_edit' style='margin-right: 12px;'><i class='fa fa-pencil-alt'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a class='function_delete' style='margin-right: 12px;font-size: 18px;color:blue'><i class='bi bi-x-circle'></i></a></td>"
                        ]).draw();
                    });

                }

                utilsJS.toastNotify(true, "XML carregado com sucesso!");
            } else {
                utilsJS.toastNotify(false, "Erro: " + (res.message ?? "não foi possível carregar o XML"));
            }
        },
        error: function(err){
            utilsJS.toastNotify(false,"Erro ao enviar o XML");
            console.error(err);
        }
    });
}


$(document).ready(function(){

    fcListarOptionParcela();
    fcCarregarCategorias();
    fcCarregarFornecedor('');
    $("#categoria_pk_ins").change(function(){
        $(".chzn-select").chosen('destroy');
        fcCarregarProdutos($("#categoria_pk_ins").val());
        $(".chzn-select").chosen({allow_single_deselect: true});
    });
    fcCarregarEmpresa();
    fcListarMetodosPagamentoReceita();
    $("#tipo_grupo_centro_custo_pk").change(function(){
        $(".chzn-select").chosen('destroy');
        fcListarItensGruposCentroCustoReceita();
        $(".chzn-select").chosen({allow_single_deselect: true});
    });
    fcCarregarGridProduto();

    fcCarregarGridDocumentos();
    fcCarregar();

    $(document).on('click', '#cmdVoltar', fcVoltar);
    $(document).on('click', '#cmdIncluirProduto', fcIncluirProduto);
    $(document).on('click', '#cmdIncluirDocumento', fcAbrirFormNovoDocumento);
    $(document).on('click', '#cmdEnviarDocumento', fcValidarDocumentos);

    $("#vl_notafiscal").on('keyup', function () {
        mascara(this,moeda);
    });
    $("#vl_frete").on('keyup', function () {
        mascara(this,moeda);
    });
    $("#ds_numero_nota_ins").on('keyup', function () {
        mascara(this,soNumeros);
    });

    $('#dt_pagamento').datepicker({defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_pagamento").on('keyup', function () {
        mascara(this,mdata);
    });

    $('#dt_entrega').datepicker({defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_entrega").on('keyup', function () {
        mascara(this,mdata);
    });


   

    $(".chzn-select").chosen({allow_single_deselect: true});

    


    //------------------ANEXO XML--------------------
    const fileInput = document.getElementById("fileUploadXml");
    const fileName = document.getElementById("fileName");

    fileInput.addEventListener("change", function () {
        if (this.files.length > 0) {
        const file = this.files[0];
        const fileExt = file.name.split('.').pop().toLowerCase();

        if (fileExt !== "xml") {
            alert("⚠️ Apenas arquivos .xml são permitidos!");
            this.value = ""; // limpa o input
            fileName.textContent = "Nenhum arquivo selecionado";
        } else {
            fileName.textContent = "📄 " + file.name;
            //FUNÇÃO PARA MANDAR O XML VIA AJAX
            fcCarregarInformacoesXml(file);
        }
        } else {
        fileName.textContent = "Nenhum arquivo selecionado";
        }
    });

});
