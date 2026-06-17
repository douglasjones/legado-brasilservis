var IntContratosPk = "0";
function fcPesquisar() {

    tblResultado.clear().destroy();
    fcCarregarGridConjuntoMateriais();

}

function fcVoltar(){
    var objParametros = {};
    sendPost('menu','compra_estoque' ,objParametros);
}

function fcCarregarCategorias() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros);
    carregarComboAjax($("#categoria_res_pk"), arrCarregar, " ", "pk", "ds_categoria");
    carregarComboAjax($("#categorias_produto_pk"), arrCarregar, " ", "pk", "ds_categoria");
}

function fcCarregarProdutos(categorias_produto_pk) {
    var objParametros = {
        "categorias_produto_pk": categorias_produto_pk
    };
    var arrCarregar = carregarController("produto", "listarPorCategoria", objParametros);

    carregarComboAjax($("#produtos_res_pk"), arrCarregar, " ", "pk", "ds_produto");
    carregarComboAjax($("#produtos_pk"), arrCarregar, " ", "pk", "ds_produto");
}


function fcVerificarMovimentadoParaIns() {
    $("#str_opc_ins").text("");
    if ($("#grupo_para_movimentacao_ins_pk").val() == 1) {

        $("#str_opc_ins").text("Colaborador(es)");
        var objParametros = {
            "pk": ""
        };
        var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);
        carregarComboAjax($("#movimentar_para_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    }
    else if ($("#grupo_para_movimentacao_ins_pk").val() == 2) {
        $("#str_opc_ins").text("Posto(s) de Trabalho");
        var objParametros = {
            "pk": ""
        };
        var arrCarregar = carregarController("lead", "listarTodos", objParametros);
        carregarComboAjax($("#movimentar_para_pk"), arrCarregar, " ", "pk", "ds_lead");
    }

}

function fcVerificarMovimentadoPara() {
    $("#str_opc").text("");
    if ($("#grupo_para_movimentacao_pk").val() == 1) {

        $("#str_opc").text("Colaborador(es)");
        var objParametros = {
            "pk": ""
        };
        var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);
        carregarComboAjax($("#movimentar_para_pesq_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    }
    else if ($("#grupo_para_movimentacao_pk").val() == 2) {
        $("#str_opc").text("Posto(s) de Trabalho");
        var objParametros = {
            "pk": ""
        };
        var arrCarregar = carregarController("lead", "listarTodos", objParametros);
        carregarComboAjax($("#movimentar_para_pesq_pk"), arrCarregar, " ", "pk", "ds_lead");
    }

}



function pegarPkProdutosItensNotIn(produtos_itens_pk_res) {
    try {
        var produtos_itens_pk = "";

        var arrKeys = [];
        var arrDados = [];
        arrKeys[0] = "produtos_itens_pk";

        var data = tblResultado.rows().data();

        for (i = 0; i < data.length; i++) {
            if (produtos_itens_pk_res != data[i]['produtos_itens_pk']) {
                produtos_itens_pk = data[i]['produtos_itens_pk'];
                arrDados[i] = [produtos_itens_pk];
            }


        }
        return arrayToJson(arrKeys, arrDados);
    }
    catch (err) {
        utilsJS.toastNotify(false, err);
    }
}
function fcCarregarProdutosItens(produtos_pk, produtos_itens_pk) {

    var colaborador_pk = "";
    var leads_pk = "";

    if ($("#grupo_para_movimentacao_pk").val() == 1) {
        var colaborador_pk = $("#movimentar_para_pesq_pk").val();
    }
    else if ($("#grupo_para_movimentacao_pk").val() == 2) {
        var leads_pk = $("#movimentar_para_pesq_pk").val();
    }

    var objParametros = {
        "produtos_pk": produtos_pk,
        "leads_pk": leads_pk,
        "colaborador_pk": colaborador_pk,
        "produtos_itens_pk": produtos_itens_pk
    };

    var arrCarregar = carregarController("produto_item", "listarPorPkProdutoNotIn", objParametros);

    carregarComboAjax($("#produtos_itens_pk"), arrCarregar, " ", "pk", "ds_produto_item");

    $("#count_material").val(arrCarregar.data.length);
}



var tblMaterial;
function fcCarregarGridConjuntoMateriais() {

    var v_colaborador_pk = "";
    var v_leads_pk = "";
    var contratos_pk = "";

    if ($("#contratos_pk").val() == undefined) {
        var IntContratosPk = "";
    }
    else {
        if ($("#contratos_pk").val() != "") {
            var IntContratosPk = $("#contratos_pk").val();
        }
        else {
            var IntContratosPk = "0";
        }

    }
    if ($("#grupo_para_movimentacao_pk").val() == 1) {
        var v_colaborador_pk = $("#colaborador_pk").val();
    }
    else if ($("#grupo_para_movimentacao_pk").val() == 2) {
        var v_leads_pk = $("#leads_pk").val();
        if (IntContratosPk != "") {
            contratos_pk = IntContratosPk;
        }
    }
    var objParametros = {
        "leads_pk": v_leads_pk,
        "colaborador_pk": v_colaborador_pk,
        "contratos_pk": contratos_pk,
        "categoria_pk": $("#categoria_res_pk").val(),
        "produtos_pk": $("#produtos_res_pk").val(),
        "grupo_para_movimentacao_pk": $("#grupo_para_movimentacao_pk").val(),
        "dt_movimentacao_ini": $("#dt_movimentacao_ini").val(),
        "dt_movimentacao_fim": $("#dt_movimentacao_fim").val()
    };

    var v_url = routes_api("conjunto_material", "listarMovimentarMaterialProd", objParametros);

    tblResultado = $("#tblResultado").DataTable({
        searching: false,
        paging: true,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
        iDisplayLength: 10,
        processing: true,
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
                    return full['conjunto_material_pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_grupo_movimentado'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_movimentado'];
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
                    return full['qtde'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_painel"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="editar"></i></span></a> &nbsp;';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-printer" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';
                    return buttonPainel + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]

    });

    //Atribui os eventos na coluna ação.
    $('#tblResultado tbody').on('click', '.function_painel', function () {
        var data;

        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }

        fcEditarConjuntoMaterialProd(data);

    });
    //Atribui os eventos na coluna ação.
    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;

        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        if (data['colaborador_pk'] != null) {
            fcImprimirConjuntoMaterial(data);
        }
        else {
            sweetMensagem('warning', 'Posto de trabalho não gera impressão!');

        }

    });


    return false;
}

function fcImprimirConjuntoMaterial(objRegistro) {
    if ($("#colaborador_pk").val() != "") {
        var objParametros = {
            "pk": $("#colaborador_pk").val(),
            "leads_pk": objRegistro['leads_pk'],
            "conjunto_material_pk": objRegistro['conjunto_material_pk'],
            "local": ""
        };
        sendPost('impressao_material','abrirImpressao' ,objParametros);
    }
    else {
        var objParametros = {
            "pk": objRegistro['colaborador_pk'],
            "leads_pk": objRegistro['leads_pk'],
            "conjunto_material_pk": objRegistro['conjunto_material_pk'],
            "local": 2
        };
        sendPost('impressao_material','abrirImpressao' ,objParametros);
    }

}

function fcCarregarGridMateriais() {
    var colaborador_pk = "";
    var leads_pk = "";
    var contratos_pk = "";

    if ($("#contratos_pk").val() == undefined) {
        var IntContratosPk = "";
    }
    else {
        if ($("#contratos_pk").val() != "") {
            var IntContratosPk = $("#contratos_pk").val();
        }
        else {
            var IntContratosPk = "0";
        }

    }

    if ($("#grupo_para_movimentacao_ins_pk").val() == 1) {
        var colaborador_pk = $("#movimentar_para_pk").val();
    }
    else if ($("#grupo_para_movimentacao_ins_pk").val() == 2) {
        var leads_pk = $("#movimentar_para_pk").val();
        if (IntContratosPk != "") {
            contratos_pk = IntContratosPk;
        }
    }

    if($("#movimentar_para_pk").val()!=""){

        var objParametros = {
            "leads_pk": leads_pk,
            "colaborador_pk": colaborador_pk,
            "contratos_pk": contratos_pk,
            "conjunto_material_pk": $("#conjunto_material_pk").val()
        };

        var v_url = routes_api("movimentacao_estoque", "listar_por_pk_conjunto", objParametros);
        tblMaterial = $("#tblMaterial").DataTable({
            searching: true,
            paging: true,
            pageLength: 10,
            aLengthMenu: [10, 25, 50, 100],
            iDisplayLength: 10,
            processing: true,
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
                        return full['ds_categorias_produto'];
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

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_produto_item'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['dt_entrega'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['dt_devolucao'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['obs_material'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_ic_mateiral_carga'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        //var buttonPainel = '<a  class="btn btn-sm" onclick="fcEditarContato('+full+')" style="margin-right: 12px;"><i class="fa fa-pencil-alt"></i></a>&nbsp;';
                        var buttonDelete = '<a  class="btn btn-sm" onclick="fcExcluirMaterial('+full['pk']+')"  style="margin-right: 12px;font-size:18px;color:blue"><i class="bi bi-x-circle"></i></a>&nbsp;';
                        return buttonDelete;
                    },
                    'orderable': false,
                    'searchable': false,
                    width: '80px'
                }
            ]

        });
    }
    else if($("#leads_pk").val()=="" || $("#colaborador_pk").val()==""){

        tblMaterial = $('#tblMaterial').DataTable( {
            responsive: true,
        });

        //Atribui os eventos na coluna ação.
        $('#tblMaterial tbody').on('click', '.function_edit', function (e) {

            e.preventDefault();
            let element = $(this);
            $("#categorias_produto_pk").val(element.parents('tr').find("td:nth-child(2) input").val());
            $("#produtos_pk").val(element.parents('tr').find("td:nth-child(3) input").val());
            $("#produtos_itens_pk").val(element.parents('tr').find("td:nth-child(4) input").val());
            $("#dt_entrega").val(element.parents('tr').find("td:nth-child(5) input").val());
            $("#dt_devolucao").val(element.parents('tr').find("td:nth-child(6) input").val());
            $("#obs_material").val(element.parents('tr').find("td:nth-child(7) input").val());
            $("#ic_mateiral_carga").val(element.parents('tr').find("td:nth-child(8) input").val());

            tblMaterial.row($(this).parents('tr')).remove().draw();

        } );

        $('#tblMaterial tbody').on('click', '.function_delete', function () {
            tblMaterial.row($(this).parents('tr')).remove().draw();
        } );
    }
    else{
        var objParametros = {
            "leads_pk": leads_pk,
            "colaborador_pk": colaborador_pk,
            "contratos_pk": contratos_pk,
            "conjunto_material_pk": $("#conjunto_material_pk").val()
        };

        var v_url = routes_api("movimentacao_estoque", "listar_por_pk_conjunto", objParametros);
        tblMaterial = $("#tblMaterial").DataTable({
            searching: true,
            paging: true,
            pageLength: 10,
            aLengthMenu: [10, 25, 50, 100],
            iDisplayLength: 10,
            processing: true,
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
                        return full['ds_categorias_produto'];
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

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_produto_item'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['dt_entrega'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['dt_devolucao'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['obs_material'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_ic_mateiral_carga'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        //var buttonPainel = '<a  class="btn btn-sm" onclick="fcEditarContato('+full+')" style="margin-right: 12px;"><i class="fa fa-pencil-alt"></i></a>&nbsp;';
                        var buttonDelete = '<a  class="btn btn-sm" onclick="fcExcluirMaterial('+full['pk']+')"  style="margin-right: 12px;font-size:18px;color:blue"><i class="bi bi-x-circle"></i></a>&nbsp;';
                        return  buttonDelete;
                    },
                    'orderable': false,
                    'searchable': false,
                    width: '80px'
                }
            ]

        });
    }
}

function fcExcluirMaterial(v_pk) {

    if (v_pk != "") {
        var objParametros = {
            "pk": v_pk
        };

        var arrExcluir = carregarController("movimentacao_estoque", "excluir", objParametros);

        if (arrExcluir.status == true) {

            //Exibe a mensagem
            utilsJS.toastNotify(true, arrExcluir.message);
            fcRecarregarGridMateriais();

            tblResultado.ajax.reload();
        }
        else {
            utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
        }
    }
    else {
        utilsJS.toastNotify(false, "Código não encontrado");
    }
}
function fcEditarMaterial(objRegistro) {


    fcLimparFormMaterial();

    $(".chzn-select").chosen('destroy');

    fcCarregarProdutosItens(objRegistro['produtos_pk'], objRegistro['produtos_itens_pk']);


    $("#div_dt_devolucao").show();
    $("#movimentacao_estoque_pk").val("");
    $("#acao").val("upd");

    //Carrega as informações da linha selecionada.
    $("#movimentacao_estoque_pk").val(objRegistro['pk']);
    $("#categorias_produto_pk").val(objRegistro['categorias_produto_pk']);
    $("#produtos_pk").val(objRegistro['produtos_pk']);
    $("#produtos_itens_pk").val(objRegistro['produtos_itens_pk']);
    $("#dt_entrega").val(objRegistro['dt_entrega']);
    $("#dt_devolucao").val(objRegistro['dt_devolucao']);
    $("#observacao_material").val(objRegistro['obs_material']);
    $("input[id=ic_mateiral_carga]").prop("checked", false);
    if (objRegistro['ic_mateiral_carga'] == 1) {
        $("input[id=ic_mateiral_carga]").prop("checked", "true");
    }
    else {
        $("input[id=ic_mateiral_carga]").prop("checked", false);
    }
    $("#qtde_materias").val(1);
    $("#produtos_itens_pk").prop('disabled', false);
    $("#qtde_materias").prop('disabled', true);


    $(".chzn-select").chosen({ allow_single_deselect: true });

}

function fcEditarConjuntoMaterialProd(objRegistro) {

    $(".chzn-select").chosen('destroy');
    if ($('#janela_materiais').is(':visible') == false) {

        $(".chzn-select").chosen('destroy');


        $("#ds_conjunto_material").val("");
        $("#grupo_para_movimentacao_ins_pk").val("");
        $("#movimentar_para_pk").val("");
        $("#str_opc_ins").text("");
        $("#conjunto_material_pk").val("");
        $("#contratos_pk").val("");
        $("#janela_materiais").modal('show');

        $("#movimentar_para_pk").prop('disabled', false);
        $("#grupo_para_movimentacao_ins_pk").prop('disabled', false);

        $("#grupo_para_movimentacao_ins_pk").val("");
        $("#movimentar_para_pk").val("");

        $("#grupo_para_movimentacao_ins_pk").val(objRegistro['grupo_para_movimentacao_pk']);

        fcVerificarMovimentadoParaIns();

        if ($("#grupo_para_movimentacao_ins_pk").val() == 1) {
            $("#movimentar_para_pk").val(objRegistro['colaborador_pk']);
        }
        else if ($("#grupo_para_movimentacao_ins_pk").val() == 2) {
            $("#movimentar_para_pk").val(objRegistro['leads_pk']);
        }

        $("#ds_conjunto_material").val("");
        $("#conjunto_material_pk").val("");
        $("#movimentacao_estoque_pk").val("");
        $("#contratos_pk").val(objRegistro['contratos_pk']);


        $("#movimentar_para_pk").prop('disabled', true);
        $("#grupo_para_movimentacao_ins_pk").prop('disabled', true);

        $("#categorias_produto_pk").val("");
        $("#produtos_pk").val("");
        $("#produtos_itens_pk").val("");
        $("#dt_entrega").val("");
        $("#dt_devolucao").val("");
        $("#observacao_material").val("");
        $("#conjunto_material_pk").val(objRegistro['conjunto_material_pk']);
        $("#ds_conjunto_material").val(objRegistro['ds_conjunto_material']);

        tblMaterial.clear().destroy();
        fcCarregarGridMateriais();

        setTimeout(function () { $(".chzn-select").chosen({ allow_single_deselect: true }); }, 500);
    }
    $(".chzn-select").chosen('destroy');

}

function fcValidarFormModalMateriais() {
    $("#form_materiais").validate({
        rules: {
            ds_conjunto_material: {
                required: true
            }
        },
        messages: {
            ds_conjunto_material: {
                required: "Por favor, informe Descrição Conjunto Material"
            }
        },
        submitHandler: function (form) {
            fcEnviarConjuntoMateriais(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });
}

function fcEnviarConjuntoMateriais() {
    fcSalvarConjuntoMateriais();
}

function fcValidarMaterial() {


    if ($("#grupo_para_movimentacao_ins_pk").val() == "") {
        $("#alert_grupo_para_movimentacao").show();
        $("#alert_grupo_para_movimentacao").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_grupo_para_movimentacao").slideUp(500);
        });
        return false;
    }
    if ($("#grupo_para_movimentacao_ins_pk").val() == 1) {
        if ($("#movimentar_para_pk").val() == "") {
            $("#alert_movimentar_colaborador").show();
            $("#alert_movimentar_colaborador").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_movimentar_colaborador").slideUp(500);
            });
            return false;
        }
    }
    if ($("#grupo_para_movimentacao_ins_pk").val() == 2) {
        if ($("#movimentar_para_pk").val() == "") {
            $("#alert_movimentar_lead").show();
            $("#alert_movimentar_lead").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_movimentar_lead").slideUp(500);
            });
            return false;
        }
    }

    if ($('#categorias_produto_pk').val() == "") {
        $("#alert_categoria").show();
        $("#alert_categoria").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_categoria").slideUp(500);
        });
        $('#categorias_produto_pk').focus();
        return false;
    }
    if ($('#produtos_pk').val() == "") {
        $("#alert_produto").show();
        $("#alert_produto").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_produto").slideUp(500);
        });
        $('#produtos_pk').focus();
        return false;
    }
    if ($("#qtde_materias").val() == "" || $("#qtde_materias").val() == 0) {
        if ($('#produtos_itens_pk').val() == "") {
            $("#alert_produtos_itens_pk").show();
            $("#alert_produtos_itens_pk").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_produtos_itens_pk").slideUp(500);
            });
            $('#produtos_itens_pk').focus();
            return false;
        }
    }
    if ($('#dt_entrega').val() == "") {
        $("#alert_dt_entrega").show();
        $("#alert_dt_entrega").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_dt_entrega").slideUp(500);
        });
        $('#dt_entrega').focus();
        return false;
    }

    if ($("#qtde_materias").val() > 0) {
        if (parseInt($("#count_material").val()) < parseInt($("#qtde_materias").val())) {
            sweetMensagem('warning', "Quantidade disponível em estoque inferior. Quantidade disponivel " + $("#count_material").val());
            return false;
        }
    }


    return true;
}
function fcIncluirMateriais() {

    if ($("#conjunto_material_pk").val() == "") {

        if ($("#acao").val() == "ins") {

            if (fcValidarMaterial()) {
                fcIncluirMateriaisSemPk();
                fcLimparFormMaterial();
            }
        }
        else if ($("#acao").val() == "upd") {

            if (fcValidarMaterial()) {
                fcEditarMateriaisSemPk();
                fcLimparFormMaterial();
            }
        }
    }
    else {
        if (fcValidarMaterial()) {
            fcSalvarMateriais();
        }

    }



}

function fcSalvarMateriais() {

    var v_movimentacao_estoque_pk = $("#movimentacao_estoque_pk").val();
    var v_produtos_itens_pk = $("#produtos_itens_pk").val();
    var v_dt_entrega = $("#dt_entrega").val();
    if ($("#movimentacao_estoque_pk").val() != "") {
        var v_dt_devolucao = $("#dt_devolucao").val();
    } else {
        var v_dt_devolucao = "";
    }
    var v_obs_material = $("#observacao_material").val();



    if ($("#ic_mateiral_carga").is(":checked") == true) {
        var ds_ic_mateiral_carga = "Sim";
        var ic_mateiral_carga = 1;
    }
    else {
        var ds_ic_mateiral_carga = "Não";
        var ic_mateiral_carga = 2;
    }


    var colaborador_pk = "";
    var leads_pk = "";
    var contratos_pk = "";


    if ($("#contratos_pk").val() == undefined) {
        var IntContratosPk = "";
    }
    else {
        if ($("#contratos_pk").val() != "") {
            var IntContratosPk = $("#contratos_pk").val();
        }
        else {
            var IntContratosPk = "0";
        }

    }

    if ($("#grupo_para_movimentacao_ins_pk").val() == 1) {
        var colaborador_pk = $("#movimentar_para_pk").val();
    }
    else if ($("#grupo_para_movimentacao_ins_pk").val() == 2) {
        var leads_pk = $("#movimentar_para_pk").val();
        if (IntContratosPk != "") {
            contratos_pk = IntContratosPk;
        }
    }

    if ($("#qtde_materias").val() > 0) {

        var data = tblMaterial.rows().data();
        var strProdutoGrid = "";
        if (data.length > 0) {
            strProdutoGrid += "not in ("
            for (i = 0; i < data.length; i++) {
                strProdutoGrid += data[i]['produtos_itens_pk'] + ",";

            }
            strProdutoGrid += "0)";
        }

        var objParametros1 = {
            "produtos_pk": $("#produtos_pk").val(),
            "qtde": $("#qtde_materias").val(),
            "strProdutoGrid": strProdutoGrid
        };
        var arrCarregar1 = carregarController("produto_item", "listarPorProdutosQtde", objParametros1);

        if (arrCarregar1.data.length > 0) {
            if ($("#qtde_materias").val() > arrCarregar1.data.length) {

                sweetMensagem('warning', "Só existem " + arrCarregar1.data.length + " unidades desse produto.");
                $("#janela_materiais").modal("show");
                return false;
            }
            for (i = 0; i < arrCarregar1.data.length; i++) {
                var objParametros = {
                    "pk": v_movimentacao_estoque_pk,
                    "produtos_itens_pk": arrCarregar1.data[i]['pk'],
                    "conjunto_material_pk": $("#conjunto_material_pk").val(),
                    "dt_entrega": v_dt_entrega,
                    "dt_devolucao": v_dt_devolucao,
                    "obs_material": (v_obs_material),
                    "ic_mateiral_carga": (ic_mateiral_carga),
                    "leads_pk": leads_pk,
                    "contratos_pk": contratos_pk,
                    "colaborador_pk": colaborador_pk
                };

                var arrEnviar = carregarController("movimentacao_estoque", "salvar", objParametros);

                if (arrEnviar.status == true) {
                    // Reload datable
                    tblResultado.ajax.reload();

                }
                else {
                    utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
                }
            }
        }
    }
    else {
        var objParametros = {
            "pk": v_movimentacao_estoque_pk,
            "produtos_itens_pk": v_produtos_itens_pk,
            "conjunto_material_pk": $("#conjunto_material_pk").val(),

            "dt_entrega": v_dt_entrega,
            "dt_devolucao": v_dt_devolucao,
            "ic_mateiral_carga": ic_mateiral_carga,
            "obs_material": (v_obs_material),
            "leads_pk": leads_pk,
            "contratos_pk": contratos_pk,
            "colaborador_pk": colaborador_pk
        };

        var arrEnviar = carregarController("movimentacao_estoque", "salvar", objParametros);

        if (arrEnviar.status == true) {
            // Reload datable
        }
        else {

            utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
        }
    }

    utilsJS.toastNotify(true, 'Registro salvo com sucesso.');
    fcLimparFormMaterial();


    fcRecarregarGridMateriais();



}

function fcEditarMateriaisSemPk() {
    fcIncluirMateriaisSemPk();
    tblMaterial.row(rLinhaSelecionadaMaterial).remove().draw();
    return false;
}

function fcIncluirMateriaisSemPk() {

    if ($("#ic_mateiral_carga").is(":checked") == true) {
        var ds_ic_mateiral_carga = "Sim";
        var ic_mateiral_carga = 1;
    }
    else {
        var ds_ic_mateiral_carga = "Não";
        var ic_mateiral_carga = 2;
    }


    if ($("#qtde_materias").val() > 0) {

        var data = tblMaterial.rows().data();
        var strProdutoGrid = "";
        if (data.length > 0) {
            strProdutoGrid += "not in ("
            for (i = 0; i < data.length; i++) {
                strProdutoGrid += data[i]['produtos_itens_pk'] + ",";

            }
            strProdutoGrid += "0)";
        }

        var objParametros1 = {
            "produtos_pk": $("#produtos_pk").val(),
            "qtde": $("#qtde_materias").val(),
            "strProdutoGrid": strProdutoGrid
        };
        var arrCarregar1 = carregarController("produto_item", "listarPorProdutosQtde", objParametros1);

        if (arrCarregar1.data.length > 0) {
            if ($("#qtde_materias").val() > arrCarregar1.data.length) {

                sweetMensagem('warning', "Só existem " + arrCarregar1.data.length + " unidades desse produto.");
                $("#janela_materiais").modal("show");
                return false;
            }
            for (i = 0; i < arrCarregar1.data.length; i++) {
                tblMaterial.row.add( [
                    "<td><input type='hidden' id='movimentacao_estoquePk[]' value=''>"+i+"</td>",
                    "<td><input type='hidden' id='categorias_produto_pk[]' value ='"+$("#categorias_produto_pk option:selected").val()+"'>"+ $("#categorias_produto_pk option:selected").text()+"</td>",
                    "<td><input type='hidden' id='produtos_pk[]' value ='"+$("#produtos_pk option:selected").val()+"'>"+ $("#produtos_pk option:selected").text()+"</td>",
                    "<td><input type='hidden' id='produtos_itens_pk[]' value ='"+arrCarregar1.data[i]['pk']+"'>"+ arrCarregar1.data[i]['ds_produto_item']+"</td>",
                    "<td><input type='hidden' id='dt_entrega[]' value ='"+$("#dt_entrega").val()+"'>"+ $("#dt_entrega").val()+"</td>",
                    "<td><input type='hidden' id='dt_devolucao[]' value =''></td>",
                    "<td><input type='hidden' id='obs_material[]' value ='"+$("#observacao_material").val()+"'>"+ $("#observacao_material").val()+"</td>",
                    "<td><input type='hidden' id='ic_mateiral_carga[]' value ='"+ic_mateiral_carga+"'>"+ ds_ic_mateiral_carga+"</td>",
                    "<td><a class='function_edit' style='margin-right: 12px;'><i class='fa fa-pencil-alt'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a class='function_delete'style='margin-right: 12px;font-size:18px;color:blue'><i class='bi bi-x-circle'></i></a></td>"
                ] ).draw().node();

            }
        }
    }
    else {
        tblMaterial.row.add( [
            "<td><input type='hidden' id='movimentacao_estoquePk[]' value=''>1</td>",
            "<td><input type='hidden' id='categorias_produto_pk[]' value ='"+$("#categorias_produto_pk option:selected").val()+"'>"+ $("#categorias_produto_pk option:selected").text()+"</td>",
            "<td><input type='hidden' id='produtos_pk[]' value ='"+$("#produtos_pk option:selected").val()+"'>"+ $("#produtos_pk option:selected").text()+"</td>",
            "<td><input type='hidden' id='produtos_itens_pk[]' value ='"+$("#produtos_itens_pk option:selected").val()+"'>"+ $("#produtos_itens_pk option:selected").text()+"</td>",
            "<td><input type='hidden' id='dt_entrega[]' value ='"+$("#dt_entrega").val()+"'>"+ $("#dt_entrega").val()+"</td>",
            "<td><input type='hidden' id='dt_devolucao[]' value =''></td>",
            "<td><input type='hidden' id='obs_material[]' value ='"+$("#observacao_material").val()+"'>"+ $("#observacao_material").val()+"</td>",
            "<td><input type='hidden' id='ic_mateiral_carga[]' value ='"+ic_mateiral_carga+"'>"+ ds_ic_mateiral_carga+"</td>",
            "<td><a class='function_edit' style='margin-right: 12px;'><i class='fa fa-pencil-alt'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a class='function_delete'style='margin-right: 12px;font-size:18px;color:blue'><i class='bi bi-x-circle'></i></a></td>"
        ] ).draw().node();
    }
    return false;


}


function fcSalvarConjuntoMateriais() {



    if ($("#grupo_para_movimentacao_ins_pk").val() == "") {
        $("#alert_grupo_para_movimentacao").show();
        $("#alert_grupo_para_movimentacao").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_grupo_para_movimentacao").slideUp(500);
        });
        return false;
    }
    if ($("#grupo_para_movimentacao_ins_pk").val() == 1) {
        if ($("#movimentar_para_pk").val() == "") {
            $("#alert_movimentar_colaborador").show();
            $("#alert_movimentar_colaborador").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_movimentar_colaborador").slideUp(500);
            });
            return false;
        }
    }
    if ($("#grupo_para_movimentacao_ins_pk").val() == 2) {
        if ($("#movimentar_para_pk").val() == "") {
            $("#alert_movimentar_lead").show();
            $("#alert_movimentar_lead").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert_movimentar_lead").slideUp(500);
            });
            return false;
        }
    }



    //Esta função está em colaborador_cad_form.js
    var strJSONDadosMateriais = fcFormatarDadosMateriais();

    var data = tblMaterial.rows().data();
    if (data.length == 0) {
        sweetMensagem('warning', "Por favor, Incluir um Material");
        return false;
    }

    var v_colaborador_pk = "";
    var v_leads_pk = "";
    var contratos_pk = "";

    if ($("#grupo_para_movimentacao_ins_pk").val() == 1) {
        var v_colaborador_pk = $("#movimentar_para_pk").val();
    }
    else if ($("#grupo_para_movimentacao_ins_pk").val() == 2) {
        var v_leads_pk = $("#movimentar_para_pk").val();
        if (IntContratosPk != "") {
            contratos_pk = IntContratosPk;
        }
    }
    var v_ds_conjunto_material = $("#ds_conjunto_material").val();


    var objParametros = {
        "pk": $("#conjunto_material_pk").val(),
        "ds_conjunto_material": v_ds_conjunto_material,
        "colaborador_pk": v_colaborador_pk,
        "leads_pk": v_leads_pk,
        "contratos_pk": contratos_pk,
        "materiais_pk": strJSONDadosMateriais
    };

    var arrEnviar = carregarController("conjunto_material", "salvar", objParametros);

    if (arrEnviar.status == true) {
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        $("#janela_materiais").modal("hide");



        fcRecarregarGridConjuntoMateriais();
    }
    else {

        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}
function fcRecarregarGridMateriais() {
    tblMaterial.clear().destroy();
    fcCarregarGridMateriais();
}
function fcRecarregarGridConjuntoMateriais() {
    tblResultado.ajax.reload();
    //fcCarregarGridConjuntoMateriais();
}


function fcFormatarDadosMateriais() {
    try {
        var movimentacao_estoquePk = "";
        var categorias_produto_pk = "";
        var produtos_pk = "";
        var produtos_itens_pk = "";
        var dt_entrega = "";
        var dt_devolucao = "";
        var obs_material = "";
        var ic_mateiral_carga = "";

        var arrKeys = [];
        var arrDados = [];
        arrKeys[0] = "movimentacao_estoque_pk";
        arrKeys[1] = "categorias_produto_pk";
        arrKeys[2] = "produtos_pk";
        arrKeys[3] = "produtos_itens_pk";
        arrKeys[4] = "dt_entrega";
        arrKeys[5] = "dt_devolucao";
        arrKeys[6] = "obs_material";
        arrKeys[7] = "ic_mateiral_carga";
        var i = 0;
        $("#tblMaterial").find('tbody tr').each(function () {
            if ($(this).find('td:nth-child(1) input').val() == "") {
                movimentacao_estoquePk = $(this).find('td:nth-child(1) input').val();
                categorias_produto_pk = $(this).find('td:nth-child(2) input').val();
                produtos_pk = $(this).find('td:nth-child(3) input').val();
                produtos_itens_pk = $(this).find('td:nth-child(4) input').val();
                dt_entrega = $(this).find('td:nth-child(5) input').val();
                dt_devolucao = $(this).find('td:nth-child(6) input').val();
                obs_material = $(this).find('td:nth-child(7) input').val();
                ic_mateiral_carga = $(this).find('td:nth-child(8) input').val();

                arrDados[i] = [movimentacao_estoquePk, categorias_produto_pk, produtos_pk, produtos_itens_pk, dt_entrega, dt_devolucao, obs_material, ic_mateiral_carga];
                i++;
            }
        });

        return arrayToJson(arrKeys, arrDados);
    }
    catch (err) {

        utilsJS.toastNotify(false, err);
    }
}

function fcAbrirFormNovoMaterial() {

    $(".chzn-select").chosen('destroy');
    fcLimparFormMaterial();

    //limpa os dados de qualquer registro existe
    $("#ds_conjunto_material").val("");
    $("#grupo_para_movimentacao_ins_pk").val("");
    $("#movimentar_para_pk").val("");
    $("#str_opc_ins").text("");
    $("#conjunto_material_pk").val("");
    $("#janela_materiais").modal("show");

    $("#movimentar_para_pk").prop('disabled', false);
    $("#grupo_para_movimentacao_ins_pk").prop('disabled', false);

    if ($("#colaborador_pk").val() != "") {

        $("#grupo_para_movimentacao_ins_pk").val(1);
        fcVerificarMovimentadoParaIns();
        $("#movimentar_para_pk").val($("#colaborador_pk").val());
        $("#grupo_para_movimentacao_ins_pk").prop('disabled', true);
        $("#movimentar_para_pk").prop('disabled', true);

    }
    if ($("#leads_pk").val() != "") {
        $("#grupo_para_movimentacao_ins_pk").val(2);
        fcVerificarMovimentadoParaIns();
        $("#movimentar_para_pk").val($("#leads_pk").val());
        $("#grupo_para_movimentacao_ins_pk").prop('disabled', true);
        $("#movimentar_para_pk").prop('disabled', true);
    }


    if (IntContratosPk != 0) {
        $("#grupo_para_movimentacao_ins_pk").val(2);
        fcVerificarMovimentadoParaIns();
        if ($("#leads_pk_cad_form").val() != "") {
            $("#movimentar_para_pk").val($("#leads_pk_cad_form").val());
        }

        $("#leads_pk_cad_form").change(function () {
            if ($("#leads_pk_cad_form").val() != "") {
                $("#movimentar_para_pk").val($("#leads_pk_cad_form").val());
            }
        });

        $("#grupo_para_movimentacao_ins_pk").prop('disabled', true);
        $("#movimentar_para_pk").prop('disabled', true);

    }

    tblMaterial.clear().destroy();
    tblMaterial = $('#tblMaterial').DataTable( {
        responsive: true,
    });
    $("#acao").val("ins");
    setTimeout(function () {
        $(".chzn-select").chosen('destroy');
        $(".chzn-select").chosen({ allow_single_deselect: true });
        fcCarregarProdutos("");
        fcCarregarProdutosItens("", "");

        if ($("#colaborador_pk").val() != "") {
            $("#movimentar_para_pk").val($("#colaborador_pk").val());
        }
        if ($("#leads_pk").val() != "") {
            $("#movimentar_para_pk").val($("#leads_pk").val());
        }

    }, 500);
}



function fcLimparFormMaterial() {


    $("#produtos_itens_pk").prop('disabled', false);
    $("#qtde_materias").prop('disabled', false);
    $("#categorias_produto_pk").val("");
    $("#produtos_pk").val("");
    $("#produtos_itens_pk").val("");
    $("#dt_entrega").val("");
    $("#observacao_material").val("");
    $("input[id=ic_mateiral_carga]").prop("checked", false);
    $("#dt_devolucao").val("");
    $("#qtde_materias").val("");
    $("#count_material").val("");
}

function fcFecharModalMovimentacao() {
    $("#janela_materiais").modal("hide");
}

let mat = 0;
$(document).ready(function(){

    //Atribui os eventos
    $(document).on('click', '#cmdIncluirConjuntoMaterial', fcAbrirFormNovoMaterial);
    $(document).on('click', '#cmdIncluirMaterial', fcIncluirMateriais);
    $(document).on('click', '.fechar_modal_movimentacao', fcFecharModalMovimentacao);

    var contratos_pk = "";
    if ($("#contratos_pk").val() == undefined) {
        var IntContratosPk = "";
    }
    else {
        if ($("#contratos_pk").val() != "") {
            var IntContratosPk = $("#contratos_pk").val();
        }
        else {
            var IntContratosPk = "0";
        }

    }

    $("#exibir_lead_colaborador").hide();
    $("#exibir_em_menu_estoque").show();
    $("#exibir_titulo").show();

    $("#grupo_para_movimentacao_pk").change(function () {
        if ($("#grupo_para_movimentacao_pk").val() == 1) {
            $(".chzn-select").chosen('destroy');
            fcVerificarMovimentadoPara();
            $(".chzn-select").chosen({ allow_single_deselect: true });
        }
        else if ($("#grupo_para_movimentacao_pk").val() == 2) {
            $(".chzn-select").chosen('destroy');
            fcVerificarMovimentadoPara();
            $(".chzn-select").chosen({ allow_single_deselect: true });

        }

    });

    $("#grupo_para_movimentacao_ins_pk").prop('disabled', false);
    $("#movimentar_para_pk").prop('disabled', false);

    if (IntContratosPk != "") {


        $("#grupo_para_movimentacao_pk").val(2);
        fcVerificarMovimentadoPara();

        $("#grupo_para_movimentacao_ins_pk").val(2);
        fcVerificarMovimentadoParaIns();

        $("#leads_pk_cad_form").change(function () {

            if ($("#leads_pk_cad_form").val() != "") {
                $("#movimentar_para_pesq_pk").val($("#leads_pk_cad_form").val());
                $("#movimentar_para_pk").val($("#leads_pk_cad_form").val());
            }


        });

        $("#grupo_para_movimentacao_ins_pk").prop('disabled', false);
        $("#movimentar_para_pk").prop('disabled', true);
        $("#exibir_lead_colaborador").show();
        $("#exibir_em_menu_estoque").hide();
        $("#exibir_titulo").hide();

    }
    fcCarregarGridConjuntoMateriais();

    fcCarregarCategorias("");
    //Produtos
    fcCarregarProdutos("");


    $("#categorias_produto_pk").change(function () {
        $(".chzn-select").chosen('destroy');

        fcCarregarProdutos($("#categorias_produto_pk").val());
        $(".chzn-select").chosen({ allow_single_deselect: true });
    });




    $(".chzn-select").chosen({ allow_single_deselect: true });

    $("#grupo_para_movimentacao_pk").change(function () {
        if ($("#grupo_para_movimentacao_pk").val() == 1) {
            $(".chzn-select").chosen('destroy');
            fcVerificarMovimentadoPara();
            $(".chzn-select").chosen({ allow_single_deselect: true });
        }
        else if ($("#grupo_para_movimentacao_pk").val() == 2) {
            $(".chzn-select").chosen('destroy');
            fcVerificarMovimentadoPara();
            $(".chzn-select").chosen({ allow_single_deselect: true });

        }

    });


    $("#dt_movimentacao_ini").on('keyup', function () {
        mascara(this, mdata);
    });
    $("#dt_movimentacao_fim").on('keyup', function () {
        mascara(this, mdata);
    });


    $('#dt_movimentacao_ini').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $('#dt_movimentacao_fim').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();





    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdEnviarMateriais', fcSalvarConjuntoMateriais);
    $(document).on('click', '#cmdVoltar', fcVoltar);


    //------------------------MODAL------------------------------//

    fcCarregarProdutosItens("", "");



    $("#categorias_produto_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarProdutos($("#categorias_produto_pk").val());
        $(".chzn-select").chosen({ allow_single_deselect: true });
    });

    //Seleciona o produto
    $("#produtos_pk").change(function () {
        //Itens Material
        $(".chzn-select").chosen('destroy');
        fcCarregarProdutosItens($("#produtos_pk").val(), "");
        $(".chzn-select").chosen({ allow_single_deselect: true });
    });
    $("#produtos_itens_pk").change(function () {
        if ($("#produtos_itens_pk").val() != "") {
            $("#qtde_materias").prop('disabled', true);
            $("#qtde_materias").val("");
        }
        else {
            $("#qtde_materias").prop('disabled', false);
        }

    });

    $("#qtde_materias").keypress(function () {
        mascara(this, soNumeros);
    });

    $("#qtde_materias").change(function () {
        if ($("#qtde_materias").val() != "") {
            if ($("#qtde_materias").val() > 0) {
                $("#produtos_itens_pk").prop('disabled', true);
                $("#produtos_itens_pk").val("");
            }
            else {
                $("#produtos_itens_pk").prop('disabled', false);
            }
        }
        else {
            $("#produtos_itens_pk").prop('disabled', false);
        }
    });

    $('#dt_entrega').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", new Date());
    $("#dt_entrega").keypress(function () {
        mascara(this, mdata);
    });
    $("#dt_entrega").keypress(function () {
        mascara(this, horamask);
    });


    $('#dt_devolucao').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", new Date());
    $("#dt_devolucao").keypress(function () {
        mascara(this, mdata);
    });
    $("#dt_devolucao").keypress(function () {
        mascara(this, horamask);
    });

    $("#grupo_para_movimentacao_ins_pk").change(function () {
        if ($("#grupo_para_movimentacao_ins_pk").val() == 1) {
            $(".chzn-select").chosen('destroy');
            fcVerificarMovimentadoParaIns();
            $(".chzn-select").chosen({ allow_single_deselect: true });
        }
        else if ($("#grupo_para_movimentacao_ins_pk").val() == 2) {
            $(".chzn-select").chosen('destroy');
            fcVerificarMovimentadoParaIns();
            $(".chzn-select").chosen({ allow_single_deselect: true });

        }

    });
    fcCarregarGridMateriais();

});
