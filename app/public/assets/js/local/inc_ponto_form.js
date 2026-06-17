function abrirModalPonto(i, colaborador_pk, leads_pk, dia){
    listarPontosDia(colaborador_pk, leads_pk, dia)
    $("#janela_ponto").modal("show");
}

function fcFecharModalPonto(){
    tblListarPontoDia.clear().destroy();
    $("#janela_ponto").modal("hide");
}
function formatarDataBR(data) {
    const partesData = data.split('-');
    return `${partesData[2]}/${partesData[1]}/${partesData[0]}`;
}
function listarPontosDia(colaborador_pk, leads_pk, dia){
   
    $("#date").text(formatarDataBR(dia))
    var objParametros = {
        "leads_pk": leads_pk,
        "colaborador_pk": colaborador_pk,
        "dt_ponto": dia
    };

    var v_url = routes_api("ponto_folha", "listarModalPonto", objParametros);

    tblListarPontoDia = $("#tblListarPontoDia").DataTable({
        searching: false,
        paging: false,
        processing: false,
        serverSide: false,
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
                    return full['indice'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['hora'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['tipo_apontamento'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['usuario_cadastro'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            }
        ]

    });
}