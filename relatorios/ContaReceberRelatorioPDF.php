<?php
###########
## Módulo de relatório geral de contas a receber
## Criado: 07/06/2007 - Maycon Edinger
## Alterado: 14/08/2007 - Maycon Edinger
## Alterações: 
## 20/06/2007 - Invertido a ordem de exibição do sacado e descrição da conta
## 26/06/2007 - Incluído a filtragem opcional por datas
## 16/07/2007 - Incluído a filtragem por sacado
## 19/07/2007 - Incluído a filtragem por subgrupo
## 14/08/2007 - Incluído a filtragem agrupada grupo, subgrupo, categoria
###########

require('../fpdf/fpdf.php');

include '../conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include '../include/ManipulaDatas.php';

//Recupera os valores para filtragem

$empresaId = $_GET['EmpresaId'];
$empresaNome = $_GET['EmpresaNome'];
$usuarioNome = $_GET['UsuarioNome'];
$dataIni = DataMySQLInserir($_GET[DataIni]);
$dataFim = DataMySQLInserir($_GET[DataFim]);

$TipoSituacao = $_GET['TipoSituacao'];

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
  
		$colspan = '133';
  
	} 
	else
	{
    
		$colspan = '109';
	}

}

else

{
  
	if ($chkDataVencimento == 1)
	{
  
		$colspan = '125';
  
	} 
	else
	{
    
		$colspan = '101';
	}
  
}

//Verifica a situação informada
if ($_GET['TipoSituacao'] > 0)
{
	
	//Efetua o switch da situacao informada
	switch ($_GET['TipoSituacao']) 
    {
		//Se for 1 então é visualização em aberto
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
	if ($dataIni != 0) 
	{
		$TextoFiltraData = "E com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
		$TextoSQLData = "	 AND rec.data_vencimento >= '$dataIni' AND rec.data_vencimento <= '$dataFim' ";
	}

	//Efetua o switch para o campo de tipo de listagem
	switch ($_GET[TipoListagem]) 
	{
		//Se for 1 então é visualização por data
		case 1: 

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
					rec.origem_conta,
					rec.valor_recebido,
					rec.boleto_id,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					WHERE rec.empresa_id = '$empresaId' 
					AND rec.data_vencimento >= '$dataIni' 
					AND rec.data_vencimento <= '$dataFim' $TextoSituacao 
					AND rec.valor_original > 0
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
              
			//Monta o texto para caso não houver registros
			$texto_vazio = 'Não há contas a Receber entre as datas informadas';
                  
		break;

		//Se for 2 então é visualização por centro de custo
		case 2: 
			//Monta as variáveis
			$grupoId = $_GET[GrupoId];
		
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
					rec.origem_conta,
					rec.valor_recebido,
					rec.boleto_id,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					WHERE rec.empresa_id = $empresaId 
					AND rec.grupo_conta_id = '$grupoId' $TextoSQLData $TextoSituacao 
					AND rec.valor_original > 0
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
             
			//Monta o texto para caso não houver registros
			$texto_vazio = "Não há contas a Receber para o centro de custo $dados_grupo[nome]";
                  	
		break;
	
		//Se for 3 então é visualização por evento
		case 3: 
			//Monta as variáveis
			$eventoId = $_GET[EventoId];
		
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
					rec.origem_conta,
					rec.valor_recebido,
					rec.boleto_id,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					WHERE rec.empresa_id = '$empresaId' 
					AND rec.evento_id = '$eventoId' $TextoSQLData $TextoSituacao 
					AND rec.valor_original > 0
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
              
			//Monta o texto para caso não houver registros
			$texto_vazio = "Não há contas a Receber para o evento $dados_evento[nome]";
                  	
		break;		

		//Se for 4 então é visualização por situacao
		case 4:
			//Monta as variáveis
			$TipoSituacao = $_GET[TipoSituacao];
		
			//Efetua o switch da situacao informada
			switch ($TipoSituacao) 
			{
				//Se for 1 então é visualização a vencer
				case 1:
					$texto_situacao = 'A Vencer';
					$where_situacao = "rec.situacao = '$TipoSituacao'";
				break;		
				//Se for 2 então é visualização das recebidas
				case 2:
					$texto_situacao = 'Pagas';
					$where_situacao = "rec.situacao = '$TipoSituacao'";
				break;
				//Se for 3 então é visualização das vencidas
				case 3:
					$texto_situacao = 'Vencidas';
					$data_base_vencimento = date('Y-m-d', mktime());
					$where_situacao = "rec.situacao = '1' AND rec.data_vencimento < '$data_base_vencimento'";
				break;
			}	
		
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
					rec.origem_conta,
					rec.valor_recebido,
					rec.boleto_id,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					WHERE rec.empresa_id = $empresaId 
					AND $where_situacao $TextoSQLData 
					AND rec.valor_original > 0
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
              
			//Monta o texto para caso não houver registros
			$texto_vazio = "Não há contas a Receber com a situação $texto_situacao";
			  
		break;

		//Se for 5 então é visualização por sacado
		case 5:
			//Monta as variáveis
			$TipoPessoa = $_GET[TipoPessoa];
			$PessoaId = $_GET[PessoaId];    
			
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
					rec.origem_conta,
					rec.boleto_id,
					rec.valor_recebido,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					WHERE rec.empresa_id = $empresaId 
					AND rec.tipo_pessoa = '$TipoPessoa' 
					AND rec.pessoa_id = '$PessoaId' $TextoSQLData $TextoSituacao 
					AND rec.valor_original > 0
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
		  
			//Monta o texto para caso não houver registros
			$texto_vazio = "Não há contas a Receber do $texto_pessoa";
			  
		break;	

		//Se for 6 então é visualização por conta-caixa
		case 6: 
			//Monta as variáveis
			$subgrupoId = $_GET[SubgrupoId];
	
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
					rec.origem_conta,
					rec.valor_recebido,
					rec.boleto_id,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					WHERE rec.empresa_id = $empresaId 
					AND rec.subgrupo_conta_id = '$subgrupoId' $TextoSQLData $TextoSituacao 
					AND rec.valor_original > 0
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";
		  
			//Monta o texto para caso não houver registros
			$texto_vazio = "Não há contas a Receber para a conta-caixa $dados_subgrupo[nome]";
			  
		break;	

		//Se for 7 então é visualização agrupada
		case 7: 
			//Monta as variáveis
			$grupoId = $_GET[GrupoId];
			$subgrupoId = $_GET[SubgrupoId];
	
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
					rec.origem_conta,
					rec.valor_recebido,
					rec.boleto_id,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					WHERE rec.empresa_id = $empresaId 
					AND rec.grupo_conta_id = '$grupoId' 
					AND rec.subgrupo_conta_id = '$subgrupoId' $TextoSQLData $TextoSituacao 
					AND rec.valor_original > 0
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";					
		  
			//Monta o texto para caso não houver registros
			$texto_vazio = 'Não há contas a Receber para este agrupamento !';
								
		break;

		//Se for 8 então é visualização por evento e formando
		case 8: 
			//Monta as variáveis
			$eventoId = $_GET[EventoId];
			$formandoId = $_GET[FormandoId];

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
					rec.origem_conta,
					rec.valor_recebido,
					rec.boleto_id,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					WHERE rec.empresa_id = '$empresaId' 
					AND rec.evento_id = '$eventoId' AND rec.formando_id = '$formandoId' $TextoSQLData $TextoSituacao 
					AND rec.valor_original > 0
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";	
		  
			//Monta o texto para caso não houver registros
			$texto_vazio = 'Não há contas a Receber para este evento e formando';
			  
		break;		

		//Se for 9 então é visualização por evento e conta-caixa
		case 9: 
			//Monta as variáveis
			$eventoId = $_GET[EventoId];
			$contaCaixaId = $_GET[ContaCaixaId];

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
					rec.origem_conta,
					rec.valor_recebido,
					rec.boleto_id,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					WHERE rec.empresa_id = '$empresaId' 
					AND rec.evento_id = '$eventoId' 
					AND rec.subgrupo_conta_id = '$contaCaixaId' 
					$TextoSQLData $TextoSituacao 
					AND rec.valor_original > 0
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";	
		  
			//Monta o texto para caso não houver registros
			$texto_vazio = 'Não há contas a Receber para este evento e conta-caixa';
			  
		break;
		
		//Se for 10 então é visualização por evento e curso
		case 10: 
			//Monta as variáveis
			$eventoId = $_GET[EventoId];
			$cursoId = $_GET[CursoId];

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
					rec.origem_conta,
					rec.valor_recebido,
					rec.boleto_id,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					LEFT OUTER JOIN eventos_formando forma ON forma.id = rec.formando_id
					WHERE rec.empresa_id = '$empresaId' 
					AND rec.evento_id = '$eventoId' 
					AND forma.curso_id = '$cursoId' $TextoSQLData $TextoSituacao 
					AND rec.valor_original > 0
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";	
		  
			//Monta o texto para caso não houver registros
			$texto_vazio = 'Não há contas a Receber para este evento e curso';
			  
		break;
		
		//Se for 11 então é visualização por evento, formando e conta-caixa
		case 11: 
			//Monta as variáveis
			$eventoId = $_GET[EventoId];
			$formandoId = $_GET[FormandoId];
			$contaCaixaId = $_GET[ContaCaixaId];

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
					rec.origem_conta,
					rec.valor_recebido,
					rec.boleto_id,
					rec.observacoes,
					eve.nome as evento_nome
					FROM contas_receber rec
					LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
					WHERE rec.empresa_id = '$empresaId' 
					AND rec.evento_id = '$eventoId' 
					AND rec.formando_id = '$formandoId' 
					AND rec.subgrupo_conta_id = '$contaCaixaId' 
					AND rec.valor_original > 0
					$TextoSQLData $TextoSituacao 
					ORDER BY rec.situacao, rec.data_vencimento, rec.descricao";	
		  
			//Monta o texto para caso não houver registros
			$texto_vazio = 'Não há contas a Receber para este evento, formando e conta-caixa';
			  
		break;
	}

$query = mysql_query($sql);

//echo $sql;
	  	
$registros = mysql_num_rows($query);
//verifica o número total de registros
$tot_regs = mysql_num_rows($query);  

class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{

		//Pega a data inicial pra ver se veio vazia
		$dataIni = DataMySQLInserir($_GET[DataIni]);
	    
		$empresaNome = $_GET['EmpresaNome'];
		
		//Monta o switch para criar o texto da filtragem
		switch ($_GET[TipoListagem]) 
		{
  
			//Se for 1 então é visualização por data
			case 1: 
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
			break;

			//Se for 2 então é visualização por centro de custo
			case 2: 
				//Monta as variáveis
				$grupoId = $_GET[GrupoId];
		
				//Recupera o nome do grupo selecionado
				$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = '$grupoId'");
		
				//Monta o array com os dados
				$dados_grupo = mysql_fetch_array($sql_grupo);
		
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber do centro de custo: $dados_grupo[nome]";
		
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
				}
			break;

			//Se for 3 então é visualização por evento
			case 3: 
				//Monta as variáveis
				$eventoId = $_GET[EventoId];
				
				//Recupera o nome do evento selecionado
				$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
				
				//Monta o array com os dados
				$dados_evento = mysql_fetch_array($sql_evento);
				
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber do evento: $dados_evento[nome]";
				
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
				}
			break;	

			//Se for 4 então é visualização por situacao
			case 4:
				//Monta as variáveis
				$TipoSituacao = $_GET[TipoSituacao];
			
				//Efetua o switch da situacao informada
				switch ($TipoSituacao) 
				{
				
					//Se for 1 então é visualização a Vencer
					case 1:
						$texto_situacao = 'A Vencer';
					break;		
					//Se for 2 então é visualização das pagas
					case 2:
						$texto_situacao = 'Recebidas';
					break;
					//Se for 3 então é visualização das vencidas
					case 3:
						$texto_situacao = 'Vencidas';
					break;
				}
			
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber com situacao: $texto_situacao";
			
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
				}
			break;
		
			//Se for 5 então é visualização por sacado
			case 5:
				//Monta as variáveis
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
						$texto_pessoa = "Cliente: $nome_cliente[nome]";
					break;		
					//Se for 2 então é fornecedor
					case 2:
						//Recupera o nome do fornecedor
						$query_fornecedor = mysql_query("SELECT nome FROM fornecedores WHERE id = '$PessoaId'");
						$nome_fornecedor = mysql_fetch_array($query_fornecedor);
						$texto_pessoa = "Fornecedor: $nome_fornecedor[nome]";
					break;
					//Se for 3 então é colaborador
					case 3:
						//Recupera o nome do colaborador
						$query_colaborador = mysql_query("SELECT nome FROM colaboradores WHERE id = '$PessoaId'");
						$nome_colaborador = mysql_fetch_array($query_colaborador);
						$texto_pessoa = "Colaborador: $nome_colaborador[nome]";
					break;
				}
			
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber do $texto_pessoa";
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
				}
			break;		
		
		
			//Se for 6 então é visualização por conta-caixa
			case 6: 
				//Monta as variáveis
				$subgrupoId = $_GET[SubgrupoId];
					
				//Recupera o nome do subgrupo selecionado
				$sql_subgrupo = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$subgrupoId'");
				
				//Monta o array com os dados
				$dados_subgrupo = mysql_fetch_array($sql_subgrupo);
				
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber da conta-caixa: $dados_subgrupo[nome]";
				
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
				}
			break;
		
			//Se for 7 então é visualização agrupada
			case 7: 
				$grupoId = $_GET[GrupoId];
				$subgrupoId = $_GET[SubgrupoId];		
				$categoriaId = $_GET[CategoriaId];
				
				//Recupera o nome do grupo selecionado
				$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = '$grupoId'");
				
				//Monta o array com os dados
				$dados_grupo = mysql_fetch_array($sql_grupo);
				
				//Recupera o nome do subgrupo selecionado
				$sql_subgrupo = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$subgrupoId'");
				
				//Monta o array com os dados
				$dados_subgrupo = mysql_fetch_array($sql_subgrupo);
						
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber do centro de custo: $dados_grupo[nome]\nE da conta-caixa: $dados_subgrupo[nome]";
				
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					$desc_filtragem .= "\ne com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
				}
			break;
	  
			//Se for 8 então é visualização por evento e formando
			case 8: 
				//Monta as variáveis
				$eventoId = $_GET[EventoId];
				$formandoId = $_GET[FormandoId];
				
				//Recupera o nome do evento selecionado
				$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
				
				//Monta o array com os dados
				$dados_evento = mysql_fetch_array($sql_evento);
			
				//Recupera o nome do formando selecionado
				$sql_formando = mysql_query("SELECT nome FROM eventos_formando WHERE id = '$formandoId'");
				
				//Monta o array com os dados
				$dados_formando = mysql_fetch_array($sql_formando);
				
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber do evento: $dados_evento[nome]\nE do formando: $dados_formando[nome]";
				
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
				}
			break;
	  
			//Se for 9 então é visualização por evento e conta-caixa
			case 9: 
				//Monta as variáveis
				$eventoId = $_GET[EventoId];
				$contaCaixaId = $_GET[ContaCaixaId];
				
				//Recupera o nome do evento selecionado
				$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
				
				//Monta o array com os dados
				$dados_evento = mysql_fetch_array($sql_evento);
			
				//Recupera o nome do formando selecionado
				$sql_formando = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$contaCaixaId'");
				
				//Monta o array com os dados
				$dados_formando = mysql_fetch_array($sql_formando);
				
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber do evento: $dados_evento[nome]\nE da Conta-caixa: $dados_formando[nome]";
				
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
				}
			break;
			
			//Se for 10 então é visualização por evento e curso
			case 10: 
				//Monta as variáveis
				$eventoId = $_GET[EventoId];
				$cursoId = $_GET[CursoId];
				
				//Recupera o nome do evento selecionado
				$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
				
				//Monta o array com os dados
				$dados_evento = mysql_fetch_array($sql_evento);
			
				//Recupera o nome do formando selecionado
				$sql_curso = mysql_query("SELECT nome FROM cursos WHERE id = '$cursoId'");
				
				//Monta o array com os dados
				$dados_curso = mysql_fetch_array($sql_curso);
				
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber do evento: $dados_evento[nome]\nE do curso: $dados_curso[nome]";
				
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
				}
			break;
			
			//Se for 11 então é visualização por evento, formando e conta-caixa
			case 11: 
				//Monta as variáveis
				$eventoId = $_GET[EventoId];
				$formandoId = $_GET[FormandoId];
				$contaCaixaId = $_GET[ContaCaixaId];
				
				//Recupera o nome do evento selecionado
				$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
				
				//Monta o array com os dados
				$dados_evento = mysql_fetch_array($sql_evento);
			
				//Recupera o nome do formando selecionado
				$sql_formando = mysql_query("SELECT nome FROM eventos_formando WHERE id = '$formandoId'");
				
				//Monta o array com os dados
				$dados_formando = mysql_fetch_array($sql_formando);
				
				//Recupera o nome da conta-caixa selecionado
				$sql_conta_caixa = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$contaCaixaId'");
				
				//Monta o array com os dados
				$dados_conta_caixa = mysql_fetch_array($sql_conta_caixa);
				
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a receber do evento: $dados_evento[nome]\nE do formando: $dados_formando[nome]\nE da conta-caixa: $dados_conta_caixa[nome]";
				
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
				}
			break;

			
		}

		
		//Ajusta a fonte
		$this->SetFont('Arial','',9);
		//Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->SetFont('Arial','B',15);
		$this->Cell(0,6,'Relação Geral de Contas a Receber');
		$this->SetFont('Arial','',9);
		$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
		$this->Ln(5);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(19,6,'Filtragem:');
		$this->SetFont('Arial', '', 10);
		$this->Multicell(0,5, $desc_filtragem);
		//Line break
		$this->Ln(2);
	}

	//Rodapé do Relatório
	function Footer()
	{
		$usuarioNome = $_GET['UsuarioNome'];
		//Position at 1.5 cm from bottom
		$this->SetY(-15);    
		//Arial italic 8
		$this->SetFont('Arial','I',7);
		//Page number
		$this->Line(10,281,200,281);
		$this->Cell(0,3,'Emitido por: ' . $usuarioNome);
	}
}

//Instancia a classe gerador de pdf
$pdf=new PDF();
//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos');
$pdf->SetAuthor($usuarioNome . ' - ' . $empresaNome);
$pdf->SetTitle('Relação Geral de Contas a Receber');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');


//*************************************************************************************

//Caso houverem registros
if ($tot_regs > 0) 
{

	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());

	$imprime_cabecalho = 0;              

	//Cria a variavel zerada para o contador de checkboxes
	$edtItemChk = 0;
		 
}
	  
//Caso não houverem registros
if ($tot_regs == 0) 
{ 

	//Exibe uma linha dizendo que nao há registros
	$pdf->Cell(0,6, $texto_vazio,1,0);
		  
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
				$desc_pessoa = 'Cliente';
				$dados_pessoa = mysql_fetch_array($sql);

				$desc_participante = ' ';
				$edtValorCapital = '0,00';
				$edtValorCapitalSoma = 0;				
  			break;
			case 2: //Se for fornecedor
  				$sql = mysql_query("SELECT nome FROM fornecedores WHERE id = '$dados_rec[pessoa_id]'");
  				$desc_pessoa = 'Fornecedor';
  				$dados_pessoa = mysql_fetch_array($sql);
          
				$desc_participante = ' ';
				$edtValorCapital = '0,00';
				$edtValorCapitalSoma = 0;				
  			break;
			case 3: //Se for colaborador
  				$sql = mysql_query("SELECT nome FROM colaboradores WHERE id = '$dados_rec[pessoa_id]'");
  				$desc_pessoa = 'Colaborador';
  				$dados_pessoa = mysql_fetch_array($sql);
          
				$desc_participante = ' ';
				$edtValorCapital = '0,00';
				$edtValorCapitalSoma = 0;				
  			break;
			case 4: //Se for formando
  				$sql = mysql_query("SELECT nome, participante, chk_culto, chk_colacao, chk_jantar, chk_baile, valor_capital FROM eventos_formando WHERE id = '$dados_rec[pessoa_id]'");
  				$desc_pessoa = 'Formando';
  				$dados_pessoa = mysql_fetch_array($sql);
          
				$desc_participante = "";
					
				if ($dados_pessoa["chk_culto"] == 1)
				{
				
					$desc_participante .= "M";
					
				}
				
				if ($dados_pessoa["chk_colacao"] == 1)
				{
				
					$desc_participante .= "C";
					
				}
				
				if ($dados_pessoa["chk_jantar"] == 1)
				{
				
					$desc_participante .= "J";
					
				}
				
				if ($dados_pessoa["chk_baile"] == 1)
				{
				
					$desc_participante .= "B";
					
				}
          
				$edtValorCapital = number_format($dados_pessoa[valor_capital], 2, ',', '.');
				$edtValorCapitalSoma = $dados_pessoa[valor_capital];
          				
  			break;
			case 5: //Se for por evento
  				$sql = mysql_query("SELECT nome FROM eventos WHERE id = '$dados_rec[pessoa_id]'");
  				$desc_pessoa = 'Evento';
  				$dados_pessoa = mysql_fetch_array($sql);
          
				$desc_participante = ' ';
				$edtValorCapital = '0,00';
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
			case 2: $desc_situacao = "<span style='color: blue'>[Recebida]</span>"; break;
		}
    
		//Verifica se a situacao da primeira está vencida
		if ($dados_rec['situacao'] == 1 AND $dados_rec['data_vencimento'] < $data_base_vencimento)
		{
    
			if ($mostra_topo_vencidas == 0)
			{
              
				//Títulos das colunas
				$pdf->SetFont('Arial', 'B', 14);
				$pdf->Cell(0,6, 'Contas Vencidas','B');
				$pdf->Ln(8);
				$pdf->SetFont('Arial', 'B', 10);
				//Define a cor RGB do fundo da celula
				$pdf->SetFillColor(178,178,178);
				//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
				$pdf->Cell(101,6, 'Sacado/Descrição da Conta a Receber',1,0,'L',1);
				
				if ($chkParticipante == 1)
				{
				
				  $pdf->Cell(8,6, 'P',1,0,'C',1);
				  
				}
        
				if ($chkDataVencimento == 1)
				{
				  
				  $pdf->Cell(24,6, 'Vencimento',1,0,'C',1);
				  
				}
				
				if ($chkValorContrato == 1)
				{
				  
				  $pdf->Cell(24,6, 'Vl. Contrato',1,0,'C',1);
				  
				}
				
				if ($chkValorBoleto == 1)
				{
				  
				  $pdf->Cell(24,6, 'Vl. Boleto',1,0,'C',1);
				  
				}
        
				if ($chkValorMultaJuros == 1)
				{
				
				  $pdf->Cell(24,6, 'Multa/Juros',1,0,'C',1);
				  
				}
				
				if ($chkValorReceber == 1)
				{
				
				  $pdf->Cell(24,6, 'Vl. Receber',1,0,'C',1);
				  
				}
				
				if ($chkValorRecebido == 1)
				{
				  
				  $pdf->Cell(24,6, 'Vl. Recebido',1,0,'C',1);
				  
				}
        
				if ($chkSaldoReceber == 1)
				{
				
				  $pdf->Cell(24,6, 'Saldo',1,0,'C',1);
				  
				}        
		  
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
        
				$pdf->Ln();
				$pdf->SetFont('Arial', 'B', 10);
				//Define a cor RGB do fundo da celula
				$pdf->SetFillColor(178,178,178);
				//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
				$pdf->Cell($colspan,6, 'Sub-total (Vencidas): ',1,0,'R',1);
				
				if ($chkValorContrato == 1)
				{
          
					$pdf->Cell(24,6, number_format($total_vencido_original, 2, ',', '.'),1,0,'C',1);
          
				}
        
				if ($chkValorBoleto == 1)
				{
				  
				  $pdf->Cell(24,6, number_format($total_vencido_boletos, 2, ',', '.'),1,0,'C',1);
				  
				}
        
				if ($chkValorMultaJuros == 1)
				{
				
				  $pdf->Cell(24,6, number_format($total_vencido_multa_juros, 2, ',', '.'),1,0,'C',1);
				  
				}
				
				if ($chkValorReceber == 1)
				{
				
				  $pdf->Cell(24,6, number_format($total_vencido_receber, 2, ',', '.'),1,0,'C',1);
				  
				}
        
				if ($chkValorRecebido == 1)
				{
				
				  $pdf->Cell(24,6, number_format($total_vencido_recebido, 2, ',', '.'),1,0,'C',1);
				  
				}
				
				if ($chkSaldoReceber == 1)
				{
				  
				  $pdf->Cell(24,6, number_format($saldo_vencido_receber, 2, ',', '.'),1,0,'C',1);
				  
				}
        
      
			}
        
			$mostra_sub_aberto = 1;
							   
			//Títulos das colunas  A VENCER
			$pdf->Ln(10);
			$pdf->SetFont('Arial', 'B', 14);
			$pdf->Cell(0,6, 'Contas A Vencer','B');
			$pdf->Ln(8);
			$pdf->SetFont('Arial', 'B', 10);
			//Define a cor RGB do fundo da celula
			$pdf->SetFillColor(178,178,178);
			//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
			$pdf->Cell(101,6, 'Sacado/Descrição da Conta a Receber',1,0,'L',1);
			
			if ($chkParticipante == 1)
			{
			  
				$pdf->Cell(8,6, 'P',1,0,'C',1);
			
			}
        
			if ($chkDataVencimento == 1)
			{
					
				$pdf->Cell(24,6, 'Vencimento',1,0,'C',1);
			  
			}
			
			if ($chkValorContrato == 1)
			{
			
				$pdf->Cell(24,6, 'Vl. Contrato',1,0,'C',1);
			  
			}
			
			if ($chkValorBoleto == 1)
			{
			
				$pdf->Cell(24,6, 'Vl. Boleto',1,0,'C',1);
			  
			}
			
			if ($chkValorMultaJuros == 1)
			{
			
				$pdf->Cell(24,6, 'Multa/Juros',1,0,'C',1);
			  
			}
        
        if ($chkValorReceber == 1)
        {
          
			$pdf->Cell(24,6, 'Vl. Receber',1,0,'C',1);
        
        }
        
        if ($chkValorRecebido == 1)
        {
        
			$pdf->Cell(24,6, 'Vl. Recebido',1,0,'C',1);
          
        }
        
        if ($chkSaldoReceber == 1)
        {
        
			$pdf->Cell(24,6, 'Saldo',1,0,'C',1);
          
        }  
      
        $imprime_cabecalho_umavez = 1;
        
        $total_aberto_original = 0;    
        $total_aberto_boletos = 0;    
        $total_aberto_multa_juros = 0;    
        $total_aberto_receber = 0;
        $total_aberto_recebido = 0;
        $saldo_aberto_receber = 0;
    
    }
               

    if ($situacao != $dados_rec['situacao'])
    {
            
		if ($imprime_rodape != 1)
		{
        
			$pdf->Ln();
			$pdf->SetFont('Arial', 'B', 10);
			//Define a cor RGB do fundo da celula
			$pdf->SetFillColor(178,178,178);
			//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
			$pdf->Cell($colspan,6, 'Sub-total (Aberto): ',1,0,'R',1);
			
			if ($chkValorContrato == 1)
			{
			  
				$pdf->Cell(24,6, number_format($total_aberto_original, 2, ',', '.'),1,0,'C',1);
			  
			}
        
			if ($chkValorBoleto == 1)
			{
			
				$pdf->Cell(24,6, number_format($total_aberto_boletos, 2, ',', '.'),1,0,'C',1);
			  
			}
			
			if ($chkValorMultaJuros == 1)
			{
			
				$pdf->Cell(24,6, number_format($total_aberto_multa_juros, 2, ',', '.'),1,0,'C',1);
			  
			}
        
			if ($chkValorReceber == 1)
			{
			  
				$pdf->Cell(24,6, number_format($total_aberto_receber, 2, ',', '.'),1,0,'C',1);
			  
			}
			
			if ($chkValorRecebido == 1)
			{
			
				$pdf->Cell(24,6, number_format($total_aberto_recebido, 2, ',', '.'),1,0,'C',1);
			  
			}
			
			if ($chkSaldoReceber == 1)
			{
			
				$pdf->Cell(24,6, number_format($saldo_aberto_receber, 2, ',', '.'),1,0,'C',1);
			
			}
    
			$mostra_sub_aberto = 0;
    
		}
      
      
        $mostra_sub_recebidas = 1;
      
        //Títulos das colunas  Recebidas
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0,6, 'Contas Recebidas','B');
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 10);
        //Define a cor RGB do fundo da celula
        $pdf->SetFillColor(178,178,178);
        //Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
        $pdf->Cell(101,6, 'Sacado/Descrição da Conta a Receber',1,0,'L',1);
        
        if ($chkParticipante == 1)
        {
          
			$pdf->Cell(8,6, 'P',1,0,'C',1);
        
        }
        
        if ($chkDataVencimento == 1)
        {        
        
			$pdf->Cell(24,6, 'Vencimento',1,0,'C',1);
          
        }
        
        if ($chkValorContrato == 1)
        {
        
			$pdf->Cell(24,6, 'Vl. Contrato',1,0,'C',1);
          
        }
        
        if ($chkValorBoleto == 1)
        {
        
			$pdf->Cell(24,6, 'Vl. Boleto',1,0,'C',1);
          
        }
        
        if ($chkValorMultaJuros == 1)
        {
        
			$pdf->Cell(24,6, 'Multa/Juros',1,0,'C',1);
          
        }
        
        if ($chkValorReceber == 1)
        {
        
			$pdf->Cell(24,6, 'Vl. Receber',1,0,'C',1);
        
        }
        
        if ($chkValorRecebido == 1)
        {
        
			$pdf->Cell(24,6, 'Vl. Recebido',1,0,'C',1);
          
        }
        
        if ($chkSaldoReceber == 1)
        {
        
			$pdf->Cell(24,6, 'Saldo',1,0,'C',1);
          
        } 
     
        $situacao = $dados_rec['situacao'];
       
        $total_recebido_original = 0;
        $total_recebido_boletos = 0;
        $total_recebido_multa_juros = 0;
        $total_recebido_receber = 0;
        $total_recebido_recebido = 0;
        $saldo_recebido_receber = 0;
      
    }
      
	if ($dados_rec['origem_conta'] == 2)
	{
		
		$origem  = "$dados_rec[evento_nome] EM " . DataMySQLRetornar($dados_rec[data]);

	} 
    
	else 
    
	{
			
		if ($dados_rec[boleto_id] > 0) 
        {
			
			$origem = 'Gerada via Contas a Receber (VIA BOLETO) EM ' . DataMySQLRetornar($dados_rec[data]);
          
        }
        else
        {
          
          $origem = 'Gerada via Contas a Receber EM ' . DataMySQLRetornar($dados_rec[data]);
          
        }
        
	}
        
	//Imprime os Registros
	$pdf->ln();
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(101,5, $dados_pessoa['nome'],'LTR',0,'L'); 
	$pdf->SetFont('Arial','',9);
	//$pdf->Cell(0,5, $desc_pessoa);
      
	if ($chkParticipante == 1)
	{

		$pdf->Cell(8,5, $desc_participante,'LTR',0,'C');

	}
      
	if ($chkDataVencimento == 1)
	{

		$pdf->Cell(24,5, DataMySQLRetornar($dados_rec['data_vencimento']),'LTR',0,'C');

	}

	if ($chkValorContrato == 1)
	{

		$pdf->Cell(24,5, number_format($dados_rec['valor_original'], 2, ',', '.'),'LTR',0,'C');

	}

	if ($chkValorBoleto == 1)
	{

		$pdf->Cell(24,5, number_format($dados_rec['valor_boleto'], 2, ',', '.'),'LTR',0,'C');

	}
      
	if ($chkValorMultaJuros == 1)
	{

		$pdf->Cell(24,5, number_format($dados_rec['valor_multa_juros'], 2, ',', '.'),'LTR',0,'C');

	}

	if ($chkValorReceber == 1)
	{

		$pdf->Cell(24,5, number_format($dados_rec['valor'], 2, ',', '.'),'LTR',0,'C');

	}
      
	if ($chkValorRecebido == 1)
	{

		$pdf->Cell(24,5, number_format($dados_rec['valor_recebido'], 2, ',', '.'),'LTR',0,'C');

	}

	if ($chkSaldoReceber == 1)
	{

		$pdf->Cell(24,5, number_format($dados_rec[valor] - $dados_rec[valor_recebido], 2, ',', '.'),'LTR',0,'C');

	}
      
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(101,3, $dados_rec['descricao'],'LR',0,'L');
      
	if ($chkParticipante == 1)
	{

		$pdf->Cell(8,3, ' ','LR',0,'C');

	}

	if ($chkDataVencimento == 1)
	{

		$pdf->Cell(24,3, ' ','LR',0,'C');

	}

	if ($chkValorContrato == 1)
	{

		$pdf->Cell(24,3, ' ','LR',0,'C');

	}

	if ($chkValorBoleto == 1)
	{

		$pdf->Cell(24,3, ' ','LR',0,'C');

	}
      
	if ($chkValorMultaJuros == 1)
	{

		$pdf->Cell(24,3, ' ','LR',0,'C');

	}

	if ($chkValorReceber == 1)
	{

		$pdf->Cell(24,3, ' ','LR',0,'C');

	}

	if ($chkValorRecebido == 1)
	{

		$pdf->Cell(24,3, ' ','LR',0,'C');

	}
      
	if ($chkSaldoReceber == 1)
	{

		$pdf->Cell(24,3, ' ','LR',0,'C');

	}
      
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(101,3, $origem,'LBR',0,'L');
      
	if ($chkParticipante == 1)
	{

		$pdf->Cell(8,3, ' ','LBR',0,'C');

	}

	if ($chkDataVencimento == 1)
	{

		$pdf->Cell(24,3, ' ','LBR',0,'C');

	}
      
	if ($chkValorContrato == 1)
	{

		$pdf->Cell(24,3, ' ','LBR',0,'C');

	}

	if ($chkValorBoleto == 1)
	{

		$pdf->Cell(24,3, ' ','LBR',0,'C');

	}

	if ($chkValorMultaJuros == 1)
	{

		$pdf->Cell(24,3, ' ','LBR',0,'C');

	}
		  
	if ($chkValorReceber == 1)
	{

		$pdf->Cell(24,3, ' ','LBR',0,'C');

	}

	if ($chkValorRecebido == 1)
	{

		$pdf->Cell(24,3, ' ','LBR',0,'C');

	}

	if ($chkSaldoReceber == 1)
	{

		$pdf->Cell(24,3, ' ','LBR',0,'C');

	}
  
	if ($_GET["Observacoes"] == 1 AND $dados_rec["observacoes"] != "")
	{
	
		$pdf->ln();
		$pdf->SetFont('Arial','',8);
		$pdf->Multicell(0,3, "Observações:\n\n" . $dados_rec["observacoes"] ,'LBR');	
	
	}
  
	$total_vencido_original = $total_vencido_original + $dados_rec[valor_original];
	$total_aberto_original = $total_aberto_original + $dados_rec[valor_original];
	$total_recebido_original = $total_recebido_original + $dados_rec[valor_original];
	$total_geral_original = $total_geral_original + $dados_rec[valor_original];
		
	$total_vencido_boletos = $total_vencido_boletos + $dados_rec[valor_boleto];
	$total_aberto_boletos = $total_aberto_boletos + $dados_rec[valor_boleto];
	$total_recebido_boletos = $total_recebido_boletos + $dados_rec[valor_boleto];
	$total_geral_boletos = $total_geral_boletos + $dados_rec[valor_boleto];      
									
	$total_vencido_multa_juros = $total_vencido_multa_juros + $dados_rec[valor_multa_juros];
	$total_aberto_multa_juros = $total_aberto_multa_juros + $dados_rec[valor_multa_juros];
	$total_recebido_multa_juros = $total_recebido_multa_juros + $dados_rec[valor_multa_juros];
	$total_geral_multa_juros = $total_geral_multa_juros + $dados_rec[valor_multa_juros];

	$total_vencido_receber = $total_vencido_receber + $dados_rec[valor];            
	$total_aberto_receber = $total_aberto_receber + $dados_rec[valor];
	$total_recebido_receber = $total_recebido_receber + $dados_rec[valor];
	$total_geral_receber = $total_geral_receber + $dados_rec[valor];

	$total_vencido_recebido = $total_vencido_recebido + $dados_rec[valor_recebido];
	$total_aberto_recebido = $total_aberto_recebido + $dados_rec[valor_recebido];
	$total_recebido_recebido = $total_recebido_recebido + $dados_rec[valor_recebido];
	$total_geral_recebido = $total_geral_recebido + $dados_rec[valor_recebido];

	$saldo_vencido_receber = $saldo_vencido_receber + ($dados_rec[valor] - $dados_rec[valor_recebido]);
	$saldo_aberto_receber = $saldo_aberto_receber + ($dados_rec[valor] - $dados_rec[valor_recebido]);
	$saldo_recebido_receber = $saldo_recebido_receber + ($dados_rec[valor] - $dados_rec[valor_recebido]);
	$saldo_geral_receber = $saldo_geral_receber + ($dados_rec[valor] - $dados_rec[valor_recebido]);
				

			
	//Fecha o WHILE
	};
  
	//Fecha o if de se tem registros
	}

	//Verifica se precisa imprimir o rodapé
	if ($tot_regs > 0) 
  {
    
    if ($mostra_sub_aberto == 1)
    {
      
      $pdf->Ln();
      $pdf->SetFont('Arial', 'B', 10);
      //Define a cor RGB do fundo da celula
      $pdf->SetFillColor(178,178,178);
      //Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
      $pdf->Cell($colspan,6, 'Sub-total (Aberto): ',1,0,'R',1);
      
      if ($chkValorContrato == 1)
      {
        
        $pdf->Cell(24,6, number_format($total_aberto_original, 2, ',', '.'),1,0,'C',1);
        
      }
      
      if ($chkValorBoleto == 1)
      {
      
        $pdf->Cell(24,6, number_format($total_aberto_boletos, 2, ',', '.'),1,0,'C',1);
        
      }
      
      if ($chkValorMultaJuros == 1)
      {
      
        $pdf->Cell(24,6, number_format($total_aberto_multa_juros, 2, ',', '.'),1,0,'C',1);
        
      }
      
      if ($chkValorReceber == 1)
      {
        
        $pdf->Cell(24,6, number_format($total_aberto_receber, 2, ',', '.'),1,0,'C',1);
        
      }
      
      if ($chkValorRecebido == 1)
      {
      
        $pdf->Cell(24,6, number_format($total_aberto_recebido, 2, ',', '.'),1,0,'C',1);
        
      }
      
      if ($chkSaldoReceber == 1)
      {
      
        $pdf->Cell(24,6, number_format($saldo_aberto_receber, 2, ',', '.'),1,0,'C',1);
      
      }
    
    }    
        
    
    if ($mostra_sub_recebidas == 1)
    {    
	
      $pdf->Ln();
      $pdf->SetFont('Arial', 'B', 10);
      //Define a cor RGB do fundo da celula
      $pdf->SetFillColor(178,178,178);
      //Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
      $pdf->Cell($colspan,6, 'Sub-total (Recebidas): ',1,0,'R',1);
      
      if ($chkValorContrato == 1)
      {
        
        $pdf->Cell(24,6, number_format($total_recebido_original, 2, ',', '.'),1,0,'C',1);
        
      }
      
      if ($chkValorBoleto == 1)
      {
      
        $pdf->Cell(24,6, number_format($total_recebido_boletos, 2, ',', '.'),1,0,'C',1);
        
      }
      
      if ($chkValorMultaJuros == 1)
      {
      
        $pdf->Cell(24,6, number_format($total_recebido_multa_juros, 2, ',', '.'),1,0,'C',1);
        
      }
      
      if ($chkValorReceber == 1)
      {
      
        $pdf->Cell(24,6, number_format($total_recebido_receber, 2, ',', '.'),1,0,'C',1);
        
      }
      
      if ($chkValorRecebido == 1)
      {
      
        $pdf->Cell(24,6, number_format($total_recebido_recebido, 2, ',', '.'),1,0,'C',1);
      
      }
      
      if ($chkSaldoReceber == 1)
      {
        
        $pdf->Cell(24,6, number_format($saldo_recebido_receber, 2, ',', '.'),1,0,'C',1);
        
      }
    
    }
    
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0,6, 'Totais','B');
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 10);
    //Define a cor RGB do fundo da celula
    $pdf->SetFillColor(178,178,178);
    //Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
    $pdf->Cell($colspan,6, 'Total Geral: ',1,0,'R',1);
    
    if ($chkValorContrato == 1)
    {
      
      $pdf->Cell(24,6, number_format($total_geral_original, 2, ',', '.'),1,0,'C',1);
      
    }
    
    if ($chkValorBoleto == 1)
    {
    
      $pdf->Cell(24,6, number_format($total_geral_boletos, 2, ',', '.'),1,0,'C',1);
      
    }
    
    if ($chkValorMultaJuros == 1)
    {
    
      $pdf->Cell(24,6, number_format($total_geral_multa_juros, 2, ',', '.'),1,0,'C',1);
      
    }
    
    if ($chkValorReceber == 1)
    {
    
      $pdf->Cell(24,6, number_format($total_geral_receber, 2, ',', '.'),1,0,'C',1);
      
    }
    
    if ($chkValorRecebido == 1)
    {
    
      $pdf->Cell(24,6, number_format($total_geral_recebido, 2, ',', '.'),1,0,'C',1);
    
    }
    
    if ($chkSaldoReceber == 1)
    {
    
      $pdf->Cell(24,6, number_format($saldo_geral_receber, 2, ',', '.'),1,0,'C',1);
      
    }

	//Fecha o IF
	};

//Gera o PDF
$pdf->Output();
?>