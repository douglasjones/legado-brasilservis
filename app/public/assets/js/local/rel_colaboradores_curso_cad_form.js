function fcCancelar(){
    var objParametros = {};
    sendPost("colaborador", "receptivoRelCursos", objParametros);
}

function fcCarregarGrid(){
    var objParametros = {
        "cursos_pk": $("#cursos_pk").val(),
        "colaboradores_pk": $("#colaboradores_pk").val(),
        "dt_execucao_ini": $("#dt_execucao_ini").val(),
        "dt_execucao_fim": $("#dt_execucao_fim").val(),
        "dt_validacao_ini": $("#dt_validacao_ini").val(),
        "dt_validacao_fim": $("#dt_validacao_fim").val()
    };         

    var v_url = routes_api("colaborador", "RelatorioColaboradorCurso", objParametros); 

    tblResultado = $("#tblResultado").DataTable({
        searching: false,
        paging: false,
        scrollX: true,
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
                    return full['ds_curso'];
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

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_execucao'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_validacao'];
                },
                'orderable': false,
                'searchable': false,
                width: '80px'

            },
        ]

    });
}

function fcExport(){
    location.href = window.location.protocol + "//" + window.location.host + 
    "/api/colaborador/exportRelCurso?cursos_pk="+$("#cursos_pk").val()+
    "&colaboradores_pk="+$("#colaboradores_pk").val()+
    "&dt_execucao_ini="+$("#dt_execucao_ini").val()+
    "&dt_execucao_fim="+$("#dt_execucao_fim").val()+
    "&dt_validacao_ini="+$("#dt_validacao_ini").val()+
    "&dt_validacao_fim="+$("#dt_validacao_fim").val();
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
    
    $("#dt_execucao").text($("#dt_execucao_ini").val()+" até "+$("#dt_execucao_fim").val());
    $("#dt_validacao").text($("#dt_validacao_ini").val()+" até "+$("#dt_validacao_fim").val());
    
    fcCarregarGrid();

});