var tblDocumentos;
var tblResultado;
function fcVisibilidadeSolicitacoes(){
    $("#solicitar_correcao").hide()
    $("#solicitar_recusa").hide()
    $("#solicitar_aprovacao").hide()
    $("#gestor").hide()
    switch ($('#ic_status').val()) {
        case '2':
            $("#solicitar_aprovacao").show()
            $("#gestor").show()
            break;
        case '3':
            $("#solicitar_aprovacao").show()
            break;
        case '5':
            $("#solicitar_recusa").show()
            break;
        case '4':
            $("#solicitar_correcao").show()
            break;
        case '6':
            $("#solicitar_correcao").show()
            break;
    }
}

function fcCarregar(){

        var objParametros = {
            "pk": $("#pk").val(),
            "lancamento_old_pk": $("#lancamento_old_pk").val()

        };

        var arrCarregar = carregarController("analise_financeira", "listarPk", objParametros);

            $("#lancamentos_pk").html(arrCarregar.data[0]['lancamentos_pk'])
            $("#dt_cadastro").html(arrCarregar.data[0]['dt_cadastro'])
            $("#ds_usuario").html(arrCarregar.data[0]['ds_usuario'])
            $("#ds_operacao").html(arrCarregar.data[0]['ds_operacao'])
            $("#ds_metodo_pagamento").html(arrCarregar.data[0]['ds_metodo_pagamento'])
            $("#ds_empresas").html(arrCarregar.data[0]['ds_razao_social'])
            $("#ds_conta_bancaria").html(arrCarregar.data[0]['ds_conta_bancaria'])
            $("#ds_lancamento").html(arrCarregar.data[0]['ds_lancamento'])
            $("#ds_tipo_grupo").html(arrCarregar.data[0]['ds_tipo_grupo'])
            $("#ds_lead").html(arrCarregar.data[0]['ds_recebido_de'])
            $("#ds_grupo_lancamento_centro_custo").html(arrCarregar.data[0]['ds_grupo_lancamento_centro_custo'])
            $("#ds_leads_clientes").html(arrCarregar.data[0]['ds_leads_clientes'])
            $("#vl_lancamento").html(arrCarregar.data[0]['vl_lancamento'])
            $("#dt_vencimento").html(arrCarregar.data[0]['dt_vencimento'])
            $("#ds_tipo_operacao").html(arrCarregar.data[0]['ds_operacao'])
            $("#ds_contrato").html(arrCarregar.data[0]['ds_lancamento_contrato'])
            $("#ds_posto_trabalho").html(arrCarregar.data[0]['ds_lancamento_posto_trabalho'])
            $("#ds_cliente").html(arrCarregar.data[0]['ds_cliente'])
            $("#ds_banco").html(arrCarregar.data[0]['ds_banco'])
            $("#ds_agencia").html(arrCarregar.data[0]['ds_agencia'])
            $("#ds_conta").html(arrCarregar.data[0]['ds_conta'])
            $("#parcela_pk").html(arrCarregar.data[0]['parcela_pk'])
            $("#ds_pix").html(arrCarregar.data[0]['ds_pix'])
            $("#obs").html(arrCarregar.data[0]['obs'])

            fcCarregarGridDocumentos(arrCarregar.data[0]['lancamentos_pk'])
            
}


function fcCarregarGestor() {
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("usuario", "listarTodosGestores", objParametros);
    carregarComboAjax($("#gestores_pk"), arrCarregar, " ", "pk", "ds_usuario");
}


function fcCarregarGridDocumentos(lancamento_pk) {
    var objParametros = {
        "lancamentos_pk": lancamento_pk
    };

    var v_url = routes_api("documento", "listarDocumentosLancamentos", objParametros);
    //Trata a tabela
    tblDocumentos = $('#tblDocumentos').DataTable({
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
                    return full['t_ds_documento'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_obs'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_nome_original'];
                },
                'orderable': true,
                'searchable': false

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
   
    $('#tblDocumentos tbody').on('click', '.function_edit', function () {
        var data;

        if (tblDocumentos.row($(this).parents('li')).data()) {
            data = tblDocumentos.row($(this).parents('li')).data();
        }
        else if (tblDocumentos.row($(this).parents('tr')).data()) {
            data = tblDocumentos.row($(this).parents('tr')).data();
        }

        if (data['t_pk'] != "") {
            fcDownloadDocumento(data['pk_doc_bd'],data['t_ds_documento']);
        }
    });
}

function fcCarregarGrid() {
    var objParametros = {
        "analise_financeira_pk": $("#pk").val()
    };

    var v_url = routes_api("analise_financeira_processo", "historicoAnaliseFinanceira", objParametros);
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
                    return full['t_ic_status'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_usuario_cadastro'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['t_obs'];
                },
                'orderable': true,
                'searchable': false

            }
        ]
    });
}


function fcCancelar() {
    
    var objParametros = {};
    sendPost('analise_financeira','receptivo' ,objParametros);
}

function fcSalvar(){

    switch($("#ic_status").val()){
        case '2': 
            var obs_aprovacao = $("#obs_aprovacao").val();
            var ic_aprovacao = $("#ic_status").val();
            var gestor_aprovacao_pk = $("#gestores_pk").val();
            var analise_financeira_pk = $("#pk").val();
            
            var objParametros = {
                "obs_aprovacao": obs_aprovacao,
                "ic_aprovacao": (ic_aprovacao),
                "analise_financeira_pk": (analise_financeira_pk),
                "gestor_aprovacao_pk": (gestor_aprovacao_pk)
            };  
        break;
        case '3':
            var obs_aprovacao = $("#obs_aprovacao").val();
            var ic_aprovacao = $("#ic_status").val();
            var analise_financeira_pk = $("#pk").val();
            
            var objParametros = {
                "obs_aprovacao": obs_aprovacao,
                "ic_aprovacao": (ic_aprovacao),
                "analise_financeira_pk": (analise_financeira_pk)
            };  
        break;
        case '4':
            var obs_correcao = $("#obs_correcao").val();
            var ic_correcao = $("#ic_status").val();
            var analise_financeira_pk = $("#pk").val();
            
            var objParametros = {
                "obs_correcao": (obs_correcao),
                "ic_correcao": (ic_correcao),
                "analise_financeira_pk": (analise_financeira_pk)
            };  
        break;
        case '5':
            var obs_recusa = $("#obs_recusa").val();
            var ic_recusa = $("#ic_status").val();
            var analise_financeira_pk = $("#pk").val();
            
            var objParametros = {
                "obs_recusa": (obs_recusa),
                "ic_recusa": (ic_recusa),
                "analise_financeira_pk": (analise_financeira_pk)
            };  
        break;
        case '6':
            var obs_correcao = $("#obs_correcao").val();
            var ic_correcao = $("#ic_status").val();
            var analise_financeira_pk = $("#pk").val();
            
            var objParametros = {
                "obs_correcao": (obs_correcao),
                "ic_correcao": (ic_correcao),
                "analise_financeira_pk": (analise_financeira_pk)
            };  
        break;
    }
    
    var arrEnviar = carregarController("analise_financeira_processo", "salvar", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);

        sendPost('analise_financeira','receptivo' ,objParametros);    
    }else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');

    }
}

function fcListarSutatus(){
    var objParametros = {
        "usuario_pk": ""
    };
    var arrCarregar = carregarController("usuario", "listarGruposUsuario", objParametros);

    var ds_drupo =  arrCarregar.data[0]['ds_grupo'];

    
    var html = "";
    html += "<option value=''></option>";
    if(ds_drupo == "Analista Financeiro"){
        html += "<option value='2'>Aprovado Analista</option>";
    }else if(ds_drupo == "Controller"){
        html += "<option value='3'>Aprovado Gestor</option>";
    }
    html += "<option value='4'>Correção Solicitada</option>";
    html += "<option value='5'>Recusado</option>";
    //html += "<option value='6'>Correção Feita</option>";
    $("#ic_status").html(html)
    
    
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



$(document).ready(function () {
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $("#solicitar_correcao").hide()
    $("#solicitar_recusa").hide()
    $("#solicitar_aprovacao").hide()
    $("#gestor").hide();

    
    $(document).on('change', '#ic_status', fcVisibilidadeSolicitacoes);
    $(document).on('click', '#cmdIncluirAnalise', fcSalvar);

    fcCarregarGestor();
    fcListarSutatus();
    //fcCarregarGridDocumentos();
    fcCarregarGrid();
    fcCarregar();
})