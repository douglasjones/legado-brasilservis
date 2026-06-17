var tblBeneficio;
var strComboBeneficio = "";

function fcFormatarGridBeneficio(){
    tblBeneficio = $("#tblBeneficio").DataTable(
        {
            "searching": false,
            "paging": false,
            "columnDefs" : [{
                orderable: false,
                targets: [0,1,2,3]
            }]
        }
    );
    return false;

}

function carregarListaComboBeneficio(){
    var objParametros = {
        pk:""
    };

    var arrCarregar = carregarController("beneficio", "listarPk", objParametros);

    if (arrCarregar.status == true){
        strComboBeneficio = "<select id='beneficios_pk' class='form-control form-control-sm' name='beneficios_pk'><option></option>";
        for(i = 0; i < arrCarregar.data.length; i++){
            strComboBeneficio = strComboBeneficio + "<option value='"+arrCarregar.data[i]['t_pk']+"'>"+arrCarregar.data[i]['t_ds_beneficio']+"</option>";
        }
        strComboBeneficio += "</select>";
        //Carrega os dados no combo.

        fcFormatarGridBeneficio();
        fcAtualizarDadosGridBeneficio();
    }
    else{

        utilsJS.toastNotify(false,'Falhar ao carregar o registro');

    }
}


function fcAtualizarDadosGridBeneficio(){

    var objParametros = {
        "colaboradores_pk":$("#colaborador_pk").val()
    };

    var arrCarregar = carregarController("colaborador", "listarBeneficioColaboradores", objParametros);

    if (arrCarregar.status == true){
        for(i = 0; i < arrCarregar.data.length; i++){

            //Adiciona a linha.
            fcIncluirBeneficio();

            //Pega as variaveis
            var cboBeneficiosPk = $("select[id='beneficios_pk']");
            var VlBeneficio = $("input[id='vl_beneficio']");
            var Obs = $("input[id='obs']");

            cboBeneficiosPk.get(i).value = arrCarregar.data[i]['beneficios_pk'];
            VlBeneficio.get(i).value = float2moeda(arrCarregar.data[i]['vl_beneficio']);
            Obs.get(i).value = arrCarregar.data[i]['obs'];
        }
    }
    else{

        utilsJS.toastNotify(false,'Falhar ao carregar o registro');
    }

}

function fcIncluirBeneficio(){

    tblBeneficio.row.add(
        [strComboBeneficio,
            "<input type='text' id='vl_beneficio' class='form-control form-control-sm' onkeypress='mascara(this,moeda)'/>",
            "<input type='text' class='form-control form-control-sm' id='obs' />",
            "<a class='function_delete'><span><i class='bi bi-x-circle' style='font-size:18px; color:blue' title='EXCLUIR'></i></span></a>"
        ]
    ).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcExcluirLinhaBeneficio);

    return false;
}

function fcExcluirLinhaBeneficio(){

    tblBeneficio.row($(this).parents('tr')).remove().draw();
    return false;
}

let a=0;
$(document).ready(function(){
    $(document).on('click', '#cmdIncluirBeneficio', fcIncluirBeneficio);
    if(a==0){
        carregarListaComboBeneficio();
        a++;
    }

    //faz a carga inicial do grid.
    //Atribui os eventos dos demais controles
});