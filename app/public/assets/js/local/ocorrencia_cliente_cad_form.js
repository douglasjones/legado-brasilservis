function fcAbrirFormNovaOcorrencia(){
    $('#doc').show();
    $('#tipo_ocorrencia_pk').prop('disabled',true);
    $("#ocorrencias_pk").val("");
    $("#ds_ocorrencia").val("");
    $("#tipo_ocorrencia_pk").val("");
    $('#tipo_ocorrencia_pk').prop('disabled', false);
    $('#dt_fechamento').prop('checked', false);
    $('#ic_docs').prop('checked', false);
    $('#ic_docs').prop('disabled', false);

    //AGENDA RETORNO
    $("#agenda_visible").hide();
    $("#agenda_retorno_pk").val("");

    $("#edit_agenda_visible").hide();
    $("#agenda_equipe_visible").hide();
    $("#agenda_responsavel_visible").hide();
    $('#agenda_retorno').prop('checked', false);
    $('#agenda_retorno').prop('disabled', true);
    $("#agenda_dt_retorno").val("");
    $("#agenda_hr_retorno").val("");
    $("#agenda_ic_agendar_para").val("");
    $("#agenda_equipes_pk").val("");
    $("#agenda_responsavel_pk").val("");
    $("#agenda_ds_retorno").val("");
    $("#tipo_lembrete_pk").val("");
    $("#edit_tipo_lembrete_pk").val("");
    $("#dt_prazo_execucao").val("");
    //EDIÇÃO AGENDA

    $("#edit_agenda_dt_retorno").html("");
    $('#edit_agenda_responsavel_pk').prop('disabled', false);
    $("#edit_agenda_equipes_pk").val("");
    $("#edit_agenda_dt_retorno_termino").val("");
    $("#edit_agenda_hr_retorno_termino").val("");
    $("input[id=edit_agenda_dt_retorno_termino]").prop("disabled", false);
    $("input[id=edit_agenda_hr_retorno_termino]").prop("disabled", false);
    $('#edit_agenda_equipes_pk').prop('disabled', false);
    $("#edit_agenda_responsavel_pk").val("");
    $("#edit_agenda_ds_retorno").html("");

    $("#agenda_ds_retorno").html("");
    setTimeout(function(){
        tblDocumentosOc.clear().destroy();
        fcCarregarGridDocumentosOC();
    }, 2000);

    $('#doc').hide();
    $("#janela_ocorrencia").modal("show");



}

function fcCarregarOcorrencia(ocorrencias_pk){
    if(ocorrencias_pk > 0){
        var objParametros = {
            "pk": ocorrencias_pk
        };

        var arrCarregar = carregarController("ocorrencia", "listarPorPk", objParametros);

        if (arrCarregar.status == true){

            fcCarregarLeadsOcorrencia();
            $('#cmdEnviarOcorrencia').prop('disabled', false);
            $('#cmdEnviarOcorrencia2').prop('disabled', false);
            $("#ocorrencias_pk").val(arrCarregar.data[0]['t_pk']);
            $("#leads_pk_ocorrencia").val(arrCarregar.data[0]['t_leads_pk']).trigger('change');
            $("#tipo_ocorrencia_pk").val(arrCarregar.data[0]['t_tipos_ocorrencias_pk']);
            $('#tipo_ocorrencia_pk').prop('disabled', true);
            $("#ds_ocorrencia").val(arrCarregar.data[0]['t_ds_ocorrencia']);
            $("#obs_execucao").val(arrCarregar.data[0]['t_obs_execucao']);
            $("#obs_status").val(arrCarregar.data[0]['t_obs_recusa']);
            $("#motivo_sem_interesse_pk").val(arrCarregar.data[0]['motivo_sem_interesse_pk']);
            $("#ds_motivo_sem_interesse").val(arrCarregar.data[0]['ds_motivo_sem_interesse']);
            $("#processos_etapas_pk").val(arrCarregar.data[0]['processos_etapas_pk']);
            $('#colaborador_pk_ocorrencia').val(arrCarregar.data[0]['t_colaborador_pk']).trigger('change');

            $("#dt_prazo_execucao").val(arrCarregar.data[0]['t_dt_prazo_execucao']);

            if(arrCarregar.data[0]['t_dt_fechamento']!=null){
                $("input[id=dt_fechamento]").prop("checked", "true");
                $('#ds_ocorrencia').prop('disabled', true);
                $('#cmdEnviarOcorrencia').prop('disabled', true);
                $('#cmdEnviarOcorrencia2').prop('disabled', true);
            }

            $("#dt_fechamento").val(arrCarregar.data[0]['t_dt_fechamento']);

            $("#motivo_sem_interesse_pk").val(arrCarregar.data[0]['motivo_sem_interesse_pk']);
            $("#ds_motivo_sem_interesse").val(arrCarregar.data[0]['ds_motivo_sem_interesse']);
            $("#processos_etapas_pk").val(arrCarregar.data[0]['processos_etapas_pk']);

            //Carrega as informações da linha selecionada.
            if(arrCarregar.data[0]['t_dt_fechamento']!=null){
                $("input[id=dt_fechamento]").prop("checked", "true");
                $('#dt_fechamento').prop('disabled', true);
                $("#sem_interesse").hide();
            }

            if(arrCarregar.data[0]['t_motivo_sem_interesse_pk']!="" && arrCarregar.data[0]['t_motivo_sem_interesse_pk']!=null){
                $("#sem_interesse").show();
                $('#motivo_sem_interesse_pk').prop('disabled', true);
                $("#motivo_sem_interesse_pk").val(arrCarregar.data[0]['t_motivo_sem_interesse_pk']);
                $("#ds_motivo_sem_interesse").val(arrCarregar.data[0]['t_ds_motivo_sem_interesse']);
            }

            //carrega agenda retorno
            fcEditarRetorno(arrCarregar.data[0]['t_pk']);
            var qtdDocumentos = fcQtdeDocumentosOcorrenciaPk()
            if(qtdDocumentos > 0){
                $('#ic_docs').prop('checked', true);
                if($('#ic_docs').is(":checked")){
                    $('#doc').show();
                    tblDocumentosOc.clear().destroy();
                    fcCarregarGridDocumentosOC();
                }
            }
        }
        else{
            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
        }
    }
}
function fecharModalOcorrencia(){
    $("#janela_ocorrencia").modal("hide")
}
//FINAL DOCUMENTOS UPLOAD
function fcEditarRetorno(ocorrencias_pk){

    if(ocorrencias_pk > 0){
        $("#ocorrencias_pk").val(ocorrencias_pk);
        var objParametros = {
            "ocorrencias_pk": ocorrencias_pk
        };

        var arrCarregar = carregarController("retorno", "listarOcorrenciasPk", objParametros);

        if (arrCarregar.status == true){
            if(arrCarregar.data.length > 0){

                $("input[id=agenda_retorno]").prop("checked", "true");
                $("input[id=agenda_retorno]").prop("disabled", "true");
                $("#edit_agenda_dt_retorno").html(arrCarregar.data[0]['dt_retorno']);
                $("#edit_agenda_hr_retorno").html(arrCarregar.data[0]['hr_retorno']);
                $("#agenda_ds_retorno").val(arrCarregar.data[0]['ds_retorno']);
                $("#tipo_lembrete_pk").val(arrCarregar.data[0]['tipo_lembrete_pk']);
                $("#edit_tipo_lembrete_pk").val(arrCarregar.data[0]['tipo_lembrete_pk']);
                $('#agenda_ds_retorno').prop('disabled', false);
                $('#dt_termino_retorno').prop('checked', false);
                $("input[id=dt_termino_retorno]").prop("disabled", false);

                $("#agenda_retorno_pk").val(arrCarregar.data[0]['pk']);

                if(arrCarregar.data[0]['dt_termino_retorno']!=null){
                    $('#dt_termino_retorno').prop('checked', true);
                    $("input[id=dt_termino_retorno]").prop("disabled", "true");

                    //descrição do retorno
                    $('#agenda_ds_retorno').prop('disabled', true);

                    //Desabilita o fechamento da Ocorrencia
                    $("input[id=dt_fechamento]").prop("disabled", "true");

                }

                if(arrCarregar.data[0]['equipes_pk']!= null && arrCarregar.data[0]['responsavel_pk']==null){

                    fcCarregarComboResponsavelEquipe(arrCarregar.data[0]['responsavel_pk']);
                    $("#edit_agenda_responsavel_pk").val(arrCarregar.data[0]['responsavel_pk']);
                    fcCarregarComboEquipeEdit();
                    $("#edit_agenda_equipes_pk").val(arrCarregar.data[0]['equipes_pk']);
                    $("select[id=edit_agenda_equipes_pk]").prop("disabled", "true");
                }else{

                    fcCarregarComboResponsavelEquipe(arrCarregar.data[0]['equipes_pk']);

                    $("#edit_agenda_responsavel_pk").val(arrCarregar.data[0]['responsavel_pk']);

                    $("select[id=edit_agenda_responsavel_pk]").prop("disabled", "true");
                    $("select[id=edit_agenda_equipes_pk]").prop("disabled", "true");
                }

                $("#edit_agenda_visible").show();
            }
            else{

                $('#agenda_retorno').prop('checked', false);
                $("#agenda_retorno").prop("disabled", false);

                $("#edit_agenda_visible").hide();
            }


        }
        else{
            utilsJS.toastNotify(false,"Falha ao carregar registro.")
        }
    }
}
function fcEditRetornoFechaOC(){

    if($('#dt_termino_retorno').is(":checked")){
        $('#dt_fechamento').prop('disabled', false);

    }else{
        $('#dt_fechamento').prop('disabled',true);
        $('#dt_fechamento').prop('checked', false);
    }
}

// Validaão de OC
function fcValidarFormOcorrencia(){

    $("#form_ocorrencia").validate({
        rules :{
            ds_ocorrencia:{
                required:true
            },
            tipo_ocorrencia_pk:{
                required:true
            }
        },
        messages:{
            ds_ocorrencia:{
                required:"Por favor, informe Ocorrência"
            },
            tipo_ocorrencia_pk:{
                required:"Por favor, informe Tipo ocorrência"
            }
        },
        submitHandler: function(form){

            fcEnviarOcorrencia(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });
}

function fcEnviarOcorrencia(){
    try {



        var strJSONDadosTabela =  fcFormatarDadosArquivosOc();
        var v_ds_ocorrencia = $("#ds_ocorrencia").val();
        var v_tipo_ocorrencia_pk = $("#tipo_ocorrencia_pk").val();


        var objParametros = {
            "leads_pk": $("#leads_pk_ocorrencia").val(),
            "pk": $("#ocorrencias_pk").val(),
            "ds_ocorrencia":v_ds_ocorrencia,
            "tipos_ocorrencias_pk":v_tipo_ocorrencia_pk,
            "doc_oc":strJSONDadosTabela
        };

        var arrEnviar = carregarController("ocorrencia", "salvar", objParametros);

        if (arrEnviar.status == true){
            // Reload datable
            utilsJS.toastNotify(true,arrEnviar.message)
            tblOcorrencia.ajax.reload();
            $("#janela_ocorrencia").modal("hide");

        }
        else{
            utilsJS.toastNotify(false,"Falhou a requisição para salvar o registro")
        }
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
}

function fcQtdeDocumentosOcorrenciaPk(){
    var objParametros = {
        "ocorrencias_pk": $("#ocorrencias_pk").val()
    };
    var arrCarregar = carregarController("documento", "listarQtdeDocumentosOc", objParametros);
    if (arrCarregar.status == true){
        return arrCarregar.data.length;
    }

}

function fcCarregarTipoOcorrencia(){
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("tipo_ocorrencia", "listarTodos", objParametros);
    carregarComboAjax($("#tipo_ocorrencia_pk"), arrCarregar, " ", "pk", "ds_tipo_ocorrencia");

}

function fcIncluirLinhaArquivoOc(nome_original){
    tblDocumentosOc.row.add(
        {
            "t_pk": $("#pk_documento_bd").text(),
            "t_ds_documento":$("#ds_documento_oc").text(),
            "t_ds_nome_original":nome_original,
            "t_functions":"<a class='function_delete'><span><img width=16 height=16 src='../img/excluir.png'></span></a>"
        }
    ).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcApagarArquivoOc);
    return false;
}


function fcFormatarDadosArquivosOc(){

    var DocOcPk = "";
    var dsDocumento = "";
    var dsNomeOriginal = "";

    var arrKeys = [];
    arrKeys[0] = "doc_oc_pk";
    arrKeys[1] = "ds_documento";
    arrKeys[2] = "ds_nome_original";

    var arrDados = [];
    var i = 0;
    $('#tblDocumentosOc tbody tr').each(function () {
        var colunas = $(this).children();
        DocOcPk =  $(colunas[0]).text();
        dsDocumento =  $(colunas[1]).text();
        dsNomeOriginal = $(colunas[2]).text();


        arrDados[i] = [DocOcPk,dsDocumento, dsNomeOriginal];
        i++;
    });

    return arrayToJson(arrKeys, arrDados);

}

function fcAlterarNomeArquivoOc(v_arquivo){
    var objParametros = {
        "leads_pk": $("#leads_pk_ocorrencia").val(),
        "ds_arquivo": v_arquivo
    };

    var arrEnviar = carregarController("documento", "renomearArquivo", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        $("#ds_documento_oc").text(arrEnviar.data);

    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcApagarArquivoOc(){
    var nome_arquivo = "";
    $('#tblDocumentosOc tbody tr').click(function () {
        var colunas = $(this).children();
        nome_arquivo = $(colunas[0]).text();
        fcExcluirArquivoOc(nome_arquivo);
    });

    tblDocumentosOc.row($(this).parents('tr')).remove().draw();
}


function fcExcluirArquivoOc(v_nome_arquivo){
    var objParametros = {
        "nome_arquivo": v_nome_arquivo
    };
    carregarController("documento", "removerArquivo", objParametros);
}

function fcCarregarGridDocumentosOC(){
    var objParametros = {
        "ocorrencias_pk": $("#ocorrencias_pk").val()
    };

    var v_url = routes_api("documento", "listarDocumentosOc", objParametros);

    //Trata a tabela
    tblDocumentosOc = $('#tblDocumentosOc').DataTable({
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
                    var buttonDelete = "<i class='fa fa-download function_download' style='font-size:18px; color:blue' title='DOWNLOAD DOCUMENTO'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='bi bi-x-circle' style='font-size:18px; color:blue' title='EXCLUIR O DOCUMENTO'></i>";
                    return  buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]
    });
    $('#tblDocumentosOc tbody').on('click', '.function_download', function () {
        var data;

        if(tblDocumentosOc.row( $(this).parents('li') ).data()){
            data = tblDocumentosOc.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentosOc.row( $(this).parents('tr') ).data()){
            data = tblDocumentosOc.row( $(this).parents('tr') ).data();
        }

        if(data['t_pk'] != ""){
            fcDownloadDocumentoOc(data['pk_doc_bd'],data['t_ds_documento']);
        }
    });
    $('#tblDocumentosOc tbody').on('click', '.function_delete', function () {
        var data;

        if(tblDocumentosOc.row( $(this).parents('li') ).data()){
            data = tblDocumentosOc.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentosOc.row( $(this).parents('tr') ).data()){
            data = tblDocumentosOc.row( $(this).parents('tr') ).data();
        }
        if(data['t_pk'] != ""){
            fcExcluirDocumentoOc(data['t_pk'],data['t_ds_documento'],data['pk_doc_bd']);
        }
    });
}

function fcDownloadDocumentoOc(pk_doc_bd,ds_documento){


    //var url_documento = (window.location.protocol+"//"+window.location.host+"/app/src/docs/"+ds_documento)

    //DOWNLOAD
    var v_url = "/documento/download?pk_doc_bd="+pk_doc_bd+"&ds_documento="+ds_documento;

    window.open(v_url, '_blank');
}

function fcExcluirDocumentoOc(v_pk,v_ds_documento,v_pk_doc){

    if(v_pk != ""){

        var objParametros = {
            "pk": v_pk,
            "pk_doc_bd":v_pk_doc
        };

        var arrExcluir = carregarController("documento", "excluir", objParametros);

        if (arrExcluir.status == true){

            //Exibe a mensagem
            utilsJS.toastNotify(true,arrExcluir.message);
            fcExcluirArquivoOc(v_ds_documento);
            tblDocumentosAgenda.clear().destroy();
            fcCarregarGridDocumentosAgenda();
        }
        else{
            utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
        }
    }
    else{
        utilsJS.toastNotify(false,'Código não encontrado');
    }
}

function fcMostrarDocumento(){
    if($('#ic_docs').is(":checked")){
        $('#doc').show();
        tblDocumentosOc.clear().destroy();
        fcCarregarGridDocumentosOC();
    }
    else{
        $('#doc').hide();
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


function fcCarregarLeadsOcorrencia(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listarTodosClientes", objParametros);

    if($("#leads_pk").val()==undefined){
        carregarComboAjax($("#leads_pk_ocorrencia"), arrCarregar, " ", "pk", "ds_lead");
        $("#leads_pk_ocorrencia").select2();
    }
    else{
        carregarComboAjax($("#leads_pk_ocorrencia"), arrCarregar, "", "pk", "ds_lead");
        $("#leads_pk_ocorrencia").val($("#leads_pk").val());
        $("#leads_pk_ocorrencia").prop("disabled",true);
    }
}
var formdata = null;
$(document).ready(function(){

    fcCarregarLeadsOcorrencia();

    formdata = new FormData();
    $('#fileuploadOc').change(function(){
        //on change event
        if($(this).prop('files').length > 0){
            $.each($(this).prop('files'), function (index, file) {
                formdata.append(index, file);
                fcSalvarDocumentos(formdata);

                $("#ds_nome_original_Agenda").html(file.name);
                fcAlterarNomeArquivoOc(file.name);
                fcIncluirLinhaArquivoOc(file.name);

            });

        }
    });
    $(document).on('click', '#ic_docs', fcMostrarDocumento);
    $(document).on('click', '#cmdEnviarOcorrencia', fcValidarFormOcorrencia);
    $(document).on('click', '#cmdEnviarOcorrencia2', fcValidarFormOcorrencia);

    fcCarregarTipoOcorrencia();

    //Valida Campos Ocorrencia
    fcValidarFormOcorrencia();

    fcCarregarGridDocumentosOC();

    $('#doc').hide();

});
