let count = 0;
function fcComboLeads() {
    var v_leads_pk = "";
    var objParametros = {
        "leads_pk": v_leads_pk
    };
    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);

    carregarComboAjax($("#leads_pk"), arrCarregar, "", "pk", "ds_lead");
}
function fcColaborador() {

    var objParametros = {
        "leads_pk": $("#leads_pk").val()
    };

    var arrCarregar = carregarController("colaborador", "listarColaboradorLeadCalendario", objParametros);
    count = arrCarregar.data.length;
    carregarComboAjax($("#colaborador_calendario"), arrCarregar, " ", "pk", "ds_colaborador");
}

function fcQualificacao() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("produto_servico", "listarTodos", objParametros);
    carregarComboAjax($("#produtos_servicos_pk"), arrCarregar, " ", "pk", "ds_produto_servico");
}

function fcCarregarCalendario(){
    let countApectRatio = 8 - count;


    var objParametros = {
        "leads_pk": $("#leads_pk").val(),
        "colaborador_pk": $("#colaborador_calendario").val(),
        "produtos_servicos_pk": $("#produtos_servicos_pk").val(),
        "n_qtde_dias_semana": $("#n_qtde_dias_semana").val()
    };
    var v_url_events = routes_api("agenda_colaborador_padrao", "calendarioDados", objParametros);
    var v_url_resource = routes_api("agenda_colaborador_padrao", "calendarioDadosEscala", objParametros);

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'resourceTimelineMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
        },
        locale: 'pt-br',
        editable: true,
        height: 400,
        selectable: true,
        buttonText:{
            day:    'Hoje',
            week:    'Semana',
            month:    'Mês',
        },
        views: {
            resourceTimelineMonth:{
                slotLabelFormat: [
                    { weekday: 'short'}, // lower level of text
                    { days: 'long'},
                ],
            }
        },
        resourceAreaWidth:"200",
        eventMinWidth:"200",
        slotMinWidth:"200",
        displayEventEnd:true,
        //aspectRatio: countApectRatio,
        slotLabelFormat: [
            { weekday: 'short'}, // lower level of text
            { days: 'long'},
            {
                hour: 'numeric',
                minute: '2-digit',
                omitZeroMinute: false,
                hour12: false
            },


        ],

        resourceAreaColumns: [
            {
                field: 'posto_trabalho',
                headerContent: 'Posto de Trabalho'
            },
            {
                field: 'colaborador',
                headerContent: 'Colaborador'
            },
            {
                field: 'qualificacao',
                headerContent: 'Qualificação'
            },
            {
                field: 'escala',
                headerContent: 'Tipo Escala'
            },
            {
                field: 'tipo_escala',
                headerContent: 'Variação Escala'
            },
        ],
        refetchResourcesOnNavigate:true,
        resources: {
            url: v_url_resource,
        },
        events: {
            url: v_url_events,
        },
        eventTimeFormat: { // like '14:30:00'
            hour: '2-digit',
            minute: '2-digit',
            meridiem: true
        },
        eventClick: function(info) {
            var data = new Date(info.event.start);
            let dia = "";
            let mes = "";
            let ano = data.getFullYear();
            if(data.getDate()<10){
                dia = "0"+data.getDate();
            }
            else{
                dia = data.getDate();
            }
            if(data.getMonth()<10){
                mes = "0"+parseInt(data.getMonth()+1);
            }
            else{
                mes = parseInt(data.getMonth()+1);
            }
            let dt_apontamento = dia+"/"+mes+"/"+ano;

            fcAbrirApontamentoDia(info.event.extendedProps.colaborador_pk, info.event.extendedProps.leads_pk,dt_apontamento)
            // change the border color just for fun
            info.el.style.borderColor = 'red';
        },
        eventDrop: function(info) {
            if(info.oldResource){
                var colaborador_inicial = info.oldResource.extendedProps.colaborador_pk;
                var colaborador_futuro = info.newResource.extendedProps.colaborador_pk;
                if(parseInt(colaborador_inicial)!=parseInt(colaborador_futuro)){
                    utilsJS.toastNotify(false,"Troque a escala para o mesmo colaborador!");
                    info.revert();
                    return false;
                }
            }
            else{
                var today = new Date(info.oldEvent.start);
                var dd = today.getDate();
                var mm = today.getMonth()+1; //January is 0!
                var yyyy = today.getFullYear();
                //data
                if(dd<10) {
                    dd = '0'+dd
                }

                if(mm<10) {
                    mm = '0'+mm
                }

                var dt_atual = yyyy+"-"+mm+"-"+dd;

                var todayN = new Date(info.event.start);
                var ddN = todayN.getDate();
                var mmN = todayN.getMonth()+1; //January is 0!
                var yyyyN = todayN.getFullYear();
                //data
                if(ddN<10) {
                    ddN = '0'+ddN
                }

                if(mmN<10) {
                    mmN = '0'+mmN
                }

                var nova_data = yyyyN+"-"+mmN+"-"+ddN;

                utilsJS.sweetMensagemConfirm('Alterar ?', 'Deseja alterar a data da escala do colaborador '+info.event.extendedProps.ds_colaborador+' ?', function () {

                    var objParametros = {
                        "colaborador_pk": (info.event.extendedProps.colaborador_pk),
                        "leads_pk": (info.event.extendedProps.leads_pk),
                        "dt_atual": dt_atual,
                        "nova_data": nova_data

                    };
                    var arrEnviar = carregarController("agenda_colaborador_padrao", "updateDataEscalaColaborador", objParametros);

                    if (arrEnviar.status == true){
                        // Reload datable
                        utilsJS.toastNotify(true, arrEnviar.message);
                    }
                    else{
                        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
                    }

                });

            }

        },
        dateClick: function(info) {
            var data = info.dateStr.split("-");
            let dt_apontamento = data[2]+"/"+data[1]+"/"+data[0];
            fcAbrirApontamentoDia(info.resource.extendedProps.colaborador_pk, info.resource.extendedProps.leads_pk,dt_apontamento)
        },

    });

    utilsJS.loaded();
    calendar.render();
    
}

function fcAbrirApontamentoDia(colaborador_pk, leads_pk,dt_apontamento) {

    $("#dv_formulario_ponto").hide();
    $("#dv_formulario_falta").hide();
    $("#dv_formulario_folga").hide();
    $("#dv_formulario_troca_escala").hide();
    $("#dv_formulario_afastamento").hide();
    $("#dv_formulario_ferias").hide();
    $("#dv_formulario_servico_extra").hide();
    $("#janela_apontamento_colaborador").modal("show");
    $("#tipo_apontamento_pk").val("");


    $("#dt_apontamento").val(dt_apontamento);

    fcChangeCarregarTabelas(colaborador_pk, dt_apontamento, leads_pk);
    fcColaboradorModal();
    $("#colaborador_pk_modal").val(colaborador_pk)
    $("#colaborador_pk_modal").prop("disabled",true);
}
$(document).ready(function(){
    utilsJS.loading("Carregando informações...");
    fcComboLeads()//CARREGA COMBO DE POSTOS DE TRABALHO
    fcColaborador()//CARREGA COMBO COLABORADORES
    fcQualificacao()//CARREGA COMBO DE QUALIFICAÇÃO



    fcCarregarCalendario()///carrega o calendario


    $("#leads_pk").change(function () {
        //tblResultado.clear();
        utilsJS.loading("Carregando informações...");
        fcColaborador();
        fcCarregarCalendario();
    });

    $("#colaborador_calendario").change(function () {
        utilsJS.loading("Carregando informações...");
        fcCarregarCalendario();
    });


    $("#produtos_servicos_pk").change(function () {
        utilsJS.loading("Carregando informações...");
        fcCarregarCalendario();
    });
    $("#n_qtde_dias_semana").change(function () {
        utilsJS.loading("Carregando informações...");
        fcCarregarCalendario();
    });


    //FORMATAÇÃO
    $("#leads_pk").select2();




});