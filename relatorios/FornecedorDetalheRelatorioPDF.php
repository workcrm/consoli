<?php
###########
## Módulo para montagem do relatório de Detalhamento do fornecedor em PDF
## Criado: 18/04/07 - Maycon Edinger
## Alterado: 28/05/2007 - Maycon Edinger
## Alterações: 
## 28/05/2007 - Implementado o campo ClienteID para a tabela 
###########
/**
* @package workeventos
* @abstract Módulo para montagem do relatório de Detalhamento do fornecedor em PDF
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//http://localhost/consoli/relatorios/FornecedorDetalheRelatorioPDF.php?FornecedorId=1&UsuarioNome=Maycon%20Edinger&EmpresaNome=Nome%20da%20empresa

//Acesso as rotinas do PDF
require('../fpdf/fpdf.php');

//Inclui o arquivo de conexão com o servidor
include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "../include/ManipulaDatas.php";

//Recupera os valores para filtragem
$FornecedorId = $_GET["FornecedorId"];

//Monta o SQL
$sql_conta = "SELECT 
		  		  con.id,
				  	con.ativo,
				  	con.empresa_id,
				  	con.nome,
				  	con.tipo_pessoa,
				  	con.endereco,
				  	con.complemento,
				  	con.bairro,
				  	con.cidade_id,
				  	con.uf,
				  	con.cep,
				  	con.inscricao,
				  	con.cnpj,
				  	con.rg,
				  	con.cpf,
				  	con.telefone,
				  	con.fax,
				  	con.celular,
				  	con.email,		  
				  	con.contato,
				  	con.observacoes,
				  	con.cadastro_timestamp,
				  	con.cadastro_operador_id,
				  	con.alteracao_timestamp,
				  	con.alteracao_operador_id,
				  	cid.nome as cidade_nome,
				  	usu_cad.nome as operador_cadastro_nome, 
				  	usu_cad.sobrenome as operador_cadastro_sobrenome,
				  	usu_alt.nome as operador_alteracao_nome, 
				  	usu_alt.sobrenome as operador_alteracao_sobrenome
		
				  	FROM fornecedores con
				  	INNER JOIN usuarios usu_cad ON con.cadastro_operador_id = usu_cad.usuario_id 
				  	LEFT OUTER JOIN usuarios usu_alt ON con.alteracao_operador_id = usu_alt.usuario_id
						LEFT OUTER JOIN cidades cid ON cid.id = con.cidade_id				  	
				  	WHERE con.id = '$FornecedorId'";

//Executa a query de consulta do fornecedor
$query_conta = mysql_query($sql_conta);

//Monta a matriz com os dados do fornecedor
$dados_conta = mysql_fetch_array($query_conta);

//Efetua o switch para a figura do tipo de fornecedor
switch ($dados_conta[tipo_pessoa]) {
  case 1: 
		//String da figura da conta
		$conta_figura = "../image/bt_prospect_relatorio.jpg";
		//Descrição do tipo de conta
		$conta_descricao = "Física";	
	break;
  case 2: 
		//String da figura da conta
		$conta_figura = "../image/bt_cliente_relatorio.jpg"; 
		//Descrição do tipo de conta
		$conta_descricao = "Jurídica";
	break;
}
 
//Efetua o switch para o campo de ativo
switch ($dados_conta[ativo]) {
  case 0: $desc_ativo = "Cadastro Inativo"; break;
	case 1: $desc_ativo = "Cadastro Ativo"; break;
}    
  
//Chama a classe para gerar o PDF
class PDF extends FPDF
{
//Cabeçalho do relatório
function Header()
{    
	//Recupera o nome da empresa
	$empresaNome = $_GET["EmpresaNome"];
  //Ajusta a fonte
  $this->SetFont('Arial','',9);
  //Titulo do relatório
	$this->Cell(0,4, $empresaNome);
	$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
	$this->Ln();
	$this->SetFont('Arial','B',15);
  $this->Cell(0,6,'Detalhamento do Fornecedor');
  $this->SetFont('Arial','',9);
	$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
  //Line break
  $this->Ln(10);
}

//Rodapé do Relatório
function Footer()
{
   $usuarioNome = $_GET["UsuarioNome"];
   //Posiciona a 1.5 cm do final
   $this->SetY(-15);    
   //Arial italico 8
   $this->SetFont('Arial','I',7);
   $this->Line(10,281,200,281);
   $this->Cell(0,3,'Emitido por: ' . $usuarioNome);
}
}

//Instancia a classe gerador de pdf
$pdf=new PDF();
//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('edinger@bol.com.br');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Detalhamento do fornecedor');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();


$pdf->AddPage();

$pdf->SetY(25);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, 'Detalhamento do Fornecedor',1,0,'C',1);

//Nova Linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Tipo Forneced:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(39);
$pdf->Cell(0,6, $conta_descricao,0,0,'L');
$pdf->SetX(57);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Nome/Razão Social:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(88);
$pdf->Cell(0,6, $dados_conta['nome'],0,0,'L');
$pdf->Image($conta_figura,35,32,4,4);

//Nova Linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Endereço:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta['endereco'],0,0,'L');
$pdf->SetX(105);


//Nova Linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Complemento:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta['complemento'],0,0,'L');
$pdf->SetX(105);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Bairro:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(120);
$pdf->Cell(0,6, $dados_conta['bairro'],0,0,'L');

$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(95,6, 'Cidade/UF:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta['cidade_nome'] . " - " . $dados_conta['uf'],0,0,'L');
$pdf->SetX(105);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Cep:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(120);
$pdf->Cell(0,6, $dados_conta['cep'],0,0,'L');

//Nova Linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(95,6, 'Inscr. Estadual:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta['inscricao'],0,0,'L');
$pdf->SetX(105);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'CNPJ:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(120);
$pdf->Cell(0,6, $dados_conta['cnpj'],0,0,'L');

//Nova Linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(95,6, 'Nº RG:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta['rg'],0,0,'L');
$pdf->SetX(105);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Cpf:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(120);
$pdf->Cell(0,6, $dados_conta['cpf'],0,0,'L');

//Nova Linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(66,6, 'Telefone:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta['telefone'],0,0,'L');
$pdf->SetX(76);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(59,6, 'Fax:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(85);
$pdf->Cell(0,6, $dados_conta['fax'],0,0,'L');
$pdf->SetX(135);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(65,6, 'Celular:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(149);
$pdf->Cell(0,6, $dados_conta['celular'],0,0,'L');

//Nova Linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(95,6, 'E-mail:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta['email'],0,0,'L');
$pdf->SetX(105);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Contato',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(120);
$pdf->Cell(0,6, $dados_conta['contato'],0,0,'L');

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, 'Observações',1,0,'C',1);
$pdf->ln();
$pdf->SetFont('Arial', 'I', 10);
$pdf->MultiCell(0,4, $dados_conta['observacoes'],1);

//Gera o PDF
$pdf->Output();

?>                                                      