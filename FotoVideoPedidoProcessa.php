<?php 
###########
## Módulo para processamento do pedido do foto e video
## Criado: 18/07/2013 - Maycon Edinger
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
$edtEventoId = $_POST['edtEventoId'];
$edtFormandoId = $_POST['edtFormandoId'];
$edtPedidoObs = $_POST['edtPedidoObs'];
$edtPosicao = $_POST['edtPosicao'];
$edtRateio = $_POST['edtRateio'];
$edtDataVenda = DataMySQLInserir($_POST['edtDataVenda']);
$edtDataPrazoEnvio = DataMySQLInserir($_POST['edtDataPrazoEnvio']);

/*
echo 'Eve: ' . $edtEventoId . '<br/>';
echo 'Form: ' . $edtFormandoId . '<br/>';
echo 'Posicao: ' . $edtPosicao . '<br/>';
echo 'Rateio: ' . $edtRateio . '<br/>';
*/

//Insere o pedido
$sql_pedidos = mysql_query("INSERT INTO fotovideo_pedido (
                            data,
                            hora,
                            data_venda,
                            data_prazo_envio,
                            formando_id,
                            observacoes,
                            usuario_cadastro_id,
                            cadastro_timestamp
                            ) VALUES (
                            now(),
                            now(),
                            '$edtDataVenda',
                            'edtDataPrazoEnvio',
                            '$edtFormandoId',
                            '$edtPedidoObs',
                            '$usuarioId',
                            now()
                            )");

$numero_pedido = mysql_insert_id();

//Atualiza os dados de compra do formando
$sql_atualiza_compra = mysql_query("UPDATE eventos_formando SET data_venda = '$edtDataVenda', data_entrega_cliente = '$edtDataPrazoEnvio' WHERE id = '$edtFormandoId'");

//Caso tenha vendedores alocados
if ($edtPosicao > 0)
{
  
  //Processa as comissoes
  for ($i=1; $i <= $edtPosicao; $i++)
  {

    $edtVendedor = $_POST["VEN_$i"];
    $edtComissao = $_POST["edtComissao_$i"];
    
    if ($edtVendedor > 0)
    {
      
      //Insere a comissao
      $sql_comissao = mysql_query("INSERT INTO fotovideo_pedido_comissoes 
                                  (pedido_id, vendedor_id, comissao, rateio) 
                                  VALUES 
                                  ('$numero_pedido','$edtVendedor', '$edtComissao','$edtRateio')");
      
    }

  }
  
}

?>
<script>
  
  parent.document.getElementById('botao_produto').style.display = '';
  parent.document.getElementById('aviso_produto').style.display = 'none';
  parent.document.getElementById('imprime_pedido').style.display = '';
  
  parent.document.getElementById('numero_pedido').innerHTML = '<?php echo $numero_pedido ?>';
  
  parent.document.getElementById('PedidoId').value = '<?php echo $numero_pedido ?>';
  
  alert('Pedido Número <?php echo $numero_pedido ?> Criado ! -> Posicoes de comissao: <?php echo $edtPosicao ?>');
  
</script>