<?php

    /** @noinspection PhpParamsInspection */
    /** @noinspection SpellCheckingInspection */
    /** @noinspection PhpUndefinedVariableInspection */

    use App\Middleware\Authentication;
    $container = $app->getContainer();


    //AREA COLABORADOR
    $app->get('/area_colaborador/receptivo', 'App\Controller\AreaColaboradorController:receptivo')
        ->setName('root');
    $app->get('/area_colaborador/passo1', 'App\Controller\AreaColaboradorController:passo1')
        ->setName('root');
    $app->get('/area_colaborador/passo2', 'App\Controller\AreaColaboradorController:passo2')
        ->setName('root');
    $app->get('/area_colaborador/passo3', 'App\Controller\AreaColaboradorController:passo3')
        ->setName('root');

    $app->get('/area_colaborador/passo4', 'App\Controller\AreaColaboradorController:passo4')
    ->setName('root');

    $app->get('/area_colaborador/tirar_foto_novo_registro', 'App\Controller\AreaColaboradorController:tirarFotoNovoRegistro')
    ->setName('root');

    //REGISTRAR PONTO
    $app->get('/area_colaborador/receptivoRegistrarPonto', 'App\Controller\AreaColaboradorController:receptivoRegistrarPonto')
    ->setName('root');


    $app->get('/teste', 'App\Controller\testeController:teste')
    ->setName('root');


    //new Authentication($container) faz a verificação para ver se tem algum usuário logado.

    //MENU
    $app->get('/', 'App\Controller\LoginController:login')
        ->setName('root')->add(new Authentication($container));

    $app->get('/menu/principal', 'App\Controller\MenuController:principal')
        ->setName('root')->add(new Authentication($container));

    $app->get('/menu/comercial', 'App\Controller\MenuController:comercial')
        ->setName('root')->add(new Authentication($container));

    $app->get('/menu/supervisao', 'App\Controller\MenuController:supervisao')
        ->setName('root')->add(new Authentication($container));
    
    $app->get('/menu/administracao', 'App\Controller\MenuController:administracao')
        ->setName('root')->add(new Authentication($container));

    $app->get('/menu/rh', 'App\Controller\MenuController:rh')
        ->setName('root')->add(new Authentication($container));

    $app->get('/menu/operacional', 'App\Controller\MenuController:operacional')
        ->setName('root')->add(new Authentication($container));

    $app->get('/menu/compra_estoque', 'App\Controller\MenuController:compra_estoque')
        ->setName('root')->add(new Authentication($container));

    $app->get('/menu/cpainel', 'App\Controller\MenuController:cpainel')
        ->setName('root')->add(new Authentication($container));

    $app->get('/menu/financeiro', 'App\Controller\MenuController:financeiro')
        ->setName('root')->add(new Authentication($container));

    $app->get('/menu/relatorio', 'App\Controller\MenuController:relatorio')
        ->setName('root')->add(new Authentication($container));

    //ADF
    $app->get('/afd/receptivo', 'App\Controller\AfdController:receptivo')
    ->setName('root')->add(new Authentication($container));
    $app->get('/afd/cadForm', 'App\Controller\AfdController:cadForm')
    ->setName('root')->add(new Authentication($container));

    

    //AGENDA CALENDARIO
    $app->get('/agenda_calendario/receptivo', 'App\Controller\AgendaCalendarioController:receptivo')
        ->setName('root')->add(new Authentication($container));
    //AGENDA PADRAO
    $app->get('/agenda_colaborador_padrao/receptivo', 'App\Controller\AgendaColaboradorPadraoController:receptivo')
        ->setName('root')->add(new Authentication($container));

    $app->get('/agenda_colaborador_padrao/receptivoEscala', 'App\Controller\AgendaColaboradorPadraoController:receptivoEscala')
        ->setName('root')->add(new Authentication($container));

    $app->get('/agenda_colaborador_padrao/cadFormEscala', 'App\Controller\AgendaColaboradorPadraoController:cadFormEscala')
    ->setName('root')->add(new Authentication($container));

    //AUDITORIA CATEGORIAS
    $app->get('/auditoria_categoria/receptivo', 'App\Controller\AuditoriaCategoriaController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/auditoria_categoria/cadForm', 'App\Controller\AuditoriaCategoriaController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //AUDITORIA CATEGORIAS TIPOS
    $app->get('/auditoria_categoria_tipos/receptivo', 'App\Controller\AuditoriaCategoriaTiposController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/auditoria_categoria_tipos/cadForm', 'App\Controller\AuditoriaCategoriaTiposController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //ANALISE FINANCEIRA
    $app->get('/analise_financeira/receptivo', 'App\Controller\AnaliseFinanceiraController:receptivo')
    ->setName('root')->add(new Authentication($container));
    $app->get('/analise_financeira/cadForm', 'App\Controller\AnaliseFinanceiraController:cadForm')
    ->setName('root')->add(new Authentication($container));


	//CATEGORIA PRODUTO
    $app->get('/categoria_produto/receptivo', 'App\Controller\CategoriaProdutoController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/categoria_produto/cadForm', 'App\Controller\CategoriaProdutoController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //CERTIFICADOS EMPRESAS
    $app->get('/certificados_empresas/receptivo', 'App\Controller\CertificadosEmpresasController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/certificados_empresas/cadForm', 'App\Controller\CertificadosEmpresasController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //CONTROLE NFSE
    $app->get('/controle_nfse/receptivo', 'App\Controller\ControleNfseController:receptivo')
        ->setName('root')->add(new Authentication($container));

    $app->get('/controle_nfse/receptivoFake', 'App\Controller\ControleNfseController:receptivoFake')
        ->setName('root')->add(new Authentication($container));
    $app->get('/controle_nfse/cadForm', 'App\Controller\ControleNfseController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //CONTAS BANCARIAS
    $app->get('/contas_bancarias/receptivo', 'App\Controller\ContaBancariaController:receptivo')
    ->setName('root')->add(new Authentication($container));
    $app->get('/contas_bancarias/cadForm', 'App\Controller\ContaBancariaController:cadForm')
    ->setName('root')->add(new Authentication($container));
    //CONTRATO
    $app->get('/contrato/receptivo', 'App\Controller\ContratoController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/contrato/cadForm', 'App\Controller\ContratoController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //BENEFICIO
    $app->get('/beneficio/receptivo', 'App\Controller\BeneficioController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/beneficio/cadForm', 'App\Controller\BeneficioController:cadForm')
        ->setName('root');

    //CONTA
    $app->get('/conta/receptivo', 'App\Controller\ContaController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/conta/editarConta', 'App\Controller\ContaController:editarConta')
        ->setName('editarConta')->add(new Authentication($container));

    //COMPRAS SOLICITAÇÃO
    $app->get('/compra_solicitacao/receptivo', 'App\Controller\CompraSolicitacaoController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/compra_solicitacao/cadForm', 'App\Controller\CompraSolicitacaoController:cadForm')
        ->setName('root')->add(new Authentication($container));
    $app->get('/compra_solicitacao_orcamento/cadForm', 'App\Controller\CompraSolicitacaoOrcamentoController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //COLABORADOR
    $app->get('/colaborador/relAniversariantes', 'App\Controller\ColaboradorController:relAniversariantes')
        ->setName('root')->add(new Authentication($container));
    $app->get('/colaborador/receptivoRelAniversarianteMes', 'App\Controller\ColaboradorController:receptivoRelAniversarianteMes')
        ->setName('root')->add(new Authentication($container));
	$app->get('/colaborador/receptivo', 'App\Controller\ColaboradorController:receptivo')
    ->setName('root')->add(new Authentication($container));
    $app->get('/colaborador/painel', 'App\Controller\ColaboradorController:painel')
    ->setName('root')->add(new Authentication($container));
    $app->get('/colaborador/cadForm', 'App\Controller\ColaboradorController:cadForm')
    ->setName('root')->add(new Authentication($container));
    $app->get('/colaborador/cadFormCliente', 'App\Controller\ColaboradorController:cadFormCliente')
    ->setName('root')->add(new Authentication($container));
    $app->get('/colaborador/relCursos', 'App\Controller\ColaboradorController:relCursos')
    ->setName('root')->add(new Authentication($container));
    $app->get('/colaborador/receptivoRelCursos', 'App\Controller\ColaboradorController:receptivoRelCursos')
    ->setName('root')->add(new Authentication($container));
	$app->get('/colaborador/print', 'App\Controller\ColaboradorController:print')
    ->setName('root')->add(new Authentication($container));
    $app->get('/colaborador/fcAbrirGridForulario', 'App\Controller\ColaboradorController:fcAbrirGridForulario')
    ->setName('root')->add(new Authentication($container));
	
    $app->get('/colaborador/relApontamento', 'App\Controller\ColaboradorController:relApontamento')
        ->setName('root')->add(new Authentication($container));
    //COMPRA
    $app->get('/compra/receptivo', 'App\Controller\CompraController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/compra/cadForm', 'App\Controller\CompraController:cadForm')
        ->setName('editarConta')->add(new Authentication($container));
    //CONCILIACAO
    $app->get('/conciliacao_bancaria/receptivo', 'App\Controller\ConciliacaoBancariaController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/conciliacao_bancaria/cadForm', 'App\Controller\ConciliacaoBancariaController:cadForm')
        ->setName('editarConta')->add(new Authentication($container));
    //CURSO
    $app->get('/curso/receptivo', 'App\Controller\CursoController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/curso/cadForm', 'App\Controller\CursoController:cadForm')
        ->setName('editarConta')->add(new Authentication($container));

    //DOCUMENTO
    $app->get('/documento/download', 'App\Controller\DocumentoController:download')
        ->setName('root')->add(new Authentication($container));

    $app->get('/documento/downloadCertificado', 'App\Controller\CertificadosEmpresasController:downloadCertificado')
    ->setName('root')->add(new Authentication($container));

    $app->post('/documento/salvarDocumento', 'App\Controller\SalvarDocumentoController:salvarDocumento')
        ->setName('root')->add(new Authentication($container));

	//DISCRIMINAÇÃO SERVIÇOS
    $app->get('/discriminacao_servicos/receptivo', 'App\Controller\DiscriminacaoServicosController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/discriminacao_servicos/cadForm', 'App\Controller\DiscriminacaoServicosController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //ISS MUNICIPIO
    $app->get('/iss_municipio/receptivo', 'App\Controller\IssMunicipioController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/iss_municipio/cadForm', 'App\Controller\IssMunicipioController:cadForm')
        ->setName('root')->add(new Authentication($container));


        
    //EQUIPE
    $app->get('/equipe/receptivo', 'App\Controller\EquipeController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/equipe/cadForm', 'App\Controller\EquipeController:cadForm')
        ->setName('root')->add(new Authentication($container));
	//FATURAMENTO
    $app->get('/faturamento/receptivo', 'App\Controller\FaturamentoController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/faturamento/cadForm', 'App\Controller\FaturamentoController:cadForm')
        ->setName('root')->add(new Authentication($container));
    $app->get('/faturamento/faturamentoItens', 'App\Controller\FaturamentoController:faturamentoItens')
        ->setName('root')->add(new Authentication($container));
	$app->get('/faturamento/listarEmissoes', 'App\Controller\FaturamentoController:listarEmissoes')
        ->setName('root')->add(new Authentication($container));

    //EQUIPE
    $app->get('/feriado/receptivo', 'App\Controller\FeriadoController:receptivo')
        ->setName('root')->add(new Authentication($container));    
        
    //FORNECEDORES
    $app->get('/fornecedor/receptivo', 'App\Controller\FornecedorController:receptivo')
        ->setName('root')->add(new Authentication($container));
        
    $app->get('/fornecedor/cadForm', 'App\Controller\FornecedorController:cadForm')
    ->setName('root')->add(new Authentication($container));

	//ENTRADA ESTOQUE
    $app->get('/entrada_estoque/receptivo', 'App\Controller\EntradaEstoqueController:receptivo')
        ->setName('root')->add(new Authentication($container));
        
    $app->get('/entrada_estoque/cadForm', 'App\Controller\EntradaEstoqueController:cadForm')
    ->setName('root')->add(new Authentication($container));
	 //GRUPOS
    $app->get('/grupo/receptivo', 'App\Controller\GrupoController:receptivo')
        ->setName('root')->add(new Authentication($container));
        
    $app->get('/grupo/cadForm', 'App\Controller\GrupoController:cadForm')
    ->setName('root')->add(new Authentication($container));
    //IMPRESSAO
    $app->get('/impressao_material/abrirImpressao', 'App\Controller\ImpressaoMaterialController:abrirImpressao')
        ->setName('root')->add(new Authentication($container));

	//LANÇAMENTOS
    $app->get('/lancamento/usuarioFinanceiroReceptivo', 'App\Controller\LancamentoController:usuarioFinanceiroReceptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/lancamento/contasPagarReceberReceptivo', 'App\Controller\LancamentoController:contasPagarReceberReceptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/lancamento/impressaoLancamento', 'App\Controller\LancamentoController:impressaoLancamento')
        ->setName('root')->add(new Authentication($container));

    //LEAD
    $app->get('/lead/receptivo', 'App\Controller\LeadController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/lead/cadForm', 'App\Controller\LeadController:cadForm')
        ->setName('root')->add(new Authentication($container));
    $app->get('/lead/leadMainPainel', 'App\Controller\LeadController:leadMainPainel')
        ->setName('root')->add(new Authentication($container));
    $app->get('/lead/qrCode', 'App\Controller\LeadController:qrCode')
        ->setName('root')->add(new Authentication($container));

    //MÓDULOS
    $app->get('/modulo/receptivo', 'App\Controller\ModuloController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/modulo/cadForm', 'App\Controller\ModuloController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //MOVIMENTACAO ESTOQUE
    $app->get('/movimentacao_estoque/receptivo', 'App\Controller\MovimentacaoEstoqueController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/movimentacao_estoque/cadForm', 'App\Controller\MovimentacaoEstoqueController:cadForm')
        ->setName('root')->add(new Authentication($container));

    // OCORRENCIAS
    $app->get('/ocorrencia/receptivo', 'App\Controller\OcorrenciaController:receptivo')
    ->setName('root')->add(new Authentication($container));

	//PLANO CONTAS
    $app->get('/plano_contas/receptivo', 'App\Controller\PlanoContaController:receptivo')
        ->setName('root')->add(new Authentication($container));
        
    $app->get('/plano_contas/cadForm', 'App\Controller\PlanoContaController:cadForm')
    ->setName('root')->add(new Authentication($container));

	//PONTO
    $app->get('/ponto/receptivoPontoAtraso', 'App\Controller\PontoController:receptivoPontoAtraso')
        ->setName('root')->add(new Authentication($container));
    $app->get('/ponto/receptivoAcompanhamentoPontoDiario', 'App\Controller\PontoController:receptivoAcompanhamentoPontoDiario')
        ->setName('root')->add(new Authentication($container));
    //PROPOSTA FACILITIES
    $app->get('/propostas_facilities/abrirPropostaSelecao', 'App\Controller\PropostaFacilitiesController:abrirPropostaSelecao')
        ->setName('abrirPopostaSelecao')->add(new Authentication($container));
    $app->get('/propostas_facilities/abrirPropostaDetalhada', 'App\Controller\PropostaFacilitiesController:abrirPropostaDetalhada')
        ->setName('abrirPropostaDetalhada')->add(new Authentication($container));
    $app->get('/propostas_facilities/abrirImpressao', 'App\Controller\PropostaFacilitiesController:abrirImpressao')
        ->setName('abrirPropostaDetalhada')->add(new Authentication($container));
    $app->get('/propostas_facilities/receptivo', 'App\Controller\PropostaFacilitiesController:receptivo')
        ->setName('root')->add(new Authentication($container));
	   
	//PONTO FOLHA
    $app->get('/ponto_folha/receptivoPontoFolha', 'App\Controller\PontoFolhaController:receptivoPontoFolha')
        ->setName('root')->add(new Authentication($container));
    $app->get('/ponto_folha/cadForm', 'App\Controller\PontoFolhaController:cadForm')
        ->setName('root')->add(new Authentication($container));
    $app->get('/ponto_folha/registrosCad', 'App\Controller\PontoFolhaController:registrosCad')
        ->setName('root')->add(new Authentication($container));
    $app->get('/ponto_folha/colaboradoresCad', 'App\Controller\PontoFolhaController:colaboradoresCad')
        ->setName('root')->add(new Authentication($container));
	$app->get('/ponto_folha/receptivoPrint', 'App\Controller\PontoFolhaController:receptivoPrint')
        ->setName('root')->add(new Authentication($container));
	$app->get('/ponto_folha/receptivoPrintByColaboradorPeriodo', 'App\Controller\PontoFolhaController:receptivoPrintByColaboradorPeriodo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/ponto_folha/colaboradoresCadFechamento', 'App\Controller\PontoFolhaController:colaboradoresCadFechamento')
        ->setName('root')->add(new Authentication($container));
    $app->get('/ponto_folha/receptivoPrintFechamento', 'App\Controller\PontoFolhaController:receptivoPrintFechamento')
        ->setName('root')->add(new Authentication($container));
    $app->get('/ponto_folha/receptivoPontoFolhaFechamento', 'App\Controller\PontoFolhaController:receptivoPontoFolhaFechamento')
        ->setName('root')->add(new Authentication($container));
	
	
	//PROCESSO DEFAULT
    $app->get('/processo_default/receptivo', 'App\Controller\ProcessoDefaultController:receptivo')
    ->setName('root')->add(new Authentication($container));
    $app->get('/processo_default/cadForm', 'App\Controller\ProcessoDefaultController:cadForm')
    ->setName('root')->add(new Authentication($container));


	//PRODUTOS
    $app->get('/produtos/receptivo', 'App\Controller\ProdutoController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/produtos/cadForm', 'App\Controller\ProdutoController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //RELATORIOS 
     $app->get('/relatorio/comercial', 'App\Controller\RelatorioController:comercial')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqRelProposta', 'App\Controller\RelatorioController:pesqRelProposta')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoProposta', 'App\Controller\RelatorioController:receptivoProposta')
    ->setName('root')->add(new Authentication($container));
	$app->get('/relatorio/pesqRelContrato', 'App\Controller\RelatorioController:pesqRelContrato')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoContrato', 'App\Controller\RelatorioController:receptivoContrato')
    ->setName('root')->add(new Authentication($container));




    $app->get('/relatorio/compra_estoque', 'App\Controller\RelatorioController:compra_estoque')
    ->setName('root')->add(new Authentication($container));
	$app->get('/relatorio/pesqRelEstoqueSintetico', 'App\Controller\RelatorioController:pesqRelEstoqueSintetico')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoEstoqueSintetico', 'App\Controller\RelatorioController:receptivoEstoqueSintetico')
    ->setName('root')->add(new Authentication($container));
	$app->get('/relatorio/pesqRelMovimentacaoEstoque', 'App\Controller\RelatorioController:pesqRelMovimentacaoEstoque')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoMovimentacaoEstoque', 'App\Controller\RelatorioController:receptivoMovimentacaoEstoque')
    ->setName('root')->add(new Authentication($container));
	$app->get('/relatorio/pesqControleCompra', 'App\Controller\RelatorioController:pesqControleCompra')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoControleCompra', 'App\Controller\RelatorioController:receptivoControleCompra')
    ->setName('root')->add(new Authentication($container));
	$app->get('/relatorio/pesqControleSolicitacaoCompra', 'App\Controller\RelatorioController:pesqControleSolicitacaoCompra')
    ->setName('root')->add(new Authentication($container));
	$app->get('/relatorio/receptivoControleSolicitacaoCompra', 'App\Controller\RelatorioController:receptivoControleSolicitacaoCompra')
    ->setName('root')->add(new Authentication($container));
	$app->get('/relatorio/receptivoCompraMovimentacaoLead', 'App\Controller\RelatorioController:receptivoCompraMovimentacaoLead')
    ->setName('root')->add(new Authentication($container));
	$app->get('/relatorio/pesqCompraMovimentacaoLead', 'App\Controller\RelatorioController:pesqCompraMovimentacaoLead')
    ->setName('root')->add(new Authentication($container));



    $app->get('/relatorio/rh', 'App\Controller\RelatorioController:rh')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqRelDadosColaborador', 'App\Controller\RelatorioController:pesqRelDadosColaborador')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoDadosColaborador', 'App\Controller\RelatorioController:receptivoDadosColaborador')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqRelColaboradorExameCurso', 'App\Controller\RelatorioController:pesqRelColaboradorExameCurso')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoColaboradorExameCurso', 'App\Controller\RelatorioController:receptivoColaboradorExameCurso')
    ->setName('root')->add(new Authentication($container));
	$app->get('/relatorio/pesqRelAcompanhamentoFerias', 'App\Controller\RelatorioController:pesqRelAcompanhamentoFerias')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoAcompanhamentoFerias', 'App\Controller\RelatorioController:receptivoAcompanhamentoFerias')
    ->setName('root')->add(new Authentication($container));

	$app->get('/relatorio/pesqAcompanhamentoSupervisor', 'App\Controller\RelatorioController:pesqAcompanhamentoSupervisor')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoAcompanhamentoSupervisor', 'App\Controller\RelatorioController:receptivoAcompanhamentoSupervisor')
    ->setName('root')->add(new Authentication($container));


    $app->get('/relatorio/financeiro', 'App\Controller\RelatorioController:financeiro')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqRelFluxoCaixa', 'App\Controller\RelatorioController:pesqRelFluxoCaixa')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoFluxoCaixa', 'App\Controller\RelatorioController:receptivoFluxoCaixa')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqRelReceitaPostoTrabalho', 'App\Controller\RelatorioController:pesqRelReceitaPostoTrabalho')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoReceitaPostoTrabalho', 'App\Controller\RelatorioController:receptivoReceitaPostoTrabalho')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqRelDespesaPostoTrabalho', 'App\Controller\RelatorioController:pesqRelDespesaPostoTrabalho')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqRelTituloPlanoContas', 'App\Controller\RelatorioController:pesqRelTituloPlanoContas')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoPlanoContas', 'App\Controller\RelatorioController:receptivoPlanoContas')
    ->setName('root')->add(new Authentication($container));

    $app->get('/relatorio/operacional', 'App\Controller\RelatorioController:operacional')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqAcompanhamentoPontoSintetico', 'App\Controller\RelatorioController:pesqAcompanhamentoPontoSintetico')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/resAcompanhamentoPontoSintetico', 'App\Controller\RelatorioController:resAcompanhamentoPontoSintetico')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqAcompanhamentoPontoAnalitico', 'App\Controller\RelatorioController:pesqAcompanhamentoPontoAnalitico')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/resAcompanhamentoPontoAnalitico', 'App\Controller\RelatorioController:resAcompanhamentoPontoAnalitico')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqRelApontamento', 'App\Controller\RelatorioController:pesqColaboradorApontamento')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoRelApontamento', 'App\Controller\RelatorioController:resColaboradorApontamento')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqColaboradorPostoTrabalho', 'App\Controller\RelatorioController:pesqColaboradorPostoTrabalho')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoColaboradorPostoTrabalho', 'App\Controller\RelatorioController:receptivoColaboradorPostoTrabalho')
    ->setName('root')->add(new Authentication($container));
	 $app->get('/relatorio/pesqControleFts', 'App\Controller\RelatorioController:pesqControleFts')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoControleFt', 'App\Controller\RelatorioController:receptivoControleFt')
    ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/pesqRondas', 'App\Controller\RelatorioController:pesqRondas')
   ->setName('root')->add(new Authentication($container));
    $app->get('/relatorio/receptivoRondas', 'App\Controller\RelatorioController:receptivoRondas')
   ->setName('root')->add(new Authentication($container));

    $app->get('/relatorio/receptivoRondasCliente', 'App\Controller\RelatorioController:receptivoRondasCliente')
   ->setName('root')->add(new Authentication($container));


    $app->get('/relatorio/receptivoDespesaPostoTrabalho', 'App\Controller\RelatorioController:receptivoDespesaPostoTrabalho')
   ->setName('root')->add(new Authentication($container));

    $app->get('/relatorio/pesqRelContasPagarPeriodo', 'App\Controller\RelatorioController:pesqRelContasPagarPeriodo')
   ->setName('root')->add(new Authentication($container));

    $app->get('/relatorio/receptivoPlanoContasPagarPeriodo', 'App\Controller\RelatorioController:receptivoPlanoContasPagarPeriodo')
   ->setName('root')->add(new Authentication($container));

    $app->get('/relatorio/pesqAcompanhamentoBancoHoras', 'App\Controller\RelatorioController:pesqAcompanhamentoBancoHoras')
   ->setName('root')->add(new Authentication($container));

    $app->get('/relatorio/receptivoAcompanhamentoBancoHoras', 'App\Controller\RelatorioController:receptivoAcompanhamentoBancoHoras')
   ->setName('root')->add(new Authentication($container));
    
   $app->get('/relatorio/receptivoAcompanhamentoFalta', 'App\Controller\RelatorioController:receptivoAcompanhamentoFalta')
   ->setName('root')->add(new Authentication($container));

    $app->get('/relatorio/pesqAcompanhamentoFalta', 'App\Controller\RelatorioController:pesqAcompanhamentoFalta')
   ->setName('root')->add(new Authentication($container));

    $app->get('/relatorio/receptivoFechamento', 'App\Controller\RelatorioController:receptivoFechamento')
   ->setName('root')->add(new Authentication($container));

    $app->get('/relatorio/pesqFechamento', 'App\Controller\RelatorioController:pesqFechamento')
   ->setName('root')->add(new Authentication($container));


    $app->get('/relatorio/pesqStatusColaborador', 'App\Controller\RelatorioController:pesqStatusColaborador')
   ->setName('root')->add(new Authentication($container));

    $app->get('/relatorio/receptivoStatusColaborador', 'App\Controller\RelatorioController:receptivoStatusColaborador')
   ->setName('root')->add(new Authentication($container));



	//SERVICO
    $app->get('/servico/receptivo', 'App\Controller\ServicoController:receptivo')
        ->setName('root')->add(new Authentication($container));
    $app->get('/servico/cadForm', 'App\Controller\ServicoController:cadForm')
        ->setName('root')->add(new Authentication($container));

    //SOLICITAÇÃO ACESSO APP PONTO
    $app->get('/solicitacao_acesso_app/receptivo', 'App\Controller\SolicitacaoAcessoAppController:receptivo')
        ->setName('root')->add(new Authentication($container));

    //SUPERVISÃO
    $app->get('/supervisao_auditoria_lead/receptivo', 'App\Controller\SupervisaoAuditoriaLeadController:receptivo')
    ->setName('root')->add(new Authentication($container));
    
	
	//TETO DE GASTOS
    $app->get('/teto_gasto/receptivo', 'App\Controller\TetoGastoController:receptivo')
    ->setName('root')->add(new Authentication($container));
    $app->get('/teto_gasto/cadForm', 'App\Controller\TetoGastoController:cadForm')
    ->setName('root')->add(new Authentication($container));
    

	//TIPO OCORRENCIAS
    $app->get('/tipo_ocorrencia/receptivo', 'App\Controller\TipoOcorrenciaController:receptivo')
    ->setName('root')->add(new Authentication($container));
    $app->get('/tipo_ocorrencia/cadForm', 'App\Controller\TipoOcorrenciaController:cadForm')
    ->setName('root')->add(new Authentication($container));
    
    //USUARIO
    $app->get('/usuario/receptivo', 'App\Controller\UsuarioController:receptivo')
    ->setName('root')->add(new Authentication($container));
    $app->get('/usuario/cadForm', 'App\Controller\UsuarioController:cadForm')
    ->setName('root')->add(new Authentication($container));

    $app->get('/usuarios/edit/{pk}', 'App\Controller\UsuarioController:edit')
    ->setName('root')->add(new Authentication($container));

    //LOGIN

    $app->get('/login', 'App\Controller\LoginController:login')
        ->setName('login');      



    //API APLICATIVO
    //DOCUMENTOS
    $app->post('/getDocumentosApp', 'App\Controller\DocumentoController:getDocumentosApp')
    ->setName('root');
    $app->post('/getAssinaturaColaborador', 'App\Controller\DocumentoController:getAssinaturaColaborador')
    ->setName('root');
    $app->post('/salvarAssinaturaColaborador', 'App\Controller\DocumentoController:salvarAssinaturaColaborador')
    ->setName('root');
    $app->post('/getDocumentoByIdApp', 'App\Controller\DocumentoController:getDocumentoByIdApp')
    ->setName('root');
    $app->post('/assinarDocumentoApp', 'App\Controller\DocumentoController:assinarDocumentoApp')
    ->setName('root');

    //PONTO
    $app->post('/registrarPontoApp', 'App\Controller\WebPontoApiController:registraPontoApp')
    ->setName('root');
    $app->post('/sincronizarPontoApp', 'App\Controller\WebPontoApiController:sincronizarPontoApp')
    ->setName('root');
    $app->post('/listarPostoTrabalhoApp', 'App\Controller\WebPontoApiController:listarPostoTrabalhoApp')
    ->setName('root');
    $app->post('/pesquisarPonto', 'App\Controller\WebPontoApiController:pesquisarPonto')
    ->setName('root');


    //WHATSAPP
    $app->post('/whatsAppWebPonto', 'App\Controller\WebPontoWhatsAppController:webPontoWhatsApp')
    ->setName('root');


    //ROTA PARA DIMINUIR O TAMANHO DA IMAGEM DA TABELA PONTO
    $app->post('/testeXml', 'App\Controller\AgendaColaboradorPadraoController:testeXml')
    ->setName('root');

    
