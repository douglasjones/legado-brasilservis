var today = new Date();
var dd = today.getDate();
var mm = today.getMonth()+1; //January is 0!
var yyyy = today.getFullYear();

if(dd<10) {
    dd = '0'+dd
}
if(mm<10) {
    mm = '0'+mm
}
var dtAtual = dd+'/'+mm+'/'+yyyy;

function fcCarregarMateriaisImpressao(){

    var objParametros = {
        "leads_pk": $("#leads_pk").val(),
        "colaborador_pk": $("#pk").val(),
        "conjunto_material_pk":$("#conjunto_material_pk").val()
    };

    var v_url = routes_api("movimentacao_estoque", "listar_impressao", objParametros);

    //Trata a tabela
    tblMaterialImpressao = $('#tblMaterialImpressao').DataTable({
        searching: false,
        paging: false,
        processing: true,
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
                    return full['dt_entrega'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_devolucao'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['qtde'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['produtos_itens_pk']+" - "+full['ds_produto'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return "";
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            }
        ]
    });


    return false;
}

function printDiv() {
    var printContents = document.getElementById("printableArea").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
function fcVoltar(){
    if($("#local").val()==1){
        sendPost('menu_colaborador_cad_form.php',{token: token, pk: pk});
    }
    else if($("#local").val()==2){
        sendPost('movimentar_material_prod_res_form.php',{token: token, pk: pk});
    }
    else{

        sendPost('colaborador_cad_form.php',{token: token, colaborador_pk: pk});
    }

}
$(document).ready(function(){
    $("#exibir_colaborador").hide();
    if($("#pk").val()!=""){
        $("#exibir_colaborador").show();
    }

    fcCarregarMateriaisImpressao();
    $(document).on('click', '#cmdVoltar', fcVoltar);
    $(document).on('click', '#cmdImprimirModal', printDiv);

    $("#dt_atual").text(dtAtual);
    $("#dt_entrega").text(dtAtual);
});