<?php
###########
## Módulo de relatório de eventos para auditoria
## Criado: 11/02/2009 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

require("../fpdf/fpdf.php");

include "../conexao/ConexaoMySQL.php";

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{

	//Inclui o arquivo para manipulação de datas
	include "../include/ManipulaDatas.php";

}

//Recupera os valores para filtragem
$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];
$DataIni = DataMySQLInserir($_GET["DataIni"]);
$DataFim = DataMySQLInserir($_GET["DataFim"]);
$edtTipoConsulta = $_GET["TipoConsulta"];
$edtTipoStatus = $_GET["TipoStatus"];

if ($edtTipoConsulta > 0)
{
	if ($edtTipoConsulta == 1)
	{
    
		$desc_tipo = "Tipo de Evento: Eventos Sociais";
    
	}
  
	else if ($edtTipoConsulta == 2)
	{
    
		$desc_tipo = "Tipo de Evento: Formaturas";
  
	}
	
	else if ($edtTipoConsulta == 3)
	{
    
		$desc_tipo = "Tipo de Evento: Pregão/Edital";
  
	}
  
	$where_tipo = "AND eve.tipo = $edtTipoConsulta";

}

if ($desc_status < 4)
{
  
	switch ($edtTipoStatus) 
	{
		case 0: 
		  $desc_status = "Com Status: Em orçamento"; 
		  $where_status = "AND status = 0";
		break;
		case 1: 
		  $desc_status = "Com Status: Em aberto";
		  $where_status = "AND status = 1"; 
		break;
		case 2: 
		  $desc_status = "Com Status: Realizado"; 
		  $where_status = "AND status = 2";
		break;
		case 3: 
		  $desc_status = "Com Status: Não-Realizado";
		  $where_status = "AND status = 3"; 
		break;
	}

}

$sql = mysql_query("SELECT 
					eve.id,
					eve.nome,
					eve.descricao,
					eve.status,
					eve.cliente_id,				
					eve.data_realizacao,
					eve.hora_realizacao,
					eve.duracao
					FROM eventos eve 
					WHERE eve.data_realizacao >= '$DataIni' AND eve.data_realizacao <= '$DataFim' 
					$where_tipo 
					$where_status
					ORDER BY eve.data_realizacao, eve.hora_realizacao, eve.nome");

$registros = mysql_num_rows($sql);  

class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{

		$DataIni = $_GET["DataIni"];
		$DataFim = $_GET["DataFim"]; 
  
		global $desc_tipo;
		global $desc_status;
   
		$empresaNome = $_GET["EmpresaNome"];
		//Ajusta a fonte
		$this->SetFont("Arial","",9);
		//Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date("d/m/Y", mktime()),0,0,"R");
		$this->Ln();
		$this->SetFont("Arial","B",15);
		$this->Cell(0,6,"Relação de Eventos para Auditoria");
		$this->Ln();
		$this->SetFont("Arial","",11);
		$this->Cell(0,6,"Período: " . $DataIni . " a " . $DataFim);
		$this->SetFont("Arial","",9);
		$this->Cell(0,5, "Pagina: " . $this->PageNo(), 0, 0, "R");
		$this->Ln();
		$this->Cell(0,5, $desc_tipo);
		$this->Ln();
		$this->Cell(0,5, $desc_status);

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
		$this->SetFont("Arial","I",7);
		//Page number
		$this->Line(10,281,200,281);
		$this->Cell(0,3,"Emitido por: " . $usuarioNome);
	
	}

}

//Instancia a classe gerador de pdf
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator("work | eventos");
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle("Relação de Eventos para Auditoria");
$pdf->SetSubject("Relatório gerado automaticamente pelo sistema");
$pdf->AliasNbPages();
$pdf->AddPage("L");

//Títulos das colunas
$pdf->SetFont("Arial", "B", 9);

//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);

//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(18,6, "Data",1,0,"C",1);
$pdf->Cell(12,6, "Cód.",1,0,"C",1);
$pdf->Cell(100,6, "Evento",1,0,"C",1);
$pdf->Cell(11,6, "Nº F.",1,0,"C",1);
$pdf->Cell(27,6, "Total Contratos", 1, 0,"C", 1);
$pdf->Cell(27,6, "Recebido", 1, 0, "C", 1);
$pdf->Cell(27,6, "Vencido", 1, 0, "C", 1);
$pdf->Cell(27,6, "A Receber", 1, 0, "C", 1);

//Monta as linhas com os dados da query
$pdf->SetFont("Arial","",9);

//Percorre os registros
while ($dados = mysql_fetch_array($sql))
{

	$eventoId = $dados["id"];
	
	//Busca o total de formandos do evento
	$sql_formando = mysql_query("SELECT 
								count(1) AS total_formandos
								FROM eventos_formando					
								WHERE status < 3
								AND evento_id = $eventoId");

	$registros_formando = mysql_num_rows($sql);
	
	//Percorre os registros
	while ($dados_formando = mysql_fetch_array($sql_formando))
	{
			
		$total_formandos = $dados_formando["total_formandos"];
		$total_geral_formandos = $total_geral_formandos + $dados_formando["total_formandos"];
		
	}
	
	//Verifica se existem formandos no evento
	if ($registros_formando > 0)
	{			
			
		//Percorre os formandos do evento para ver se há contas
		$sql_receber_1 = mysql_query("SELECT 
									SUM( valor_original ) AS total_contratos, 
									SUM( valor ) AS total_efetivo
									FROM contas_receber
									WHERE evento_id = $eventoId ");
					
		//Percorre os registros
		while ($dados_receber_1 = mysql_fetch_array($sql_receber_1))
		{
	
			$total_contratos = $dados_receber_1["total_contratos"];
			$total_geral_contratos = $total_geral_contratos + $dados_receber_1["total_contratos"];
			
			$total_efetivo = $dados_receber_1["total_efetivo"];
			$total_geral_efetivo = $total_geral_efetivo + $dados_receber_1["total_efetivo"];
		
		}
		
		
		//************************************************************
		//Percorre os formandos do evento para ver as contas recebidas
		$sql_receber_recebida = mysql_query("SELECT 
											SUM( valor_original ) AS total_recebido 
											FROM contas_receber
											WHERE evento_id = $eventoId 
											AND situacao = 2");
					
		//Percorre os registros
		while ($dados_receber_recebida = mysql_fetch_array($sql_receber_recebida))
		{
	
			$total_recebido = $dados_receber_recebida["total_recebido"];
			$total_geral_recebido = $total_geral_recebido + $dados_receber_recebida["total_recebido"];
		
		}
		
		
		//***********************************************************
		//Percorre os formandos do evento para ver as contas vencidas
		
		$dataHoje = date("Y-m-d", mktime());
		
		$sql_receber_vencida = mysql_query("SELECT 
											SUM( valor_original ) AS total_vencido 
											FROM contas_receber
											WHERE evento_id = $eventoId 
											AND situacao = 1
											AND data_vencimento < '$dataHoje'
											");
					
		//Percorre os registros
		while ($dados_receber_vencida = mysql_fetch_array($sql_receber_vencida))
		{
	
			$total_vencido = $dados_receber_vencida["total_vencido"];
			$total_geral_vencido = $total_geral_vencido + $dados_receber_vencida["total_vencido"];
		
		}
		
		
		//***********************************************************
		//Percorre os formandos do evento para ver as contas a vencer
		
		$dataHoje = date("Y-m-d", mktime());
		
		$sql_receber_vencer = mysql_query("SELECT 
											SUM( valor_original ) AS total_vencer
											FROM contas_receber
											WHERE evento_id = $eventoId 
											AND situacao = 1
											AND data_vencimento >'$dataHoje'
											");
					
		//Percorre os registros
		while ($dados_receber_vencer = mysql_fetch_array($sql_receber_vencer))
		{
	
			$total_vencer = $dados_receber_vencer["total_vencer"];
			$total_geral_vencer = $total_geral_vencer + $dados_receber_vencer["total_vencer"];
			
		}

	}
	
	$pdf->ln();
	$pdf->Cell(18,5, DataMySQLRetornar($dados['data_realizacao']),1,0);	
	$pdf->Cell(12,5, $dados['id'],1,0,'C');
	$pdf->Cell(100,5, substr($dados['nome'],0,55),1,0);
	$pdf->Cell(11,5, $total_formandos,1,0,'C');
	$pdf->Cell(27,5, number_format($total_contratos,2,",","."),1,0,'C');
	$pdf->Cell(27,5, number_format($total_recebido,2,",","."),1,0,'C');
	$pdf->Cell(27,5, number_format($total_vencido,2,",","."),1,0,'C');
	$pdf->Cell(27,5, number_format($total_vencer,2,",","."),1,0,'C');

}

$pdf->ln();
$pdf->Cell(130,6, 'Totais: ',1,0,'R',1);
$pdf->Cell(11,6, $total_geral_formandos,1,0,'C',1);
$pdf->Cell(27,6, number_format($total_geral_contratos,2,",","."),1,0,'C',1);
$pdf->Cell(27,6, number_format($total_geral_recebido,2,",","."),1,0,'C',1);
$pdf->Cell(27,6, number_format($total_geral_vencido,2,",","."),1,0,'C',1);
$pdf->Cell(27,6, number_format($total_geral_vencer,2,",","."),1,0,'C',1);

$pdf->SetFont('Arial', 'B', 8);
$pdf->ln();
$pdf->Cell(0,6, 'Total de eventos listados: ' . $registros,'T');

//Gera o PDF
$pdf->Output();
?>