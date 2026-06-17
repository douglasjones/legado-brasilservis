var tblResultado;

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','pesqRelContrato' ,objParametros);
}

function fcCarregarGrid(){  
    var objParametros = {
        "empresa_pk": $("#empresa_pk").val(),
        "ds_cpf_cnpj":$("#ds_cpf_cnpj").val(),
        "leads_clientes_pk": $("#leads_clientes_pk").val(),
        "leads_pk": $("#leads_pk").val(),
        "dt_ini_cadastro":$("#dt_ini_cadastro").val(),
        "dt_fim_cadastro":$("#dt_fim_cadastro").val(),
        "dt_ini_contrato":$("#dt_ini_contrato").val(),
        "dt_fim_contrato":$("#dt_fim_contrato").val(),
        "usuario_cadastro_pk": $("#usuario_cadastro_pk").val(),
        "ic_status": $("#ic_status").val(),
        "tp_contrato": $("#tp_contrato").val()
    };         
    var v_url = routes_api("contrato", "relContrato", objParametros); 
    tblResultado = $("#tblResultado").DataTable({
        searching: false,
        paging: false,
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
        columns: [
            {
                mRender: function (data, type, full) {
                    return full['ds_empresa'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_cliente'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_posto_trabalho'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_usuario'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_cadastro'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            }, {
                mRender: function (data, type, full) {
                    return full['dt_inicio_contrato'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            }, {
                mRender: function (data, type, full) {
                    return full['dt_fim_contrato'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
             {
                mRender: function (data, type, full) {
                    return full['ds_tipo_contrato'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
             {
                mRender: function (data, type, full) {
                    return full['ds_status'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return float2moeda(full['vl_contrato']);
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            
        ]

    });
     
};

function fcExport(){

    var htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    htmlPlanilha += '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PlanilhaTeste</x:Name>';
    htmlPlanilha += '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>';
    htmlPlanilha += '<![endif]--></head><body><table>' + $("#form").html() + '</table></body></html>';
    
    var htmlBase64 = btoa(htmlPlanilha);
    var link = "data:application/vnd.ms-excel;base64," + htmlBase64;
    
    var hyperlink = document.createElement("a");
    hyperlink.download = "relContrato.xls";
    hyperlink.href = link;
    hyperlink.style.display = 'none';
    
    document.body.appendChild(hyperlink);
    hyperlink.click();
    document.body.removeChild(hyperlink);
}
    

$(document).ready(function(){    
    fcCarregarGrid();
    
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdExport', fcExport);
    
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();
    var hh = today.getHours();
    var min = today.getMinutes();
    var seg = today.getSeconds();
    //data
    if(dd<10) {
        dd = '0'+dd
    } 
    
    if(mm<10) {
        mm = '0'+mm
    } 
    //hora 
    if(hh<10) {
        hh = '0'+hh
    } 
    
    if(min<10) {
        min = '0'+min
    } 
    if(seg<10) {
        seg = '0'+seg
    } 
    
    today = dd + '/' + mm + '/' + yyyy + ' '+hh+':'+min+':'+seg;
    
    
        $("#dt_emissao").text(today);
        $("#ds_lead").text(ds_lead);
        $("#ds_colaborador").text(ds_colaborador);
        $("#periodo").text(dt_ini + " - " + dt_fim);
        
    });
    