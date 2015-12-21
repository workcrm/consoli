<?php
###########
## Módulo de relatório geral de cheques
## Criado: 12/09/2007 - Maycon Edinger
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
$dataIni = DataMySQLInserir($_GET[DataIni]);
$dataFim = DataMySQLInserir($_GET[DataFim]);

//Verifica se foi informado alguma data para filtrar junto
if ($dataIni != 0) {
	$TextoFiltraData = "E com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
	$TextoSQLData = "	 AND che.data_vencimento >= '$dataIni' AND che.data_vencimento <= '$dataFim' ";
}

//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) {	

  //Se for 1 então é visualização por situacao
	case 1:
		//Monta as variáveis
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) {
  	//Se for 1 então é visualização em aberto
		case 0:
			$where_filtragem = "WHERE che.empresa_id = '$empresaId' $TextoSQLData ORDER BY che.pago_a";		
		break;		
		//Se for 1 então é visualização em aberto
		case 1:
			$where_filtragem = "WHERE che.empresa_id = '$empresaId' AND che.status = '$TipoSituacao' $TextoSQLData ORDER BY che.pago_a";		
		break;		
  	//Se for 2 então é visualização das compensadas
		case 2:
			$where_filtragem = "WHERE che.empresa_id = '$empresaId' AND che.status = '$TipoSituacao' $TextoSQLData ORDER BY che.pago_a";
		break;
  	//Se for 3 então é visualização dos que voltaram
		case 3:
			$where_filtragem = "WHERE che.empresa_id = '$empresaId' AND che.status = '$TipoSituacao' $TextoSQLData ORDER BY che.pago_a";
		break;
		}
  
}

$sql="SELECT 
			che.id,
			che.numero,
			che.banco_id,
			che.nome,
			che.pago_a,
			che.valor,
			che.data_vencimento,
			che.status,
			ban.nome as banco_nome
			FROM cheques che
			LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
			
			$where_filtragem ";

$query = mysql_query($sql);
	  	
$registros = mysql_num_rows($query);  

class PDF extends FPDF
{
//Cabeçalho do relatório
function Header()
{
  
  //Pega a data inicial pra ver se veio vazia
  $dataIni = DataMySQLInserir($_GET[DataIni]);
	  
	$empresaNome = $_GET["EmpresaNome"];
 	//Monta o switch para criar o texto da filtragem
	switch ($_GET[TipoListagem]) {

  //Se for 1 então é visualização por situacao
	case 1:
		//Monta as variáveis
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) {		
  	//Se for 0 então é visualização de todos
		case 0: $texto_situacao = "Todos"; break;		
		//Se for 1 então é visualização em aberto
		case 1: $texto_situacao = "Em aberto"; break;		
  	//Se for 2 então é visualização dos compensados
		case 2: $texto_situacao = "Compensados"; break;
  	//Se for 3 então é visualização dos que voltaram
		case 3: $texto_situacao = "Voltou"; break;
		}
		
		//Monta a descrição a exibir
		$desc_filtragem = "Cheques com situacao: $texto_situacao";
		//Se tem filtragem acrescenta as datas ao texto
		if ($dataIni != 0) {
			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
		}
	break;	
	
	
	}

	
  //Ajusta a fonte
  $this->SetFont('Arial','',9);
  //Titulo do relatório
	$this->Cell(0,4, $empresaNome);
	$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
	$this->Ln();
	$this->SetFont('Arial','B',15);
  $this->Cell(0,6,'Relação Geral de Cheques');
  $this->SetFont('Arial','',9);
	$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
  $this->Ln(5);
  $this->SetFont('Arial', 'B', 10);
  $this->Cell(19,6,'Filtragem:');
  $this->SetFont('Arial', '', 10);
  $this->Multicell(0,6, $desc_filtragem);
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
$pdf->SetTitle('Relação Geral de Cheques');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');

//Títulos das colunas
$pdf->SetFont('Arial', 'B', 10);
//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);
//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(0,6, 'Numero',1,0,'L',1);
$pdf->SetX(30);
$pdf->Cell(0,6, 'Banco');
$pdf->SetX(90);
$pdf->Cell(0,6, 'Nome');
$pdf->SetX(170);
$pdf->Cell(0,6, 'Pago a');
$pdf->SetX(235);
$pdf->Cell(0,6, 'Vencim.');
$pdf->SetX(246);
$pdf->Cell(24,6, 'Valor',0,0,'R');
$pdf->SetX(270);
$pdf->Cell(0,6, 'Status');

//Seta a variável do total a pagar zerado
$total_pagar = 0;

//Percorre o array dos dados
while ($dados = mysql_fetch_array($query)){
	
	//Efetua o switch para o campo de situação
  switch ($dados[status]) {
  	case 1: $texto_status = "Em aberto";	break;
		case 2: $texto_status = "Compensado";	break;     	
		case 3: $texto_status = "Voltou";	break;     	
  }
  
  $pdf->ln();
  $pdf->SetFont('Arial','',10);
	$pdf->Cell(0,5, $dados['numero']);
	$pdf->SetFont('Arial','',8);
	$pdf->SetX(30);
  $pdf->Cell(0,5, $dados['banco_nome']);
	$pdf->SetX(90);
  $pdf->Cell(0,5, $dados['nome']);
	$pdf->SetX(170);
  $pdf->Cell(0,5, $dados['pago_a']);
	$pdf->SetX(235);
  $pdf->Cell(0,5, DataMySQLRetornar($dados['data_vencimento']));  
  $pdf->SetX(246);
  $pdf->Cell(24,5, "R$ " . number_format($dados['valor'], 2, ",", "."),0,0,"R");
  $pdf->SetX(270);
  $pdf->Cell(0,5, $texto_status);
	//Acumula o valor a pagar
	$total_pagar = $total_pagar + $dados['valor'];	  
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->ln();
$pdf->Cell(0,6, 'Total de registros listados: ' . $registros,'T');
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(224);
$pdf->Cell(20,6, 'Total:',0,0,'R');
$pdf->SetX(246);
$pdf->Cell(24,6, "R$ " . number_format($total_pagar, 2, ",", "."),0,0,'R');

//Gera o PDF
$pdf->Output();
?>