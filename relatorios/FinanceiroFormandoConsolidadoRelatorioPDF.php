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
$edtTipoConta = $_GET["TipoConta"];

$edtDataIni = $_GET["DataIni"];
$edtDataFim = $_GET["DataFim"];
$edtDataIniBanco = DataMySQLInserir($_GET["DataIni"]);
$edtDataFimBanco = DataMySQLInserir($_GET["DataFim"]);

$edtSituacao = $_GET["Situacao"];

$edtContaCaixaId = $_GET["ContaCaixaId"];

if ($edtContaCaixaId > 0)
{

	
	$where_conta_caixa = "AND rec.subgrupo_conta_id = $edtContaCaixaId";

}

//Caso for informada um periodo a pesquisar
if ($edtDataIni != '')
{

	$filtra_data_conta = "AND rec.data_vencimento BETWEEN '$edtDataIniBanco' AND '$edtDataFimBanco'";
	
}

//Caso for informado uma situacao a pesquisar
if ($edtSituacao > 0)
{

	switch($edtSituacao)
	{
	
		//Em aberto
		case 1: 
			$filtra_situacao_comissao = "AND con.data_vencimento BETWEEN '$edtDataIniBanco' AND '$edtDataFimBanco'";
			$filtra_situacao_conta = "AND rec.situacao = 1"; 		
		break;
		//Recebidas
		case 2: 
			$filtra_situacao_comissao = "AND con.data_vencimento BETWEEN '$edtDataIniBanco' AND '$edtDataFimBanco'";
			$filtra_situacao_conta = "AND rec.situacao = 2";
		break;
		//Vencidas
		case 3: 
			$hoje = date("Y-m-d" , mktime());
			$filtra_situacao_comissao = "AND con.data_vencimento BETWEEN '$edtDataIniBanco' AND '$edtDataFimBanco'";
			$filtra_situacao_conta = "AND rec.situacao = 1 AND rec.data_vencimento < '$hoje'"; 
			$filtra_data_conta = '';
		break;
	
	}
			
	
	
}

//Busca o nome do evento
//Monta o sql
$sql_evento = mysql_query("SELECT nome, valor_geral_evento FROM eventos WHERE id = $edtEventoId");

//Monta o array com os dados
$dados_evento = mysql_fetch_array($sql_evento);

$desc_evento = $dados_evento["nome"];

class PDF extends FPDF
{
	
	//Cabeçalho do relatório
	function Header()
	{
			
		global $edtDataIni, $edtDataFim, $edtSituacao, $desc_evento;
		
		$empresaNome = $_GET["EmpresaNome"];
		$edtEventoId = $_GET["EventoId"];
		$edtContaCaixaId = $_GET["ContaCaixaId"];

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
		$this->Cell(15,4,'Evento:');
		$this->SetFont('Arial', '', 10);
		$this->Cell(0,4, $edtEventoId);
		$this->Ln();
		$this->Cell(0,4, $desc_evento);
		
		if ($edtDataIni != '')
		{
		
			$this->Ln();
			$this->SetFont('Arial', 'B', 10);
			$this->Cell(19,4,'Periodo: ');
			$this->SetFont('Arial', '', 10);
			$this->Cell(0,4, $edtDataIni . ' a ' . $edtDataFim);
		
		}
		
		if ($edtSituacao > 0)
		{
		
			switch($edtSituacao)
			{
			
				case 1: $desc_situacao = 'A Vencer'; break;
				case 2: $desc_situacao = 'Recebidas'; break;
				case 3: $desc_situacao = 'Vencidas'; break;
			
			}
			
			$this->Ln();
			$this->SetFont('Arial', 'B', 10);
			$this->Cell(19,4,'Situacao: ');
			$this->SetFont('Arial', '', 10);
			$this->Cell(0,4, $desc_situacao);
		
		}
		
		if ($edtContaCaixaId > 0)
		{
			
			$sql_conta_caixa = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = $edtContaCaixaId");

			//Monta o array com os dados
			$dados_conta_caixa = mysql_fetch_array($sql_conta_caixa);

			$desc_conta_caixa = "($edtContaCaixaId) - " . $dados_conta_caixa["nome"];
	
			$this->Ln();
			$this->SetFont('Arial', 'B', 10);
			$this->Cell(23,4,'Conta-caixa: ');
			$this->SetFont('Arial', '', 10);
			$this->Cell(0,4, $desc_conta_caixa);
		
		}
		
		//Line break
		$this->Ln(6);
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
$pdf->AddPage('P');


//Caso deva exibir as contas da comissao
if ($edtTipoConta != 2)
{ 

	//Monta o sql de filtragem das contas da comissao
	$sql = "SELECT 
			rec.id,			
			rec.pessoa_id,			
			rec.valor_original,
			rec.data_vencimento,
			rec.situacao,
			cli.nome AS cliente_nome,
			rec.descricao
			FROM contas_receber rec
			LEFT OUTER JOIN clientes cli ON cli.id = rec.pessoa_id
			WHERE rec.empresa_id = $empresaId 
			AND rec.evento_id = $edtEventoId
			AND rec.conta_de_comissao = 1
			AND rec.formando_id > 0
			$filtra_data_conta
			$where_conta_caixa
			ORDER BY rec.data_vencimento";
			
			
	$query = mysql_query($sql);

	$registros = mysql_num_rows($query);  
		  
	//Verifica se há formandos cadastrados para o evento
	if ($registros == 0) 
	{
	  
		//Títulos das colunas
		$pdf->SetFont('Arial', 'B', 14);
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->Cell(0,6, 'Posição Financeira da Comissão de Formatura',1,0,'C',1);
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(0,7, "Não há contas a receber para a comissão de formatura deste evento !",1,0);
		$pdf->Ln();
	  
	}
	  
	else 

	{
	
		//Títulos das colunas
		$pdf->SetFont('Arial', 'B', 14);
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->Cell(0,6, 'Posição Financeira da Comissão de Formatura',1,0,'C',1);
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(74,6, 'Descrição',1,0,'L',1);
		$pdf->Cell(23,6, 'Vencimento',1,0,'C',1);
		$pdf->Cell(24,6, 'Vl. Original',1,0,'C',1);		
		$pdf->Cell(24,6, 'Vl. Receber',1,0,'C',1);
		$pdf->Cell(24,6, 'Vl. Recebido',1,0,'C',1);
		$pdf->Cell(0,6, 'Situação',1,0,'C',1);
		$pdf->Ln();
		
		$total_comissao = 0;
			
		while ($dados = mysql_fetch_array($query))
		{
								
			//Efetua o switch para o campo de situacao
			switch ($dados["situacao"]) 
			{
				case 1: 
					if ($dados["data_vencimento"] < date("Y-m-d" , mktime()))
					{
					
						$desc_situacao = "Vencida";
						$valor_saldo_vencida = $valor_saldo_vencida  + $dados["valor_original"];
					
					}
					
					else
					
					{
					
						$desc_situacao = "Em Aberto";
						$valor_saldo_aberto = $valor_saldo_aberto  + $dados["valor_original"];
						
					}
					
					$valor_recebido = 0;
					$valor_receber = $dados["valor_original"];
					$total_receber = $total_receber + $dados["valor_original"];
						
				break;
				case 2: 
					$desc_situacao = "Recebida"; 
					
					$valor_recebido = $dados["valor_original"];
					$total_recebido = $total_recebido + $dados["valor_original"];
					$valor_receber = 0;
				break;
				
			}
			
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(74,5, $dados["descricao"], "B");
			$pdf->SetFont('Arial', '', 10);
			$pdf->Cell(24,5, DataMySQLRetornar($dados["data_vencimento"]), "B",0,'C');
			$pdf->Cell(24,5, "R$ " . number_format($dados["valor_original"], 2, ',', '.'), "B",0, 'R');
			$pdf->Cell(24,5, "R$ " . number_format($valor_receber, 2, ",", "."),"T",0,'R');
			$pdf->Cell(24,5, "R$ " . number_format($valor_recebido, 2, ",", "."),"T",0,'R');
			$pdf->Cell(0,5, $desc_situacao, "T", 0, 'C');
			
			$pdf->Ln();
			
			$total_comissao = $total_comissao + $dados["valor_original"];
			
		}
		
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(74,6, ' ');
		$pdf->Cell(23,6, 'TOTAL:',1,0,'R',1);
		$pdf->Cell(24,6, "R$ " . number_format($total_comissao, 2, ',', '.'),1,0,'R',1);	
		$pdf->Cell(24,6, "R$ " . number_format($total_receber, 2, ',', '.'),1,0,'R',1);	
		$pdf->Cell(24,6, "R$ " . number_format($total_recebido, 2, ',', '.'),1,0,'R',1);	
		$pdf->Ln(6);
		

	}

}

if ($edtTipoConta < 3)
{	

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
					FROM 
						contas_receber rec
					LEFT OUTER JOIN 
						eventos eve ON eve.id = rec.evento_id
					LEFT OUTER JOIN 
						eventos_formando form ON form.id = rec.pessoa_id
					WHERE 
						rec.empresa_id = $empresaId 
					AND 
						rec.evento_id = $edtEventoId
					AND 
						rec.formando_id > 0
					AND
						form.status < 3
						$filtra_data_conta
						$filtra_situacao_conta
						$where_conta_caixa
					GROUP BY 
						formando_nome
					ORDER BY 
						formando_nome"; 	
			
	$query = mysql_query($sql);

	$registros = mysql_num_rows($query);  
		  
	//Verifica se há formandos cadastrados para o evento
	if ($registros == 0) 
	{
	  
		$pdf->Cell(0,7, "Não há contas a receber para o evento selecionado !",1,0);
	  
	}
	  
	else 

	{
		
		$pdf->Ln();
		//Títulos das colunas
		$pdf->SetFont('Arial', 'B', 14);
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->Cell(0,6, 'Posição Financeira dos Formandos',1,0,'C',1);
		$pdf->Ln(10);
		
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
			$pdf->Cell(28,6, 'Vl. Contrato',1,0,'C',1);		
			$pdf->Cell(28,6, 'Vl. Receber',1,0,'C',1);
			$pdf->Cell(28,6, 'Vl. Recebido',1,0,'C',1);
			$pdf->Cell(28,6, 'Situação',1,0,'C',1);
			
			//Monta o sql de filtragem das contas
			$sql_conta = "SELECT * FROM contas_receber rec
						  WHERE evento_id = $edtEventoId 
						  AND formando_id = $FormandoId 
						  $filtra_data_conta
						  $filtra_situacao_conta
						  $where_conta_caixa
						  ORDER BY data_vencimento";   
					
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
				
				while ($dados_conta = mysql_fetch_array($query_conta))
				{
			
					//Efetua o switch para o campo de situacao
					switch ($dados_conta["situacao"]) 
					{
						case 1: 
							if ($dados_conta["data_vencimento"] < date("Y-m-d" , mktime()))
							{
							
								$desc_situacao = "Vencida";
								$valor_saldo_vencida = $valor_saldo_vencida  + $dados_conta["valor_original"];
								$saldo_formando_vencido = $saldo_formando_vencido + $dados_conta["valor_original"];
							
							}
							
							else
							
							{
							
								$desc_situacao = "Em Aberto";
								$valor_saldo_aberto = $valor_saldo_aberto  + $dados_conta["valor_original"];
								$saldo_formando_vencer = $saldo_formando_vencer + $dados_conta["valor_original"];
								
							}
							
							$valor_recebido = 0;
							$valor_receber = $dados_conta["valor_original"];
								
						break;
						case 2: 
							$desc_situacao = "Recebida"; 
							
							$valor_recebido = $dados_conta["valor_original"];
							$valor_receber = 0;
						break;
					}
			
					//Monta o subtotal por formando
					$pdf->ln();
					$pdf->Cell(16,6, $parcela,"T", 0, 'C');
					$pdf->Cell(24,6, DataMySQLRetornar($dados_conta["data_vencimento"]), "T",0,'C');
					$pdf->Cell(28,6, "R$ " . number_format($dados_conta["valor_original"], 2, ",", "."),"T",0,'R');
					$pdf->Cell(28,6, "R$ " . number_format($valor_receber, 2, ",", "."),"T",0,'R');
					$pdf->Cell(28,6, "R$ " . number_format($valor_recebido, 2, ",", "."),"T",0,'R');
					$pdf->Cell(28,6, $desc_situacao, "T", 0, 'C');
					
					$parcela++;
					
					$total_original = $total_original + $dados_conta["valor_original"];				
					$total_receber = $total_receber + $valor_receber;
					$total_recebido = $total_recebido + $valor_recebido;
					
					$total_geral_original = $total_geral_original + $dados_conta["valor_original"];
					$total_geral_receber = $total_geral_receber + $valor_receber;
					$total_geral_recebido = $total_geral_recebido + $valor_recebido;
				}
				
				$pdf->ln();
				//Títulos das colunas
				$pdf->SetFont('Arial', 'B', 10);
				//Define a cor RGB do fundo da celula
				$pdf->SetFillColor(178,178,178);
				//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
				$pdf->Cell(40,6, 'TOTAL:',1,0,'R',1);
				$pdf->Cell(28,6, "R$ " . number_format($total_original, 2, ",", "."),1,0,'R',1);
				$pdf->Cell(28,6, "R$ " . number_format($total_receber, 2, ",", "."),1,0,'R',1);
				$pdf->Cell(28,6, "R$ " . number_format($total_recebido, 2, ",", "."),1,0,'R',1);
				$pdf->Cell(28,6, " " ,'T');
				$pdf->ln();
				$pdf->Cell(40,6, 'Saldo em Atraso:',1,0,'R',1);
				$pdf->Cell(28,6, "R$ " . number_format($saldo_formando_vencido, 2, ",", "."),1,0,'R',1);
				$pdf->ln();
				$pdf->Cell(40,6, 'Saldo a Vencer:',1,0,'R',1);
				$pdf->Cell(28,6, "R$ " . number_format($saldo_formando_vencer, 2, ",", "."),1,0,'R',1);
				$pdf->ln(10);
				
				$saldo_formando_vencido = 0;
				$saldo_formando_vencer = 0;
				
			}
	  
		}
}
		
	$pdf->ln();
	//Títulos das colunas
	$pdf->SetFont('Arial', 'B', 10);
	//Define a cor RGB do fundo da celula
	$pdf->SetFillColor(178,178,178);
	//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
	$pdf->Cell(40,6, '','LTR',0,'R',1);
	$pdf->Cell(28,6, "Contratos",1,0,'R',1);
	$pdf->Cell(28,6, "A Receber",1,0,'R',1);
	$pdf->Cell(28,6, "Recebido",1,0,'R',1);
	$pdf->Cell(34,6, "Vencido",1,0,'R',1);
	$pdf->Cell(0,6, "A Vencer",1,0,'R',1);
	$pdf->ln();
	$pdf->Cell(40,6, 'TOTAL GERAL R$:','LBR',0,'R',1);
	$pdf->Cell(28,6, number_format($total_geral_original, 2, ",", "."),1,0,'R',1);
	$pdf->Cell(28,6, number_format($total_geral_receber, 2, ",", "."),1,0,'R',1);
	$pdf->Cell(28,6, number_format($total_geral_recebido, 2, ",", "."),1,0,'R',1);
	
	$pdf->SetTextColor(255,0,0);
	@$percentual_vencida = ($valor_saldo_vencida / $total_geral_original) * 100;
	$pdf->Cell(34,6, number_format($valor_saldo_vencida, 2, ",", ".") . ' (' .  number_format($percentual_vencida, 0, ",", ".") . '%)',1,0,'R',1);
	
	@$percentual_vencer = ($valor_saldo_aberto / $total_geral_original) * 100;
	$pdf->Cell(0,6, number_format($valor_saldo_aberto, 2, ",", ".") . ' (' .  number_format($percentual_vencer, 0, ",", ".") . '%)',1,0,'R',1);
	
	$pdf->SetTextColor(0,0,0);

}

//So mostra se for o rlatorio completo
if ($edtSituacao == 0)
{
	$pdf->ln(10);
	$pdf->Cell(0,6, "Valor Orçado do Evento R$: " . number_format($dados_evento["valor_geral_evento"], 2, ",", "."),1,0,'L',1);
	$pdf->ln();
	$pdf->Cell(0,6, "Valor em Contratos Individuais R$: " . number_format($total_geral_original, 2, ",", "."),1,0,'L',1);

	$diferenca_total = $dados_evento["valor_geral_evento"] - $total_geral_original;
	@$diferenca_percentual = ($diferenca_total / $dados_evento["valor_geral_evento"]) * 100;

	$pdf->ln();
	$pdf->SetTextColor(255,0,0);
	$pdf->Cell(0,6, "Previsão de Débito/Crédito R$: " . number_format($diferenca_total, 2, ",", ".") . ' (' .  number_format($diferenca_percentual, 0, ",", ".") . '%)',1,0,'L',1);
	$pdf->SetTextColor(0,0,0);

}
	
//Adendos do evento
$sql_adendos = mysql_query("SELECT 
							ade.evento_id,
							ade.data,
							ade.hora,
							ade.usuario_id,
							ade.pessoas_confirmadas,
							ade.lugares_montados,
							ade.alunos_colacao,
							ade.alunos_baile,
							ade.participantes_baile,
							ade.valor_colacao,
							ade.valor_baile,
							ade.valor_desconto_individual,
							ade.valor_total_individual,
							ade.valor_geral_evento,
							ade.detalhamento,
							usu.nome AS usuario_nome
							FROM eventos_adendo ade
							LEFT OUTER JOIN usuarios usu ON usu.usuario_id = ade.usuario_id
							WHERE ade.evento_id = $edtEventoId 
							ORDER BY ade.data");
														 
$registros_adendos = mysql_num_rows($sql_adendos);

if ($registros_adendos > 0)
{

	$pdf->ln(10);
	$pdf->Cell(0,6, "ADENDOS DO EVENTO",1,0,'C',1);
	
	while ($dados_adendo = mysql_fetch_array($sql_adendos))
	{
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->ln();
		$pdf->Cell(0,5, 'Adendo de ' . DataMySQLRetornar($dados_adendo['data']) . ' as ' . substr($dados_adendo['hora'],0,5),1,'L');
		
		$pdf->SetFont('Arial', '', 8);
		$pdf->ln();
		$pdf->Multicell(0,4, $dados_adendo['detalhamento'],1,'L');
	
	}

}

//Gera o PDF
$pdf->Output();
?>