<?php 
###########
## Módulo para Listagem dos Formandos do evento para foto e video
## Criado: 01/07/2013 - Maycon Edinger
## Alterado: 
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

$EventoId = $_GET[EventoId];

//verifica os formandos já cadastrados para este evento e exibe na tela
$sql_consulta = mysql_query("SELECT
                            form.id,
                            form.senha,
                            form.cpf,
                            form.status,
                            form.situacao,
                            form.nome,
                            form.curso_id,
                            form.email,
                            form.contato,
                            form.operadora,
                            form.observacoes,
                            form.chk_culto,
                            form.chk_colacao,
                            form.chk_jantar,
                            form.chk_baile,
                            form.status_fotovideo,
                            curso.nome AS curso_nome,
                            curso.id AS curso_id 
                            FROM eventos_formando form
                            LEFT OUTER JOIN cursos curso ON curso.id = form.curso_id
                            WHERE form.evento_id = $EventoId
                            ORDER BY form.nome");

$registros = mysql_num_rows($sql_consulta);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<form id="form" name="cadastro" action="sistema.php?ModuloNome=EventoCadastra" method="post" onsubmit="return valida_form();">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>  
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440"><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Formandos Cadastrados para o Evento:</span></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
       <table width="100%" cellpadding="0" cellspacing="0" border="0">
         <?php
			
            //Caso não houverem registros
            if ($registros == 0)
            { 

              //Exibe uma linha dizendo que nao registros
              echo "
              <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0' class='listView'>
              <tr height='24'>
                <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
                  <font color='#33485C'><strong>Não há formandos cadastrados para este evento</strong></font>
                </td>
              </tr>";	  

            }

            else 

            {
              
              $total_formandos = $registros;
              $atendido = 0;
              $nao_atendido = 0;
               
               //Cria o array e o percorre para montar a listagem dinamicamente
               while ($dados_consulta = mysql_fetch_array($sql_consulta))
               {

                  //Efetua o switch para o campo de status
                  switch ($dados_consulta[status]) 
                  {
                     case 1: $desc_status = "<img src='image/bt_a_formar.png' alt='A se formar'>"; break;
                     case 2: $desc_status = "<img src='image/bt_formado.png' alt='Formado'>"; break;
                     case 3: $desc_status = "<img src='image/bt_desistente.png' alt='Desistente'>"; break;
                     case 4: $desc_status = "<img src='image/bt_pendencia.gif' alt='Aguardando Declaração de Rescisão'>"; break;
                  }   


                  $desc_participante = "&nbsp;";

                  if ($dados_consulta["chk_culto"] == 1)
                  {

                    $desc_participante .= "<span title='Formando Participa do Culto'>M</span>&nbsp;";

                  }

                  if ($dados_consulta["chk_colacao"] == 1)
                  {

                    $desc_participante .= "<span title='Formando Participa da Colação'>C</span>&nbsp;";

                  }

                  if ($dados_consulta["chk_jantar"] == 1)
                  {

                     $desc_participante .= "<span title='Formando Participa do Jantar'>J</span>&nbsp;";

                  }

                  if ($dados_consulta["chk_baile"] == 1)
                  {

                     $desc_participante .= "<span title='Formando Participa do Baile'>B</span>";

                  }

                  $FormandoId = $dados_consulta['id'];

                  //Busca os pedidos que o formando já efetuou
                  $sql_pedidos = mysql_query("SELECT
                                              ped.id,
                                              ped.data,
                                              ped.hora,
                                              CONCAT(usu.nome, ' ', usu.sobrenome) AS usuario_nome
                                              FROM fotovideo_pedido ped
                                              LEFT OUTER JOIN usuarios usu ON usu.usuario_id = ped.usuario_cadastro_id
                                              WHERE ped.formando_id = $FormandoId");

                  $registros_pedidos = mysql_num_rows($sql_pedidos);
                  
                  $total_pedidos = $total_pedidos + $registros_pedidos;

                  ?>  
                  <tr>
                    <td>
                      <br/>
                      <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">
                        <tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">
                           <td width="22">&nbsp;&nbsp;S</td>
                           <td width="50" align="center">Part.</td>
                           <td style="padding-left: 5px">Formando</td>
                           <td width="200">Curso</td>
                        </tr>
                        <tr valign="middle" height="26">
                          <td bgcolor="<?php echo $cor_celula ?>" align="center"><?php echo $desc_status ?></td>
                          <td bgcolor="<?php echo $cor_celula ?>" align="center"><span style="color: #6666CC;"><b><?php echo $desc_participante ?></b></span></td>
                          <td bgcolor="<?php echo $cor_celula ?>" valign="middle" bgcolor="#fdfdfd" style="padding-left: 5px;">
                              <?php echo '(' . $dados_consulta['id'] . ') - ' ?><font color="#CC3300" size="2" face="Tahoma"><a href='#'><?php echo $dados_consulta["nome"]; ?></a></font>     
                            </td>
                            <td bgcolor="<?php echo $cor_celula ?>">
                              <?php

                                //Verifica se existe um curso cadastrado
                                if ($dados_consulta["curso_id"] > 0)
                                {

                                    echo "<span style='color: #990000'>$dados_consulta[curso_nome]</span><br/>";

                                }

                              ?>
                            </td>
                          </tr>
                          <?php
                           
                            if ($registros_pedidos > 0)
                            {
                              
                            ?>
                            <tr>
                              <td colspan="4" style="padding-left: 10px;">
                                <b>Pedidos: <?php echo $registros_pedidos ?></b> 
                              </td>
                            </tr>
                            <?php
                              
                              //Cria o array e o percorre para montar a listagem dinamicamente
                              while ($dados_pedidos = mysql_fetch_array($sql_pedidos))
                              {
                              ?>
                              <tr>
                                <td colspan="3" style="border-bottom: 1px #aaa dashed; padding-top: 10px; padding-left: 10px; padding-bottom: 4px">
                                  <span style="font-size: 12px"><a href='#' onclick="wdCarregarFormulario('FotoVideoPedidoExibe.php?PedidoId=<?php echo $dados_pedidos['id'] ?>','conteudo');"><?php echo DataMySQLRetornar($dados_pedidos['data']) . ' - ' . substr($dados_pedidos['hora'],0,5) ?></a></span>
                                </td>
                                <td style="border-bottom: 1px #aaa dashed;">
                                  Pedido ID: <?php echo $dados_pedidos['id'] ?>
                                </td>
                              </tr>
                              <?php
                              
                              }
                              
                            }
                          
                          ?>
                          <tr>
                            <td colspan="4" style="padding-top: 10px; padding-left: 10px; padding-bottom: 4px">
                                <input class="button" value="Incluir Novo Pedido" name="btnNovoPedido" type="button" id="btnNovoPedido" onclick="wdCarregarFormulario('FotoVideoPedidoCadastra.php?FormandoId=<?php echo $FormandoId ?>&EventoId=<?php echo $EventoId ?>','conteudo');" />
                            </td>
                          </tr>
                        </table>
                     </td>
                  </tr> 
                  <?php    

               //Fecha o While
               }
            
            echo '</table>';
          
         //Fecha o if de se tem registros
         }
      
      ?>
    </td>
  </tr>
</table>
<br/>
<br/>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
  <tr>
    <td height="26" style="padding-top: 4px; padding-bottom: 4px">
      <span style="font-size: 12px;">
        <b>&nbsp;&nbsp;Estatísticas:<br/><br/>&nbsp;&nbsp;Status:</b><br/>
        <?php        

           echo "&nbsp;&nbsp;Total de formandos no evento: <b><span style='color: #990000'>" . $total_formandos . "</span></b><br/>";
           echo "&nbsp;&nbsp;Total de pedidos no evento: <b><span style='color: #990000'>" . $total_pedidos  . "</span></b><br/>";
           echo "&nbsp;&nbsp;Total de formandos atendidos: <b><span style='color: #990000'>" . $atendido . "</span></b><br/>";
           echo "&nbsp;&nbsp;Total de formandos não atendidos: <b><span style='color: #990000'>" . $nao_atendido . "</span></b><br/>";
           
        ?>
      </span>
    </td>
  </tr>
</table>
</form>
