var tblQualificacao;
var strComboProdutoServico = "";
var tblDocumentos;
function fcFormatarGridCurso(){
    tblCurso = $("#tblCurso").DataTable(
        {
            "searching": false,
            "paging": false,
            "columnDefs" : [{
                orderable: false,
                targets: [0,1,2,3],
            }]
        }
    );
    return false;

}

function carregarListaComboCurso(){
    var objParametros = {
        pk:""
    };

    var arrCarregar = carregarController("curso", "listarTodosAtivo", objParametros);


    if (arrCarregar.status == true){
        strComboCurso = "<select id='cursos_pk' class='form-control form-control-sm' name='cursos_pk'><option></option>";
        for(i = 0; i < arrCarregar.data.length; i++){
            strComboCurso = strComboCurso + "<option value='"+arrCarregar.data[i]['pk']+"'>"+arrCarregar.data[i]['ds_curso']+"</option>";
        }
        strComboCurso += "</select>";
        //Carrega os dados no combo.
        fcFormatarGridCurso();
        fcAtualizarDadosGridCurso();
    }
    else{
        utilsJS.toastNotify(false,'Falhar ao carregar o registro');

    }
}


function fcAtualizarDadosGridCurso(){
    
    if($("#colaborador_pk").val()!=""){
       
        var objParametros = {
            "colaboradores_pk":$("#colaborador_pk").val()
        };

        var arrCarregar = carregarController("colaborador", "listarCursoColaboradores", objParametros);
        if (arrCarregar.status == true){
            for(i = 0; i < arrCarregar.data.length; i++){

                //Adiciona a linha.
                fcIncluirCurso();

                //Pega as variaveis
                var cboCursoPk = $("select[id='cursos_pk']");
                var dt_execucao = $("input[id='dt_execucao']");
                var dt_validacao = $("input[id='dt_validacao']");

                cboCursoPk.get(i).value = arrCarregar.data[i]['cursos_pk'];
                dt_execucao.get(i).value = arrCarregar.data[i]['dt_execucao'];
                dt_validacao.get(i).value = arrCarregar.data[i]['dt_validacao'];
            }
        }
        else{

            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
        }
    }


}

function fcIncluirCurso(){
    tblCurso.row.add(
        [strComboCurso,
            "<input type='text' id='dt_execucao' class='form-control form-control-sm dt_execucao' maxlength=10 onkeypress='mascara(this,mdata)' style=' width:132px' />",
            "<input type='text' class='form-control form-control-sm dt_validacao' id='dt_validacao' maxlength=10 onkeypress='mascara(this,mdata)' style='width:132px'/>",
            "<a class='function_delete'><span><i class='bi bi-x-circle' style='font-size:18px; color:blue' title='EXCLUIR'></i></span></a>"
        ]
    ).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcExcluirLinhaCurso);
    $('.dt_validacao').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $('.dt_execucao').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    return false;
}

function fcExcluirLinhaCurso(){

    tblCurso.row($(this).parents('tr')).remove().draw();

    return false;
}
function fcFormatarDadosCurso(){

    var cursos_pk = $("select[id='cursos_pk']");
    var dt_execucao = $("input[id='dt_execucao']");
    var dt_validacao = $("input[id='dt_validacao']");

    var arrKeys = [];
    arrKeys[0] = "cursos_pk";
    arrKeys[1] = "dt_execucao";
    arrKeys[2] = "dt_validacao";

    var arrDados = [];


    for(i = 0; i < cursos_pk.length; i++){
        if(cursos_pk.get(i).value == ""){
            cursos_pk.get(i).focus();
            return false;

        }

        arrDados[i] = [cursos_pk.get(i).value, (dt_execucao.get(i).value), dt_validacao.get(i).value];

    }

    return arrayToJson(arrKeys, arrDados);

}

$(document).ready(function(){

    $(document).on('click', '#cmdIncluirCurso', fcIncluirCurso);
    carregarListaComboCurso();
 
    //faz a carga inicial do grid.
    //Atribui os eventos dos demais controles

});