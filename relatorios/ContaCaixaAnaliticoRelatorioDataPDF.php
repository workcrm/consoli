<?php
###########
## Módulo de relatório de eventos por conta caixa - analitico
## Criado: 16/06/2011 - Maycon Edinger
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
$TipoLancamento = $_GET['TipoLancamento'];
$ContaCaixaId = $_GET['ContaCaixaId'];
$DataProcura = $_GET['DataProcura'];

class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{

		$DataIni = $_GET['DataIni'];
		$DataFim = $_GET['DataFim']; 
		$DataProcura = $_GET['DataProcura'];
   
		$empresaNome = $_GET['EmpresaNome'];
		//Ajusta a fonte
		$this->SetFont('Arial','',9);
		//Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->SetFont('Arial','B',15);
		$this->Cell(0,6,'Relação de Lançamentos no Caixa - Registro por Conta-Caixa');
		
		//caso for pela data da emissao da conta
		if ($DataProcura == 1)
		{
		
			$this->Ln();
			$this->SetFont('Arial','B',15);
			$this->Cell(0,6,'Por data Recebimento/Pagamento');
			$this->SetFont('Arial','',9);
		
		}
		
		else if ($DataProcura == 2)
		{
		
			$this->Ln();
			$this->SetFont('Arial','B',15);
			$this->Cell(0,6,'Por data de Emissão');
			$this->SetFont('Arial','',9);
		
		}
		
		else if ($DataProcura == 3)
		{
		
			$this->Ln();
			$this->SetFont('Arial','B',15);
			$this->Cell(0,6,'Por data de Vencimento');
			$this->SetFont('Arial','',9);
		
		}
		
		$this->Ln();
		$this->SetFont('Arial','',11);
		$this->Cell(0,6,'Período: ' . $DataIni . ' a ' . $DataFim);

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
		$this->Cell(150,6, 'Conta-Caixa/Centro de Custo','TB',0,'L',1);
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
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos');
$pdf->SetAuthor($usuarioNome . ' - ' . $empresaNome);
$pdf->SetTitle('Relação de Lançamentos por Conta-caixa');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('P');

if ($TipoLancamento == 1)
{
	
	//Se e para procurar pela data do recebimento
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
									WHERE ent.data_recebimento between '$DataIni' AND '$DataFim'
									AND rec.subgrupo_conta_id = $ContaCaixaId
									GROUP BY ccx.nome, ccu.nome
									ORDER BY ccx.nome, ccu.nome");

		$registros_entradas = mysql_num_rows($sql_entradas);

		$ccx_nome = 'a';

		$pdf->SetFont('Arial', '', 9);

		//Percorre os registros
		while ($dados_entradas = mysql_fetch_array($sql_entradas))
		{

			
			//Verifica se ainda é a mesmo centro de custo
			if ($dados_entradas["conta_caixa_nome"] != $ccx_nome)
			{
			
				$pdf->ln();	
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->SetX(14);
				
				//Verifica se o nome está vazio
				if ($dados_entradas["conta_caixa_nome"] == '')
				{
				
					$pdf->Cell(0,5, "Conta-Caixa Não Definido",0,0,'L');
				
				}
				
				else
				{
				
					$pdf->Cell(0,5, "Conta-Caixa: " . $dados_entradas["conta_caixa_nome"],0,0,'L');
					
				}
				
				$pdf->SetFont('Arial', '', 9);
			}
			
			$pdf->ln();
			$pdf->SetX(18);
			
			//Verifica se o nome está vazio
			if ($dados_entradas["centro_custo_nome"] == '')
			{
			
				$pdf->Cell(60,5, "Não Definido",0,0,'L');
			
			}
			
			else
			{
			
				$pdf->Cell(60,5, $dados_entradas["centro_custo_nome"],0,0,'L');
				
			}
			
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,5, number_format($dados_entradas["total"],2,",","."),0,0,'R');
			$pdf->SetFont('Arial', '', 9);
			
			$ccx_nome = $dados_entradas["conta_caixa_nome"];
			
			$total_entradas = $total_entradas + $dados_entradas["total"];
			
			//Verifica os lancamentos especificos dentro do centro de custo e conta-caixa listado
			//Busca as entradas do periodo
			$sql_entradas_detalhe = mysql_query("SELECT 
										ent.total_recebido AS total,
										ent.data_recebimento,
										rec.descricao,
										rec.grupo_conta_id,
										rec.subgrupo_conta_id,
										ccu.nome AS centro_custo_nome,
										ccx.nome AS conta_caixa_nome
										FROM contas_receber_recebimento ent
										LEFT OUTER JOIN contas_receber rec ON rec.id = ent.conta_receber_id
										LEFT OUTER JOIN grupo_conta ccu ON ccu.id = rec.grupo_conta_id
										LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
										WHERE ent.data_recebimento between '$DataIni' AND '$DataFim'
										AND ccx.id = $ContaCaixaId
										AND ccu.id = $dados_entradas[grupo_conta_id]
										ORDER BY ent.data_recebimento, descricao");

			//Percorre os registros
			while ($dados_entradas_detalhe = mysql_fetch_array($sql_entradas_detalhe))
			{
			
				$pdf->ln();
				$pdf->SetX(30);
				$pdf->Cell(0,5, DataMySQLRetornar($dados_entradas_detalhe["data_recebimento"]),"B",0,'L');
				$pdf->SetX(50);
				$pdf->Cell(0,5, $dados_entradas_detalhe["descricao"],"B",0,'L');
				$pdf->SetX(140);
				$pdf->Cell(0,5, number_format($dados_entradas_detalhe["total"],2,",","."),"B",0,'R');
				
			}
		}

		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(160,5, "Total:",'T',0,'R');
		$pdf->Cell(0,5, number_format($total_entradas,2,",","."),'T',0,'R');

	}
	
	//caso for pela data da emissao da conta
	else if ($DataProcura == 2)
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
									WHERE rec.data between '$DataIni' AND '$DataFim'
									AND rec.subgrupo_conta_id = $ContaCaixaId
									GROUP BY ccx.nome, ccu.nome
									ORDER BY ccx.nome, ccu.nome");

		$registros_entradas = mysql_num_rows($sql_entradas);

		$ccx_nome = 'a';

		$pdf->SetFont('Arial', '', 9);

		//Percorre os registros
		while ($dados_entradas = mysql_fetch_array($sql_entradas))
		{

			
			//Verifica se ainda é a mesmo centro de custo
			if ($dados_entradas["conta_caixa_nome"] != $ccx_nome)
			{
			
				$pdf->ln();	
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->SetX(14);
				
				//Verifica se o nome está vazio
				if ($dados_entradas["conta_caixa_nome"] == '')
				{
				
					$pdf->Cell(0,5, "Conta-Caixa Não Definido",0,0,'L');
				
				}
				
				else
				{
				
					$pdf->Cell(0,5, "Conta-Caixa: " . $dados_entradas["conta_caixa_nome"],0,0,'L');
					
				}
				
				$pdf->SetFont('Arial', '', 9);
			}
			
			$pdf->ln();
			$pdf->SetX(18);
			
			//Verifica se o nome está vazio
			if ($dados_entradas["centro_custo_nome"] == '')
			{
			
				$pdf->Cell(60,5, "Não Definido",0,0,'L');
			
			}
			
			else
			{
			
				$pdf->Cell(60,5, $dados_entradas["centro_custo_nome"],0,0,'L');
				
			}
			
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,5, number_format($dados_entradas["total"],2,",","."),0,0,'R');
			$pdf->SetFont('Arial', '', 9);
			
			$ccx_nome = $dados_entradas["conta_caixa_nome"];
			
			$total_entradas = $total_entradas + $dados_entradas["total"];
			
			//Verifica os lancamentos especificos dentro do centro de custo e conta-caixa listado
			//Busca as entradas do periodo
			$sql_entradas_detalhe = mysql_query("SELECT 
												rec.valor AS total,
												rec.data,
												rec.descricao,
												rec.grupo_conta_id,
												rec.subgrupo_conta_id,
												ccu.nome AS centro_custo_nome,
												ccx.nome AS conta_caixa_nome
												FROM contas_receber rec
												LEFT OUTER JOIN grupo_conta ccu ON ccu.id = rec.grupo_conta_id
												LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
												WHERE rec.data between '$DataIni' AND '$DataFim'
												AND ccx.id = $ContaCaixaId
												AND ccu.id = $dados_entradas[grupo_conta_id]
												ORDER BY rec.data, descricao");

			//Percorre os registros
			while ($dados_entradas_detalhe = mysql_fetch_array($sql_entradas_detalhe))
			{
			
				$pdf->ln();
				$pdf->SetX(30);
				$pdf->Cell(0,5, DataMySQLRetornar($dados_entradas_detalhe["data"]),"B",0,'L');
				$pdf->SetX(50);
				$pdf->Cell(0,5, $dados_entradas_detalhe["descricao"],"B",0,'L');
				$pdf->SetX(140);
				$pdf->Cell(0,5, number_format($dados_entradas_detalhe["total"],2,",","."),"B",0,'R');
				
			}
		}

		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(160,5, "Total:",'T',0,'R');
		$pdf->Cell(0,5, number_format($total_entradas,2,",","."),'T',0,'R');

	}
	
	
	//caso for pela data da vencimento da conta
	else if ($DataProcura == 3)
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
									WHERE rec.data_vencimento between '$DataIni' AND '$DataFim'
									AND rec.subgrupo_conta_id = $ContaCaixaId
									GROUP BY ccx.nome, ccu.nome
									ORDER BY ccx.nome, ccu.nome");

		$registros_entradas = mysql_num_rows($sql_entradas);

		$ccx_nome = 'a';

		$pdf->SetFont('Arial', '', 9);

		//Percorre os registros
		while ($dados_entradas = mysql_fetch_array($sql_entradas))
		{

			
			//Verifica se ainda é a mesmo centro de custo
			if ($dados_entradas["conta_caixa_nome"] != $ccx_nome)
			{
			
				$pdf->ln();	
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->SetX(14);
				
				//Verifica se o nome está vazio
				if ($dados_entradas["conta_caixa_nome"] == '')
				{
				
					$pdf->Cell(0,5, "Conta-Caixa Não Definido",0,0,'L');
				
				}
				
				else
				{
				
					$pdf->Cell(0,5, "Conta-Caixa: " . $dados_entradas["conta_caixa_nome"],0,0,'L');
					
				}
				
				$pdf->SetFont('Arial', '', 9);
			}
			
			$pdf->ln();
			$pdf->SetX(18);
			
			//Verifica se o nome está vazio
			if ($dados_entradas["centro_custo_nome"] == '')
			{
			
				$pdf->Cell(60,5, "Não Definido",0,0,'L');
			
			}
			
			else
			{
			
				$pdf->Cell(60,5, $dados_entradas["centro_custo_nome"],0,0,'L');
				
			}
			
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,5, number_format($dados_entradas["total"],2,",","."),0,0,'R');
			$pdf->SetFont('Arial', '', 9);
			
			$ccx_nome = $dados_entradas["conta_caixa_nome"];
			
			$total_entradas = $total_entradas + $dados_entradas["total"];
			
			//Verifica os lancamentos especificos dentro do centro de custo e conta-caixa listado
			//Busca as entradas do periodo
			$sql_entradas_detalhe = mysql_query("SELECT 
												rec.valor AS total,
												rec.data,
												rec.descricao,
												rec.grupo_conta_id,
												rec.subgrupo_conta_id,
												ccu.nome AS centro_custo_nome,
												ccx.nome AS conta_caixa_nome
												FROM contas_receber rec
												LEFT OUTER JOIN grupo_conta ccu ON ccu.id = rec.grupo_conta_id
												LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
												WHERE rec.data_vencimento between '$DataIni' AND '$DataFim'
												AND ccx.id = $ContaCaixaId
												AND ccu.id = $dados_entradas[grupo_conta_id]
												ORDER BY rec.data, descricao");

			//Percorre os registros
			while ($dados_entradas_detalhe = mysql_fetch_array($sql_entradas_detalhe))
			{
			
				$pdf->ln();
				$pdf->SetX(30);
				$pdf->Cell(0,5, DataMySQLRetornar($dados_entradas_detalhe["data_vencimento"]),"B",0,'L');
				$pdf->SetX(50);
				$pdf->Cell(0,5, $dados_entradas_detalhe["descricao"],"B",0,'L');
				$pdf->SetX(140);
				$pdf->Cell(0,5, number_format($dados_entradas_detalhe["total"],2,",","."),"B",0,'R');
				
			}
		}

		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(160,5, "Total:",'T',0,'R');
		$pdf->Cell(0,5, number_format($total_entradas,2,",","."),'T',0,'R');

	}
	
}

//Caso for conta caixa de saida
if ($TipoLancamento == 2)
{
	
	//Se e para procurar pela data do pagamento
	if ($DataProcura == 1)
	{
	
		//Busca o total de formandos do evento
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
									WHERE sai.data_pagamento between '$DataIni' AND '$DataFim'
									AND pag.subgrupo_conta_id = $ContaCaixaId
									GROUP BY ccx.nome, ccu.nome
									ORDER BY ccx.nome, ccu.nome");

		$registros_saidas = mysql_num_rows($sql_saidas);

		$ccx_nome = 'a';

		$pdf->SetFont('Arial', '', 9);

		//Percorre os registros
		while ($dados_saidas = mysql_fetch_array($sql_saidas))
		{

			
			//Verifica se ainda é a mesmo centro de custo
			if ($dados_saidas["conta_caixa_nome"] != $ccx_nome)
			{
			
				$pdf->ln();	
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->SetX(14);
				
				//Verifica se o nome está vazio
				if ($dados_saidas["conta_caixa_nome"] == '')
				{
				
					$pdf->Cell(0,5, "Conta-Caixa Não Definido",0,0,'L');
				
				}
				
				else
				{
				
					$pdf->Cell(0,5, "Conta-Caixa: " . $dados_saidas["conta_caixa_nome"],0,0,'L');
					
				}
				
				$pdf->SetFont('Arial', '', 9);
			}
			
			$pdf->ln();
			$pdf->SetX(18);
			
			//Verifica se o nome está vazio
			if ($dados_saidas["centro_custo_nome"] == '')
			{
			
				$pdf->Cell(60,5, "Não Definido",0,0,'L');
			
			}
			
			else
			{
			
				$pdf->Cell(60,5, $dados_saidas["centro_custo_nome"],0,0,'L');
				
			}
			
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,5, number_format($dados_saidas["total"],2,",","."),0,0,'R');
			$pdf->SetFont('Arial', '', 9);
			
			$ccx_nome = $dados_saidas["conta_caixa_nome"];
			
			$total_saidas = $total_saidas + $dados_saidas["total"];
			
			//Verifica os lancamentos especificos dentro do centro de custo e conta-caixa listado
			//Busca as saidas do periodo
			$sql_saidas_detalhe = mysql_query("SELECT 
										sai.total_pago AS total,
										sai.data_pagamento,
										pag.descricao,
										pag.grupo_conta_id,
										pag.subgrupo_conta_id,
										ccu.nome AS centro_custo_nome,
										ccx.nome AS conta_caixa_nome
										FROM contas_pagar_pagamento sai
										LEFT OUTER JOIN contas_pagar pag ON pag.id = sai.conta_pagar_id
										LEFT OUTER JOIN grupo_conta ccu ON ccu.id = pag.grupo_conta_id
										LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
										WHERE sai.data_pagamento between '$DataIni' AND '$DataFim'
										AND ccx.id = $ContaCaixaId
										AND ccu.id = $dados_saidas[grupo_conta_id]
										ORDER BY sai.data_pagamento");

			//Percorre os registros
			while ($dados_saidas_detalhe = mysql_fetch_array($sql_saidas_detalhe))
			{
			
				$pdf->ln();
				$pdf->SetX(30);
				$pdf->Cell(0,5, DataMySQLRetornar($dados_saidas_detalhe["data_pagamento"]),"B",0,'L');
				$pdf->SetX(50);
				$pdf->Cell(0,5, $dados_saidas_detalhe["descricao"],"B",0,'L');
				$pdf->SetX(140);
				$pdf->Cell(0,5, number_format($dados_saidas_detalhe["total"],2,",","."),"B",0,'R');
				
			}
		}

		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(160,5, "Total:",'T',0,'R');
		$pdf->Cell(0,5, number_format($total_saidas,2,",","."),'T',0,'R');
	
	}
	
	//Se e para procurar pela data da emissao
	if ($DataProcura == 2)
	{
	
		//Busca o total de formandos do evento
		$sql_saidas = mysql_query("SELECT 
									sum(pag.valor) AS total,
									pag.grupo_conta_id,
									pag.subgrupo_conta_id,
									ccu.nome AS centro_custo_nome,
									ccx.nome AS conta_caixa_nome
									FROM contas_pagar pag
									LEFT OUTER JOIN grupo_conta ccu ON ccu.id = pag.grupo_conta_id
									LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
									WHERE pag.data between '$DataIni' AND '$DataFim'
									AND pag.subgrupo_conta_id = $ContaCaixaId
									GROUP BY ccx.nome, ccu.nome
									ORDER BY ccx.nome, ccu.nome");
									

		$registros_saidas = mysql_num_rows($sql_saidas);

		$ccx_nome = 'a';

		$pdf->SetFont('Arial', '', 9);

		//Percorre os registros
		while ($dados_saidas = mysql_fetch_array($sql_saidas))
		{

			
			//Verifica se ainda é a mesmo centro de custo
			if ($dados_saidas["conta_caixa_nome"] != $ccx_nome)
			{
			
				$pdf->ln();	
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->SetX(14);
				
				//Verifica se o nome está vazio
				if ($dados_saidas["conta_caixa_nome"] == '')
				{
				
					$pdf->Cell(0,5, "Conta-Caixa Não Definido",0,0,'L');
				
				}
				
				else
				{
				
					$pdf->Cell(0,5, "Conta-Caixa: " . $dados_saidas["conta_caixa_nome"],0,0,'L');
					
				}
				
				$pdf->SetFont('Arial', '', 9);
			}
			
			$pdf->ln();
			$pdf->SetX(18);
			
			//Verifica se o nome está vazio
			if ($dados_saidas["centro_custo_nome"] == '')
			{
			
				$pdf->Cell(60,5, "Não Definido",0,0,'L');
			
			}
			
			else
			{
			
				$pdf->Cell(60,5, $dados_saidas["centro_custo_nome"],0,0,'L');
				
			}
			
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,5, number_format($dados_saidas["total"],2,",","."),0,0,'R');
			$pdf->SetFont('Arial', '', 9);
			
			$ccx_nome = $dados_saidas["conta_caixa_nome"];
			
			$total_saidas = $total_saidas + $dados_saidas["total"];
			
			//Verifica os lancamentos especificos dentro do centro de custo e conta-caixa listado
			//Busca as saidas do periodo
			$sql_saidas_detalhe = mysql_query("SELECT 
										pag.valor AS total,
										pag.data,
										pag.descricao,
										pag.grupo_conta_id,
										pag.subgrupo_conta_id,
										ccu.nome AS centro_custo_nome,
										ccx.nome AS conta_caixa_nome
										FROM contas_pagar pag
										LEFT OUTER JOIN grupo_conta ccu ON ccu.id = pag.grupo_conta_id
										LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
										WHERE pag.data between '$DataIni' AND '$DataFim'
										AND ccx.id = $ContaCaixaId
										AND ccu.id = $dados_saidas[grupo_conta_id]
										ORDER BY pag.data, pag.descricao");

			//Percorre os registros
			while ($dados_saidas_detalhe = mysql_fetch_array($sql_saidas_detalhe))
			{
			
				$pdf->ln();
				$pdf->SetX(30);
				$pdf->Cell(0,5, DataMySQLRetornar($dados_saidas_detalhe["data"]),"B",0,'L');
				$pdf->SetX(50);
				$pdf->Cell(0,5, $dados_saidas_detalhe["descricao"],"B",0,'L');
				$pdf->SetX(140);
				$pdf->Cell(0,5, number_format($dados_saidas_detalhe["total"],2,",","."),"B",0,'R');
				
			}
		}

		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(160,5, "Total:",'T',0,'R');
		$pdf->Cell(0,5, number_format($total_saidas,2,",","."),'T',0,'R');
	
	}
	
	//Se e para procurar pela data de vencimento
	if ($DataProcura == 3)
	{
	
		//Busca o total de formandos do evento
		$sql_saidas = mysql_query("SELECT 
									sum(pag.valor) AS total,
									pag.grupo_conta_id,
									pag.subgrupo_conta_id,
									ccu.nome AS centro_custo_nome,
									ccx.nome AS conta_caixa_nome
									FROM contas_pagar pag
									LEFT OUTER JOIN grupo_conta ccu ON ccu.id = pag.grupo_conta_id
									LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
									WHERE pag.data_vencimento between '$DataIni' AND '$DataFim'
									AND pag.subgrupo_conta_id = $ContaCaixaId
									GROUP BY ccx.nome, ccu.nome
									ORDER BY ccx.nome, ccu.nome");
									

		$registros_saidas = mysql_num_rows($sql_saidas);

		$ccx_nome = 'a';

		$pdf->SetFont('Arial', '', 9);

		//Percorre os registros
		while ($dados_saidas = mysql_fetch_array($sql_saidas))
		{

			
			//Verifica se ainda é a mesmo centro de custo
			if ($dados_saidas["conta_caixa_nome"] != $ccx_nome)
			{
			
				$pdf->ln();	
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->SetX(14);
				
				//Verifica se o nome está vazio
				if ($dados_saidas["conta_caixa_nome"] == '')
				{
				
					$pdf->Cell(0,5, "Conta-Caixa Não Definido",0,0,'L');
				
				}
				
				else
				{
				
					$pdf->Cell(0,5, "Conta-Caixa: " . $dados_saidas["conta_caixa_nome"],0,0,'L');
					
				}
				
				$pdf->SetFont('Arial', '', 9);
			}
			
			$pdf->ln();
			$pdf->SetX(18);
			
			//Verifica se o nome está vazio
			if ($dados_saidas["centro_custo_nome"] == '')
			{
			
				$pdf->Cell(60,5, "Não Definido",0,0,'L');
			
			}
			
			else
			{
			
				$pdf->Cell(60,5, $dados_saidas["centro_custo_nome"],0,0,'L');
				
			}
			
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,5, number_format($dados_saidas["total"],2,",","."),0,0,'R');
			$pdf->SetFont('Arial', '', 9);
			
			$ccx_nome = $dados_saidas["conta_caixa_nome"];
			
			$total_saidas = $total_saidas + $dados_saidas["total"];
			
			//Verifica os lancamentos especificos dentro do centro de custo e conta-caixa listado
			//Busca as saidas do periodo
			$sql_saidas_detalhe = mysql_query("SELECT 
										pag.valor AS total,
										pag.data,
										pag.descricao,
										pag.grupo_conta_id,
										pag.subgrupo_conta_id,
										ccu.nome AS centro_custo_nome,
										ccx.nome AS conta_caixa_nome
										FROM contas_pagar pag
										LEFT OUTER JOIN grupo_conta ccu ON ccu.id = pag.grupo_conta_id
										LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
										WHERE pag.data_vencimento between '$DataIni' AND '$DataFim'
										AND ccx.id = $ContaCaixaId
										AND ccu.id = $dados_saidas[grupo_conta_id]
										ORDER BY pag.data, pag.descricao");

			//Percorre os registros
			while ($dados_saidas_detalhe = mysql_fetch_array($sql_saidas_detalhe))
			{
			
				$pdf->ln();
				$pdf->SetX(30);
				$pdf->Cell(0,5, DataMySQLRetornar($dados_saidas_detalhe["data_vencimento"]),"B",0,'L');
				$pdf->SetX(50);
				$pdf->Cell(0,5, $dados_saidas_detalhe["descricao"],"B",0,'L');
				$pdf->SetX(140);
				$pdf->Cell(0,5, number_format($dados_saidas_detalhe["total"],2,",","."),"B",0,'R');
				
			}
		}

		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(160,5, "Total:",'T',0,'R');
		$pdf->Cell(0,5, number_format($total_saidas,2,",","."),'T',0,'R');
	
	}
	
}
	
//Gera o PDF
$pdf->Output();
?>