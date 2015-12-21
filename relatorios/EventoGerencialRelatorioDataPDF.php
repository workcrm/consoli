<?php
###########
## Módulo de relatório de eventos por Gerencial para Joni - ANALITICO	
## Criado: 21/05/2013 - Maycon Edinger
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

//Recupera os valores para filtragem
$empresaId = $_GET['EmpresaId'];
$empresaNome = $_GET['EmpresaNome'];
$usuarioNome = $_GET['UsuarioNome'];
$EventoId = $_GET['EventoId'];

if ($EventoId > 0)
{

  $filtra_evento_entrada = "AND rec.evento_id = $EventoId";
  $filtra_evento_saida = "AND pag.evento_id = $EventoId";

  //Busca o total de formandos do evento
  $sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = $EventoId");

  //Percorre os registros
  while ($dados_evento = mysql_fetch_array($sql_evento))
  {

    $NomeEvento = $dados_evento["nome"];

  }

}

//Busca os dados dos formandos

//Busca o total de formandos do evento
$sql_formando = mysql_query("select COUNT(1) AS quantidade_formando FROM eventos_formando WHERE evento_id = $EventoId AND status < 3");

//Percorre os registros
while ($dados_formando = mysql_fetch_array($sql_formando))
{

  $TotalFormandos = $dados_formando["quantidade_formando"];

}

class PDF extends FPDF
{

  //Cabeçalho do relatório
  function Header()
  {

    global $EventoId, $NomeEvento;

    $empresaNome = $_GET['EmpresaNome'];
    //Ajusta a fonte
    $this->SetFont('Arial','',9);
    //Titulo do relatório
    $this->Cell(0,4, $empresaNome);
    $this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
    $this->Ln();
    $this->SetFont('Arial','B',15);
    $this->Cell(0,6,'Análise Gerencial por Evento');

    if ($EventoId > 0)
    {
		
      $this->Ln();
      $this->SetFont('Arial','',11);
      $this->Cell(0,5,'Evento: (' . $EventoId . ') - ' . $NomeEvento);
      $this->SetFont('Arial','',9);

    }

    //Line break
    $this->Ln(6);

    //Títulos das colunas
    $this->SetFont('Arial', 'B', 10);
    //Define a cor RGB do fundo da celula
    $this->SetFillColor(178,178,178);

    //Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
    $this->ln(2);
    $this->Cell(0,6, ' ',1,0,'',1);
    $this->SetX(14);
    $this->Cell(150,6, 'Centro de Custo/Conta-Caixa','TB',0,'L',1);
    $this->Cell(0,6, 'Valor R$ ','TBR',0,'R',1);

    $this->Ln(6);
    
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
$pdf->SetTitle('Analise Gerencial por Evento');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('P');

//Busca o total de formandos do evento
$sql_entradas = mysql_query("SELECT 
                            SUM(ent.total_recebido) AS total,
                            rec.grupo_conta_id,
                            rec.subgrupo_conta_id,
                            ccu.nome AS centro_custo_nome,
                            ccx.nome AS conta_caixa_nome
                            FROM contas_receber_recebimento ent
                            LEFT OUTER JOIN contas_receber rec ON rec.id = ent.conta_receber_id
                            LEFT OUTER JOIN grupo_conta ccu ON ccu.id = rec.grupo_conta_id
                            LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
                            LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
                            WHERE 1=1
                            $filtra_evento_entrada
                            GROUP BY ccu.nome, ccx.nome
                            ORDER BY ccu.nome, ccx.nome");

$registros_entradas = mysql_num_rows($sql_entradas);

$ccu_nome = 'a';

$pdf->ln(2);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0,5, "ENTRADAS",0,0,'L');
$pdf->SetFont('Arial', '', 9);

//Percorre os registros
while ($dados_entradas = mysql_fetch_array($sql_entradas))
{

  //Faz o switch pra totalizar
  switch ($dados_entradas["grupo_conta_id"])
  {
    
    //Se o centro de custo for foto e video
    case 6: $total_ccu_6 = $total_ccu_6 + $dados_entradas["total"]; break;
    
    //Se o centro de custo for formatura
    case 7: $total_ccu_7 = $total_ccu_7 + $dados_entradas["total"]; break;
    
  }
	
  //Verifica se ainda é a mesmo centro de custo
  if ($dados_entradas["centro_custo_nome"] != $ccu_nome)
  {

    $pdf->ln();	
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetX(14);
      
    //Verifica se o nome está vazio
    if ($dados_entradas["centro_custo_nome"] == '')
    {

      $pdf->Cell(0,5, "Centro de Custo: Não Definido",0,0,'L');

    }

    else
    {

      $pdf->Cell(0,5, "Centro de Custo: " . $dados_entradas["centro_custo_nome"],0,0,'L');

    }

    $pdf->SetFont('Arial', '', 9);
    
  }

  $pdf->ln();
  $pdf->SetX(18);
			
  //Verifica se o nome está vazio
  if ($dados_entradas["conta_caixa_nome"] == '')
  {

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(60,5, "Não Definido","B",0,'L');
    $pdf->SetFont('Arial', '', 9);

  }

  else
  {

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(60,5, $dados_entradas["conta_caixa_nome"],"B",0,'L');
    $pdf->SetFont('Arial', '', 9);

  }

  $pdf->SetFont('Arial', 'B', 9);
  $pdf->Cell(0,5, "Total: " . number_format($dados_entradas["total"],2,",","."),"B",0,'R');
  $pdf->SetFont('Arial', '', 9);

  $ccu_nome = $dados_entradas["centro_custo_nome"];
	
  $total_entradas = $total_entradas + $dados_entradas["total"];

  $centro_custo_id = $dados_entradas["grupo_conta_id"];
  $conta_caixa_id = $dados_entradas["subgrupo_conta_id"];

  //Verifica os lancamentos especificos dentro do centro de custo e conta-caixa listado
  //Busca as entradas do periodo
  $sql_entradas_detalhe = mysql_query("SELECT 
                                      ent.total_recebido AS total,
                                      ent.data_recebimento,
                                      rec.descricao,
                                      rec.grupo_conta_id,
                                      rec.subgrupo_conta_id,
                                      ccu.nome AS centro_custo_nome,
                                      ccx.nome AS conta_caixa_nome
                                      FROM contas_receber_recebimento ent
                                      LEFT OUTER JOIN contas_receber rec ON rec.id = ent.conta_receber_id
                                      LEFT OUTER JOIN grupo_conta ccu ON ccu.id = rec.grupo_conta_id
                                      LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = rec.subgrupo_conta_id
                                      LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
                                      WHERE 1=1
                                      $filtra_evento_entrada
                                      AND ccu.id = $centro_custo_id
                                      AND ccx.id = $conta_caixa_id
                                      ORDER BY ent.data_recebimento");


  //Percorre os registros
  while ($dados_entradas_detalhe = mysql_fetch_array($sql_entradas_detalhe))
  {

    $pdf->ln();
    $pdf->SetX(30);
    $pdf->Cell(0,5, DataMySQLRetornar($dados_entradas_detalhe["data_recebimento"]),"B",0,'L');
    $pdf->SetX(50);
    $pdf->Cell(0,5, $dados_entradas_detalhe["descricao"],"B",0,'L');
    $pdf->SetX(140);
    $pdf->Cell(0,5, number_format($dados_entradas_detalhe["total"],2,",","."),"B",0,'R');

  }
			
}

$pdf->ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0,7, "SAÍDAS",'T',0,'L');
$pdf->SetFont('Arial', '', 9);

//Busca as saidas do periodo
$sql_saidas = mysql_query("SELECT 
                          sum(sai.total_pago) AS total,
                          pag.grupo_conta_id,
                          pag.subgrupo_conta_id,
                          ccu.nome AS centro_custo_nome,
                          ccx.nome AS conta_caixa_nome
                          FROM contas_pagar_pagamento sai
                          LEFT OUTER JOIN contas_pagar pag ON pag.id = sai.conta_pagar_id
                          LEFT OUTER JOIN grupo_conta ccu ON ccu.id = pag.grupo_conta_id
                          LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
                          LEFT OUTER JOIN eventos eve ON eve.id = pag.evento_id
                          WHERE 1=1
                          $filtra_evento_saida
                          GROUP BY ccu.nome, ccx.nome
                          ORDER BY ccu.nome, ccx.nome");

$registros_saidas = mysql_num_rows($sql_saidas);

$ccu_nome = 'a';

//Percorre os registros
while ($dados_saidas = mysql_fetch_array($sql_saidas))
{
	
  //Verifica se ainda é a mesmo centro de custo
  if ($dados_saidas["centro_custo_nome"] != $ccu_nome)
  {

    $pdf->ln();	
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetX(14);

    //Verifica se o nome está vazio
    if ($dados_saidas["centro_custo_nome"] == '')
    {

      $pdf->Cell(0,5, "Centro de Custo: Não Definido",0,0,'L');

    }

    else
    {

      $pdf->Cell(0,5, "Centro de Custo: " . $dados_saidas["centro_custo_nome"],0,0,'L');

    }

    $pdf->SetFont('Arial', '', 9);

  }

  $pdf->ln(10);
  $pdf->SetX(18);

  //Verifica se o nome está vazio
  if ($dados_saidas["conta_caixa_nome"] == '')
  {
	
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(60,5, "Não Definido","B",0,'L');
    $pdf->SetFont('Arial', '', 9);

  }

  else
  {

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(60,5, $dados_saidas["conta_caixa_nome"],"B",0,'L');
    $pdf->SetFont('Arial', '', 9);

  }
	
  $pdf->SetFont('Arial', 'B', 9);
  $pdf->Cell(0,5, "Total: " . number_format($dados_saidas["total"],2,",","."),"B",0,'R');
  $pdf->SetFont('Arial', '', 9);

  $ccu_nome = $dados_saidas["centro_custo_nome"];

  $total_saidas = $total_saidas + $dados_saidas["total"];


  $centro_custo_id = $dados_saidas["grupo_conta_id"];
  $conta_caixa_id = $dados_saidas["subgrupo_conta_id"];

  //Verifica os lancamentos especificos dentro do centro de custo e conta-caixa listado
  //Busca as saidas do periodo
  $sql_saidas_detalhe = mysql_query("SELECT 
                                    sai.total_pago AS total,
                                    sai.data_pagamento,
                                    pag.descricao,
                                    pag.grupo_conta_id,
                                    pag.subgrupo_conta_id,
                                    ccu.nome AS centro_custo_nome,
                                    ccx.nome AS conta_caixa_nome
                                    FROM contas_pagar_pagamento sai
                                    LEFT OUTER JOIN contas_pagar pag ON pag.id = sai.conta_pagar_id
                                    LEFT OUTER JOIN grupo_conta ccu ON ccu.id = pag.grupo_conta_id
                                    LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = pag.subgrupo_conta_id
                                    LEFT OUTER JOIN eventos eve ON eve.id = pag.evento_id
                                    WHERE 1=1
                                    $filtra_evento_saida
                                    AND ccu.id = $centro_custo_id
                                    AND ccx.id = $conta_caixa_id
                                    ORDER BY sai.data_pagamento");

  //Percorre os registros
  while ($dados_saidas_detalhe = mysql_fetch_array($sql_saidas_detalhe))
  {

    $pdf->ln();
    $pdf->SetX(30);
    $pdf->Cell(0,5, DataMySQLRetornar($dados_saidas_detalhe["data_pagamento"]),"B",0,'L');
    $pdf->SetX(50);
    $pdf->Cell(0,5, $dados_saidas_detalhe["descricao"],"B",0,'L');
    $pdf->SetX(140);
    $pdf->Cell(0,5, number_format($dados_saidas_detalhe["total"],2,",","."),"B",0,'R');

  }
		
}

$pdf->SetFont('Arial', 'B', 9);

//Busca o total de entradas por tipo de recebimento
$sql_total_entradas = mysql_query("SELECT 
                                  sum(ent.total_recebido) AS total,
                                  ent.tipo_recebimento,
                                  rec.id
                                  FROM contas_receber_recebimento ent
                                  LEFT OUTER JOIN contas_receber rec ON rec.id = ent.conta_receber_id
                                  LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
                                  WHERE 1=1
                                  $filtra_evento_entrada
                                  GROUP BY ent.tipo_recebimento
                                  ORDER BY ent.tipo_recebimento");

$registros_total_entradas = mysql_num_rows($sql_total_entradas);

//Percorre os registros
while ($dados_total_entradas = mysql_fetch_array($sql_total_entradas))
{
			
  //Efetua o switch do tipo de recebimento
  switch ($dados_total_entradas['tipo_recebimento']) 
  {
    case 1: $nome_tipo = 'Recebimentos em Dinheiro'; break;
    case 2: $nome_tipo = 'Recebimentos em Cheque'; break;       	
    case 3: $nome_tipo = 'Recebimentos em Cheque de Terceiro'; break;
    case 4: $nome_tipo = 'Recebimentos em Boleto Bancário';	break;
  }

  $pdf->ln();
  $pdf->Cell(150,5, $nome_tipo,0,0,'R');
  $pdf->Cell(0,5, number_format($dados_total_entradas["total"],2,",","."),0,0,'R');

}

$pdf->ln();
$pdf->Cell(150,5, "TOTAL DE ENTRADAS:",0,0,'R');
$pdf->Cell(0,5, number_format($total_entradas,2,",","."),0,0,'R');

//Calcula os percentuais
@$percentual_ccu6 = ($total_ccu_6 / $total_entradas) * 100;
@$percentual_ccu7 = ($total_ccu_7 / $total_entradas) * 100;

$pdf->ln();
$pdf->Cell(150,5, "TOTAL CCU FORMATURA:",0,0,'R');
$pdf->Cell(0,5, number_format($total_ccu_7, 2,",",".") . ' (' . number_format(@$percentual_ccu7, 2,",",".") . '%)',0,0,'R');

$pdf->ln();
$pdf->Cell(150,5, "TOTAL CCU FOTO E VÍDEO:",0,0,'R');
$pdf->Cell(0,5, number_format($total_ccu_6, 2,",",".") . ' (' . number_format(@$percentual_ccu6, 2,",",".") . '%)',0,0,'R');

$pdf->ln();
$pdf->Cell(150,5, "TOTAL DE FORMANDOS ATIVOS:",0,0,'R');
$pdf->Cell(0,5, number_format($TotalFormandos,0,"","."),0,0,'R');

$media_entrada_formando = $total_entradas / $TotalFormandos;

$pdf->ln();
$pdf->Cell(150,5, "MÉDIA DE ENTRADAS POR FORMANDO ATIVO:",'B',0,'R');
$pdf->Cell(0,5, number_format($media_entrada_formando,2,",","."),'B',0,'R');

$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);

//Busca o total de saidas por tipo de pagamento
$sql_total_saidas = mysql_query("SELECT 
                                sum(ent.total_pago) AS total,
                                ent.tipo_pagamento,
                                pag.id
                                FROM contas_pagar_pagamento ent
                                LEFT OUTER JOIN contas_pagar pag ON pag.id = ent.conta_pagar_id
                                LEFT OUTER JOIN eventos eve ON eve.id = pag.evento_id
                                WHERE 1=1
                                $filtra_evento_saida
                                GROUP BY ent.tipo_pagamento
                                ORDER BY ent.tipo_pagamento");

$registros_total_saidas = mysql_num_rows($sql_total_saidas);

//Percorre os registros
while ($dados_total_saidas = mysql_fetch_array($sql_total_saidas))
{
	
  //Efetua o switch do tipo de pagamento
  switch ($dados_total_saidas['tipo_pagamento']) 
  {
    case 1: $nome_tipo = 'Pagamentos em Dinheiro'; break;
    case 2: $nome_tipo = 'Pagamentos em Cheque'; break;       	
    case 3: $nome_tipo = 'Pagamentos em Cheque de Terceiro'; break;
  }

  $pdf->ln();
  $pdf->Cell(150,5, $nome_tipo,0,0,'R');
  $pdf->Cell(0,5, number_format($dados_total_saidas["total"],2,",","."),0,0,'R');

}

$pdf->ln();
$pdf->Cell(150,5, "TOTAL DE SAIDAS:",0,0,'R');
$pdf->Cell(0,5, number_format($total_saidas,2,",","."),0,0,'R');	

$pdf->ln();
$pdf->Cell(150,5, "TOTAL DE FORMANDOS ATIVOS:",0,0,'R');
$pdf->Cell(0,5, number_format($TotalFormandos,0,"","."),0,0,'R');

$media_saida_formando = $total_saidas / $TotalFormandos;

$pdf->ln();
$pdf->Cell(150,5, "MÉDIA DE SAIDAS POR FORMANDO ATIVO:",'B',0,'R');
$pdf->Cell(0,5, number_format($media_saida_formando,2,",","."),'B',0,'R');

$pdf->ln();
$pdf->Cell(150,5, "SALDO:","T" ,0,'R');

$total_saldo = $total_entradas - $total_saidas;
@$total_saldo_perc = ($total_saldo / $total_entradas) * 100;

$pdf->Cell(0,5, number_format($total_saldo,2,",",".") . ' (' . number_format($total_saldo_perc,2,".",".") . '%)',"T",0,'R');

$pdf->ln();
$pdf->Cell(150,5, "SALDO POR FORMANDO:","B" ,0,'R');

$total_saldo_formando = $media_entrada_formando - $media_saida_formando;
@$total_saldo_formando_perc = ($total_saldo_formando / $media_entrada_formando) * 100;

$pdf->Cell(0,5, number_format($total_saldo_formando,2,",",".") . ' (' . number_format($total_saldo_formando_perc,2,".",".") . '%)',"B",0,'R');

//Gera o PDF
$pdf->Output();

?>