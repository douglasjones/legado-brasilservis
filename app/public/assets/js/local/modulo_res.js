function fcCarregarGridModulo() {
    //tblResultado.clear().destroy();
    var objParametros = {
        "tipo_modulo_pk": $("#tipo_modulo_pk").val()
    };     
    
    var v_url = routes_api("modulo", "listarGrid", objParametros);

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
                'searchable': false,
                 width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_modulo'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_dominio'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'
            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="editar"></i></span></a> ';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';
                

                    return buttonPainel + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]

    });

    
    //Atribui os eventos na coluna ação.
    $('#tblResultado tbody').on('click', '.function_edit', function () {
        var data;
        if(tblResultado.row( $(this).parents('li')).data()){
            data = tblResultado.row( $(this).parents('li')).data();
        }
        else if(tblResultado.row( $(this).parents('tr')).data()){
            data = tblResultado.row( $(this).parents('tr')).data();
        }
        fcEditar(data['pk']);
        
    } );   


    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcExcluir(data['pk']);
    });

}

function fcCarregarTiposModulos() {
    //Carrega os tipos modulos
    var arrCarregar = carregarController("modulo", "listarTipoModulo", '');
    carregarComboAjax($("#tipo_modulo_pk"), arrCarregar, " ", "pk", "ds_tipo_modulo");
}

function fcIncluir() {
    var objParametros = {
        "pk":''
    };
    sendPost('modulo', 'cadForm' ,objParametros);
}

function fcEditar(pk) {
    var objParametros = {
        "pk":pk
    };
    sendPost('modulo', 'cadForm' ,objParametros);
}

function fcVoltar(){
    var objParametros = {};
    sendPost('menu','cpainel' ,objParametros);
}

function fcExcluir(v_pk){
    utilsJS.jqueryConfirm('Excluir ?', 'Deseja excluir o registro '+v_pk+'?',function(){
        if(v_pk != ""){
            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("modulo", "excluir", objParametros);

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true, arrExcluir.message);
                tblResultado.clear().destroy();
                fcCarregarGridModulo();
            }
            else{
                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
            }
        }
    })
}
function fcPesquisar() {
    tblResultado.clear().destroy();
    fcCarregarGridModulo();
}
$(document).ready(function () {
    fcCarregarGridModulo();
    fcCarregarTiposModulos();
    $(document).on('click', '#cmdIncluirModulo', fcIncluir);
    $(document).on('click', '#cmdVoltarModulo', fcVoltar);
    $(document).on('click', '#cmdPesquisarModulo', fcPesquisar);
})