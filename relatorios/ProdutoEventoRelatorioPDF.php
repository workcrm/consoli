<?php
###########
## Módulo de relatório geral de produtos do evento
## Criado: 20/05/2007 - Maycon Edinger
## Alterado: 26/11/2007 - Maycon Edinger
## Alterações: 
###########

require('../fpdf/fpdf.php');

include "../conexao/ConexaoMySQL.php";

//Recupera os valores para filtragem

$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];
$CategoriaId = $_GET["CategoriaId"];

if ($CategoriaId)
{

	$filtra_categoria = "AND ite.categoria_id = '$CategoriaId'";

}

$sql = mysql_query("SELECT 
					ite.id,
					ite.nome,
					ite.classificacao,
					ite.valor_custo,
					ite.estoque_atual,
					ite.estoque_minimo,
					ite.unidade,
					ite.ativo,
					ite.categoria_id,
					ite.localizacao_1,
					ite.localizacao_2,
					ite.localizacao_3,
					cat.nome as categoria_nome 
					FROM item_evento ite 
					LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
					WHERE ite.empresa_id = $empresaId AND ite.tipo_produto = '1' 
					$filtra_categoria
					ORDER BY cat.nome, ite.nome");
								
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
		$this->Cell(0,6,'Relação Geral de Produtos');
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
$pdf->SetTitle('Relação Geral de Produtos');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');

//Títulos das colunas
$pdf->SetFont('Arial', 'B', 9);
//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);
//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(0,6, '    Cod.',1,0,'L',1);
$pdf->SetX(27);
$pdf->Cell(140,6, 'Descrição do Produto');
$pdf->Cell(10,6, 'Un');
$pdf->Cell(22,6, 'Custo',0,0,'R');
$pdf->Cell(22,6, 'Mínimo',0,0,'R');
$pdf->Cell(22,6, 'Atual',0,0,'R');
$pdf->Cell(30,6, 'Localização',0,0,'C');
$pdf->Cell(0,6, 'Ativo');

$categoria_lista = 0;

//Monta as linhas com os dados da query
while ($dados = mysql_fetch_array($sql))
{
	
	switch ($dados[ativo]) 
	{
  
		case 0: $ativo = "Inativo";	break;
		case 1: $ativo = "Ativo";	break;     	
  
	}
	
	//Caso seja uma outra categoria de produtos.
	if ($categoria_lista != $dados["categoria_id"])
	{
	
		$pdf->ln(10);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(0,5, $dados['categoria_nome']);
	
	}
	
	$pdf->ln();
	$pdf->SetFont('Arial','',9);
	$pdf->SetX(14);
	$pdf->Cell(14,5, $dados['id']);
	$pdf->Cell(140,5, $dados['nome']);
	$pdf->Cell(10,5, $dados['unidade']);
	$pdf->Cell(22,5, "R$ " . number_format($dados['valor_custo'], 2, ",", "."),0,0,'R'); 
	$pdf->Cell(22,5, number_format($dados['estoque_minimo'], 3, ".", ","),0,0,'R');
	$pdf->Cell(22,5, number_format($dados['estoque_atual'], 3, ".", ","),0,0,'R');
	$pdf->Cell(30,5, $dados['localizacao_1'] . "/" . $dados["localizacao_2"] . "/" . $dados["localizacao_3"], 0, 0, 'C');
	$pdf->Cell(0,5, $ativo);
	
	$categoria_lista = $dados["categoria_id"];
	
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->ln();
$pdf->Cell(0,6, 'Total de registros listados: ' . $registros,'T');

//Gera o PDF
$pdf->Output();
?>