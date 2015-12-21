<?php
###########
## Módulo de relatório geral de contas a pagar
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

include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "../include/ManipulaDatas.php";

//Recupera os valores para filtragem
$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];
$dataIni = DataMySQLInserir($_GET[DataIni]);
$dataFim = DataMySQLInserir($_GET[DataFim]);

//Verifica a situação informada
if ($_GET["TipoSituacao"] > 0 AND $_GET["TipoSituacao"] != 4)
{
	
	//Efetua o switch da situacao informada
	switch ($_GET["TipoSituacao"]) 
	{
		
		//Se for 1 então é visualização em aberto
		case 1: $texto_situacao = " e com Situação: Em aberto"; break;		
		//Se for 2 então é visualização das pagas
		case 2: $texto_situacao = " e com Situação: Pagas"; break;
		//Se for 3 então é visualização das vencidas
		case 3: $texto_situacao = " e com Situação: Vencidas"; break;
	
	}	
		
	$TipoSituacao = $_GET["TipoSituacao"];
	$TextoSituacao = " AND con.situacao = '$TipoSituacao'";
	
}

//Verifica a regiao informada
if ($_GET["Regiao"] > 0)
{
	
	$Regiao = $_GET["Regiao"];
	$WhereRegiao = " AND con.regiao_id = $Regiao";
	
} 

//Verifica se foi informado alguma data para filtrar junto
if ($dataIni != 0) 
{
	
	$TextoFiltraData = "E com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
	$TextoSQLData = "	 AND con.data_vencimento >= '$dataIni' AND con.data_vencimento <= '$dataFim' ";

}

//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) 
{
	//Se for 1 então é visualização por data
	case 1: 
		//Monta o sql
		$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.data_vencimento >= '$dataIni' AND con.data_vencimento <= '$dataFim' $TextoSituacao";
	break;

	//Se for 2 então é visualização por grupo
	case 2: 
		//Monta as variáveis
		$grupoId = $_GET[GrupoId];
		
		//Monta o sql
		$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.grupo_conta_id = '$grupoId' $TextoSQLData $TextoSituacao";		
	break;
	
	//Se for 3 então é visualização por evento
	case 3: 
		//Monta as variáveis
		$EventoId = $_GET[EventoId];
		
		//Monta o sql
		$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.evento_id = '$EventoId' $TextoSQLData $TextoSituacao";		
	break;		

	//Se for 4 então é visualização por situacao
	case 4:
		//Monta as variáveis
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
			//Se for 1 então é visualização em aberto
			case 1:
				$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.situacao = '$TipoSituacao' $TextoSQLData";		
			break;		
			//Se for 2 então é visualização das pagas
			case 2:
				$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.situacao = '$TipoSituacao' $TextoSQLData";
			break;
			//Se for 3 então é visualização das vencidas
			case 3:
				$data_base_vencimento = date("Y-m-d", mktime());
				$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.situacao = '1' AND con.data_vencimento < '$data_base_vencimento' $TextoSQLData";
			break;
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
				$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.tipo_pessoa = '1' AND con.pessoa_id = '$PessoaId' $TextoSQLData $TextoSituacao";		
			break;		
			//Se for 2 então é fornecedor
			case 2:
				$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.tipo_pessoa = '2' AND con.pessoa_id = '$PessoaId' $TextoSQLData $TextoSituacao";
			break;
			//Se for 3 então é colaborador
			case 3:
				$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.tipo_pessoa = '3' AND con.pessoa_id = '$PessoaId' $TextoSQLData $TextoSituacao";
			break;
		}
		
	break;
	
	//Se for 6 então é visualização por subgrupo
	case 6: 
		//Monta as variáveis
		$subgrupoId = $_GET[SubgrupoId];
		
		//Monta o sql
		$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.subgrupo_conta_id = '$subgrupoId' $TextoSQLData $TextoSituacao";		
	break;

	//Se for 7 então é visualização agrupada
	case 7: 
		//Monta as variáveis
		$grupoId = $_GET[GrupoId];
		$subgrupoId = $_GET[SubgrupoId];
		$categoriaId = $_GET[CategoriaId];
		
		//Monta o sql
		$where_filtragem = "WHERE con.empresa_id = '$empresaId' AND con.grupo_conta_id = '$grupoId' AND con.subgrupo_conta_id = '$subgrupoId' AND con.categoria_id = '$categoriaId' $TextoSQLData $TextoSituacao";		
	break;
}

$sql = "SELECT 
					con.id,
					con.data,
					con.tipo_pessoa,
					con.pessoa_id,
					con.grupo_conta_id,
					con.subgrupo_conta_id,
					con.categoria_id,		  
					con.descricao,
					con.valor,
					con.data_vencimento,
					con.situacao,
					con.data_pagamento,
					con.tipo_pagamento,
					con.numero_cheque,
					con.valor_pago,
					cat.nome as categoria_nome,
					gru.nome as grupo_nome,
					sub.nome as subgrupo_nome

				FROM 
					contas_pagar con
				LEFT OUTER JOIN 
					usuarios usu_cad ON con.cadastro_operador_id = usu_cad.usuario_id 
				LEFT OUTER JOIN 
					usuarios usu_alt ON con.alteracao_operador_id = usu_alt.usuario_id
				LEFT OUTER JOIN 
					categoria_conta cat ON con.categoria_id = cat.id 
				LEFT OUTER JOIN 
					grupo_conta gru ON con.grupo_conta_id = gru.id 							 
				LEFT OUTER JOIN 
					subgrupo_conta sub ON con.subgrupo_conta_id = sub.id 
		
				$where_filtragem 
				$WhereRegiao
			
			ORDER BY 
				con.data_vencimento, 
				con.descricao";

$query = mysql_query($sql);
	  	
$registros = mysql_num_rows($query);  

class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{
	  
		//Pega a data inicial pra ver se veio vazia
		$dataIni = DataMySQLInserir($_GET[DataIni]);
		  
		$empresaNome = $_GET["EmpresaNome"];
		
		//Verifica a regiao informada
		if ($_GET["Regiao"] > 0)
		{
			
			$Regiao = $_GET["Regiao"];
			
			//Recupera o nome do grupo selecionado
			$sql_regiao = mysql_query("SELECT nome FROM regioes WHERE id = '$Regiao'");
			
			//Monta o array com os dados
			$dados_regiao = mysql_fetch_array($sql_regiao);
			
			$desc_regiao = "Regional: " . $dados_regiao["nome"];
			
		} 
		
		//Monta o switch para criar o texto da filtragem
		switch ($_GET[TipoListagem]) 
		{
		
			//Se for 1 então é visualização por data
			case 1: 
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a pagar com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim] $texto_situacao";
			break;

			//Se for 2 então é visualização por grupo
			case 2: 
				//Monta as variáveis
				$grupoId = $_GET[GrupoId];
					
				//Recupera o nome do grupo selecionado
				$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = '$grupoId'");
				
				//Monta o array com os dados
				$dados_grupo = mysql_fetch_array($sql_grupo);
				
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a pagar do grupo: $dados_grupo[nome]";
			
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim] $texto_situacao";
				
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
				$desc_filtragem = "Contas a pagar do evento: $dados_evento[nome]";
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim] $texto_situacao";
				
				}
			break;	

			//Se for 4 então é visualização por situacao
			case 4:
				//Monta as variáveis
				$TipoSituacao = $_GET[TipoSituacao];
				
				//Efetua o switch da situacao informada
				switch ($TipoSituacao) 
				{		
					//Se for 1 então é visualização em aberto
					case 1:
						$texto_situacao = "Em aberto";
					break;		
					//Se for 2 então é visualização das pagas
					case 2:
						$texto_situacao = "Pagas";
					break;
					//Se for 3 então é visualização das vencidas
					case 3:
						$texto_situacao = "Vencidas";
					break;
				}
			
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a pagar com situacao: $texto_situacao";
			
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
						$texto_pessoa = "Cliente $nome_cliente[nome]";
					break;		
					//Se for 2 então é visualização das pagas
					case 2:
						//Recupera o nome do fornecedor
						$query_fornecedor = mysql_query("SELECT nome FROM fornecedores WHERE id = '$PessoaId'");
						$nome_fornecedor = mysql_fetch_array($query_fornecedor);
						$texto_pessoa = "Fornecedor $nome_fornecedor[nome]";
					break;
					//Se for 3 então é visualização das vencidas
					case 3:
						//Recupera o nome do colaborador
						$query_colaborador = mysql_query("SELECT nome FROM colaboradores WHERE id = '$PessoaId'");
						$nome_colaborador = mysql_fetch_array($query_colaborador);
						$texto_pessoa = "Colaborador $nome_colaborador[nome]";
					break;
				}
			
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a pagar do $texto_pessoa";
				
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
				
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim] $texto_situacao";
				}
			break;	

			//Se for 6 então é visualização por subgrupo
			case 6: 
				
				//Monta as variáveis
				$subgrupoId = $_GET[SubgrupoId];
					
				//Recupera o nome do subgrupo selecionado
				$sql_subgrupo = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$subgrupoId'");
				
				//Monta o array com os dados
				$dados_subgrupo = mysql_fetch_array($sql_subgrupo);
				
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a pagar da conta-caixa: $dados_subgrupo[nome]";
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					
					$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim] $texto_situacao";
				
				}
			break;
		
			//Se for 7 então é visualização por subgrupo
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
				
				//Recupera o nome da categoria
				$sql_categoria = mysql_query("SELECT nome FROM categoria_conta WHERE id = '$categoriaId'");
				
				//Monta o array com os dados
				$dados_categoria = mysql_fetch_array($sql_categoria);
				
				//Monta a descrição a exibir
				$desc_filtragem = "Contas a pagar do centro de custo: $dados_grupo[nome]\nsub-grupo: $dados_subgrupo[nome]\ncategoria: $dados_categoria[nome]";
				//Se tem filtragem acrescenta as datas ao texto
				if ($dataIni != 0) 
				{
					
					$desc_filtragem .= "\ne com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim] $texto_situacao";
				
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
		$this->Cell(0,6,'Relação Geral de Contas a Pagar');
		$this->SetFont('Arial','',9);
		$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
		$this->Ln(5);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(19,6,'Filtragem:');
		$this->SetFont('Arial', '', 10);
		$this->Multicell(0,6, $desc_filtragem);
		
		if ($desc_regiao != '')
		{
		
			$this->SetFont('Arial', 'B', 10);
			$this->Cell(19,6,'Região:');
			$this->SetFont('Arial', '', 10);
			$this->Cell(0,5, $desc_regiao);
		
		}
		
		//Line break
		$this->Ln(8);
	}

	//Rodapé do Relatório
	function Footer()
	{
		
		$usuarioNome = $_GET["UsuarioNome"];
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
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Relação Geral de Contas a Pagar');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('');

//Títulos das colunas
$pdf->SetFont('Arial', 'B', 10);
//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);
//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(106,6, 'Dados do Sacado/Descrição da Conta a Pagar',1,0,'L',1);
$pdf->Cell(18,6, 'Vencto',1,0,'C',1);
$pdf->Cell(24,6, 'Valor',1,0,'R',1);
$pdf->Cell(24,6, 'A Pagar',1,0,'R',1);
$pdf->Cell(0,6, 'Situação',1,0,'R',1);

//Seta a variável do total a pagar zerado
$total_pagar = 0;
$saldo_pagar = 0;


//Percorre o array dos dados
while ($dados = mysql_fetch_array($query))
{

	//Efetua o switch para recuperar o nome do sacado
	switch ($dados[tipo_pessoa]) 
	{
		
		case 1: //Se for cliente
			$sql = mysql_query("SELECT nome FROM clientes WHERE id = '$dados[pessoa_id]'");
			$desc_pessoa = "Cliente";
			$dados_pessoa = mysql_fetch_array($sql);				
		break;
		case 2: //Se for fornecedor
			$sql = mysql_query("SELECT nome FROM fornecedores WHERE id = '$dados[pessoa_id]'");
			$desc_pessoa = "Fornecedor";
			$dados_pessoa = mysql_fetch_array($sql);				
		break;
		case 3: //Se for colaborador
			$sql = mysql_query("SELECT nome FROM colaboradores WHERE id = '$dados[pessoa_id]'");
			$desc_pessoa = "Colaborador";
			$dados_pessoa = mysql_fetch_array($sql);				
		break;			
	
	}		
	
	//Efetua o switch para o campo de situação
	switch ($dados[situacao]) 
	{
		
		case 1: $texto_situacao = "Em aberto";	break;
		case 2: $texto_situacao = "Pago";	break;     	
	
	}
  
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(106,5, $dados_pessoa['nome']);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(18,5, DataMySQLRetornar($dados['data_vencimento']),0,0,"C");
	$pdf->Cell(24,5, "R$ " . number_format($dados['valor'], 2, ",", "."),0,0,"R");
	$pdf->Cell(24,5, "R$ " . number_format($dados[valor] - $dados[valor_pago], 2, ",", "."),0,0,"R");
	$pdf->Cell(0,5, $texto_situacao,0,0,"R");
	$pdf->ln();
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(0,3, $dados['descricao'],"B");
	//Acumula o valor a pagar
	$total_pagar = $total_pagar + $dados['valor'];
	$saldo_pagar = $saldo_pagar + ($dados[valor] - $dados[valor_pago]);	  

}

$pdf->SetFont('Arial', 'B', 8);
$pdf->ln();
$pdf->Cell(0,6, 'Total de registros listados: ' . $registros,'T');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetX(116);
$pdf->Cell(18,6, 'Total:',0,0,'R');
$pdf->Cell(24,6, "R$ " . number_format($total_pagar, 2, ",", "."),0,0,'R');
$pdf->Cell(24,6, "R$ " . number_format($saldo_pagar, 2, ",", "."),0,0,'R');

//Gera o PDF
$pdf->Output();
?>