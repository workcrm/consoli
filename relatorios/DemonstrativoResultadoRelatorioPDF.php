<?php
###########
## Módulo de relatório de Demonstrativo de Resultado
## Criado: 07/11/2012 - Maycon Edinger
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
$MesPesquisa = $_GET['MesPesquisa'];
$AnoPesquisa = $_GET['AnoPesquisa'];

$monta_data_ini = $AnoPesquisa . '-' . $MesPesquisa . '-01';
$monta_data_fim = $AnoPesquisa . '-' . $MesPesquisa . '-31';

class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{

		$MesPesquisa = $_GET['MesPesquisa'];
		$AnoPesquisa = $_GET['AnoPesquisa'];
		$empresaNome = $_GET['EmpresaNome'];
		
		global $GrupoId, $NomeGrupo, $EventoId, $NomeEvento;
		
		//Ajusta a fonte
		$this->SetFont('Arial','',9);
		//Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->SetFont('Arial','B',16);
		$this->Cell(0,6,'DEMONSTRAÇÃO DE RESULTADO: ' . $MesPesquisa . '/' . $AnoPesquisa);
		
		$this->Ln(8);
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
$pdf->SetTitle('Demonstração de Resultado');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('P');

//Busca as entradas
//Totalizador
$sql_entradas = mysql_query("SELECT 
							sum(ent.total_recebido) AS total
							FROM contas_receber_recebimento ent
							LEFT OUTER JOIN contas_receber rec ON rec.id = ent.conta_receber_id
							LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
							WHERE ent.data_recebimento between '$monta_data_ini' AND '$monta_data_fim'");

$dados_entradas_total = mysql_fetch_array($sql_entradas);

$total_geral_entrada = $dados_entradas_total['total'];
							
//Detalhado
$sql_entradas = mysql_query("SELECT 
							sum(ent.total_recebido) AS total,
							rec.grupo_conta_id,
							rec.subgrupo_conta_id,
							ccx.nome AS conta_caixa_nome
							FROM contas_receber_recebimento ent
							LEFT OUTER JOIN contas_receber rec ON rec.id = ent.conta_receber_id
							LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
							WHERE ent.data_recebimento between '$monta_data_ini' AND '$monta_data_fim'
							GROUP BY ccx.nome
							ORDER BY total DESC");

$registros_entradas = mysql_num_rows($sql_entradas);

$pdf->ln(2);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(122,7, "(+) ENTRADAS (Recebimento Efetivo):",'T',0,'L');
$pdf->Cell(16,7, number_format($total_geral_entrada,2,",","."),'T',0,'R');
$pdf->Cell(30,7, '100,000%','T',0,'R');
$pdf->Cell(0,7, ' ','T',0,'R');
$pdf->SetFont('Arial', '', 9);

//Percorre os registros
while ($dados_entradas = mysql_fetch_array($sql_entradas))
{
	
	$pdf->ln();
	$pdf->SetX(18);
	
	//Verifica se o nome está vazio
	if ($dados_entradas["conta_caixa_nome"] == '')
	{
	
		$pdf->Cell(100,5, "Não Definido","B",0,'L');
	
	}
	
	else
	{
	
		$pdf->Cell(100,5, $dados_entradas["conta_caixa_nome"],"B",0,'L');
		
	}
	
	$total_percentual = (($dados_entradas["total"] / $total_geral_entrada) * 100);
	$total_entradas = $total_entradas + $dados_entradas["total"];
	
	$pdf->Cell(30,5, number_format($dados_entradas["total"],2,",","."),"B",0,'R');
	$pdf->Cell(30,5, number_format($total_percentual,3,",",".") . '%',"B",0,'R');
	$pdf->Cell(0,5, ' ',"B",0,'R');
	
	
}

//Busca o total de entradas por tipo de recebimento
$sql_total_entradas = mysql_query("SELECT 
									sum(ent.total_recebido) AS total,
									ent.tipo_recebimento,
									rec.id
									FROM contas_receber_recebimento ent
									LEFT OUTER JOIN contas_receber rec ON rec.id = ent.conta_receber_id
									LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
									WHERE ent.data_recebimento between '$monta_data_ini' AND '$monta_data_fim'
									GROUP BY ent.tipo_recebimento
									ORDER BY ent.tipo_recebimento");

$registros_total_entradas = mysql_num_rows($sql_total_entradas);

//Percorre os registros
while ($dados_total_entradas = mysql_fetch_array($sql_total_entradas))
{
	
	//Efetua o switch do tipo de recebimento
	switch ($dados_total_entradas['tipo_recebimento']) 
	{
		case 1: $nome_tipo = 'Recebimentos em Dinheiro:';			break;
		case 2: $nome_tipo = 'Recebimentos em Cheque:';				break;       	
		case 3: $nome_tipo = 'Recebimentos em Cheque de Terceiro:';	break;
		case 4: $nome_tipo = 'Recebimentos em Boleto Bancário:';		break;
	}
	
	$pdf->ln();
	$pdf->Cell(112,5, $nome_tipo,0,0,'R');
	$pdf->Cell(26,5, number_format($dados_total_entradas["total"],2,",","."),0,0,'R');

}


//Debito dos custos variaveis
//Totalizacao dos custos
$sql_variavel = mysql_query("SELECT 
							sum(sai.total_pago) AS total
							FROM contas_pagar_pagamento sai
							LEFT OUTER JOIN contas_pagar pag ON pag.id = sai.conta_pagar_id
							LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
							WHERE sai.data_pagamento between '$monta_data_ini' AND '$monta_data_fim'
							AND ccx.tipo_despesa = 'V'
							ORDER BY total DESC");
							
$dados_variavel_total = mysql_fetch_array($sql_variavel);

$total_geral_variavel = $dados_variavel_total['total'];

//Detalhado
$sql_variavel = mysql_query("SELECT 
							sum(sai.total_pago) AS total,
							pag.subgrupo_conta_id,
							ccx.nome AS conta_caixa_nome
							FROM contas_pagar_pagamento sai
							LEFT OUTER JOIN contas_pagar pag ON pag.id = sai.conta_pagar_id
							LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
							WHERE sai.data_pagamento between '$monta_data_ini' AND '$monta_data_fim'
							AND ccx.tipo_despesa = 'V'
							GROUP BY ccx.nome
							ORDER BY total DESC");

$registros_variavel = mysql_num_rows($sql_variavel);

$total_percentual_variavel = (($total_geral_variavel / $total_geral_entrada) * 100);

$pdf->ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(122,7, "(-) CUSTOS VARIÁVEIS:",'T',0,'L');
$pdf->Cell(16,7, number_format($total_geral_variavel,2,",","."),'T',0,'R');
$pdf->Cell(30,7, number_format($total_percentual_variavel,3,",",".") . '%','T',0,'R');
$pdf->Cell(0,7, '100,000%','T',0,'R');
$pdf->SetFont('Arial', '', 9);


//Percorre os registros
while ($dados_variavel = mysql_fetch_array($sql_variavel))
{

	$pdf->ln();
	$pdf->SetX(18);
	
	//Verifica se o nome está vazio
	if ($dados_variavel["conta_caixa_nome"] == '')
	{
	
		$pdf->Cell(100,5, "Não Definido","B",0,'L');
	
	}
	
	else
	{
	
		$pdf->Cell(100,5, $dados_variavel["conta_caixa_nome"],"B",0,'L');
		
	}
	
	$total_percentual_ent = (($dados_variavel["total"] / $total_geral_entrada) * 100);
	$total_percentual_sai = (($dados_variavel["total"] / $total_geral_variavel) * 100);
	
	$total_entradas = $total_entradas + $dados_entradas["total"];
	
	$pdf->Cell(30,5, number_format($dados_variavel["total"],2,",","."),"B",0,'R');
	$pdf->Cell(30,5, number_format($total_percentual_ent,3,",",".") . '%','B',0,'R');
	$pdf->Cell(0,5, number_format($total_percentual_sai,3,",",".") . '%',"B",0,'R');
	
	
	$total_variavel = $total_variavel + $dados_variavel["total"];
}

//margem de contribuicao
$margem_contribuicao = $total_geral_entrada - $total_geral_variavel;
$percentual_margem_contribuicao = (($margem_contribuicao / $total_geral_entrada) * 100);

$pdf->ln();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(118,7, "(=) MARGEM DE CONTRIBUIÇÃO (ENTR - CV):",'LTB',0,'L');
$pdf->Cell(20,7, number_format($margem_contribuicao,2,",","."),'TB',0,'R');
$pdf->Cell(0,7, number_format($percentual_margem_contribuicao,3,",",".") . '%','TRB',0,'R');
$pdf->SetFont('Arial', '', 9);

//Debito dos custos fixos
//Totalizacao dos custos
$sql_fixo = mysql_query("SELECT 
						sum(sai.total_pago) AS total
						FROM contas_pagar_pagamento sai
						LEFT OUTER JOIN contas_pagar pag ON pag.id = sai.conta_pagar_id
						LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
						WHERE sai.data_pagamento between '$monta_data_ini' AND '$monta_data_fim'
						AND ccx.tipo_despesa = 'F'
						ORDER BY total DESC");
							
$dados_fixo_total = mysql_fetch_array($sql_fixo);

$total_geral_fixo = $dados_fixo_total['total'];

//Detalhado
$sql_fixo = mysql_query("SELECT 
						sum(sai.total_pago) AS total,
						pag.subgrupo_conta_id,
						ccx.nome AS conta_caixa_nome
						FROM contas_pagar_pagamento sai
						LEFT OUTER JOIN contas_pagar pag ON pag.id = sai.conta_pagar_id
						LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
						WHERE sai.data_pagamento between '$monta_data_ini' AND '$monta_data_fim'
						AND ccx.tipo_despesa = 'F'
						GROUP BY ccx.nome
						ORDER BY total DESC");

$registros_fixo = mysql_num_rows($sql_fixo);

$total_percentual_fixo = (($total_geral_fixo / $total_geral_entrada) * 100);

$pdf->ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(122,7, "(-) CUSTOS FIXOS:",0,0,'L');
$pdf->Cell(16,7, number_format($total_geral_fixo,2,",","."),0,0,'R');
$pdf->Cell(30,7, number_format($total_percentual_fixo,3,",",".") . '%',0,0,'R');
$pdf->Cell(0,7, '100,000%',0,0,'R');
$pdf->SetFont('Arial', '', 9);


//Percorre os registros
while ($dados_fixo = mysql_fetch_array($sql_fixo))
{

	$pdf->ln();
	$pdf->SetX(18);
	
	//Verifica se o nome está vazio
	if ($dados_fixo["conta_caixa_nome"] == '')
	{
	
		$pdf->Cell(100,5, "Não Definido","B",0,'L');
	
	}
	
	else
	{
	
		$pdf->Cell(100,5, $dados_fixo["conta_caixa_nome"],"B",0,'L');
		
	}
	
	$total_percentual_ent = (($dados_fixo["total"] / $total_geral_entrada) * 100);
	$total_percentual_sai = (($dados_fixo["total"] / $total_geral_variavel) * 100);
	
	$pdf->Cell(30,5, number_format($dados_fixo["total"],2,",","."),"B",0,'R');
	$pdf->Cell(30,5, number_format($total_percentual_ent,3,",",".") . '%','B',0,'R');
	$pdf->Cell(0,5, number_format($total_percentual_sai,3,",",".") . '%',"B",0,'R');
	
	
	$total_fixo = $total_fixo + $dados_fixo["total"];
}

//Totalizacao das despesas
$total_despesas = $total_geral_variavel + $total_geral_fixo;
$percentual_despesas = (($total_despesas / $total_geral_entrada) * 100);

$pdf->ln();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(118,7, "(=) TOTAL CUSTOS VARIAVEIS + FIXOS:",'LTB',0,'L');
$pdf->Cell(20,7, number_format($total_despesas,2,",","."),'TB',0,'R');
$pdf->Cell(0,7, number_format($percentual_despesas,3,",",".") . '%','TRB',0,'R');
$pdf->SetFont('Arial', '', 9);

//Totalizacao das despesas
$total_liquido = $total_geral_entrada - $total_despesas;
$percentual_liquido = (($total_liquido / $total_geral_entrada) * 100);

$pdf->ln();
$pdf->SetFillColor(178,178,178);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(118,7, "(=) RESULTADO LÍQUIDO:",'LTB',0,'L',1);
$pdf->Cell(20,7, number_format($total_liquido,2,",","."),'TB',0,'R',1);
$pdf->Cell(0,7, number_format($percentual_liquido,3,",",".") . '%','TRB',0,'R',1);
$pdf->SetFont('Arial', '', 9);

//Gera o PDF
$pdf->Output();
?>