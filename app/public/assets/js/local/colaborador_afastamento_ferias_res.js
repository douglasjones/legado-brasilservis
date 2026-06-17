var tblAfastamento;
var strComboAfastamento = "";
function fcFormatarGridAfastamento(){
    tblAfastamento = $("#tblAfastamento").DataTable(
        {
            "searching": false,
            "paging": false,
            "columnDefs" : [{
                orderable: false,
                targets: [0,1,2,3,4]
            }]
        }
    );
    return false;

}

function carregarListaComboAfastamento(){

    strComboAfastamento = "<select id='tipo_apontamento' class='form-control form-control-sm' name='tipo_apontamento'><option></option>";
    strComboAfastamento +="<option value='1'>Afastamento Médio</option>";
    strComboAfastamento +="<option value='2'>Férias</option>";
    strComboAfastamento += "</select>";
    //Carrega os dados no combo.

    fcFormatarGridAfastamento();
    fcAtualizarDadosGridAfastamento();
}


function fcAtualizarDadosGridAfastamento(){

    var objParametros = {
        "colaborador_pk":$("#colaborador_pk").val(),
    };

    var arrCarregar = carregarController("colaborador", "listarAfastamentoColaboradores", objParametros);

    if (arrCarregar.status == true){
        for(i = 0; i < arrCarregar.data.length; i++){

            //Adiciona a linha.
            fcIncluirAfastamento();

            //Pega as variaveis
            var tipo_apontamento = $("select[id='tipo_apontamento']");
            var dt_inicio = $("input[id='dt_inicio_afastamento']");
            var dt_fim = $("input[id='dt_fim_afastamento']");
            var obs = $("input[id='obs_afastamento']");

            tipo_apontamento.get(i).value = arrCarregar.data[i]['tipo_apontamento'];
            dt_inicio.get(i).value = arrCarregar.data[i]['dt_inicio'];
            dt_fim.get(i).value = arrCarregar.data[i]['dt_fim'];
            obs.get(i).value = arrCarregar.data[i]['ds_obs'];
        }
    }
    else{

        utilsJS.toastNotify(false,'Falhar ao carregar o registro');
    }

}

function fcIncluirAfastamento(){

    tblAfastamento.row.add(
        [strComboAfastamento,
            "<input type='text' id='dt_inicio_afastamento' maxlength='10' class='form-control form-control-sm' onkeypress='mascara(this,mdata)'/>",
            "<input type='text' id='dt_fim_afastamento' maxlength='10'  class='form-control form-control-sm' onkeypress='mascara(this,mdata)'/>",
            "<input type='text' class='form-control form-control-sm' id='obs_afastamento' />",
            "<a class='function_delete'><span><i class='bi bi-x-circle' style='font-size:18px; color:blue' title='EXCLUIR'></i></span></a>"
        ]
    ).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcExcluirLinhaAfastamento);

    return false;
}

function fcExcluirLinhaAfastamento(){

    tblAfastamento.row($(this).parents('tr')).remove().draw();

    return false;
}

function fcFormatarDadosAfastamento(){

    var tipo_apontamento = $("select[id='tipo_apontamento']");
    var dt_inicio = $("input[id='dt_inicio_afastamento']");
    var dt_fim = $("input[id='dt_fim_afastamento']");
    var obs = $("input[id='obs_afastamento']");

    var alert = 0;


    var arrKeys = [];
    arrKeys[0] = "tipo_apontamento";
    arrKeys[1] = "dt_inicio";
    arrKeys[2] = "dt_fim";
    arrKeys[3] = "obs";

    var arrDados = [];

    var  data = tblAfastamento.rows().data();

    if(data.length >0){
        for(i = 0; i < data.length; i++){

            if(tipo_apontamento.get(i).value == ""){
                alert = 1;
            }
            if(dt_inicio.get(i).value == ""){
                alert = 1;
            }

            arrDados[i] = [tipo_apontamento.get(i).value, dt_inicio.get(i).value, dt_fim.get(i).value, obs.get(i).value];

        }
    }


    if(alert>0){
        return 1;
    }
    else{
        return arrayToJson(arrKeys, arrDados);
    }


}

$(document).ready(function(){

    $(document).on('click', '#cmdIncluirAfastamento', fcIncluirAfastamento);
    if($("#colaborador_pk").val()!=""){
        carregarListaComboAfastamento();
    }
    //faz a carga inicial do grid.
    //Atribui os eventos dos demais controles

});
