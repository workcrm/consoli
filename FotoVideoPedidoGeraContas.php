<?php 
###########
## Módulo para processamento das contas a pagar das comissoes
## Criado: 18/11/2013 - Maycon Edinger
## Alterado: 
## 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Inclui o arquivo para manipulação de valor monetário
include './include/ManipulaMoney.php';

//Variaveis
$PedidoId = $_GET['PedidoId'];

//echo 'Padido: ' . $PedidoId;

//Obtem o total do pedido para inicio dos cálculos
//verifica os produtos
$sql_consulta = mysql_query("SELECT 
                            prod.id,
                            prod.pedido_id,
                            prod.produto_id,
                            prod.quantidade_venda,
                            prod.valor_unitario                        
                            FROM fotovideo_pedido_produto prod                        
                            WHERE prod.pedido_id = $PedidoId
                            ");

//Cria o array e o percorre para montar a listagem dinamicamente
while ($dados_consulta = mysql_fetch_array($sql_consulta))
{
  
  $total_produto = $dados_consulta['quantidade_venda'] * $dados_consulta['valor_unitario'];
  $total_geral = $total_geral + $total_produto;
  
}

//verifica os vendedores
$sql_consulta = mysql_query("SELECT 
                            com.id,
                            com.pedido_id,
                            com.vendedor_id,
                            com.comissao,
                            com.rateio,
                            vend.pessoa_id,
                            vend.nome as vendedor_nome,
                            ped.formando_id,
                            eve.evento_id
                            FROM fotovideo_pedido_comissoes com
                            LEFT OUTER JOIN fotovideo_pedido ped ON ped.id = com.pedido_id
                            LEFT OUTER JOIN eventos_formando eve ON eve.id = ped.formando_id 
                            LEFT OUTER JOIN fotovideo_vendedores vend ON vend.id = com.vendedor_id                          
                            WHERE com.pedido_id = $PedidoId
                            ORDER BY vend.nome");

//Cria o array e o percorre para montar a listagem dinamicamente
while ($dados_consulta = mysql_fetch_array($sql_consulta))
{
  
  //echo '<br/><br/>Pessoa: ' . $dados_consulta['pessoa_id'];
  //echo '<br/>Nome: ' . $dados_consulta['vendedor_nome'];
  $edtComissaoId = $dados_consulta['id'];
  $edtPessoaId = $dados_consulta['pessoa_id'];
  $edtFormandoId = $dados_consulta['formando_id'];
  $edtEventoId = $dados_consulta['evento_id'];
  
  @$total_comissao = ($total_geral * ($dados_consulta['comissao'] / $dados_consulta['rateio'])) / 100;
  
  //Calcula o vencimento
  $dia_vencimento = date('d', mktime()); //Verifica o dia de hoje
  $mes_vencimento = date('m', mktime()); //Verifica o mes de hoje
  $ano_vencimento = date('Y', mktime()); //Verifica o ano de hoje
  
  if ($dia_vencimento > 1 && $dia_vencimento <= 3) $dia_vencimento = '3';
  if ($dia_vencimento > 3 && $dia_vencimento <= 13) $dia_vencimento = '13';
  if ($dia_vencimento > 13 && $dia_vencimento <= 23) $dia_vencimento = '23';
  
  if ($dia_vencimento > 23)
  {
    
    $dia_vencimento = '13';
    $mes_vencimento = $mes_vencimento++;
    
    //Caso seja dezembro
    if ($mes_vencimento == 12)
    {
      
      $mes_vencimento = 1;
      
      $ano_vencimento++;
      
    }
    
  }
  
  $data_vencimento = "$ano_vencimento-$mes_vencimento-$dia_vencimento";
  
  $insere_conta = "INSERT INTO contas_pagar (
                  empresa_id,
                  data,
                  tipo_pessoa,
                  regiao_id,
                  pessoa_id,
                  grupo_conta_id,
                  subgrupo_conta_id,
                  evento_id,
                  formando_id,
                  descricao,
                  origem_conta,
                  valor,
                  data_vencimento,
                  situacao,
                  cadastro_timestamp,
                  pedido_id
                  ) VALUES (
                  1,
                  now(),
                  2,
                  1,
                  '$edtPessoaId',
                  6,
                  23,
                  '$edtEventoId',
                  '$edtFormandoId',
                  'Pagamento Comissão - Geração Automática',
                  1,
                  '$total_comissao',
                  '$data_vencimento',
                  1,
                  now(),
                  $PedidoId
                  )";
  
  $roda_insere = mysql_query($insere_conta);
  
  $id_conta_pagar = mysql_insert_id();
  
  //Atualiza o id da conta a pagar no cadastro da comissào
  $atu_contas = mysql_query("UPDATE fotovideo_pedido_comissoes SET valor = '$total_comissao', conta_pagar_id = '$id_conta_pagar' WHERE id = '$edtComissaoId'");
  
  if ($roda_insere)
  {
    
    echo "<br/>Conta a pagar cadastrada com sucesso ! - " . $dados_consulta['vendedor_nome'];
    
  }
  
}

?>
<script>
  wdCarregarFormulario('ContaReceberCadastra.php?headers=1&Origem=FV&EventoFVId=<?php echo $edtEventoId ?>&FormandoFVId=<?php echo $edtFormandoId ?>','conteudo');
</script>