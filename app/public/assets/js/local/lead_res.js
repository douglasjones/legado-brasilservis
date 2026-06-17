var pesquisa = 1;
var tblResultado;
function fcCarregarGridLeads() {
    //tblResultado.clear().destroy();
    var objParametros = {
        "ds_lead": $("#leads_pk").val(),
        "ic_cliente": $("#ic_status").val(),
        "segmentos_pk": $("#segmentos_pk").val(),
        "supervisores_pk": $("#supervisores_pk").val(),
        "ic_tipo_lead": $("#ic_tipo_lead").val(),
        "responsavel_pk": $("#responsavel_pk").val(),
        "leads_pai_pk": $("#leads_clientes_pk").val(),
        "leads_clientes_pk": $("#leads_clientes_pk").val()

    };
    var v_url = routes_api("lead", "listarDataTable", objParametros);
        tblResultado = $("#tblResultado").DataTable({
            searching: true,
            paging: true,
            scrollX: true,
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
                        return full['ds_tipo_lead'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_lead'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_cidade'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ic_cliente'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '60px'

                },
                {
                    mRender: function (data, type, full) {
                        var buttonPainel = '<a class="function_painel"><span><i class="bi bi-card-list" style="font-size=18px;color:blue" title="Painel"></i></span></a> &nbsp;&nbsp;';
                        var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> &nbsp;&nbsp;';
                        var buttonOc = '<a class="function_oc"><span><i class="bi bi-list-task" style="font-size=18px;color:blue" title="ocorrencia"></i></span></a> &nbsp;&nbsp;';
                        var buttonCalendar = '<a class="function_agenda"><span><i class="bi bi-calendar2-date" style="font-size=18px;color:blue" title="Agenda"></i></span></a> &nbsp;&nbsp;';
                        var buttonQrCode = '<a class="function_qr_code"><span><i class="bi bi-qr-code" style="font-size=18px;color:blue" title="QrCode"></i></span></a> ';



                        return buttonPainel +buttonCalendar + buttonOc +buttonQrCode + buttonDelete;
                    },
                    'orderable': false,
                    'searchable': false,
                    width: '60px'
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
        fcAbrirPainel(data['pk']);
    });
    $('#tblResultado tbody').on('click', '.function_qr_code', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcAbrirQrCode(data['pk'], data['ds_lead']);
    });
    $('#tblResultado tbody').on('click', '.function_oc', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcAbrirOcorrencia(data['pk']);
    });



    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcExcluir(data['pk'], data['ds_lead']);
    });

    $('#tblResultado tbody').on('click', '.function_agenda', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcAbrirFormAgendaDatatable("", "", "inserir", data['pk']);
    });

}


//Abre modal cadastro
function fcAbrirFormAgendaDatatable(id, date, acao, leads_pk){
    try {
        var dataFormatada = "";
        if(date != ""){
            var date = new Date(date);
            var month = date.getMonth()<9?"0"+(date.getMonth()+1):date.getMonth()+1;
            var day = date.getDate()<9?"0"+(date.getDate()+1):date.getDate()+1;
            dataFormatada = day + "/" + month + "/" + date.getFullYear();
        }

        $("#ds_rua_html").html("");
        $("#ds_numero_html").html("");
        $("#ds_complemento_html").html("");
        $("#ds_bairro_html").html("");
        $("#ds_cidade_html").html("");
        $("#ds_cep_html").html("");
        $("#ds_uf_html").html("");
        $("#tipo_agenda_pk").val("");
        $("#dt_ini_evento").val(dataFormatada);
        $("#hr_ini_evento").val("");
        $("#dt_fim_evento").val("");
        $("#hr_fim_evento").val("");
        $("#ic_lembrete").val("");
        $("#ic_repetir").val("");
        $("#ds_link_reuniao").val("");
        $("#endereco_pk").val("");
        $("#participante_pk").val("");
        $("#tipo_participante_pk").val("");
        $("#agendas_pk").val(id);
        $("#ds_obs_agenda").val("");
        $("#div_link_reuniao").hide();
        $("#div_endereco").hide();
        $("#div_endereco_botao").hide();
        $("#ic_status").val(1);
        $("#motivo_cancelameto_pk").val("");
        $("#classificacao_agenda_pk").val("");
        $("#obs_classificacao").val("");

        if(acao != 'edit'){
            $("#div_motivo_cancelameto_pk").hide();
            $("#div_classificacao_agenda_pk").hide();
            $("#div_obs_classificacao").hide();
        }else if(acao != 'add'){
            $("#div_classificacao_agenda_pk").show();
            $("#div_obs_classificacao").show();
        }

        fcCarregarLeadsAgenda();
        $("#leads_pk_agenda").val(leads_pk)

        $('#leads_pk_agenda').select2();

        fcCarregarInformacoesAgenda()
        tblParticipantesAgenda.clear().destroy();
        tblDocumentosAgenda.clear().destroy();
        fcGridAgendaParticipantes();
        fcCarregarGridDocumentosAgenda();
        fcCarregarEnderecosAgenda();
        //dtAgenda(start);
        $('#event-modal').modal("show");
    } catch (error) {
        utilsJS.toastNotify(false, error);
    }
}
function fcCarregarLeads() {
    //Carrega os grupos

    var objParametros = {
        "ic_tipo_lead": 2,
        "leads_pai_pk": $("#leads_clientes_pk").val(),
        "ic_cliente": $("#ic_status").val()
    };

    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);

    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");

}

function fcCarregarClientesRes() {
    //Carrega os grupos
    var objParametros = {
        "ic_tipo_lead": 1,
        "ic_cliente": $("#ic_status").val()
    };

    var arrCarregar = carregarController("lead", "listarTodosClientes", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#leads_clientes_pk"), arrCarregar, " ", "pk", "ds_lead");

}

function fcSelecionarLeads() {
    try {
        var data;
        data = tblResultado.rows('.selected').data();
        var leads = [];
        for (var i = 0; i < data.length; i++) {
            leads[i] = data[i]['t_pk'];
        }

        var json_leads = JSON.stringify(leads);
        var objParametros = {
            "modulos_pk": json_leads
        }
        var arrEnviar = carregarController("comercial", "salvarProcessoMovimentacaoPesquisa", objParametros);
        if (arrEnviar.status == true){
            // Reload datable

            utilsJS.toastNotify(true, arrEnviar.message);
            fcFecharModalLead();
        }
        else{

            utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
        }

    } catch (error) {

        utilsJS.toastNotify(false, error);
    }
}

function fcAbrirQrCode(leads_pk, ds_lead) {
    var objParametros = {
        "ds_lead":ds_lead,
        "pk":leads_pk,
        "local":$("#local").val()
    };
    sendPost('lead','qrCode' ,objParametros);
}
function fcAbrirOcorrencia(leads_pk) {
    if(leads_pk!=""){
        $("#janela_ocorrencia_modal").modal("show");
        tblOcorrencia.clear().destroy();
    }
    var objParametros = {
        "leads_pk": leads_pk
    };
    var v_url = routes_api("ocorrencia", "listarOcorrenciasLeadPk", objParametros);

    //Trata a tabela
    tblOcorrencia = $('#tblOcorrencia').DataTable({
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
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_tipo_ocorrencia'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_tipos_ocorrencias_pk'];
                },
                'orderable': true,
                'searchable': false,
                'visible': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_ocorrencia'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_nome_usuario_cadastro'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_fechamento'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_agendado_para'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_retorno'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_retorno'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_termino_retorno'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            }
        ]

    });
}
function fecharModalOcLead(){
    $("#janela_ocorrencia_modal").modal("hide");
}
function fcAbrirPainel(v_pk) {
    var arrCarregar = permissao("lead", "cons");
    if (arrCarregar.status != true) {
        utilsJS.toastNotify(false, "Você não tem permissão");
        return false;
    }
    var objParametros = {
        "ic_abertura":1,
        "pk":v_pk,
        "local":$("#local").val()
    };
    sendPost('lead','leadMainPainel' ,objParametros);

}



function fcExcluir(v_pk, v_ds_lead) {
    var arrCarregar = permissao("lead", "del");

    if (arrCarregar.status != true) {
        utilsJS.toastNotify(false, 'Você não tem permissão para acessar essa pagina!');
        return false;
    }
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_ds_lead+'?', function () {
        if (v_pk != "") {

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("lead", "excluir", objParametros);

            if (arrExcluir.status == true) {
                utilsJS.toastNotify(true, arrExcluir.message);

                // Reload datable
                tblResultado.ajax.reload();

            }
            else {
                utilsJS.toastNotify(false, "Falhou a requisição");
            }
        }
        else {
            utilsJS.toastNotify(false, "Código não encontrado");
        }
    });
    return false;
}

function fcPesquisarLead() {
    tblResultado.clear().destroy();
    fcCarregarGridLeads();
}

function fcIncluirLead() {
    sendPost('lead','cadForm',{"local":$("#local").val()})
}

function fcCarregarLeads1() {
    var arrCarregar = carregarController("lead", "listarTodos", "");
    carregarComboAjax($("#ds_lead"), arrCarregar, " ", "pk", "ds_lead");
}


function fcVoltarLead(){
    if($("#local").val()==1){
        sendPost("menu", "comercial",{});
    }
    else{
        sendPost("menu", "operacional",{});
    }
}

function fcAbreModalLead(){

    $('#abrir').modal({backdrop: '', keyboard: false});
}

function fcFecharModalLead(){
    $('#abrir').hide();
    fcAtualizaComercialPainel();
}

$(document).ready(function () {
    //CONTROLE DE PERMISSÃO DA PAGINA
    var arrCarregar = permissao("lead", "cons");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false, 'Você não tem permissão para acessar essa pagina!');
        setTimeout(function() {
            sendPost('menu','principal',{})
        }, 2000);
        return false;
    }
    fcCarregarClientesRes();
    fcCarregarLeads();

    $('#abrir').addClass('');
    $("#bt_titulo_ab_padrao").show();


    $(".chzn-select").chosen({ allow_single_deselect: true });
    $("#ic_status").change(function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarClientesRes();
        fcCarregarLeads();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });
    $("#leads_clientes_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarLeads();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });


    fcCarregarGridLeads();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisarLead', fcPesquisarLead);
    $(document).on('click', '#cmdIncluirLead', fcIncluirLead);
    $(document).on('click', '#cmdVoltarLead', fcVoltarLead);
    $(document).on('click', '#cmdFecharModalLead', fcFecharModalLead);
    $(document).on('click', '#cmdSalvarModalLead', fcSelecionarLeads);


    $('#abrir').addClass('');
    $("#bt_titulo_ab_padrao").show();

    $('#tblResultado tbody').on('click', "input[name='checkbox[]']", function () {
        $(this).parents("tr").toggleClass('selected');
    });

    fcAbrirOcorrencia("");

});
