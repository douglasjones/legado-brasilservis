function fcCarregarGridFaturamento() {
    var objParametros = {
        "empresas_pk": $("#empresas_pk").val(),
        "dt_faturamento_ini": $("#dt_faturamento_ini").val(),
        "dt_faturamento_fim": $("#dt_faturamento_fim").val(),
        "ic_status": $("#ic_status").val(),
        "tipo_contrato_pk": $("#tipo_contrato_pk").val(),
        "n_emissoes": $("#n_emissoes").val()

    };
    var v_url = routes_api("faturamento", "listarDataTable", objParametros);

    tblResultado = $("#tblResultado").DataTable({
        searching: true,
        paging: true,
        scrollX: true,
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
                    return full['origem_pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_faturamento_ini'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_faturamento_fim'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['vl_total_faturamento'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['n_emissoes'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_status'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonEdit = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="Editar"></i></span></a>';
                    var buttonEmissoes = ' <a class="function_emissoes"><span><i class="bi bi-clipboard-check" style="font-size=18px;color:blue" title="Listar Emissões"></i></span></a>';
                    var buttonVerao = " <a class='function_copiar'><span><i class='bi bi-file-earmark-break' style='font-size:14px; color:blue' title='Copiar'></i></span></a>";
                    var buttonCancelar = " <a class='function_cancelar'><span><i class='bi bi-file-x' style='font-size:14px; color:blue' title='Cancelar'></i></span></a>";
                    var buttonExcluir = " <a class='function_excluir'><span><i class='bi bi-x-circle' style='font-size:14px; color:blue' title='Excluir'></i></span></a>";
        
                    return buttonEdit + buttonEmissoes + buttonVerao + buttonCancelar + buttonExcluir;
                },
                'orderable': false,
                'searchable': false,
                width: '60px'
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

        if(data['ds_status'] == 'Faturamento Gerado'){
            fcEditar(data['pk']);
        }else{
            sweetMensagem('warning', "Esse item já foi processado, não pode ser editado");
        }
        
    } );   


    $('#tblResultado tbody').on('click', '.function_emissoes', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        if(data['ds_status'] != 'Faturamento Gerado'){
            fcHistoricoEmissoes(data['pk']);
        }else{ 
            sweetMensagem('warning', "Esse item ainda não foi processado");
        }
    });

    $('#tblResultado tbody').on('click', '.function_copiar', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        //if(data['ds_status'] != 'Faturamento Gerado'){
            fcAbrirModalCopiar(data['pk']);
        /* }else{ 
            utilsJS.toastNotify(false, "Esse item ainda não foi processado");
        }*/
    });
    $('#tblResultado tbody').on('click', '.function_excluir', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        if(data['ds_status'] == 'Faturamento Cancelado'){
            fcExcluir(data['pk']);
        }else{ 
            sweetMensagem('warning', "Esse item não pode ser excluído");
        }
    });
    $('#tblResultado tbody').on('click', '.function_cancelar', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        if(data['ds_status'] != 'Faturamento Gerado'){
            fcCancelar(data['pk']);
        }else{ 
            sweetMensagem('warning', "Esse item ainda não foi processado");
        }
    });

}

function fcIncluir() {
    var objParametros = {
        "pk":''
    };
    sendPost('faturamento', 'cadForm' ,objParametros);
}

function fcEditar(pk) {
    var objParametros = {
        "faturamento_pk": pk,
        "acao": 2
    };
    sendPost('faturamento', 'faturamentoItens' ,objParametros);
}

function fcVoltar() {
    var objParametros = {
        "pk":''
    };
    sendPost('menu', 'financeiro' ,objParametros);
}

function fcHistoricoEmissoes(pk) {
    var objParametros = {
        "pk":pk
    };
    sendPost('faturamento', 'listarEmissoes' ,objParametros);
}

function fcExcluir(v_pk){
   /* var arrCarregar = permissao("faturamento_excluir", "del");
    if (arrCarregar.status != true){
        utilsJS.toastNotify(false,'Você não tem permissão');
        return false;
    }*/
    if(v_pk != ""){
        var objParametros = {
            "pk": v_pk
        };

        var arrExcluir = carregarController("faturamento", "excluir", objParametros);

        if (arrExcluir.status == true){

            //Exibe a mensagem
            utilsJS.toastNotify(true, arrExcluir.message);
        }
        else{
            utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
        }
    }
}


function fcCancelar(v_pk){
    var arrCarregar = permissao("faturamento_cancelar", "del");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false,'Você não tem permissão');
        return false;
    }
    var objParametros = {
        "pk": v_pk
    };
    
    var arrCarregar = carregarController("faturamento", "cancelarFaturamento", objParametros);
    if (arrCarregar.status != true){
        utilsJS.toastNotify(true,'Faturamento cancelado com sucesso!');
    }else{
        utilsJS.toastNotify(false, "Ocorreu um erro na requisição <br /> Contate o suporte");
    }

}

$(document).ready(function () {
    fcCarregarGridFaturamento();
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);
    $(document).on('click', '#cmdCancelar', fcCancelar);

    $('#dt_faturamento_ini').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_faturamento_ini").keypress(function(){
        mascara(this,mdata);
    });
   
    $('#dt_faturamento_fim').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_faturamento_fim").keypress(function(){
        mascara(this,mdata);
    });
})