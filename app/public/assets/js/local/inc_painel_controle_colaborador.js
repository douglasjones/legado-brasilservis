function fcAbrirPainelControle(
            colaborador_pk,
            ds_colaborador,
            ds_cpf,
            ic_status_rh,
            ds_status_app,
            ds_lead,
            ds_turno,
            escala
)
{

    //LIMPA A VARIAVEL
    $("#painel_colaborador_pk").val("");
    $("#painel_ds_colaborador").text("");
    $("#painel_ds_cpf").text("");
    $("#painel_ic_status_rh").text("");
    $("#painel_ic_status_app").text("");
    $("#painel_ds_lead").text("");
    $("#painel_ds_turno").text("");
    $("#painel_escala").text("");
    $("#painel_foto_colaborador").text("");


    //APLICA OS VALORES DA VARIAVEL
    $("#painel_colaborador_pk").val(colaborador_pk);
    $("#painel_ds_colaborador").text(ds_colaborador);
    $("#painel_ds_cpf").text(ds_cpf);
    $("#painel_ic_status_rh").text(ic_status_rh);
    $("#painel_ic_status_app").text(ds_status_app);
    $("#painel_ds_lead").text(ds_lead);
    $("#painel_ds_turno").text(ds_turno);
    $("#painel_escala").text(escala);


    //CARREGA A IMAGEM DO COLABORADOR
    fcCarregarImg();

    $("#painel_controle_colaborador_modal").modal("show")

}

function fcCarregarImg(){
    var objParametros = {
        "pk": $("#painel_colaborador_pk").val()
    };
    var arrCarregar = carregarController("colaborador", "listarPk", objParametros);
    //NewWindow(v_last_url)
    if (arrCarregar.status == true){
        if(arrCarregar.data[0]['ds_imagem']!=null){
            var ds_imagem = arrCarregar.data[0]['ds_imagem'];
            $("#painel_foto_colaborador").html("<img width='100' height='100' src='data:image/png;base64,"+(ds_imagem)+"'>");
        }else{
            $("#painel_foto_colaborador").html(' <img src="/assets/img/profile/avatar.jpg" width="100" height="100">');
        }
    }
    else{
        utilsJS.toastNotify(false,'Falhar ao carregar o registro');
    }
}

function fcAcessarFicha(){
    //função está dentro de colaborador_res_form.js
    fcEditar($("#painel_colaborador_pk").val());
}

$(document).ready(function()
    {
        $(document).on('click', '.acessarFicha', fcAcessarFicha);
    }
);