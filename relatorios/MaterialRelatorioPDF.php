<?php
###########
## Módulo de relatório geral de materiais
## Criado: 20/05/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo de Relatório geral de materiais
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

require('../fpdf/fpdf.php');

include "../conexao/ConexaoMySQL.php";

//Recupera os valores para filtragem

$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];


$sql = mysql_query("SELECT * FROM item_evento WHERE tipo_material = '1' AND empresa_id = '$empresaId' ORDER BY nome");
$registros = mysql_num_rows($sql);  

class PDF extends FPDF
{
//Cabeçalho do relatório
function Header()
{
    
	$empresaNome = $_GET["EmpresaNome"];
    //Ajusta a fonte
    $this->SetFont('Arial','',9);
    //Titulo do relatório
	$this->Cell(0,4, $empresaNome);
	$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
	$this->Ln();
	$this->SetFont('Arial','B',15);
  $this->Cell(0,6,'Relação de Materiais');
  $this->SetFont('Arial','',9);
	$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
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
$pdf->SetTitle('Relação de Materiais');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage();

//Títulos das colunas
$pdf->SetFont('Arial', 'B', 10);
//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);
//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(0,6, 'Descrição do Material',1,0,'L',1);
$pdf->SetX(146);
$pdf->Cell(0,6, 'Un');
$pdf->SetX(158);
$pdf->Cell(22,6, 'Pr. Custo',0,0,'R');
$pdf->SetX(184);
$pdf->Cell(0,6, 'Ativo');

//Monta as linhas com os dados da query
$pdf->SetFont('Arial','',10);
while ($dados = mysql_fetch_array($sql)){
  switch ($dados[ativo]) {
  case 0: $ativo = "Inativo";	break;
	case 1: $ativo = "Ativo";	break;     	
  }
  $pdf->ln();
  $pdf->Cell(0,5, $dados['nome']);
	$pdf->SetX(146);
  $pdf->Cell(0,5, $dados['unidade']);
	$pdf->SetX(158);
  $pdf->Cell(22,5, "R$ " . number_format($dados['valor_custo'], 2, ",", "."),0,0,'R');	 
  $pdf->SetX(184);
  $pdf->Cell(0,5, $ativo);
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->ln();
$pdf->Cell(0,6, 'Total de registros listados: ' . $registros,'T');
//Gera o PDF
$pdf->Output();
?>