
function fcCarregarInformacoesProposta(){
    try {
        var objParametros = {
            "pk": $("#pk").val()
        };
        var arrCarregar = carregarController("propostas_facilities", "listarImpressaoProposta", objParametros);

        $("#logo").attr('src', arrCarregar.data[0]['ds_img_cliente']);
        $("#ds_lead").html(arrCarregar.data[0]['ds_lead']);
        $("#dt_base_categoria").html(arrCarregar.data[0]['dt_base_categoria']);
        $("#ds_aos_cuidados").html(arrCarregar.data[0]['ds_conta']);
        $("#n_orcamento").html(arrCarregar.data[0]['pk']);
        $("#ds_tipo").html(arrCarregar.data[0]['ds_tipo_proposta']);

        var ds_proposta = document.getElementById("ds_proposta");
        var vl_total = 0;
        for(var i=0; i < arrCarregar.data[0].dados_proposta.length; i++){
            var label = document.createElement("label");
            var labelDsValor = document.createElement("label");
            var br = document.createElement("br");
            vl_total += new Number(arrCarregar.data[0].dados_proposta[i].dadosItens[0]['ds_valor']);


            label.innerText = ' '+arrCarregar.data[0].dados_proposta[i]['ds_nome_grupo'];
            labelDsValor.innerText = " - R$ "+float2moeda(arrCarregar.data[0].dados_proposta[i].dadosItens[0]['ds_valor']);

            ds_proposta.appendChild(label);
            ds_proposta.appendChild(labelDsValor);
            ds_proposta.appendChild(br);

        }
        $("#ds_total").html(float2moeda(vl_total))

    } catch (error) {
        utilsJS.toastNotify(false, error);
    }
}

function fcImprimir() {
    var printContents = document.getElementById("printableArea").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
function fcVoltar(){
    if($("#ic_abertura").val() == 1){
        var objParametros = {
            "ic_abertura":1,
            "pk":$("#leads_pk").val()
        };
        sendPost('lead','leadMainPainel' ,objParametros);

    }else{
        var objParametros = {

        };
        sendPost('propostas_facilities','receptivo',objParametros);
    }
}

$(document).ready(function(){
    fcCarregarInformacoesProposta();
    $(document).on('click', '#cmdImprimir', fcImprimir);
    $(document).on('click', '#cmdVoltar', fcVoltar);
})