<head>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body style="margin: 0px">
<?php
###########
## Módulo para processamento dos produtos do pedido do foto e video
## Criado: 21/07/2013 - Maycon Edinger
## Alterado: 
## 
###########
//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1', true);

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
$edtPedidoId = $_POST['PedidoId'];
$edtProdutoId = $_POST['cmbProdutoId'];
$edtQuantidade = $_POST['edtQuantidade'];
$edtBrinde = $_POST['chkBrinde'];
if ($edtBrinde == 'on') $edtBrinde = 1;

$edtValorUnitario = MoneyMySQLInserir($_POST['edtValorUnitario']);
$edtObs = $_POST['edtObs'];

/*
echo 'Pedido: ' . $edtPedidoId . '<br/>';
echo 'Produto: ' . $edtProdutoId . '<br/>';
echo 'Quantidade: ' . $edtQuantidade . '<br/>';
echo 'Brinde: ' . $edtBrinde . '<br/>';
echo 'Valor: ' . $edtValorUnitario . '<br/>';
echo 'Obs: ' . $edtObs . '<br/>';

*/

//Insere o item
$sql_produtos = mysql_query("INSERT INTO fotovideo_pedido_produto (
                            pedido_id,
                            produto_id,
                            quantidade_venda,
                            chk_brinde,
                            valor_unitario,
                            obs_cadastro,
                            data_produto,
                            usuario_produto
                            ) VALUES (
                            '$edtPedidoId',
                            '$edtProdutoId',
                            '$edtQuantidade',
                            '$edtBrinde',
                            '$edtValorUnitario',
                            '$edtObs',
                            now(),
                            '$usuarioId'
                            )");
 
  
    //Monta a tabela de consulta dos produtos cadastrados
    //Cria a SQL
    $consulta = "SELECT
                prod.id,
                prod.pedido_id,
                prod.produto_id,
                prod.quantidade_venda,
                prod.chk_brinde,
                prod.valor_unitario,
                prod.obs_cadastro,
                produto.nome AS produto_nome
                FROM fotovideo_pedido_produto prod
                LEFT OUTER JOIN categoria_fotovideo produto ON produto.id = prod.produto_id
                WHERE prod.pedido_id = $edtPedidoId 
                ORDER BY produto_nome";
    
    //Executa a query
    $listagem = mysql_query($consulta);
    
    $registros_produtos = mysql_num_rows($listagem);
    
    echo 'Regs: ' . $registros_produtos;
    
    if ($registros_produtos > 0)
    {
      
      ?>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="margin-top: 14px">
        <tr>
          <td colspan="15" align="right">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td nowrap align="left"  class="listViewPaginationTdS1">
                  <span class="pageNumbers">Produtos do Pedido: <b><?php echo $registros_produtos ?></b></span>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr height="20">
          <td width="42" class="listViewThS1">
            <div align="center">A&ccedil;&atilde;o</div>
          </td>
          <td width="575" class="listViewThS1">
            &nbsp;&nbsp;Descrição do Produto
          </td>
          <td nowrap="nowrap" class="listViewThS1">
            <div align="center">Quantidade</div>
          </td>
          <td width="24" class="listViewThS1">
            <div align="center">Br</div>
          </td>
          <td width="80" class="listViewThS1" align="right">
            Unitário
          </td>
          <td width="80" class="listViewThS1" align="right" style="padding-right: 8px;">
            Total
          </td>
        </tr>
        <?php

        //Monta e percorre o array com os dados da consulta
        while ($dados = mysql_fetch_array($listagem))
        {

          //Efetua o switch para exibir a imagem para quando o cadastro estiver ativo
          switch ($dados['chk_brinde'])
          {
            case 0: $brinde_figura = ""; break;
            case 1: $brinde_figura = "<img src='./image/grid_ativo.gif' alt='brinde' />"; break;
          }
          
          $total_item = $dados['quantidade_venda'] * $dados['valor_unitario'];

          ?>
          <tr height="16">
            <td width="42">
              <div align="center">
                jjj         
              </div>
            </td>
            <td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" class="oddListRowS1">
              <a title="Clique para editar este registro" href="#"><?php echo $dados['produto_nome'] ?></a>
            </td>
            <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
              <div align="center"><?php echo $dados['quantidade_venda'] ?></div>
            </td>
            <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
              <div align="center"><?php echo $brinde_figura ?></div>
            </td>
            <td valign="middle" bgcolor="#fdfdfd" class="currentTabList" align="right">
              <?php echo number_format($dados['valor_unitario'],2,',','.') ?>
            </td>
            <td valign="middle" bgcolor="#fdfdfd" class="currentTabList" align="right" style="padding-right: 8px;">
              <?php echo number_format($total_item,2,',','.') ?>
            </td>
          </tr>
          <?php

        //Fecha o while
        }
        
      ?>
      </table>  
      <?php  

      //Fecha o if de se achou registros  
      }

    ?>
<script>

          ///parent.document.getElementById('botao_produto').style.display = '';
          //parent.document.getElementById('aviso_produto').style.display = 'none';

</script>
</body>