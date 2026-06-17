var tblResultado;
function fcPesquisar(){
	
    tblResultado.clear().destroy();
    fcCarregarGrid();
    fcLimparDados();
    
}

function fcVoltar(){
    var objParametros = {};
    sendPost('menu','operacional' ,objParametros);
}

function fcExcluir(v_pk, v_ds_equipe){
    var arrCarregar = permissao("equipe", "del");        

    if (arrCarregar.status != true) {
        utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        return false;
    }
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_ds_equipe+'?', function () {
        if(v_pk != ""){

            var objParametros = {
                "pk": v_pk
            };              
            
            var arrExcluir = carregarController("equipe", "excluir", objParametros);   

            if (arrExcluir.status == true){

                //Exibe a mensagem
                utilsJS.toastNotify(true, arrExcluir.message);         

                // Reload datable
                tblResultado.ajax.reload();

            }
            else{
                utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');         
            }
        }
        else{
            utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
        }
    });
}


function fcCarregarGrid(){
    var objParametros = {
        "nome": $("#nome").val(),
        "data": $("#data").val(),
        "tipo": $("#tipo").val(),
        "estado": $("#estado").val(),
        "cidade": $("#cidade").val()
    };     
    
    var v_url = routes_api("feriado", "listarGrid", objParametros);

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
                        return full['nome'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['data_feriado'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['tipo'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['estado'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['cidade'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';
                    
    
                        return  buttonDelete;
                    },
                    'orderable': false,
                    'searchable': false,
                }
            ]
    });
    
    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['pk'], data['nome']);
    } );            
    
}

function fcSalvar(){

    formdata.append("nome",$("#nome").val());
    formdata.append("data",$("#data").val());
    formdata.append("tipo",$("#tipo").val());
    formdata.append("cidade",$("#cidade").val());
    formdata.append("estado",$("#estado").val());
    utilsJS.loading('Salvando...');
    $.ajax({
        type: 'POST',
        url: '/api/feriado/salvar',
        data: formdata,
        processData: false,
        contentType: false,
        complete: function (response) {
            try {
                utilsJS.loaded();
                var log = JSON.parse(response.responseText);
                if(log.status==true){
                    fcLimparDados();
                    fcPesquisar();
                }
                else{
                    utilsJS.toastNotify(log.status,log.message);
                }

            } catch (e) {
                utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
            }
        }
    });

    utilsJS.loaded();
}

function fcLimparDados(){
    $("#nome").val("");
    $("#data").val("");
    $("#tipo").val("");
    $("#estado").val("");
    $("#cidade").val("");
}
$(document).ready(function(){
    formdata = new FormData();
    //faz a carga inicial do grid.
    fcCarregarGrid();

    $('#data').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

    $("#data").keypress(function(){
        mascara(this,mdata);
    });

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdSalvar', fcSalvar);
    $(document).on('click', '#cmdVoltar', fcVoltar);

});


