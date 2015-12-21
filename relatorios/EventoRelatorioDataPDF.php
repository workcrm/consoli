<?php
###########
## Módulo de relatório de eventos por data
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
$edtRegiao = $_GET["Regiao"];
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

if ($edtRegiao > 0)
{
  
  $where_regiao = "AND eve.regiao_id = '$edtRegiao'";
  
}

$sql = mysql_query("SELECT 
	                    eve.id,
	                    eve.nome,
	                    eve.descricao,
	                    eve.status,
	                    eve.cliente_id,				
	                    eve.data_realizacao,
	                    eve.hora_realizacao,
	                    eve.duracao,
	                    cli.nome as cliente_nome,
                    	FLOOR(DATEDIFF(eve.data_realizacao, now()) / 7) AS semanas
                    FROM 
                    	eventos eve 
                    LEFT OUTER JOIN 
                    	clientes cli ON cli.id = eve.cliente_id
                    WHERE 
                    	eve.data_realizacao >= '$DataIni' AND eve.data_realizacao <= '$DataFim' 
	                    $where_tipo 
	                    $where_status
	                    $where_regiao
                    ORDER BY 
                    	eve.data_realizacao, eve.hora_realizacao, eve.nome");

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
    $this->Cell(0,6,"Relação de Eventos por Data");
    $this->Ln();
    $this->SetFont("Arial","",11);
    $this->Cell(0,6,"Período: " . $DataIni . " a " . $DataFim);
    $this->SetFont("Arial","",9);
    $this->Cell(0,5, "Pagina: " . $this->PageNo(),0,0,"R");
    $this->Ln();
    $this->Cell(0,5, $desc_tipo);
    $this->Ln();
    $this->Cell(0,5, $desc_status);

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
    $this->SetFont("Arial", "I",7);
    //Page number
    $this->Line(10,281,200,281);
    $this->Cell(0,3,"Emitido por: " . $usuarioNome);

  }

}

//Instancia a classe gerador de pdf
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator("work | eventos");
$pdf->SetAuthor($usuarioNome . ' - ' . $empresaNome);
$pdf->SetTitle("Relação de Eventos por Data");
$pdf->SetSubject("Relatório gerado automaticamente pelo sistema");
$pdf->AliasNbPages();
$pdf->AddPage("L");

//Títulos das colunas
$pdf->SetFont("Arial", "B", 10);

//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);

//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(0,6, "Data", 1, 0, "L",1);
$pdf->SetX(31);
$pdf->Cell(0,6, "Hora");
$pdf->SetX(44);
$pdf->Cell(0,6, "Código");
$pdf->SetX(60);
$pdf->Cell(0,6, "Evento");
$pdf->SetX(235);
$pdf->Cell(0,6, "Status");
$pdf->SetX(266);
$pdf->Cell(0,6, "Semanas");

//Monta as linhas com os dados da query
$pdf->SetFont("Arial","",10);

while ($dados = mysql_fetch_array($sql))
{

  switch ($dados[status]) 
  {
    case 0: $desc_status = "Em orçamento"; break;
    case 1: $desc_status = "Em aberto"; break;
    case 2: $desc_status = "Realizado"; break;
    case 3: $desc_status = "Não-Realizado"; break;
  } 

  $pdf->ln();
  $pdf->SetFont("Arial", "", 10);
  $pdf->Cell(0,5, DataMySQLRetornar($dados["data_realizacao"]));
  $pdf->SetX(31);
  $pdf->Cell(0,5, $dados["hora_realizacao"]);
  $pdf->SetX(44);
  $pdf->SetFont("Arial", "B", 10);
  $pdf->Cell(0,5, $dados["id"]);
  $pdf->SetX(60);
  $pdf->SetFont("Arial", "B", 10);
  $pdf->Cell(0,5, $dados["nome"]);
  $pdf->SetFont("Arial", "", 10);
  $pdf->SetX(235);
  $pdf->Cell(0,5, $desc_status);
  $pdf->SetX(266);
  $pdf->Cell(0,5, $dados["semanas"],0,0,'C');
  $pdf->ln();
  $pdf->SetFont("Arial", "", 8);
  $pdf->Cell(0,4, '','B');
  $pdf->SetX(60);
  $pdf->Cell(0,4, $dados["cliente_nome"]);

}

$pdf->SetFont("Arial", "B", 8);
$pdf->ln();
$pdf->Cell(0,6, "Total de registros listados: " . $registros, "T");

//Gera o PDF
$pdf->Output();

?>