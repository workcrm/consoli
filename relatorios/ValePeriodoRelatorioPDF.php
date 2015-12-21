<?php
###########
## Módulo para montagem do relatório de Detalhamento do Colaborador em PDF
## Criado: 23/04/07 - Maycon Edinger
## Alterado: 05/06/2007 - Maycon Edinger
## Alterações: 
## 20/05/2007 - Incluídos os novos campos
## 28/05/2007 - Implementado o campo ClienteID para a tabela
## 05/06/2007 - Implementado rotinas de segurança para ver os dados
###########

//http://localhost/consoli/relatorios/ColaboradorDetalheRelatorioPDF.php?ColaboradorId=1&UsuarioNome=Maycon%20Edinger&EmpresaNome=Nome%20da%20empresa

//Acesso as rotinas do PDF
require("../fpdf/fpdf.php");

//Inclui o arquivo de conexão com o servidor
include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "../include/ManipulaDatas.php";

//Captura o evento informado
$edtDataIni = DataMySQLInserir($_GET["DataIni"]);
$edtDataFim = DataMySQLInserir($_GET["DataFim"]);
$edtDataIniNormal = $_GET["DataIni"];
$edtDataFimNormal = $_GET["DataFim"];
  
//Chama a classe para gerar o PDF
class PDF extends FPDF
{
//Cabeçalho do relatório
function Header()
{    
	
  global $edtDataIniNormal;
  global $edtDataFimNormal;
  
  //Recupera o nome da empresa
	$empresaNome = $_GET["EmpresaNome"];
  //Ajusta a fonte
  $this->SetFont("Arial","",9);
  //Titulo do relatório
	$this->SetX(58);
  $this->Cell(0,4, $empresaNome);
	$this->Cell(0,4, date("d/m/Y", mktime()),0,0,"R");
	$this->Ln();
  $this->SetX(58);
	$this->SetFont("Arial","B",15);
  $this->Cell(0,6,"Relação de Vales Emitidos por Período");
  $this->SetFont("Arial","",9);
	$this->Cell(0,4, "Pagina: ".$this->PageNo(),0,0,"R"); 
  $this->Ln(8);
  $this->SetX(58);
	$this->SetFont("Arial","B",11); 
  $this->Cell(0,4, "Período: " . $edtDataIniNormal . " a " . $edtDataFimNormal,0,0,"L"); 
  //Imprime o logotipo da empresa
	$this->Image("../image/logo_consoli2.jpg",10,10,46);
  
  //Line break
  $this->Ln(16);
}

//Rodapé do Relatório
function Footer()
{
   $usuarioNome = $_GET["UsuarioNome"];
   //Posiciona a 1.5 cm do final
   $this->SetY(-15);    
   //Arial italico 8
   $this->SetFont("Arial","I",7);
   $this->Line(10,281,200,281);
   $this->Cell(0,3,"Emitido por: " . $usuarioNome);
}
}

//Instancia a classe gerador de pdf
$pdf=new PDF();
//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator("mayconedinger@gmail.com");
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle("Detalhamento do colaborador");
$pdf->SetSubject("Relatório gerado automaticamente pelo sistema");
$pdf->AliasNbPages();


$pdf->AddPage("L");


//Lista os vales fornecidos ao colaborador
//verifica os vales deste colaborador e exibe na tela
$sql_consulta = mysql_query("SELECT 
                            vale.data,
                            vale.id,
                            vale.valor,
							vale.colaborador_id,
                            vale.observacoes,
                            vale.data_devolucao,
                            col.nome as colaborador_nome
                            FROM vales vale
                            LEFT OUTER JOIN colaboradores col ON col.id = vale.colaborador_id																	
                            WHERE vale.data >= '$edtDataIni' AND vale.data <= '$edtDataFim'
							AND vale.colaborador_id <> 0
                            ORDER by vale.data, colaborador_nome
														 ");

//Verifica o numero de registros retornados
$registros = mysql_num_rows($sql_consulta); 

//Verifica se há vales para o colaborador
if ($registros == 0){

  $pdf->SetFont("Arial", "BI", 10);
  $pdf->Cell(0,6, "Não há vales emitidos neste período",1,0,"L");

} else {
  
  $pdf->SetFont("Arial", "B", 10);
  $pdf->SetFillColor(178,178,178);
  $pdf->Cell(0,6, " Data Emissão",1,0,"L",1);
  $pdf->SetX(37);
  $pdf->Cell(90,6, "Colaborador",1,0,"C",1);
  $pdf->Cell(24,6, "Valor",1,0,"C",1);   
  $pdf->Cell(28,6, "Data Devolução",1,0,"C",1);
  $pdf->Cell(0,6, "Observações",1,0,"C",1);
  
  //Cria a variável do total de vales
	$total_vales = 0;
	
	//Cria o array e o percorre para montar a listagem dinamicamente
  while ($dados_consulta = mysql_fetch_array($sql_consulta)){
    
    $pdf->ln();
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(0,5, " " . DataMySQLRetornar($dados_consulta["data"]),"T");
    $pdf->SetX(37);
    $pdf->Cell(90,5, '(' . $dados_consulta["colaborador_id"] . ') - ' . $dados_consulta["colaborador_nome"], "T");
    $pdf->Cell(24,5, "R$ " . number_format($dados_consulta["valor"], 2, ",", "."),"T",0,"R");    
        
    if ($dados_consulta["data_devolucao"] != "0000-00-00")
    {
      
      $pdf->Cell(28,5, DataMySQLRetornar($dados_consulta["data_devolucao"]),"T",0,"C");
    
    } 
    else 
    {
    
      $pdf->Cell(28,5, " ","T",0,"C");
    
    }
    
    $pdf->SetFont("Arial", "", 8);
    $pdf->Multicell(0,3, $dados_consulta["observacoes"]);
    $pdf->SetFont("Arial", "", 10);
    
    $total_vales = $total_vales + $dados_consulta[valor];
  
  }
  
  $pdf->ln();
  $pdf->SetFont("Arial", "BI", 10);
  $pdf->SetFillColor(178,178,178);
  $pdf->Cell(0,7, "Valor total de vales: R$ " . number_format($total_vales, 2, ",", "."),1,0);
  
}

//Gera o PDF
$pdf->Output();

?>                                                      