function fcCarregarCoberturaTrocaEscala(colaborador_pk) {
    if(colaborador_pk > 0){
        var objParametros = {
            pk: colaborador_pk
        };
        var arrCarregar = carregarController("colaborador", "RelatorioDadosColaborador", objParametros);

        var qualificacao = arrCarregar.data[0]['ds_qualificacao'];
        qualificacao = qualificacao.replace(/,/g , "");

        var objParametros = {
            ds_produtos_servicos: qualificacao,
            colaborador_pk: colaborador_pk
        };

        var arrCarregar = carregarController("colaborador", "listarColaboradoresQualificacao", objParametros);
        carregarComboAjax($("#colaborador_cobertura_troca_escala_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    }
}

function fcLimparFormTrocaEscala(){
    $("#form_trocaEscala select").val("");
    $("#form_trocaEscala input").val("");
    $("#form_trocaEscala textarea").val("");

}
