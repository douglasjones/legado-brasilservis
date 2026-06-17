var tblResultado;
var componentDateRangePicker = false;
localStorage.getItem('filtro_range_date') ? localStorage.getItem('filtro_range_date') : (localStorage.setItem('filtro_range_date', ''));

function fcCarregarGrid(){
    var ic_cliente = $("#leads_clientes_pk").val();
    var ds_cliente = $("#leads_clientes_pk option:selected").text();
    var leads_pk = $("#leads_pk").val();
    var ds_lead = $("#leads_pk option:selected").text();
    var colaborador_pk = $("#colaborador_pk").val();
    var ds_colaborador = $("#colaborador_pk option:selected").text();
    var ds_periodo = $.trim(localStorage.getItem('filtro_range_date'));


    /*if(leads_pk=="" && colaborador_pk=="" && ic_cliente==""){
        utilsJS.toastNotify(false,"Por favor, informe um posto de trabalho, colaborador ou cliente");
        return false;
    }*/

    if(ds_periodo==""){
        sweetMensagem('warning',"Por favor, informe o período");
        return false;
    }

    var objParametros = {
        "ic_cliente": ic_cliente,
        "ds_cliente": ds_cliente,
        "leads_pk":leads_pk,
        "ds_lead":ds_lead,
        "colaborador_pk":colaborador_pk,
        "ds_colaborador":ds_colaborador,
        "ds_periodo":ds_periodo
    }
    sendPost("relatorio","resAcompanhamentoPontoAnalitico",objParametros);
    //cria rota, voce vai colocar ela em colaboradores, por que é um relatorio que pega informação
    //especifica de colaborador.
}

function fcCancelar(){
    var objParametros = {};
    sendPost('menu','relatorio' ,objParametros);
}

function fcCarregarClientes() {
    //Carrega os grupos
    var objParametros = {
        "ic_tipo_lead": 1,
        "ic_cliente":1
    };

    var arrCarregar = carregarController("lead", "listarTodosClientes", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#leads_clientes_pk"), arrCarregar, " ", "pk", "ds_lead");

}

function fcCarregarLeads() {
    //Carrega os grupos

    var objParametros = {
        "ic_tipo_lead": 2,
        "leads_pai_pk": $("#leads_clientes_pk").val(),
        "ic_cliente": 1
    };

    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);

    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");

}

function fcCarregarColaborador() {
    //Carrega os grupos

    var objParametros = {
        "leads_pk": $("#leads_pk").val()
    };

    var arrCarregar = carregarController("colaborador", "listarColaboradorLead", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#colaborador_pk"), arrCarregar, " ", "pk", "ds_colaborador");

}



$(document).ready(function(){
    function setDateRangePicker(start, end) {
        $('input[name="date_range_filter"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    var start = moment();
    var end = moment().add(1, 'day');

    localStorage.setItem('filtro_range_date', start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

    componentDateRangePicker = $('input[name="date_range_filter"]').daterangepicker({
        autoUpdateInput: true,
        autoApply: false,
        timePicker24Hour: false,
        singleDatePicker: false,
        timePickerIncrement: false,
        timePicker: false,
        showDropdowns: true,
        opens: 'left',
        drops: 'up',
        linkedCalendars: true,
        alwaysShowCalendars: false,
        applyButtonClasses: 'btn-success',
        cancelButtonClasses: 'btn-danger',
        startDate: start,
        endDate: end,
        locale: {
            //format: "DD/MM/YYYY HH:mm",
            format: "DD/MM/YYYY",
            cancelLabel: 'Limpar',
            applyLabel: 'Selecionar',
            customRangeLabel: 'Data Personalizada',
        },
        ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()]
        }
    }, setDateRangePicker);
    setDateRangePicker(start, end);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('filtro_range_date', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('filtro_range_date', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    });

    fcCarregarClientes();
    fcCarregarLeads();
    fcCarregarColaborador();
    $(".chzn-select").chosen({ allow_single_deselect: true });
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);
    $(document).on('click', '#cmdCancelar', fcCancelar);



    $("#leads_clientes_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarLeads();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });

    $(".chzn-select").chosen({allow_single_deselect: true});

});