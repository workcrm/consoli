<?php
###########
## Módulo de relatório de eventos por caixa - SINTETICO
## Criado: 09/06/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

require('../fpdf/fpdf.php');

include '../conexao/ConexaoMySQL.php';

//Verifica se a funcão já foi declarada
if (function_exists('DataMySQLRetornar') == false) 
{

	//Inclui o arquivo para manipulação de datas
	include '../include/ManipulaDatas.php';

}

//Recupera os valores para filtragem
$empresaId = $_GET['EmpresaId'];
$empresaNome = $_GET['EmpresaNome'];
$usuarioNome = $_GET['UsuarioNome'];
$DataIni = DataMySQLInserir($_GET['DataIni']);
$DataFim = DataMySQLInserir($_GET['DataFim']);
$TipoLancamento = $_GET["TipoLancamento"];
$DataProcura = $_GET['DataProcura'];
$EventoId = $_GET['EventoId'];
$GrupoId = $_GET['GrupoId'];
$RegiaoId = $_GET['Regiao'];

if ($EventoId > 0)
{

	$filtra_evento_entrada = "AND rec.evento_id = $EventoId";
	$filtra_evento_saida = "AND pag.evento_id = $EventoId";
	
	//Busca o total de formandos do evento
	$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = $EventoId");

	//Percorre os registros
	while ($dados_evento = mysql_fetch_array($sql_evento))
	{
	
		$NomeEvento = $dados_evento["nome"];
	
	}

}

if ($GrupoId > 0)
{

	$filtra_grupo_entrada = "AND rec.grupo_conta_id = $GrupoId";
	$filtra_grupo_saida = "AND pag.grupo_conta_id = $GrupoId";
	
	//Busca o total de formandos do evento
	$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = $GrupoId");

	//Percorre os registros
	while ($dados_grupo = mysql_fetch_array($sql_grupo))
	{
	
		$NomeGrupo = $dados_grupo["nome"];
	
	}

}

else

{

	$filtra_grupo_entrada = "AND grupo_conta_id <> 0";

}


//Verifica a regiao informada
if ($RegiaoId > 0)
{
	
	$filtra_regiao_entrada = " AND eve.regiao_id = $RegiaoId";
	$filtra_regiao_saida = " AND pag.regiao_id = $RegiaoId";
	
} 

class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{

		$DataIni = $_GET['DataIni'];
		$DataFim = $_GET['DataFim']; 
		$DataProcura = $_GET['DataProcura'];
		$empresaNome = $_GET['EmpresaNome'];
		
		global $GrupoId, $NomeGrupo, $EventoId, $NomeEvento;
		
		//Verifica o evento informado
		if ($_GET["Evento"] > 0)
		{
			
			$Evento = $_GET["Evento"];
			
			//Recupera o nome do grupo selecionado
			$sql_evento = mysql_query("SELECT id, nome FROM evento WHERE id = '$Evento'");
			
			//Monta o array com os dados
			$dados_evento = mysql_fetch_array($sql_evento);
			
			$desc_evento = "Evento: (" . $dados_evento["id"] . ') - ' . $dados_evento["nome"];
			
		}
		
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
		
		//Ajusta a fonte
		$this->SetFont('Arial','',9);
		//Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->SetFont('Arial','B',15);
		$this->Cell(0,6,'Relação de Lançamentos no Caixa - Sintético');
		
		//caso for pela data da emissao da conta
		if ($DataProcura == 1)
		{
		
			$this->Ln();
			$this->SetFont('Arial','B',15);
			$this->Cell(0,6,'Por Data Recebimento/Pagamento');
			$this->SetFont('Arial','',9);
		
		}
		
		else if ($DataProcura == 2)
		{
		
			$this->Ln();
			$this->SetFont('Arial','B',15);
			$this->Cell(0,6,'Por Data de Emissão');
			$this->SetFont('Arial','',9);
		
		}
		
		else if ($DataProcura == 3)
		{
		
			$this->Ln();
			$this->SetFont('Arial','B',15);
			$this->Cell(0,6,'Por Data de Vencimento');
			$this->SetFont('Arial','',9);
		
		}
		
		$this->Ln();
		$this->SetFont('Arial','',11);
		$this->Cell(0,5,'Período: ' . $DataIni . ' a ' . $DataFim);
		$this->SetFont('Arial','',9);
		
		if ($EventoId > 0)
		{
		
			$this->Ln();
			$this->SetFont('Arial','',11);
			$this->Cell(0,5,'Evento: (' . $EventoId . ') - ' . $NomeEvento);
			$this->SetFont('Arial','',9);
		
		}
		
		if ($GrupoId > 0)
		{
		
			$this->Ln();
			$this->SetFont('Arial','',11);
			$this->Cell(0,5,'Centro de Custo: ' . $NomeGrupo);
			$this->SetFont('Arial','',9);
		
		}
		
		
		if ($desc_regiao != '')
		{
		
			$this->Ln();
			$this->SetFont('Arial', '', 11);
			$this->Cell(0,5, $desc_regiao);
		
		}

		//Line break
		$this->Ln(6);
		
		//Títulos das colunas
		$this->SetFont('Arial', 'B', 10);
		//Define a cor RGB do fundo da celula
		$this->SetFillColor(178,178,178);

		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$this->ln(2);
		$this->Cell(0,6, ' ',1,0,'',1);
		$this->SetX(14);
		$this->Cell(150,6, 'Centro de Custo/Conta-Caixa','TB',0,'L',1);
		$this->Cell(0,6, 'Valor R$ ','TBR',0,'R',1);
		
		$this->Ln(6);
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
$pdf = new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos');
$pdf->SetAuthor($usuarioNome . ' - ' . $empresaNome);
$pdf->SetTitle('Relação de Lançamentos no Caixa');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('P');

//Verifica se deve imprimir as entradas
if ($TipoLancamento == 0 OR $TipoLancamento == 1)
{
	
	//caso for pela data do recebimento
	if ($DataProcura == 1)
	{
	
		//Busca o total de formandos do evento
		$sql_entradas = mysql_query("SELECT 
									sum(ent.total_recebido) AS total,
									rec.grupo_conta_id,
									rec.subgrupo_conta_id,
									ccu.nome AS centro_custo_nome,
									ccx.nome AS conta_caixa_nome
									FROM contas_receber_recebimento ent
									LEFT OUTER JOIN contas_receber rec ON rec.id = ent.conta_receber_id
									LEFT OUTER JOIN grupo_conta ccu ON ccu.id = rec.grupo_conta_id
									LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
									LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
									WHERE ent.data_recebimento between '$DataIni' AND '$DataFim'
									$filtra_regiao_entrada
									$filtra_grupo_entrada
									$filtra_evento_entrada								
									GROUP BY ccu.nome, ccx.nome
									ORDER BY ccu.nome, ccx.nome");

		$registros_entradas = mysql_num_rows($sql_entradas);

		$ccu_nome = 'a';

		$pdf->ln(2);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0,5, "ENTRADAS",0,0,'L');
		$pdf->SetFont('Arial', '', 9);

		//Percorre os registros
		while ($dados_entradas = mysql_fetch_array($sql_entradas))
		{

			
			//Verifica se ainda é a mesmo centro de custo
			if ($dados_entradas["centro_custo_nome"] != $ccu_nome)
			{
			
				$pdf->ln(8);	
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->SetX(14);
				
				//Verifica se o nome está vazio
				if ($dados_entradas["centro_custo_nome"] == '')
				{
				
					$pdf->Cell(60,5, "Centro de Custo:  Não Definido",0,0,'L');
				
				}
				
				else
				{
				
					$pdf->Cell(60,5, "Centro de Custo: " . $dados_entradas["centro_custo_nome"],0,0,'L');
					
				}
				
				$CentroCusto = $dados_entradas["grupo_conta_id"];
				
				//Busca o total de formandos do evento geral por centro de custo
				$sql_entradas_total = mysql_query("SELECT 
											sum(ent.total_recebido) AS total,
											rec.grupo_conta_id,
											rec.subgrupo_conta_id,
											ccu.nome AS centro_custo_nome,
											ccx.nome AS conta_caixa_nome
											FROM contas_receber_recebimento ent
											LEFT OUTER JOIN contas_receber rec ON rec.id = ent.conta_receber_id
											LEFT OUTER JOIN grupo_conta ccu ON ccu.id = rec.grupo_conta_id
											LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
											LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
											WHERE ent.data_recebimento between '$DataIni' AND '$DataFim'
											AND rec.grupo_conta_id = $CentroCusto
											$filtra_regiao_entrada
											$filtra_grupo_entrada
											$filtra_evento_entrada
											GROUP BY ccu.nome");
											
											

				$registros_entradas_total = mysql_num_rows($sql_entradas_total);

				//Percorre os registros
				while ($dados_entradas_total = mysql_fetch_array($sql_entradas_total))
				{
				
					$pdf->SetFont('Arial', 'B', 9);
					$pdf->Cell(0,5, number_format($dados_entradas_total["total"],2,",","."),0,0,'R');
					$pdf->SetFont('Arial', '', 9);
				}
			}
			
			$pdf->ln();
			$pdf->SetX(18);
			
			//Verifica se o nome está vazio
			if ($dados_entradas["conta_caixa_nome"] == '')
			{
			
				$pdf->Cell(60,5, "Não Definido","B",0,'L');
			
			}
			
			else
			{
			
				$pdf->Cell(60,5, $dados_entradas["conta_caixa_nome"],"B",0,'L');
				
			}
			
			$pdf->Cell(0,5, number_format($dados_entradas["total"],2,",","."),"B",0,'R');
			
			$ccu_nome = $dados_entradas["centro_custo_nome"];
			
			$total_entradas = $total_entradas + $dados_entradas["total"];
		
		}
	}
	
	//caso for pela data da emissao
	if ($DataProcura == 2)
	{
	
		//Busca o total de formandos do evento
		$sql_entradas = mysql_query("SELECT 
									sum(rec.valor) AS total,
									rec.grupo_conta_id,
									rec.subgrupo_conta_id,
									ccu.nome AS centro_custo_nome,
									ccx.nome AS conta_caixa_nome
									FROM contas_receber rec
									LEFT OUTER JOIN grupo_conta ccu ON ccu.id = rec.grupo_conta_id
									LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
									LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
									WHERE rec.data between '$DataIni' AND '$DataFim'
									$filtra_regiao_entrada
									$filtra_grupo_entrada
									$filtra_evento_entrada
									GROUP BY ccx.nome
									ORDER BY ccx.nome");

		$registros_entradas = mysql_num_rows($sql_entradas);

		$ccu_nome = 'a';

		$pdf->ln(2);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0,5, "ENTRADAS",0,0,'L');
		$pdf->SetFont('Arial', '', 9);

		//Percorre os registros
		while ($dados_entradas = mysql_fetch_array($sql_entradas))
		{
			
			$pdf->ln();
			$pdf->SetX(18);
			
			//Verifica se o nome está vazio
			if ($dados_entradas["conta_caixa_nome"] == '')
			{
			
				$pdf->Cell(60,5, "Não Definido","B",0,'L');
			
			}
			
			else
			{
			
				$pdf->Cell(60,5, $dados_entradas["conta_caixa_nome"],"B",0,'L');
				
			}
			
			$pdf->Cell(0,5, number_format($dados_entradas["total"],2,",","."),"B",0,'R');
			
			$total_entradas = $total_entradas + $dados_entradas["total"];
		}
	
	}
	
	//caso for pela data da vencimento
	if ($DataProcura == 3)
	{
	
		//Busca o total de formandos do evento
		$sql_entradas = mysql_query("SELECT 
									sum(rec.valor) AS total,
									rec.grupo_conta_id,
									rec.subgrupo_conta_id,
									ccu.nome AS centro_custo_nome,
									ccx.nome AS conta_caixa_nome
									FROM contas_receber rec
									LEFT OUTER JOIN grupo_conta ccu ON ccu.id = rec.grupo_conta_id
									LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
									LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
									WHERE rec.data_vencimento between '$DataIni' AND '$DataFim'
									$filtra_regiao_entrada
									$filtra_grupo_entrada
									$filtra_evento_entrada
									GROUP BY ccx.nome
									ORDER BY ccx.nome");

		$registros_entradas = mysql_num_rows($sql_entradas);

		$ccu_nome = 'a';

		$pdf->ln(2);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0,5, "ENTRADAS",0,0,'L');
		$pdf->SetFont('Arial', '', 9);

		//Percorre os registros
		while ($dados_entradas = mysql_fetch_array($sql_entradas))
		{
			
			$pdf->ln();
			$pdf->SetX(18);
			
			//Verifica se o nome está vazio
			if ($dados_entradas["conta_caixa_nome"] == '')
			{
			
				$pdf->Cell(60,5, "Não Definido","B",0,'L');
			
			}
			
			else
			{
			
				$pdf->Cell(60,5, $dados_entradas["conta_caixa_nome"],"B",0,'L');
				
			}
			
			$pdf->Cell(0,5, number_format($dados_entradas["total"],2,",","."),"B",0,'R');
			
			$total_entradas = $total_entradas + $dados_entradas["total"];
		}
	
	}

}

//Verifica se deve imprimir as saidas
if (($TipoLancamento == 0 OR $TipoLancamento == 2) AND $DataProcura == 1)
{

	$pdf->ln(10);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->Cell(0,7, "SAÍDAS",'T',0,'L');
	$pdf->SetFont('Arial', '', 9);

	//Busca as saidas do periodo
	$sql_saidas = mysql_query("SELECT 
								sum(sai.total_pago) AS total,
								pag.grupo_conta_id,
								pag.subgrupo_conta_id,
								ccu.nome AS centro_custo_nome,
								ccx.nome AS conta_caixa_nome
								FROM contas_pagar_pagamento sai
								LEFT OUTER JOIN contas_pagar pag ON pag.id = sai.conta_pagar_id
								LEFT OUTER JOIN grupo_conta ccu ON ccu.id = pag.grupo_conta_id
								LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
								LEFT OUTER JOIN eventos eve ON eve.id = pag.evento_id
								WHERE sai.data_pagamento between '$DataIni' AND '$DataFim'
								$filtra_regiao_saida
								$filtra_grupo_saida
								$filtra_evento_saida
								GROUP BY ccu.nome, ccx.nome
								ORDER BY ccu.nome, ccx.nome");

	$registros_saidas = mysql_num_rows($sql_saidas);

	$ccu_nome = 'a';

	//Percorre os registros
	while ($dados_saidas = mysql_fetch_array($sql_saidas))
	{

		
		//Verifica se ainda é a mesmo centro de custo
		if ($dados_saidas["centro_custo_nome"] != $ccu_nome)
		{
		
			$pdf->ln(8);	
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->SetX(14);
			
			//Verifica se o nome está vazio
			if ($dados_saidas["centro_custo_nome"] == '')
			{
			
				$pdf->Cell(0,5, "Centro de Custo: Não Definido",0,0,'L');
			
			}
			
			else
			{
			
				$pdf->Cell(0,5, "Centro de Custo: " . $dados_saidas["centro_custo_nome"],0,0,'L');
				
			}
			
			$CentroCusto = $dados_saidas["grupo_conta_id"];
			
			//Busca o total de formandos do evento geral por centro de custo
			$sql_saidas_total = mysql_query("SELECT 
										sum(sai.total_pago) AS total,
										pag.grupo_conta_id,
										pag.subgrupo_conta_id,
										ccu.nome AS centro_custo_nome,
										ccx.nome AS conta_caixa_nome
										FROM contas_pagar_pagamento sai
										LEFT OUTER JOIN contas_pagar pag ON pag.id = sai.conta_pagar_id
										LEFT OUTER JOIN grupo_conta ccu ON ccu.id = pag.grupo_conta_id
										LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
										LEFT OUTER JOIN eventos eve ON eve.id = pag.evento_id
										WHERE sai.data_pagamento between '$DataIni' AND '$DataFim'
										AND pag.grupo_conta_id = $CentroCusto
										$filtra_regiao_saida
										$filtra_grupo_saida
										$filtra_evento_saida										
										GROUP BY ccu.nome");
										
										

			$registros_saidas_total = mysql_num_rows($sql_saidas_total);

			//Percorre os registros
			while ($dados_saidas_total = mysql_fetch_array($sql_saidas_total))
			{
			
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->Cell(0,5, number_format($dados_saidas_total["total"],2,",","."),0,0,'R');
				$pdf->SetFont('Arial', '', 9);
			}
		}
		
		$pdf->ln();
		$pdf->SetX(18);
		
		//Verifica se o nome está vazio
		if ($dados_saidas["conta_caixa_nome"] == '')
		{
		
			$pdf->Cell(60,5, "Não Definido","B",0,'L');
		
		}
		
		else
		{
		
			$pdf->Cell(60,5, $dados_saidas["conta_caixa_nome"],"B",0,'L');
			
		}
		
		$pdf->Cell(0,5, number_format($dados_saidas["total"],2,",","."),"B",0,'R');
		
		$ccu_nome = $dados_saidas["centro_custo_nome"];
		
		$total_saidas = $total_saidas + $dados_saidas["total"];
	}

}

$pdf->SetFont('Arial', 'B', 9);

//Verifica se deve imprimir as entradas
if (($TipoLancamento == 0 OR $TipoLancamento == 1) AND $DataProcura == 1)
{	

	//Busca o total de entradas por tipo de recebimento
	$sql_total_entradas = mysql_query("SELECT 
										sum(ent.total_recebido) AS total,
										ent.tipo_recebimento,
										rec.id
										FROM contas_receber_recebimento ent
										LEFT OUTER JOIN contas_receber rec ON rec.id = ent.conta_receber_id
										LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
										WHERE ent.data_recebimento BETWEEN '$DataIni' AND '$DataFim'
										$filtra_regiao_entrada
										$filtra_grupo_entrada
										$filtra_evento_entrada
										GROUP BY ent.tipo_recebimento
										ORDER BY ent.tipo_recebimento");

	$registros_total_entradas = mysql_num_rows($sql_total_entradas);

	//Percorre os registros
	while ($dados_total_entradas = mysql_fetch_array($sql_total_entradas))
	{
		
		//Efetua o switch do tipo de recebimento
		switch ($dados_total_entradas['tipo_recebimento']) 
		{
			case 1: $nome_tipo = 'Recebimentos em Dinheiro';			break;
			case 2: $nome_tipo = 'Recebimentos em Cheque';				break;       	
			case 3: $nome_tipo = 'Recebimentos em Cheque de Terceiro';	break;
			case 4: $nome_tipo = 'Recebimentos em Boleto Bancário';		break;
		}
		
		$pdf->ln();
		$pdf->Cell(160,5, $nome_tipo,0,0,'R');
		$pdf->Cell(0,5, number_format($dados_total_entradas["total"],2,",","."),0,0,'R');

	}

	$pdf->ln();
	$pdf->Cell(160,5, "TOTAL DE ENTRADAS:",'B',0,'R');
	$pdf->Cell(0,5, number_format($total_entradas,2,",","."),'B',0,'R');

}

//Verifica se deve imprimir as saidas
if (($TipoLancamento == 0 OR $TipoLancamento == 2) AND $DataProcura == 1)
{

	//Busca o total de saidas por tipo de pagamento
	$sql_total_saidas = mysql_query("SELECT 
									sum(ent.total_pago) AS total,
									ent.tipo_pagamento,
									pag.id
									FROM contas_pagar_pagamento ent
									LEFT OUTER JOIN contas_pagar pag ON pag.id = ent.conta_pagar_id
									LEFT OUTER JOIN eventos eve ON eve.id = pag.evento_id
									WHERE ent.data_pagamento between '$DataIni' AND '$DataFim'
									$filtra_regiao_saida
									$filtra_grupo_saida
									$filtra_evento_saida
									GROUP BY ent.tipo_pagamento
									ORDER BY ent.tipo_pagamento");

	$registros_total_saidas = mysql_num_rows($sql_total_saidas);

	//Percorre os registros
	while ($dados_total_saidas = mysql_fetch_array($sql_total_saidas))
	{
		
		//Efetua o switch do tipo de pagamento
		switch ($dados_total_saidas['tipo_pagamento']) 
		{
			case 1: $nome_tipo = 'Pagamentos em Dinheiro';				break;
			case 2: $nome_tipo = 'Pagamentos em Cheque';				break;       	
			case 3: $nome_tipo = 'Pagamentos em Cheque de Terceiro';	break;
		}
		
		$pdf->ln();
		$pdf->Cell(160,5, $nome_tipo,0,0,'R');
		$pdf->Cell(0,5, number_format($dados_total_saidas["total"],2,",","."),0,0,'R');

	}


	$pdf->ln();
	$pdf->Cell(160,5, "TOTAL DE SAIDAS:",0,0,'R');
	$pdf->Cell(0,5, number_format($total_saidas,2,",","."),0,0,'R');

}

//Verifica se deve imprimir as saidas
if ($TipoLancamento == 0)
{

	$pdf->ln();
	$pdf->Cell(160,5, "SALDO:","T" ,0,'R');
	$total_saldo = $total_entradas - $total_saidas;
	$pdf->Cell(0,5, number_format($total_saldo,2,",","."),"T",0,'R');

}
//Gera o PDF
$pdf->Output();
?>