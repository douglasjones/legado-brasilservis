function fcMascarasCamposServicosExtra(){
    $('#dt_ini_servico_extra').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

    $('#dt_fim_servico_extra').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });


    $("#dt_ini_servico_extra").keypress(function () {
        mascara(this, mdata);
    });
    $("#hr_ini_servico_extra").keypress(function () {
        mascara(this, horamask);
    });
    $("#hr_fim_servico_extra").keypress(function () {
        mascara(this, horamask);
    });
    $("#vl_servico_extra").keypress(function () {
        mascara(this,moeda);
    });
    $("#vl_mao_obra_servico_extra").keypress(function () {
        mascara(this,moeda);
    });

    $("#dt_fim_servico_extra").keypress(function () {
        mascara(this, mdata);
    });



}

function fcCarregarLeadServicoExtra(){
    var objParametros = {
        pk: ""
    };

    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    carregarComboAjax($("#leads_servico_extra_pk"), arrCarregar, " ", "pk", "ds_lead");

}
function fcCarregarProdutoServicoExtra(){
    var objParametros = {
    };

    var arrCarregar = carregarController("produto_servico", "listarTodos", objParametros);
    carregarComboAjax($("#pordutos_servicos_extra_pk"), arrCarregar, " ", "pk", "ds_produto_servico");

}
function fcLimparFormServicoExtra(){

    $("#leads_servico_extra_pk").val("");
    $("#pordutos_servicos_extra_pk").val("");
    $("#dt_ini_servico_extra").val("");
    $("#hr_ini_servico_extra").val("");
    $("#dt_fim_servico_extra").val("");
    $("#hr_fim_servico_extra").val("");
    $("#vl_servico_extra").val("");
    $("#vl_mao_obra_servico_extra").val("");
    $("#obs_servico_extra").val("");
}





//DOCUMENTOS
function fcEnviarDocumentoApontamento(){

    var strJSONDadosTabela =  fcFormatarDadosArquivosApontamento();

    var objParametros = {
        "colaborador_pk": $("#colaborador_pk_modal").val(),
        "ds_arquivo": strJSONDadosTabela,
    };


    var arrEnviar = carregarController("documento", "salvar", objParametros);
    if (arrEnviar.status == true){
    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcCarregarGridArquivosApontamento(){
    try {

        tblArquivosApontamento = $("#tblArquivosApontamento").DataTable(
            {
                "searching": false,
                "paging": false,
                "scrollX": true,
                "columnDefs" : [{
                    orderable: false,
                    targets: [0,1,2]
                }]
            }
        );
        return false;
    } catch (error) {
        utilsJS.toastNotify(false,error);
    }
}
//COMEÇO DOCUMENTOS UPLOAD

function fcAlterarNomeArquivoApontamento(v_arquivo){

    var objParametros = {
        "colaborador_pk": $("#colaborador_pk_modal").val(),
        "ds_arquivo": v_arquivo
    };

    var arrEnviar = carregarController("documento", "renomearArquivoColaborador", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        $("#ds_documento").text(arrEnviar.data);

    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcApagarArquivo(){
    var nome_arquivo = "";
    $('#tblArquivosApontamento tbody tr').click(function () {
        var colunas = $(this).children();
        nome_arquivo = $(colunas[0]).text();
        fcExcluirArquivo(nome_arquivo);
    });

    tblArquivosApontamento.row($(this).parents('tr')).remove().draw();
}



function fcExcluirArquivo(v_nome_arquivo){
    var objParametros = {
        "nome_arquivo": v_nome_arquivo
    };
    carregarController("documento", "removerArquivo", objParametros);
}
function fcIncluirLinhaArquivoApontamento(nome_original){
    tblArquivosApontamento.row.add(
        [$("#ds_documento").text(),
            nome_original,
            "<a class='function_delete'><span><i class='bi bi-x-circle' style='width: 18px;color:blue'></i></span></a>"
        ]
    ).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcApagarArquivo);
    return false;
}


function fcFormatarDadosArquivosApontamento(){

    var dsDocumento = "";
    var dsNomeOriginal = "";

    var arrKeys = [];
    arrKeys[0] = "ds_documento";
    arrKeys[1] = "ds_nome_original";

    var arrDados = [];
    var i = 0;
    $('#tblArquivosApontamento tbody tr').each(function () {
        var colunas = $(this).children();
        dsDocumento =  $(colunas[0]).text();
        dsNomeOriginal = $(colunas[1]).text();


        arrDados[i] = [dsDocumento, dsNomeOriginal];
        i++;
    });

    return arrayToJson(arrKeys, arrDados);

}
function fcSalvarDocumentosApontamento(formdata){
    routesDocumentos("documento", "salvarDocumento", formdata);
}
var formdata = null;


$(document).ready(function () {
    fcMascarasCamposServicosExtra();
    fcCarregarLeadServicoExtra();
    fcCarregarProdutoServicoExtra();


    //DOCUMENTOS
    formdata = new FormData();
    $('#fileuploadApontamento').change(function(){
        //on change event
        if($(this).prop('files').length > 0){
            $.each($(this).prop('files'), function (index, file) {
                formdata.append(index, file);
                fcSalvarDocumentosApontamento(formdata);

                $("#ds_nome_original_apontamento").html(file.name);

                fcAlterarNomeArquivoApontamento(file.name);
                fcIncluirLinhaArquivoApontamento(file.name);

                //fcEnviarDocumento();

            });

        }
    });

    fcCarregarGridArquivosApontamento();
});