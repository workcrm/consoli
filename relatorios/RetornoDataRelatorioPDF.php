<?php
###########
## Módulo de relatório de retornos por data
## Criado: 03/02/2011 - Maycon Edinger
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

//Captura o evento informado
$DataIni = DataMySQLInserir($_GET['DataIni']);
$DataFim = DataMySQLInserir($_GET['DataFim']);
$edtTipoConsulta = $_GET["TipoConsulta"];
 
switch ($edtTipoConsulta) 
{
  
  case 0: 
    $where_tipo = "";
  break;		  
  case 1: 
    $where_tipo = "AND proc.status = 1";
  break;
  case 2: 
    $where_tipo = "AND proc.status = 2";
  break;
  case 3: 
    $where_tipo = "AND proc.status = 3";
  break;
}

//Busca no banco de dados se existe o boleto
//Monta a query para pegar os dados
$sql = "SELECT 
        proc.data_processamento,
        proc.titulo,
        proc.valor,
        proc.juros,
        proc.status,
        bol.sacado AS sacado
        FROM retornos proc
        LEFT OUTER JOIN boleto bol ON bol.nosso_numero = proc.titulo
				WHERE proc.data_processamento >= '$DataIni' AND proc.data_processamento <= '$DataFim' $where_tipo
				ORDER BY proc.status, titulo";

//echo $sql . "<br/>";

//Executa a query
$query = mysql_query($sql);

//Conta o numero de registros da query
$registros = mysql_num_rows($query);

class PDF extends FPDF
{
//Cabeçalho do relatório
function Header()
{
  $DataIni = $_GET['DataIni'];
	$DataFim = $_GET['DataFim']; 
   
	$empresaNome = $_GET['EmpresaNome'];
  //Ajusta a fonte
  $this->SetFont('Arial','',9);
  //Titulo do relatório
	$this->Cell(0,4, $empresaNome);
	$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
	$this->Ln();
	$this->SetFont('Arial','B',15);
  $this->Cell(0,6,'Relação de Retornos por Data');
  $this->Ln();
	$this->SetFont('Arial','',11);
  $this->Cell(0,6,'Período: ' . $DataIni . ' a ' . $DataFim);
  $this->SetFont('Arial','',9);
	$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');
     
  //Line break
  $this->Ln(10);
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
$pdf->SetTitle('Relação de Retornos por Data');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');

//Verifica se existem registros
if ($registros > 0)
{

  //Define o status zero para fazer a quebra
  if ($edtTipoConsulta == "0")
  {
    $status_atual = 1;
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0,5, "Títulos Processados");
    $pdf->ln();
    
  }
  
  else
  
  {
    $status_atual = $edtTipoConsulta;
    
    switch ($status_atual) 
    {
      		  
      case 1: 
        $desc_situacao = "Títulos Processados";
      break;
      case 2: 
        $desc_situacao = "Titulos Não Processados - Juros";
      break;
      case 3: 
        $desc_situacao = "Títulos Não Encontrados";
      break;
    }
    
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0,5, $desc_situacao);
    $pdf->ln();
    
  }
  
  //Títulos das colunas
  $pdf->SetFont('Arial', 'B', 10);
  //Define a cor RGB do fundo da celula
  $pdf->SetFillColor(178,178,178);
  //Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
  $pdf->Cell(0,6, 'Data',1,0,'L',1);
  $pdf->SetX(31);
  $pdf->Cell(0,6, 'Nosso Número');
  $pdf->SetX(70);
  $pdf->Cell(150,6, 'Sacado');
  $pdf->Cell(30,6, 'Valor',0,0,'R');
  $pdf->Cell(30,6, 'Juros', 0,0,'R');
  
  //Monta as linhas com os dados da query
  $pdf->SetFont('Arial','',10);
  
  
  while ($dados = mysql_fetch_array($query))
  {
  
    switch ($dados["status"]) 
    {
      		  
      case 1: 
        $desc_situacao = "Títulos Processados";
      break;
      case 2: 
        $desc_situacao = "Titulos Não Processados - Juros";
      break;
      case 3: 
        $desc_situacao = "Títulos Não Encontrados";
      break;
    }
      
    if ($status_atual != $dados["status"])
    {      
      
      if ($status_atual > 0)
      {
        
        $subtotal_geral =  "R$ " . number_format($soma_subtotal, 2, ",", ".");
        $subtotal_geral_juros =  "R$ " . number_format($soma_juros_subtotal, 2, ",", ".");
        
        $pdf->ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0,5, " ");
        $pdf->SetX(190);
        $pdf->Cell(30,5, "Subtotal:",'T',0,'R');
        $pdf->Cell(30,5, $subtotal_geral, 'T',0,'R');
        $pdf->Cell(30,5, $subtotal_geral_juros, 'T',0,'R');
        
        $soma_subtotal = 0;
        $soma_juros_subtotal = 0;
  
      }
      
      $pdf->ln(10);
      $pdf->SetFont('Arial', 'B', 14);
      $pdf->Cell(0,5, $desc_situacao);
      $pdf->ln();
      $pdf->SetFont('Arial', 'B', 10);
      $pdf->SetFillColor(178,178,178);
      $pdf->Cell(0,6, 'Data',1,0,'L',1);
      $pdf->SetX(31);
      $pdf->Cell(0,6, 'Nosso Número');
      $pdf->SetX(70);
      $pdf->Cell(150,6, 'Sacado');
      $pdf->Cell(30,6, 'Valor',0,0,'R');
      $pdf->Cell(30,6, 'Juros', 0,0,'R');
    
    }
      
    $pdf->ln();
    $pdf->Cell(0,5, DataMySQLRetornar($dados['data_processamento']));
    $pdf->SetX(31);
    $pdf->Cell(0,5, $dados['titulo']);
    $pdf->SetX(70);
    $pdf->SetFont('Arial', 'B', 10);
  	$pdf->Cell(150,5, $dados['sacado']);
    $pdf->SetFont('Arial', '', 10);
  	$pdf->Cell(30,5, $dados['valor'],0,0,'R');
    $pdf->Cell(30,5, $dados['juros'],0,0,'R');
    
    $soma_total = $soma_total + $dados["valor"];
    $soma_subtotal = $soma_subtotal + $dados["valor"];
    
    $soma_juros_total = $soma_juros_total + $dados["juros"];
    $soma_juros_subtotal = $soma_juros_subtotal + $dados["juros"];
    
    $status_atual = $dados["status"];
    
  }
  
  $total_geral =  "R$ " . number_format($soma_total, 2, ",", ".");
  $total_geral_juros =  "R$ " . number_format($soma_juros_total, 2, ",", ".");
  
  $subtotal_geral =  "R$ " . number_format($soma_subtotal, 2, ",", ".");
  $subtotal_geral_juros =  "R$ " . number_format($soma_juros_subtotal, 2, ",", ".");
  
  if ($edtTipoConsulta == "0")
  {
      
    $pdf->ln();
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0,5, " ");
    $pdf->SetX(190);
    $pdf->Cell(30,5, "Subtotal:",'T',0,'R');
    $pdf->Cell(30,5, $subtotal_geral, 'T',0,'R');
    $pdf->Cell(30,5, $subtotal_geral_juros, 'T',0,'R');
  
  }
  
  $pdf->ln();
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(0,5, " ");
  $pdf->SetX(190);
  $pdf->Cell(30,5, "Total Geral:",'T',0,'R');
  $pdf->Cell(30,5, $total_geral, 'T',0,'R');
  $pdf->Cell(30,5, $total_geral_juros,'T',0,'R');
  
}

else

{
  
  $pdf->SetFont('Arial', 'B', 12);
  $pdf->Cell(0,7, "Não há retornos para o período e data especificado !",1,0);
  
}
  

//Gera o PDF
$pdf->Output();
?>