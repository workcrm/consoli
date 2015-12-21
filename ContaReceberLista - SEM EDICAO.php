<?php
###########
## Módulo para Listagem das Contas a Receber
## Criado: 07/06/2007 - Maycon Edinger
## Alterado: 14/08/2007 - Maycon Edinger
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Desativar o CSS redundante
//<link rel='stylesheet' type='text/css' href='include/workStyle.css'>

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

//Inclui o arquivo para manipulação de valor monetário
include './include/ManipulaMoney.php';

//Pega os valores padrão que vem do formulario
$dataIni = DataMySQLInserir($_GET[DataIni]);
$dataFim = DataMySQLInserir($_GET[DataFim]);

//Captura os parâmetros das colunas a exibir
$chkParticipante = $_GET['Participante'];
$chkDataVencimento = $_GET['DataVencimento'];
$chkValorContrato = $_GET['ValorContrato'];
$chkValorBoleto = $_GET['ValorBoleto'];
$chkValorMultaJuros = $_GET['ValorMultaJuros'];
$chkValorReceber = $_GET['ValorReceber'];
$chkValorRecebido = $_GET['ValorRecebido'];
$chkSaldoReceber = $_GET['SaldoReceber'];

if ($chkParticipante == 1)
{

	if ($chkDataVencimento == 1)
	{
  
		$colspan = '4';
  
	} 
	else
	{
    
		$colspan = '3';
	}

}
else
{
  
	if ($chkDataVencimento == 1)
	{
  
		$colspan = '3';
  
	} 
	else
	{
    
		$colspan = '2';
	}
  
}
  
//Verifica a situação informada
if ($_GET['TipoSituacao'] > 0 AND $_GET['TipoSituacao'] != 4)
{
	
	//Efetua o switch da situacao informada
	switch ($_GET['TipoSituacao']) 
	{
		//Se for 1 então é visualização a vencer
		case 1:
			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>A Vencer</span>";
		break;		
		//Se for 2 então é visualização das recebidas
		case 2:
			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Recebidas</span>";
		break;
		//Se for 3 então é visualização das vencidas
		case 3:
			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Vencidas</span>";
		break;
	}	
		
	$TipoSituacao = $_GET['TipoSituacao'];
	
	//Verifica se a situacao está vencida
	if ($TipoSituacao == 3)
	{
    
		$hoje = date('Y-m-d', mktime());
    
		$TextoSituacao = " AND rec.situacao = 1 AND rec.data_vencimento < '$hoje'";
  
	}
  
	else
  
	{
  
		$TextoSituacao = " AND rec.situacao = '$TipoSituacao'";
	
	}  
	
} 

//Verifica se foi informado alguma data para filtrar junto
if ($dataIni != 0) {
	$TextoFiltraData = "</br><b>E com data de vencimento entre: </b><span style='color: #990000'>$_GET[DataIni]</span><b> a </b><span style='color: #990000'>$_GET[DataFim]</span>";
	$TextoSQLData = "	 AND rec.data_vencimento >= '$dataIni' AND rec.data_vencimento <= '$dataFim' ";
}


//Recebe os valores vindos do formulário
//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) 
{
	//Se for 1 então é visualização por data
	case 1: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por data de vencimento'; 

		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber com data de vencimento entre: </b><span style='color: #990000'>$_GET[DataIni]</span><b> a </b><span style='color: #990000'>$_GET[DataFim]</span> $texto_situacao";

		//Monta o sql
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.valor_recebido,
				rec.boleto_id,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				WHERE rec.empresa_id = '$empresaId' AND rec.data_vencimento >= '$dataIni' AND rec.data_vencimento <= '$dataFim' $TextoSituacao 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";

		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há contas a Receber entre as datas informadas';
	break;
  
	//Se for 2 então é visualização por centro de custo
	case 2: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por Centro de Custo'; 
		$grupoId = $_GET[GrupoId];		
		
		//Recupera o nome do grupo selecionado
		$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = '$grupoId'");
		
		//Monta o array com os dados
		$dados_grupo = mysql_fetch_array($sql_grupo);
				
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do centro de custo: </b><span style='color: #990000'>$dados_grupo[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.valor_recebido,
				rec.boleto_id,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				WHERE rec.empresa_id = $empresaId AND rec.grupo_conta_id = '$grupoId' $TextoSQLData $TextoSituacao 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber para o centro de custo <span style='color: #990000'>$dados_grupo[nome]</span>";
	break;

	//Se for 3 então é visualização por evento
	case 3: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por Evento'; 
		$eventoId = $_GET[EventoId];
		
		//Recupera o nome da categoria selecionada
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do evento: </b><span style='color: #990000'>$dados_evento[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.valor_recebido,
				rec.boleto_id,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				WHERE rec.empresa_id = '$empresaId' AND rec.evento_id = '$eventoId' $TextoSQLData $TextoSituacao 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber para o evento <span style='color: #990000'>$dados_evento[nome]</span>";
	break;
	
	//Se for 4 então é visualização por situacao
	case 4: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por Situação'; 
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
			//Se for 1 então é visualização A Vencer
			case 1:
				$texto_situacao = 'A Vencer';
				$where_situacao = "rec.situacao = '$TipoSituacao'";
			break;		
			//Se for 2 então é visualização das recebidas
			case 2:
				$texto_situacao = 'Recebidas';
				$where_situacao = "rec.situacao = '$TipoSituacao'";
			break;
			//Se for 3 então é visualização das vencidas
			case 3:
				$texto_situacao = 'Vencidas';
				$data_base_vencimento = date('Y-m-d', mktime());
				$where_situacao = "rec.situacao = '1' AND rec.data_vencimento < '$data_base_vencimento'";
			break;
		}				
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber com situação: </b><span style='color: #990000'>$texto_situacao</span>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.valor_recebido,
				rec.boleto_id,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				WHERE rec.empresa_id = $empresaId AND $where_situacao $TextoSQLData 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber com a situação <span style='color: #990000'>$texto_situacao</span>";
	break;
	
	//Se for 5 então é visualização por sacado
	case 5: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por Sacado'; 
		$TipoPessoa = $_GET[TipoPessoa];
		$PessoaId = $_GET[PessoaId];
		
		//Efetua o switch da pessoa informada
		switch ($TipoPessoa) 
		{
			//Se for 1 então é cliente
			case 1:
				//Recupera o nome do cliente
				$query_cliente = mysql_query("SELECT nome FROM clientes WHERE id = '$PessoaId'");
				$nome_cliente = mysql_fetch_array($query_cliente);			
				$texto_pessoa = "<b>Cliente:</b> <span style='color: #990000'>$nome_cliente[nome]</span>";
			break;		
			//Se for 2 então é fornecedor
			case 2:
				//Recupera o nome do fornecedor
				$query_fornecedor = mysql_query("SELECT nome FROM fornecedores WHERE id = '$PessoaId'");
				$nome_fornecedor = mysql_fetch_array($query_fornecedor);
				$texto_pessoa = "<b>Fornecedor:</b> <span style='color: #990000'>$nome_fornecedor[nome]</span>";
			break;
			//Se for 3 então é colaborador
			case 3:
				//Recupera o nome do colaborador
				$query_colaborador = mysql_query("SELECT nome FROM colaboradores WHERE id = '$PessoaId'");
				$nome_colaborador = mysql_fetch_array($query_colaborador);
				$texto_pessoa = "<b>Colaborador:</b> <span style='color: #990000'>$nome_colaborador[nome]</span>";
			break;
		}				
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do </b><span style='color: #990000'>$texto_pessoa</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.boleto_id,
				rec.valor_recebido,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				WHERE rec.empresa_id = $empresaId AND rec.tipo_pessoa = '$TipoPessoa' AND rec.pessoa_id = '$PessoaId' $TextoSQLData $TextoSituacao 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber do <span style='color: #990000'>$texto_pessoa</span>";
	break;
	
	//Se for 6 então é visualização por conta-caixa
	case 6: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por Conta-caixa'; 
		$subgrupoId = $_GET[SubgrupoId];		
		
		//Recupera o nome do subgrupo selecionado
		$sql_subgrupo = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$subgrupoId'");
		
		//Monta o array com os dados
		$dados_subgrupo = mysql_fetch_array($sql_subgrupo);
				
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber da conta-caixa: </b><span style='color: #990000'>$dados_subgrupo[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.valor_recebido,
				rec.boleto_id,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				WHERE rec.empresa_id = $empresaId AND rec.subgrupo_conta_id = '$subgrupoId' $TextoSQLData $TextoSituacao 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber para a conta-caixa <span style='color: #990000'>$dados_subgrupo[nome]</span>";
	break;			
	
	//Se for 7 então é visualização agrupada
	case 7: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por Conta-caixa e Centro de Custo'; 
		$grupoId = $_GET[GrupoId];
		$subgrupoId = $_GET[SubgrupoId];		
		
		//Recupera o nome do grupo selecionado
		$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = '$grupoId'");
		
		//Monta o array com os dados
		$dados_grupo = mysql_fetch_array($sql_grupo);
		
		//Recupera o nome do subgrupo selecionado
		$sql_subgrupo = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$subgrupoId'");
		
		//Monta o array com os dados
		$dados_subgrupo = mysql_fetch_array($sql_subgrupo);
						
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber da conta-caixa: </b><span style='color: #990000'>$dados_subgrupo[nome]</span><br>
											 <b>e do centro de custo: </b><span style='color: #990000'>$dados_grupo[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.valor_recebido,
				rec.boleto_id,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				WHERE rec.empresa_id = $empresaId AND rec.grupo_conta_id = '$grupoId' AND rec.subgrupo_conta_id = '$subgrupoId' $TextoSQLData $TextoSituacao 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";

		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há contas a Receber para este agrupamento !';
	break;
  
 	//Se for 8 então é visualização por evento e formando
	case 8: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por Evento e Formando'; 
		$eventoId = $_GET[EventoId];
		$formandoId = $_GET[FormandoId];
		
		//Recupera o nome da categoria selecionada
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
    
		//Recupera o nome do formando
		$sql_formando = mysql_query("SELECT nome FROM eventos_formando WHERE id = '$formandoId'");
		
		//Monta o array com os dados
		$dados_formando = mysql_fetch_array($sql_formando);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do evento: </b><span style='color: #990000'>$dados_evento[nome]</span><br/><strong>E do formando: </strong><span style='color: #990000'>$dados_formando[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.valor_recebido,
				rec.boleto_id,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				WHERE rec.empresa_id = '$empresaId' AND rec.evento_id = '$eventoId' AND rec.formando_id = '$formandoId' $TextoSQLData $TextoSituacao 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há contas a Receber para este evento e formando';
	break;
  
	//Se for 9 então é visualização por evento e conta-caixa
	case 9: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por Evento e Conta-Caixa'; 
		$eventoId = $_GET[EventoId];
		$contaCaixaId = $_GET[ContaCaixaId];
		
		//Recupera o nome da categoria selecionada
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
    
		//Recupera o nome do formando
		$sql_contacaixa = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$contaCaixaId'");
		
		//Monta o array com os dados
		$dados_contacaixa = mysql_fetch_array($sql_contacaixa);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do evento: </b><span style='color: #990000'>$dados_evento[nome]</span><br/><strong>E da Conta-Caixa: </strong><span style='color: #990000'>$dados_contacaixa[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.valor_recebido,
				rec.boleto_id,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				WHERE rec.empresa_id = '$empresaId' 
				AND rec.evento_id = '$eventoId' 
				AND rec.subgrupo_conta_id = '$contaCaixaId' 
				$TextoSQLData $TextoSituacao 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";            
		
		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há contas a Receber para este evento e conta-caixa';
	break;

	//Se for 10 então é visualização por evento e curso
	case 10: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por Evento e Curso'; 
		$eventoId = $_GET[EventoId];
		$cursoId = $_GET[CursoId];
		
		//Recupera o nome do evento
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
    
		//Recupera o nome do formando
		$sql_curso = mysql_query("SELECT nome FROM cursos WHERE id = '$cursoId'");
		
		//Monta o array com os dados
		$dados_curso = mysql_fetch_array($sql_curso);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do evento: </b><span style='color: #990000'>$dados_evento[nome]</span><br/><strong>E do curso: </strong><span style='color: #990000'>$dados_curso[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.valor_recebido,
				rec.boleto_id,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				LEFT OUTER JOIN eventos_formando forma ON forma.id = rec.formando_id
				WHERE rec.empresa_id = '$empresaId' 
				AND rec.evento_id = '$eventoId' 
				AND forma.curso_id = '$cursoId' $TextoSQLData $TextoSituacao 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há contas a Receber para este evento e curso';
	break;	
	
	//Se for 11 então é visualização por evento, formando e conta-caixa
	case 11: 
		//Monta o título da página
		$titulo = 'Relação de Contas a Receber por Evento, Formando e Conta-Caixa'; 
		$eventoId = $_GET[EventoId];
		$formandoId = $_GET[FormandoId];
		$contaCaixaId = $_GET[ContaCaixaId];
		
		//Recupera o nome da categoria selecionada
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
    
		//Recupera o nome do formando
		$sql_formando = mysql_query("SELECT nome FROM eventos_formando WHERE id = '$formandoId'");
		
		//Monta o array com os dados
		$dados_formando = mysql_fetch_array($sql_formando);
		
		//Recupera o nome da conta-caixa
		$sql_conta_caixa = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$contaCaixaId'");
		
		//Monta o array com os dados
		$dados_conta_caixa = mysql_fetch_array($sql_conta_caixa);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do evento: </b><span style='color: #990000'>$dados_evento[nome]</span><br/><strong>E do formando: </strong><span style='color: #990000'>$dados_formando[nome]</span><br/><strong>E da Conta-Caixa: </strong><span style='color: #990000'>$dados_conta_caixa[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				rec.id,
				rec.data,
				rec.valor_original,
				rec.valor,
				rec.valor_recebido,
				rec.valor_boleto,
				rec.valor_multa_juros,
				rec.tipo_pessoa,
				rec.pessoa_id,
				rec.data_vencimento,
				rec.descricao,
				rec.situacao,
				rec.restricao,
				rec.origem_conta,
				rec.valor_recebido,
				rec.boleto_id,
				eve.nome as evento_nome
				FROM contas_receber rec
				LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
				WHERE rec.empresa_id = '$empresaId' 
				AND rec.evento_id = '$eventoId' 
				AND rec.formando_id = '$formandoId' 
				AND rec.subgrupo_conta_id = '$contaCaixaId'
				$TextoSQLData $TextoSituacao 
				ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há contas a Receber para este evento e formando';
	break;

}
			
//Executa a Query
$query = mysql_query($sql);		  	  
//verifica o número total de registros
$tot_regs = mysql_num_rows($query);	    

//Gera a variável com o total de contas a Receber
$total_receber = 0;
$saldo_receber = 0;	  

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form id="form" name="cadastro" action="sistema.php?ModuloNome=ContaReceberListaAltera" method="post">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo"><?php echo $titulo ?></span>			  	
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">			    	
						<?php echo $desc_filtragem ?>
						<br/><br/>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="PADDING-BOTTOM: 2px">
			<input class="button" title="Retorna ao Módulo de Contas a Receber" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Módulo de Contas a Receber" onclick="wdCarregarFormulario('ModuloContasReceber.php','conteudo')" />	
		</td>		
	</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>	   
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
				<?php

					//Caso houverem registros
					if ($tot_regs > 0) 
					{
				  
						//Cria a data do vencimento para comparar se a conta está vencida
						$data_base_vencimento = date("Y-m-d", mktime());
					  
						$imprime_cabecalho = 0;              
					   
						//Cria a variavel zerada para o contador de checkboxes
						$edtItemChk = 0;
								 
					}
				  
					//Caso não houverem registros
					if ($tot_regs == 0) 
					{ 

					//Exibe uma linha dizendo que nao há registros
					echo "<tr height='24'>
					  		<td colspan='10' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
					  			<slot><font color='#33485C'><strong>$texto_vazio</strong></font></slot>
							</td>
						</tr>";
      	  
		} 

		else 
		{
	   
			//Armazena o valor da situação sendo listada primeiro
			$situacao = 1;
    
			$total_vencido_original = 0;
			$total_aberto_original = 0;    
			$total_geral_original = 0;
			
			$total_vencido_boletos = 0;
			$total_aberto_boletos = 0;    
			$total_geral_boletos = 0;
			
			$total_vencido_multa_juros = 0;
			$total_aberto_multa_juros = 0;    
			$total_geral_multa_juros = 0;
			
			$total_vencidos_receber = 0;
			$total_aberto_receber = 0;
			$total_geral_receber = 0;
			
			$total_vencidos_recebido = 0;
			$total_aberto_recebido = 0;
			$total_geral_recebido = 0;
			
			$saldo_vencidos_receber = 0;
			$saldo_aberto_receber = 0;
			$saldo_geral_receber = 0;
						
			$mostra_topo_vencidas = 0;
    		  
			//Cria o array e o percorre para montar a listagem dinamicamente
			while ($dados_rec = mysql_fetch_array($query))
			{

				//Efetua o switch para recuperar o nome do sacado
				switch ($dados_rec[tipo_pessoa]) 
				{
				
					case 1: //Se for cliente
						$sql = mysql_query("SELECT nome FROM clientes WHERE id = '$dados_rec[pessoa_id]'");
						$desc_pessoa = "Cliente";
						$dados_pessoa = mysql_fetch_array($sql);

						$desc_participante = "&nbsp;";
						$edtValorCapital = "0,00";
						$edtValorCapitalSoma = 0;				
					break;
					case 2: //Se for fornecedor
						$sql = mysql_query("SELECT nome FROM fornecedores WHERE id = '$dados_rec[pessoa_id]'");
						$desc_pessoa = "Fornecedor";
						$dados_pessoa = mysql_fetch_array($sql);

						$desc_participante = "&nbsp;";
						$edtValorCapital = "0,00";
						$edtValorCapitalSoma = 0;				
					break;
					case 3: //Se for colaborador
						$sql = mysql_query("SELECT nome FROM colaboradores WHERE id = '$dados_rec[pessoa_id]'");
						$desc_pessoa = "Colaborador";
						$dados_pessoa = mysql_fetch_array($sql);

						$desc_participante = "&nbsp;";
						$edtValorCapital = "0,00";
						$edtValorCapitalSoma = 0;				
					break;
					case 4: //Se for formando
						$sql = mysql_query("SELECT nome, participante, chk_culto, chk_colacao, chk_jantar, chk_baile, valor_capital FROM eventos_formando WHERE id = '$dados_rec[pessoa_id]'");
						$desc_pessoa = "Formando";
						$dados_pessoa = mysql_fetch_array($sql);
        
						$desc_participante = "";
					
						if ($dados_pessoa["chk_culto"] == 1)
						{
						
							$desc_participante .= "<span title='Formando Participa do Culto'>M</span>&nbsp;";
							
						}
						
						if ($dados_pessoa["chk_colacao"] == 1)
						{
						
							$desc_participante .= "<span title='Formando Participa da Colação'>C</span>&nbsp;";
							
						}
						
						if ($dados_pessoa["chk_jantar"] == 1)
						{
						
							$desc_participante .= "<span title='Formando Participa do Jantar'>J</span>&nbsp;";
							
						}
						
						if ($dados_pessoa["chk_baile"] == 1)
						{
						
							$desc_participante .= "<span title='Formando Participa do Baile'>B</span>";
							
						}
        
						$edtValorCapital = number_format($dados_pessoa[valor_capital], 2, ",", ".");
						$edtValorCapitalSoma = $dados_pessoa[valor_capital];
        				
					break;
					case 5: //Se for por evento
						$sql = mysql_query("SELECT nome FROM eventos WHERE id = '$dados_rec[pessoa_id]'");
						$desc_pessoa = "Evento";
						$dados_pessoa = mysql_fetch_array($sql);

						$desc_participante = "&nbsp;";
						$edtValorCapital = "0,00";
						$edtValorCapitalSoma = 0;				
					break;			
				}		

		//Efetua o switch para o campo de situacao
		switch ($dados_rec[situacao]) 
		{
			case 1: 
        
				if ($dados_rec["data_vencimento"] < $data_base_vencimento)
				{
			
					$desc_situacao = "<span style='color: #990000'>[Vencida]</span>";
			
				}
				
				else
				
				{
								
					$desc_situacao = "<span style='color: #990000'>[A Vencer]</span>";
			  
				} 
			
			break;
			case 2: 
				$desc_situacao = "<span style='color: blue'>[Recebida]</span>"; 
			break;
		
		}
		
		//Se o formando estiver com restricoes financeiras, muda a cor da celula
		if ($dados_rec["restricao"] == 2)
		{
		
			$cor_celula = "#F0D9D9";
			
		}
		
		else
		
		{
		
			$cor_celula = "#FFFFFF";
			
		}
    
    //Verifica se a situacao da primeira está vencida
    if ($dados_rec["situacao"] == 1 AND $dados_rec["data_vencimento"] < $data_base_vencimento)
    {
    
      if ($mostra_topo_vencidas == 0)
      {
      
        echo "   
              </tr>
                </table>
                <br/>
                <span class='TituloModulo'>Contas Vencidas</span>
                <br/>
                <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
                  <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
                    <td width='20'>&nbsp;</td>
          	      	<td style='border-right: 1px dotted'>&nbsp;&nbsp;Sacado/Descrição da Conta a Receber</td>";
                    
                    if ($chkParticipante == 1)
                    {
                      
                      echo "<td style='border-right: 1px dotted' width='22' align='center'>P</td>";
                      
                    }
                    
                    
                    if ($chkDataVencimento == 1)
                    {
                    
                      echo "<td style='border-right: 1px dotted' width='86' align='center'>Data de<br/>Vencimento</td>";
                      
                    }
                    
                    if ($chkValorContrato == 1)
                    {
                      
                      echo "<td style='border-right: 1px dotted' width='66' align='center'>Valor do<br/>Contrato</td>";
                    
                    }
                    
                    if ($chkValorBoleto == 1)
                    {
                      
                      echo "<td style='border-right: 1px dotted' width='60' align='center'>Custo do<br/>Boleto</td>";
                      
                    }
                    
                    if ($chkValorMultaJuros == 1)
                    {
                      
                      echo "<td style='border-right: 1px dotted' width='60' align='center'>Multa<br/>e Juros</td>";
                      
                    }
                    
                    if ($chkValorReceber == 1)
                    {
                      
                      echo "<td style='border-right: 1px dotted' width='66' align='center'>Valor a<br/>Receber</td>";
                      
                    }
                    
                    if ($chkValorRecebido == 1)
                    {
                      
                      echo "<td style='border-right: 1px dotted' width='62' align='center'>Valor<br/>Recebido</td>";
                      
                    }
                    
                    if ($chkSaldoReceber == 1)
                    {
                      
                      echo "<td width='62' align='center'>Saldo a<br/>Receber</td>";
                      
                    } 
                     
                    echo "</tr>";      
    
          $mostra_topo_vencidas = 1;
          
        }
    
      $imprime_cabecalho = 1;
      
      $imprime_rodape_vencidas = 1;
    
    } 
    
    else
    
    {
      
      $imprime_cabecalho = 0;
    
    }
    
    
    //Verifica se não está vendo somente as recebidas
    if ($TipoSituacao == 2)
    {
     
     //Não exibe o topo das aberto
     $imprime_cabecalho = 1;
     
     $imprime_rodape = 1; 
      
    }
    
 	  if ($imprime_cabecalho == 0 AND $imprime_cabecalho_umavez == 0)
    {
      
      
      if ($imprime_rodape_vencidas == 1)
      {
        
      ?>
      
      <tr height="16">
                
        <td colspan="<?php echo $colspan ?>" height="20" align="right" style="border-right: 1px dotted"><strong>Sub-Total (Vencidas):&nbsp;&nbsp;</strong></td>
        
        <?php
        
        if ($chkValorContrato == 1)
        {
         
        ?>             
        <td bgcolor="#F0D9D9" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_vencido_original, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkValorBoleto == 1)
        {
        
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_vencido_boletos, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkValorMultaJuros == 1)
        {
          
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_vencido_multa_juros, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkValorReceber == 1)
        {
        ?>
        <td bgcolor="#FFFFCD" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_vencido_receber, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkValorRecebido == 1)
        {                     
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_vencido_recebido, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        if ($chkSaldoReceber == 1)
        {
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center">
          <span style="color: #990000"><?php echo number_format($saldo_vencido_receber, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        					      
      }
      
      $mostra_sub_aberto = 1;
                                 
      echo "
      </tr>
        </table>
        <br/>
        <span class='TituloModulo'>Contas A Vencer</span>
        <br/>
        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>    
          <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
            <td width='20'>&nbsp;</td>
  	      	<td style='border-right: 1px dotted'>&nbsp;&nbsp;Sacado/Descrição da Conta a Receber</td>";
            
            if ($chkParticipante == 1)
            {
            
              echo "<td style='border-right: 1px dotted' width='22' align='center'>P</td>";
            
            }
            
            if ($chkDataVencimento == 1)
            {
                    
              echo "<td style='border-right: 1px dotted' width='86' align='center'>Data de<br/>Vencimento</td>";
               
            } 
            
            if ($chkValorContrato == 1)
            {     
            
              echo "<td style='border-right: 1px dotted' width='66' align='center'>Valor do<br/>Contrato</td>";
            
            }
            
            if ($chkValorBoleto == 1)
            {
              
              echo "<td style='border-right: 1px dotted' width='60' align='center'>Custo do<br/>Boleto</td>";
              
            }
            
            if ($chkValorMultaJuros == 1)
            {
              
              echo "<td style='border-right: 1px dotted' width='60' align='center'>Multa<br/>e Juros</td>";
              
            }
            
            if ($chkValorReceber == 1)
            {
              
              echo "<td style='border-right: 1px dotted' width='66' align='center'>Valor a<br/>Receber</td>";
              
            }
            
            if ($chkValorRecebido == 1)
            {
              
              echo "<td style='border-right: 1px dotted' width='62' align='center'>Valor<br/>Recebido</td>";
              
            }
            
            if ($chkSaldoReceber == 1)
            {
              
              echo "<td width='62' align='center'>Saldo a<br/>Receber</td>";
              
            }
            
            
            echo "</tr>";
      
      $imprime_cabecalho_umavez = 1;
      
      $total_aberto_original = 0;    
      $total_aberto_boletos = 0;    
      $total_aberto_multa_juros = 0;    
      $total_aberto_receber = 0;
      $total_aberto_recebido = 0;
      $saldo_aberto_receber = 0;
    
    }
               

    if ($situacao != $dados_rec["situacao"])
    {
            
     if ($imprime_rodape != 1)
     {
     ?>
     
     <tr height="16">
        <td colspan="<?php echo $colspan ?>" height="20" align="right" style="border-right: 1px dotted"><strong>Sub-Total (A Vencer):&nbsp;&nbsp;</strong></td>
        
        <?php
        if ($chkValorContrato == 1)
        {
        
        ?>
        
        <td bgcolor="#F0D9D9" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_aberto_original, 2, ",", ".") ?></span>
        </td>
        
        <?php
        }
        
        if ($chkValorBoleto == 1)
        {
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_aberto_boletos, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkValorMultaJuros == 1)
        {
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_aberto_multa_juros, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkValorReceber == 1)
        {
        ?>
        <td bgcolor="#FFFFCD" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_aberto_receber, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkValorRecebido == 1)
        {
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_aberto_recebido, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkSaldoReceber == 1)
        {
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center">
          <span style="color: #990000"><?php echo number_format($saldo_aberto_receber, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        ?>					
  	</tr>
    </table>
    
    <?php
    
      $mostra_sub_aberto = 0;
    
      }
      
      
      $mostra_sub_recebidas = 1;
    ?>
    <br/>
    <span class="TituloModulo">Contas Recebidas</span>
    <br/>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
       <tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">
          <td width="20">&nbsp;</td>
	      	<td style="border-right: 1px dotted">&nbsp;&nbsp;Sacado/Descrição da Conta a Receber</td>
          
          <?php
          
          if ($chkParticipante == 1)
          { 
              
            echo "<td style='border-right: 1px dotted' width='22' align='center'>P</td>";
            
          }
          
          if ($chkDataVencimento == 1)
          {
          
            echo "<td style='border-right: 1px dotted' width='86' align='center'>Data de<br/>Vencimento</td>";
          
          }
          
          if ($chkValorContrato == 1)
          {          
          
            echo "<td style='border-right: 1px dotted' width='66' align='center'>Valor do<br/>Contrato</td>";
          
          }
          
          if ($chkValorBoleto == 1)
          {
            
            echo "<td style='border-right: 1px dotted' width='60' align='center'>Custo do<br/>Boleto</td>";
            
          }
          
          if ($chkValorMultaJuros == 1)
          {
           
            echo "<td style='border-right: 1px dotted' width='60' align='center'>Multa<br/>e Juros</td>";
            
          }
          
          if ($chkValorReceber == 1)
          {
            
            echo "<td style='border-right: 1px dotted' width='66' align='center'>Valor a<br/>Receber</td>";
            
          }
          
          if ($chkValorRecebido == 1)
          {
            
            echo "<td style='border-right: 1px dotted' width='62' align='center'>Valor<br/>Recebido</td>";
            
          }
          
          if ($chkSaldoReceber == 1)
          {
            
            echo "<td width='62' align='center'>Saldo a<br/>Receber</td>";
            
          }
          
          ?>                    
                                               
        </tr>
     <?php
     
     $situacao = $dados_rec["situacao"];
     
     $total_recebido_original = 0;
     $total_recebido_boletos = 0;
     $total_recebido_multa_juros = 0;
     $total_recebido_receber = 0;
     $total_recebido_recebido = 0;
     $saldo_recebido_receber = 0;
      
    }
    
    ?>

	  <tr height="16">
      <td bgcolor="<?php echo $cor_celula ?>" style="border-bottom: 1px solid" height="20">
        <input name="edtContaId<?php echo $edtItemChk ?>" type="hidden" value="<?php echo $dados_rec[id] ?>" />
        <input name="edtBoletoId<?php echo $edtItemChk ?>" type="hidden" value="<?php echo $dados_rec[boleto_id] ?>" />
      </td>
      <td bgcolor="<?php echo $cor_celula ?>" style="border-bottom: 1px solid; border-right: 1px dotted; padding-bottom: 2px" height="20">        
			<font color="#CC3300" size="2" face="Tahoma">
				<a title="Clique para exibir esta conta a Receber" href="#" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $dados_rec[id] ?>','conteudo')">&nbsp;<?php echo $dados_pessoa[nome]; ?></a>
			</font>				
			<br/>
			<span style="font-size: 9px">&nbsp;<?php echo $dados_rec["descricao"] ?>
			<br/>
			<?php 
				
				if ($dados_rec["origem_conta"] == 2)
				{
						
					echo "<span style='color: #990000'>&nbsp;<b>$dados_rec[evento_nome]</b></span>&nbsp;&nbsp;EM " . DataMySQLRetornar($dados_rec[data]);
					
				} 
				
				else 
				
				{
					
					if ($dados_rec[boleto_id] > 0) 
					{
					
						echo "&nbsp;<b>Gerada via Contas a Receber&nbsp;<span style='color: #990000'>(VIA BOLETO)</span></b>&nbsp;&nbsp;EM " . DataMySQLRetornar($dados_rec[data]);
              
					}
            
					else
            
					{
              
						echo "&nbsp;<b>Gerada via Contas a Receber</b>&nbsp;&nbsp;EM " . DataMySQLRetornar($dados_rec[data]);
              
					}
            
				}
				
			?>
			</span>
			<br/>
			&nbsp;<strong><?php echo $desc_situacao ?></strong>
			<?php 
					
				if ($dados_rec[boleto_id] > 0) 
				{
					 
					echo "<input class='button' style='width: 80px; height: 16px' title='Visualizar Boleto' name='btnBoleto' type='button' id='btnBoleto' value='Exibir Boleto' onclick='abreJanelaBoleto(\"./boletos/boleto_bb.php?TipoBol=1&BoletoId=$dados_rec[boleto_id]&EmpresaId=$empresaId&EmpresaNome=$empresaNome\")' style='cursor: pointer' />&nbsp;";					

					if ($dados_rec[situacao] == 1) 
					{
  
						echo "<input class='button' style='width: 90px; height: 16px' title='Receber esta boleto e quita a conta' name='btnRecebeBoleto' type='button' id='btnRecebeBoleto' value='Baixar Boleto' onclick='wdCarregarFormulario(\"BoletoQuita.php?BoletoId=$dados_rec[boleto_id]&headers=1\",\"conteudo\")' style='cursor: pointer' />";					
  
  					}

				}
          
				else
				
				{

  					if ($dados_rec[situacao] == 1) 
					{
  
						echo "<input class='button' style='width: 80px; height: 16px' title='Receber esta conta' name='btnRecebe' type='button' id='btnRecebe' value='Receber Conta' onclick='wdCarregarFormulario(\"ContaReceberQuita.php?ContaId=$dados_rec[id]&headers=1\",\"conteudo\")' style='cursor: pointer' />";					
  
  					}
          
				}
			?>      
			</td>
			
      <?php
      
        if ($chkParticipante == 1)
        {
          
          echo "<td bgcolor='$cor_celula' style='border-bottom: 1px solid; border-right: 1px dotted' align='center'>$desc_participante</td>";
          
        }      
        
        if ($chkDataVencimento == 1)
        {
          
          
			echo "<td bgcolor='$cor_celula' style='border-bottom: 1px solid; border-right: 1px dotted' align='center'>";            
					
			echo DataMySQLRetornar($dados_rec[data_vencimento]);
          	
          	echo "</td>";
        
        }
        
        if ($chkValorContrato == 1)
        {
          
          echo "<td bgcolor='#F0D9D9' style='border-bottom: 1px solid; border-right: 1px dotted' align='center'>";        
					     
          echo "<strong><span style='color: blue'>" . number_format($dados_rec[valor_original], 2, ",", ".") . "</span></strong>";
          
			$total_vencido_original = $total_vencido_original + $dados_rec[valor_original];
          $total_aberto_original = $total_aberto_original + $dados_rec[valor_original];
          $total_recebido_original = $total_recebido_original + $dados_rec[valor_original];
          $total_geral_original = $total_geral_original + $dados_rec[valor_original];
        
          echo "</td>";
        
        }
        
        if ($chkValorBoleto == 1)
        {
          
          echo "<td bgcolor='$cor_celula' style='border-bottom: 1px solid; border-right: 1px dotted' align='center'>";           
					
			echo "<strong>" . number_format($dados_rec[valor_boleto], 2, ",", ".") . "</strong>";
            		
          $total_vencido_boletos = $total_vencido_boletos + $dados_rec[valor_boleto];
          $total_aberto_boletos = $total_aberto_boletos + $dados_rec[valor_boleto];
          $total_recebido_boletos = $total_recebido_boletos + $dados_rec[valor_boleto];
          $total_geral_boletos = $total_geral_boletos + $dados_rec[valor_boleto];
          
          echo "</td>";
        
        }
        
        if ($chkValorMultaJuros == 1)
        {
          
			echo "<td bgcolor='$cor_celula' style='border-bottom: 1px solid; border-right: 1px dotted' align='center'>"; 
					
			echo "<strong>" . number_format($dados_rec[valor_multa_juros], 2, ",", ".") . "</strong>";
            				
			$total_vencido_multa_juros = $total_vencido_multa_juros + $dados_rec[valor_multa_juros];
			$total_aberto_multa_juros = $total_aberto_multa_juros + $dados_rec[valor_multa_juros];
			$total_recebido_multa_juros = $total_recebido_multa_juros + $dados_rec[valor_multa_juros];
			$total_geral_multa_juros = $total_geral_multa_juros + $dados_rec[valor_multa_juros];
 
 
			echo "</td>";
          
        }
        
        if ($chkValorReceber == 1)
        {
          
          echo "<td bgcolor='#FFFFCD' style='border-bottom: 1px solid; border-right: 1px dotted' align='center'>";

          echo "<strong>" . number_format($dados_rec[valor], 2, ",", ".") . "</strong>"; 
          
          $total_vencido_receber = $total_vencido_receber + $dados_rec[valor];            
          $total_aberto_receber = $total_aberto_receber + $dados_rec[valor];
          $total_recebido_receber = $total_recebido_receber + $dados_rec[valor];
          $total_geral_receber = $total_geral_receber + $dados_rec[valor];
          
          echo "</td>";
          
        }
        
        if ($chkValorRecebido == 1)
        {
          
			echo "<td bgcolor='$cor_celula' style='border-bottom: 1px solid; border-right: 1px dotted' align='center'><strong><span style='color: #031C98;'>";
           
			echo number_format($dados_rec[valor_recebido], 2, ",", ".");
				  
			$total_vencido_recebido = $total_vencido_recebido + $dados_rec[valor_recebido];
			$total_aberto_recebido = $total_aberto_recebido + $dados_rec[valor_recebido];
			$total_recebido_recebido = $total_recebido_recebido + $dados_rec[valor_recebido];
			$total_geral_recebido = $total_geral_recebido + $dados_rec[valor_recebido];
          
			echo "</span></strong></td>";
          
        }
        
        if ($chkSaldoReceber == 1)
        {
          
			echo "<td bgcolor='$cor_celula' style='border-bottom: 1px solid;' align='center'><strong><span style='color: #990000;'>";
 
			echo number_format($dados_rec[valor] - $dados_rec[valor_recebido], 2, ",", ".");
				  
			$saldo_vencido_receber = $saldo_vencido_receber + ($dados_rec[valor] - $dados_rec[valor_recebido]);
			$saldo_aberto_receber = $saldo_aberto_receber + ($dados_rec[valor] - $dados_rec[valor_recebido]);
			$saldo_recebido_receber = $saldo_recebido_receber + ($dados_rec[valor] - $dados_rec[valor_recebido]);
			$saldo_geral_receber = $saldo_geral_receber + ($dados_rec[valor] - $dados_rec[valor_recebido]);
          
        }
          
		?>
        </span></strong>				
		</td>						
	</tr>
	<?php
	//Fecha o WHILE
	};
  
  //Envia com o formulario o total final do contador para efetuar o for depois
 	?>
  
  <input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>" />
	
  <?php
	//Fecha o if de se tem registros
	}

	//Verifica se precisa imprimir o rodapé
	if ($tot_regs > 0) 
  {
    
    if ($mostra_sub_aberto == 1)
    {
	?>
  
   <tr height="16">
        <td colspan="<?php echo $colspan ?>" height="20" align="right" style="border-right: 1px dotted"><strong>Sub-Total (A Vencer):&nbsp;&nbsp;</strong></td>
        
        <?php
        if ($chkValorContrato == 1)
        {
        
        ?>
        
        <td bgcolor="#F0D9D9" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_aberto_original, 2, ",", ".") ?></span>
        </td>
        
        <?php
        }
        
        if ($chkValorBoleto == 1)
        {
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_aberto_boletos, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkValorMultaJuros == 1)
        {
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_aberto_multa_juros, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkValorReceber == 1)
        {
        ?>
        <td bgcolor="#FFFFCD" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_aberto_receber, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkValorRecebido == 1)
        {
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
          <span style="color: #990000"><?php echo number_format($total_aberto_recebido, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        
        if ($chkSaldoReceber == 1)
        {
        ?>
        <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center">
          <span style="color: #990000"><?php echo number_format($saldo_aberto_receber, 2, ",", ".") ?></span>
        </td>
        <?php
        }
        ?>					
  	</tr>

  
  <?php
  }
  
  if ($mostra_sub_recebidas == 1)
  {

  ?>
	<tr height="16">
    <td colspan="<?php echo $colspan ?>" height="20" align="right" style="border-right: 1px dotted"><strong>Sub-Total (Recebidas):&nbsp;&nbsp;</strong></td>
    
    <?php
    
    if ($chkValorContrato == 1)
    {
      
    ?>
    
    <td bgcolor="#F0D9D9" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_recebido_original, 2, ",", ".") ?></span>
		</td>
    
    <?php
    }
    
    if ($chkValorBoleto == 1)
    {
    ?>
    
    <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_recebido_boletos, 2, ",", ".") ?></span>
		</td>
    <?php
    }
    
    if ($chkValorMultaJuros == 1)
    {
    ?>
    <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_recebido_multa_juros, 2, ",", ".") ?></span>
		</td>
    <?php
    }
    
    if ($chkValorReceber == 1)
    {
    ?>
    <td bgcolor="#FFFFCD" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_recebido_receber, 2, ",", ".") ?></span>
		</td>
    <?php
    }
    
    if ($chkValorRecebido == 1)
    {
    ?>
    <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_recebido_recebido, 2, ",", ".") ?></span>
		</td>
    <?php
    }
    
    if ($chkSaldoReceber == 1)
    {
    ?>
		<td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center">
      <span style="color: #990000"><?php echo number_format($saldo_recebido_receber, 2, ",", ".") ?></span>
		</td>	
    <?php
    }
    ?>				
	</tr>
  <?php
  }
  
  ?>
  
</table>

<br/>
<span class="TituloModulo">Totais</span>
<br/>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
  
  
  <tr height="16">
    <td colspan="<?php echo $colspan ?>" height="20" align="right" style="border-right: 1px dotted"><strong>Total Geral:&nbsp;&nbsp;</strong></td>
    
    <?php
    
    if ($chkValorContrato == 1)
    {
    
    ?> 
    <td bgcolor="#F0D9D9" width='66' height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_geral_original, 2, ",", ".") ?></span>
		</td>
    <?php
    
    }
    
    if ($chkValorBoleto == 1)
    {
    
    ?>
    <td width="60" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_geral_boletos, 2, ",", ".") ?></span>
		</td>
    <?php
    }
    
    if ($chkValorMultaJuros == 1)
    {
    ?>
    <td width='60' height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_geral_multa_juros, 2, ",", ".") ?></span>
		</td>
    <?php
    }
    
    if ($chkValorReceber == 1)
    {
    ?>
    <td bgcolor="#FFFFCD" width='66' height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_geral_receber, 2, ",", ".") ?></span>
		</td>
    <?php
    }
    
    if ($chkValorRecebido == 1)
    {
    ?>
    <td width='62' height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_geral_recebido, 2, ",", ".") ?></span>
		</td>
    <?php
    }
    
    if ($chkSaldoReceber == 1)
    {
    ?>
		<td width='62' height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center">
      <span style="color: #990000"><?php echo number_format($saldo_geral_receber, 2, ",", ".") ?></span>
		</td>
    <?php
    }
    ?>					
	</tr>	

	<?php
	//Fecha o IF
	};
	?>
		
	</table>	
	</td>
  </tr>  
</table>
</form>