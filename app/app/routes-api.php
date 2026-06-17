<?php

use App\Middleware\Authentication;

$app->group('/api', function () use ($app) {

	$container = $app->getContainer();


    //AREA DO COLABORADOR
    $app->group('/area_colaborador', function () use ($app) {
        $app->post('/buscarColaborador', 'App\Controller\AreaColaboradorController:buscarColaborador')->setName('buscarColaborador');
        $app->post('/salvarPrimeiroRegistro', 'App\Controller\AreaColaboradorController:salvarPrimeiroRegistro')->setName('salvarPrimeiroRegistro');
        $app->post('/pegarInfoColaborador', 'App\Controller\AreaColaboradorController:pegarInfoColaborador')->setName('pegarInfoColaborador');
        $app->post('/salvarPonto', 'App\Controller\AreaColaboradorController:salvarPonto')->setName('salvarPonto');
    });
	//SECTION AUTH
	$app->group('/auth', function () use ($app) {
        
        $app->post('/verificarTrocaSenha', 'App\Controller\LoginController:verificarTrocaSenha')->setName('api-login');
		$app->post('/login', 'App\Controller\LoginController:apiLogin')->setName('api-login');
        $app->post('/logout', 'App\Controller\LoginController:apiLogoff')->setName('api-logoff');
        $app->post('/updateSenha', 'App\Controller\LoginController:updateSenha')->setName('api-updateSenha');
	});
    //AFD
    $app->group('/afd', function () use ($app) {
        $app->get('/downloadAfd/{pk}', 'App\Controller\AfdController:downloadAfd')->setName('downloadAfd');
        $app->get('/listarGrid', 'App\Controller\AfdController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\AfdController:salvar')->setName('salvar');
    }); 
    //AGENDA
    $app->group('/agenda', function () use ($app) {
        $app->get('/listarDataTable', 'App\Controller\AgendaController:listarDataTable')->setName('salvar');
        $app->post('/salvar', 'App\Controller\AgendaController:salvar')->setName('salvar');
        $app->post('/listarPk', 'App\Controller\AgendaController:listarPk')->setName('listarPk');
        $app->post('/excluir', 'App\Controller\AgendaController:excluir')->setName('excluir');
    });
    //AGENDA CALENDARIO
	$app->group('/agenda_calendario', function () use ($app) {
		$app->get('/listarEventos', 'App\Controller\AgendaCalendarioController:listarEventos')->setName('salvar');
		$app->post('/salvar', 'App\Controller\AgendaController:salvar')->setName('salvar');
		$app->post('/listarPk', 'App\Controller\AgendaController:listarPk')->setName('listarPk');
		$app->post('/excluir', 'App\Controller\AgendaController:excluir')->setName('excluir');
	});
    //AGENDA COLABORADOR PADRAO
	$app->group('/agenda_colaborador_padrao', function () use ($app) {
        $app->get('/calendarioDados', 'App\Controller\AgendaColaboradorPadraoController:calendarioDados')->setName('calendarioDados');
        $app->get('/calendarioDadosEscala', 'App\Controller\AgendaColaboradorPadraoController:calendarioDadosEscala')->setName('calendarioDadosEscala');
        $app->post('/listarTurno', 'App\Controller\AgendaColaboradorPadraoController:listarTurno')->setName('listarTurno');
		$app->post('/listarEscalasPostosColaborador', 'App\Controller\AgendaColaboradorPadraoController:listarEscalasPostosColaborador')->setName('listarEscalasPostosColaborador');
		$app->get('/listarEscalasResPadrao', 'App\Controller\AgendaColaboradorPadraoController:listarEscalasResPadrao')->setName('listarEscalasResPadrao');
		$app->get('/lisarEscalasResPadraoColaborador', 'App\Controller\AgendaColaboradorPadraoController:lisarEscalasResPadraoColaborador')->setName('lisarEscalasResPadraoColaborador');
		$app->post('/listarQRCode', 'App\Controller\AgendaColaboradorPadraoController:listarQRCode')->setName('listarQRCode');
		$app->post('/consultarEscalaContratosItens', 'App\Controller\AgendaColaboradorPadraoController:consultarEscalaContratosItens')->setName('listarQRCode');
		$app->post('/lisarEscalaEditar', 'App\Controller\AgendaColaboradorPadraoController:lisarEscalaEditar')->setName('lisarEscalaEditar');
		$app->post('/pegarPostoDeTrabalhoPorLeadEColaborador', 'App\Controller\AgendaColaboradorPadraoController:pegarPostoDeTrabalhoPorLeadEColaborador')->setName('lisarEscalaEditar');
		$app->post('/verificaOutraEscalaColaborador', 'App\Controller\AgendaColaboradorPadraoController:verificaOutraEscalaColaborador')->setName('verificaOutraEscalaColaborador');
		$app->post('/salvar', 'App\Controller\AgendaColaboradorPadraoController:salvar')->setName('salvar');
		$app->post('/updateDataEscalaColaborador', 'App\Controller\AgendaColaboradorPadraoController:updateDataEscalaColaborador')->setName('updateDataEscalaColaborador');
		$app->post('/excluir', 'App\Controller\AgendaColaboradorPadraoController:excluir')->setName('excluir');
        $app->post('/processa_escala', 'App\Controller\AgendaColaboradorPadraoController:processa_escala')->setName('processa_escala');
		$app->post('/escalaDadosColaborador', 'App\Controller\AgendaColaboradorPadraoController:escalaDadosColaborador')->setName('escalaDadosColaborador');
        $app->post('/cancelarEscalasDemissao', 'App\Controller\AgendaColaboradorPadraoController:cancelarEscalasDemissao')->setName('cancelarEscalasDemissao');
        $app->get('/relAcompanhamentoPontoSintetico', 'App\Controller\AgendaColaboradorPadraoController:relAcompanhamentoPontoSintetico')->setName('relAcompanhamentoPontoSintetico');
		$app->get('/listaPostoXColaboradores', 'App\Controller\AgendaColaboradorPadraoController:listaPostoXColaboradores')->setName('listaPostoXColaboradores');
		$app->post('/pegarPostoByColaboradorPorMesAno', 'App\Controller\AgendaColaboradorPadraoController:pegarPostoByColaboradorPorMesAno')->setName('pegarPostoByColaboradorPorMesAno');
    
    });
    //AGENDA COLABORADOR APONTAMENTO
	$app->group('/agenda_colaborador_apontamento', function () use ($app) {
        $app->post('/listarApontamentoColaboradorDia', 'App\Controller\AgendaColaboradorApontamentoController:listarApontamentoColaboradorDia')->setName('listarApontamentoColaboradorDia');
        $app->post('/salvar', 'App\Controller\AgendaColaboradorApontamentoController:salvar')->setName('listarApontamentoColaboradorDia');
        $app->post('/desabilitarApontamento', 'App\Controller\AgendaColaboradorApontamentoController:desabilitarApontamento')->setName('listarApontamentoColaboradorDia');
        $app->post('/salvarApontamentoReloginho', 'App\Controller\AgendaColaboradorApontamentoController:salvarApontamentoReloginho')->setName('listarApontamentoColaboradorDia');
        $app->post('/salvarValidadoReloginho', 'App\Controller\AgendaColaboradorApontamentoController:salvarValidadoReloginho')->setName('salvarValidadoReloginho');
        $app->post('/listarDisciplina', 'App\Controller\AgendaColaboradorApontamentoController:listarDisciplina')->setName('listarDisciplina');
        $app->get('/relApontamento', 'App\Controller\AgendaColaboradorApontamentoController:relApontamento')->setName('relApontamento');
		$app->get('/relControleFt', 'App\Controller\AgendaColaboradorApontamentoController:relControleFt')->setName('relControleFt');

	});
    //AGENDA PARTICIPANTES
	$app->group('/agendas_participantes', function () use ($app) {
		$app->get('/listarDataTable', 'App\Controller\AgendaParticipanteController:listarDataTable')->setName('carregarParicipantes');
		$app->post('/carregarParicipantes', 'App\Controller\AgendaParticipanteController:carregarParicipantes')->setName('carregarParicipantes');
		$app->post('/carregarParicipantePorParticipantePk', 'App\Controller\AgendaParticipanteController:carregarParicipantePorParticipantePk')->setName('carregarParicipantes');
		$app->post('/excluir', 'App\Controller\AgendaParticipanteController:excluir')->setName('excluir');
	});

    
    //ANALISE FINANCEIRA
	$app->group('/analise_financeira', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\AnaliseFinanceiraController:listarGrid')->setName('listarGrid');
        $app->post('/listarPk', 'App\Controller\AnaliseFinanceiraController:listarPk')->setName('listarPk');
        $app->post('/excluir', 'App\Controller\AnaliseFinanceiraController:excluir')->setName('excluir');

	});

    //ANALISE FINANCEIRA PROCESSOS
	$app->group('/analise_financeira_processo', function () use ($app) {
		$app->get('/historicoAnaliseFinanceira', 'App\Controller\AnaliseFinanceiraProcessoController:historicoAnaliseFinanceira')->setName('historicoAnaliseFinanceira');
        $app->post('/salvar', 'App\Controller\AnaliseFinanceiraProcessoController:salvar')->setName('salvar');

	});
    //AUDITORIA CATEGORIAS
	$app->group('/auditoria_categoria', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\AuditoriaCategoriaController:listarGrid')->setName('listarGrid');
        $app->post('/listarPk', 'App\Controller\AuditoriaCategoriaController:listarPk')->setName('listarPk');
        $app->post('/salvar', 'App\Controller\AuditoriaCategoriaController:salvar')->setName('salvar');
        $app->post('/listarTodos', 'App\Controller\AuditoriaCategoriaController:listarTodos')->setName('listarTodos');
	});

    //AUDITORIA CATEGORIAS TIPOS
	$app->group('/auditoria_categoria_tipos', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\AuditoriaCategoriaTiposController:listarGrid')->setName('listarGrid');
        $app->post('/listarPk', 'App\Controller\AuditoriaCategoriaTiposController:listarPk')->setName('listarPk');
        $app->post('/salvar', 'App\Controller\AuditoriaCategoriaTiposController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\AuditoriaCategoriaTiposController:excluir')->setName('excluir');
        $app->post('/salvarItens', 'App\Controller\AuditoriaCategoriaTiposController:salvarItens')->setName('salvarItens');
        $app->post('/salvarItensCampos', 'App\Controller\AuditoriaCategoriaTiposController:salvarItensCampos')->setName('salvarItensCampos');
        $app->post('/atualizarStatus', 'App\Controller\AuditoriaCategoriaTiposController:atualizarStatus')->setName('atualizarStatus');
        $app->post('/listarPorCategoriasTiposPk', 'App\Controller\AuditoriaCategoriaTiposController:listarPorCategoriasTiposPk')->setName('listarPorCategoriasTiposPk');
        $app->post('/listarPorAuditoriaCategoriasPk', 'App\Controller\AuditoriaCategoriaTiposController:listarPorAuditoriaCategoriasPk')->setName('listarPorCategoriasTiposPk');
	});

    //BANCO
    $app->group('/banco', function () use ($app) {
        $app->post('/listarTodos', 'App\Controller\BancoController:listarTodos')->setName('listarTodos');
    });  

    //BENEFICIO
    $app->group('/beneficio', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\BeneficioController:listarGrid')->setName('listarGrid');
        $app->post('/listarPk', 'App\Controller\BeneficioController:listarPk')->setName('listarPk');
        $app->post('/excluir', 'App\Controller\BeneficioController:excluir')->setName('excluir');
        $app->post('/salvar', 'App\Controller\BeneficioController:salvar')->setName('salvar');
    });   

	//CATEGORIA FINANCEIRA
    $app->group('/categoria_financeira', function () use ($app) {
        $app->post('/listarTodos', 'App\Controller\CategoriaFinanceiraController:listarTodos')->setName('listarTodos');
        $app->post('/listarPorPlano', 'App\Controller\CategoriaFinanceiraController:listarPorPlano')->setName('listarPorPlano');
     });
    //CATEGORIA PRODUTO
    $app->group('/categoria_produto', function () use ($app) {
        $app->post('/listarTodos', 'App\Controller\CategoriaProdutoController:listarTodos')->setName('listarTodos');
		$app->get('/listarGrid', 'App\Controller\CategoriaProdutoController:listarGrid')->setName('listarGrid');
        $app->post('/listarPk', 'App\Controller\CategoriaProdutoController:listarPk')->setName('listarPk');
        $app->post('/salvar', 'App\Controller\CategoriaProdutoController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\CategoriaProdutoController:excluir')->setName('excluir');
    
    });

    //CARGO
    $app->group('/cargo', function () use ($app) {
        $app->post('/listarTodos', 'App\Controller\CargoController:listarTodos')->setName('listarTodos');
    });

    //CERTIFICADOS EMPRESAS
    $app->group('/certificados_empresas', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\CertificadosEmpresasController:listarGrid')->setName('listarGrid');
        $app->post('/listarPk', 'App\Controller\CertificadosEmpresasController:listarPk')->setName('listarPk');
        $app->get('/contaLeadConsulta', 'App\Controller\CertificadosEmpresasController:contaLeadConsulta')->setName('contaLeadConsulta');
        $app->post('/contaConfigSalvar', 'App\Controller\CertificadosEmpresasController:contaConfigSalvar')->setName('contaConfigSalvar');
        $app->get('/contaConfigConsulta', 'App\Controller\CertificadosEmpresasController:contaConfigConsulta')->setName('contaConfigConsulta');
        $app->post('/contaConfigConsultaPk', 'App\Controller\CertificadosEmpresasController:contaConfigConsultaPk')->setName('contaConfigConsultaPk');
        $app->post('/excluirDocs', 'App\Controller\CertificadosEmpresasController:excluirDocs')->setName('excluirDocs');
        $app->post('/salvarNfeServico', 'App\Controller\CertificadosEmpresasController:salvarNfeServico')->setName('salvarNfeServico');
        $app->post('/listarNfeServico', 'App\Controller\CertificadosEmpresasController:listarNfeServico')->setName('listarNfeServico');
        $app->post('/excluirServico', 'App\Controller\CertificadosEmpresasController:excluirServico')->setName('excluirServico');
        $app->post('/contaConfigListarEmpresas', 'App\Controller\CertificadosEmpresasController:contaConfigListarEmpresas')->setName('contaConfigListarEmpresas');
        $app->post('/listarDadosServico', 'App\Controller\CertificadosEmpresasController:listarDadosServico')->setName('listarDadosServico');
        $app->post('/listarServicosPk', 'App\Controller\CertificadosEmpresasController:listarServicosPk')->setName('listarServicosPk');
    });

    //COLABORADOR
    $app->group('/colaborador', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\ColaboradorController:listarGrid')->setName('listarGrid');
		$app->get('/listarColaboradorPorLead', 'App\Controller\ColaboradorController:listarColaboradorPorLead')->setName('listarColaboradorPorLead');
		$app->get('/listarGridCliente', 'App\Controller\ColaboradorController:listarGridCliente')->setName('listarGridCliente');
        $app->post('/salvar', 'App\Controller\ColaboradorController:salvar')->setName('salvar');
        $app->post('/listarDsPin', 'App\Controller\ColaboradorController:listarDsPin')->setName('listarDsPin');
        $app->post('/listarTodos', 'App\Controller\ColaboradorController:listarTodos')->setName('listarTodos');
        $app->post('/listarTodosAtivo', 'App\Controller\ColaboradorController:listarTodosAtivo')->setName('listarTodosAtivo');
        $app->post('/listarColaboradoresQualificacao', 'App\Controller\ColaboradorController:listarColaboradoresQualificacao')->setName('listarTodos');
        $app->post('/RelatorioDadosColaborador', 'App\Controller\ColaboradorController:RelatorioDadosColaborador')->setName('RelatorioDadosColaborador');
        $app->get('/RelDadosColaborador', 'App\Controller\ColaboradorController:RelatorioDadosColaborador')->setName('RelatorioDadosColaborador');
        $app->post('/listarColaboradorLeadCalendario', 'App\Controller\ColaboradorController:listarColaboradorLeadCalendario')->setName('listarColaboradorLeadCalendario');
        $app->post('/listarPk', 'App\Controller\ColaboradorController:listarPk')->setName('listarPk');
        $app->post('/verificarCpf', 'App\Controller\ColaboradorController:verificarCpf')->setName('verificarCpf');
        $app->get('/relatorioAniversariantesMes','App\Controller\ColaboradorController:relatorioAniversariantesMes')->setName('relatorioAniversariantesMes');
        $app->post('/listarColaboradorLead','App\Controller\ColaboradorController:listarColaboradorLead')->setName('listarColaboradorLead');
        $app->post('/listarColaboradoresQualidicacaoContrato','App\Controller\ColaboradorController:listarColaboradoresQualidicacaoContrato')->setName('listarColaboradoresQualidicacaoContrato');
        $app->get('/RelatorioColaboradorCurso','App\Controller\ColaboradorController:RelatorioColaboradorCurso')->setName('RelatorioColaboradorCurso');
        $app->get('/exportRelAniversarianteMes','App\Controller\ColaboradorController:exportRelAniversarianteMes')->setName('exportRelAniversarianteMes');
        $app->get('/exportRelCurso','App\Controller\ColaboradorController:exportRelCurso')->setName('exportRelCurso');
        $app->post('/excluir', 'App\Controller\ColaboradorController:excluir')->setName('excluir');
        $app->post('/listarColaboradorPkPrint', 'App\Controller\ColaboradorController:listarColaboradorPkPrint')->setName('listarColaboradorPkPrint');
        $app->get('/listarFormulario', 'App\Controller\ColaboradorController:listarFormulario')->setName('listarFormulario');
        $app->post('/listarCursoColaboradores', 'App\Controller\ColaboradorController:listarCursoColaboradores')->setName('listarCursoColaboradores');
        $app->post('/listarNomeFilhoColaboradorPk', 'App\Controller\ColaboradorController:listarNomeFilhoColaboradorPk')->setName('listarNomeFilhoColaboradorPk');
        $app->post('/listarBeneficioColaboradores', 'App\Controller\ColaboradorController:listarBeneficioColaboradores')->setName('listarBeneficioColaboradores');
        $app->post('/listarAfastamentoColaboradores', 'App\Controller\ColaboradorController:listarAfastamentoColaboradores')->setName('listarAfastamentoColaboradores');
		$app->post('/listaColaborador','App\Controller\ColaboradorController:listaColaborador')->setName('listaColaborador');
        $app->post('/listarDadosBancarios','App\Controller\ColaboradorController:listarDadosBancarios')->setName('listarDadosBancarios');
		$app->post('/listarColaboradorFolha','App\Controller\ColaboradorController:listarColaboradorFolha')->setName('listarColaboradorFolha');
        $app->post('/listarColaboradorEscala','App\Controller\ColaboradorController:listarColaboradorEscala')->setName('listarColaboradorEscala');
		$app->post('/listarTodosRel','App\Controller\ColaboradorController:listarTodosRel')->setName('listarTodosRel');
        $app->get('/RelatorioAcompanhamentoFerias','App\Controller\ColaboradorController:RelatorioAcompanhamentoFerias')->setName('RelatorioAcompanhamentoFerias');
       
    });

    //COMPRA
    $app->group('/compra', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\CompraController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\CompraController:salvar')->setName('salvar');
        $app->post('/salvarProduto', 'App\Controller\CompraController:salvarProduto')->setName('salvarProduto');
        $app->post('/listarPk', 'App\Controller\CompraController:listarPk')->setName('listarPk');
        $app->post('/excluir', 'App\Controller\CompraController:excluir')->setName('excluir');
        $app->post('/lerXml', 'App\Controller\CompraController:lerXml')->setName('lerXml');
		$app->get('/relControleCompra', 'App\Controller\CompraController:relControleCompra')->setName('relControleCompra');
		$app->get('/relControleSolicitacaoCompra', 'App\Controller\CompraController:relControleSolicitacaoCompra')->setName('relControleSolicitacaoCompra');
    
    });
    
    //CONTROLE NFE
    $app->group('/controle_nfse', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\ControleNfseController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\ControleNfseController:salvar')->setName('salvar');
        $app->get('/download_nfse/{id_notas}', 'App\Controller\ControleNfseController:downloadNfse')->setName('download_nfse');
        $app->get('/download_nfse_lancamento/{notas_pk}', 'App\Controller\ControleNfseController:downloadNfseLancamento')->setName('download_nfse');
        $app->get('/exbir_xml/{id_notas}', 'App\Controller\ControleNfseController:exibirXML')->setName('exbir_xml');
        $app->post('/cancelarNota', 'App\Controller\ControleNfseController:cancelarNota')->setName('cancelarNota');
        
    });

    //CONCILIACAO BANCO
    $app->group('/conciliacao_bancaria', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\ConciliacaoBancariaController:listarGrid')->setName('listarGrid');
        $app->get('/listarDataTableItens', 'App\Controller\ConciliacaoBancariaController:listarDataTableItens')->setName('listarDataTableItens');
        $app->post('/salvar', 'App\Controller\ConciliacaoBancariaController:salvar')->setName('listarGrid');
        $app->post('/excluir', 'App\Controller\ConciliacaoBancariaController:excluir')->setName('excluir');
        $app->post('/salvarConciliacaoLancamento', 'App\Controller\ConciliacaoBancariaController:salvarConciliacaoLancamento')->setName('salvarConciliacaoLancamento');
        $app->post('/listarPk', 'App\Controller\ConciliacaoBancariaController:listarPk')->setName('listarPk');
    });
    //CONJUNTO MATERIAL
    $app->group('/conjunto_material', function () use ($app) {
        $app->get('/listarMovimentarMaterialProd', 'App\Controller\ConjuntoMaterialController:listarMovimentarMaterialProd')->setName('listarDataTable');
        $app->post('/salvar', 'App\Controller\ConjuntoMaterialController:salvar')->setName('listarDataTable');
    });

    //CONTA
    $app->group('/conta', function () use ($app) {
		$app->post('/salvar', 'App\Controller\ContaController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\ContaController:excluir')->setName('excluir');    	
		$app->post('/ativar', 'App\Controller\ContaController:ativar')->setName('ativar');  	
        $app->post('/desativar', 'App\Controller\ContaController:desativar')->setName('desativar');  	
        $app->post('/listarTodos', 'App\Controller\ContaController:listarTodos')->setName('listarTodos');
        $app->get('/carregarLogo', 'App\Controller\ContaController:carregarLogo')->setName('carregarLogo');
        $app->get('/listarDataTable', 'App\Controller\ContaController:listarDataTable')->setName('listarDataTable');
        $app->post('/listarPk', 'App\Controller\ContaController:listarPk')->setName('listarPk');
        $app->post('/verificarConta', 'App\Controller\ContaController:verificarConta')->setName('verificarConta');
        $app->post('/listarEmpresasCnpj', 'App\Controller\ContaController:listarEmpresasCnpj')->setName('listarEmpresasCnpj');
        $app->post('/configModulo', 'App\Controller\ContaController:configModulo')->setName('configModulo');
        $app->post('/contaLeadSalvar', 'App\Controller\ContaController:contaLeadSalvar')->setName('contaLeadSalvar');
        $app->post('/verificaContaPrincipal', 'App\Controller\ContaController:verificaContaPrincipal')->setName('verificaContaPrincipal');
    });

	//CONTAS BANCARIAS
    $app->group('/conta_bancaria', function () use ($app) {
		$app->post('/salvar', 'App\Controller\ContaBancariaController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\ContaBancariaController:excluir')->setName('excluir');    	
        $app->get('/listarGrid', 'App\Controller\ContaBancariaController:listarGrid')->setName('listarGrid');
        $app->post('/listarPk', 'App\Controller\ContaBancariaController:listarPk')->setName('listarPk');
		$app->post('/listaPorEmpresa', 'App\Controller\ContaBancariaController:listaPorEmpresa')->setName('listaPorEmpresa');
        $app->post('/listarEmpresaContasAtivas', 'App\Controller\ContaBancariaController:listarEmpresaContasAtivas')->setName('listarEmpresaContasAtivas');
        $app->post('/listarContasLancamento', 'App\Controller\ContaBancariaController:listarContasLancamento')->setName('listarContasLancamento');
    
    });
    //CONTATOS
    $app->group('/contato', function () use ($app) {
        $app->post('/salvar', 'App\Controller\ContatoController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\ContatoController:excluir')->setName('excluir');
    });
    //CONTRATOS
    $app->group('/contrato', function () use ($app) {
        $app->post('/salvar', 'App\Controller\ContratoController:salvar')->setName('salvar');
        $app->post('/salvarProdutosItens', 'App\Controller\ContratoController:salvarProdutosItens')->setName('salvarProdutosItens');
        $app->post('/excluir', 'App\Controller\ContratoController:excluir')->setName('excluir');
        $app->post('/excluirProdutosItens', 'App\Controller\ContratoController:excluirProdutosItens')->setName('excluirProdutosItens');
        $app->get('/listarContratoOperacional', 'App\Controller\ContratoController:listarContratoOperacional')->setName('listarContratoOperacional');
        $app->get('/listarProdutosItens', 'App\Controller\ContratoController:listarProdutosItens')->setName('listarProdutosItens');
        $app->post('/excluirProdutosItensPk', 'App\Controller\ContratoController:excluirProdutosItensPk')->setName('excluirProdutosItensPk');
        $app->post('/listarContratoPai', 'App\Controller\ContratoController:listarContratoPai')->setName('listarContratoPai');
        $app->post('/listarLeadsPk', 'App\Controller\ContratoController:listarLeadsPk')->setName('listarLeadsPk');
        $app->post('/listarPk', 'App\Controller\ContratoController:listarPk')->setName('listarPk');
        $app->post('/listarPkCad', 'App\Controller\ContratoController:listarPkCad')->setName('listarPkCad');
    	$app->post('/listaLeadContratos', 'App\Controller\ContratoController:listaLeadContratos')->setName('listaLeadContratos');
        $app->post('/listaColaboradorContratos', 'App\Controller\ContratoController:listaColaboradorContratos')->setName('listaColaboradorContratos');
		$app->get('/relContrato', 'App\Controller\ContratoController:relContrato')->setName('relContrato');
		$app->post('/listarContratos', 'App\Controller\ContratoController:listarContratos')->setName('listarContratos');

    });
    //CONTRATOS ITENS
    $app->group('/contrato_item', function () use ($app) {
        $app->get('/excluir', 'App\Controller\ContratoItemController:excluir')->setName('excluir');
        $app->post('/listarContratoItem', 'App\Controller\ContratoItemController:listarContratoItem')->setName('listarContratoItem');
        $app->post('/verificaServidoQtdeEscala', 'App\Controller\ContratoItemController:verificaServidoQtdeEscala')->setName('verificaServidoQtdeEscala');
    });
    //CONTRATOS DADOS FATURAMENTO
    $app->group('/contrato_dados_faturamento', function () use ($app) {
        $app->post('/listarGridContratoDadosFaturamento', 'App\Controller\ContratoDadosFaturamentoController:listarGridContratoDadosFaturamento')->setName('listarGridContratoDadosFaturamento');
        $app->post('/addMes', 'App\Controller\ContratoDadosFaturamentoController:addMes')->setName('addMes');
    });

    //COMPRAS SOLICITAÇÃO
    $app->group('/compra_solicitacao', function () use ($app) {
        $app->post('/listarPk', 'App\Controller\CompraSolicitacaoController:listarPk')->setName('listarPk');
        $app->get('/listarGrid', 'App\Controller\CompraSolicitacaoController:listarGrid')->setName('listarGrid');
        $app->post('/excluir', 'App\Controller\CompraSolicitacaoController:excluir')->setName('excluir');
        $app->post('/salvar', 'App\Controller\CompraSolicitacaoController:salvar')->setName('salvar');
    });

    //COMPRAS SOLICITAÇÃO ORÇAMENTOS
    $app->group('/compra_solicitacao_orcamento', function () use ($app) {
        $app->post('/listarPk', 'App\Controller\CompraSolicitacaoOrcamentoController:listarPk')->setName('listarPk');
        $app->post('/vinculaSolicitacaoOrcamento', 'App\Controller\CompraSolicitacaoOrcamentoController:vinculaSolicitacaoOrcamento')->setName('salvar');
        $app->post('/salvar', 'App\Controller\CompraSolicitacaoOrcamentoController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\CompraSolicitacaoOrcamentoController:excluir')->setName('excluir');
        $app->get('/listarGrid', 'App\Controller\CompraSolicitacaoOrcamentoController:listarGrid')->setName('listarGrid');
    });
    
    //COMPRAS SOLICITAÇÃO ORÇAMENTOS ITENS
    $app->group('/compra_solicitacao_orcamento_item', function () use ($app) {
        //colocar a rota aqui--------
        $app->post('/excluir', 'App\Controller\CompraSolicitacaoOrcamentoItemController:excluir')->setName('excluir');
        $app->post('/excluirPorSolicitacaoOrcamento', 'App\Controller\CompraSolicitacaoOrcamentoItemController:excluirPorSolicitacaoOrcamento')->setName('excluirPorSolicitacaoOrcamento');
        $app->post('/salvar', 'App\Controller\CompraSolicitacaoOrcamentoItemController:salvar')->setName('salvar');
        $app->get('/listarItensOrcamentoPk', 'App\Controller\CompraSolicitacaoOrcamentoItemController:listarItensOrcamentoPk')->setName('listarItensOrcamentoPk');
    });

	//CURSOS
    $app->group('/curso', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\CursoController:listarGrid')->setName('listarGrid');
        $app->post('/excluir', 'App\Controller\CursoController:excluir')->setName('excluir');
        $app->post('/salvar', 'App\Controller\CursoController:salvar')->setName('salvar');
        $app->post('/listarPk', 'App\Controller\CursoController:listarPk')->setName('listarPk');
        $app->post('/listarTodosAtivo', 'App\Controller\CursoController:listarTodosAtivo')->setName('listarTodosAtivo');
    });
	//DISCRIMINAÇÃO SERVIÇOS
    $app->group('/discriminacao_servicos', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\DiscriminacaoServicosController:listarGrid')->setName('listarGrid');
        $app->post('/excluir', 'App\Controller\DiscriminacaoServicosController:excluir')->setName('excluir');
        $app->post('/salvar', 'App\Controller\DiscriminacaoServicosController:salvar')->setName('salvar');
        $app->post('/listarPk', 'App\Controller\DiscriminacaoServicosController:listarPk')->setName('listarPk');
        $app->post('/listarDiscriminacao', 'App\Controller\DiscriminacaoServicosController:listarDiscriminacao')->setName('listarDiscriminacao');
    });
    //ISS MUNICIPIO
    $app->group('/iss_municipio', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\IssMunicipioController:listarGrid')->setName('listarGrid');
        $app->post('/pegarAliquotaPorMunicipio', 'App\Controller\IssMunicipioController:pegarAliquotaPorMunicipio')->setName('pegarAliquotaPorMunicipio');
        $app->post('/excluir', 'App\Controller\IssMunicipioController:excluir')->setName('excluir');
        $app->post('/salvar', 'App\Controller\IssMunicipioController:salvar')->setName('salvar');
        $app->post('/listarPk', 'App\Controller\IssMunicipioController:listarPk')->setName('listarPk');
        $app->post('/listarCidade', 'App\Controller\IssMunicipioController:listarCidade')->setName('listarCidade');
    });
    //DOCUMENTOS
    $app->group('/documento', function () use ($app) {
        $app->get('/listarDocumentosAgenda', 'App\Controller\DocumentoController:listarDocumentosAgenda')->setName('removerArquivo');
        $app->get('/listarDocumentosCompra', 'App\Controller\DocumentoController:listarDocumentosCompra')->setName('listarDocumentosCompra');
        $app->get('/listarDocumentosOc', 'App\Controller\DocumentoController:listarDocumentosOc')->setName('listarDocumentosOc');
        $app->get('/listarDocumentosLead', 'App\Controller\DocumentoController:listarDocumentosLead')->setName('listarDocumentosLead');
        $app->get('/listarDocumentosColaborador', 'App\Controller\DocumentoController:listarDocumentosColaborador')->setName('listarDocumentosColaborador');
        $app->post('/listarQtdeDocumentosOc', 'App\Controller\DocumentoController:listarDocumentosOc')->setName('listarDocumentosOc');
        $app->post('/renomearArquivoAgenda', 'App\Controller\DocumentoController:renomearArquivoAgenda')->setName('salvar');
        $app->post('/renomearArquivoColaborador', 'App\Controller\DocumentoController:renomearArquivoColaborador')->setName('renomearArquivoColaborador');
		$app->get('/listarDocumentosLancamentos', 'App\Controller\DocumentoController:listarDocumentosLancamentos')->setName('listarDocumentosLancamentos');
        $app->post('/renomearArquivoLancamento', 'App\Controller\DocumentoController:renomearArquivoLancamento')->setName('salvar');
        $app->post('/renomearArquivo', 'App\Controller\DocumentoController:renomearArquivo')->setName('salvar');
        $app->post('/renomearArquivoCompra', 'App\Controller\DocumentoController:renomearArquivoCompra')->setName('salvar');
        $app->post('/removerArquivo', 'App\Controller\DocumentoController:removerArquivo')->setName('removerArquivo');
        $app->post('/excluir', 'App\Controller\DocumentoController:excluir')->setName('removerArquivo');
        $app->post('/excluirDocBd', 'App\Controller\DocumentoController:excluirDocBd')->setName('removerArquivo');
        $app->post('/salvar', 'App\Controller\DocumentoController:salvar')->setName('salvar');
        $app->post('/salvarLancamentos', 'App\Controller\DocumentoController:salvarLancamentos')->setName('salvar');
        $app->get('/listarDocumentoClienteLead', 'App\Controller\DocumentoController:listarDocumentoClienteLead')->setName('listarDocumentoClienteLead');
    });

    //EQUIPE
    $app->group('/equipe', function () use ($app) {
        $app->post('/listarTodos', 'App\Controller\EquipeController:listarTodos')->setName('listarTodos');
		$app->get('/listarGrid', 'App\Controller\EquipeController:listarGrid')->setName('listarGrid');
        $app->post('/excluir', 'App\Controller\EquipeController:excluir')->setName('excluir');
        $app->post('/salvar', 'App\Controller\EquipeController:salvar')->setName('salvar');
        $app->post('/listarPk', 'App\Controller\EquipeController:listarPk')->setName('listarPk');
        $app->post('/listarResponsavelEquipe', 'App\Controller\EquipeController:listarResponsavelEquipe')->setname('listarResponsavelEquipe');
        $app->post('/listarEquipesUsuarios', 'App\Controller\EquipeController:listarEquipesUsuarios')->setName('listarPk');
        $app->post('/listarEquipeUsuarioLogado', 'App\Controller\EquipeController:listarEquipeUsuarioLogado')->setName('listarPk');
     });

	//FATURAMENTO
    $app->group('/faturamento', function () use ($app) {
        $app->get('/listarDataTable', 'App\Controller\FaturamentoController:listarDataTable')->setName('listarDataTable');
        $app->post('/salvar', 'App\Controller\FaturamentoController:salvar')->setName('salvar');
        $app->post('/listarDadosFaturamento', 'App\Controller\FaturamentoController:listarDadosFaturamento')->setName('listarDadosFaturamento');
        $app->post('/listarUpdateFaturamento', 'App\Controller\FaturamentoController:listarUpdateFaturamento')->setName('listarUpdateFaturamento');
        $app->post('/salvarItensContratos', 'App\Controller\FaturamentoController:salvarItensContratos')->setName('salvarItensContratos');
		$app->post('/processar', 'App\Controller\FaturamentoController:processar')->setName('processar');
		$app->post('/listarDadosEmissoes', 'App\Controller\FaturamentoController:listarDadosEmissoes')->setName('listarDadosEmissoes');
		$app->post('/faturamentoCopiar', 'App\Controller\FaturamentoController:faturamentoCopiar')->setName('faturamentoCopiar');
		$app->post('/listarContratoFaturamento', 'App\Controller\FaturamentoController:listarContratoFaturamento')->setName('listarContratoFaturamento');
    	$app->post('/cancelarFaturamento', 'App\Controller\FaturamentoController:cancelarFaturamento')->setName('cancelarFaturamento');
		$app->post('/excluir', 'App\Controller\FaturamentoController:excluir')->setName('excluir');
		$app->post('/listarDadosFaturamentoNFSE', 'App\Controller\FaturamentoController:listarDadosFaturamentoNFSE')->setName('listarDadosFaturamentoNFSE');
		$app->post('/listarDetalhamentoCorpoNota', 'App\Controller\FaturamentoController:listarDetalhamentoCorpoNota')->setName('listarDetalhamentoCorpoNota');
		$app->get('/listarContratos', 'App\Controller\FaturamentoController:listarContratos')->setName('listarContratos');
	});

     //FERIADO
     $app->group('/feriado', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\FeriadoController:listarGrid')->setName('listarGrid');
		$app->get('/listarFeriadoRelogio', 'App\Controller\FeriadoController:listarFeriadoRelogio')->setName('listarGrid');
        $app->post('/excluir', 'App\Controller\FeriadoController:excluir')->setName('excluir');
        $app->post('/salvar', 'App\Controller\FeriadoController:salvar')->setName('salvar');
    });
     //FORNECEDORES
     $app->group('/fornecedor', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\FornecedorController:listarGrid')->setName('listarGrid');
        $app->post('/excluir', 'App\Controller\FornecedorController:excluir')->setName('excluir');
        $app->post('/salvar', 'App\Controller\FornecedorController:salvar')->setName('salvar');
        $app->post('/listarPk', 'App\Controller\FornecedorController:listarPk')->setName('listarPk');
        $app->post('/listarTodos', 'App\Controller\FornecedorController:listarTodos')->setName('listarTodos');
	    $app->post('/listarCpfCnpjFornecedor', 'App\Controller\FornecedorController:listarCpfCnpjFornecedor')->setName('listarCpfCnpjFornecedor');
		$app->post('/listarPorCategoria', 'App\Controller\FornecedorController:listarPorCategoria')->setName('listarPorCategoria');
     
	});
	
	//ENTRADA ESTOQUE
     $app->group('/entrada_estoque', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\EntradaEstoqueController:listarGrid')->setName('listarGrid');
        $app->post('/excluir', 'App\Controller\EntradaEstoqueController:excluir')->setName('excluir');
        $app->post('/salvar', 'App\Controller\EntradaEstoqueController:salvar')->setName('salvar');
        $app->post('/listarPk', 'App\Controller\EntradaEstoqueController:listarPk')->setName('listarPk');
     });

	//GRUPO
    $app->group('/grupo', function () use ($app) {
        $app->post('/listarTodos', 'App\Controller\GrupoController:listarTodos')->setName('listarTodos');
        $app->get('/listarGrid', 'App\Controller\GrupoController:listarGrid')->setName('listarGrid');
		$app->post('/listarPk', 'App\Controller\GrupoController:listarPk')->setName('listarPk');
        $app->post('/listarPermissoesGrupo', 'App\Controller\GrupoController:listarPermissoesGrupo')->setName('listarPermissoesGrupo');
        $app->post('/salvar', 'App\Controller\GrupoController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\GrupoController:excluir')->setName('salvar');
    });

	//GENERO
    $app->group('/genero', function () use ($app) {
        $app->post('/listarTodos', 'App\Controller\GeneroController:listarTodos')->setName('listarTodos');
       });
    //LANÇAMENTOS
    $app->group('/lancamento', function () use ($app){
        $app->post('/listaItensGrupoLeads', 'App\Controller\LancamentoController:listaItensGrupoLeads')->setName('listaItensGrupoLeads');
        $app->post('/listaItensGrupoColaboradores', 'App\Controller\LancamentoController:listaItensGrupoColaboradores')->setName('listaItensGrupoColaboradores');
        $app->post('/listaItensGrupoFornecedores', 'App\Controller\LancamentoController:listaItensGrupoFornecedores')->setName('listaItensGrupoFornecedores');
        $app->post('/salvar', 'App\Controller\LancamentoController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\LancamentoController:excluir')->setName('excluir');
        $app->post('/listarLancamentoPk', 'App\Controller\LancamentoController:listarLancamentoPk')->setName('listarLancamentoPk');
        $app->post('/listarExtratoMes', 'App\Controller\LancamentoController:listarExtratoMes')->setName('listarExtratoMes');
        $app->post('/listarReceita', 'App\Controller\LancamentoController:listarReceita')->setName('listarReceita');
        $app->post('/listarDespesa', 'App\Controller\LancamentoController:listarDespesa')->setName('listarDespesa');
        $app->post('/listarDashboard', 'App\Controller\LancamentoController:listarDashboard')->setName('listarDashboard');
        $app->post('/listarLancamento', 'App\Controller\LancamentoController:listarLancamento')->setName('listarLancamento');
        $app->post('/RelatorioLancamento', 'App\Controller\LancamentoController:RelatorioLancamento')->setName('RelatorioLancamento');
		$app->post('/migrarBaseFinanceira', 'App\Controller\LancamentoController:migrarBaseFinanceira')->setName('migrarBaseFinanceira');
        $app->post('/relLancamentoPlanoConta', 'App\Controller\LancamentoController:relLancamentoPlanoConta')->setName('relLancamentoPlanoConta');
        $app->post('/listarImpressao', 'App\Controller\LancamentoController:listarImpressao')->setName('listarImpressao');
		$app->post('/cargaTabelaLancamentos', 'App\Controller\LancamentoController:cargaTabelaLancamentos')->setName('cargaTabelaLancamentos');
        $app->post('/listarHistoricoParcial', 'App\Controller\LancamentoController:listarHistoricoParcial')->setName('listarHistoricoParcial');
        $app->get('/listarDataTableReceitaDespesaConciliacao', 'App\Controller\LancamentoController:listarDataTableReceitaDespesaConciliacao')->setName('listarDataTableReceitaDespesaConciliacao');
        $app->get('/relReceitaPostoTrabalho', 'App\Controller\LancamentoController:relReceitaPostoTrabalho')->setName('relReceitaPostoTrabalho');
        $app->get('/relDespesaPostoTrabalho', 'App\Controller\LancamentoController:relDespesaPostoTrabalho')->setName('relDespesaPostoTrabalho');
        $app->get('/listarLancamentosUsuarios', 'App\Controller\LancamentoController:listarLancamentosUsuarios')->setName('listarLancamentosUsuarios');
    });
    //LEAD
    $app->group('/lead', function () use ($app) {
		$app->post('/salvarQrCode', 'App\Controller\LeadController:salvarQrCode')->setName('salvarQrCode');
        $app->get('/listarDataTable', 'App\Controller\LeadController:listarDataTable')->setName('listarDataTable');
        $app->post('/listarTodos', 'App\Controller\LeadController:listarTodos')->setName('listarTodos');
		$app->post('/listarQRCode', 'App\Controller\LeadController:listarQRCode')->setName('listarQRCode');
        $app->post('/listarEnderecos', 'App\Controller\LeadController:listarEnderecos')->setName('listarEnderecos');
        $app->post('/listarTodosPostTrabalho', 'App\Controller\LeadController:listarTodosPostTrabalho')->setName('listarDataTable');
        $app->get('/listarContatoLead', 'App\Controller\LeadController:listarContatoLead')->setName('listarDataTable');
        $app->post('/listarPk', 'App\Controller\LeadController:listarPk')->setName('listarPk');
        $app->post('/listarTodosClientes', 'App\Controller\LeadController:listarTodosClientes')->setName('listarDataTable');
        $app->post('/verificarCNPJ', 'App\Controller\LeadController:verificarCNPJ')->setName('verificarCNPJ');
        $app->post('/listarLeadPai', 'App\Controller\LeadController:listarLeadPai')->setName('listarLeadPai');
        $app->post('/salvar', 'App\Controller\LeadController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\LeadController:excluir')->setName('excluir');
		$app->post('/listaLeadsClientes', 'App\Controller\LeadController:listaLeadsClientes')->setName('listaLeadsClientes');
        $app->post('/listaLeadsPostosTrabalho', 'App\Controller\LeadController:listaLeadsPostosTrabalho')->setName('listaLeadsPostosTrabalho');
        $app->post('/listarClienteColaborador', 'App\Controller\LeadController:listarClienteColaborador')->setName('listarClienteColaborador');
        $app->post('/listaColaboradorPostosTrabalho', 'App\Controller\LeadController:listaColaboradorPostosTrabalho')->setName('listaColaboradorPostosTrabalho');
        $app->post('/listaFornecedorPostosTrabalho', 'App\Controller\LeadController:listaFornecedorPostosTrabalho')->setName('listaFornecedorPostosTrabalho');
		$app->post('/listarLeadsPorEmpresa', 'App\Controller\LeadController:listarLeadsPorEmpresa')->setName('listarLeadsPorEmpresa');
		$app->post('/listarCpfCnpjClientes', 'App\Controller\LeadController:listarCpfCnpjClientes')->setName('listarCpfCnpjClientes');
    });
    //METODO PAGAMENTO
    $app->group('/metodo_pagamento', function () use ($app) {
        $app->post('/listarTodos', 'App\Controller\MetodoPagamentoController:listarTodos')->setName('listarTodos');
    });
	//MÓDULOS
    $app->group('/modulo', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\ModuloController:listarGrid')->setName('listarGrid');
		$app->post('/listarTodos', 'App\Controller\ModuloController:listarTodos')->setName('listarTodos');
		$app->post('/listarTipoModulo', 'App\Controller\ModuloController:listarTipoModulo')->setName('listarTipoModulo');
        $app->post('/salvar', 'App\Controller\ModuloController:salvar')->setName('salvar');
        $app->post('/listarPk', 'App\Controller\ModuloController:listarPk')->setName('listarPk');
        $app->post('/excluir', 'App\Controller\ModuloController:excluir')->setName('excluir');
    });
    //MOVIMENTACAO ESTOQUE
    $app->group('/movimentacao_estoque', function () use ($app) {
        $app->get('/listar_por_pk_conjunto', 'App\Controller\MovimentacaoEstoqueController:listar_por_pk_conjunto')->setName('listar_por_pk_conjunto');
        $app->get('/listar_impressao', 'App\Controller\MovimentacaoEstoqueController:listar_impressao')->setName('listar_impressao');
        $app->post('/excluir', 'App\Controller\MovimentacaoEstoqueController:excluir')->setName('excluir');
        $app->post('/salvar', 'App\Controller\MovimentacaoEstoqueController:salvar')->setName('salvar');
	    $app->get('/RelatorioEstoque', 'App\Controller\MovimentacaoEstoqueController:RelatorioEstoque')->setName('RelatorioEstoque');
		$app->get('/relatorioMovimentacaoEstoqueTroca', 'App\Controller\MovimentacaoEstoqueController:relatorioMovimentacaoEstoqueTroca')->setName('relatorioMovimentacaoEstoqueTroca');
		$app->get('/relCompraMovimentacaoLead', 'App\Controller\MovimentacaoEstoqueController:relCompraMovimentacaoLead')->setName('relCompraMovimentacaoLead');
    
    });
    //OCORRENCIA
    $app->group('/ocorrencia', function () use ($app) {
        $app->get('/listarOcorrenciasLeadPk', 'App\Controller\OcorrenciaController:listarOcorrenciasLeadPk')->setName('listarOcorrenciasLeadPk');
        $app->get('/listarDataTableGrid', 'App\Controller\OcorrenciaController:listarDataTableGrid')->setName('listarDataTableGrid');
        $app->get('/listarDataTableGridCliente', 'App\Controller\OcorrenciaController:listarDataTableGridCliente')->setName('listarDataTableGridCliente');
        $app->post('/salvar', 'App\Controller\OcorrenciaController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\OcorrenciaController:excluir')->setName('excluir');
        $app->post('/listarPorPk', 'App\Controller\OcorrenciaController:listarPorPk')->setName('salvar');
    });
    
    //PLANO CONTAS
    $app->group('/plano_contas', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\PlanoContaController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\PlanoContaController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\PlanoContaController:excluir')->setName('excluir');
        $app->post('/listarPk', 'App\Controller\PlanoContaController:listarPk')->setName('listarPk');
		$app->post('/listaPorCategoria', 'App\Controller\PlanoContaController:listaPorCategoria')->setName('listaPorCategoria');
		$app->post('/listarTodos', 'App\Controller\PlanoContaController:listarTodos')->setName('listaTodos');


    });

	//PONTO
    $app->group('/ponto', function () use ($app) {
        $app->post('/listarColaborador', 'App\Controller\PontoController:listarColaborador')->setName('relAcompanhamentoPontoSintetico');
        $app->post('/acompanhamentoPontoDiario', 'App\Controller\PontoController:acompanhamentoPontoDiario')->setName('acompanhamentoPontoDiario');
        $app->post('/relatorioPontoSinteticaAntigo', 'App\Controller\PontoController:relatorioPontoSinteticaAntigo')->setName('relAcompanhamentoPontoSintetico');
        $app->post('/relatorioPonto', 'App\Controller\PontoController:relatorioPonto')->setName('relatorioPonto');
        $app->post('/reloginhoHistoricoPonto', 'App\Controller\PontoController:reloginhoHistoricoPonto')->setName('relatorioPonto');
        $app->post('/relAcompanhamentoPontoSintetico', 'App\Controller\PontoController:relAcompanhamentoPontoSintetico')->setName('relAcompanhamentoPontoSintetico');
        $app->post('/validarImgPonto', 'App\Controller\PontoController:validarImgPonto')->setName('relAcompanhamentoPontoSintetico');
        $app->get('/popUpAtraso', 'App\Controller\PontoController:popUpAtraso')->setName('popUpAtraso');
        $app->get('/relRondas', 'App\Controller\PontoController:relRondas')->setName('relRondas');
        $app->post('/pegarDadosFechamento', 'App\Controller\PontoController:pegarDadosFechamento')->setName('pegarDadosFechamento');
    });
    //RONDA
    $app->group('/ronda', function () use ($app) {
        $app->get('/relRondas', 'App\Controller\RondaController:relRondas')->setName('relRondas');
    });
    //PRODUTO
    $app->group('/produto', function () use ($app) {
        $app->post('/listarPorCategoria', 'App\Controller\ProdutoController:listarPorCategoria')->setName('listarPorCategoria');
		$app->get('/listarGrid', 'App\Controller\ProdutoController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\ProdutoController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\ProdutoController:excluir')->setName('excluir');
        $app->post('/listarPk', 'App\Controller\ProdutoController:listarPk')->setName('listarPk');
    	$app->post('/listarTodosComTempoTroca', 'App\Controller\ProdutoController:listarTodosComTempoTroca')->setName('listarTodosComTempoTroca');
    });
	//PONTO FOLHA
    $app->group('/ponto_folha', function () use ($app) {
        $app->post('/listarGrid', 'App\Controller\PontoFolhaController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\PontoFolhaController:salvar')->setName('salvar');
        $app->post('/gerarFolhaPontoByRelogio', 'App\Controller\PontoFolhaController:gerarFolhaPontoByRelogio')->setName('gerarFolhaPontoByRelogio');
        $app->post('/finalizarFolhaByReloginho', 'App\Controller\PontoFolhaController:finalizarFolhaByReloginho')->setName('gerarFolhaPontoByRelogio');
        $app->get('/listarPontoFolhaPK', 'App\Controller\PontoFolhaController:listarPontoFolhaPK')->setName('listarPontoFolhaPK');
        $app->post('/listarFolhasRegistros', 'App\Controller\PontoFolhaController:listarFolhasRegistros')->setName('listarFolhasRegistros');
        $app->post('/listarRegistros', 'App\Controller\PontoFolhaController:listarRegistros')->setName('listarRegistros');
        $app->post('/listarFolhaPorPeridoColaborador', 'App\Controller\PontoFolhaController:listarFolhaPorPeridoColaborador')->setName('listarRegistros');
        $app->post('/salvarRegistros', 'App\Controller\PontoFolhaController:salvarRegistros')->setName('salvarRegistros');
        $app->post('/alterarRegistrosFolhaPonto', 'App\Controller\PontoFolhaController:alterarRegistrosFolhaPonto')->setName('alterarRegistrosFolhaPonto');
		$app->post('/salvarFolhaFinalizada', 'App\Controller\PontoFolhaController:salvarFolhaFinalizada')->setName('salvarFolhaFinalizada');
    	$app->post('/listarDadosImpressao', 'App\Controller\PontoFolhaController:listarDadosImpressao')->setName('listarDadosImpressao');	
    	$app->post('/listarFolhaPorPeriodoByLeads', 'App\Controller\PontoFolhaController:listarFolhaPorPeriodoByLeads')->setName('listarFolhaPorPeriodoByLeads');	
    	$app->post('/listarConsultaPontoColaborador', 'App\Controller\PontoFolhaController:listarConsultaPontoColaborador')->setName('listarConsultaPontoColaborador');	
		$app->post('/regerar', 'App\Controller\PontoFolhaController:regerar')->setName('regerar');	
		$app->post('/excluir', 'App\Controller\PontoFolhaController:excluir')->setName('excluir');
		$app->post('/excluirFolhaColaborador', 'App\Controller\PontoFolhaController:excluirFolhaColaborador')->setName('excluir');
        $app->get('/listarModalPonto', 'App\Controller\PontoFolhaController:listarModalPonto')->setName('listarModalPonto');	
    });
    //PROCESSO
    $app->group('/processo', function () use ($app) {
        $app->post('/listarProcessoLead', 'App\Controller\ProcessoController:listarProcessoLead')->setName('listarProcessoLead');
    });
	//PROCESSO DEFAULT
    $app->group('/processo_default', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\ProcessoDefaultController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\ProcessoDefaultController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\ProcessoDefaultController:excluir')->setName('excluir');
        $app->post('/listarPk', 'App\Controller\ProcessoDefaultController:listarPk')->setName('listarPk');
        $app->post('/listarModulosProcessoDefaultPk', 'App\Controller\ProcessoDefaultController:listarModulosProcessoDefaultPk')->setName('listarModulosProcessoDefaultPk');
        
        $app->post('/listarProcessoDefaultPk', 'App\Controller\ProcessoDefaultController:listarProcessoDefaultPk')->setName('listarProcessoDefaultPk');
    });
    //PRODUTO ITEM
    $app->group('/produto_item', function () use ($app) {
        $app->post('/listarPorPkProdutoNotIn', 'App\Controller\ProdutoItemController:listarPorPkProdutoNotIn')->setName('listarPorPkProdutoNotIn');
        $app->post('/listarPorProdutosQtde', 'App\Controller\ProdutoItemController:listarPorProdutosQtde')->setName('listarPorProdutosQtde');
		$app->post('/listarProdutoEstoqueNSerie', 'App\Controller\ProdutoItemController:listarProdutoEstoque')->setName('listarProdutoEstoqueNSerie');
        $app->post('/listarProdutoEstoque', 'App\Controller\ProdutoItemController:listarProdutoEstoque')->setName('listarProdutoEstoque');
        $app->post('/excluir', 'App\Controller\ProdutoItemController:excluir')->setName('excluir');
        $app->get('/listarPorCompra', 'App\Controller\ProdutoItemController:listarPorCompra')->setName('listarProdutoEstoque');
    });

    //PRODUTO SERVICOS
    $app->group('/produto_servico', function () use ($app) {
        $app->post('/listarFuncaoColaborador', 'App\Controller\ProdutoServicoController:listarFuncaoColaborador')->setName('listarFuncaoColaborador');
        $app->post('/listarTodos', 'App\Controller\ProdutoServicoController:listarTodos')->setName('listarFuncaoColaborador');
        $app->post('/listarProdutosContrato', 'App\Controller\ProdutoServicoController:listarProdutosContrato')->setName('listarProdutosContrato');
        $app->post('/listarQualificacaoColaboradores', 'App\Controller\ProdutoServicoController:listarQualificacaoColaboradores')->setName('listarQualificacaoColaboradores');
    });
    //PROPOSTAS FACILITIES
    $app->group('/propostas_facilities', function () use ($app) {
		$app->get('/relatorioProposta', 'App\Controller\PropostaFacilitiesController:relatorioProposta')->setName('relatorioProposta');
        $app->get('/listarDataTablePk', 'App\Controller\PropostaFacilitiesController:listarDataTablePk')->setName('listarDataTablePk');
        $app->post('/excluir', 'App\Controller\PropostaFacilitiesController:excluir')->setName('excluir');
        $app->post('/listarPropostaDetalhada', 'App\Controller\PropostaFacilitiesController:listarPropostaDetalhada')->setName('listarPropostaDetalhada');
        $app->post('/pegarDadosItens', 'App\Controller\PropostaFacilitiesController:pegarDadosItens')->setName('pegarDadosItens');
        $app->post('/salvar', 'App\Controller\PropostaFacilitiesController:salvar')->setName('salvar');
        $app->post('/listarDadosPropostaDetalhada', 'App\Controller\PropostaFacilitiesController:listarDadosPropostaDetalhada')->setName('listarDadosPropostaDetalhada');
        $app->post('/listarImpressaoProposta', 'App\Controller\PropostaFacilitiesController:listarImpressaoProposta')->setName('listarImpressaoProposta');
    });
    //PROPOSTAS FACILITIES ITENS
    $app->group('/propostas_facilities_itens', function () use ($app) {
        $app->post('/salvar', 'App\Controller\PropostaFacilitiesItemController:salvar')->setName('salvar');
        $app->post('/listarPropostaDetalhada', 'App\Controller\PropostaFacilitiesController:listarPropostaDetalhada')->setName('listarPropostaDetalhada');
        $app->post('/pegarDadosItens', 'App\Controller\PropostaFacilitiesController:pegarDadosItens')->setName('pegarDadosItens');
    });
    
    //TETO GASTOS
    $app->group('/teto_gasto', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\TetoGastoController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\TetoGastoController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\TetoGastoController:excluir')->setName('excluir');
        $app->post('/listarPk', 'App\Controller\TetoGastoController:listarPk')->setName('listarPk');

    });

    //TETO GASTOS ITENS
    $app->group('/teto_gasto_item', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\TetoGastoItemController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\TetoGastoItemController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\TetoGastoItemController:excluir')->setName('excluir');

    });

    //TIPO OCORRENCIA
    $app->group('/tipo_ocorrencia', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\TipoOcorrenciaController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\TipoOcorrenciaController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\TipoOcorrenciaController:excluir')->setName('excluir');
        $app->post('/listarPk', 'App\Controller\TipoOcorrenciaController:listarPk')->setName('listarPk');
        $app->post('/listarTodos', 'App\Controller\TipoOcorrenciaController:listarTodos')->setName('listarTodos');
    });
    //RETORNO
    $app->group('/retorno', function () use ($app) {
        $app->post('/listarOcorrenciasPk', 'App\Controller\RetornoController:listarOcorrenciasPk')->setName('listarTodos');
    });
    
    //SERVICO
    $app->group('/servico', function () use ($app) {
        $app->get('/listarGrid', 'App\Controller\ServicoController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\ServicoController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\ServicoController:excluir')->setName('excluir');
        $app->post('/listarPk', 'App\Controller\ServicoController:listarPk')->setName('listarPk');
        
        
    });

    //SOLICITACAO ACESSO APP
    $app->group('/solicitacao_acesso_app', function () use ($app) {
        $app->get('/listar_solicitacoes', 'App\Controller\SolicitacaoAcessoAppController:listarSolicitacoes')->setName('listar_solicitacoes');
        $app->post('/liberarAcesso', 'App\Controller\SolicitacaoAcessoAppController:liberarAcesso')->setName('salvar');
        $app->post('/refazerNovoRegistro', 'App\Controller\SolicitacaoAcessoAppController:refazerNovoRegistro')->setName('refazerNovoRegistro');
        $app->post('/buscarTodosBase64', 'App\Controller\SolicitacaoAcessoAppController:buscarTodosBase64')->setName('salvar');
        $app->post('/excluir', 'App\Controller\SolicitacaoAcessoAppController:excluir')->setName('excluir');
    });

    //SUPERVISÃO CHECKLIST
    $app->group('/supervisao_auditoria_lead', function () use ($app) {
        $app->post('/salvar', 'App\Controller\SupervisaoAuditoriaLeadController:salvar')->setName('salvar');
        $app->post('/listarPorCategoriasTiposSupervisao', 'App\Controller\SupervisaoAuditoriaLeadController:listarPorCategoriasTiposSupervisao')->setName('salvar');
    });


    //USUARIO
    $app->group('/usuario', function () use ($app) {
		$app->get('/listarGrid', 'App\Controller\UsuarioController:listarGrid')->setName('listarGrid');
        $app->post('/salvar', 'App\Controller\UsuarioController:salvar')->setName('salvar');
        $app->post('/excluir', 'App\Controller\UsuarioController:excluir')->setName('excluir');
        $app->post('/listarPk', 'App\Controller\UsuarioController:listarPk')->setName('listarPk');
        $app->post('/listarAdmSistema', 'App\Controller\UsuarioController:listarAdmSistema')->setName('listarAdmSistema');
        $app->post('/listarSupervisor', 'App\Controller\UsuarioController:listarSupervisor')->setName('listarSupervisor');
        $app->post('/listarTodos', 'App\Controller\UsuarioController:listarTodos')->setName('listarTodos');
        $app->post('/listarUsuarioLogado', 'App\Controller\UsuarioController:listarUsuarioLogado')->setName('listarUsuarioLogado');
        $app->post('/listarTodosSemAdm', 'App\Controller\UsuarioController:listarTodosSemAdm')->setName('listarTodosSemAdm');
        $app->post('/verificarPermissao', 'App\Controller\UsuarioController:verificarPermissao')->setName('verificarPermissao');
        $app->post('/verificarPermissaoMenu', 'App\Controller\UsuarioController:verificarPermissaoMenu')->setName('verificarPermissaoMenu');
        $app->post('/listarTodosGestores', 'App\Controller\UsuarioController:listarTodosGestores')->setName('listarTodosGestores');
        $app->post('/listarTodosAnalistas', 'App\Controller\UsuarioController:listarTodosAnalistas')->setName('listarTodosAnalistas');
        $app->post('/listarGruposUsuario', 'App\Controller\UsuarioController:listarGruposUsuario')->setName('listarGruposUsuario');
    });
});

