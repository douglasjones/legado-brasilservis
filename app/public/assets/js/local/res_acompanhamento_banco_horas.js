function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','pesqAcompanhamentoBancoHoras' ,objParametros);
}


function fcPrintFolha(pk,colaborador_pk,leads_pk){
        var objParametros = {
            "leads_pk":leads_pk,
            "pk":pk,
            "colaborador_pk":colaborador_pk,
            "relatorio_banco_horas": 1
        };
        sendPost('ponto_folha','receptivoPrint',objParametros)
       
        
    
}
function fcExport() {
    // pega apenas a tabela que você quer exportar
    var tabela = document.querySelector("#tblResultado");

    if (!tabela) {
        alert("Tabela não encontrada!");
        return;
    }

    // cria um HTML válido para o Excel
    var html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="UTF-8">
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>StatusColaborador</x:Name>
                            <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
        </head>
        <body>${tabela.outerHTML}</body>
        </html>
    `;

    // cria um Blob com charset correto
    var blob = new Blob(["\ufeff", html], { type: "application/vnd.ms-excel;charset=utf-8" });

    // cria um link temporário
    var url = URL.createObjectURL(blob);
    var link = document.createElement("a");
    link.href = url;
    link.download = "statusColaborador.xls";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}
$(document).ready(function () {
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdExport', fcExport);

    $(document).ready(function() {
        $('#tblResultado').DataTable({
            scrollX: true,
            responsive: true,
            searching: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: 'Exportar PDF',
                    title: 'Relatório Acompanhamento Banco de Horas - '+$("#ds_mes").val()+"-"+$("#ds_ano").val(),
                    orientation: 'landscape',
                    pageSize: 'A1',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.styles.title = {
                            fontSize: 14,
                            bold: true,
                            alignment: 'center'
                        };
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 12,
                            color: 'white',
                            fillColor: '#4CAF50',
                            alignment: 'center'
                        };
                        doc.content[1].table.widths = ['11%', '11%', '11%', '11%', '11%', '11%', '11%', '12%', '11%', '10%'];
                        doc.pageMargins = [20, 20, 20, 20];
                        doc.defaultStyle.fontSize = 10;
    
                        var rowCount = doc.content[1].table.body.length;
                        for (var i = 1; i < rowCount; i++) {
                            doc.content[1].table.body[i].forEach(function(cell) {
                                cell.alignment = 'center';
                                cell.margin = [2, 2, 2, 2];
                            });
                        }
                    }
                }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/Portuguese-Brasil.json"
            }
        });
    });
    

});

