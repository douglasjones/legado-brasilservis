var calendar;
function runCalendar() {
    try {

        var objParametros = {
            "leads_pk": $("#leads_pk_pesquisa").val(),
            "tipo_agenda_pk": $("#tipo_agenda_pesquisa_pk").val(),
            "ic_status": $("#status_pesq").val()
        };
        var v_url = routes_api("agenda_calendario", "listarEventos", objParametros);

        calendar = $('#calendario').fullCalendar({
            slotDuration: '00:15:00', /* If we want to split day time each 15minutes */
            minTime: '00:00:00',
            maxTime: '23:59:59',
            lang: 'pt-br',
            header: {
                left: 'today',
                center: 'prevYear, prev, title, next, nextYear',
                right: 'month,agendaWeek,agendaDay'
            },
            // editable: true,
            selectable: true,
            events: {
                url: v_url,
            },
            eventRender: function(event, element) {
                if(event.icon){
                    element.find(".fc-title").prepend("<i class='"+event.icon+"'></i> ");
                }
            },
            eventClick: function (calEvent) {
                fcAbrirFormAgenda(calEvent.id, "", "edit");
            },
            select: function (startDate) {
                fcAbrirFormAgenda("", startDate.format(), "add");
            }

        });
    } catch (error) {
        utilsJS.toastNotify(false, error);
    }

}

function fcListarUsuarioLogado(){
    var arrCarregar = carregarController("usuario", "listarUsuarioLogado", "");
    $("#usuario").html("<b>Olá, "+arrCarregar.data['ds_usuario']+"</b>")
}
function fcCarregarLeadsPesquisaAgenda(){

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    carregarComboAjax($("#leads_pk_pesquisa"), arrCarregar, " ", "pk", "ds_lead");
}


$(document).ready(function() {
    runCalendar();
    fcListarUsuarioLogado();
    fcCarregarLeadsPesquisaAgenda();

    $("#cmdPesquisarAgenda").click(function(){

        $('#calendario').fullCalendar( 'destroy' );

        runCalendar();
    });
});