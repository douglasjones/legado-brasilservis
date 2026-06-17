var tblResultado;

function formatReal( int )
{
        var tmp = int+'';
        tmp = tmp.replace(/([0-9]{2})$/g, ",$1");
        if( tmp.length > 6 )
                tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");

        return tmp;
}

function fcCarregarGrid(){  
    var objParametros = {
        "colaborador_pk": $("#colaboradores_pk").val(),
        "leads_pk": $("#leads_pk").val(),
        "dt_ini": $("#dt_ini").val(),
        "dt_fim": $("#dt_fim").val()
    };         
    var v_url = routes_api("agenda_colaborador_apontamento", "relControleFt", objParametros); 
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
                    return full['ds_usuario'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_mes_apontamento'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_apontamento'];
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
                    return full['ds_colaborador'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            }, {
                mRender: function (data, type, full) {
                    return full['motivo_ft_pk'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            }, {
                mRender: function (data, type, full) {
                    return full['vl_ft'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
             {
                mRender: function (data, type, full) {
                    return full['ds_obs'];
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
    sendPost('relatorio','pesqControleFts' ,objParametros);
}

function fcExport(){

var htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
htmlPlanilha += '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PlanilhaTeste</x:Name>';
htmlPlanilha += '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>';
htmlPlanilha += '<![endif]--></head><body><table>' + $("#form").html() + '</table></body></html>';

var htmlBase64 = btoa(htmlPlanilha);
var link = "data:application/vnd.ms-excel;base64," + htmlBase64;

var hyperlink = document.createElement("a");
hyperlink.download = "relControleFt.xls";
hyperlink.href = link;
hyperlink.style.display = 'none';

document.body.appendChild(hyperlink);
hyperlink.click();
document.body.removeChild(hyperlink);
}

function fcPegarTipoOC(){    
var objParametros = {
    "pk": tipos_ocorrencias_pk
};          
var arrCarregar = carregarController("tipo_ocorrencia", "listarPk", objParametros);    
$("#ds_tipo_oc").text(arrCarregar.data[0]['ds_tipo_ocorrencia']);
}
function fcPegarUsuarioCad(){    
var objParametros = {
    "pk": usuario_cadastro_pk
};          
var arrCarregar = carregarController("usuario", "listarPk", objParametros);      
$("#ds_usuario_cadastro").text(arrCarregar.data[0]['ds_usuario']);
}
function fcPegarAgendadoPara(){    
var objParametros = {
    "pk": usuario_agendado_para
};          
var arrCarregar = carregarController("usuario", "listarPk", objParametros);      
$("#ds_usuario_agendado_para").text(arrCarregar.data[0]['ds_usuario']);
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


