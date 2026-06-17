var tblResultado;

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','pesqCompraMovimentacaoLead' ,objParametros);
}
 
function fcCarregarGrid() {
    var objParametros = {
        "leads_pk": $("#leads_pk").val(),
        "categorias_produto_pk": $("#categorias_produto_pk").val(),
        "produtos_pk": $("#produtos_pk").val(),
        "tipo_operacao_pk": $("#tipo_operacao_pk").val(),
        "dt_ini_compra": $("#dt_ini_compra").val(),
        "dt_fim_compra": $("#dt_fim_compra").val()
    };

    var v_url = routes_api("movimentacao_estoque", "relCompraMovimentacaoLead", objParametros);

    var tblResultado = $("#tblResultado").DataTable({
        searching: false,
        paging: false,
        scrollX: true,
        pageLength: 30,
        aLengthMenu: [30, 50, 100],
        iDisplayLength: 30,
        processing: false,
        serverSide: false,
        ajax: v_url,
        responsive: true,
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },
        columns: [
            { data: 'ds_categoria', orderable: false, searchable: false, width: '80px' },
            { data: 'ds_produto', orderable: false, searchable: false, width: '80px' },
            { data: 'tipo_operacao', orderable: false, searchable: false, width: '80px' },
            { data: 'ds_numero_nota', orderable: false, searchable: false, width: '80px' },
            { data: 'qtde', orderable: false, searchable: false, width: '80px' },
            { data: 'valor', orderable: false, searchable: false, width: '80px' },
            { data: 'ds_lead', orderable: false, searchable: false, width: '80px' },
            { data: 'dt_movimentacao_compra', orderable: false, searchable: false, width: '80px' }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' && i.length > 0 ?
                    parseFloat(i.replace(/[\$,]/g, '')) :
                    typeof i === 'number' ?
                    i : 0;
            };

            // Total over this page
            var total = api
                .column(4, { page: 'current' }) // Correct column index for 'qtde'
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            // Update footer
            $(api.column(4).footer()).html(total);
        }
    });
}

function fcExport(){

    var htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    htmlPlanilha += '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PlanilhaTeste</x:Name>';
    htmlPlanilha += '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>';
    htmlPlanilha += '<![endif]--></head><body><table>' + $("#tblResultado").html() + '</table></body></html>';
    
    var htmlBase64 = btoa(htmlPlanilha);
    var link = "data:application/vnd.ms-excel;base64," + htmlBase64;
    
    var hyperlink = document.createElement("a");
    hyperlink.download = "relCompraMovimentacaoPostoTrabalho.xls";
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
        
    });
    