var tblResultado;

function fcCarregarGrid() {
    var objParametros = {
        "pk": $("#pk").val()

    };
    var arrCarregar = carregarController("faturamento", "listarDadosEmissoes", objParametros);

    if (arrCarregar.status == true) {
        if (arrCarregar.data.length > 0) {
            $('#tblResultado').append('<tbody></tbody>');
            for (var i = 0; i < arrCarregar.data.length; i++) {
            
                var lancamentos_pk = "";
                var ds_lead = "";
                var dt_faturamento_ini = "";
                var dt_faturamento_fim = "";
                var vl_total_contrato = "";
                var ds_tipo_contrato = "";
                var ds_status_pagamento = "";

                lancamentos_pk = arrCarregar.data[i].lancamentos_pk;
                lancamentos_pk = lancamentos_pk == null ? '' : lancamentos_pk;

                ds_lead = arrCarregar.data[i].ds_lead;
                ds_lead = ds_lead == null ? '' : ds_lead;

                dt_faturamento_ini = arrCarregar.data[i].dt_faturamento_ini;
                dt_faturamento_ini = dt_faturamento_ini == null ? '' : dt_faturamento_ini;

                dt_faturamento_fim = arrCarregar.data[i].dt_faturamento_fim;
                dt_faturamento_fim = dt_faturamento_fim == null ? '' : dt_faturamento_fim;

                vl_total_contrato = arrCarregar.data[i].vl_total_contrato;
                vl_total_contrato = vl_total_contrato == null ? '' : vl_total_contrato;

                ds_tipo_contrato = arrCarregar.data[i].ds_tipo_contrato;
                ds_tipo_contrato = ds_tipo_contrato == null ? '' : ds_tipo_contrato;

                ds_status_pagamento = arrCarregar.data[i].ds_status_pagamento;
                ds_status_pagamento = ds_status_pagamento == null ? '' : ds_status_pagamento;

                $('#tblResultado tbody').append('<tr id="tblResultadoTr'+i+'"></tr>');
                $('#tblResultadoTr'+i).append('<td>'+lancamentos_pk+'</td>');
                $('#tblResultadoTr'+i).append('<td>'+ds_lead+'</td>');
                $('#tblResultadoTr'+i).append('<td>'+dt_faturamento_ini + ' - ' +dt_faturamento_fim+'</td>');
                $('#tblResultadoTr'+i).append('<td>'+vl_total_contrato+'</td>');
                $('#tblResultadoTr'+i).append('<td>'+ds_tipo_contrato+'</td>');
                $('#tblResultadoTr'+i).append('<td>'+ds_status_pagamento+'</td>');
                
            }
        }
    }
}

function fcVoltar() {
    var objParametros = {
        "pk":''
    };
    sendPost('faturamento', 'receptivo' ,objParametros);
}


$(document).ready(function(){
    $(document).on('click', '#cmdVoltar', fcVoltar);
    fcCarregarGrid();
    

    $("#tblResultado input").keyup(function(){
        var index = $(this).parent().index();
        var nth = "#tblResultado td:nth-child("+(index+1).toString()+")";
        var valor = $(this).val().toUpperCase();
        $("#tblResultado tbody tr").show();
        $(nth).each(function(){
                if($(this).text().toUpperCase().indexOf(valor) < 0){
                        $(this).parent().hide();
                }
        });
    });
    $("#tblResultado input").blur(function(){
            $(this).val("");
    });	
});
