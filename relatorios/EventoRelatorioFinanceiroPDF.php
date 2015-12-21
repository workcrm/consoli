<?php
###########
## Módulo de relatório de eventos por posição financeira
## Criado: 07/12/2009 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

require("../fpdf/fpdf.php");

include "../conexao/ConexaoMySQL.php";

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) {
	//Inclui o arquivo para manipulação de datas
	include "../include/ManipulaDatas.php";
}

//Recupera os valores para filtragem
$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];
$TipoFin = $_GET["TipoFin"];


$sql = mysql_query("SELECT 
										eve.id,
										eve.nome,
										eve.descricao,
										eve.status,
										eve.cliente_id,				
										eve.data_realizacao,
										eve.hora_realizacao,
										eve.duracao,
										cli.nome as cliente_nome
										FROM eventos eve 
										LEFT OUTER JOIN clientes cli ON cli.id = eve.cliente_id
										WHERE eve.posicao_financeira = $TipoFin ORDER BY eve.data_realizacao, eve.hora_realizacao, eve.nome");

$registros = mysql_num_rows($sql);  

class PDF extends FPDF
{
//Cabeçalho do relatório
function Header()
{
  $TipoFin = $_GET["TipoFin"];  
	$empresaNome = $_GET["EmpresaNome"];
  
  //Efetua o switch para o campo de status
  switch ($TipoFin) {
    case 1: $desc_fin = "A Receber"; break;
    case 2: $desc_fin = "Recebido"; break;
  	case 3: $desc_fin = "Cortesia"; break;	
  } 
  
  //Ajusta a fonte
  $this->SetFont("Arial","",9);
  //Titulo do relatório
	$this->Cell(0,4, $empresaNome);
	$this->Cell(0,4, date("d/m/Y", mktime()),0,0,"R");
	$this->Ln();
	$this->SetFont("Arial","B",15);
  $this->Cell(0,6,"Relação de Eventos por Posição Financeira");
  $this->Ln();
	$this->SetFont("Arial","",11);
  $this->Cell(0,6,"Posição Financeira: " . $desc_fin);
  $this->SetFont("Arial","",9);
	$this->Cell(0,4, "Pagina: " . $this->PageNo(),0,0,"R");    
  //Line break
  $this->Ln(10);
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
$pdf->SetTitle('Relação de Eventos por Posição Financeira');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');

//Títulos das colunas
$pdf->SetFont('Arial', 'B', 10);
//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);
//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(0,6, 'Data',1,0,'L',1);
$pdf->SetX(31);
$pdf->Cell(0,6, 'Hora');
$pdf->SetX(44);
$pdf->Cell(0,6, 'Evento');
$pdf->SetX(175);
$pdf->Cell(0,6, 'Cliente');
$pdf->SetX(256);
$pdf->Cell(0,6, 'Status');

//Monta as linhas com os dados da query
$pdf->SetFont('Arial','',10);
while ($dados = mysql_fetch_array($sql)){

	switch ($dados[status]) {
	  case 0: $desc_status = "Em orçamento"; break;
	  case 1: $desc_status = "Em aberto"; break;
		case 2: $desc_status = "Realizado"; break;
	  case 3: $desc_status = "Não-Realizado"; break;
	} 

  $pdf->ln();
  $pdf->Cell(0,5, DataMySQLRetornar($dados['data_realizacao']));
  $pdf->SetX(31);
  $pdf->Cell(0,5, $dados['hora_realizacao']);
  $pdf->SetX(44);
  $pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(0,5, $dados['nome']);
	$pdf->SetFont('Arial', '', 10);
  $pdf->SetX(175);
  $pdf->Cell(0,5, $dados['cliente_nome']);
  $pdf->SetX(256);
  $pdf->Cell(0,5, $desc_status);
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->ln();
$pdf->Cell(0,6, 'Total de registros listados: ' . $registros,'T');
//Gera o PDF
$pdf->Output();
?>