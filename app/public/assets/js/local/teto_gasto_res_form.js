var tblResultado;
function fcPesquisar(){
    tblResultado.clear().destroy();
    fcCarregarGrid();
}
function fcIncluir(){
    var objParametros = {};
    sendPost('teto_gasto','cadForm' ,objParametros);
}

function fcExcluir(v_pk){

    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("teto_gasto", "excluir", objParametros);

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true, arrExcluir.message);

                // Reload datable
                tblResultado.ajax.reload();

            }
            else{
                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
            }
        }
        else{
            utilsJS.toastNotify(false, 'Código não encontrado');
        }
    });
}

function fcEditar(v_pk){
    var objParametros = {
        "pk":v_pk
    };
    sendPost('teto_gasto','cadForm' ,objParametros);

}

function fcDetalhar(v_pk){
    var objParametros = {
        "pk":v_pk
    };
    sendPost('teto_gasto','cadForm' ,objParametros);
}

function fcCarregarGrid(){
try {
    var objParametros = {
        "tipo_grupo_pk": $("#tipo_grupo_pk").val(),
        "grupo_leancamento_pk": $("#grupo_leancamento_pk").val(),
        "grupo_lancamento_centro_custo_pk": $("#grupo_lancamento_centro_custo_pk").val(),
        "posto_trabalho_pk": $("#posto_trabalho_pk").val(),
        "contratos_pk": $("#contratos_pk").val(),
        "ds_ano_vigente_teto": $("#ds_ano_vigente_teto").val(),
        "ic_status": $("#ic_status").val()
    };

    var v_url = routes_api("teto_gasto", "listarGrid", objParametros);
    //Trata a tabela
    tblResultado = $('#tblResultado').DataTable({
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
                    return full['t_tipo_grupo_pk'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_grupo_lancamento'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_leads_posto_trabalho_pk'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['t_contratos_pk'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_ano_vigente_teto'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_vl_total_teto'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['t_vl_utilizado_atual'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ic_status'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="editar"></i></span></a> ';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';
                

                    return buttonPainel + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
            }
        ]
    });
   
    //Atribui os eventos na coluna ação.
    $('#tblResultado tbody').on('click', '.function_edit', function () {
        var data;
        if(tblResultado.row( $(this).parents('li')).data()){
            data = tblResultado.row( $(this).parents('li')).data();
        }
        else if(tblResultado.row( $(this).parents('tr')).data()){
            data = tblResultado.row( $(this).parents('tr')).data();
        }
        fcEditar(data['t_pk']);

    } );

    //Atribui os eventos na coluna ação.
    $('#tblResultado tbody').on('click', '.function_det', function () {
        var data;
        if(tblResultado.row( $(this).parents('li')).data()){
            data = tblResultado.row( $(this).parents('li')).data();
        }
        else if(tblResultado.row( $(this).parents('tr')).data()){
            data = tblResultado.row( $(this).parents('tr')).data();
        }
        fcDetalhar(data['t_pk']);

    } );

    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['t_pk'], data['t_tipo_origem_teto_gastos']);
    } );

} catch (error) {
    utilsJS.toastNotify(false, error);
}
    
}

function  fcVoltar(){
    var objParametros = {};
    sendPost('menu','financeiro' ,objParametros);
}

//Combos clientes
function fccarregarLeadsClientes() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#grupo_leancamento_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fccarregarLeadsPostosTrabalho() {
    var objParametros = {
        "pk": $("#grupo_leancamento_pk").val()
    };
    var arrCarregar = carregarController("lead", "listaLeadsPostosTrabalho", objParametros);
    carregarComboAjax($("#posto_trabalho_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fccarregarLeadsContratos() {
    var objParametros = {
        "leads_pk": $("#posto_trabalho_lancamento_pk").val()
    };
    var arrCarregar = carregarController("contrato", "listaLeadContratos", objParametros);
    
    carregarComboAjax($("#contratos_pk"), arrCarregar, " ", "pk", "ds_contrato");
}


function fccarregarLeadsClientesCentroCustoForncedor() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#grupo_lancamento_centro_custo_pk"), arrCarregar, " ", "pk", "ds_lead");
}

//Combo colaborador
function fccarregarColaboradorContratos() {
    

    var objParametros = {
        "leads_pk": $("#posto_trabalho_pk").val(),
        "colaborador_pk": $("#colaborador_pk").val()
    };
    var arrCarregar = carregarController("contrato", "listaColaboradorContratos", objParametros);
    carregarComboAjax($("#contratos_pk"), arrCarregar, " ", "pk", "ds_contrato");
}

function fcCarregarColaborador(){
    
    var objParametros = {
        "pk": ""
    };      
    
    var arrCarregar = carregarController("colaborador", "listaColaborador", objParametros);    
    carregarComboAjax($("#colaboradores_pk"), arrCarregar, " ", "pk", "ds_colaborador");
        
}

function fccarregarColaboradorPostosTrabalho() {

    var objParametros = {
        "colaborador_pk": $("#grupo_leancamento_pk").val(),
    };
    var arrCarregar = carregarController("lead", "listaColaboradorPostosTrabalho", objParametros);
    carregarComboAjax($("#posto_trabalho_pk"), arrCarregar, " ", "pk", "ds_lead");
}

//Combo fornecedor
function fccarregarFornecedor() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("fornecedor", "listarTodos", objParametros);
    carregarComboAjax($("#grupo_leancamento_pk"), arrCarregar, " ", "pk", "ds_fornecedor");
}

function fccarregarFornecedorPostosTrabalho() {

    var objParametros = {
        "leads_pk": $("#grupo_lancamento_centro_custo_pk").val()
    };
    var arrCarregar = carregarController("lead", "listaFornecedorPostosTrabalho", objParametros);
    carregarComboAjax($("#posto_trabalho_pk"), arrCarregar, " ", "pk", "ds_lead");
}


function fccarregarFornecedorContratos() {
    var objParametros = {
        "leads_pk": $("#posto_trabalho_pk").val()
    };
    var arrCarregar = carregarController("contrato", "listaLeadContratos", objParametros);
    carregarComboAjax($("#contratos_pk"), arrCarregar, " ", "pk", "ds_contrato");
}

function fcSelecionaGrupo() {

    $("#div_grupo_lancamento_centro_custo").hide()
    if ($("#tipo_grupo_pk").val() == 1) {
        fccarregarLeadsClientes();
    } else if ($("#tipo_grupo_pk").val() == 2) {
        fccarregarColaborador();
    } else if ($("#tipo_grupo_pk").val() == 3) {
        $("#div_grupo_lancamento_centro_custo").show()
        fccarregarFornecedor();
        fccarregarLeadsClientesCentroCustoForncedor();
    }
}

$(document).ready(function(){

    $("#tipo_grupo_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcSelecionaGrupo();
        
        if($("#tipo_grupo_pk").val() == '1'){
            $("#grupo_leancamento_pk").change(function () {
                $(".chzn-select").chosen('destroy');
                fccarregarLeadsPostosTrabalho();
            });
            
            $("#posto_trabalho_pk").change(function () {
                $(".chzn-select").chosen('destroy');
                fccarregarLeadsContratos();
            });
        }
        if($("#tipo_grupo_pk").val() == '2'){
            $("#grupo_leancamento_pk").change(function () {
                $(".chzn-select").chosen('destroy');
                fccarregarColaboradorPostosTrabalho();
            });
        
            $("#posto_trabalho_pk").change(function () {
                $(".chzn-select").chosen('destroy');
                fccarregarColaboradorContratos()
            });
        }
        if($("#tipo_grupo_pk").val() == '3'){
            $("#grupo_leancamento_pk").change(function () {
                $(".chzn-select").chosen('destroy');
                fccarregarFornecedorPostosTrabalho();
            });
        
            $("#posto_trabalho_pk").change(function () {
                $(".chzn-select").chosen('destroy');
                fccarregarFornecedorContratos()
            });
        }
    });

    //faz a carga inicial do grid.
    fcCarregarGrid();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);


});


