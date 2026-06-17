var tblResultado;
var tblResultadoColab;
var click_id = 0;
var arrMes = [];

function fcCarregarGrid(){
    var objParametros = {
        "leads_clientes_pk":$("#leads_clientes_pk").val(),
        "leads_pk":$("#leads_pk").val(),
        "contratos_pk_combo":$("#contratos_pk_combo").val()
    };         

    var v_url = routes_api("lancamento", "relReceitaPostoTrabalho", objParametros); 
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
                    return full['ds_cliente'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_lead'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_contrato'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return "R$ "+float2moeda(full['vl_contrato']);
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return "R$ "+float2moeda(full['vl_receita']);
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            }
        ]

    });

}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','pesqRelReceitaPostoTrabalho' ,objParametros);
}

function fcExport(){

    var htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    htmlPlanilha += '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PlanilhaTeste</x:Name>';
    htmlPlanilha += '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>';
    htmlPlanilha += '<![endif]--></head><body><table>' + $("#form").html() + '</table></body></html>';
 
    var htmlBase64 = btoa(htmlPlanilha);
    var link = "data:application/vnd.ms-excel;base64," + htmlBase64;
 
    var hyperlink = document.createElement("a");
    hyperlink.download = "relReceitaPostoTrabalho.xls";
    hyperlink.href = link;
    hyperlink.style.display = 'none';
 
    document.body.appendChild(hyperlink);
    hyperlink.click();
    document.body.removeChild(hyperlink);
}

$(document).ready(function(){    

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
    fcCarregarGrid();

});