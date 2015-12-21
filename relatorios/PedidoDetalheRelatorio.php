<?php
###########
## Módulo de relatório geral de detalhamento do pedido
## Criado: 30/09/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

require('../fpdf/fpdf.php');

include '../conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include '../include/ManipulaDatas.php';

//Recupera os valores para filtragem
$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];
$edtPedidoId = $_GET["PedidoId"];

//Busca os dados do pedido                                              
$sql_pedido = mysql_query("SELECT 
                          pedido.id,
                          pedido.data,
                          pedido.evento_id,
                          pedido.data_entrega,
                          pedido.formando_id,
                          pedido.fornecedor_id,
                          pedido.observacoes,
                          fornec.nome as fornecedor_nome,
                          fornec.endereco as fornecedor_endereco,
                          fornec.complemento as fornecedor_complemento,
                          fornec.bairro as fornecedor_bairro,
                          cidade.nome as cidade_fornecedor_nome,
                          fornec.uf as fornecedor_uf,
                          fornec.cep as fornecedor_cep,
                          fornec.telefone as fornecedor_telefone,
                          fornec.email as fornecedor_email,
                          fornec.contato as fornecedor_contato,
						  formando.id AS formando_id,
                          formando.nome AS formando_nome,
                          evento.nome AS evento_nome,
                          evento.data_realizacao as evento_data                          
                          FROM pedido_fv pedido
                          LEFT OUTER JOIN fornecedores fornec ON fornec.id = pedido.fornecedor_id
                          LEFT OUTER JOIN eventos_formando formando ON formando.id = pedido.formando_id
                          LEFT OUTER JOIN cidades cidade ON cidade.id = fornec.cidade_id
                          LEFT OUTER JOIN eventos evento ON evento.id = pedido.evento_id
                          WHERE pedido.id = $edtPedidoId
                          ");

//Monta o array com os dados
$dados_pedido = mysql_fetch_array($sql_pedido);


class PDF extends FPDF
{
  /*
  
  //Cabeçalho do relatório
  function Header()
  {    
  	//Recupera o nome da empresa
  	$empresaNome = 'Consoli Eventos Ltda';
    $edtPedidoId = $_GET["PedidoId"];
    
    //Ajusta a fonte	
  	$this->Ln(14);	    
  	$this->SetFont('Arial','B',15);
    $this->Cell(0,6,$empresaNome,0,0,'R');
    $this->SetFont('Arial','',9);
    
    //Imprime o logotipo da empresa
  	$this->Image('../image/logo_consoli2.jpg',10,10,40);
  	
    //Line break
    $this->Ln(14);
  }
  
  //Rodapé do Relatório
  function Footer()
  {
     $usuarioNome = $_GET["UsuarioNome"];
     //Position at 1.5 cm from bottom
     $this->SetY(-15);    
     //Arial italic 8
     $this->SetFont('Arial','I',8);
     //Page number
     $this->Line(10,281,200,281);
     $this->Cell(0,3,'Rua dos Caçadores, 102 - Bairro Laranjeiras - Rio do Sul - SC - CEP: 89160-000',0,0,'C');
     $this->Ln();
     $this->Cell(0,3,'Fone: (47) 3522-1336 / 3521-4024 - consoli@consolieventos.com.br',0,0,'C');     

  }
  
  */
}

//Instancia a classe gerador de pdf
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Emissão do Pedido');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

$pdf->AddPage();

//Recupera o nome da empresa
$empresaNome = 'Consoli Eventos Ltda';
$edtPedidoId = $_GET["PedidoId"];

//Imprime o logotipo da empresa
$pdf->Image('../image/logo_consoli2.jpg',10,5,40);

//Ajusta a fonte	   
$pdf->SetFont('Arial','B',15);
$pdf->Cell(0,6,$empresaNome,0,0,'R');
$pdf->SetFont('Arial','',9);
$pdf->Ln();	 
$pdf->Cell(0,4,'Rua São Bento, 289 - Bairro Progresso - Rio do Sul - SC - CEP: 89160-000',0,0,'R');
$pdf->Ln();
$pdf->Cell(0,4,'Fone: (47) 3522-1336 / 3521-4024 - consoli@consolieventos.com.br',0,0,'R');
$pdf->Ln(14);	
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(90,7, 'Emissão do Pedido',1,0,'C',1);
$pdf->Cell(40,7, 'Nro: ' . $dados_pedido["id"],1,0,'C');
$pdf->Cell(0,7, 'Emissão: ' . DataMySQLRetornar($dados_pedido["data"]),1,0,'C');
$pdf->ln(8);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0,6, 'Dados do Formando',1,0,'C',1);
$pdf->ln();

$pdf->SetFont('Arial', '', 10);
$pdf->Multicell(0,5, '[' . $dados_pedido["formando_id"]  . '] - ' . $dados_pedido["formando_nome"] . "\n" . $dados_pedido["evento_nome"] . "\n" . DataMySQLRetornar($dados_pedido["evento_data"]) ,1);


$pdf->ln(2);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0,6, 'Dados do Fornecedor',1,0,'C',1);
$pdf->ln();

$pdf->SetFont('Arial', '', 10);
$pdf->Multicell(0,5, $dados_pedido["fornecedor_nome"] . "\n" . $dados_pedido["fornecedor_endereco"] . " - " . $dados_pedido["fornecedor_complemento"] . "\n" . $dados_pedido["fornecedor_bairro"] . " - " . $dados_pedido["fornecedor_cep"] . " - " . $dados_pedido["cidade_fornecedor_nome"] . "/" . $dados_pedido["fornecedor_uf"] . "\n" . $dados_pedido["fornecedor_telefone"] . " - " . $dados_pedido["fornecedor_email"] . " - " . $dados_pedido["fornecedor_contato"],1);

$pdf->ln(3);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0,6, 'Descrição dos Produtos',1,0,'C',1);
$pdf->ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20,6, 'Quant.',1,0,'C',1);
$pdf->Cell(0,6, 'Descrição/Observações',1,0,'C',1);
$pdf->ln();

//Busca os dados dos produtos                                           
$sql_produto = mysql_query("SELECT 
                          prod.id,
                          prod.quantidade,
                          prod.observacoes,
                          produto.nome as produto_nome                          
                          FROM pedido_fv_produtos prod
                          LEFT OUTER JOIN categoria_fotovideo produto ON produto.id = prod.produto_id                          
                          WHERE prod.pedido_id = $edtPedidoId
                          ORDER BY produto_nome
                          ");

//Percorre o array
while ($dados_produto = mysql_fetch_array($sql_produto))
{
  
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(20,6, $dados_produto[quantidade],1,0,'C');
  $pdf->Cell(0,6, $dados_produto[produto_nome],1,0,'L');
  
  //Verifica se há alguma observação para o produto
  if ($dados_produto[observacoes] != "")
  {
    
    //Imprime as observações
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->ln();
    $pdf->Multicell(0,5, $dados_produto[observacoes],1); 
    $pdf->ln(2);
    
  }
  
  else
  
  {
    
    $pdf->ln(8);
    
  }

}

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60,6, 'Data para Entrega:',1,0,'C',1);
$pdf->SetFont('Arial', '', 10);

if ($dados_pedido["data_entrega"] != "0000-00-00")
{
  
  $pdf->Cell(0,6, DataMySQLRetornar($dados_pedido["data_entrega"]),1,0,'L');
}

else

{
  
  $pdf->Cell(0,6, " ",1,0,'L');
  
}

$pdf->ln(8);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0,6, 'Observações do Pedido',1,0,'C',1);
$pdf->ln();
$pdf->SetFont('Arial', 'I', 9);
$pdf->Multicell(0,5, $dados_pedido[observacoes],1);

//Gera o PDF
$pdf->Output();
?>