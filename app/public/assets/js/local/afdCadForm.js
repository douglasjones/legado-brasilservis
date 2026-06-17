var componentDateRangePicker = false;
localStorage.getItem('filtro_range_date') ? localStorage.getItem('filtro_range_date') : (localStorage.setItem('filtro_range_date', ''));

function fcEnviar(){
    if($("#contas_pk").val()==""){
        sweetMensagem('warning','Selecione a conta !');
        return false;
    }
    var data = $("#form").serializeArray();

    $.ajax({
        type: 'POST',
        url: '/api/afd/salvar',
        data: data,
        dataType:'json',
        complete: function (response) {
            try {
                var log = JSON.parse(response.responseText);
                if(log.status==true){
                    utilsJS.toastNotify(true,log.message);
                    sendPost("colaborador","receptivo", {});
                }
                else{
                    utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
                }

            } catch (e) {
                utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
            }
        }
    });
}

function fcCancelar(){
    var objParametros = {};
    sendPost("afd", "receptivo", objParametros);
}

function fcCarregarGrid(){
    let leads_pk = $('#leads_pk').val();

    var objParametros = {
        "leads_pk": leads_pk
    };     
    
    var v_url = routes_api("colaborador", "listarColaboradorPorLead", objParametros);
    tblResultado = $('#tblResultado').DataTable({
        searching: false,
        paging: false,
        scrollX: true,
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
                    return "<input type=checkbox class='check' name='colaborador_pk[]' value='"+full['pk']+"'>";
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_colaborador'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            }
        ]

    });
        
    
}


$(document).ready(function()
    {
        //Atribui os eventos
        $(document).on('click', '#cmdCancelar', fcCancelar);
        $(document).on('click', '#cmdEnviar', fcEnviar);

     

        fcCarregarGrid();

        $("#leads_pk").change(function(){
            if($('#leads_pk').val()!=""){
          
                tblResultado.clear().destroy();
                fcCarregarGrid();
            }
        });

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

        $("#ic_marcar_todos").on( 'change', function () {
            if ($("#ic_marcar_todos").prop( "checked")) 
            $(".check").attr('checked', true)
            else 
            $(".check").attr('checked', false)
        } );
        


    }
);
