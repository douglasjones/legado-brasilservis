function fcCancelar(){
    var objParametros = {};
    sendPost('menu','financeiro' ,objParametros);
}
function fcFecharModalLancamento(){
    fclimpaFormLancamento();
    $("#event-modal").modal("hide");
}
function fcEventosMenu(){
    $("#extrato_mes-tab").click(function(){
        $(".loader").show();
        $("#carregar").show();
        $("#exibir").hide();
        setTimeout(() => {
            $("#extrato_mes-tab").removeClass();
            $("#extrato_mes-tab").addClass('nav-link active btn-sm');
            $("#receitas-tab").removeClass();
            $("#receitas-tab").addClass('nav-link btn-sm');
            $("#despesas-tab").removeClass();
            $("#despesas-tab").addClass('nav-link btn-sm');
            $("#lancamentos-tab").removeClass();
            $("#lancamentos-tab").addClass('nav-link btn-sm');

            $("#extrato_mes").addClass('tab-pane fade show active');
            $("#receitas").removeClass();
            $("#receitas").addClass('tab-pane fade');
            $("#despesas").removeClass();
            $("#despesas").addClass('tab-pane fade');
            $("#lancamentos").removeClass();
            $("#lancamentos").addClass('tab-pane fade');
            $(".loader").hide();
            $("#carregar").hide();
            $("#exibir").show();
        }, 1000);
    });

    $("#receitas-tab").click(function(){
        $(".loader").show();
        $("#carregar").show();
        $("#exibir").hide();
        setTimeout(() => {
            $("#extrato_mes-tab").removeClass();
            $("#extrato_mes-tab").addClass('nav-link btn-sm');
            $("#receitas-tab").addClass('nav-link active btn-sm');
            $("#despesas-tab").removeClass();
            $("#despesas-tab").addClass('nav-link btn-sm');
            $("#lancamentos-tab").removeClass();
            $("#lancamentos-tab").addClass('nav-link btn-sm');

            $("#extrato_mes").removeClass();
            $("#extrato_mes").addClass('tab-pane fade');
            $("#receitas").addClass('tab-pane fade show active');
            $("#despesas").removeClass();
            $("#despesas").addClass('tab-pane fade');
            $("#lancamentos").removeClass();
            $("#lancamentos").addClass('tab-pane fade');
            fcCarregarFuncoesReceita();
            $(".loader").hide();
            $("#carregar").hide();
            $("#exibir").show();
        }, 1000);
    });
    
    $("#despesas-tab").click(function(){
        $(".loader").show();
        $("#carregar").show();
        $("#exibir").hide();
        setTimeout(() => {
            $("#extrato_mes-tab").removeClass();
            $("#extrato_mes-tab").addClass('nav-link btn-sm');
            $("#receitas-tab").removeClass();
            $("#receitas-tab").addClass('nav-link btn-sm');
            $("#despesas-tab").removeClass();
            $("#despesas-tab").addClass('nav-link active btn-sm');
            $("#lancamentos-tab").removeClass();
            $("#lancamentos-tab").addClass('nav-link btn-sm');

            $("#extrato_mes").removeClass();
            $("#extrato_mes").addClass('tab-pane fade');
            $("#receitas").removeClass();
            $("#receitas").addClass('tab-pane fade');
            $("#despesas").addClass('tab-pane fade show active');
            $("#lancamentos").removeClass();
            $("#lancamentos").addClass('tab-pane fade');
            fcCarregarFuncoesDespesa();

            $(".loader").hide();
            $("#carregar").hide();
            $("#exibir").show();
        }, 1000);
        
    
        
    });

    $("#lancamentos-tab").click(function(){
        $(".loader").show();
        $("#carregar").show();
        $("#exibir").hide();
        setTimeout(() => {
            $("#extrato_mes-tab").removeClass();
            $("#extrato_mes-tab").addClass('nav-link btn-sm');
            $("#receitas-tab").removeClass();
            $("#receitas-tab").addClass('nav-link btn-sm');
            $("#despesas-tab").removeClass();
            $("#despesas-tab").addClass('nav-link btn-sm');
            $("#lancamentos-tab").removeClass();
            $("#lancamentos-tab").addClass('nav-link active  btn-sm');

            $("#extrato_mes").removeClass();
            $("#extrato_mes").addClass('tab-pane fade');
            $("#receitas").removeClass();
            $("#receitas").addClass('tab-pane fade');
            $("#despesas").removeClass();
            $("#despesas").addClass('tab-pane fade');
            $("#lancamentos").addClass('tab-pane fade show active');
            fcCarregarFuncoesLancamento();
            $(".loader").hide();
            $("#carregar").hide();
            $("#exibir").show();
        }, 1000);
    });
}

$(document).ready(function () {
    
    $(document).on('click', '#cmdVoltar', fcCancelar);
    fcEventosMenu();
    fcCarregarFuncoesExtrato();
    fcCarregarFuncoesExtrato();
    fcCarregarGridArquivos();

    
    //fcCarregarTblHistoricoParcial();

    $(document).on('click', '#btnSalvarLancamento', fcValidar);
    $(document).on('click', '#btnSalvarLancamento2', fcValidar);

    $(document).on('click', '#fecharModalLancamento', fcFecharModalLancamento);
    $(document).on('click', '#fecharModalLancamento2', fcFecharModalLancamento);
    $("#cmdNovoLancamento").click(function () {
        fcAbrirCadastroLancamento('');
    });

    $("#buscaGeralLancamento").keyup(function () {
        filtrarDados($(this).val());
    });


    var startCad = moment();
    var endCad = moment().add(1, 'day');
    //DATA CADASTRO
    function setDateRangePickerCad(start, end) {
        $('input[name="date_range_filter_cadastro"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    

    localStorage.setItem('date_range_filter_cadastro',  ' - ');
    //localStorage.setItem('date_range_filter_cadastro',  start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

    componentDateRangePicker = $('input[name="date_range_filter_cadastro"]').daterangepicker({
        autoUpdateInput: false,
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
        startDate: startCad,
        endDate: endCad,
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
    }, setDateRangePickerCad);
    setDateRangePickerCad(startCad, endCad);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_cadastro"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_cadastro', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_cadastro', ' - ');

    });



    var startFat = moment();
    var endFat = moment().add(1, 'day');
    //DATA FATURAMENTO
    function setDateRangePickerFat(start, end) {
        $('input[name="date_range_filter_faturamento"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    

    localStorage.setItem('date_range_filter_faturamento',' - ');

    componentDateRangePicker = $('input[name="date_range_filter_faturamento"]').daterangepicker({
        autoUpdateInput: false,
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
        startDate: startFat,
        endDate: endFat,
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
    }, setDateRangePickerFat);
    setDateRangePickerFat(startFat, endFat);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_faturamento"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_faturamento', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_faturamento', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    });
    //DATA VENCIMENTO
    var startVenc = moment().startOf('month');
    var endVenc = moment().endOf('month');

    function setDateRangePickerVenc(start, end) {
        $('input[name="date_range_filter_vencimento"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    

    localStorage.setItem('date_range_filter_vencimento',startVenc.format('DD/MM/YYYY') + ' - ' + endVenc.format('DD/MM/YYYY'));

    componentDateRangePicker = $('input[name="date_range_filter_vencimento"]').daterangepicker({
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
        startDate: startVenc,
        endDate: endVenc,
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
    }, setDateRangePickerVenc);
    setDateRangePickerVenc(startVenc, endVenc);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_vencimento"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_vencimento', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_vencimento', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    });
    //DATA PAGAMENTO
    function setDateRangePickerPag(start, end) {
        $('input[name="date_range_filter_pagamento"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    var startPag = moment();
    var endPag = moment().add(1, 'day');

    localStorage.setItem('date_range_filter_pagamento','-');

    componentDateRangePicker = $('input[name="date_range_filter_pagamento"]').daterangepicker({
        autoUpdateInput: false,
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
        startDate: startPag,
        endDate: endPag,
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
    }, setDateRangePickerPag);
    setDateRangePickerPag(startPag, endPag);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_pagamento"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_pagamento', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_pagamento', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    });


     


    //--------------------------------------------RECEITA----------------------------------------------------//
    $("#buscaGeralReceita").keyup(function () {
        filtrarDadosReceita($(this).val());
    });
    

    var startCadReceita = moment();
    var endCadReceita = moment().add(1, 'day');
    //DATA CADASTRO
    function setDateRangePickerCadReceita(start, end) {
        $('input[name="date_range_filter_cadastro_receita"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    
    
    localStorage.setItem('date_range_filter_cadastro_receita',  ' - ');
    //localStorage.setItem('date_range_filter_cadastro',  start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

    componentDateRangePicker = $('input[name="date_range_filter_cadastro_receita"]').daterangepicker({
        autoUpdateInput: false,
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
        startDate: startCadReceita,
        endDate: endCadReceita,
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
    }, setDateRangePickerCadReceita);
    setDateRangePickerCadReceita(startCadReceita, endCadReceita);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_cadastro_receita"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_cadastro_receita', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_cadastro_receita', ' - ');

    });

    

    var startFatReceita = moment();
    var endFatReceita = moment().add(1, 'day');
    //DATA FATURAMENTO
    function setDateRangePickerFatReceita(start, end) {
        $('input[name="date_range_filter_faturamento_receita"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    

    localStorage.setItem('date_range_filter_faturamento_receita',' - ');

    componentDateRangePicker = $('input[name="date_range_filter_faturamento_receita"]').daterangepicker({
        autoUpdateInput: false,
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
        startDate: startFat,
        endDate: endFat,
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
    }, setDateRangePickerFatReceita);
    setDateRangePickerFatReceita(startFatReceita, endFatReceita);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_faturamento_receita"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_faturamento_receita', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_faturamento_receita', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    });
    

    //DATA VENCIMENTO
    
    var startVencReceita = moment();
    var endVencReceita = moment().add(1, 'day');

    function setDateRangePickerVencReceita(start, end) {
        $('input[name="date_range_filter_vencimento_receita"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    

    localStorage.setItem('date_range_filter_vencimento_receita',startVencReceita.format('DD/MM/YYYY') + ' - ' + endVencReceita.format('DD/MM/YYYY'));

    componentDateRangePicker = $('input[name="date_range_filter_vencimento_receita"]').daterangepicker({
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
        startDate: startVencReceita,
        endDate: endVencReceita,
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
    }, setDateRangePickerVencReceita);
    setDateRangePickerVencReceita(startVencReceita, endVencReceita);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_vencimento_receita"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_vencimento_receita', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_vencimento_receita', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    });
    //DATA PAGAMENTO
    function setDateRangePickerPagReceita(start, end) {
        $('input[name="date_range_filter_pagamento_receita"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    var startPagReceita = moment();
    var endPagReceita = moment().add(1, 'day');

    localStorage.setItem('date_range_filter_pagamento_receita','-');

    componentDateRangePicker = $('input[name="date_range_filter_pagamento_receita"]').daterangepicker({
        autoUpdateInput: false,
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
        startDate: startPagReceita,
        endDate: endPagReceita,
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
    }, setDateRangePickerPagReceita);
    setDateRangePickerPagReceita(startPag, endPag);
    
    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_pagamento_receita"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_pagamento_receita', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_pagamento_receita', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    });
    
    //--------------------------------------------DESPESA----------------------------------------------------//
    $("#buscaGeralDespesa").keyup(function () {
        filtrarDadosDespesa($(this).val());
    });


    var startCadDespesa = moment();
    var endCadDespesa = moment().add(1, 'day');
    //DATA CADASTRO
    var startCadDespesa = moment();
    var endCadDespesa = moment().add(1, 'day');
    
    //DATA CADASTRO
    function setDateRangePickerCadDespesa(start, end) {
        $('input[name="date_range_filter_cadastro_despesa"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }
    
    // Check and set stored date range
    const storedDateRange = localStorage.getItem('date_range_filter_cadastro_despesa');
    if (storedDateRange) {
        $('input[name="date_range_filter_cadastro_despesa"]').val(storedDateRange);
    } else {
        localStorage.setItem('date_range_filter_cadastro_despesa', '');
    }
    
    componentDateRangePicker = $('input[name="date_range_filter_cadastro_despesa"]').daterangepicker({
        autoUpdateInput: false,
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
        startDate: startCadDespesa,
        endDate: endCadDespesa,
        locale: {
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
    }, setDateRangePickerCadDespesa);
    setDateRangePickerCadDespesa(startCadDespesa, endCadDespesa);
    
    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_cadastro_despesa"]').trigger('cancel.daterangepicker');
    });
    
    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        localStorage.setItem('date_range_filter_cadastro_despesa', '');
    });
    
    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_cadastro_despesa', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });
    



    var startFatDespesa = moment();
    var endFatDespesa = moment().add(1, 'day');
    //DATA FATURAMENTO
    function setDateRangePickerFatDespesa(start, end) {
        $('input[name="date_range_filter_faturamento_despesa"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    

    localStorage.setItem('date_range_filter_faturamento_despesa',' - ');

    componentDateRangePicker = $('input[name="date_range_filter_faturamento_despesa"]').daterangepicker({
        autoUpdateInput: false,
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
        startDate: startFatDespesa,
        endDate: endFatDespesa,
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
    }, setDateRangePickerFatDespesa);
    setDateRangePickerFatDespesa(startFatDespesa, endFatDespesa);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_faturamento_despesa"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_faturamento_despesa', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_faturamento_despesa', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    });
    //DATA VENCIMENTO
    
    var startVencDespesa = moment();
    var endVencDespesa = moment().add(1, 'day');

    function setDateRangePickerVencDespesa(start, end) {
        $('input[name="date_range_filter_vencimento_despesa"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    

    localStorage.setItem('date_range_filter_vencimento_despesa',startVencDespesa.format('DD/MM/YYYY') + ' - ' + endVencDespesa.format('DD/MM/YYYY'));

    componentDateRangePicker = $('input[name="date_range_filter_vencimento_despesa"]').daterangepicker({
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
        startDate: startVencDespesa,
        endDate: endVencDespesa,
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
    }, setDateRangePickerVencDespesa);
    setDateRangePickerVencDespesa(startVencDespesa, endVencDespesa);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_vencimento_despesa"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_vencimento_despesa', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_vencimento_despesa', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    });
    //DATA PAGAMENTO
    function setDateRangePickerPagDespesa(start, end) {
        $('input[name="date_range_filter_pagamento_despesa"]').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    var startPagDespesa = moment();
    var endPagDespesa = moment().add(1, 'day');

    localStorage.setItem('date_range_filter_pagamento_despesa','-');

    componentDateRangePicker = $('input[name="date_range_filter_pagamento_despesa"]').daterangepicker({
        autoUpdateInput: false,
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
        startDate: startPagDespesa,
        endDate: endPagDespesa,
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
    }, setDateRangePickerPagDespesa);
    setDateRangePickerPagDespesa(startPag, endPag);

    //Event clear date on click button
    $('.fa.fa-times').parent().on('click', function () {
        $('input[name="date_range_filter_pagamento_despesa"]').trigger('cancel.daterangepicker');
    });

    //Event clear date
    componentDateRangePicker.on('cancel.daterangepicker', function (ev, picker) {

        $(this).val('');
        localStorage.setItem('date_range_filter_pagamento_despesa', '');
    });

    //Event apply date
    componentDateRangePicker.on('apply.daterangepicker', function (ev, picker) {

        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        localStorage.setItem('date_range_filter_pagamento_despesa', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    });
   
    $(".loader").hide();
    $("#carregar").hide();
    $("#exibir").show();
})