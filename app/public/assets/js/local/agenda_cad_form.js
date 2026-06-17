var tblParticipantesAgenda;
var tblDocumentosAgenda;
function fcValidarFormAgenda(){
    if($("#tipo_agenda_pk").val() == ''){
        $("#alert_tipo_agenda_pk").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_tipo_agenda_pk").slideUp(500);
        });
        $('#tipo_agenda_pk').focus();
        return false;
    }
    if($("#dt_ini_evento").val() == ''){
        $("#alert_dt_ini_evento").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_ini_evento").slideUp(500);
        });
        $('#dt_ini_evento').focus();
        return false;
    }
    if($("#hr_ini_evento").val() == ''){
        $("#alert_hr_ini_evento").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_hr_ini_evento").slideUp(500);
        });
        $('#hr_ini_evento').focus();
        return false;
    }
    if($("#dt_fim_evento").val() == ''){
        $("#alert_dt_fim_evento").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_fim_evento").slideUp(500);
        });
        $('#dt_fim_evento').focus();
        return false;
    }
    if($("#hr_fim_evento").val() == ''){
        $("#alert_hr_fim_evento").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_hr_fim_evento").slideUp(500);
        });
        $('#hr_fim_evento').focus();
        return false;
    }
    if($("#tipo_agenda_pk").val() == 1){
        if($("#leads_pk_agenda").val() == ''){
            $("#alert_leads_pk_agenda").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_leads_pk_agenda").slideUp(500);
            });
            $('#leads_pk_agenda').focus();
            return false;
        }
        if($("#endereco_pk").val() == ''){
            $("#alert_endereco_pk").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_endereco_pk").slideUp(500);
            });
            $('#endereco_pk').focus();
            return false;
        }
    }
    fcEnviarAgenda();
}

function fcEnviarAgenda(){
    var strJSONDadosTabelaArquivos = fcFormatarDadosArquivosAgenda();
    var strJSONDadosTabelaParticipantes = fcFormatarDadosParticipantes();

    var v_tipo_agenda_pk = $("#tipo_agenda_pk").val();
    var v_dt_ini_agenda = $("#dt_ini_evento").val();
    var v_hr_ini_agenda = $("#hr_ini_evento").val();
    var v_dt_fim_agenda = $("#dt_fim_evento").val();
    var v_hr_fim_agenda = $("#hr_fim_evento").val();
    var v_ic_lembrete = $("#ic_lembrete").val();
    var v_ic_repetir = $("#ic_repetir").val();
    var v_ds_link_reuniao = $("#ds_link_reuniao").val();
    var v_leads_pk = $("#leads_pk_agenda").val();
    var v_agendas_pk = $("#agendas_pk").val();
    var v_ds_uf = $("#ds_uf").val();
    var v_ds_rua = $("#ds_rua").val();
    var v_ds_numero = $("#ds_numero").val();
    var v_ds_complemento = $("#ds_complemento").val();
    var v_ds_bairro = $("#ds_bairro").val();
    var v_ds_cidade = $("#ds_cidade").val();
    var v_ds_cep = $("#ds_cep").val();
    var v_ds_obs = $("#ds_obs_agenda").val();
    var v_ic_status = $("#ic_status").val();
    var v_motivo_cancelameto_pk = $("#motivo_cancelameto_pk").val();
    var v_classificacao_agenda_pk = $("#classificacao_agenda_pk").val();
    var v_obs_classificacao = $("#obs_classificacao").val();

    var objParametros = {
        "pk": v_agendas_pk,
        "tipo_agendas_pk": v_tipo_agenda_pk,
        "dt_ini_agenda": v_dt_ini_agenda,
        "hr_ini_agenda": v_hr_ini_agenda,
        "dt_fim_agenda": v_dt_fim_agenda,
        "hr_fim_agenda": v_hr_fim_agenda,
        "ic_lembrete": v_ic_lembrete,
        "ic_repetir": v_ic_repetir,
        "ds_link_reuniao": v_ds_link_reuniao,
        "leads_pk": v_leads_pk,
        "ds_uf": v_ds_uf,
        "ds_endereco": v_ds_rua,
        "ds_numero": v_ds_numero,
        "ds_complemento": v_ds_complemento,
        "ds_bairro": v_ds_bairro,
        "ds_cidade": v_ds_cidade,
        "ds_cep": v_ds_cep,
        "ds_obs": v_ds_obs,
        "ic_status": v_ic_status,
        "motivo_cancelameto_pk": v_motivo_cancelameto_pk,
        "classificacao_pk": v_classificacao_agenda_pk,
        "obs_classificacao": v_obs_classificacao,
        "doc_agenda":strJSONDadosTabelaArquivos,
        "participantes_agenda":strJSONDadosTabelaParticipantes
    };

    var arrEnviar = carregarController("agenda", "salvar", objParametros);
    if (arrEnviar.status == true){
        //Retorna para tela inicial do calendario
        utilsJS.toastNotify(true, arrEnviar.message);
        if($("#ic_abertura").val() == 1){
            sendPost('lead', 'leadMainPainel',{pk: v_leads_pk,ic_abertura: 1});

        }else{
            var objParametros = { };
            sendPost('agenda_calendario','receptivo' ,objParametros);
        }
    }
    else{

        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcFecharModalCadastroAgenda(){

    $('#event-modal').modal("hide");
    if($("#ic_abertura").val() == 1){

        sendPost('lead', 'leadMainPainel',{pk: $("#leads_pk_agenda").val(),ic_abertura:1});
    }else {
        var objParametros = { };
        sendPost('agenda_calendario','receptivo' ,objParametros);
    }
}

//Libera campos para adicionar novo endereço
function fcAdicionarNovoEndereco(){
    var htmlComboUf = "<select class='form-control form-control-sm'  id='ds_uf' name='ds_uf'>";
    htmlComboUf += "    <option></option>";
    htmlComboUf += "    <option>AC</option>";
    htmlComboUf += "    <option>AL</option>";
    htmlComboUf += "    <option>AP</option>";
    htmlComboUf += "    <option>AM</option>";
    htmlComboUf += "    <option>BA</option>";
    htmlComboUf += "    <option>CE</option>";
    htmlComboUf += "    <option>DF</option>";
    htmlComboUf += "    <option>ES</option>";
    htmlComboUf += "    <option>GO</option>";
    htmlComboUf += "    <option>MA</option>";
    htmlComboUf += "    <option>MT</option>";
    htmlComboUf += "    <option>MS</option>";
    htmlComboUf += "    <option>MG</option>";
    htmlComboUf += "    <option>PA</option>";
    htmlComboUf += "    <option>PB</option>";
    htmlComboUf += "    <option>PR</option>";
    htmlComboUf += "    <option>PE</option>";
    htmlComboUf += "    <option>PI</option>";
    htmlComboUf += "    <option>RJ</option>";
    htmlComboUf += "    <option>RN</option>";
    htmlComboUf += "    <option>RS</option>";
    htmlComboUf += "    <option>RO</option>";
    htmlComboUf += "    <option>RR</option>";
    htmlComboUf += "    <option>SC</option>";
    htmlComboUf += "    <option>SP</option>";
    htmlComboUf += "    <option>SE</option>";
    htmlComboUf += "    <option>TO</option>";
    htmlComboUf += "</select>";

    $("#ds_rua_html").html("Rua: <input id='ds_rua' class='form-control form-control-sm '>");
    $("#ds_numero_html").html("N°: <input id='ds_numero' class='form-control form-control-sm '>");
    $("#ds_complemento_html").html("Complemento: <input id='ds_complemento' class='form-control form-control-sm '>");
    $("#ds_bairro_html").html("Bairro: <input id='ds_bairro' class='form-control form-control-sm '>");
    $("#ds_cidade_html").html("Cidade: <input id='ds_cidade' class='form-control form-control-sm '>");
    $("#ds_cep_html").html("CEP: <input id='ds_cep' class='form-control form-control-sm '>");
    $("#ds_uf_html").html("UF: "+htmlComboUf);

}

//Abre modal cadastro
function fcAbrirFormAgenda(id, date, acao, leads_pk){
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
        $('#leads_pk_agenda').select2();
        fcCarregarInformacoesAgenda()
        tblParticipantesAgenda.clear().destroy();
        tblDocumentosAgenda.clear().destroy();
        fcGridAgendaParticipantes();
        fcCarregarGridDocumentosAgenda();
        if($("#leads_pk").val() != ""){
            fcCarregarEnderecosAgenda()
        }

        //dtAgenda(start);
        $('#event-modal').modal("show");
    } catch (error) {
        utilsJS.toastNotify(false, error);
    }
}

//Combos e carregamento de informações
function fcCarregarLeadsAgenda(){
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);
    carregarComboAjax($("#leads_pk_agenda"), arrCarregar, " ", "pk", "ds_lead");
    $("#leads_pk_agenda").val($("#leads_pk").val());
}

function fcCarregarEnderecosAgenda(){
    var objParametros = {
        "leads_pk": $("#leads_pk_agenda").val()
    };

    var arrCarregar = carregarController("lead", "listarEnderecos", objParametros);
    carregarComboAjax($("#endereco_pk"), arrCarregar, " ", "pk", "ds_endereco_completo");
}

function fcCarregarParticipante(){
    var objParametros = {
        "ic_tipo_participante": $('#tipo_participante_pk').val(),
        "leads_pk": $('#leads_pk_agenda').val()
    };

    var arrCarregar = carregarController("agendas_participantes", "carregarParicipantes", objParametros);
    carregarComboAjax($("#participante_pk"), arrCarregar, " ", "participante_pk", "ds_participante");
}

function fcCarregarDadosEnderecoAgenda(){
    var objParametros = {
        "leads_pk": $("#leads_pk_agenda").val()
    };

    var arrCarregar = carregarController("lead", "listarEnderecos", objParametros);
    var ds_rua = arrCarregar.data[0]['ds_endereco']==null?"":arrCarregar.data[0]['ds_endereco'];
    var ds_numero = arrCarregar.data[0]['ds_numero']==null?"":arrCarregar.data[0]['ds_numero'];
    var ds_complemento = arrCarregar.data[0]['ds_complemento']==null?"":arrCarregar.data[0]['ds_complemento'];
    var ds_bairro = arrCarregar.data[0]['ds_bairro']==null?"":arrCarregar.data[0]['ds_bairro'];
    var ds_cidade = arrCarregar.data[0]['ds_cidade']==null?"":arrCarregar.data[0]['ds_cidade'];
    var ds_cep = arrCarregar.data[0]['ds_cep']==null?"":arrCarregar.data[0]['ds_cep'];
    var ds_uf = arrCarregar.data[0]['ds_uf']==null?"":arrCarregar.data[0]['ds_uf'];
    $("#ds_rua_html").html("Rua: "+ds_rua);
    $("#ds_numero_html").html("N°: "+ds_numero);
    $("#ds_complemento_html").html("Complemento: "+ds_complemento);
    $("#ds_bairro_html").html("Bairro: "+ds_bairro);
    $("#ds_cidade_html").html("Cidade: "+ds_cidade);
    $("#ds_cep_html").html("CEP: "+ds_cep);
    $("#ds_uf_html").html("UF: "+ds_uf);
}

function fcCarregarInformacoesAgenda(){

    try {
        if($("#agendas_pk").val() > 0){
            var objParametros = {
                "pk": $("#agendas_pk").val()
            };
            var arrCarregar = carregarController("agenda", "listarPk", objParametros);

            var leads_pk = arrCarregar.data[0]['leads_pk']
            $('#leads_pk_agenda').val(leads_pk).trigger('change');
            $("#tipo_agenda_pk").val(arrCarregar.data[0]['tipo_agendas_pk']);
            $("#dt_ini_evento").val(arrCarregar.data[0]['dt_agenda_ini']);
            $("#hr_ini_evento").val(arrCarregar.data[0]['hr_agenda_ini']);
            $("#dt_fim_evento").val(arrCarregar.data[0]['dt_agenda_fim']);
            $("#hr_fim_evento").val(arrCarregar.data[0]['hr_agenda_fim']);
            $("#ic_lembrete").val(arrCarregar.data[0]['ic_lembrete']);
            $("#ic_repetir").val(arrCarregar.data[0]['ic_repetir']);
            $("#ds_link_reuniao").val(arrCarregar.data[0]['ds_link_reuniao']);
            $("#ds_obs_agenda").val(arrCarregar.data[0]['ds_obs']);
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);
            $("#motivo_cancelameto_pk").val(arrCarregar.data[0]['motivo_cancelameto_pk']);
            $("#classificacao_agenda_pk").val(arrCarregar.data[0]['classificacao_pk']);
            $("#obs_classificacao").val(arrCarregar.data[0]['obs_classificacao']);
            $("#div_motivo_cancelameto_pk").hide();
            if(arrCarregar.data[0]['ic_status'] == 3){
                $("#div_motivo_cancelameto_pk").show();
            }
            fcCarregarEnderecosAgenda();
            $("#endereco_pk").val(arrCarregar.data[0]['leads_pk']);

            if(arrCarregar.data[0]['ds_endereco']!=null){
                fcCarregarDadosEnderecoAgenda();
            }else{
                $("#ds_rua_html").html("");
                $("#ds_numero_html").html("");
                $("#ds_complemento_html").html("");
                $("#ds_bairro_html").html("");
                $("#ds_cidade_html ").html("");
                $("#ds_cep_html").html("");
                $("#ds_uf_html").html("");
            }

            if(arrCarregar.data[0]['tipo_agendas_pk'] == 1 || arrCarregar.data[0]['tipo_agendas_pk'] == 5){
                $("#div_endereco").show();
                $("#div_endereco_botao").show();
            }else if(arrCarregar.data[0]['tipo_agendas_pk'] == 2 ){
                $("#div_link_reuniao").show();
            }
        }
    } catch (error) {
        utilsJS.toastNotify(false, error);
    }


}

//Participantes
function fcGridAgendaParticipantes(){
    try {
        var objParametros = {
            "agendas_pk": $("#agendas_pk").val()
        };
        var v_url = routes_api("agendas_participantes", "listarDataTable", objParametros);
        //Trata a tabela
        tblParticipantesAgenda = $('#tblParticipantesAgenda').DataTable({
            searching: false,
            paging: false,
            scrollX: true,
            pageLength: 10,
            aLengthMenu: [10, 25, 50, 100],
            iDisplayLength: 10,
            processing: false,
            serverSide: false,
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
                        return full['t_ic_tipo_participante'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_participante_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_participante'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_cel'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_email'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        var buttonDelete = "<i class=bi bi-x-circle style='font-size:18px; color:blue' title='EXCLUIR O PARTICIPANTE'></i>";
                        return  buttonDelete;
                    },
                    'orderable': false,
                    'searchable': false,
                    width: '80px'
                }
            ]

        });

    } catch (error) {
        utilsJS.toastNotify(false, error);
    }

    $('#tblParticipantesAgenda tbody').on('click', '.function_delete', function () {
        var data;
        if(tblParticipantesAgenda.row( $(this).parents('li') ).data()){
            data = tblParticipantesAgenda.row( $(this).parents('li') ).data();
        }
        else if(tblParticipantesAgenda.row( $(this).parents('tr') ).data()){
            data = tblParticipantesAgenda.row( $(this).parents('tr') ).data();
        }
        tblParticipantesAgenda.row($(this).parents('tr')).remove().draw();
        fcExcluirParticipante(data['t_pk']);
    } );

}

function fcIncluirParticipante(){

    if($("#tipo_participante_pk").val()==""){
        $("#alert_tipo_participante_pk").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_tipo_participante_pk").slideUp(500);
        });
        $('#tipo_participante_pk').focus();
        return false;
    }

    if($("#participante_pk").val()==""){
        $("#alert_participante_pk").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_participante_pk").slideUp(500);
        });
        $('#participante_pk').focus();
        return false;
    }

    var objParametros = {
        "participante_pk": $("#participante_pk").val(),
        "ic_tipo_participante": $("#tipo_participante_pk").val()
    };
    var arrCarregar = carregarController("agendas_participantes", "carregarParicipantePorParticipantePk", objParametros);

    tblParticipantesAgenda.row.add({
        "t_pk":"",
        "t_ic_tipo_participante":$("#tipo_participante_pk").val(),
        "t_participante_pk":$("#participante_pk").val(),
        "t_ds_participante":$("#participante_pk option:selected").text(),
        "t_ds_email":arrCarregar.data[0]['ds_email'],
        "t_ds_cel":arrCarregar.data[0]['ds_cel'],
        "t_function":"<i class='bi bi-x-circle function_delete' style='font-size:18px; color:blue' title='EXCLUIR O PARTICIPANTE'></i>",
    }).draw(false);

    $("#participante_pk").val('');
    $("#tipo_participante_pk").val('');

    return false;
}


function fcExcluirParticipante(pk){
    if(pk > 0){
        var objParametros = {
            "pk": pk
        };
        var arrEnviar = carregarController("agendas_participantes", "excluir", objParametros);
        if (arrEnviar.status == true){
            utilsJS.toastNotify(true, arrEnviar.message);
        }
    }
}

function fcFormatarDadosParticipantes(){
    try {
        var participante_agenda_pk = "";
        var participante_pk = "";
        var ds_email = "";
        var ds_cel = "";
        var ic_tipo_participante = "";

        var arrKeys = [];
        arrKeys[0] = "participante_agenda_pk";
        arrKeys[1] = "ic_tipo_participante";
        arrKeys[2] = "participante_pk";
        arrKeys[3] = "ds_email";
        arrKeys[4] = "ds_cel";

        var arrDados = [];
        var  data = tblParticipantesAgenda.rows().data();


        for(i = 0; i< data.length; i++){//calcula o valor total
            var participante_agenda_pk = data[i]['t_pk'];
            var ic_tipo_participante = data[i]['t_ic_tipo_participante'];
            var participante_pk = data[i]['t_participante_pk'];
            var ds_email = data[i]['t_ds_email'];
            var ds_cel = data[i]['t_ds_cel'];
            arrDados[i] = [participante_agenda_pk, ic_tipo_participante, participante_pk, ds_email, ds_cel];
        }

        return arrayToJson(arrKeys, arrDados);
    } catch (error) {
        utilsJS.toastNotify(false, error);
    }


}

//Documentos
function fcIncluirLinhaArquivoAgenda(nome_original){
    tblDocumentosAgenda.row.add({
        "t_pk":$("#pk_documento_bd").text(),
        "t_ds_documento":$("#ds_documento_agenda").text(),
        "t_ds_nome_original":nome_original,
        "t_functions":"<i class='bi bi-cloud-arrow-up function_download' style='font-size:18px; color:blue' title='DOWNLOAD DOCUMENTO'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='bi bi-x-circle function_delete' style='font-size:18px; color:blue' title='EXCLUIR O DOCUMENTO'></i>"
    }).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcApagarArquivoAgenda);
    return false;
}

function fcFormatarDadosArquivosAgenda(){

    var dsDocumento = "";
    var dsNomeOriginal = "";

    var arrKeys = [];
    arrKeys[0] = "ds_documento";
    arrKeys[1] = "ds_nome_original";
    arrKeys[2] = "pk_doc_bd";

    var arrDados = [];
    var i = 0;
    $('#tblDocumentosAgenda tbody tr').each(function () {
        var colunas = $(this).children();
        pkDocBd = $(colunas[0]).text();
        dsDocumento =  $(colunas[1]).text();
        dsNomeOriginal = $(colunas[2]).text();



        arrDados[i] = [dsDocumento, dsNomeOriginal,pkDocBd];
        i++;
    });

    return arrayToJson(arrKeys, arrDados);
   

}

function fcAlterarNomeArquivoAgenda(v_arquivo){
    var objParametros = {
        "ds_arquivo": v_arquivo
    };

    var arrEnviar = carregarController("documento", "renomearArquivoAgenda", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        $("#ds_documento_agenda").text(arrEnviar.data);

    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcApagarArquivoAgenda(){
    var nome_arquivo = "";
    $('#tblDocumentosAgenda tbody tr').click(function () {
        var colunas = $(this).children();
        nome_arquivo = $(colunas[0]).text();
        fcExcluirArquivoAgenda(nome_arquivo);
    });

    tblDocumentosAgenda.row($(this).parents('tr')).remove().draw();
}

function fcExcluirArquivoAgenda(v_nome_arquivo){
    var objParametros = {
        "nome_arquivo": v_nome_arquivo
    };
    carregarController("documento", "removerArquivo", objParametros);
}

function fcCarregarGridDocumentosAgenda(){
    var objParametros = {
        "agendas_pk": $("#agendas_pk").val()
    };

    var v_url = routes_api("documento", "listarDocumentosAgenda", objParametros);
    //Trata a tabela
    tblDocumentosAgenda = $('#tblDocumentosAgenda').DataTable({
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
                    return full['t_ds_nome_original'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonDelete = "<i class='fa fa-download function_download' style='font-size:18px; color:blue' title='DOWNLOAD DOCUMENTO'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='bi bi-x-circle function_delete' style='font-size:18px; color:blue' title='EXCLUIR O DOCUMENTO'></i>";
                    return  buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]

    });
    $('#tblDocumentosAgenda tbody').on('click', '.function_download', function () {
        var data;

        if(tblDocumentosAgenda.row( $(this).parents('li') ).data()){
            data = tblDocumentosAgenda.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentosAgenda.row( $(this).parents('tr') ).data()){
            data = tblDocumentosAgenda.row( $(this).parents('tr') ).data();
        }

        fcDownloadDocumentoAgenda(data['t_ds_documento'],data['pk_doc_bd']);

    });
    $('#tblDocumentosAgenda tbody').on('click', '.function_delete', function () {
        var data;

        if(tblDocumentosAgenda.row( $(this).parents('li') ).data()){
            data = tblDocumentosAgenda.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentosAgenda.row( $(this).parents('tr') ).data()){
            data = tblDocumentosAgenda.row( $(this).parents('tr') ).data();
        }

        if(data['t_pk'] != ""){
            fcExcluirDocumentoAgenda(data['t_pk'],data['t_ds_documento'],data['pk_doc_bd']);
        }
    });
}

function fcDownloadDocumentoAgenda(ds_documento,pk_doc_bd){
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

function fcExcluirDocumentoAgenda(v_pk,v_ds_documento,v_pk_doc){
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
            fcExcluirArquivoAgenda(v_ds_documento);
            tblDocumentosAgenda.clear().destroy();
            fcCarregarGridDocumentosAgenda();
        }
        else{
            utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
        }
    }
    else{
        sweetMensagem('warning','Código não encontrado');
    }
}

function fcExcluirAgenda(){
    if($("#agendas_pk").val() != ""){

        var objParametros = {
            "pk": $("#agendas_pk").val()
        };

        var arrExcluir = carregarController("agenda", "excluir", objParametros);

        if (arrExcluir.status == true){

            //Exibe a mensagem
            utilsJS.toastNotify(true,arrExcluir.message);
            fcFecharModalCadastroAgenda();
            tblAgenda.ajax.reload();
        }
        else{
            utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
        }
    }
    else{
        sweetMensagem('warning','Código não encontrado');
    }
}
function fcSalvarDocumentos(formdata){
    var url = "";


    url = "/documento/salvarDocumento";


    var arrRetornoCarregarControle;

    var request = $.ajax({
        url:          url,
        data:         formdata,
        processData:  false,
        cache:        false,
        async:        false,
        dataType:     'json',
        contentType:  false,
        type:         'post'
    });
    request.done(function(output){
        if (output.status == true){
            $("#pk_documento_bd").text(output.data);
        }else{
            utilsJS.toastNotify(false, 'Falhou a requisição: '+output.message);
        }
    });
    request.fail(function(jqXHR, textStatus){
        utilsJS.toastNotify(false, 'Falhou a requisição: '+textStatus);
    });
}
var formdata = null;
$(document).ready(function(){

    formdata = new FormData();
    $('#fileuploadAgenda').change(function(){
        //on change event
        if($(this).prop('files').length > 0){
            $.each($(this).prop('files'), function (index, file) {
                formdata.append(index, file);
                fcSalvarDocumentos(formdata);

                $("#ds_nome_original_Agenda").html(file.name);

                fcAlterarNomeArquivoAgenda(file.name);
                fcIncluirLinhaArquivoAgenda(file.name);

            });

        }
    });



    //Formatações
    //Datas
    $('#dt_ini_evento').datepicker({
        dateFormat: 'dd/mm/yy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked"
    });
    $("#dt_ini_evento").keypress(function(){
        mascara(this,mdata);
    });
    $('#dt_fim_evento').datepicker({
        dateFormat: 'dd/mm/yy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked"
    });
    $("#dt_fim_evento").keypress(function(){
        mascara(this,mdata);
    });
    $("#hr_ini_evento").keypress(function(){
        mascara(this,horamask);
    });
    //Hora
    $("#hr_fim_evento").keypress(function(){
        mascara(this,horamask);
    });
    //Carregar
    //Grids
    fcGridAgendaParticipantes()
    fcCarregarGridDocumentosAgenda();

    $("#ic_status").change(function(){
        $("#div_motivo_cancelameto_pk").hide();
        if($("#ic_status").val() == 3){
            $("#div_motivo_cancelameto_pk").show();
        }
    });

    //Combos e informações gerais
    $("#leads_pk_agenda").change(function(){
        $("#ds_rua_html").html("");
        $("#ds_numero_html").html("");
        $("#ds_complemento_html").html("");
        $("#ds_bairro_html").html("");
        $("#ds_cidade_html").html("");
        $("#ds_cep_html").html("");
        $("#ds_uf_html").html("");
        fcCarregarEnderecosAgenda();
    });
    $("#endereco_pk").change(function(){
        fcCarregarDadosEnderecoAgenda();
    });
    $("#tipo_participante_pk").change(function(){
        if($("#tipo_participante_pk").val() == 1){
            if($("#leads_pk_agenda").val()==""){
                $("#tipo_participante_pk").val("");
                $("#alert_leads_pk_agenda").fadeTo(2000, 500).slideUp(500, function(){
                    $("#alert_leads_pk_agenda").slideUp(500);
                });
                $('#leads_pk_agenda').focus();
                return false;
            }
        }
        fcCarregarParticipante();
    });

    $("#tipo_agenda_pk").change(function(){
        $("#div_link_reuniao").hide();
        $("#div_endereco").hide();
        $("#div_endereco_botao").hide();
        var tipo_agenda_pk = $("#tipo_agenda_pk").val()
        if(tipo_agenda_pk == 1 || tipo_agenda_pk == 5){
            $("#div_endereco").show();
            $("#div_endereco_botao").show();
        }else if(tipo_agenda_pk == 2){
            $("#div_link_reuniao").show();
        }
    });
    //Botões
    $("#cmdFecharAgenda").click(function(){
        fcFecharModalCadastroAgenda();
    });
    $("#cmdIncluirParticipante").click(function(){
        fcIncluirParticipante();
    });
    $("#cmdSalvarNovoEndereço").click(function(){
        fcAdicionarNovoEndereco();
    });
    $("#cmdEnviarAgenda").click(function(){
        fcValidarFormAgenda();
    });
    $("#cmdEnviarAgenda1").click(function(){
        fcValidarFormAgenda();
    });

    $("#cmdExcluirAgenda").click(function(){
        fcExcluirAgenda();
    });

    $('#leads_pk_agenda').select2();


});
