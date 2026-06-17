var tblResultadoItens;

function fcValidarForm(){

    if($("#tipo_grupo_pk").val()==''){
        sweetMensagem('warning', "Por favor, selecione o campo Grupo de Origem Lançamento!");
        return false;        
    }
    if($("#tipo_grupo_pk option:selected").val()==1){
        if($("#leads_clientes_pk").val()==''){
            sweetMensagem('warning', "Por favor, selecione o campo Pago para\ Recebido de!");
            return false;        
        }
    }else if($("#tipo_grupo_pk option:selected").val()==2){
        if($("#colaborador_pk").val()==''){
            sweetMensagem('warning', "Por favor, selecione o campo Pago para\ Recebido de!");
            return false;        
        }
    }else if($("#tipo_grupo_pk option:selected").val()==3){
        if($("#fornecedor_pk").val()==''){
            sweetMensagem('warning', "Por favor, selecione o campo Pago para\ Recebido de!");
            return false;        
        }
    }

    if($("#ds_ano_vigente_teto").val()==''){
        sweetMensagem('warning', "Por favor, selecione o campo Ano de vigência Teto!");
        return false;        
    }

    if($("#vl_total_teto").val()==''){
        sweetMensagem('warning', "Por favor, informe o campo Vl Total Teto!");
        return false;        
    }

    if($("#ic_status").val()==''){
        sweetMensagem('warning', "Por favor, informe o campo Status!");
        return false;        
    }
    fcEnviar();
}

function fcEnviar(){
    var v_grupo_lancamento_centro_custo_pk = "";
    var v_grupo_leancamento_pk = "";
    var v_tipo_grupo_pk = $("#tipo_grupo_pk").val();

    if(v_tipo_grupo_pk == 2){
        v_grupo_lancamento_centro_custo_pk =  $("#grupo_lancamento_centro_custo_colaborador_pk").val()
    }else if(v_tipo_grupo_pk == 3){
        v_grupo_lancamento_centro_custo_pk = $("#grupo_lancamento_centro_custo_fornecedor_pk").val()
    }
    if (v_tipo_grupo_pk == 1) {
        v_grupo_leancamento_pk = $("#leads_clientes_pk").val();
    } else if (v_tipo_grupo_pk == 2) {
        v_grupo_leancamento_pk = $("#colaborador_pk").val();
    } else if (v_tipo_grupo_pk == 3) {
        v_grupo_leancamento_pk = $("#fornecedor_pk").val();
    } 

    var objParametros = {
        "pk":  $("#pk").val(),
        "tipo_grupo_pk":  v_tipo_grupo_pk,
        "grupo_leancamento_pk": v_grupo_leancamento_pk,
        "leads_posto_trabalho_pk": $("#leads_posto_trabalho_pk").val(),
        "contratos_pk": $("#leads_contratos_pk").val(),
        "colaborador_posto_trabalho_pk":  $("#colaborador_posto_trabalho_pk").val(),
        "colaborador_contratos_pk":  $("#colaborador_contratos_pk").val(),
        "fornecedor_posto_trabalho_pk": $("#fornecedor_posto_trabalho_pk").val(),
        "fornecedor_contratos_pk": $("#fornecedor_contratos_pk").val(),
        "ic_status": $("#ic_status").val(),
        "obs": $("#obs").val(),
        "vl_total_teto": moeda2float($("#vl_total_teto").val()),
        "vl_utilizado_atual": moeda2float($("#vl_utilizado_atual").val()),
        "ds_ano_vigente_teto": $("#ds_ano_vigente_teto").val(),
        "grupo_lancamento_centro_custo_pk": v_grupo_lancamento_centro_custo_pk          
    };    

    var arrEnviar = carregarController("teto_gasto", "salvar", objParametros); 
    
    if (arrEnviar.status == true){
        // Reload datable
        if($("#pk").val() !== ''){
            tblResultadoItens.clear().destroy();
        }
        $("#pk").val(arrEnviar.data);
        fcCarregarGrid();
        utilsJS.toastNotify(true, arrEnviar.message);
        $("#informacoesItens").show()
        
        
        
    }
    else{
        utilsJS.toastNotify(false, "Falhou a requisição para salvar o registro");
    }
}

function fcValidarItensForm(){

    if($("#tipos_operacao_pk").val()==''){
        sweetMensagem('warning', "Por favor, selecione o campo Tipo de Lançamento!");
        return false;        
    }
    if($("#categoria_operacao_pk").val()==''){
        sweetMensagem('warning', "Por favor, selecione o campo Categoria(s)!");
        return false;    
    }
    if($("#operacao_pk").val()==''){

        sweetMensagem('warning', "Por favor, selecione o campo Planos de Conta!");
        return false;    
    }
    if($("#dt_ini_teto").val()==''){
        sweetMensagem('warning', "Por favor, selecione o campo Dt Ini Validade Teto!");
        return false;    
    }
    if($("#dt_fim_teto").val()==''){
        sweetMensagem('warning', "Por favor, selecione o campo Dt fim Validade Teto!");
        return false;    
    }
    if($("#vl_teto_anual").val()==''){
        sweetMensagem('warning', "Por favor, selecione o campo Vl Teto Anual!");
        return false;    
    }
    fcEnviarItens();
}

function fcEnviarItens(){
    try{
        var objParametros = {
            "ic_status": $("#ic_status").val(),    
            "teto_gastos_pk": $("#pk").val(),  
            "tipos_operacao_pk":  $("#tipos_operacao_pk").val(),
            "categoria_operacao_pk":  $("#categoria_operacao_pk").val(),
            "operacao_pk": $("#operacao_pk").val(),
            "dt_ini_teto": $("#dt_ini_teto").val(),
            "dt_fim_teto":  $("#dt_fim_teto").val(),
            "vl_teto_anual":  moeda2float($("#vl_teto_anual").val()),
            "vl_teto_mensal": moeda2float($("#vl_teto_mensal").val()),
            "obs": $("#obs_teto_itens").val()    
        };    
    
        var arrEnviar = carregarController("teto_gasto_item", "salvar", objParametros);  
        
        if (arrEnviar.status == true){
            utilsJS.toastNotify(true, arrEnviar.message); 
            tblResultadoItens.destroy();
            fcCarregarGrid();
            $("#tipos_operacao_pk").val("")
            $("#categoria_operacao_pk").val("")
            $("#operacao_pk").val("")
            $("#dt_ini_teto").val("")
            $("#dt_fim_teto").val("")
            $("#vl_teto_anual").val("")
            $("#vl_teto_mensal").val("")
            $("#obs_teto_itens").val("")
            
        }
        else{
            utilsJS.toastNotify(false, arrEnviar.message);
        }
    }catch(e){
        utilsJS.toastNotify(e, arrEnviar.message);
    }
   
}

function fcCarregarGrid(){
    var objParametros = {
        "teto_gastos_pk": $("#pk").val()
    };

    var v_url = routes_api("teto_gasto_item", "listarGrid", objParametros);
    //NewWindow(v_last_url)
    //Trata a tabela
    tblResultadoItens = $('#tblResultadoItens').DataTable({
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
                    return full['t_pk'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_tipos_operacao'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_tipos_operacao_pk'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_categoria_operacao_pk'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_ini_teto'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_fim_teto'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_vl_teto_anual'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['t_vl_teto_mensal'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    var buttonDelete = '<a class="function_delete_item"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';
                

                    return buttonDelete;
                },
                'orderable': false,
                'searchable': false,
            }
        ]
    });
    //Atribui os eventos na coluna ação.
    $('#tblResultadoItens tbody').on('click', '.function_edit', function () {
        var data;
        if(tblResultadoItens.row( $(this).parents('li')).data()){
            data = tblResultadoItens.row( $(this).parents('li')).data();
        }
        else if(tblResultadoItens.row( $(this).parents('tr')).data()){
            data = tblResultadoItens.row( $(this).parents('tr')).data();
        }
        fcEditar(data['t_pk']);

    } );

    $('#tblResultadoItens tbody').on('click', '.function_delete_item', function () {
        var data;
        if(tblResultadoItens.row( $(this).parents('li') ).data()){
            data = tblResultadoItens.row( $(this).parents('li') ).data();
        }
        else if(tblResultadoItens.row( $(this).parents('tr') ).data()){
            data = tblResultadoItens.row( $(this).parents('tr') ).data();
        }
        fcExcluirItem(data['t_pk']);
    } );
}

function fcExcluirItem(v_pk){
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("teto_gasto_item", "excluir", objParametros);

            if (arrExcluir.status == true){

                //Exibe a mensagem
                
                utilsJS.toastNotify(true, arrExcluir.message);

                // Reload datable
                tblResultadoItens.ajax.reload();


            }
            else{
                utilsJS.toastNotify(false, "Falhou a requisição de exclusão.");
            }
        }
        else{
            utilsJS.toastNotify(false, "Código não encontrado");
        }
    });
}


function fcCancelar(){
    var objParametros = {};
    sendPost('teto_gasto','receptivo' ,objParametros);
}

function fcCarregar(){
    if($("#pk").val() > 0){

        $("#informacoesItens").show();
        

        var objParametros = {
            "pk": $("#pk").val()
        };  
        
        var arrCarregar = carregarController("teto_gasto", "listarPk", objParametros);

        if (arrCarregar.status == true){
            $("#div_clientes").hide();
            $("#div_colaborador").hide();
            $("#div_fornecedor").hide();
        
            var tipo_grupo_pk = arrCarregar.data[0]['tipo_grupo_pk']
            $("#tipo_grupo_pk").val(tipo_grupo_pk);
            
            if(tipo_grupo_pk=='1'){        
                $("#div_clientes").show();
                fccarregarLeadsClientes();
                $("#leads_clientes_pk").val(arrCarregar.data[0]['grupo_leancamento_pk']);
                if(arrCarregar.data[0]['leads_posto_trabalho_pk'] != null){
                    fccarregarLeadsPostosTrabalho();
                    $("#leads_posto_trabalho_pk").val(arrCarregar.data[0]['leads_posto_trabalho_pk']);
                }
                if(arrCarregar.data[0]['contratos_pk'] != null){
                    fccarregarLeadsContratos();
                    $("#leads_contratos_pk").val(arrCarregar.data[0]['contratos_pk']);
                }
            }else if(tipo_grupo_pk=='2'){
                
                $("#div_colaborador").show();
                fccarregarColaborador()
                $("#colaborador_pk").val(arrCarregar.data[0]['grupo_leancamento_pk']);
                
                if(arrCarregar.data[0]['grupo_lancamento_centro_custo_pk'] != null){
                    
                    fccarregarLeadsClientesCentroCusto()
                    $("#grupo_lancamento_centro_custo_colaborador_pk").val(arrCarregar.data[0]['grupo_lancamento_centro_custo_pk']);
                }
                if(arrCarregar.data[0]['colaborador_posto_trabalho_pk'] != null){
                    fccarregarColaboradorPostosTrabalho();
                    $("#colaborador_posto_trabalho_pk").val(arrCarregar.data[0]['colaborador_posto_trabalho_pk']);
                }
                if(arrCarregar.data[0]['colaborador_contratos_pk'] != null){
                    fccarregarColaboradorContratos()
                    $("#colaborador_contratos_pk").val(arrCarregar.data[0]['colaborador_contratos_pk']);
                }
            }else if(tipo_grupo_pk=='3'){
                $("#div_fornecedor").show();
                fccarregarFornecedor();
                $("#fornecedor_pk").val(arrCarregar.data[0]['grupo_leancamento_pk']);
                if(arrCarregar.data[0]['grupo_lancamento_centro_custo_pk'] != null){
                    fccarregarLeadsClientesCentroCustoForncedor();
                    $("#grupo_lancamento_centro_custo_fornecedor_pk").val(arrCarregar.data[0]['grupo_lancamento_centro_custo_pk']);
                }
                if(arrCarregar.data[0]['leads_posto_trabalho_pk'] != null){
                    fccarregarFornecedorPostosTrabalho();
                    $("#fornecedor_posto_trabalho_pk").val(arrCarregar.data[0]['leads_posto_trabalho_pk']);
                }
                if(arrCarregar.data[0]['fornecedor_contratos_pk'] != null){
                    fccarregarFornecedorContratos();
                    $("#fornecedor_contratos_pk").val(arrCarregar.data[0]['fornecedor_contratos_pk']);
                }
            }

            $("#ds_ano_vigente_teto").val(arrCarregar.data[0]['ds_ano_vigente_teto']);
            $("#vl_total_teto").val(arrCarregar.data[0]['vl_total_teto']);
            $("#vl_utilizado_atual").val(arrCarregar.data[0]['vl_utilizado_atual']);
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);
            $("#obs").val(arrCarregar.data[0]['obs']);
            fcCarregarGrid();

        }
        else{
            utilsJS.toastNotify(false, "Falhar ao carregar o registro");
        }
    }
}

function fccarregarLeadsClientes() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#leads_clientes_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarLeadsPostosTrabalho() {

    var objParametros = {
        "leads_pk": $("#grupo_lancamento_pk").val()
    };
    var arrCarregar = carregarController("lead", "listaLeadsPostosTrabalho", objParametros);
    carregarComboAjax($("#posto_trabalho_lancamento_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fccarregarLeadsContratos() {
    var objParametros = {
        "leads_pk": $("#leads_posto_trabalho_pk").val()
    };
    var arrCarregar = carregarController("contrato", "listaLeadContratos", objParametros);
    carregarComboAjax($("#leads_contratos_pk"), arrCarregar, " ", "pk", "ds_contrato");
}

//combo colaborador
function fccarregarColaboradorContratos() {
    var objParametros = {
        "leads_pk": $("#colaborador_posto_trabalho_pk").val(),
        "colaborador_pk": $("#colaborador_pk").val()
    };
    var arrCarregar = carregarController("contrato", "listaColaboradorContratos", objParametros);
    carregarComboAjax($("#colaborador_contratos_pk"), arrCarregar, " ", "pk", "ds_contrato");
}

function fccarregarColaborador() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);
    carregarComboAjax($("#colaborador_pk"), arrCarregar, " ", "pk", "ds_colaborador");
}

function fccarregarLeadsClientesCentroCusto() {
    var objParametros = {
        "colaborador_pk": $("#colaborador_pk").val()
    };
    var arrCarregar = carregarController("lead", "listarClienteColaborador", objParametros);
    carregarComboAjax($("#grupo_lancamento_centro_custo_colaborador_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fccarregarColaboradorPostosTrabalho() {

    var objParametros = {
        "colaborador_pk": $("#colaborador_modal_pk").val(),
        "leads_pk": $("#grupo_lancamento_centro_custo_colaborador_pk").val()
    };
    var arrCarregar = carregarController("lead", "listaColaboradorPostosTrabalho", objParametros);
    carregarComboAjax($("#colaborador_posto_trabalho_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fccarregarFornecedor() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("fornecedor", "listarTodos", objParametros);
    carregarComboAjax($("#fornecedor_pk"), arrCarregar, " ", "pk", "ds_fornecedor");
}

function fccarregarLeadsClientesCentroCustoForncedor() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#grupo_lancamento_centro_custo_fornecedor_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fccarregarFornecedorPostosTrabalho() {
    var objParametros = {
        "leads_pk": $("#grupo_lancamento_centro_custo_fornecedor_pk").val()
    };
    var arrCarregar = carregarController("lead", "listaFornecedorPostosTrabalho", objParametros);
    carregarComboAjax($("#fornecedor_posto_trabalho_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fccarregarFornecedorContratos() {
    var objParametros = {
        "leads_pk": $("#posto_trabalho_pk").val()
    };
    var arrCarregar = carregarController("contrato", "listaLeadContratos", objParametros);
    carregarComboAjax($("#contratos_pk"), arrCarregar, " ", "pk", "ds_contrato");
}

function fccarregarCategoriaoperacao() {
    var objParametros = {

    };
    var arrCarregar = carregarController("categoria_financeira", "listarTodos", objParametros);
    carregarComboAjax($("#categoria_operacao_pk"), arrCarregar, " ", "pk", "ds_categoria");
}

function fccarregarTipoPlanoNegocio() {
    var objParametros = {
        "categorias_financeiras_pk": $("#categoria_operacao_pk").val()
    };
    var arrCarregar = carregarController("plano_contas", "listaPorCategoria", objParametros);
    carregarComboAjax($("#operacao_pk"), arrCarregar, " ", "pk", "ds_tipo_operacao");
}

function fcSelecionaGrupo() {

    if ($("#tipo_grupo_pk").val() == 1) {
        fccarregarLeadsClientes();
        $("#div_clientes").show();
        $("#div_colaborador").hide();
        $("#div_fornecedor").hide();
    } else if ($("#tipo_grupo_pk").val() == 2) {
        fccarregarColaborador();
        $("#div_clientes").hide();
        $("#div_colaborador").show();
        $("#div_fornecedor").hide();
    } else if ($("#tipo_grupo_pk").val() == 3) {
        fccarregarFornecedor();
        fccarregarLeadsClientesCentroCustoForncedor();
        $("#div_clientes").hide();
        $("#div_colaborador").hide();
        $("#div_fornecedor").show();
    } else if ($("#tipo_grupo_pk").val() == '') {
        $("#div_clientes").hide();
        $("#div_colaborador").hide();
        $("#div_fornecedor").hide();
    }
}

/*function fcExcluir(v_pk, v_tipo_origem_teto_gastos){

    if (confirm("Deseja realmente excluir o registro '" + v_tipo_origem_teto_gastos + "'?")){
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("teto_gasto", "excluir", objParametros);

            if (arrExcluir.result == 'success'){

                //Exibe a mensagem
                alert(arrExcluir.message);

                // Reload datable
                tblResultado.ajax.reload();

            }
            else{
                alert('Falhou a requisição de exclusão.');
            }
        }
        else{
            alert("Código não encontrado");
        }
    }
}*/

$(document).ready(function(){

        $('#dt_ini_teto').datepicker({
            startDate: 0,
            defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked"
        });
    
        $("#dt_ini_teto").keypress(function () {
            mascara(this, mdata);
        });

        $('#dt_fim_teto').datepicker({
            startDate: 0,
            defaultDate: "getDate()",
            dateFormat: 'dd/mm/yyyy',
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true,
            todayBtn: "linked"
        });
    
        $("#dt_fim_teto").keypress(function () {
            mascara(this, mdata);
        });

        $("#vl_teto_anual").keypress(function () {
            mascara(this, moeda);
        });

        $("#vl_teto_mensal").keypress(function () {
            mascara(this, moeda);
        });

        $("#vl_total_teto").keypress(function () {
            mascara(this, moeda);
        });

        $("#vl_utilizado_atual").keypress(function () {
            mascara(this, moeda);
        });
        
        fccarregarCategoriaoperacao()
        $("#categoria_operacao_pk").change(function () {
            $(".chzn-select").chosen('destroy');
            fccarregarTipoPlanoNegocio();
        });

        $("#tipo_grupo_pk").change(function () {
            $(".chzn-select").chosen('destroy');
            fcSelecionaGrupo();
        });

        //Carrega Combos 
        $("#leads_clientes_pk").change(function () {
            $(".chzn-select").chosen('destroy');
            fccarregarLeadsPostosTrabalho();
        });
    
        $("#leads_posto_trabalho_pk").change(function () {
            $(".chzn-select").chosen('destroy');
            fccarregarLeadsContratos();
        });

        //Selecionar tipo de origem para teto
        $("#tipo_origem_teto_gastos").change(function () {
            fcCarrgaComboOrigem()
        });

        
        $("#colaborador_pk").change(function () {
            $(".chzn-select").chosen('destroy');
            fccarregarLeadsClientesCentroCusto();
            fccarregarColaboradorPostosTrabalho();
        });

        $("#grupo_lancamento_centro_custo_colaborador_pk").change(function () {
            $(".chzn-select").chosen('destroy');
            fccarregarColaboradorPostosTrabalho();
        });

        $("#colaborador_posto_trabalho_pk").change(function () {
            $(".chzn-select").chosen('destroy');
            fccarregarColaboradorContratos()
        });

        $("#grupo_lancamento_centro_custo_fornecedor_pk").change(function () {
            $(".chzn-select").chosen('destroy');
            fccarregarFornecedorPostosTrabalho();
        });

        $("#fornecedor_posto_trabalho_pk").change(function () {
            $(".chzn-select").chosen('destroy');
            fccarregarFornecedorContratos()
        });
        
        //Atribui os eventos
        $(document).on('click', '#cmdEnviar', fcEnviar);
        $(document).on('click', '#cmdCancelar', fcCancelar);
        $(document).on('click', '#cmdEnviarTetoGastos', fcValidarForm);
        $(document).on('click', '#cmdIncluirItem', fcValidarItensForm);
        
        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();
});
