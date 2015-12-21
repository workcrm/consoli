<?php
###########
## Módulo de relatório de produtos e eventos por data
## Criado: 14/03/2014 - Maycon Edinger
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
$Produto = $_GET["Produto"];
$Categoria = $_GET["Categoria"];
$Departamento = $_GET["Departamento"];
$Regiao = $_GET["Regiao"];
$Evento = $_GET["Evento"];
$Valores = $_GET["Valores"];

if ($Produto > 0) $where_produto = "AND ite.item_id = " . $Produto;
if ($Categoria > 0) $where_categoria = "AND prod.categoria_id = " . $Categoria;
if ($Departamento > 0) $where_departamento = "AND prod.departamento_id = " . $Departamento;
if ($Regiao > 0) $where_regiao = "AND eve.regiao_id = " . $Regiao;
if ($Evento > 0) $where_evento = "AND eve.id = " . $Evento;

//Busca o nome do produto
//Monta o sql
$sql_produto = mysql_query("SELECT nome FROM item_evento WHERE id = $Produto");

//Monta o array com os dados
$dados_produto = mysql_fetch_array($sql_produto);

$desc_produto = $dados_produto["nome"];

$sql = mysql_query("SELECT 
	                    ite.evento_id,
	                    ite.item_id,
	                    ite.quantidade,
	                    ite.valor_venda,
	                    ite.observacoes,
	                    eve.nome AS evento_nome,			
	                    eve.data_realizacao,
	                    eve.duracao,
	                    prod.nome AS produto_nome
                    FROM 
                    	eventos_item ite 
                    LEFT OUTER JOIN 
                    	eventos eve ON eve.id = ite.evento_id
                    LEFT OUTER JOIN 
                    	item_evento prod ON prod.id = ite.item_id
                    WHERE 
                    	eve.data_realizacao >= '$DataIni' 
                  	AND 
                  		eve.data_realizacao <= '$DataFim' 
	                    $where_produto
	                    $where_categoria
	                    $where_departamento
	                    $where_regiao
	                    $where_evento
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

    global $Produto;
    global $desc_produto;

    $empresaNome = $_GET["EmpresaNome"];
    
    //Ajusta a fonte
    $this->SetFont("Arial","",9);
    //Titulo do relatório
    $this->Cell(0,4, $empresaNome);
    $this->Cell(0,4, date("d/m/Y", mktime()),0,0,"R");
    
    $this->Ln();
    $this->SetFont("Arial","B",15);
    $this->Cell(0,6,"Relação de Alocação de Produtos em Eventos por Data");
    $this->SetFont("Arial","",9);
    $this->Cell(0,5, "Pagina: " . $this->PageNo(),0,0,"R");
    
    $this->Ln();
    $this->SetFont("Arial","",10);
    $this->Cell(0,5,"Alocados em Eventos no Período: " . $DataIni . " a " . $DataFim);
    
    //Line break
    $this->Ln(8); 
    
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
$pdf->SetTitle("RElatorio");
$pdf->SetSubject("Relatório gerado automaticamente pelo sistema");
$pdf->AliasNbPages();
$pdf->AddPage("L");

//Títulos das colunas
$pdf->SetFont("Arial", "B", 10);

//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);

//Monta as linhas com os dados da query
$pdf->SetFont("Arial","",10);

//Caso tenha registros
if ($registros > 0)
{

  //Cabeçalho
  $pdf->SetFont("Arial", "B", 9);
  $pdf->Cell(0,5, 'Dt Evento',1,0,'L',1);
  $pdf->SetX(26);
  $pdf->Cell(0,5, '    Quant.');

  //Caso deva exibir os valores
  if ($Valores == 1)
  {

	  $pdf->SetX(46);
	  $pdf->Cell(0,5, 'Vlr Venda');
	  $pdf->SetX(66);
	  $pdf->Cell(0,5, 'Total');

	}

  $pdf->SetX(86);
  $pdf->Cell(0,5, 'Evento');
  $pdf->ln();
  
  while ($dados = mysql_fetch_array($sql))
  {

    $EventoId = $dados['evento_id'];

    $pdf->ln();
    $pdf->SetFont("Arial", "B", 9);
    $pdf->Cell(0,5, "Produto: " . $dados["produto_nome"],1,1,'L',1);
    $pdf->SetFont("Arial", "", 9);
    $pdf->Cell(20,5, DataMySQLRetornar($dados["data_realizacao"]),1,0,'C');
    $pdf->Cell(16,5, $dados['quantidade'],1,0,'R');

    //Caso deva exibir os valores
	  if ($Valores == 1)
	  {
    
	    $pdf->Cell(20,5, number_format($dados['valor_venda'],2,',','.'),1,0,'R');
	    $pdf->Cell(20,5, number_format($dados['valor_venda'] * $dados['quantidade'],2,',','.'),1,0,'R');
	    $edtTotalGeral+= $dados['valor_venda'] * $dados['quantidade'];

	  }

    $pdf->Cell(0,5, '[' . $dados['evento_id'] . '] - ' . $dados['evento_nome'],1);
    
    $total_quantidade = $total_quantidade + $dados['quantidade'];
    
    //Caso tenha observações
    if ($dados['observacoes'] != '')
    {
      
      $pdf->ln();
      $pdf->SetFont("Arial", "I", 7);
      $pdf->SetX(86);
      $pdf->Multicell(0,4, $dados['observacoes'],1);
      
    } else $pdf->ln();
    
  }
  
  //Totalização
  $pdf->ln();
  $pdf->SetFont("Arial", "B", 9);
  $pdf->Cell(20,5, 'TOTAL:',1,0,'R',1);
  $pdf->Cell(16,5, number_format($total_quantidade,2,'.',''),1,0,'R',1);

  //Caso deva exibir os valores
  if ($Valores == 1)
  {

	  $pdf->Cell(20,5, number_format($edtTotalGeral / $total_quantidade,2,',','.'),1,0,'R',1);
	  $pdf->Cell(20,5, number_format($edtTotalGeral,2,',','.'),1,0,'R',1);

	}
  
}

else
{
  
  //Cabeçalho
  $pdf->SetFont("Arial", "B", 11);
  $pdf->Cell(0,6, "Nenhum produto associado em evento",1,0,'L',1);
  
}

//Gera o PDF
$pdf->Output();

?>