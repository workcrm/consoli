<?php
###########
## Mѓdulo de relatѓrio geral de detalhamento do pedido do FV
## Criado: 09/08/2013 - Maycon Edinger
## Alterado: 
## Alteraчѕes: 
###########

require('../fpdf/fpdf.php');

include '../conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulaчуo de datas
include '../include/ManipulaDatas.php';

//Recupera os valores para filtragem
$edtPedidoId = $_GET["PedidoId"];

//Busca os dados do pedido                                              
$sql_pedido = mysql_query("SELECT 
                          ped.id, 
                          ped.data, 
                          ped.hora, 
                          ped.data_venda,
                          ped.formando_id, 
                          ped.usuario_cadastro_id, 
                          ped.observacoes,
                          eve.id AS evento_id, 
                          eve.nome AS evento_nome, 
                          formando.nome AS formando_nome, 
                          CONCAT(usu.nome, ' ', usu.sobrenome) AS usuario_nome 
                          FROM fotovideo_pedido ped 
                          LEFT OUTER JOIN eventos_formando formando ON formando.id = ped.formando_id 
                          LEFT OUTER JOIN eventos eve ON eve.id = formando.evento_id 
                          LEFT OUTER JOIN usuarios usu ON usu.usuario_id = ped.usuario_cadastro_id
                          WHERE ped.id = $edtPedidoId");

//Monta o array com os dados
$dados_pedido = mysql_fetch_array($sql_pedido);

class PDF extends FPDF
{}

//Instancia a classe gerador de pdf
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Emissуo do Pedido');
$pdf->SetSubject('Relatѓrio gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

$pdf->AddPage();

//Recupera o nome da empresa
$empresaNome = 'Consoli Eventos Ltda';
$edtPedidoId = $_GET["PedidoId"];

//Imprime o logotipo da empresa
$pdf->Image('../image/logo_consoli2.jpg',10,10,34);

//Ajusta a fonte	   
$pdf->SetFont('Arial','B',15);
$pdf->Cell(0,6,$empresaNome,0,0,'R');
$pdf->SetFont('Arial','',9);
$pdf->Ln();	 
$pdf->Cell(0,4,'Rua Sуo Bento, 289 - Bairro Progresso - Rio do Sul - SC - CEP: 89160-000',0,0,'R');
$pdf->Ln();
$pdf->Cell(0,4,'Fone: (47) 3520-1650 - consoli@consolieventos.com.br',0,0,'R');
$pdf->Ln(14);	
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,7, 'Pedido de Foto e Vэdeo Nro ' . $dados_pedido["id"],0,0);
$pdf->ln();
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0,5, 'Emissуo: ' . DataMySQLRetornar($dados_pedido["data"]) . ' Por: ' . $dados_pedido["usuario_nome"],0,0);
$pdf->ln();
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0,5, 'Data da Venda: ' . DataMySQLRetornar($dados_pedido["data_venda"]),0,0);
$pdf->ln(8);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0,6, 'Formando:',1,0,'L',1);
$pdf->ln();

$pdf->SetFont('Arial', '', 10);
$pdf->Multicell(0,5, '[' . $dados_pedido["formando_id"]  . '] - ' . $dados_pedido["formando_nome"] . "\n[" . $dados_pedido["evento_id"] . '] - ' . $dados_pedido["evento_nome"] ,1);

$pdf->ln(3);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0,6, 'Observaчѕes do Pedido:',1,0,'L',1);
$pdf->ln();
$pdf->SetFont('Arial', 'I', 9);
$pdf->Multicell(0,5, $dados_pedido["observacoes"],1);

$pdf->ln(3);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0,6, 'Descriчуo dos Produtos:',1,0,'L',1);
$pdf->ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(118,6, 'Descriчуo/Observaчѕes',1,0,'C',1);
$pdf->Cell(16,6, 'Quant.',1,0,'C',1);
$pdf->Cell(28,6, 'Unitсrio',1,0,'R',1);
$pdf->Cell(28,6, 'Total',1,0,'R',1);
$pdf->ln();

//Busca os dados dos produtos                                           
$sql_produto = mysql_query("SELECT 
                            prod.id,
                            prod.pedido_id,
                            prod.produto_id,
                            prod.quantidade_venda,
                            prod.chk_brinde,
                            prod.valor_unitario,
                            prod.obs_cadastro,
                            produto.nome as produto_nome                          
                            FROM fotovideo_pedido_produto prod
                            LEFT OUTER JOIN categoria_fotovideo produto ON produto.id = prod.produto_id                          
                            WHERE prod.pedido_id = $edtPedidoId
                            ORDER BY produto_nome
                            ");

//Conta os produtos do pedido
$registros_produto = mysql_num_rows($sql_produto);

//Caso tenha registros
if ($registros_produto > 0)
{
  
  $pos = 1;
  
  //Percorre o array
  while ($dados_produto = mysql_fetch_array($sql_produto))
  {
    
    if ($pos > 1) $pdf->ln(10);
    $pos++;
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(118,6, $dados_produto['produto_nome'],1,0,'L');
    $pdf->Cell(16,6, $dados_produto['quantidade_venda'],1,0,'C');
    
    if ($dados_produto['chk_brinde'] == 1)
    {
      
      $pdf->Cell(28,6, 'Brinde',1,0,'R');
      $pdf->Cell(28,6, 'Brinde',1,0,'R');
      
    }
    
    else
      
    {
      
      $pdf->Cell(28,6, number_format($dados_produto['valor_unitario'],2,',','.'),1,0,'R');
      
      $total_produto = $dados_produto['quantidade_venda'] * $dados_produto['valor_unitario'];
      $total_geral = $total_geral + $total_produto;

      $pdf->Cell(28,6, number_format($total_produto,2,',','.'),1,1,'R');
      
    }

    //Verifica se hс alguma observaчуo para o produto
    if ($dados_produto['obs_cadastro'] != "")
    {

      //Imprime as observaчѕes
      $pdf->SetFont('Arial', 'I', 8);
      $pdf->Multicell(0,5, $dados_produto['obs_cadastro'],1);

    }

  }
  
  $pdf->ln();
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(162,6, 'TOTAL: ',1,0,'R',1);
  $pdf->Cell(28,6, number_format($total_geral,2,',','.'),1,0,'R',1);
  
}

//Caso nуo tenha produtos
else
{
  
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(0,6, 'Nуo hс produtos associados a este pedido !',1,0,'L');
  
}

//Gera o PDF
$pdf->Output();

?>