var tblResultado;

function fcEditar(v_pk,v_colaborador_pk){
    var v_leads_pk = $("#leads_pk").val();

    var objParametros = {
        'leads_pk': v_leads_pk,
        'colaborador_pk': v_colaborador_pk,
        'pk': v_pk
    };
    var url = 'registrosCad?nocache=' + new Date().getTime();
    sendPost('ponto_folha',url,objParametros)
}

function fcExcluir(pk,colaborador_pk,dt_periodo_ini,dt_periodo_fim){
    

    utilsJS.jqueryConfirm('Excluir ', 'Deseja realmente excluir o registro? ', function () {
       
        if(pk != ""){

            var objParametros = {
                "pk": pk,
                "dt_periodo_ini": dt_periodo_ini,
                "dt_periodo_fim": dt_periodo_fim,
                "colaborador_pk":colaborador_pk
            };              
            
            var arrExcluir = carregarController("ponto_folha", "excluirFolhaColaborador", objParametros);   

            if (arrExcluir.result == 'success'){

                //Exibe a mensagem
                alert(arrExcluir.message);

                // Reload datable
                tblResultado.ajax.reload();

            }
            else{
                alert('Falhou a requisição de exclusão.');
            }
        }
        else{
            alert("Código não encontrado");
        }
    });
}
function fcMarcarTodos() {
    var data = tblResultado.rows().data();

    if ($(".checks").prop( "checked")){
        $("#descritivo_marcar").text("Marcar Todos");
        for (i = 0; i < data.length; i++) {//calcula o valor total
            $(".checks").prop("checked", false);
        }
     } else{ 
        $("#descritivo_marcar").text("Desmarcar Todos");
        for (i = 0; i < data.length; i++) {//calcula o valor total
            $(".checks").prop("checked", true);
        }
    }


    
}
function fcCarregarGrid(){
    let pk = $('#pk').val();

    var objParametros = {
        "ponto_folha_pk": pk
    };     
    
    var v_url = routes_api("ponto_folha", "listarPontoFolhaPK", objParametros);
    tblResultado = $('#tblResultado').DataTable({
        searching: true,
        paging: false,
        scrollX: true,
        processing: false,
        serverSide: false,
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
                    return "<input type=checkbox class='checks' name='checks[]' value='1'>";
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['colaborador_pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_cadastro'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dt_ult_atualizacao'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ic_status'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_colaborador'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_edit"><span><i class="bi bi-pencil-square" style="font-size=18px;color:blue" title="Editar"></i></span></a> ';
                    var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="Excluir"></i></span></a> ';
                    var buttonPrint = '<a class="function_print"><i class="bi bi-printer" style="font-size:18px;color:blue" title="Abrir Formulário para impressao"></i></a> ';
                
                    return buttonPainel + buttonDelete + buttonPrint;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]

    });
    
    
    //Atribui os eventos na coluna ação.
    $('#tblResultado tbody').on('click', '.function_edit', function () {
        var data;
        if(tblResultado.row( $(this).parents('li')).data()){
            data = tblResultado.row( $(this).parents('li')).data();
        }
        else if(tblResultado.row( $(this).parents('tr')).data()){
            data = tblResultado.row( $(this).parents('tr')).data();
        }
        fcEditar(data['ponto_folha_pk'],data['colaborador_pk']);
        
    } );   

    $('#tblResultado tbody').on('click', '.function_print', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcPrintFolha(data['colaborador_pk'],data['ic_status_pk']);
    } );  
    
    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['pk'],data['colaborador_pk'],data['dt_periodo_ini'],data['dt_periodo_fim']);
    } );        
    
}

function fcCarregar(){
    let pk = $('#pk').val();
    if(pk > 0){

        var objParametros = {
            "pk": pk
        };        
        
        var arrCarregar = carregarController("ponto_folha", "listarFolhasRegistros", objParametros);

        if (arrCarregar.status == true){
            
            $("#ds_empresa").html(arrCarregar.data[0]['ds_conta']);
            $("#ds_lead").html(arrCarregar.data[0]['ds_lead']);
            $("#leads_pk").val(arrCarregar.data[0]['leads_pk']);
            $("#dt_periodo_ini").html(arrCarregar.data[0]['dt_periodo_ini']);
            $("#dt_periodo_fim").html(arrCarregar.data[0]['dt_periodo_fim']);
            $("#obs").val(arrCarregar.data[0]['obs']); 
        }
        else{
            alert('Falhar ao carregar o registro');
        }
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost('ponto_folha','receptivoPontoFolha',objParametros)
}

function armazenarColaboradores() {

     var arrColaboradores = [];
        tblResultado.rows().every(function() {
            var data = this.data();
            //&& data['ic_status'] == "Finalizada"
            //if (data && data['colaborador_pk'] && data['ic_status'] == "Finalizada") {
                arrColaboradores.push(data['colaborador_pk']);
            //}
        });
        return arrColaboradores;
   

 
}


function fcPrintFolha(v_colaborador_pk,ic_status){
    var v_pk = $("#pk").val();
    var v_leads_pk = $("#leads_pk").val();
    var url = 'receptivoPrint?nocache=' + new Date().getTime();
    
    if(v_colaborador_pk==null){
        utilsJS.jqueryConfirm('Imprimir Todos', 'Apenas as folhas com status finalizado serão impressas. Deseja continuar? ', function () {
       
            v_colaborador_pk = armazenarColaboradores();
            if(v_colaborador_pk.length>0){
                var objParametros = {
                    "leads_pk":v_leads_pk,
                    "pk":v_pk,
                    "colaborador_pk":v_colaborador_pk
                };
                sendPost('ponto_folha',url,objParametros)
            }
            else{
                sweetMensagem('warning',"Não existe nenhuma folha finalizada.");
            }

            
        });
    }
    else{
        if(ic_status!=1){
            sweetMensagem('warning',"Você só pode imprimir se a folha estiver Finalizada.");
        }
        else{
            var objParametros = {
                "leads_pk":v_leads_pk,
                "pk":v_pk,
                "colaborador_pk":v_colaborador_pk
            };
            sendPost('ponto_folha',url,objParametros)
        }
        
    }
        
    
    
    
}

function fcGerarPlanilhaExcel(){
    var v_leads_pk = $("#leads_pk").val();
    sendPost('ponto_folha_planilha_form.php', {token: token, pk: pk, leads_pk: v_leads_pk});
}

$(document).ready(function(){
    fcCarregar();

    //faz a carga inicial do grid.
    fcCarregarGrid();
    
    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdVoltar', fcCancelar);

    $("#cmdPrintAll").click(function(){
        fcPrintFolha(null,null);
    });
    
    $('#tblResultado tbody').on('click', "input[name='checks[]']", function () { 
        $(this).parents("tr").toggleClass('selected');
    });  
    
    $(document).on('click', '#cmdEnviar', fcValidarForm);
    $(document).on('click', '#cmdRegerarFolha', fcAbrirRegerar);
    $(document).on('click', '#cmdFechar', fcFecharModalRegerar);
    $(document).on('click', '#cmdFecharRegerar2', fcFecharModalRegerar);

    setTimeout(function() {

        var minDate =($("#dt_periodo_ini").html());
        var maxDate =($("#dt_periodo_fim").html());

        var partsMin = minDate.split('/');
        var dayMin = parseInt(partsMin[0], 10);
        var monthMin = parseInt(partsMin[1], 10) - 1; // Meses em JavaScript são baseados em zero
        var yearMin = parseInt(partsMin[2], 10);

        var partsMax = maxDate.split('/');
        var dayMax = parseInt(partsMax[0], 10);
        var monthMax = parseInt(partsMax[1], 10)-1; // Meses em JavaScript são baseados em zero
        var yearMax = parseInt(partsMax[2], 10);

        var startDate = new Date(yearMin, monthMin, dayMin); 
        var endDate = new Date(yearMax, monthMax, dayMax);
    
        $('#dt_ini_periodo').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            language: "pt-BR",
            autoclose: true,
        }).on('changeDate', function(e) {
            var selectedDate = e.date;
            if (selectedDate >= startDate && selectedDate <= endDate) {
            } else {
                sweetMensagem('warning',"A data inicio está fora do intervalo permitido.");
                $("#dt_ini_periodo").val("")
            }
        });

        $("#dt_ini_periodo").keypress(function () {
            mascara(this, mdata);
        });  
    
        $('#dt_fim_periodo').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            language: "pt-BR",
            autoclose: true,
        }).on('changeDate', function(e) {
            var selectedDate = e.date;
            if (selectedDate >= startDate && selectedDate <= endDate) {
            } else {
                sweetMensagem('warning',"A data fim está fora do intervalo permitido.");
            
                $("#dt_fim_periodo").val("")
            }
        });
        $("#dt_fim_periodo").keypress(function () {
            mascara(this, mdata);
        }); 
    }, 2000);
    $("#descritivo_marcar").text("Marcar Todos");
    $(document).on('click', '#cmdMarcarTodos', fcMarcarTodos);

});


