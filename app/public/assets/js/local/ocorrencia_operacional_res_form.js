var tblOcorrencia;
function fcPesquisar(){
    tblOcorrencia.clear().destroy();
    fcCarregarGrid();
}

function fcExcluirOcorrencia(v_pk){
    var arrCarregar = permissao("ocorrencia", "del");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false, 'Você não tem permissão!');
        return false;
    }
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_pk+'?', function () {
        if(v_pk != ""){
            var objParametros = {
                "pk": v_pk
            };
            var arrExcluir = carregarController("ocorrencia", "excluir", objParametros);
            if (arrExcluir.status == true){
                //Exibe a mensagemd
                utilsJS.toastNotify(true,arrExcluir.message);
                // Reload datable
                tblOcorrencia.ajax.reload();
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

function fcCarregarGrid(){

    var objParametros = {
        "ds_lead": $("#ds_lead option:selected").text(),
        "tipos_ocorrencias_pk": $("#tipo_ocorrencia_res_pk").val(),
        "ic_status": $("#ic_status").val(),
        "usuario_cadastro_pk": $("#usuario_cadastro_res_pk").val(),
        "dt_cadastro": $("#dt_cadastro").val(),
        "dt_prazo_execucao_ini": $("#dt_prazo_execucao_ini").val(),
        "dt_prazo_execucao_fim": $("#dt_prazo_execucao_fim").val(),
        "ic_status_fechamento": $("#ic_status_fechamento_pesq").val(),
        "equipes_pk": $("#equipes_pk_res").val(),
        "dt_cadastro_fim": $("#dt_cadastro_fim").val(),
        "colaborador_pk":$("#colaborador_pk_pesq").val()
    };


    var v_url = routes_api("ocorrencia", "listarDataTableGrid", objParametros);

    //Trata a tabela
    tblOcorrencia = $('#tblOcorrencia').DataTable({
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
                    return full['t_pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_lead'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_colaborador'];
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
                    return full['t_dt_prazo_execucao'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_obs_execucao'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_status'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_fechamento'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_tipo_ocorrencia'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_ocorrencia'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_nome_usuario_cadastro'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_agendado_para'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_dt_retorno'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            },{
                mRender: function (data, type, full) {
                    return full['t_ds_retorno'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            }
            ,{
                mRender: function (data, type, full) {
                    return full['t_dt_termino_retorno'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            }
            ,{
                mRender: function (data, type, full) {
                    return full['t_obs_recusa'];
                },
                'orderable': true,
                'searchable': false,
                width: '60px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_painel"><span><i class="fa fa-address-card" style="font-size=18px;color:blue" title="Painel"></i></span></a> &nbsp;&nbsp;';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> &nbsp;&nbsp;';
                    var buttonEdit = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="editar"></i></span></a> ';

                    return buttonEdit +buttonPainel + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '60px'
            }
        ],
        rowCallback: function (row, data) {
            if(data.t_ds_status=="Não lido"){
                $(row).css("background-color", "#FFFF00");;

            }
            if(data.t_ds_status=="Dentro do prazo"){
                $(row).css("background-color", "#1dc2ff");
            }
            if(data.t_ds_status=="Chamado atrasado"){
                $(row).css("background-color", "#FF4500");
                $(row).css("color", "#FFFFFF");
            }
            if(data.t_ds_status=="Chamado recusado"){
                $(row).css("background-color", "#fab14c");
            }
            if(data.t_ds_status=="Finalizado"){
                $(row).css("background-color", "#47e51f");
            }
            //console.log(data.HORAS >= data.SLA);
            //$(row).attr("data-message-id", data.MAIL_MESSAGE_ID);
            //$(row).attr("data-id-email-conversa", data.ID_EMAIL_CONVERSA);
            //$('div.dataTables_filter input').addClass('form-control');
        },
    });

    $('#tblOcorrencia tbody').on('click', '.function_delete', function () {

        var data;

        if(tblOcorrencia.row( $(this).parents('li') ).data()){
            data = tblOcorrencia.row( $(this).parents('li') ).data();
        }
        else if(tblOcorrencia.row( $(this).parents('tr') ).data()){
            data = tblOcorrencia.row( $(this).parents('tr') ).data();
        }

        if(data['t_pk'] != ""){
            fcExcluirOcorrencia(data['t_pk']);
        }
    } );

    $('#tblOcorrencia tbody').on('click', '.function_edit', function () {

        var data;

        rLinhaSelecionada = null;

        if(tblOcorrencia.row( $(this).parents('li')).data()){
            data = tblOcorrencia.row( $(this).parents('li')).data();
            rLinhaSelecionada = $(this).parents('li');
        }
        else if(tblOcorrencia.row( $(this).parents('tr')).data()){
            data = tblOcorrencia.row( $(this).parents('tr')).data();
            rLinhaSelecionada = $(this).parents('tr');
        }
        fcEditarOcorrencia(data);
    } );
    $('#tblOcorrencia tbody').on('click', '.function_painel', function () {
        var data;
        if(tblOcorrencia.row( $(this).parents('li') ).data()){
            data = tblOcorrencia.row( $(this).parents('li') ).data();
        }
        else if(tblOcorrencia.row( $(this).parents('tr') ).data()){
            data = tblOcorrencia.row( $(this).parents('tr') ).data();
        }
        fcAbrirPainel(data['t_leads_pk']);
    } );

}
function fcEditarOcorrencia(objRegistro){
    $(".chzn-select").chosen('destroy');
    var arrCarregar = permissao("ocorrencia", "upd");
    if (arrCarregar.status != true){
        utilsJS.toastNotify(false, 'Você não tem permissão!');
        return false;
    }
    fcAbrirFormNovaOcorrencia();
    $("#contatos_pk").val("");
    $("#acao").val("upd");

    $("#ocorrencias_pk").val(objRegistro['t_pk']);
    $(".chzn-select").chosen('destroy');
    fcCarregarOcorrencia(objRegistro['t_pk']);
    //$(".chzn-select").chosen({allow_single_deselect: true});
}

function fcAbrirPainel(leads_pk){
    sendPost('lead', 'leadMainPainel',{pk: leads_pk});
}


function fcLimparVariavelEnvioEmail(){
    $("#dt_ocorrencia").val("");
    $("#ds_tipo_oc").val("");
    $("#ds_oc").val("");
    $("#dt_termino_oc").val("");
}

function fcCarregarTipoOcorrenciaRes(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("tipo_ocorrencia", "listarTodos", objParametros);
    carregarComboAjax($("#tipo_ocorrencia_res_pk"), arrCarregar, " ", "pk", "ds_tipo_ocorrencia");
}
function fcCarregarComboUsuarioRes(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("usuario", "listarTodos", objParametros);
    carregarComboAjax($("#usuario_cadastro_res_pk"), arrCarregar, " ", "pk", "ds_usuario");
}
function fcCarregarComboEquipeRes(){
    var objParametros = {
        "pk": ""
    };


    var arrCarregarLogado = carregarController("equipe", "listarEquipeUsuarioLogado", objParametros);

    if(arrCarregarLogado.data.length > 0){
        carregarComboAjax($("#equipes_pk_res"), arrCarregarLogado, " ", "pk", "ds_equipe");
    }
    else{
        var arrCarregar = carregarController("equipe", "listarTodos", objParametros);
        carregarComboAjax($("#equipes_pk_res"), arrCarregar, " ", "pk", "ds_equipe");
    }


}


function fcCarregarLeads(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listarTodos", objParametros);

    carregarComboAjax($("#ds_lead"), arrCarregar, " ", "pk", "ds_lead");
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");
}


function fcCarregarComboColaboradorOcorrenciaPesq(){

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);
    carregarComboAjax($("#colaborador_pk_pesq"), arrCarregar, " ", "pk", "ds_colaborador");

}

function cmdVoltar(){
    sendPost('menu','operacional',{});
}
$(document).ready(function(){
    var arrCarregar = permissao("ocorrencia", "cons");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false, 'Você não tem permissão!');
        return false;
    }
    fcCarregarLeads();
    fcCarregarComboColaboradorOcorrenciaPesq();

    fcCarregarComboEquipeRes();
    $(".chzn-select").chosen({allow_single_deselect: true});

    //carrega cadastro ini
    $('#dt_cadastro').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_cadastro").keypress(function(){
        mascara(this,mdata);
    });


    //carrega cadastro fim
    $('#dt_cadastro_fim').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_cadastro_fim").keypress(function(){
        mascara(this,mdata);
    });
    $('#dt_prazo_execucao_ini').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_prazo_execucao_ini").keypress(function(){
        mascara(this,mdata);
    });
    $('#dt_prazo_execucao_fim').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_prazo_execucao_fim").keypress(function(){
        mascara(this,mdata);
    });

    fcCarregarTipoOcorrenciaRes();
    fcCarregarComboUsuarioRes();
    //faz a carga inicial do grid
    fcCarregarGrid();



    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdVoltar', cmdVoltar);
    $(document).on('click', '#cmdIncluir', fcAbrirFormNovaOcorrencia);


    $(document).on('click', '#dt_termino_retorno', fcEditRetornoFechaOC);

    


    //AGENDA RETORNO

    $('#dt_prazo_execucao').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", new Date() );
    $("#dt_prazo_execucao").keypress(function(){
        mascara(this,mdata);
    });

    //carrega datepicker com a data atual (Agenda)
    $('#agenda_dt_retorno').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", new Date() );
    $("#agenda_dt_retorno").keypress(function(){
        mascara(this,mdata);
    });
    $("#agenda_hr_retorno").keypress(function(){
        mascara(this,horamask);
    });

    $('#edit_agenda_dt_retorno_termino').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate", new Date() );
    $("#edit_agenda_dt_retorno_termino").keypress(function(){
        mascara(this,mdata);
    });
    $("#edit_agenda_hr_retorno_termino").keypress(function(){
        mascara(this,horamask);
    });



    //EXIBE O COMBO DE AGENDA DE ROTORNO DE USUARIOS E EQUIPES
    $('#ic_equipe').click(function() {
        $('#ic_equipe').prop('checked', true);
        $('#ic_usuario').prop('checked', false);
        $('#agenda_responsavel_visible').hide();
        $('#agenda_equipe_visible').show();
    });
    $('#ic_usuario').click(function() {
        $('#ic_usuario').prop('checked', true);
        $('#ic_equipe').prop('checked', false);
        $('#agenda_responsavel_visible').show();
        $('#agenda_equipe_visible').hide();
    });

    //CARREGA COMBO USUARIO E EQUIPE AGENDA
    fcCarregarComboEquipe();
    fcCarregarComboUsuario();


});


