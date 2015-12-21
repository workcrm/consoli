<?php
###########
## Módulo de relatório geral de financeiro por formando consolidado
## Criado: 05/04/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

require('../fpdf/fpdf.php');

include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "../include/ManipulaDatas.php";

//Recupera os valores para filtragem
$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];
$edtEventoId = $_GET["EventoId"];

//Busca o nome do evento
//Monta o sql
$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = $edtEventoId");

//Monta o array com os dados
$dados_evento = mysql_fetch_array($sql_evento);

$desc_evento = $dados_evento["nome"];

class PDF extends FPDF
{
	
	//Cabeçalho do relatório
	function Header()
	{
			
		$empresaNome = $_GET["EmpresaNome"];
		global $desc_evento;

		//Ajusta a fonte
		$this->SetFont('Arial','',9);
		//Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->SetFont('Arial','B',15);
		$this->Cell(0,6,'Relatório de Posição Financeira do Evento');
		$this->SetFont('Arial','',9);
		$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
		$this->Ln(6);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(19,5,'Evento:');
		$this->SetFont('Arial', '', 10);
		$this->Multicell(0,5, $desc_evento);
		
		//Line break
		$this->Ln(4);
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
$pdf->SetTitle('Relação Geral de Contas a Receber');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');
	
//Monta o sql de filtragem das contas
$sql = "SELECT 
		rec.id,			
		rec.pessoa_id,			
		eve.nome AS evento_nome,
		form.nome AS formando_nome,
		form.id AS formando_id,
		SUM(rec.valor_original) AS capital,
		form.chk_culto,
		form.chk_colacao,
		form.chk_jantar,
		form.chk_baile
		FROM contas_receber rec
		LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
		LEFT OUTER JOIN eventos_formando form ON form.id = rec.pessoa_id
		WHERE rec.empresa_id = $empresaId 
		AND rec.evento_id = $edtEventoId
		AND rec.formando_id > 0
		GROUP BY formando_nome
		ORDER BY formando_nome";  	
		
$query = mysql_query($sql);

$registros = mysql_num_rows($query);  
	  
//Verifica se há formandos cadastrados para o evento
if ($registros == 0) 
{
  
    $pdf->Cell(0,7, "Não há formandos para o evento selecionado !",1,0);
  
}
  
else 

{
    
    while ($dados = mysql_fetch_array($query))
    {
		
		$FormandoId = $dados["formando_id"];
		
		$desc_participante = "";
					
		if ($dados["chk_culto"] == 1)
		{
		
			$desc_participante .= " CULTO ";
			
		}
		
		if ($dados["chk_colacao"] == 1)
		{
		
			$desc_participante .= " COLAÇÃO ";
			
		}
		
		if ($dados["chk_jantar"] == 1)
		{
		
			$desc_participante .= " JANTAR ";
			
		}
		
		if ($dados["chk_baile"] == 1)
		{
		
			$desc_participante .= " BAILE ";
			
		}

		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0,6, $dados["formando_nome"]);
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(34,5, "Valor do Contrato: ",'B');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(30,5, "R$ " . number_format($dados["capital"], 2, ',', '.'), "B");
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(22,5, "Participante: ",'B');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0,5, $desc_participante,'B');
		$pdf->Ln(8);

		//Títulos das colunas
		$pdf->SetFont('Arial', 'B', 10);
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->Cell(16,6, 'Parcela',1,0,'L',1);
		$pdf->Cell(24,6, 'Vencimento',1,0,'C',1);
		$pdf->Cell(24,6, 'Vl. Contrato',1,0,'C',1);
		$pdf->Cell(24,6, 'Vl. Boleto',1,0,'C',1);
		$pdf->Cell(24,6, 'Multa/Juros',1,0,'C',1);
		$pdf->Cell(24,6, 'Vl. Receber',1,0,'C',1);
		$pdf->Cell(24,6, 'Vl. Recebido',1,0,'C',1);
		$pdf->Cell(24,6, 'Saldo',1,0,'C',1);
		$pdf->Cell(24,6, 'Situação',1,0,'C',1);
		
		//Monta o sql de filtragem das contas
		$sql_conta = "SELECT * FROM contas_receber WHERE evento_id = $edtEventoId AND formando_id = $FormandoId ORDER BY data_vencimento";   
				
		$query_conta = mysql_query($sql_conta);

		$registros_conta = mysql_num_rows($query_conta);  
			  
		//Verifica se há formandos cadastrados para o evento
		if ($registros_conta == 0) 
		{
		  
			$pdf->Cell(0,7, "Não há contas a receber para este formando !",1,0);
		  
		}
		  
		else 

		{
		
			$parcela = 1;
			
			$total_original = 0;
			$total_boleto = 0;
			$total_multa = 0;
			$total_receber = 0;
			$total_recebido = 0;
			$total_saldo = 0;
			
			while ($dados_conta = mysql_fetch_array($query_conta))
			{
		
				//Efetua o switch para o campo de situacao
				switch ($dados_conta["situacao"]) 
				{
					case 1: 
						if ($dados_conta["data_vencimento"] <= date("Y-m-d" , mktime()))
						{
						
							$desc_situacao = "Vencida";
						
						}
						
						else
						
						{
						
							$desc_situacao = "Em Aberto";
							
						}
							
					break;
					case 2: $desc_situacao = "Recebida"; break;
				}
		
				//Monta o subtotal por formando
				$pdf->ln();
				$pdf->Cell(16,6, $parcela,"T", 0, 'C');
				$pdf->Cell(24,6, DataMySQLRetornar($dados_conta["data_vencimento"]), "T");
				$pdf->Cell(24,6, "R$ " . number_format($dados_conta["valor_original"], 2, ",", "."),"T",0,'R');
				$pdf->Cell(24,6, "R$ " . number_format($dados_conta["valor_boleto"], 2, ",", "."),"T",0,'R');
				$pdf->Cell(24,6, "R$ " . number_format($dados_conta["valor_multa_juros"], 2, ",", "."),"T",0,'R');
				$pdf->Cell(24,6, "R$ " . number_format($dados_conta["valor"], 2, ",", "."),"T",0,'R');
				$pdf->Cell(24,6, "R$ " . number_format($dados_conta["valor_recebido"], 2, ",", "."),"T",0,'R');
				$pdf->Cell(24,6, "R$ " . number_format($dados_conta["valor"] - $dados_conta["valor_recebido"], 2, ",", "."),"T",0,'R');
				$pdf->Cell(24,6, $desc_situacao, "T", 0, 'C');
				
				$parcela++;
				
				$total_original = $total_original + $dados_conta["valor_original"];
				$total_boleto = $total_boleto + $dados_conta["valor_boleto"];
				$total_multa = $total_multa + $dados_conta["valor_multa_juros"];
				$total_receber = $total_receber + $dados_conta["valor"];
				$total_recebido = $total_recebido + $dados_conta["valor_recebido"];
				$total_saldo = $total_saldo + ($dados_conta["valor"] - $dados_conta["valor_recebido"]);
				
				$total_geral_original = $total_geral_original + $dados_conta["valor_original"];
				$total_geral_boleto = $total_geral_boleto + $dados_conta["valor_boleto"];
				$total_geral_multa = $total_geral_multa + $dados_conta["valor_multa_juros"];
				$total_geral_receber = $total_geral_receber + $dados_conta["valor"];
				$total_geral_recebido = $total_geral_recebido + $dados_conta["valor_recebido"];
				$total_geral_saldo = $total_geral_saldo + ($dados_conta["valor"] - $dados_conta["valor_recebido"]);
			}
			
			$pdf->ln();
			//Títulos das colunas
			$pdf->SetFont('Arial', 'B', 10);
			//Define a cor RGB do fundo da celula
			$pdf->SetFillColor(178,178,178);
			//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
			$pdf->Cell(40,6, 'TOTAL:',1,0,'R',1);
			$pdf->Cell(24,6, "R$" . number_format($total_original, 2, ",", "."),1,0,'R',1);
			$pdf->Cell(24,6, "R$" . number_format($total_boleto, 2, ",", "."),1,0,'R',1);
			$pdf->Cell(24,6, "R$" . number_format($total_multa, 2, ",", "."),1,0,'R',1);
			$pdf->Cell(24,6, "R$" . number_format($total_receber, 2, ",", "."),1,0,'R',1);
			$pdf->Cell(24,6, "R$" . number_format($total_recebido, 2, ",", "."),1,0,'R',1);
			$pdf->Cell(24,6, "R$" . number_format($total_saldo, 2, ",", "."),1,0,'R',1);
			$pdf->ln(10);
			
		}
  
	}
	
	$pdf->ln();
	//Títulos das colunas
	$pdf->SetFont('Arial', 'B', 10);
	//Define a cor RGB do fundo da celula
	$pdf->SetFillColor(178,178,178);
	//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
	$pdf->Cell(40,6, 'TOTAL GERAL:',1,0,'R',1);
	$pdf->Cell(24,6, "R$" . number_format($total_geral_original, 2, ",", "."),1,0,'R',1);
	$pdf->Cell(24,6, "R$" . number_format($total_geral_boleto, 2, ",", "."),1,0,'R',1);
	$pdf->Cell(24,6, "R$" . number_format($total_geral_multa, 2, ",", "."),1,0,'R',1);
	$pdf->Cell(24,6, "R$" . number_format($total_geral_receber, 2, ",", "."),1,0,'R',1);
	$pdf->Cell(24,6, "R$" . number_format($total_geral_recebido, 2, ",", "."),1,0,'R',1);
	$pdf->Cell(24,6, "R$" . number_format($total_geral_saldo, 2, ",", "."),1,0,'R',1);
	$pdf->ln(10);
	
}

//Gera o PDF
$pdf->Output();
?>