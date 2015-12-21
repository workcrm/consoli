<?php
###########
## M�dulo de relat�rio geral de servi�os
## Criado: 26/11/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

require('../fpdf/fpdf.php');

include "../conexao/ConexaoMySQL.php";

//Recupera os valores para filtragem

$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];


$sql = mysql_query("SELECT * FROM servico_evento WHERE empresa_id = '$empresaId' ORDER BY nome");
$registros = mysql_num_rows($sql);  

class PDF extends FPDF
{
//Cabe�alho do relat�rio
function Header()
{
    
	$empresaNome = $_GET["EmpresaNome"];
    //Ajusta a fonte
    $this->SetFont('Arial','',9);
    //Titulo do relat�rio
	$this->Cell(0,4, $empresaNome);
	$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
	$this->Ln();
	$this->SetFont('Arial','B',15);
  $this->Cell(0,6,'Rela��o de Servi�os');
  $this->SetFont('Arial','',9);
	$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
  //Line break
  $this->Ln(10);
}

//Rodap� do Relat�rio
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
$pdf->SetTitle('Rela��o de Servi�os');
$pdf->SetSubject('Relat�rio gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage();

//T�tulos das colunas
$pdf->SetFont('Arial', 'B', 10);
//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);
//Faz a c�lula ser preenchida. Isso � feito setando 1 ap�s a expressao de alinhamento
$pdf->Cell(0,6, 'Descri��o do Servi�o',1,0,'L',1);
$pdf->SetX(136);
$pdf->Cell(22,6, 'Pr. Custo',0,0,'R');
$pdf->SetX(158);
$pdf->Cell(22,6, 'Pr. Venda',0,0,'R');
$pdf->SetX(184);
$pdf->Cell(0,6, 'Ativo');

//Monta as linhas com os dados da query
while ($dados = mysql_fetch_array($sql)){
  switch ($dados[ativo]) {
  case 0: $ativo = "Inativo";	break;
	case 1: $ativo = "Ativo";	break;     	
  }
  $pdf->ln();
  $pdf->SetFont('Arial','',10);
	$pdf->Cell(0,5, $dados['nome']);
	$pdf->SetX(136);
  $pdf->SetFont('Arial','',9);
	$pdf->Cell(22,5, "R$ " . number_format($dados['valor_custo'], 2, ",", "."),0,0,'R'); 
	$pdf->SetX(158);
  $pdf->Cell(22,5, "R$ " . number_format($dados['valor_venda'], 2, ",", "."),0,0,'R');	 
  $pdf->SetX(184);
  $pdf->Cell(0,5, $ativo);
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->ln();
$pdf->Cell(0,6, 'Total de registros listados: ' . $registros,'T');
//Gera o PDF
$pdf->Output();
?>