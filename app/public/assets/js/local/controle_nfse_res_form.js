var tblResultado;
function fcPesquisar(){
	
    tblResultado.clear().destroy();
    fcCarregarGrid();
    
}

function fcIncluir(){
    var objParametros = {};
    sendPost('controle_nfse','cadForm' ,objParametros);
}

function fcVoltar(){
    var objParametros = {};
    sendPost('menu','financeiro' ,objParametros);
}

function fcEditar(v_pk){
    var objParametros = {
        "pk": v_pk
    };
    sendPost('controle_nfse','cadForm' ,objParametros);
}

function fcCarregarGrid(){
    var objParametros = {
        "ds_numero_nfse": $("#ds_numero_nfse").val(),
        "ds_prestador": $("#ds_prestador option:selected").text(),
        "ds_tomador": $("#ds_tomador option:selected").text(),
        "dt_emissao_ini": $("#dt_emissao_ini").val(),
        "dt_emissao_fim": $("#dt_emissao_fim").val(),
        "dt_cancelamento_ini": $("#dt_cancelamento_ini").val(),
        "dt_cancelamento_fim": $("#dt_cancelamento_fim").val(),
        "ic_status": $("#ic_status").val(),
    };     
    
    var v_url = routes_api("controle_nfse", "listarGrid", objParametros);

    tblResultado = $('#tblResultado').DataTable({
        searching: true,
        paging: true,
        scrollX: true,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
        iDisplayLength: 10,
        ajax: v_url,
        responsive: true,
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },
        order: [
            [0, "desc"]
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
                    return full['numero'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_prestador'].substring(0, 18)+"...";
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_tomador'].substring(0, 18)+"...";
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['dataEmissao'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return "R$ "+float2moeda(full['valor']);
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    if(full['minutos_passados']<3){
                        return "Processando..";
                    }
                    else{
                        return full['ds_status'].substring(0, 10)+"...";;
                    }
                    
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                   
                    return full['dt_cancelamento'];
                    
                    
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    if(full['ic_verificado']==null){
                        return "Aguardando Processamento.";
                    }
                    else{
                        var buttonPainel = '<a class="function_download" target="_blank"><i class="fa fa-download" style="font-size:18px; color:blue" title="DOWNLOAD DOCUMENTO"></i> ';
                        var buttonXML = '<a class="function_xml"><span><i class="bi bi-file-earmark-text" style="font-size=18px;color:blue" title="Exibir XML"></i></span></a> ';
                        var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="Cancelar NFSE"></i></span></a> ';
                    
                        /*if(full['ds_status']=="Cancelada"){
                            return buttonPainel + buttonXML;
                        }
                        else if(full['ds_status']=="Rejeitada"){
                            return buttonPainel + buttonXML;
                        }
                        else{*/
                            return buttonPainel + buttonXML + buttonDelete;
                        //}
                    }
                    
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]

    });
    
    
    //Atribui os eventos na coluna ação.
    $('#tblResultado tbody').on('click', '.function_download', function () {
        var data;
        if(tblResultado.row( $(this).parents('li')).data()){
            data = tblResultado.row( $(this).parents('li')).data();
        }
        else if(tblResultado.row( $(this).parents('tr')).data()){
            data = tblResultado.row( $(this).parents('tr')).data();
        }
        fcDownloadNfse(data['id_notas'],data['minutos_passados']);
        
    } );   
    
    //Atribui os eventos na coluna ação.
    $('#tblResultado tbody').on('click', '.function_xml', function () {
        var data;
        if(tblResultado.row( $(this).parents('li')).data()){
            data = tblResultado.row( $(this).parents('li')).data();
        }
        else if(tblResultado.row( $(this).parents('tr')).data()){
            data = tblResultado.row( $(this).parents('tr')).data();
        }
        fcExibirXML(data['id_notas'],data['minutos_passados']);
        
    } ); 

    $('#tblResultado tbody').on('click', '.function_delete', function () {
        var data;
        if(tblResultado.row( $(this).parents('li') ).data()){
            data = tblResultado.row( $(this).parents('li') ).data();
        }
        else if(tblResultado.row( $(this).parents('tr') ).data()){
            data = tblResultado.row( $(this).parents('tr') ).data();
        }
        fcExcluir(data['id_notas'], data['pk']);
    } );     
    
}

function fcDownloadNfse(id_notas,minutos_passados){
    /*if(minutos_passados<=6){
        
        utilsJS.toastNotify(false, "Sua nota ainda está sendo processada");
        return false;
    }*/
    var job = 'download_nfse/'+id_notas

    var url = routes_api('controle_nfse', job, {});
    window.open(url, '_blank');
}

function fcExibirXML(id_notas,minutos_passados){
    /*if(minutos_passados<=6){
        
        utilsJS.toastNotify(false, "Sua nota ainda está sendo processada");
        return false;
    }*/
    var job = 'exbir_xml/'+id_notas

    var url = routes_api('controle_nfse', job, {});
    window.open(url, '_blank');
}

function fcExcluir(id_notas, pk){
    utilsJS.jqueryConfirm('Cancelar?', 'Deseja cancelar a Nota ?', function () {
        //if(id_notas != ""){
   
            var objParametros = {
                "id_notas": id_notas,
                "pk":pk
            };              
            
            var arrExcluir = carregarController("controle_nfse", "cancelarNota", objParametros);   
    
           
            tblResultado.ajax.reload();

        /*}
        else{
            utilsJS.toastNotify(false, 'Não localizamos o ID NOTA.');
        }*/
    });
}
function listarTomador(){
    
    var objParametros = {
        'pk': ""
    };        
    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    carregarComboAjax($("#ds_tomador"), arrCarregar, " ", "pk", "ds_razao_social");
}

function listarPrestador(){
    
    var objParametros = {
        'pk': ""
    };        
    var arrCarregar = carregarController("certificados_empresas", "contaConfigListarEmpresas", objParametros);
    carregarComboAjax($("#ds_prestador"), arrCarregar, " ", "pk", "razaoSocial");
}

function fcRecarregar(){
    tblResultado.clear().destroy();
    fcCarregarGrid();
}

var formadata = null;
$(document).ready(function(){
    formdata = new FormData();
    listarTomador();
    listarPrestador();
    fcCarregarGrid();

    $(".chzn-select").chosen({ allow_single_deselect: true });


    $('#dt_emissao_ini').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $('#dt_emissao_fim').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

    $('#dt_cancelamento_ini').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $('#dt_cancelamento_fim').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

    setInterval(function() {
        tblResultado.clear().destroy();
        fcCarregarGrid();
    }, 300000); // 300000 milissegundos = 5 minutos

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdIncluir', fcIncluir);
    $(document).on('click', '#cmdVoltar', fcVoltar);
    $(document).on('click', '#cmdRecarregar', fcRecarregar);

});


