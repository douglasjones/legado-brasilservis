var tblResultado;
function fcPesquisar(){
	
    tblResultado.clear().destroy();
    fcCarregarGrid();
    
}

function fcIncluir(){

    var objParametros = {

    };
    sendPost('afd','cadForm',objParametros);

}


function fcCarregarGrid(){
    var objParametros = {
    };     
        
    var v_url = routes_api("afd", "listarGrid", objParametros);

    //Trata a tabela
    tblResultado = $('#tblResultado').DataTable({
        searching: true,
        paging: true,
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
        order: [
            [0, "asc"]
        ],
        columns: [
            {
                mRender: function (data, type, full) {
                    return full['pk'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_lead'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_colaborador'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_ini']+" - "+full['dt_fim'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_download" target="_blank"><i class="fa fa-download" style="font-size:18px; color:blue" title="DOWNLOAD DOCUMENTO"></i> ';
                        

                    return buttonPainel;
                },
                'orderable': false,
                'searchable': false,
            }
        ]
    });
    
    
    //Atribui os eventos na coluna ação.
    $('#tblResultado tbody').on('click', '.function_download', function () {
        var data;
        if(tblResultado.row( $(this).parents('li')).data()){
            data = tblResultado.row( $(this).parents('li')).data();
        }
        else if(tblResultado.row( $(this).parents('tr')).data()){
            data = tblResultado.row( $(this).parents('tr')).data();
        }
        fcDownloadAfd(data['pk']);
        
    } );  
}

function fcDownloadAfd(pk){
    /*if(minutos_passados<=6){
        
        utilsJS.toastNotify(false, "Sua nota ainda está sendo processada");
        return false;
    }*/
    var job = 'downloadAfd/'+pk

    var url = routes_api('afd', job, {});
    window.open(url, '_blank');
}
function fcVoltar(){
    var objParametros = {};
    sendPost('menu','rh' ,objParametros);
}



$(document).ready(function(){

    //faz a carga inicial do grid.
    fcCarregarGrid();

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);

});


