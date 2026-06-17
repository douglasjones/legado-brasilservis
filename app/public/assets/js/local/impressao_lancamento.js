function fcCarregar(){

    var objParametros = {
        "pk": $("#pk").val()
    };

    var arrCarregar = carregarController("lancamento", "listarImpressao", objParametros);

        $("#lancamentos_pk").html($("#pk").val())
        $("#dt_cadastro").html(arrCarregar.data[0]['dt_cadastro'])
        $("#ds_usuario").html(arrCarregar.data[0]['ds_usuario'])
        $("#ds_operacao").html(arrCarregar.data[0]['ds_operacao'])
        $("#ds_metodo_pagamento").html(arrCarregar.data[0]['ds_metodo_pagamento'])
        $("#ds_empresas").html(arrCarregar.data[0]['ds_razao_social'])
        $("#ds_conta_bancaria").html(arrCarregar.data[0]['ds_conta_bancaria'])
        $("#ds_lancamento").html(arrCarregar.data[0]['ds_lancamento'])
        $("#ds_tipo_grupo").html(arrCarregar.data[0]['ds_tipo_grupo'])
        $("#ds_lead").html(arrCarregar.data[0]['ds_recebido_de'])
        $("#ds_grupo_lancamento_centro_custo").html(arrCarregar.data[0]['ds_recebido_pago_origem'])
        $("#ds_leads_clientes").html(arrCarregar.data[0]['ds_leads_clientes'])
        $("#vl_lancamento").html(arrCarregar.data[0]['vl_lancamento'])
        $("#dt_vencimento").html(arrCarregar.data[0]['dt_vencimento'])
        $("#ds_tipo_operacao").html(arrCarregar.data[0]['ds_operacao'] +' - '+arrCarregar.data[0]['ds_tipo_operacao'])
        $("#ds_contrato").html(arrCarregar.data[0]['ds_lancamento_contrato'])
        $("#ds_posto_trabalho").html(arrCarregar.data[0]['ds_lancamento_posto_trabalho'])
        $("#ds_cliente").html(arrCarregar.data[0]['ds_cliente'])
        $("#ic_status").html(arrCarregar.data[0]['ds_status_pagamento'])
        $("#obs").html(arrCarregar.data[0]['obs'])
        
}

function printDiv() {
    var printContents = document.getElementById("printableArea").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}

function fcCancelar() {
    var objParametros = {
        'rh':1
    };
    sendPost('lancamento', 'contasPagarReceberReceptivo' ,objParametros);
}

$(document).ready(function () {
    fcCarregar();
    $(document).on('click', '#cmdImprimir', printDiv);
    $(document).on('click', '#cmdVoltar', fcCancelar);
})