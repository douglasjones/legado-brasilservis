var tblContatos;
var rLinhaSelecionada = null;

function fcValidarForm(){
    
    if($('#ic_tipo_lead').val()==""){
        $("#alert_tipo_lead").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_tipo_lead").slideUp(500);
        });
        $('#ic_tipo_lead').focus();
        return false;
    }
    if($('#ds_lead').val()==""){
        $("#alert_ds_lead").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_lead").slideUp(500);
        });
        $('#ds_lead').focus();
        return false;
    }
    if($("#ic_tipo_lead").val()==1){
        if($('#ds_cpf_cnpj').val()==""){
            $("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_cnpj").slideUp(500);
            });
            $('#ds_cpf_cnpj').focus();
            return false;
        }
        else  if($('#ds_cpf_cnpj').val()!=""){
            var ds_cpf_cnpj = $('#ds_cpf_cnpj').val();
            if(ds_cpf_cnpj.length < 14 ){
                $("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                    $("#alert_cnpj").slideUp(500);
                });
                $('#ds_cpf_cnpj').focus();
                return false;
            }
            else if(ds_cpf_cnpj.length > 14 && ds_cpf_cnpj.length < 18 ){
                $("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                    $("#alert_cnpj").slideUp(500);
                });
                $('#ds_cpf_cnpj').focus();
                return false;
            }
        }
    }

    if($("#ic_tipo_lead").val()==2 && $("#leads_pai_pk").val()==""){

        if($('#ds_cpf_cnpj').val()==""){
            $("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_cnpj").slideUp(500);
            });
            $('#ds_cpf_cnpj').focus();
            return false;
        }
        else  if($('#ds_cpf_cnpj').val()!=""){

            var ds_cpf_cnpj = $('#ds_cpf_cnpj').val();
            if(ds_cpf_cnpj.length < 14 ){

                $("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                    $("#alert_cnpj").slideUp(500);
                });
                $('#ds_cpf_cnpj').focus();
                return false;
            } else if(ds_cpf_cnpj.length > 14 && ds_cpf_cnpj.length < 18 ){

                /*$("#alert_cnpj").fadeTo(2000, 500).slideUp(500, function(){
                    $("#alert_cnpj").slideUp(500);
                });
                $('#ds_cpf_cnpj').focus();
                return false;*/
            }
        }
    }

    if($('#ds_cep').val()==""){
        $("#alert_ds_cep").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_cep").slideUp(500);
        });
        $('#ds_cep').focus();
        return false;
    }
    if($('#ds_endereco').val()==""){
        $("#alert_ds_endereco").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_endereco").slideUp(500);
        });
        $('#ds_endereco').focus();
        return false;
    }
    if($('#ds_numero').val()==""){
        $("#alert_ds_numero").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_numero").slideUp(500);
        });
        $('#ds_numero').focus();
        return false;
    }
    if($('#ds_bairro').val()==""){
        $("#alert_cidade_bairro").show();
        $("#alert_ds_bairro").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_bairro").slideUp(500);
        });
        $('#ds_bairro').focus();
        return false;
    }
    if($('#ds_cidade').val()==""){
        $("#alert_cidade_bairro").show();
        $("#alert_ds_cidade").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_cidade").slideUp(500);
        });
        $('#ds_cidade').focus();
        return false;
    }
    if($('#ds_uf').val()==""){
        $("#alert_ds_uf").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_uf").slideUp(500);
        });
        $('#ds_uf').focus();
        return false;
    }
    if($('#ic_cliente').val()==""){
        $("#alert_ic_cliente").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ic_cliente").slideUp(500);
        });
        $('#ic_cliente').focus();
        return false;
    }

    fcEnviar();
}


function fcEnviar(){
    try {
        //Contatos
        var strJSONDadosTabela = fcFormatarDadosContato();

        //Materiais
        //var strJSONDadosMateriais = fcFormatarDadosMateriais();

        //Imposto
        //var strJSONDadosImposto = fcFormatarDadosImposto();
        //Imposto
        //var strJSONDadosDesconto = fcFormatarDadosDesconto();

        var v_ds_lead = $("#ds_lead").val();
        var v_ds_endereco = $("#ds_endereco").val();
        var v_ds_numero = $("#ds_numero").val();
        var v_ds_complemento = $("#ds_complemento").val();
        var v_ds_cep = $("#ds_cep").val();
        var v_ds_bairro = $("#ds_bairro").val();
        var v_ds_cidade = $("#ds_cidade").val();
        var v_ds_uf = $("#ds_uf").val();
        var v_ic_cliente = $("#ic_cliente").val();
        var v_n_qtde_torres = $("#n_qtde_torres").val();
        var v_ds_obs = $("#ds_obs").val();
        var v_ds_razao_social = $("#ds_razao_social").val();
        var v_ds_cpf_cnpj = $("#ds_cpf_cnpj").val();
        var v_ds_ie = $("#ds_ie").val();
        var v_ds_tel_lead = $("#ds_tel_fixo").val();
        var v_ds_fax = $("#ds_tel_fixo1").val();
        var v_ds_site = $("#ds_site").val();
        var v_ds_email_lead = $("#ds_email_contato_receita").val();
        var v_supervisores_pk = $("#supervisores_pk").val();
        var v_supervisor1_pk = $("#supervisor1_pk").val();
        var v_supervisor2_pk = $("#supervisor2_pk").val();
        var v_responsavel_pk = $("#responsavel_pk").val();
        var v_segmentos_pk = $("#segmentos_pk").val();
        var v_dia_faturamento = $("#dia_faturamento").val();
        var v_leads_pai_pk = $("#leads_pai_pk").val();
        var v_ic_tipo_lead = $("#ic_tipo_lead").val();
        var v_ds_tipo_lead = $("#ds_tipo_lead").val();
        var v_ds_porte = $("#ds_porte").val();
        var t_dt_abertura = $("#dt_abertura").val();
        var v_ds_atividade_principal_receita = $("#ds_atividade_principal_receita").val();
        var v_ds_atividade_secundaria_receita = $("#ds_atividade_secundaria_receita").val();
        var v_ds_socio1 = $("#ds_socio1").val();
        var v_ds_socio2 = $("#ds_socio2").val();
        var v_ds_socio3 = $("#ds_socio3").val();

        var objParametros = {
            "pk": $("#leads_pk").val(),
            "ds_lead": (v_ds_lead),
            "ds_endereco": (v_ds_endereco),
            "ds_numero": (v_ds_numero),
            "ds_complemento": (v_ds_complemento),
            "ds_cep": (v_ds_cep),
            "ds_bairro": (v_ds_bairro),
            "ds_cidade": (v_ds_cidade),
            "ds_uf": (v_ds_uf),
            "ic_cliente": (v_ic_cliente),
            "n_qtde_torres": (v_n_qtde_torres),
            "ds_obs": (v_ds_obs),
            "ds_razao_social": (v_ds_razao_social),
            "ds_cpf_cnpj": (v_ds_cpf_cnpj),
            "ds_ie": (v_ds_ie),
            "ds_tel": (v_ds_tel_lead),
            "ds_fax": (v_ds_fax),
            "ds_site": (v_ds_site),
            "leads_pai_pk": (v_leads_pai_pk),
            "ic_tipo_lead": (v_ic_tipo_lead),
            "supervisores_pk": (v_supervisores_pk),
            "supervisor1_pk": (v_supervisor1_pk),
            "supervisor2_pk": (v_supervisor2_pk),
            "responsavel_pk": (v_responsavel_pk),
            "ds_email": (v_ds_email_lead),
            "segmentos_pk": (v_segmentos_pk),
            "dia_faturamento": (v_dia_faturamento),
            "contatos_lead": (strJSONDadosTabela),
            "ds_tipo": (v_ds_tipo_lead),
            "ds_porte": (v_ds_porte),
            "dt_abertura": (t_dt_abertura),
            "ds_atividade_principal": (v_ds_atividade_principal_receita),
            "ds_atividade_secundaria": (v_ds_atividade_secundaria_receita),
            "ds_socio1": (v_ds_socio1),
            "ds_socio2": (v_ds_socio2),
            "ds_socio3": (v_ds_socio3),
        };
        var arrEnviar = carregarController("lead", "salvar", objParametros);
        //NewWindow(v_last_url)
        if (arrEnviar.status == true){
            // Reload datable
            utilsJS.toastNotify(true, arrEnviar.message);
            if($("#ic_processo_comercial").val() != 1){
                var objParametros = {};
                sendPost('lead','receptivo' ,objParametros);
            }else{
                //sendPost("comercial_painel_res_form.php", {token: token, ic_abertura: 2});
            }
        }
        else{

            utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
        }
    } catch (error) {

        utilsJS.toastNotify(false, error);
    }

}

function fcCancelar(){
    var objParametros = {
        "local":$("#local").val()
    };
    sendPost('lead','receptivo' ,objParametros);
}
// ---------------------------------------------------------
//Inicio das funcoes da tela de contato (Modal).

function fcCarregarGridContato(){



    if($("#leads_pk").val()==""){
        tblContatos = $('#tblContatos').DataTable( {
            responsive: true,
            scrollX: true,
        });

        //Atribui os eventos na coluna ação.
        $('#tblContatos tbody').on('click', '.function_edit', function (e) {
            e.preventDefault();
            let element = $(this);
            $("#ds_contato").val(element.parents('tr').find("td:nth-child(2) input").val());
            $("#ds_email").val(element.parents('tr').find("td:nth-child(3) input").val());
            $("#ds_cel").val(element.parents('tr').find("td:nth-child(4) input").val());
            $("#ic_whatsapp").val(element.parents('tr').find("td:nth-child(5) input").val());
            $("#ds_tel_contato").val(element.parents('tr').find("td:nth-child(6) input").val());
            $("#cargos_pk").val(element.parents('tr').find("td:nth-child(7) input").val());
            $("#janela_contatos").modal("show");

            tblContatos.row($(this).parents('tr')).remove().draw();
        } );

        $('#tblContatos tbody').on('click', '.function_delete', function () {
            tblContatos.row($(this).parents('tr')).remove().draw();
        } );
    }
    else{
        var objParametros = {
            "leads_pk": $("#leads_pk").val()
        };

        var v_url = routes_api("lead", "listarContatoLead", objParametros);
        tblContatos = $("#tblContatos").DataTable({
            searching: true,
            paging: true,
            scrollX: true,
            pageLength: 10,
            aLengthMenu: [10, 25, 50, 100],
            iDisplayLength: 10,
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
                        return full['pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_contato'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_email'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_cel'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_whatsapp'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ic_whatsapp'];
                    },
                    'orderable': true,
                    'visible': false,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_tel'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_cargo'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['cargo_pk'];
                    },
                    'orderable': true,
                    'visible': false,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        var buttonEditar = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="Painel"></i></span></a> &nbsp;&nbsp;';
                        var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> &nbsp;&nbsp;';


                        return buttonEditar + buttonDelete;
                    },
                    'orderable': false,
                    'searchable': false,
                    width: '80px'
                }
            ]

        });
        $('#tblContatos tbody').on('click', '.function_edit', function () {
            var data;

            rLinhaSelecionada = null;

            if(tblContatos.row( $(this).parents('li')).data()){
                data = tblContatos.row( $(this).parents('li')).data();
                rLinhaSelecionada = $(this).parents('li');
            }
            else if(tblContatos.row( $(this).parents('tr')).data()){
                data = tblContatos.row( $(this).parents('tr')).data();
                rLinhaSelecionada = $(this).parents('tr');
            }
            fcEditarContato(data);

        } );

        $('#tblContatos tbody').on('click', '.function_delete', function () {
            var data;

            if(tblContatos.row( $(this).parents('li') ).data()){
                data = tblContatos.row( $(this).parents('li') ).data();
            }
            else if(tblContatos.row( $(this).parents('tr') ).data()){
                data = tblContatos.row( $(this).parents('tr') ).data();
            }

            if(data['pk'] != ""){
                fcExcluirContato(data['pk']);
            }
            tblContatos.row($(this).parents('tr')).remove().draw();
        } );
    }

    return false;
}


function fcEditarContato(objRegistro){
    fcLimparFormContato();
    $("#janela_contatos").modal('show');
    $("#contatos_pk").val("");
    $("#acao").val("upd");

    //Carrega as informações da linha selecionada.
    $("#contatos_pk").val(objRegistro['pk']);
    $("#ds_contato").val(objRegistro['ds_contato']);
    $("#ds_email").val(objRegistro['ds_email']);
    $("#ds_cel").val(objRegistro['ds_cel']);
    $("#ic_whatsapp").val(objRegistro['ic_whatsapp']);
    $("#ds_tel_contato").val(objRegistro['ds_tel']);
    $("#cargos_pk").val(objRegistro['cargos_pk']);

}

function fcExcluirContato(v_pk){
    if(v_pk != ""){
        var objParametros = {
            "pk": v_pk
        };

        var arrExcluir = carregarController("contato", "excluir", objParametros);

        if (arrExcluir.status == true){

            //Exibe a mensagem
            utilsJS.toastNotify(true, arrExcluir.message);
        }
        else{
            utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
        }
    }
}

function fcBotoesGridContatos(){
    return "<a class='function_edit'><span><img width=16 height=16 src='../img/copiar.png'></span></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='function_delete'><span><img width=16 height=16 src='../img/excluir.png'></span></a>";
}

function fcEnviarContato(){
    if($("#leads_pk").val() == ""){
        if($("#acao").val() == "ins"){
            fcIncluirContatoSemPk();

        }
        else if($("#acao").val() == "upd"){
            fcEditarContatoSemPk();
        }
    }
    else{
        fcSalvarContato();
    }
    $("#janela_contatos").modal("hide");
}
function fecharModalContato(){
    $("#janela_contatos").modal("hide");
}
function fcRecarregarGridContatos(){
    tblContatos.clear().destroy();
    fcCarregarGridContato();
}

function fcSalvarContato(){


    //atualiza o registro no DB, pois já existe uma PK para contatos no banco.
    var objParametros = {
        "pk": $("#contatos_pk").val(),
        "leads_pk": $("#leads_pk").val(),
        "ds_contato": $("#ds_contato").val(),
        "ds_email": $("#ds_email").val(),
        "ds_cel": $("#ds_cel").val(),
        "ds_tel": $("#ds_tel_contato").val(),
        "ic_whatsapp": $("#ic_whatsapp").val(),
        "cargos_pk": $("#cargos_pk").val()
    };
    var arrEnviar = carregarController("contato", "salvar", objParametros);

    if (arrEnviar.status == true){
        fcRecarregarGridContatos();
    }else{
        utilsJS.toastNotify(false, arrEnviar.message);
    }

}

function fcIncluirContatoSemPk(){

    var counter = 1;
    tblContatos.row.add( [
        counter,
        "<td><input type='hidden' id='ds_contato[]' value ='"+$("#ds_contato").val()+"'>"+ $("#ds_contato").val()+"</td>",
        "<td><input type='hidden' id='ds_email[]' value ='"+$("#ds_email").val()+"'>"+ $("#ds_email").val()+"</td>",
        "<td><input type='hidden' id='ds_cel[]' value ='"+$("#ds_cel").val()+"'>"+ $("#ds_cel").val()+"</td>",
        "<td><input type='hidden' id='ic_whatsApp[]' value ='"+$("#ic_whatsapp").val()+"'>"+ $("#ic_whatsapp option:selected").text()+"</td>",
        "<td><input type='hidden' id='ds_tel_contato[]' value ='"+$("#ds_tel_contato").val()+"'>"+ $("#ds_tel_contato").val()+"</td>",
        "<td><input type='hidden' id='cargos_pk[]' value ='"+$("#cargos_pk").val()+"'>"+ $("#cargos_pk option:selected").text()+"</td>",
        "<td><a class='function_edit'><span><i class='fa fa-pencil-alt' style='font-size=18px;color:blue' title='Editar'></i></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a class='function_delete'><span><i class='bi bi-x-circle' style='font-size=18px;color:blue' title='Excluir'></i></span></a></td>"
    ] ).draw().node();
    counter++;
    return false;
}

function fcEditarContatoSemPk(){

    fcIncluirContatoSemPk();
    tblContatos.row(rLinhaSelecionada).remove().draw();
    return false;
}


function fcCarregarCargo(){

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("cargo", "listarTodos", objParametros);
    carregarComboAjax($("#cargos_pk"), arrCarregar, " ", "pk", "ds_cargo");

}

function fcCarregarSupervisor(){

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("usuario", "listarSupervisor", objParametros);
    carregarComboAjax($("#supervisores_pk"), arrCarregar, " ", "pk", "ds_usuario");
    carregarComboAjax($("#supervisor1_pk"), arrCarregar, " ", "pk", "ds_usuario");
    carregarComboAjax($("#supervisor2_pk"), arrCarregar, " ", "pk", "ds_usuario");

}
function fcCarregarResponsavel(){
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("usuario", "listarTodos", objParametros);
    carregarComboAjax($("#responsavel_pk"), arrCarregar, " ", "pk", "ds_usuario");
}

function fcFormatarDadosContato(){

    try{
        var contatosPk = "";
        var dsContato = "";
        var dsEmail =  "";
        var dsCel = "";
        var icWhatsapp = "";
        var dsTelContato = "";
        var cboCargosPk = "";

        var arrKeys = [];
        var arrDados = [];
        arrKeys[0] = "contatos_pk";
        arrKeys[1] = "ds_contato";
        arrKeys[2] = "ds_email";
        arrKeys[3] = "ds_cel";
        arrKeys[4] = "ic_whatsapp";
        arrKeys[5] = "ds_tel_contato";
        arrKeys[6] = "cargos_pk";

        var i = 0;
        $("#tblContatos").find('tbody tr').each(function () {
            //if ($(this).find('td:nth-child(1) input').val() == "") {
            contatosPk = "";
            dsContato = $(this).find('td:nth-child(2) input').val();
            dsEmail = $(this).find('td:nth-child(3) input').val();
            dsCel = $(this).find('td:nth-child(4) input').val();
            icWhatsapp = $(this).find('td:nth-child(5) input').val();
            dsTelContato = $(this).find('td:nth-child(6) input').val();
            cboCargosPk = $(this).find('td:nth-child(7) input').val();

            arrDados[i] = [contatosPk, dsContato, dsEmail, dsCel, icWhatsapp, dsTelContato, cboCargosPk];
            i++;
            //}
        });
        return arrayToJson(arrKeys, arrDados);
    }
    catch (err) {

        utilsJS.toastNotify(false, err);
    }
}

function fcLimparFormContato(){
    $("#acao").val("");
    $("#contatos_pk").val("");
    $("#ds_contato").val("");
    $("#ds_email").val("");
    $("#ds_cel").val("");
    $("#ic_whatsapp").val("");
    $("#ds_tel_contato").val("");
    $("#cargos_pk").val("");
}

//abre o formulario para a inclusao de um novo contato.
function fcAbrirFormNovoContato(){

    //limpa os dados de qualquer registro existe
    fcLimparFormContato();


    $("#janela_contatos").modal('show');

    $("#acao").val("ins");
    $("#contatos_pk").val("");
}

function fcValidarFormContato(){

    $("#form_contato").validate({
        rules :{
            ds_contato:{
                required:true,
                minlength:3
            },
            ic_whatsapp:{
                required:true
            },
            ds_cel:{
                required:true,
                minlength:13
            },
            ds_tel_contato:{
                minlength:13
            },
            ds_email:{
                email: true
            }

        },
        messages:{
            ds_contato:{
                required:"Por favor, informe Contato",
                minlength:"Contato deve ter pelo menos 3 caracteres"
            },
            ds_cel:{
                required:"Por favor, informe Celular",
                minlength:"Por favor, informe Celular válido"
            },
            ds_tel_contato:{
                minlength:"Por favor, informe Telefone válido"
            },
            ic_whatsapp:{
                required:"Por favor, informe WhatsApp"
            },
            ds_email:{
                email:"Por favor, informe E-mail válido"
            }

        },
        submitHandler: function(form){
            fcEnviarContato(); //Se a validação deu certo, faz o envio do formulario.

            return false;
        }
    });

}
function fcVerificarCNPJ(){
    var ds_cpf_cnpj = $("#ds_cpf_cnpj").val();
    if(ds_cpf_cnpj.length == 14 || ds_cpf_cnpj.length == 18){
        var objParametros = {
            "ds_cpf_cnpj": $("#ds_cpf_cnpj").val()
        };

        var arrCarregar = carregarController("lead", "verificarCNPJ", objParametros);

        if (arrCarregar.status == true){

            if(arrCarregar.data.length > 0){

                sweetMensagem('warning', "Já existe um Lead com esse CNPJ");
                $("#ds_lead").val("");
                $("#ds_cpf_cnpj").val("");
                $("#ds_cidade").val("");
                $("#ds_endereco").val("");
                $("#ds_bairro").val("");
                $("#ds_uf").val("");

            }
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }


}


function fcCarregarLeadPai(){
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("lead", "listarTodosClientes", objParametros);

    carregarComboAjax($("#leads_pai_pk"), arrCarregar, " ", "pk", "ds_lead");
}
$(document).ready(function(){
    var arrCarregar = permissao("lead", "ins");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false, 'Você não tem permissão para acessar essa pagina!');
        setTimeout(function() {
            sendPost('menu','principal',{})
        }, 2000);
        return false;
    }
    $("#dados-tab").click(function(){
        $("#dados-tab").removeClass();
        $("#dados-tab").addClass('nav-link active');
        $("#contatos-tab").removeClass();
        $("#contatos-tab").addClass('nav-link');
        $("#dados").removeClass();
        $("#dados").addClass('tab-pane fade show active');
        $("#contatos").removeClass();
        $("#contatos").addClass('tab-pane fade');
    });
    $("#contatos-tab").click(function(){

        $("#dados-tab").removeClass();
        $("#dados-tab").addClass('nav-link');
        $("#contatos-tab").addClass('nav-link active');

        $("#dados").removeClass();
        $("#dados").addClass('tab-pane fade');
        $("#contatos").removeClass();
        $("#contatos").addClass('tab-pane fade  show active');

        fcRecarregarGridContatos()
    });


    colaborador_pk ="";
    $("#exibir_material").hide();

    if($("#leads_pk").val()!=""){
        $("#exibir_material").show();
    }

    //Atribui os eventos - Leads
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdEnviarTudo', fcValidarForm);
    $(document).on('click', '#cmdCancelar2', fcCancelar);
    $(document).on('click', '#cmdEnviarTudo2', fcValidarForm);
    $(document).on('click', '#btn_modal', fcAbrirFormNovoContato);
    //atribui mascara aos campos - Lead

    $('#dt_abertura').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_abertura").keypress(function(){
        mascara(this,mdata);
    });

    $("#ds_cep").keypress(function(){
        mascara(this,cep);
    });
    $("#n_qtde_torres").keypress(function(){
        mascara(this,soNumeros);
    });
    $("#ds_cpf_cnpj").keypress(function(){
        chama_mascara(this);
    });

    $("#ds_tel_fixo").keypress(function(){
        mascara(this,mascaraTelefone);
    });
    $("#ds_tel_fixo1").keypress(function(){
        mascara(this,mascaraTelefone);
    });
    $("#ds_ie").keypress(function(){
        mascara(this,soNumeros);
    });
    //Atribui os eventos dos controles do formulario de contatos.

    fcValidarFormContato();

    //Carrega os dados cadastrais do lead
    fcCarregarSupervisor();

    fcCarregarResponsavel();

    $("#lead_pai").hide();
    fcCarregarLeadPai();
    $(".chzn-select").chosen({allow_single_deselect: true});

    //fcCarregar();




    if($("#ic_cliente").val()==""){
        $("#ic_cliente").val(2);
    }

    //---------------------------------------------
    //atribui mascara aos campos - Contato

    $("#ds_tel_contato").on('keypress', function () {
        mascara(this, mascaraTelefone);
    });
    $("#ds_cel").on('keypress', function () {
        mascara(this, mascaraTelefone);
    });
    $("#ds_cep").change(function(){
        fcCarregarCep($("#ds_cep").val());
    });
    $("#ds_cpf_cnpj").change(function(){
        fcVerificarCNPJ();
    });

    $("#ic_tipo_lead").change(function(){
        if($("#ic_tipo_lead").val()==1){
            $("#lead_pai").hide();
            $("#lead_pai_pk").val("");
        }
        else if($("#ic_tipo_lead").val()==2){
            $(".chzn-select").chosen('destroy');
            $("#lead_pai").show();
            $(".chzn-select").chosen({allow_single_deselect: true});
        }
    });
    $(".chzn-select").chosen({width: "200%"});

    //Carrega os dados do campo de Cargo na tela modal dos contatos
    fcCarregarCargo();


    //Formata a grid de contatoss
    fcCarregarGridContato();

});
