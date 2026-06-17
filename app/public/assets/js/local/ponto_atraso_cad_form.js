var tblResultado;
var click_id = 0;

function fcCarregarDatatable(){
    var objParametros = {
        "leads_pk":$("#leads_pk").val(),
        "colaborador_pk":$("#colaborador_pk").val(),
        "turnos_pk":$("#turnos_pk").val(),
        "funcao_pk":$("#funcao_pk").val(),
        "dt_ini": $("#dt_ini").val(),
        "dt_fim": $("#dt_fim").val()
    };

    var v_url = routes_api("ponto", "popUpAtraso", objParametros);

    //Trata a tabela
    tblResultado = $('#tblResultado').DataTable({
            searching: false,
            paging: false,
            scrollX: true,
            aLengthMenu: [10, 25, 50, 100],
            iDisplayLength: 10,
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
                        return full['ds_posto_trabalho'];
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
                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_turno'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_funcao'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_cel'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'
    
                },
                
                {
                    mRender: function (data, type, full) {
                        return full['TotalTempoAtraso'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return "";
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                }
            ],

        "rowCallback": function( row, data, index ) {
            //COR DOS HORARIOS
            if(data.ic_status== '5'){
                $(row).css('background-color','#e6df55');
            }
            if(data.ic_status=='10' && data.ds_status == "Ponto Não Registrado"){
                $(row).css('background-color','#f99856');
                $(row).css('color','#FFFFFF');
            }
            if (data.ic_status=='25'){
                $(row).css('background-color','#ec1c24');
                $(row).css('color','#FFFFFF');
            }
            if(data.t_ic_tipo_transacao==1){
                $(row).css('background-color','#ffb4b4');
            }
        },
    });
    $("#exibir").show();
    $("#loader").hide();
}
function fcExport(){

    var htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    htmlPlanilha += '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PlanilhaTeste</x:Name>';
    htmlPlanilha += '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>';
    htmlPlanilha += '<![endif]--></head><body><table>' + $("#form").html() + '</table></body></html>';
 
    var htmlBase64 = btoa(htmlPlanilha);
    var link = "data:application/vnd.ms-excel;base64," + htmlBase64;
 
    var hyperlink = document.createElement("a");
    hyperlink.download = "export.xls";
    hyperlink.href = link;
    hyperlink.style.display = 'none';
 
    document.body.appendChild(hyperlink);
    hyperlink.click();
    document.body.removeChild(hyperlink);
}

function fcAtualizar(){
    tblResultado.ajax.reload();
}

function fcCarregarLeads() {
    //Carrega os grupos

    var objParametros = {
        "ic_tipo_lead": 2,
        "leads_pai_pk": "",
        "ic_cliente": 1
    };

    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);

    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");

}

function fcCarregarColaborador() {
    //Carrega os grupos
    
    var objParametros = {
        "leads_pk": $("#leads_pk").val()
    };

    var arrCarregar = carregarController("colaborador", "listarColaboradorLead", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#colaborador_pk"), arrCarregar, " ", "pk", "ds_colaborador");

}

function fcCarregarTurno() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("agenda_colaborador_padrao", "listarTurno", objParametros);
    carregarComboAjax($("#turnos_pk"), arrCarregar, " ", "pk", "ds_turno");

}

function fcCarregarFuncao(){
    var objParametros = {
        pk:""
    };

    var arrCarregar = carregarController("produto_servico", "listarTodos", objParametros);
    carregarComboAjax($("#funcao_pk"), arrCarregar, " ", "pk", "ds_produto_servico");
    
}

$(document).ready(function(){    
    $(document).on('click', '#cmdAtualizar', fcAtualizar);

    var today = new Date();
    var periodo = new Date();
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
    periodo  = dd + '/' + mm + '/' + yyyy;
    
    $("#dt_emissao").text("Data e hora da atualização: "+today);
    $("#dt_ini").val(periodo);
    $("#dt_fim").val(periodo);
    
    fcCarregarDatatable();
    
   
    $("#tblPesq input").keyup(function(){
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
    $(".header-fixed").css('display', 'none');
    //atualiza de 3 em 3 min
    setInterval(function() {
        
        $("#dt_emissao").text("");
        var today = new Date();
        var periodo = new Date();
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
        periodo  = dd + '/' + mm + '/' + yyyy;
        
        $("#dt_emissao").text("Data e hora da atualização: "+today);


        tblResultado.ajax.reload();
      }, 180000);

      fcCarregarLeads();
      fcCarregarTurno();
      fcCarregarFuncao();
      $(".chzn-select").chosen({ allow_single_deselect: true });
      $("#leads_pk").change(function () {
        
        $(".chzn-select").chosen('destroy');
        fcCarregarColaborador();
        $(".chzn-select").chosen({ allow_single_deselect: true });

        tblResultado.clear().destroy();
        fcCarregarDatatable();

    });
    $("#colaborador_pk").change(function () {

        tblResultado.clear().destroy();
        fcCarregarDatatable();

    });
    $("#turnos_pk").change(function () {

        tblResultado.clear().destroy();
        fcCarregarDatatable();

    });
    $("#funcao_pk").change(function () {

        tblResultado.clear().destroy();
        fcCarregarDatatable();

    });

    
    
});





