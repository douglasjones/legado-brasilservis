function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','pesqAcompanhamentoFalta' ,objParametros);
}

$(document).ready(function () {
    $(document).on('click', '#cmdCancelar', fcCancelar);

    $('#tblResultado').DataTable({
        scrollX: true,  // Desabilite o scrollX
        responsive: true,
        searching: false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdfHtml5',
                text: 'Exportar PDF',
                title: 'Relatório Acompanhamento Falta - ' + $("#ds_mes").val() + "-" + $("#ds_ano").val(),
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
                    
                    // Ajuste para 7 colunas
                    doc.content[1].table.widths = ['14%', '14%', '14%', '14%', '14%', '14%', '16%'];
    
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
            "sProcessing": "Processando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sPrevious": "Anterior",
                "sNext": "Próximo",
                "sLast": "Último"
            }
        },
        "ordering": false,
        "paging":false
    });
    
    

});

