var tblResultado;

function fcCarregarGrid(){  
    var objParametros = {
        "empresa_pk": $("#empresa_pk").val(),
        "solicitante_pk": $("#solicitante_pk").val(),
        "usuario_aprovacao_pk": $("#usuario_aprovacao_pk").val(),
        "tipo_grupo_centro_custo_pk": $("#tipo_grupo_centro_custo_pk").val(),
        "grupo_lancamento_centro_custo_pk": $("#grupo_lancamento_centro_custo_pk").val(),
        "ic_status": $("#ic_status").val(),
        "dt_ini_cad": $("#dt_ini_cad").val(),
        "dt_fim_cad": $("#dt_fim_cad").val(),
        "dt_ini_aprov": $("#dt_ini_aprov").val(),
        "dt_fim_aprov": $("#dt_fim_aprov").val()
    };         
    var v_url = routes_api("compra", "relControleSolicitacaoCompra", objParametros); 
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
                    return full['ds_razao_social'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_solicitante'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_aprovador'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_solicitacao'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_aprovacao'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_tipo_grupo'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    if(full['tipo_grupo_centro_custo_pk']==1){
                        return full['ds_lead'];
                    }
                    else if(full['tipo_grupo_centro_custo_pk']==2){
                        return full['ds_colaborador'];
                    }
                    else{
                        return full['ds_fornecedor'];
                    }
                    
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            }
            , {
                mRender: function (data, type, full) {
                    return full['pk'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            }
        ]

    });
     
};

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','pesqControleSolicitacaoCompra' ,objParametros);
}

function fcExport(){

    var htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    htmlPlanilha += '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PlanilhaTeste</x:Name>';
    htmlPlanilha += '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>';
    htmlPlanilha += '<![endif]--></head><body><table>' + $("#form").html() + '</table></body></html>';
    
    var htmlBase64 = btoa(htmlPlanilha);
    var link = "data:application/vnd.ms-excel;base64," + htmlBase64;
    
    var hyperlink = document.createElement("a");
    hyperlink.download = "relControleSolicitacaoCompra.xls";
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
        
        
        