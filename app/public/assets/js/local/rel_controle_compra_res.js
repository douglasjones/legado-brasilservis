var tblResultado;

function fcCarregarGrid(){  
    var objParametros = {
        "empresa_pk": $("#empresa_pk").val(),
        "fornecedor_pk": $("#fornecedor_pk").val(),
        "categoria_pk": $("#categoria_pk").val(),
        "tipo_grupo_centro_custo_pk": $("#tipo_grupo_centro_custo_pk").val(),
        "grupo_lancamento_centro_custo_pk": $("#grupo_lancamento_centro_custo_pk").val(),
        "ic_status": $("#ic_status").val(),
        "dt_ini_cad": $("#dt_ini_cad").val(),
        "dt_fim_cad": $("#dt_fim_cad").val(),
        "dt_ini_compra": $("#dt_ini_compra").val(),
        "dt_fim_compra": $("#dt_fim_compra").val()
    };         
    var v_url = routes_api("compra", "relControleCompra", objParametros); 
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
                    return full['ds_fornecedor'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_categoria'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_grupo_centro_custo'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    if(full['centro_custo_pk']==1){
                        return full['ds_lead'];
                    }
                    else if(full['centro_custo_pk']==2){
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
                    return full['compra_solicitacao_pk'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            }, {
                mRender: function (data, type, full) {
                    return full['dt_cadastro'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
             {
                mRender: function (data, type, full) {
                    return full['dt_entrega'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
             {
                mRender: function (data, type, full) {
                    return full['ds_numero_nota'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_pagamento'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return "R$ "+float2moeda(full['vl_pagamento']);
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_usuario_cadastro'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
        ]

    });
     
};

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','pesqControleCompra' ,objParametros);
}

function fcExport(){

    var htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    htmlPlanilha += '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PlanilhaTeste</x:Name>';
    htmlPlanilha += '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>';
    htmlPlanilha += '<![endif]--></head><body><table>' + $("#form").html() + '</table></body></html>';
    
    var htmlBase64 = btoa(htmlPlanilha);
    var link = "data:application/vnd.ms-excel;base64," + htmlBase64;
    
    var hyperlink = document.createElement("a");
    hyperlink.download = "relControleCompra.xls";
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
        
        
        