<?php 
###########
## Módulo para Exibição dos eventos
## Criado: 22/05/2007 - Maycon Edinger
## Alterado: 25/11/2007 - Maycon Edinger
## Alterações: 
## 05/06/2007 - Implementado rotina de segurança para usuario nivel 1 só visualize
##		Implementado para o relatório do evento possa exibir ou nao os participantes
## 14/07/2007 - Implementado exibição da função do colador no evento, telefone e celular
## 04/08/2007 - Implementado exibição dos itens do evento por categoria do item
## 22/08/2007 - Implementado a exibição do grupo do evento
## 10/10/2007 - Implementado a exibição do repertório do evento
## 25/11/2007 - Implementado a exibição dos serviços do evento
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET['headers'] == 1) 
{
  header('Content-Type: text/html;  charset=ISO-8859-1',true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');

//Converte uma data timestamp de mysql para normal
function TimestampMySQLRetornar($DATA)
{
  $ANO = 0000;
  $MES = 00;
  $DIA = 00;
  $HORA = '00:00:00';
  $data_array = split('[- ]',$DATA);
  if ($DATA <> '')
  {
    $ANO = $data_array[0];
    $MES = $data_array[1];
    $DIA = $data_array[2];
    $HORA = $data_array[3];
    return $DIA.'/'.$MES.'/'.$ANO. ' - ' . $HORA;
  }

  else 

  {
    $ANO = 0000;
    $MES = 00;
    $DIA = 00;
    return $DIA.'/'.$MES.'/'.$ANO;
  }

}

//Pega o valor da cliente a exibir
$EventoId = $_GET['EventoId'];

//Monitora o evento escolhido para o bactracking do usuário
$sql_backtracking = mysql_query("UPDATE usuarios SET evento_id = '$EventoId' WHERE usuario_id = '$usuarioId'");

echo "<script>wdCarregarFormulario('UltimoEvento.php','ultimo_evento',2)</script>";

//pesquisa as diretivas do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE usuario_id = $usuarioId";													  													  
							  
//Executa a query
$resultado_usuario = mysql_query($sql_usuario);

//Monta o array dos campos
$dados_usuario = mysql_fetch_array($resultado_usuario);

//Recupera dos dados do evento
$sql_evento  = "SELECT 
                eve.id,
                eve.nome,
                eve.descricao,
                eve.tipo,
                eve.status,
                eve.grupo_id,
                eve.cliente_id,
                eve.responsavel_orcamento,
                eve.responsavel,
                eve.contato1,
                eve.contato_obs1,
                eve.contato_fone1,
                eve.contato2,
                eve.contato_obs2,
                eve.contato_fone2,
                eve.contato3,
                eve.contato_obs3,
                eve.contato_fone3,
                eve.contato4,
                eve.contato_obs4,
                eve.contato_fone4,
                eve.contato5,
                eve.contato_obs5,
                eve.contato_fone5,
                eve.contato6,
                eve.contato_obs6,
                eve.contato_fone6,
                eve.contato7,
                eve.contato_obs7,
                eve.contato_fone7,
                eve.contato8,
                eve.contato_obs8,
                eve.contato_fone8,
                eve.contato9,
                eve.contato_obs9,
                eve.contato_fone9,
                eve.contato10,
                eve.contato_obs10,
                eve.contato_fone10,
                eve.contato11,
                eve.contato_obs11,
                eve.contato_fone11,
                eve.contato12,
                eve.contato_obs12,
                eve.contato_fone12,
                eve.data_realizacao,
                eve.hora_realizacao,
                eve.duracao,
                eve.numero_confirmado,
                eve.lugares_ocupados,
                eve.alunos_colacao,
                eve.alunos_baile,
                eve.participantes_baile,
                eve.observacoes,
                eve.observacoes_financeiro,
                eve.exibir_observacoes,
                eve.cadastro_timestamp,
                eve.cadastro_operador_id,
                eve.alteracao_timestamp,
                eve.alteracao_operador_id,
                eve.data_jantar,
                eve.hora_jantar,
                eve.data_certame,
                eve.hora_certame,
                eve.data_foto_convite,
                eve.hora_foto_convite,
                eve.local_foto_convite,
                eve.data_ensaio,
                eve.obs_ensaio,
                eve.data_culto,
                eve.obs_culto,
                eve.data_colacao,
                eve.obs_colacao,
                eve.data_baile,
                eve.obs_baile,
                eve.quebras,
                eve.valor_foto,
                eve.valor_dvd,
                eve.obs_fotovideo,
                eve.foto_video_liberado,
                eve.numero_nf,
                eve.roteiro,
                eve.posicao_financeira,
                eve.valor_colacao,
                eve.valor_baile,
                eve.valor_evento,
                eve.valor_geral_evento,
                eve.valor_desconto_evento,
                usu_cad.nome AS operador_cadastro_nome, 
                usu_cad.sobrenome AS operador_cadastro_sobrenome,
                usu_alt.nome AS operador_alteracao_nome, 
                usu_alt.sobrenome AS operador_alteracao_sobrenome,
                cli.id AS cliente_id,
                cli.nome AS cliente_nome,
                cli.endereco AS cliente_endereco,
                cli.complemento AS cliente_complemento,
                cli.bairro AS cliente_bairro,
                cli.cidade_id,
                cli.cep AS cliente_cep,
                cli.uf AS cliente_uf,
                cli.telefone AS cliente_telefone,
                cli.fax AS cliente_fax,
                cli.celular AS cliente_celular,
                cli.email AS cliente_email,
                cid.nome AS cliente_cidade,
                reg.nome AS regiao_nome
                FROM eventos eve 
                LEFT OUTER JOIN clientes cli ON cli.id = eve.cliente_id
                LEFT OUTER JOIN regioes reg ON reg.id = eve.regiao_id
                LEFT OUTER JOIN cidades cid ON cid.id = cli.cidade_id
                LEFT OUTER JOIN usuarios usu_cad ON eve.cadastro_operador_id = usu_cad.usuario_id 
                LEFT OUTER JOIN usuarios usu_alt ON eve.alteracao_operador_id = usu_alt.usuario_id							
                WHERE eve.id = $EventoId";
  
//Executa a query
$resultado = mysql_query($sql_evento);

//Monta o array dos campos
$dados_evento = mysql_fetch_array($resultado);

//Efetua o switch para o campo de tipo
switch ($dados_evento['tipo']) 
{
  case 1: $desc_tipo = 'Evento Social'; break;
  case 2: $desc_tipo = 'Formatura'; break;
  case 3: $desc_tipo = 'Pregão/Edital'; break;	
} 

//Efetua o switch para o campo de status
switch ($dados_evento['status']) 
{
  case 0: $desc_status = 'Em orçamento'; break;
  case 1: $desc_status = 'Em aberto'; break;
  case 2: $desc_status = 'Realizado'; break;
  case 3: $desc_status = "<span style='color: red'>Não-Realizado</span>"; break;	
}    

//Efetua o switch para o campo de posição financeira
switch ($dados_evento['posicao_financeira']) 
{
  case 1: $desc_financeiro = 'A Receber'; break;
  case 2: $desc_financeiro = 'Recebido'; break;
  case 3: $desc_financeiro = 'Cortesia'; break;	
} 

switch ($dados_evento['grupo_id']) 
{
  case 1: $grupo_status = 'Consoli Rio do Sul'; break;
  case 2: $grupo_status = 'Consoli Joinville'; break;
  case 3: $grupo_status = 'Gerri Adriani Consoli ME'; break;	
}

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440">
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Visualização do Evento</span>
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">
          </td>
        </tr>
      </table>
      <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="200" style="padding-bottom: 2px">
                  <?php
                  
                    //verifica se o usuário pode alterar este evento
                    if ($dados_usuario['evento_altera'] == 1)
                    {

                      ?> 
                      <input name="btnEditarEvento" type="button" class="button" title="Edita este Evento" value="Editar Evento" onclick="wdCarregarFormulario('EventoAltera.php?Id=<?php echo $dados_evento[id] ?>&headers=1','conteudo')" />
                      <?php

                    }

                    //verifica se o usuário pode alterar este evento
                    if ($dados_usuario['evento_exclui'] == 1)
                    {

                      ?>					  					    								
                      <input name="EventoId" type="hidden" value="<?php echo $dados_evento[id] ?>" />
                      <?php

                    }
                  
                  ?>
                </td>
                <td align="right" style="padding-bottom: 2px">
                  <?php
                  
                    //verifica se o usuário pode alterar este evento
                    if ($dados_usuario['evento_relatorio'] == 1)
                    {

                      ?> 
                      <input class="button" title="Emite o relatório dos detalhes do evento" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarFormulario('EventoRelatorio.php?EventoId=<?php echo $dados_evento[id] ?>','conteudo')" />
                      <?php

                    }
                    
                  ?>
                </td>
              </tr>
            </table>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td valign="top" class="dataLabel">Código:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF"><span style="color: #990000;"><b><?php echo $dados_evento["id"] ?></b></span></td>
              </tr>
              <tr>
                <td class="dataLabel" width="18%">
                  <span class="dataLabel">Nome do Evento:</span>             
                </td>
                <td colspan="5" class="tabDetailViewDF">
                  <span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento['nome'] ?></b></span>
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="18%">
                  <span class="dataLabel">Região:</span>             
                </td>
                <td colspan="5" class="tabDetailViewDF">
                  <span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento['regiao_nome'] ?></b></span>
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo nl2br($dados_evento['descricao']) ?></td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Tipo de Evento:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF"><b><?php echo $desc_tipo ?></b></td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Status:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF"><b><?php echo $desc_status ?></b></td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Data do Evento:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <b><?php echo DataMySQLRetornar($dados_evento['data_realizacao']) ?></b>
                </td>
                <td valign="middle" class="dataLabel">Hora:</td>
                <td width="19%" valign="middle" class="tabDetailViewDF">
                  <b><?php echo $dados_evento['hora_realizacao'] ?></b>
                </td>
                <td width="12%" valign="middle" class="dataLabel">Dura&ccedil;&atilde;o:</td>
                <td width="20%" valign="middle" class="tabDetailViewDF">
                  <b><?php echo $dados_evento['duracao'] ?></b>
                </td>
              </tr>           
              <tr>
                <td valign="top" class="dataLabel">Data do Jantar:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php

                    if ($dados_evento['data_jantar'] != '0000-00-00') echo DataMySQLRetornar($dados_evento['data_jantar']);

                  ?>						 
                </td>
                <td valign="top" class="dataLabel">Hora do Jantar:</td>
                <td colspan="3" width="19%" valign="middle" class="tabDetailViewDF">
                  <?php echo $dados_evento['hora_jantar'] ?>
                </td>             
              </tr>            
              <tr>
                <td valign="top" class="dataLabel">Data CERTAME:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php
                  
                    if ($dados_evento['data_certame'] != '0000-00-00') echo DataMySQLRetornar($dados_evento['data_certame']);
                  
                  ?>						 
                </td>
                <td valign="top" class="dataLabel">Hora CERTAME:</td>
                <td colspan="3" width="19%" valign="middle" class="tabDetailViewDF">
                  <?php echo $dados_evento['hora_certame'] ?>
                </td>             
              </tr>            
              <tr>
                <td valign="top" class="dataLabel">Data Foto Convite:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php

                    if ($dados_evento['data_foto_convite'] != '0000-00-00') echo DataMySQLRetornar($dados_evento['data_foto_convite']);

                  ?>                
                </td>
                <td valign="top" class="dataLabel">Hora Foto Convite:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php echo $dados_evento['hora_foto_convite'] ?>
                </td>
                <td valign="middle" class="dataLabel">Local:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php echo nl2br($dados_evento['local_foto_convite']) ?>
                </td>
              </tr>                                
              <tr>
                <td valign="top" class="dataLabel">Data do Ensaio:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php
                  
                    if ($dados_evento['data_ensaio'] != '0000-00-00') echo DataMySQLRetornar($dados_evento['data_ensaio']);
                  
                  ?> 						 
                </td>
                <td valign="top" class="dataLabel">Obs:</td>
                <td colspan="3" width="19%" valign="middle" class="tabDetailViewDF">
                  <span style="font-size: 10px">
                    <?php echo nl2br($dados_evento['obs_ensaio']) ?>
                  </span>
                </td>             
              </tr>					            
              <tr>
                <td valign="top" class="dataLabel">Data do Culto:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php
                  
                    if ($dados_evento['data_culto'] != '0000-00-00') echo DataMySQLRetornar($dados_evento['data_culto']); 
                  
                  ?> 						 
                </td>
                <td valign="top" class="dataLabel">Obs:</td>
                <td colspan="3" width="19%" valign="top" class="tabDetailViewDF">
                  <span style="font-size: 10px">
                    <?php echo nl2br($dados_evento['obs_culto']) ?>
                  </span>				 
                </td>             
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Data da Colação:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php
                  
                    if ($dados_evento['data_colacao'] != '0000-00-00') echo DataMySQLRetornar($dados_evento['data_colacao']);
                  
                  ?> 						 
                </td>
                <td valign="top" class="dataLabel">Obs:</td>
                <td colspan="3" width="19%" valign="top" class="tabDetailViewDF">
                  <span style="font-size: 10px">
                    <?php echo nl2br($dados_evento['obs_colacao']) ?>
                  </span>	 
                </td>             
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Data do Baile:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php
                  
                    if ($dados_evento['data_baile'] != '0000-00-00') echo DataMySQLRetornar($dados_evento['data_baile']);
                  
                  ?> 						 
                </td>
                <td valign="top" class="dataLabel">Obs:</td>
                <td colspan="3" width="19%" valign="middle" class="tabDetailViewDF">
                  <span style="font-size: 10px">
                    <?php echo nl2br($dados_evento['obs_baile']) ?>
                  </span>				 
                </td>             
              </tr>           
              <tr>
                <td valign="top" class="dataLabel">Cliente:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <span style="color: #990000"><b>[<?php echo $dados_evento[cliente_id] ?>]</b></span> -  
                  <a href="#" onclick="wdCarregarFormulario('ClienteExibe.php?ClienteId=<?php echo $dados_evento[cliente_id] ?>','conteudo')" title="Clique para exibir os detalhes deste Cliente"><span style="font-size: 14px;"><?php echo $dados_evento["cliente_nome"] ?></span></a>
                  <br/>
                  <span style="font-size: 9px">
                  <?php echo $dados_evento[cliente_endereco] . " - " . $dados_evento[cliente_complemento] ?>
                  <br/>
                  <?php echo $dados_evento[cliente_bairro] . " - " . $dados_evento[cliente_cep] . " - " . $dados_evento[cliente_cidade] . "/" . $dados_evento[cliente_uf] ?>
                  <br/>
                  <?php echo "Fone: " . $dados_evento[cliente_telefone] . " - Fax: " . $dados_evento[cliente_fax] . " - Celular: " . $dados_evento[cliente_celular] ?>
                  <br/>
                  <?php echo "email: <a href='mailto:" . $dados_evento[cliente_email] . "' title='Clique para enviar um email para o endereço'>$dados_evento[cliente_email]</a>" ?>
                  </span>						 
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Grupo:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo $grupo_status ?></td>
              </tr>           
              <tr>
                <td class="dataLabel">Respons. Orçamento:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo $dados_evento["responsavel_orcamento"] ?></td>
              </tr>
              <tr>
                <td class="dataLabel">Respons. Evento:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo $dados_evento["responsavel"] ?></td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Contatos:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="300" height="20">
                        <b>Nome:</b>
                      </td>
                      <td width="250" height="20">
                        <b>E-Mail:</b>
                      </td>
                      <td height="20">
                        <b>Telefone:</b>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato1] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs1] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone1] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato2] ?></span>                   
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs2] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone2] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato3] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs3] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone3] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato4] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs4] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone4] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato5] ?></span>                   
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs5] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone5] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato6] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs6] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone6] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato7] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs7] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone7] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato8] ?></span>                   
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs8] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone8] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato9] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs9] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone9] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato10] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs10] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone10] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato11] ?></span>                   
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs11] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone11] ?></span>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato12] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_obs12] ?></span>
                      </td>
                      <td height="20">
                        <span style="font-size: 12px"><?php echo $dados_evento[contato_fone12] ?></span>
                      </td>
                    </tr>
                  </table>
                  <br/>
                  <?php

                    if ($dados_evento["contato_obs1"] != '') $string_email.= "$dados_evento[contato_obs1];";
                    if ($dados_evento["contato_obs2"] != '') $string_email.= "$dados_evento[contato_obs2];";
                    if ($dados_evento["contato_obs3"] != '') $string_email.= "$dados_evento[contato_obs3];";
                    if ($dados_evento["contato_obs4"] != '') $string_email.= "$dados_evento[contato_obs4];";
                    if ($dados_evento["contato_obs5"] != '') $string_email.= "$dados_evento[contato_obs5];";
                    if ($dados_evento["contato_obs6"] != '') $string_email.= "$dados_evento[contato_obs6];";
                    if ($dados_evento["contato_obs7"] != '') $string_email.= "$dados_evento[contato_obs7];";
                    if ($dados_evento["contato_obs8"] != '') $string_email.= "$dados_evento[contato_obs8];";
                    if ($dados_evento["contato_obs9"] != '') $string_email.= "$dados_evento[contato_obs9];";
                    if ($dados_evento["contato_obs10"] != '') $string_email.= "$dados_evento[contato_obs10];";
                    if ($dados_evento["contato_obs11"] != '') $string_email.= "$dados_evento[contato_obs11];";
                    if ($dados_evento["contato_obs12"] != '') $string_email.= "$dados_evento[contato_obs12];";

                  ?>
                  <a href="mailto:<?php echo $string_email ?>" title="Evia um e-mail para todos os emails cadastrados da comissao de formatura">Enviar email para a comissão de formaturas</a>
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Pessoas Confirmadas:</td>
                <td colspan="2" class="tabDetailViewDF" valign="middle">
                  <?php echo $dados_evento[numero_confirmado] ?>
                </td>
                <td valign="top" class="dataLabel">Lugares Montados:</td>
                <td colspan="3" class="tabDetailViewDF" valign="middle">
                  <?php echo $dados_evento[lugares_ocupados] ?>
                </td>
              </tr>
              <tr>
                <td valign="middle" class="dataLabel">Alunos na Colação:</td>
                <td class="tabDetailViewDF" valign="middle">
                  <?php echo $dados_evento[alunos_colacao] ?>
                </td>
                <td valign="middle" class="dataLabel">Alunos no Baile:</td>
                <td class="tabDetailViewDF" valign="middle">
                  <?php echo $dados_evento[alunos_baile] ?>
                </td>
                <td valign="middle" class="dataLabel">Participantes no Baile:</td>
                <td class="tabDetailViewDF" valign="middle">
                  <?php echo $dados_evento[participantes_baile] ?>
                </td>
              </tr>
              <tr>
                <td width="130" valign="top" class="dataLabel">Vlr Culto/Formando:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <?php echo number_format($dados_evento["valor_culto"], 2, ',', '.') ?>             
                </td>
              </tr>
              <tr>
                <td width="130" valign="top" class="dataLabel">Vlr Colação/Formando:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <?php echo number_format($dados_evento["valor_colacao"], 2, ',', '.') ?>						 
                </td>
              </tr>
              <tr>
                <td valign="middle" class="dataLabel">Valor Baile/Formando:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <?php echo number_format($dados_evento["valor_baile"], 2, ',', '.') ?>						 
                </td>
              </tr>
              <tr>
                <td valign="middle" class="dataLabel">Valor Evento/Formando:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <?php echo number_format($dados_evento["valor_evento"], 2, ',', '.') ?>						 
                </td> 								
              </tr>
              <tr>
                <td valign="middle" class="dataLabel">Total de Desconto:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <?php echo number_format($dados_evento["valor_desconto_evento"], 2, ',', '.') ?>             
                </td>
              </tr>
              <tr>
                <td valign="middle" class="dataLabel">Total Geral Evento:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <?php echo number_format($dados_evento["valor_geral_evento"], 2, ',', '.') ?>            
                </td>                 
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
                <td colspan="5" class="tabDetailViewDF"><?php echo nl2br($dados_evento["observacoes"]) ?></td>
              </tr>
              <tr>
                <td colspan="6" valign="middle" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0" >
                    <tr valign="middle">
                      <td colspan="12" style="padding-bottom: 4px">
                        <span style="font-size: 11px">Tarefas Adicionais:</span>
                      </td>
                    </tr>
                    <tr>              			
                      <td width="30">
                        <img src="./image/bt_data_gd.gif" width="24" height="24" />
                      </td>
                      <td width="94">
                        <a title="Clique para gerenciar os adendos deste evento" href="#" onclick="wdCarregarFormulario('AdendosEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Adendos</a>
                      </td>
                      <td width="30">
                        <?php

                          //verifica a exibição
                          if ($dados_usuario["evento_data_exibe"] == 1)
                          {

                            ?>
                            <img src="./image/bt_data_evento_gd.gif" />
                            <?php
                            
                          }
                          
                          else
                          {

                            echo '&nbsp;';
                            
                          }
                          
                        ?> 
                      </td>
                      <td width="94">
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_data_exibe"] == 1)
                          {

                            ?>
                            <a title="Clique para gerenciar as datas deste evento" href="#" onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Datas</a>
                            <?php

                          }
                          
                          else
                          {

                            echo '&nbsp;';
                            
                          }
                        ?>
                      </td>
                      <td width="30">
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_participante_exibe"] == 1)
                          {
                            
                            ?>
                            <img src="./image/bt_participante_gd.gif" />
                            <?php
                            
                          }
                          
                          else
                          {

                            echo '&nbsp;';
                            
                          }
                          
                        ?>
                      </td>
                      <td width="94">
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_participante_exibe"] == 1)
                          {

                            ?>
                            <a title="Clique para gerenciar os participantes deste evento" href="#" onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Participantes</a>
                            <?php
                            
                          }

                          else
                          {

                            echo '&nbsp;';

                          }

                        ?>
                      </td>
                      <td width="30">
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_endereco_exibe"] == 1)
                          {

                            ?>
                            <img src="./image/bt_endereco_gd.gif" /> 
                            <?php

                          }

                          else
                          {

                            echo '&nbsp;';

                          }
                        
                        ?>
                      </td>
                      <td width="94">
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_endereco_exibe"] == 1)
                          {

                            ?>
                            <a title="Clique para gerenciar os endereços deste evento" href="#" onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endereços</a>
                            <?php

                          }

                          else
                          {

                            echo '&nbsp;';
                            
                          }
                          
                        ?>
                      </td>
                      <td width="30">
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_produto_exibe"] == 1)
                          {

                            ?>
                            <img src="./image/bt_item_gd.gif"/>
                            <?php

                          }

                          else
                          {

                            echo '&nbsp;';

                          }
                        
                        ?>
                      </td>
                      <td width="94">
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_produto_exibe"] == 1)
                          {

                            ?>
                            <a title="Clique para gerenciar os produtos deste evento" href="#" onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Produtos</a>
                            <?php

                          }

                          else
                          {

                            echo '&nbsp;';

                          }
                          
                        ?>
                      </td>
                      <td width="30">
                        <?php
                        
                          /*
                          //verifica a exibição
                          if ($dados_usuario["evento_servico_exibe"] == 1)
                          {

                            ?>
                            <img src="./image/bt_servico_gd.gif"/> 
                            <?php

                          }

                          else
                          {

                            echo '&nbsp;';

                          }
                          */
                        
                        ?>&nbsp;
                      </td>
                      <td>
                        <?php
                        
                          /*
                          //verifica a exibição
                          if ($dados_usuario["evento_servico_exibe"] == 1)
                          {

                            ?>
                            <a title="Clique para gerenciar os serviços deste evento" href="#" onclick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Serviços</a>
                            <?php

                          }

                          else
                          {

                            echo '&nbsp;';

                          }
                          */
                        
                        ?>&nbsp;
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_terceiro_exibe"] == 1)
                          {

                            ?>
                            <img src="./image/bt_terceiro_gd.gif"/>
                            <?php 

                          } 
                        
                        ?> 
                      </td>
                      <td>
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_terceiro_exibe"] == 1)
                          {

                            ?>
                            <a title="Clique para gerenciar os terceiros deste evento" href="#" onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a>
                            <?php 

                          } 
                        
                        ?> 
                      </td>
                      <td>
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_brinde_exibe"] == 1)
                          {

                            ?>
                            <img src="./image/bt_brinde_gd.gif"/> 
                            <?php 

                          } 
                        
                        ?>
                      </td>
                      <td>
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_brinde_exibe"] == 1)
                          {

                            ?>
                            <a title="Clique para gerenciar os brindes deste evento" href="#" onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a>
                            <?php 

                          } 
                        
                        ?> 
                      </td>
                      <td>
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_repertorio_exibe"] == 1)
                          {

                            ?>
                            <img src="./image/bt_repertorio_gd.gif" />
                            <?php 

                          } 
                        
                        ?> 
                      </td>
                      <td>
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_repertorio_exibe"] == 1)
                          {

                            ?>
                            <a title="Clique para gerenciar o repertório deste evento" href="#" onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repertório</a>
                            <?php 

                          } 
                        
                        ?>
                      </td>
                      <td>
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_formando_exibe"] == 1)
                          {

                            ?>
                            <img src="./image/bt_formando_gd.gif" />
                            <?php 

                          } 
                        
                        ?> 
                      </td>
                      <td>
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_formando_exibe"] == 1)
                          {
                            
                            ?>
                            <a title="Clique para gerenciar os formandos deste evento" href="#" onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
                            <?php 
                            
                          } 
                          
                        ?>
                      </td>																												 
                      <td>
                        <?php

                          //verifica a exibição
                          if ($dados_usuario["evento_fotovideo_exibe"] == 1)
                          {

                            ?>
                            <img src="./image/bt_fotovideo_gd.gif" />
                            <?php 
                            
                          } 
                          
                        ?> 
                      </td>
                      <td>
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_fotovideo_exibe"] == 1)
                          {

                            ?>
                            <a title="Clique para gerenciar o foto e vídeo deste evento" href="#" onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e Vídeo</a>
                            <?php 

                          } 
                        
                        ?>
                      </td> 
                      <td>
                        <?php
                        
                          //verifica a exibição
                          if ($dados_usuario["evento_documento_exibe"] == 1)
                          {

                            ?>
                            <img src="./image/bt_documentos_gd.gif" /> 
                            <?php 

                          } 
                        
                        ?>
                      </td>
                      <td>
                        <?php

                          //verifica a exibição
                          if ($dados_usuario["evento_documento_exibe"] == 1)
                          {

                            ?>
                            <a title="Clique para gerenciar os documentos deste evento" href="#" onclick="wdCarregarFormulario('DocumentosEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Documentos</a>
                            <?php 
                            
                          } 
                          
                        ?>
                      </td>                												
                    </tr>              			
                  </table>
                </td>
              </tr>      		
            </table>
            <br/>
            <span class="TituloModulo">Roteiro do Evento:</span>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
              <tr>
                <td width="130" valign="top" class="dataLabel">Descrição do Roteiro:</td>
                <td class="tabDetailViewDF">
                  <?php echo nl2br($dados_evento["roteiro"]) ?>
                </td>
              </tr>
            </table>
            <br/>       
            <?php

              //verifica a exibição
              if ($dados_usuario["evento_financeiro"] == 1 || $usuarioNome == 'Zulaine')
              {

                ?>
                <span class="TituloModulo">Informações Financeiras:</span>
                <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
                  <tr>
                    <td width="130" valign="top" class="dataLabel">Posição Financeira:</td>
                    <td colspan="3" valign="middle" class="tabDetailViewDF">
                      <b><?php echo $desc_financeiro ?></b>						 
                    </td>
                  </tr>
                  <tr>
                    <td valign="middle" width="110" class="dataLabel">Número da NF:</td>
                    <td colspan="3" valign="middle" class="tabDetailViewDF">
                      <?php echo $dados_evento["numero_nf"] ?>						 
                    </td>             
                  </tr>
                  <tr>
                    <td valign="top" class="dataLabel">Obs Financeiras:</td>
                    <td colspan="3" class="tabDetailViewDF">
                      <?php echo nl2br($dados_evento["observacoes_financeiro"]) ?>
                    </td>
                  </tr>						
                </table>
                <br/>
                <?php
                
              }

              //verifica a exibição
              if ($dados_usuario["evento_fotovideo"] == 1)
              {
                
                ?>
                <span class="TituloModulo">Informações de Foto e Vídeo:</span>
                <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
                  <tr>
                    <td valign="top" class="dataLabel">Foto e Vídeo Liberado?:</td>
                    <td colspan="5" class="tabDetailViewDF">
                      <?php 
                      	$mostra_libera = $dados_evento["foto_video_liberado"] == 1 ? "SIM" : "NÃO";
                      	echo $mostra_libera;
                  	 ?>
                    </td>
                  </tr>
                  <tr>
                    <td width="130" valign="top" class="dataLabel">Valor da Foto:</td>
                    <td valign="middle" class="tabDetailViewDF">
                      <?php echo $dados_evento["valor_foto"] ?>						 
                    </td>
                    <td valign="middle" width="110" class="dataLabel">Valor do DVD:</td>
                    <td colspan="2" width="200" valign="middle" class="tabDetailViewDF">
                      <?php echo $dados_evento["valor_dvd"] ?>						 
                    </td>             
                  </tr>
                  <tr>
                    <td valign="top" class="dataLabel">Obs Foto e Vídeo:</td>
                    <td colspan="5" class="tabDetailViewDF">
                      <?php echo nl2br($dados_evento["obs_fotovideo"]) ?>
                    </td>
                  </tr>
                </table>
                <br/>
                <?php
              
              }
              
            ?>
            <span class="TituloModulo">Quebras de Produtos:</span>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
              <tr>
                <td width="130" valign="top" class="dataLabel">Quebras:</td>
                <td class="tabDetailViewDF">
                  <?php echo nl2br($dados_evento["quebras"]) ?>
                </td>
              </tr>
            </table>
            <br/>
            <span class="TituloModulo">Assinatura Digital:</span>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">	
              <tr>
                <td valign="top" width="130" class="dataLabel">Data de Cadastro: </td>
                <td class="tabDetailViewDF">
                  <?php

                    //Exibe o timestamp do cadastro da conta
                    echo TimestampMySQLRetornar($dados_evento['cadastro_timestamp']);

                  ?>					
                </td>
                <td class="dataLabel">Operador:</td>
                <td class="tabDetailViewDF" colspan="3">
                  <?php
                  
                    //Exibe o nome do operador do cadastro da conta
                    echo $dados_evento['operador_cadastro_nome'] . " " . $dados_evento['operador_cadastro_sobrenome']
                          
                  ?>					
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Data de Altera&ccedil;&atilde;o: </td>
                <td class="tabDetailViewDF">
                  <?php

                    //Verifica se este registro já foi alterado
                    if ($dados_evento[alteracao_operador_id] <> 0)
                    {
                      //Exibe o timestamp da alteração da conta
                      echo TimestampMySQLRetornar($dados_evento['alteracao_timestamp']);

                    }

                  ?>			 		
                </td>
                <td class="dataLabel">Operador:</td>
                <td class="tabDetailViewDF" colspan="3">
                  <?php

                    //Verifica se este registro já foi alterado
                    if ($dados_evento[alteracao_operador_id] <> 0)
                    {
                      //Exibe o nome do operador da alteração da conta
                      echo $dados_evento['operador_alteracao_nome'] . " " . $dados_evento['operador_alteracao_sobrenome'];

                    }

                  ?>			 		
                </td>
              </tr>           
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <?php

              //verifica se o usuário pode ver este submodulo
              if ($dados_usuario["evento_data_exibe"] == 1)
              {

                ?>
                <br/>
                <?php /* EXIBE AS DATAS CADASTRADAS PARA ESTE EVENTO */ ?>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: #9e9e9e 1px solid">
                  <tr>
                    <td height="30">
                      <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td width="30" valign="middle" style="padding-left: 5px"><img src="image/bt_data_evento_gd.gif"/></td>    
                          <td width="190"><span class="TituloModulo">Datas do Evento</span></td>
                          <td>
                            <?php

                              //Verifica o nível de acesso do usuário
                              if ($dados_usuario["evento_data_inclui"] == 1)
                              {

                                ?>
                                [<a title="Clique para gerenciar as datas deste evento" href="#" onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Gerenciar Datas</a>]
                                <?php

                              }
                              
                              else
                              {

                                echo "&nbsp;";
                                
                              }
                              
                            ?>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
                <?php

                  //verifica os participantes já cadastrados para este evento e exibe na tela
                  $sql_consulta = mysql_query("SELECT * FROM eventos_data WHERE evento_id = $EventoId ORDER BY data");

                  //Verifica o numero de registros retornados
                  $registros = mysql_num_rows($sql_consulta);

                ?>
                <div id="40">   
                  <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                    <?php

                      if ($registros > 0)
                      { 

                        ////Caso houverem registros
                        //Exibe o cabeçalho da tabela
                        echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
                                <td width='110' style='padding-left: 8px'>Data/Hora</td>
                                <td>Descrição</td>
                                <td width='20'>&nbsp;</td>
                              </tr>";
                      }

                      //Caso não houverem registros
                      if ($registros == 0)
                      {

                        //Exibe uma linha dizendo que nao registros
                        echo "<tr height='24'>
                                <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
                                  <font color='#33485C'><b>Não há datas cadastradas para este evento</b></font>
                                </td>
                              </tr>";
                      }

                      //Cria o array e o percorre para montar a listagem dinamicamente
                      while ($dados_consulta = mysql_fetch_array($sql_consulta))
                      {

                        ?>
                        <tr valign="middle">
                          <td valign="middle" bgcolor="#fdfdfd" style="padding-left: 8px">
                            <?php echo DataMySQLRetornar($dados_consulta[data]) . " - " . substr($dados_consulta[hora], 0, 5) ?>
                          </td>
                          <td valign="middle" bgcolor="#fdfdfd" style="padding-bottom: 1px;">
                            <font size="2" face="Tahoma">
                              <?php
                                
                                //Verifica se o usuário pode alterar as datas
                                if ($dados_usuario["evento_data_altera"] == 1)
                                {
                                  
                                  ?>
                                  <a title="Clique para alterar esta data" href="#" onclick="wdCarregarFormulario('DataEventoAltera.php?Id=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>&headers=1','conteudo')"><?php echo $dados_consulta['descricao']; ?></a>
                                  <?php
                                  
                                }

                                else
                                {

                                  echo "<b>" . $dados_consulta['descricao'] . "</b>";

                                }
                            
                              ?>
                            </font>          
                          </td>
                          <td valign="middle" bgcolor="#fdfdfd" style="padding-top: 3px">
                            <?php

                              //Verifica se o usuário pode excluir as datas
                              if ($dados_usuario["evento_data_exclui"] == 1)
                              {

                                ?>
                                <img src="image/grid_exclui.gif" alt="Clique para excluir esta data" onclick="if(confirm('Confirma a exclusão desta Data ?')) {wdCarregarFormulario('DataEventoExclui.php?DataId=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer" />
                                <?php
                              }

                            ?>					
                          </td>         					
                        </tr>
                        <tr>
                          <td colspan="2" style="padding-left: 118px">
                            <?php echo nl2br($dados_consulta['observacoes']) ?>
                          </td>
                        </tr>
                        <?php
                   
                      //Fecha o while
                      }
                      
                    ?>
                  </table>
                </div>
                <?php
                  
              //fecha o if de se deve exibir o módulo de datas
              }
              
            ?>	
          </td>
        </tr>
        <tr>
          <td>
            <?php
            
              //verifica se o usuário pode ver os participantes
              if ($dados_usuario["evento_participante_exibe"] == 1)
              {
                
                ?>
                <br/>			
                <?php /* EXIBE AS ATIVIDADES CADASTRADAS PARA ESTE EVENTO */ ?>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: #9e9e9e 1px solid">
                  <tr>
                    <td height="30">
                      <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td width="30" valign="middle" style="padding-left: 5px"><img src="image/bt_atividades.png"/></td>    
                          <td><span class="TituloModulo">Atividades do Evento</span></td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
                <?php
                
                  //verifica as atividades já cadastrados para este evento e exibe na tela
                  $sql_consulta = mysql_query("SELECT 
                                              ati.id,
                                              ati.evento_id,
                                              ati.atividade_id,
                                              ati.data_prazo,
                                              ati.data_execucao,
                                              ati.status,
                                              ati.obs,
                                              ati.usuario_execucao,
                                              atividade.descricao AS atividade_nome,
                                              atividade.dias,
                                              CONCAT(usu.nome, ' ', usu.sobrenome) AS usuario_nome
                                              FROM eventos_atividade ati 
                                              LEFT OUTER JOIN atividades atividade ON atividade.id = ati.atividade_id
                                              LEFT OUTER JOIN usuarios usu ON usu.usuario_id = ati.usuario_execucao
                                              WHERE ati.evento_id = $EventoId 
                                              ORDER BY ati.data_prazo");

                  //Verifica o numero de registros retornados
                  $registros = mysql_num_rows($sql_consulta);
                      
                ?>
              <div id="40">   
                <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                  <?php
                  
                    if ($registros > 0)
                    {

                      //Exibe o cabeçalho da tabela
                      echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
                              <td width='50' align='center'>Status</td> 
                              <td width='350'>Atividade</td>
                              <td width='80'>Prazo</td>
                              <td width='80'>Execução</td>
                              <td>Responsável</td>
                              <td width='70'>&nbsp;</td>
                            </tr>";
                    }

                    //Caso não houverem registros
                    if ($registros == 0)
                    {

                      //Exibe uma linha dizendo que nao registros
                      echo "<tr height='24'>
                              <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
                                <font color='#33485C'><b>Não há atividades definidas para este evento</b></font>
                              </td>
                            </tr>";
                    }

                    //Cria o array e o percorre para montar a listagem dinamicamente
                    while ($dados_consulta = mysql_fetch_array($sql_consulta))
                    {

                      //Efetua o switch para o campo de situacao
                      switch ($dados_consulta[status])
                      {
                        case 0: $desc_situacao = "<img src='image/bt_a_formar.png' title='Em Aberto'>"; break;
                        case 1: $desc_situacao = "<img src='image/bt_formado.png' title='Concluido'>"; break;
                      }

                      $data_hoje = date("Y-m-d", mktime());

                      //Verifica se a atividade esta em atraso
                      if ($dados_consulta["status"] == 0 AND $dados_consulta["data_prazo"] < $data_hoje)
                      {

                        $cor_celula = "#F0D9D9";
                        
                      }
                      
                      else
                      {

                        $cor_celula = "#fdfdfd";
                        
                      }

                      //Verifica se a atividade esta concluida
                      if ($dados_consulta["status"] == 1)
                      {

                        $cor_celula = "#99FF99";
                        
                      }
                      
                    ?>
                    <tr valign="middle">
                      <td height="24" valign="middle" align="center" bgcolor="<?php echo $cor_celula ?>" style="border-top: 1px dotted #aaa; padding-top: 2px">
                        <?php echo $desc_situacao ?>&nbsp;
                      </td>
                      <td valign="middle" bgcolor="<?php echo $cor_celula ?>" style="border-top: 1px dotted #aaa;">
                        <span style="font-size: 12px"><b>
                        <?php

                          echo $dados_consulta[atividade_nome] . '&nbsp;</span></b>(' . $dados_consulta[dias] . ' Dias)';

                          if ($dados_consulta['obs'] != '')
                          {

                            echo "<br/>" . nl2br($dados_consulta['obs']);

                          }

                        ?>
                      </td>
                      <td valign="middle" bgcolor="<?php echo $cor_celula ?>" style="border-top: 1px dotted #aaa; padding-bottom: 1px;">
                        <?php echo DataMySQLRetornar($dados_consulta[data_prazo]); ?>    
                      </td>
                      <td valign="middle" bgcolor="<?php echo $cor_celula ?>" style="border-top: 1px dotted #aaa; padding-bottom: 1px;">
                        <?php
                        
                          if ($dados_consulta[data_execucao] != '0000-00-00')
                          {

                            echo DataMySQLRetornar($dados_consulta[data_execucao]);

                          }

                          else
                          {

                            echo '&nbsp;';

                          }
                        ?>    
                      </td>
                      <td valign="middle" bgcolor="<?php echo $cor_celula ?>" style="border-top: 1px dotted #aaa; padding-bottom: 1px;">
                        <?php

                          if ($dados_consulta[usuario_nome] != '')
                          {

                            echo $dados_consulta[usuario_nome];

                          }

                          else
                          {

                            echo '&nbsp;';

                          }

                        ?>   
                      </td>
                      <td valign="middle" bgcolor="<?php echo $cor_celula ?>" style="border-top: 1px dotted #aaa; padding-top: 3px">
                        <input name="btnGerenciar" type="button" class="button" title="Gerencia esta atividade" value="Gerenciar" onclick="wdCarregarFormulario('AtividadeGerencia.php?AtividadeId=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>&headers=1','conteudo')" style="width: 60px; height: 18px">					
                      </td>         					
                    </tr>
                    <?php
                    
                  //Fecha o while
                  }
                  
                  ?>
                </table>
              </div>
              <?php
              
              //fecha o if de se deve exibir o módulo de datas
              }
            
            ?>
          </td>
        </tr>
        <tr>
          <td>
            <?php
            
              //verifica se o usuário pode ver os participantes
              if ($dados_usuario["evento_participante_exibe"] == 1)
              {

                ?>
                <br/>
                <?php /* EXIBE OS PARTICIPANTES CADASTRADOS PARA ESTE EVENTO */ ?>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: #9e9e9e 1px solid">
                  <tr>
                    <td height="30">
                      <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td width="30" valign="middle" style="padding-left: 5px"><img src="image/bt_participante_gd.gif"/></td>    
                          <td width="190"><span class="TituloModulo">Participantes do Evento</span></td>
                          <td>
                            <?php

                            //verifica se o usuário pode incluir participantes
                            if ($dados_usuario["evento_participante_inclui"] == 1)
                            {

                              ?>
                              [<a title="Clique para gerenciar os participantes deste evento" href="#" onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Gerenciar Participantes</a>]
                              <?php
                              
                            //Fecha o if do nivel de acesso
                            }
                            
                            else
                            {

                              echo "&nbsp;";
                              
                            }
                            
                            ?>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
                <?php
                
                  //verifica os participantes já cadastrados para este evento e exibe na tela
                  $sql_consulta = mysql_query("SELECT
                                              par.id,
                                              par.colaborador_id,
                                              par.funcao_id,
                                              par.notificar,
                                              col.nome as colaborador_nome,
                                              col.telefone,
                                              col.celular,
                                              col.usuario_id,
                                              fun.nome as funcao_nome
                                              FROM eventos_participante par
                                              INNER JOIN colaboradores col ON col.id = par.colaborador_id
                                              LEFT OUTER JOIN funcoes fun ON fun.id = par.funcao_id
                                              WHERE par.evento_id = '$EventoId'
                                              ORDER by col.nome
                                              ");

                  //Verifica o numero de registros retornados
                  $registros = mysql_num_rows($sql_consulta);
                  
                ?>
                <div id="40">   
                  <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                    <?php
                    
                      if ($registros > 0)
                      { 

                        //Caso houverem registros
                        //Exibe o cabeçalho da tabela
                        echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
                                <td width='370'>&nbsp;Participante/Colaborador</td>
                                <td width='200'>Função no evento</td>
                                <td width='70'>Telefone</td>
                                <td width='70'>Celular</td>
                                <td width='20'><div align='left'>&nbsp;</div></td>
                                <td><div align='left'>&nbsp;</div></td>
                              </tr>";
                        
                      }

                      //Caso não houverem registros
                      if ($registros == 0)
                      {

                        //Exibe uma linha dizendo que nao registros
                        echo "<tr height='24'>
                                <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
                                  <font color='#33485C'><b>Não há participantes cadastrados para este evento</b></font>
                                </td>
                              </tr>";
                        
                      }

                      //Cria o array e o percorre para montar a listagem dinamicamente
                      while ($dados_consulta = mysql_fetch_array($sql_consulta))
                      {

                        //Efetua o switch para exibir a imagem para quando o cadastro estiver para notificar
                        switch ($dados_consulta[notificar])
                        {
                          case 0: $ativo_figura = ""; break;
                          case 1: $ativo_figura = "<img src='./image/grid_ativo.gif' alt='Participante Notificado' />"; break;
                        }

                        ?>
                        <tr valign="middle">
                          <td height="18" valign="middle" bgcolor="#fdfdfd" style="padding-bottom: 1px">
                            &nbsp;<font color="#CC3300" size="2" face="Tahoma"><a title="Clique para exibir os dados deste participante" href="#" onclick="wdCarregarFormulario('ColaboradorExibe.php?ColaboradorId=<?php echo $dados_consulta[colaborador_id] ?>&headers=1','conteudo')"><?php echo $dados_consulta['colaborador_nome']; ?></a></font>        
                          </td>
                          <td>
                            <?php echo $dados_consulta[funcao_nome] ?>
                          </td>
                          <td>
                            <?php echo $dados_consulta[telefone] ?>
                          </td>
                          <td>
                            <?php echo $dados_consulta[celular] ?>
                          </td>
                          <td valign="middle" bgcolor="#fdfdfd">
                            <?php echo $ativo_figura ?>
                          </td>											
                          <td height="18" valign="middle" bgcolor="#fdfdfd" style="padding-top: 3px; padding-botton: 0px; padding-right: 6px">
                            <?php
                            
                              //verifica se o usuário pode excluir o participante
                              if ($dados_usuario["evento_participante_exclui"] == 1)
                              {
                                
                                ?> 					
                                <img src="image/grid_exclui.gif" alt="Clique para excluir este participante" onclick="if(confirm('Confirma a exclusão deste Participante ?')) {wdCarregarFormulario('ParticipanteEventoExclui.php?ParticipanteId=<?php echo $dados_consulta[colaborador_id] ?>&UsuarioNotificaId=<?php echo $dados_consulta[usuario_id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer" />
                                <?php
                                
                              }
                              
                            ?>
                          </td>
                        </tr>
                      <?php
                      
                      //Fecha o while
                      }
                      
                    ?>
                  </table>
                </div>
                <?php
              }
              
            ?>	
          </td>
        </tr>
        <tr>
          <td>
            <?php

              //verifica se o usuário pode ver este menu
              if ($dados_usuario["evento_endereco_exibe"] == 1)
              {
                
                ?>
                <br/>	
                <?php /* EXIBE OS ENDEREÇOS CADASTRADOS PARA ESTE EVENTO */ ?>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: #9e9e9e 1px solid">
                  <tr>
                    <td height="30">
                      <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td width="30" valign="middle" style="padding-left: 5px"><img src="image/bt_endereco_gd.gif"/></td>    
                          <td width="190"><span class="TituloModulo">Endereços do Evento</span></td>
                          <td>
                            <?php

                              //verifica se o usuário pode ver este submodulo
                              if ($dados_usuario["evento_endereco_inclui"] == 1)
                              {

                                ?>
                                [<a title="Clique para gerenciar os endereços deste evento" href="#" onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Gerenciar Endereços</a>]
                                <?php

                              //Fecha o if do nivel de acesso
                              }

                              else
                              {

                                echo "&nbsp;";

                              }

                            ?>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
                <?php

                  //verifica os endereços já cadastrados para este evento e exibe na tela
                  $sql_consulta = mysql_query("SELECT
                                              end.id,
                                              end.local_id,
                                              end.fornecedor_id,
                                              end.nome,
                                              end.endereco,
                                              end.complemento,
                                              end.bairro,
                                              end.cep,
                                              end.uf,
                                              end.hora_inicio,
                                              end.hora_termino,
                                              end.telefone,
                                              end.fax,
                                              end.celular,
                                              end.email,
                                              end.observacoes,
                                              loc.nome as local_nome,
                                              cid.nome as cidade_nome,
                                              cid2.nome as fornecedor_cidade_nome,
                                              forn.nome as fornecedor_nome,
                                              forn.endereco as fornecedor_endereco,
                                              forn.complemento as fornecedor_complemento,
                                              forn.bairro as fornecedor_bairro,
                                              forn.cep as fornecedor_cep,
                                              forn.uf as fornecedor_uf,
                                              forn.telefone as fornecedor_telefone,
                                              forn.fax as fornecedor_fax,
                                              forn.celular as fornecedor_celular,
                                              forn.email as fornecedor_email							 																	 
                                              FROM eventos_endereco end
                                              INNER JOIN local_evento loc ON loc.id = end.local_id
                                              LEFT OUTER JOIN cidades cid ON cid.id = end.cidade_id
                                              LEFT OUTER JOIN fornecedores forn ON forn.id = end.fornecedor_id
                                              LEFT OUTER JOIN cidades cid2 ON cid2.id = forn.cidade_id														 
                                              WHERE end.evento_id = $EventoId
                                              ORDER BY loc.nome
                                              ");

                  //Verifica o numero de registros retornados
                  $registros = mysql_num_rows($sql_consulta);

                ?>
                <div id="50">   
                  <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                    <?php

                      if ($registros > 0)
                      { 

                        //Caso houverem registros
                        echo "<tr height='20'>
                                <td width='280' class='listViewThS1'>&nbsp;&nbsp;Local</td>
                                <td width='378' class='listViewThS1'>&nbsp;&nbsp;Nome/Endereço</td>
                                <td width='50' class='listViewThS1'>Início</td>
                                <td width='65' class='listViewThS1'>Término</td>
                                <td class='listViewThS1'>&nbsp;</td>
                              </tr>";
                        
                      }

                      if ($registros == 0)
                      { 
                        
                        //Caso não houverem registros
                        //Exibe uma linha dizendo que nao há regitros
                        echo "<tr height='24'>
                                <td colspan='5' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' >
                                  <font color='#33485C'><b>Não há endereços cadastrados para este evento</b></font>
                                </td>
                              </tr>";
                        
                      }
                      //Monta e percorre o array dos dados	  
                      while ($dados_con = mysql_fetch_array($sql_consulta))
                      {
                        
                        ?>
                        <tr height="14" style="padding: 0px">			
                          <td valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-top: 4px">
                            <strong>&nbsp;&nbsp;<?php echo $dados_con[local_nome] ?></strong>
                          </td>			
                          <td valign="top" bgcolor="#fdfdfd" class="oddListRowS1">
                            <?php
                            
                              //verifica se o usuário pode ver alterar este endereço
                              if ($dados_usuario["evento_endereco_altera"] == 1)
                              {
                                
                                ?>
                                <a title="Clique para alterar este endereço" href="#" onclick="wdCarregarFormulario('EnderecoEventoAltera.php?Id=<?php echo $dados_con[id] ?>&EventoId=<?php echo $EventoId ?>&headers=1','conteudo')">
                                <?php

                                  //Caso seja antigo e não tem fornecedor ID cadastrado
                                  if ($dados_con['fornecedor_id'] == 0)
                                  {

                                    //Imprime então o campo do nome do fornecedor
                                    echo $dados_con['nome'];

                                  }

                                  else
                                  {

                                    //Imprime então o nome do fornecedor
                                    echo $dados_con['fornecedor_nome'];

                                  }
                                  
                                ?>
                                </a>
                                <?php

                              }
                              
                              else
                              {

                                //Caso seja antigo e não tem fornecedor ID cadastrado
                                if ($dados_con['fornecedor_id'] == 0)
                                {

                                  //Imprime então o campo do nome do fornecedor
                                  echo "<b>" . $dados_con['nome'] . "</b>";

                                }

                                else
                                {

                                  //Imprime então o nome do fornecedor
                                  echo "<b>" . $dados_con['fornecedor_nome'] . "</b>";

                                }

                              }
                                
                            ?>
                            <br/>
                            <span style="font-size: 10px">
                            <?php

                              //Caso seja antigo e não tem fornecedor ID cadastrado
                              if ($dados_con['fornecedor_id'] == 0)
                              {

                                echo $dados_con[endereco] . " - " . $dados_con[complemento] . "<br>";
                                echo $dados_con[bairro] . " - " . $dados_con[cep] . " - " . $dados_con[cidade_nome] . "/" . $dados_con[uf] . "<br>";
                                echo "Fone: " . $dados_con[telefone] . " - Fax: " . $dados_con[fax] . " - Celular: " . $dados_con[celular] . "<br>";
                                echo "email: <a href='mailto:" . $dados_con[email] . "' title='Clique para enviar um email para o endereço'>$dados_con[email]</a><br><b>Informações Complementares:</b><br>$dados_con[observacoes]";
                                 
                              }

                              else
                              {

                                echo $dados_con[fornecedor_endereco] . " - " . $dados_con[fornecedor_complemento] . "<br>";
                                echo $dados_con[fornecedor_bairro] . " - " . $dados_con[fornecedor_cep] . " - " . $dados_con[fornecedor_cidade_nome] . "/" . $dados_con[fornecedor_uf] . "<br>";
                                echo "Fone: " . $dados_con[fornecedor_telefone] . " - Fax: " . $dados_con[fornecedor_fax] . " - Celular: " . $dados_con[fornecedor_celular] . "<br>";
                                echo "email: <a href='mailto:" . $dados_con[fornecedor_email] . "' title='Clique para enviar um email para o endereço'>$dados_con[fornecedor_email]</a><br><b>Informações Complementares:</b><br>$dados_con[observacoes]";

                              }
                                
                            ?>							
                            <br/>
                            </span>
                          </td>
                          <td valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-top: 4px">
                            <?php echo $dados_con[hora_inicio] ?>
                          </td>
                          <td valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-top: 4px">
                            <?php echo $dados_con[hora_termino] ?>
                          </td>
                          <td valign="top" style="padding-top: 4px">
                            <div align="center">
                              <?php

                                //verifica se o usuário pode excluir este endereço
                                if ($dados_usuario["evento_endereco_exclui"] == 1)
                                {

                                  ?>           	
                                  <img src="image/grid_exclui.gif" alt="Clique para excluir este endereço" width="12" height="12" border="0" onclick="if(confirm('Confirma a exclusão deste Endereço ?')) {wdCarregarFormulario('EnderecoEventoExclui.php?EnderecoId=<?php echo $dados_con[id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer" />
                                  <?php

                                }

                              ?>
                            </div>
                          </td>					
                        </tr>	
                        <?php
                        
                      //Fecha o while
                      }
                      
                    ?>
                  </table>
                </div>
              <?php
              
            //fecha o if de se deve exibir os endereços do evento
            }

          ?>	
        </td>
      </tr>
      <tr>
                <td>
                  <?php

                    //verifica se o usuário pode ver este submodulo
                    if ($dados_usuario["evento_formando_exibe"] == 1)
                    {

                      ?>
                      <br />
                      <?php /* EXIBE OS FORMANDOS CADASTRADOS PARA ESTE EVENTO */ ?>
                      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: #9e9e9e 1px solid">
                        <tr>
                          <td height="30">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                              <tr>
                                <td width="30" valign="middle" style="padding-left: 5px"><img src="image/bt_formando_gd.gif"/></td>    
                                <td width="190"><span class="TituloModulo">Formandos do Evento</span></td>
                                <td>
                                  <?php

                                    //verifica se o usuário pode incluir formandos
                                    if ($dados_usuario["evento_formando_inclui"] == 1)
                                    {

                                      ?>
                                      [<a title="Clique para gerenciar os formandos deste evento" href="#" onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Gerenciar Formandos</a>]
                                      <?php

                                    }

                                    else
                                    {

                                      echo "&nbsp;";

                                    }
                                  
                                  ?>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <?php
                      
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
                                                    curso.nome AS curso_nome,
                                                    curso.id AS curso_id 
                                                    FROM eventos_formando form
                                                    LEFT OUTER JOIN cursos curso ON curso.id = form.curso_id
                                                    WHERE form.evento_id = $EventoId
                                                    ORDER BY form.nome");

                        //Verifica o numero de registros retornados
                        $registros = mysql_num_rows($sql_consulta);

                      ?>
                      <div id="88">   
                        <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                          <?php
                          
                            //Caso houverem registros
                            if ($registros > 0)
                            {

                              //Exibe o cabeçalho da tabela
                              echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
                                      <td width='22'>&nbsp;&nbsp;S</td>
                                      <td width='50' align='center'>Part.</td>
                                      <td style='padding-left: 5px'>Formando</td>
                                      <td width='140'>Email</td>
                                      <td width='40' align='center'>Senha</td>	      
                                      <td width='70' align='center'>Ações</td>
                                    </tr>";
                              
                            }

                            //Caso não houverem registros
                            if ($registros == 0)
                            {

                              //Exibe uma linha dizendo que nao registros
                              echo "<tr height='24'>
                                      <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
                                        <font color='#33485C'><strong>Não há formandos cadastrados para este evento</strong></font>
                                      </td>
                                    </tr>";

                            }

                            $string_email = "";

                            $total_formandos = 0;

                            $total_formandos_aformar = 0;
                            $total_formandos_formado = 0;
                            $total_formandos_desistente = 0;
                            $total_formandos_aguardando = 0;

                            $total_formandos_culto = 0;
                            $total_formandos_colacao = 0;
                            $total_formandos_jantar = 0;
                            $total_formandos_baile = 0;

                            //Cria o array e o percorre para montar a listagem dinamicamente
                            while ($dados_consulta = mysql_fetch_array($sql_consulta))
                            {

                              //Efetua o switch para o campo de status
                              switch ($dados_consulta[status])
                              {
                                case 1:
                                  $desc_status = "<img src='image/bt_a_formar.png' alt='A se formar'>";
                                  $total_formandos_aformar++;
                                  break;
                                case 2:
                                  $desc_status = "<img src='image/bt_formado.png' alt='Formado'>";
                                  $total_formandos_formado++;
                                  break;
                                case 3:
                                  $desc_status = "<img src='image/bt_desistente.png' alt='Desistente'>";
                                  $total_formandos_desistente++;
                                  break;
                                case 4:
                                  $desc_status = "<img src='image/bt_pendencia.gif' alt='Aguardando Declaração Rescisão'>";
                                  $total_formandos_aguardando++;
                                  break;
                              }

                              $desc_participante = "";

                              if ($dados_consulta["chk_culto"] == 1)
                              {

                                $desc_participante .= "<span title='Formando Participa do Culto'>M</span>&nbsp;";
                                if ($dados_consulta["status"] < 3) $total_formandos_culto++;
                                
                              }

                              if ($dados_consulta["chk_colacao"] == 1)
                              {

                                $desc_participante .= "<span title='Formando Participa da Colação'>C</span>&nbsp;";
                                if ($dados_consulta["status"] < 3) $total_formandos_colacao++;
                                
                              }

                              if ($dados_consulta["chk_jantar"] == 1)
                              {

                                $desc_participante .= "<span title='Formando Participa do Jantar'>J</span>&nbsp;";
                                if ($dados_consulta["status"] < 3) $total_formandos_jantar++;
                              }

                              if ($dados_consulta["chk_baile"] == 1)
                              {

                                $desc_participante .= "<span title='Formando Participa do Baile'>B</span>";
                                if ($dados_consulta["status"] < 3) $total_formandos_baile++;
                              }

                              //Se o formando estiver com restricoes financeiras, muda a cor da celula
                              if ($dados_consulta["situacao"] == 2)
                              {

                                $cor_celula = "#F0D9D9";
                                
                              }
                              
                              else
                              {

                                $cor_celula = "#FFFFFF";
                                
                              }
                              
                              ?>
                              <tr valign="middle">
                                <td bgcolor="<?php echo $cor_celula ?>" align="center"><?php echo $desc_status ?></td>
                                <td bgcolor="<?php echo $cor_celula ?>" align="center"><span style="color: #6666CC;"><b><?php echo $desc_participante ?></b></span></td>
                                <td bgcolor="<?php echo $cor_celula ?>" valign="middle" bgcolor="#fdfdfd" style="padding-left: 5px;">
                                  <?php echo '(' . $dados_consulta['id'] . ') - ' ?><font color="#CC3300" size="2" face="Tahoma"><a title="Clique para alterar os dados deste formando" href="#" onclick="wdCarregarFormulario('FormandoEventoAltera.php?FormandoId=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>&headers=1','conteudo')"><?php echo $dados_consulta["nome"]; ?></a></font><br/><b>Celular:</b> <?php echo $dados_consulta["contato"] ?><br/><?php echo $dados_consulta["obs_financeiro"]; ?>        
                                </td>
                                <td bgcolor="<?php echo $cor_celula ?>" >
                                  <a href="mailto:<?php echo $dados_consulta["email"] ?>" title="Clique para enviar um email para o formando"><?php echo $dados_consulta[email] ?></a>&nbsp;
                                </td>			
                                <td bgcolor="<?php echo $cor_celula ?>" valign="middle" bgcolor="#fdfdfd" style="padding-top: 2px" align='center'>
                                  <span style="color: #990000;"><?php echo $dados_consulta["senha"] ?></span>
                                </td>											
                                <td bgcolor="<?php echo $cor_celula ?>" valign="middle" bgcolor="#fdfdfd" style="padding-top: 3px;">
                                  <?php
                                  
                                    //verifica se o usuário pode ver este menu
                                    if ($dados_usuario["menu_financeiro"] == 1)
                                    {

                                      ?>
                                      <img src="image/bt_boleto_avulso.png" alt="Clique para visualizar os boletos deste formando no site" onclick="abreJanela2('http://www.consolieventos.com.br/workeventos/WorkFinanceiro.php?user_login=<?php echo $dados_consulta[cpf] ?>')" style="cursor: pointer">
                                      <?php
                                      
                                    }

                                    //verifica se o usuário pode ver este menu
                                    if ($dados_usuario["menu_financeiro"] == 1 AND $dados_consulta["email"] != "")
                                    {
                                      
                                      ?>	
                                      <img src="image/bt_recado_novo.gif"  alt="Enviar email de aviso de disponibilidade dos boletos para este formando" onclick="if(confirm('Confirma o envio do email para este Formando ?')) {abreJanela('FormandoEventoNotificaBoleto.php?TipoEnvio=2&FormandoId=<?php echo $dados_consulta[id] ?>')}" style="cursor: pointer">
                                      <?php
                                      
                                    }

                                    if ($usuarioNome == "Maycon" OR $usuarioNome == "Josiane")
                                    {
                                      
                                      ?>
                                      <img src="image/bt_exclui_3.gif" alt="Clique para excluir este formando" onclick="if(confirm('Confirma a exclusão deste Formando ?')) {wdCarregarFormulario('FormandoEventoExclui.php?FormandoId=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer">
                                      <?php
                                      
                                    }
                                    
                                  ?>
                                </td>
                              </tr>
                              <tr>
                                <td bgcolor="<?php echo $cor_celula ?>" colspan="2" style="border-bottom: 1px solid;">
                                  &nbsp;
                                </td>
                                <td bgcolor="<?php echo $cor_celula ?>" colspan="4" valign="middle" bgcolor="#fdfdfd" style="padding-left: 5px; border-bottom: 1px solid;">
                                  <?php
                                  
                                    //Verifica se existe um curso cadastrado
                                    if ($dados_consulta["curso_id"] > 0)
                                    {

                                      echo "<b>CURSO:</b> <span style='color: #990000'>$dados_consulta[curso_nome]</span><br/>";
                                      
                                    }
                                    
                                  ?>
                                  <b>OBS:</b> <?php echo nl2br($dados_consulta["observacoes"]) ?>&nbsp;
                                </td>
                              </tr>
                              <?php

                              $total_formandos++;


                            }

                            ?>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                              <tr>
                                <td height="26" style="padding-top: 4px; padding-bottom: 4px">
                                  <span style="font-size: 12px;">
                                    <b>&nbsp;&nbsp;Estatísticas:<br/><br/>&nbsp;&nbsp;Status:</b><br/>
                                    <?php
                                    echo "&nbsp;&nbsp;Total de formandos no evento: <b><span style='color: #990000'>" . $total_formandos . "</span></b><br/>";
                                    echo "&nbsp;&nbsp;Total de formandos a se formar: <b><span style='color: #990000'>" . $total_formandos_aformar . "</span></b><br/>";
                                    echo "&nbsp;&nbsp;Total de formandos formados: <b><span style='color: #990000'>" . $total_formandos_formado . "</span></b><br/>";
                                    echo "&nbsp;&nbsp;Total de formandos desistentes: <b><span style='color: #990000'>" . $total_formandos_desistente . "</span></b><br/>";
                                    echo "&nbsp;&nbsp;Total de formandos aguardando declaração da rescisão: <b><span style='color: #990000'>" . $total_formandos_aguardando . "</span></b><br/><br/>";

                                    echo "<b>&nbsp;&nbsp;Participação:</b><br/>";

                                    echo "&nbsp;&nbsp;Total de formandos para Culto: <b><span style='color: #990000'>" . $total_formandos_culto . "</span></b><br/>";
                                    echo "&nbsp;&nbsp;Total de formandos para Colação: <b><span style='color: #990000'>" . $total_formandos_colacao . "</span></b><br/>";
                                    echo "&nbsp;&nbsp;Total de formandos para Jantar: <b><span style='color: #990000'>" . $total_formandos_jantar . "</span></b><br/>";
                                    echo "&nbsp;&nbsp;Total de formandos para Baile: <b><span style='color: #990000'>" . $total_formandos_baile . "</span></b>";
                                    
                                    $total_alunos_colacao = $total_formandos_colacao;
                                    $total_alunos_baile = $total_formandos_baile;
                                    
                                    ?>
                                  </span>
                                </td>
                              </tr>
                            </table>
                          </div>
                          <?php
                        }
                        ?>	
                      </td>
                    </tr>
                  </table>  	 
                </td>
              </tr>
      <tr>
        <td>
          <?php
          
            //verifica se o usuário pode ver os produtos
            if ($dados_usuario["evento_produto_exibe"] == 1)
            {

              ?>
              <br/>
              <?php /* EXIBE OS PRODUTOS CADASTRADOS PARA ESTE EVENTO */ ?>
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: #9e9e9e 1px solid">
                <tr>
                  <td height="30">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td width="30" valign="middle" style="padding-left: 5px"><img src="image/bt_item_gd.gif"/></td>    
                        <td width="250"><span class="TituloModulo">Produtos e Serviços do Evento</span></td>
                        <td width="150">
                          <?php

                            //verifica se o usuário pode incluir produtos
                            if ($dados_usuario["evento_produto_inclui"] == 1)
                            {

                              ?>
                              [<a title="Clique para gerenciar os produtos deste evento" href="#" onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Gerenciar Produtos</a>]
                              <?php

                            //Fecha o if do nivel de acesso
                            }

                            else
                            {

                              echo "&nbsp;";

                            }

                          ?>
                        </td>
                        <td>
                          <?php

                            //verifica se o usuário pode ver os serviços
                            if ($dados_usuario["evento_servico_inclui"] == 1)
                            {

                              ?>
                              [<a title="Clique para gerenciar os serviços deste evento" href="#" onclick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Gerenciar Serviços</a>]
                              <?php

                            }

                            else
                            {

                              echo "&nbsp;";

                            }

                          ?>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

                              <div id="70">   
                                <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                                  <tr height="24">
                                    <td colspan="8" valign="middle" bgcolor="#FFFFCD" class="oddListRowS1" style="padding-left: 6px; border-bottom: 1px solid">
                                      <span style="font-size: 18px; color: #33485C"><b>CULTO:</b></span>
                                    </td>
                                  </tr>
                                  <tr height="20">
                                    <td width="60" align='right' class='listViewThS1' style='padding-right: 5px'>Qt Venda</td>
                                    <td width="20" class='listViewThS1'>&nbsp;</td>
                                    <td width="60" align='right' class='listViewThS1' style='padding-right: 5px'>Qt Entrega</td>
                                    <td width="20" class='listViewThS1'>&nbsp;</td>
                                    <td width='300' class='listViewThS1'>&nbsp;&nbsp;Descrição do Produto/Serviço</td>
                                    <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Preço Un.</td>
                                    <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Valor Tot.</td>
                                    <td class='listViewThS1'>&nbsp;</td> 
                                  </tr>
                                  <tr height="20">
                                    <td colspan="8" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 16px;">
                                      <span style="font-size: 16px;"><strong>PRODUTOS:</strong></span>
                                    </td>
                                  </tr>
  <?php
  //EXIBE OS ITENS MARCADOS PARA CULTO
  //Monta a variável de total do evento
  $total_evento = 0;

  //Monta a query para capturar as categorias que existem cadastrados itens
  $sql_produto_culto = mysql_query("SELECT 
                                    ite.id,
                                    ite.categoria_id,											
                                    cat.nome as categoria_nome,
                                    eve.valor_venda
                                    FROM item_evento ite
                                    LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                                    INNER JOIN eventos_item eve ON eve.item_id = ite.id
                                    WHERE eve.evento_id = $EventoId	
                                    AND eve.chk_culto = 1									
                                    GROUP BY cat.nome
                                    ORDER BY cat.nome");

  //Conta o numero de compromissos que a query retornou
  $registros_produto_culto = mysql_num_rows($sql_produto_culto);

  if ($registros_produto_culto == 0)
  {

    //Exibe uma linha dizendo que nao há regitros
    echo "<tr height='18'>
            <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 16px'>
              <font color='#990000'><strong>Não há produtos cadastrados para o CULTO neste evento</strong></font>
            </td>
          </tr>";
    
  }
  
  else
  {

    //Percorre o array das funcoes
    while ($dados_produto_culto = mysql_fetch_array($sql_produto_culto))
    {

      //Fecha o php para imprimir o texto da categoria
      ?>
      <tr height="22">
        <td height="24" colspan="8" valign="bottom" style="border-bottom: 2px dotted #aaa; padding-left: 14px; padding-bottom: 2px">     				 	 
          <span style="font-size: 14px">
            <b>
              <?php

                if ($dados_produto_culto["categoria_id"] == 0)
                {

                  echo "Sem categoria definida";

                }

                else
                {

                  echo $dados_produto_culto["categoria_nome"];

                }

              ?>
            </b>
          </span>
        </td>						 
      </tr>							   
      <?php

        //Monta a query de filtragem dos itens
        $filtra_item = "SELECT 
                        ite.id,
                        ite.nome,
                        ite.unidade,											
                        cat.nome as categoria_nome,
                        eve.quantidade_alocada,
                        eve.quantidade,
                        eve.valor_venda,
                        eve.observacoes
                        FROM item_evento ite
                        LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                        INNER JOIN eventos_item eve ON eve.item_id = ite.id
                        WHERE eve.evento_id = $EventoId
                        AND eve.chk_culto = 1
                        AND ite.categoria_id = '$dados_produto_culto[categoria_id]'
                        ORDER BY cat.nome, ite.nome";

        //Executa a query
        $lista_item = mysql_query($filtra_item);

        //Percorre o array
        while ($dados_item = mysql_fetch_array($lista_item))
        {

          //Efetua o switch do campo de unidade de medida
          switch ($dados_item[unidade])
          {

            case "PC": $texto_unidade = "PC - Peça"; break;
            case "UN": $texto_unidade = "UN - Unidade"; break;
            case "GR": $texto_unidade = "GR - Grama"; break;
            case "KG": $texto_unidade = "KG - Kilo"; break;
            case "LT": $texto_unidade = "LT - Litro"; break;
            case "PT": $texto_unidade = "PT - Pacote"; break;
            case "VD": $texto_unidade = "VD - Vidro"; break;
            case "LT": $texto_unidade = "LT - Lata"; break;
            case "BD": $texto_unidade = "BD - Balde"; break;
            case "CX": $texto_unidade = "CX - Caixa"; break;
            case "GL": $texto_unidade = "GL - Galão"; break;
            case "MT": $texto_unidade = "MT - Metro"; break;
            case "M2": $texto_unidade = "M2 - Metro Quadrado"; break;
            case "M3": $texto_unidade = "M3 - Metro Cúbico"; break;
            
          }

          //Define o botão de exclusão do item
          $botão_exclui_item = "<img src='image/grid_exclui.gif' alt='Clique para remover este item do evento' width='12' height='12' border='0' onClick=\"if(confirm('Confirma a remoção deste item do evento ?')) {wdCarregarFormulario('ItemEventoExclui.php?ItemId=$dados_item[id]&EventoId=$EventoId','conteudo')}\" style='cursor: pointer'>";

          //Define a variável do valor total do item
          $total_item = $dados_item["quantidade"] * $dados_item["valor_venda"];

          $total_culto = $total_culto + $total_item;

          //Ajusta o total do evento
          $total_evento = $total_evento + $total_item;
          
          ?>
          <tr>
            <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
              <?php echo $dados_item['quantidade'] ?>
            </td>
            <td valign="middle" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px" bgcolor="#fdfdfd" class="currentTabList">
              <span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span>
            </td>	
            <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
              <?php echo $dados_item['quantidade_alocada'] ?>
            </td>
            <td valign="middle" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px" bgcolor="#fdfdfd" class="currentTabList">
              <span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span>
            </td>
            <td height="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px; padding-bottom: 2px">
              <?php
              
                echo $dados_item[nome];

                if ($dados_item[observacoes] != '')
                {

                  echo "<br/><span class='TextoAzul'>" . nl2br($dados_item[observacoes]) . "</span>";

                }

                ?>
              </td>
              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                <?php echo number_format($dados_item[valor_venda], 2, ",", ".") ?>
              </td>
              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                <?php echo number_format($total_item, 2, ",", ".") ?>
              </td>
              <td valign="middle" style="border-bottom: 1px dotted #aaa; padding-top: 1px">
                <div align="center">
                  <?php
                  
                    //verifica se o usuário pode excluir o produto
                    if ($dados_usuario["evento_produto_exclui"] == 1)
                    {

                      //Exibe o botão de excluir o item
                      echo $botão_exclui_item;
                      
                    }
                    
                    else
                    {

                      echo '&nbsp;';
                      
                    }
                    
                  ?>            	 
                </div>
              </td>
            </tr>				
          <?php
        
        //Fecha o while dos itens
        }

      //Fecha o while das categorias
      }

      $total_culto_formata = number_format($total_culto, 2, ",", ".");

      echo "<tr height='24'>
              <td colspan='6' valign='middle' align='right' bgcolor='#fdfdfd' class='oddListRowS1' >
                <font color='#990000'><strong>Total de Itens do Culto:</strong></font>
              </td>
              <td align='right' style='padding-right: 8px'>
                <font color='#990000'><strong>$total_culto_formata</strong></font>
              </td>
              <td>
                &nbsp;
              </td>
            </tr>";
      
    }
  ?>
  <tr height="20">
    <td colspan="8" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 16px;">
      <span style="font-size: 16px;"><strong>SERVIÇOS:</strong></span>
    </td>
  </tr>		
  <?php
  
    //Monta a query para capturar as categorias que existem cadastrados itens
    $sql_servico_culto = mysql_query("SELECT 
                                      serv.id,
                                      serv.categoria_id,											
                                      serv.nome as categoria_nome,
                                      serv.valor_venda
                                      FROM servico_evento serv
                                      LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
                                      INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
                                      WHERE eve.evento_id = $EventoId	
                                      AND eve.chk_culto = 1
                                      GROUP BY cat.nome
                                      ORDER BY cat.nome");

    //Conta o numero de compromissos que a query retornou
    $registros_servico_culto = mysql_num_rows($sql_servico_culto);

    if ($registros_servico_culto == 0)
    {

      //Exibe uma linha dizendo que nao há regitros
      echo "
            <tr height='18'>
              <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 16px'>
                <font color='#990000'><strong>Não há serviços cadastrados para o CULTO neste evento</strong></font>
              </td>
            </tr>";
      
    }
    
    else
    {

      //Percorre o array das funcoes
      while ($dados_servico_culto = mysql_fetch_array($sql_servico_culto))
      {

        //Fecha o php para imprimir o texto da categoria
        ?>
        <tr height="22">
          <td colspan="8" valign="bottom" style="border-bottom: 2px dotted #aaa; padding-left: 14px">    				 	 
            <span style="font-size: 14px"><b>
              <?php
              
                if ($dados_servico_culto["categoria_id"] == 0)
                {

                  echo "Sem centro de custo definido";

                }

                else
                {

                  echo $dados_servico_culto["categoria_nome"];

                }
              
              ?>
            </b></span>
          </td>						 
        </tr>
        <?php
          
          //Monta a query de filtragem dos servicos
          $filtra_servico = "SELECT 
                            serv.id,
                            serv.nome,
                            cat.nome as categoria_nome,
                            eve.quantidade,
                            eve.valor_venda,
                            eve.observacoes
                            FROM servico_evento serv
                            LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
                            INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
                            WHERE eve.evento_id = $EventoId
                            AND eve.chk_culto = 1
                            AND serv.categoria_id = '$dados_servico_culto[categoria_id]'
                            ORDER BY cat.nome, serv.nome";

          //Executa a query
          $lista_servico = mysql_query($filtra_servico);

          //Percorre o array
          while ($dados_servico = mysql_fetch_array($lista_servico))
          {

            //Define a variável do valor total do servico
            $total_servico = $dados_servico[quantidade] * $dados_servico[valor_venda];

            //Ajusta o total do evento
            $total_servico_culto = $total_servico_culto + $total_servico;
            
            ?>
            <tr valign="middle">
              <td colspan="4" valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                <?php echo $dados_servico[quantidade] ?>
              </td>					 
              <td height="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px; padding-bottom: 2px">
                <?php
                
                  echo $dados_servico[nome];

                  if ($dados_servico[observacoes] != '')
                  {

                    echo "<br/><span class='TextoAzul'>" . nl2br($dados_servico[observacoes]) . "</span>";

                  }

                ?>
              </td>
              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                <?php echo number_format($dados_servico[valor_venda], 2, ",", ".") ?>
              </td>
              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                <?php echo number_format($total_servico, 2, ",", ".") ?>
              </td>
              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                <div align="center">
                  &nbsp;            	 
                </div>
              </td>
            </tr>
            <?php

          }

        }

        $total_servico_culto_formata = number_format($total_culto_avulso, 2, ",", ".");

        echo "<tr height='24'>
                <td colspan='6' valign='middle' align='right' bgcolor='#fdfdfd' class='oddListRowS1' >
                  <font color='#990000'><strong>Total de Serviços do Culto:</strong></font>
                </td>
                <td align='right' style='padding-right: 8px'>
                  <font color='#990000'><strong>$total_servico_culto_formata</strong></font>
                </td>
                <td>
                  &nbsp;
                </td>
              </tr>";
        
      }
      
      ?>
      <tr height="24">
        <td colspan="8" valign="middle" bgcolor="#FFFFCD" class="oddListRowS1" style="padding-left: 6px; border-top:1px solid;border-bottom: 1px solid">
          <span style="font-size: 18px; color: #33485C"><b>COLAÇÃO:</b></span>
        </td>
      </tr>
      <tr height="20">
        <td width="60" align='right' class='listViewThS1' style='padding-right: 5px'>Qt Venda</td>
        <td width="20" class='listViewThS1'>&nbsp;</td>
        <td width="60" align='right' class='listViewThS1' style='padding-right: 5px'>Qt Entrega</td>
        <td width="20" class='listViewThS1'>&nbsp;</td>
        <td width='300' class='listViewThS1'>&nbsp;&nbsp;Descrição do Produto/Serviço</td>
        <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Preço Un.</td>
        <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Valor Tot.</td>
        <td class='listViewThS1'>&nbsp;</td> 
      </tr>
      <tr height="20">
        <td colspan="8" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 16px;">
          <span style="font-size: 16px;"><strong>PRODUTOS:</strong></span>
        </td>
      </tr>
      <?php

        //Monta a query para capturar as categorias que existem cadastrados itens
        $sql_produto_colacao = mysql_query("SELECT 
                                            ite.id,
                                            ite.categoria_id,											
                                            cat.nome as categoria_nome,
                                            eve.valor_venda
                                            FROM item_evento ite
                                            LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                                            INNER JOIN eventos_item eve ON eve.item_id = ite.id
                                            WHERE eve.evento_id = $EventoId	
                                            AND eve.chk_colacao = 1
                                            GROUP BY cat.nome
                                            ORDER BY cat.nome");

        //Conta o numero de compromissos que a query retornou
        $registros_produto_colacao = mysql_num_rows($sql_produto_colacao);

        if ($registros_produto_colacao == 0)
        {

          //Exibe uma linha dizendo que nao há regitros
          echo "<tr height='18'>
                  <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 16px'>
                    <font color='#990000'><strong>Não há produtos cadastrados para a COLACAO neste evento</strong></font>
                  </td>
                </tr>";

        }

        else
        {

          //Percorre o array das funcoes
          while ($dados_produto_colacao = mysql_fetch_array($sql_produto_colacao))
          {

            //Fecha o php para imprimir o texto da categoria
            ?>
            <tr height="22">
              <td height="24" colspan="8" valign="bottom" style="border-bottom: 2px dotted #aaa; padding-left: 14px; padding-bottom: 2px">      				 	 
                <span style="font-size: 14px"><b>
                  <?php
                  
                    if ($dados_produto_colacao["categoria_id"] == 0)
                    {

                      echo "Sem centro de custo definido";
                      
                    }
                    
                    else
                    {

                      echo $dados_produto_colacao["categoria_nome"];
                      
                    }
                    
                  ?>
                </b></span>
              </td>						 
            </tr>
            <?php

              //Monta a query de filtragem dos itens
              $filtra_item = "SELECT 
                              ite.id,
                              ite.nome,
                              ite.unidade,											
                              cat.nome as categoria_nome,
                              eve.quantidade_alocada,
                              eve.quantidade,
                              eve.valor_venda,
                              eve.observacoes
                              FROM item_evento ite
                              LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                              INNER JOIN eventos_item eve ON eve.item_id = ite.id
                              WHERE eve.evento_id = $EventoId
                              AND eve.chk_colacao = 1
                              AND ite.categoria_id = '$dados_produto_colacao[categoria_id]'
                              ORDER BY cat.nome, ite.nome";

              //Executa a query
              $lista_item = mysql_query($filtra_item);

              //Percorre o array
              while ($dados_item = mysql_fetch_array($lista_item))
              {

                //Efetua o switch do campo de unidade de medida
                switch ($dados_item[unidade])
                {

                  case "PC": $texto_unidade = "PC - Peça"; break;
                  case "UN": $texto_unidade = "UN - Unidade"; break;
                  case "GR": $texto_unidade = "GR - Grama"; break;
                  case "KG": $texto_unidade = "KG - Kilo"; break;
                  case "LT": $texto_unidade = "LT - Litro"; break;
                  case "PT": $texto_unidade = "PT - Pacote"; break;
                  case "VD": $texto_unidade = "VD - Vidro"; break;
                  case "LT": $texto_unidade = "LT - Lata"; break;
                  case "BD": $texto_unidade = "BD - Balde"; break;
                  case "CX": $texto_unidade = "CX - Caixa"; break;
                  case "GL": $texto_unidade = "GL - Galão"; break;
                  case "MT": $texto_unidade = "MT - Metro"; break;
                  case "M2": $texto_unidade = "M2 - Metro Quadrado"; break;
                  case "M3": $texto_unidade = "M3 - Metro Cúbico"; break;

                }

                //Define o botão de exclusão do item
                $botão_exclui_item = "<img src='image/grid_exclui.gif' alt='Clique para remover este item do evento' width='12' height='12' border='0' onClick=\"if(confirm('Confirma a remoção deste item do evento ?')) {wdCarregarFormulario('ItemEventoExclui.php?ItemId=$dados_item[id]&EventoId=$EventoId','conteudo')}\" style='cursor: pointer'>";

                //Define a variável do valor total do item
                $total_item = $dados_item["quantidade"] * $dados_item["valor_venda"];

                $total_colacao = $total_colacao + $total_item;

                //Ajusta o total do evento
                $total_evento = $total_evento + $total_item;

              ?>
            <tr>
              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                <?php echo $dados_item[quantidade] ?>
              </td>
              <td valign="middle" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px" bgcolor="#fdfdfd" class="currentTabList">
                <span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span>
              </td>	
              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                <?php echo $dados_item[quantidade_alocada] ?>
              </td>
              <td valign="middle" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px" bgcolor="#fdfdfd" class="currentTabList">
                <span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span>
              </td>
              <td height="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px; padding-bottom: 2px">
                <?php

                  echo $dados_item[nome];

                  if ($dados_item[observacoes] != '')
                  {

                    echo "<br/><span class='TextoAzul'>" . nl2br($dados_item[observacoes]) . "</span>";

                  }

                ?>
              </td>
              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                <?php echo number_format($dados_item[valor_venda], 2, ",", ".") ?>
              </td>
              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                <?php echo number_format($total_item, 2, ",", ".") ?>
              </td>
              <td valign="middle" style="border-bottom: 1px dotted #aaa; padding-top: 1px">
                <div align="center">
                  <?php

                    //verifica se o usuário pode excluir o produto
                    if ($dados_usuario["evento_produto_exclui"] == 1)
                    {

                      //Exibe o botão de excluir o item
                      echo $botão_exclui_item;

                    }

                    else
                    {

                      echo '&nbsp;';

                    }

                  ?>            	 
                </div>
              </td>
            </tr>				
            <?php
                                      
          //Fecha o while dos itens
          }

        //Fecha o while das categorias
        }

        $total_colacao_formata = number_format($total_colacao, 2, ",", ".");

        echo "<tr height='24'>
                <td colspan='6' valign='middle' align='right' bgcolor='#fdfdfd' class='oddListRowS1' >
                  <font color='#990000'><strong>Total de Itens da Colação:</strong></font>
                </td>
                <td align='right' style='padding-right: 8px'>
                  <font color='#990000'><strong>$total_colacao_formata</strong></font>
                </td>
                <td>
                  &nbsp;
                </td>
              </tr>";

      }
      
    ?>
    <tr height="20">
      <td colspan="8" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 16px;">
        <span style="font-size: 16px;"><strong>SERVIÇOS:</strong></span>
      </td>
    </tr>
    <?php

      //Monta a query para capturar as categorias que existem cadastrados itens
      $sql_servico_colacao = mysql_query("SELECT 
                                          serv.id,
                                          serv.categoria_id,											
                                          serv.nome as categoria_nome,
                                          serv.valor_venda
                                          FROM servico_evento serv
                                          LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
                                          INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
                                          WHERE eve.evento_id = $EventoId	
                                          AND eve.chk_colacao = 1
                                          GROUP BY cat.nome
                                          ORDER BY cat.nome");

      //Conta o numero de compromissos que a query retornou
      $registros_servico_colacao = mysql_num_rows($sql_servico_colacao);

      if ($registros_servico_colacao == 0)
      {

        //Exibe uma linha dizendo que nao há regitros
        echo "<tr height='18'>
                <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 16px'>
                  <font color='#990000'><strong>Não há serviços cadastrados para a COLAÇÃO neste evento</strong></font>
                </td>
              </tr>";

        }

        else
        {

          //Percorre o array das funcoes
          while ($dados_servico_colacao = mysql_fetch_array($sql_servico_colacao))
          {

            //Fecha o php para imprimir o texto da categoria
            ?>
            <tr height="22">
              <td colspan="8" valign="bottom" style="border-bottom: 2px dotted #aaa; padding-left: 14px">    				 	 
                <span style="font-size: 14px"><b>
                <?php

                  if ($dados_servico_colacao["categoria_id"] == 0)
                  {

                    echo "Sem centro de custo definido";

                  }

                  else
                  {

                    echo $dados_servico_colacao["categoria_nome"];

                  }

                ?>
                </b></span>
              </td>						 
            </tr>
            <?php
                                      
              //Monta a query de filtragem dos servicos
              $filtra_servico = "SELECT 
                                serv.id,
                                serv.nome,
                                cat.nome as categoria_nome,
                                eve.quantidade,
                                eve.valor_venda,
                                eve.observacoes
                                FROM servico_evento serv
                                LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
                                INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
                                WHERE eve.evento_id = $EventoId
                                AND eve.chk_colacao = 1
                                AND serv.categoria_id = '$dados_servico_colacao[categoria_id]'
                                ORDER BY cat.nome, serv.nome";

              //Executa a query
              $lista_servico = mysql_query($filtra_servico);

              //Percorre o array
              while ($dados_servico = mysql_fetch_array($lista_servico))
              {


                //Define a variável do valor total do servico
                $total_servico = $dados_servico[quantidade] * $dados_servico[valor_venda];


                //Ajusta o total do evento
                $total_servico_colacao = $total_servico_colacao + $total_servico;
                
                ?>
                <tr valign="middle">
                  <td colspan="4" valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                    <?php echo $dados_servico[quantidade] ?>
                  </td>					 
                  <td height="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px; padding-bottom: 2px">
                    <?php
                      
                      echo $dados_servico[nome];

                      if ($dados_servico[observacoes] != '')
                      {

                        echo "<br/><span class='TextoAzul'>" . nl2br($dados_servico[observacoes]) . "</span>";
                        
                      }
                      
                    ?>
                  </td>
                  <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                    <?php echo number_format($dados_servico[valor_venda], 2, ",", ".") ?>
                  </td>
                  <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                    <?php echo number_format($total_servico, 2, ",", ".") ?>
                  </td>
                  <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                    <div align="center">
                      &nbsp;            	 
                    </div>
                  </td>
                </tr>
                <?php

              }

            }

            $total_servico_colacao_formata = number_format($total_servico_colacao, 2, ",", ".");

            echo "<tr height='24'>
                    <td colspan='6' valign='middle' align='right' bgcolor='#fdfdfd' class='oddListRowS1' >
                      <font color='#990000'><strong>Total de Serviços da Colação:</strong></font>
                    </td>
                    <td align='right' style='padding-right: 8px'>
                      <font color='#990000'><strong>$total_servico_colacao_formata</strong></font>
                    </td>
                    <td>
                      &nbsp;
                    </td>
                  </tr>";
            
          }

          //*** ITENS DO JANTAR ***
          //Monta a variável de total do evento
          $total_evento = 0;
          
          ?>
          <tr height="24">
            <td colspan="8" valign="middle" bgcolor="#FFFFCD" class="oddListRowS1" style="padding-left: 6px; border-top:1px solid;border-bottom: 1px solid">
              <span style="font-size: 18px; color: #33485C"><b>JANTAR:</b></span>
            </td>
          </tr>
          <tr height="20">
            <td width="60" align='right' class='listViewThS1' style='padding-right: 5px'>Qt Venda</td>
            <td width="20" class='listViewThS1'>&nbsp;</td>
            <td width="60" align='right' class='listViewThS1' style='padding-right: 5px'>Qt Entrega</td>
            <td width="20" class='listViewThS1'>&nbsp;</td>
            <td width='300' class='listViewThS1'>&nbsp;&nbsp;Descrição do Produto/Serviço</td>
            <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Preço Un.</td>
            <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Valor Tot.</td>
            <td class='listViewThS1'>&nbsp;</td> 
          </tr>
          <tr height="20">
            <td colspan="8" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 16px;">
              <span style="font-size: 16px;"><strong>PRODUTOS:</strong></span>
            </td>
          </tr>
          <?php

            //Monta a query para capturar as categorias que existem cadastrados itens
            $sql_produto_jantar = mysql_query("SELECT 
                                              ite.id,
                                              ite.categoria_id,											
                                              cat.nome as categoria_nome,
                                              eve.valor_venda
                                              FROM item_evento ite
                                              LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                                              INNER JOIN eventos_item eve ON eve.item_id = ite.id
                                              WHERE eve.evento_id = $EventoId	
                                              AND eve.chk_jantar = 1
                                              GROUP BY cat.nome
                                              ORDER BY cat.nome");

            //Conta o numero de compromissos que a query retornou
            $registros_produto_jantar = mysql_num_rows($sql_produto_jantar);

            if ($registros_produto_jantar == 0)
            {

              //Exibe uma linha dizendo que nao há regitros
              echo "<tr height='18'>
                      <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 16px'>
                        <font color='#990000'><strong>Não há produtos cadastrados para o JANTAR neste evento</strong></font>
                      </td>
                    </tr>";
              
            }

            else
            {

              //Percorre o array das funcoes
              while ($dados_produto_jantar = mysql_fetch_array($sql_produto_jantar))
              {

                //Fecha o php para imprimir o texto da categoria
                ?>

                <tr height="22">
                  <td height="24" colspan="8" valign="bottom" style="border-bottom: 2px dotted #aaa; padding-left: 14px; padding-bottom: 2px">      				 	 
                    <span style="font-size: 14px"><b>
                      <?php
                        
                        if ($dados_produto_jantar["categoria_id"] == 0)
                        {

                          echo "Sem centro de custo definido";

                        }

                        else
                        {

                          echo $dados_produto_jantar["categoria_nome"];

                        }

                      ?>
                    </b></span>
                  </td>						 
                </tr>
                <?php

                  //Monta a query de filtragem dos itens
                  $filtra_item = "SELECT 
                                  ite.id,
                                  ite.nome,
                                  ite.unidade,											
                                  cat.nome as categoria_nome,
                                  eve.quantidade_alocada,
                                  eve.quantidade,
                                  eve.valor_venda,
                                  eve.observacoes
                                  FROM item_evento ite
                                  LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                                  INNER JOIN eventos_item eve ON eve.item_id = ite.id
                                  WHERE eve.evento_id = $EventoId
                                  AND eve.chk_jantar = 1
                                  AND ite.categoria_id = '$dados_produto_jantar[categoria_id]'
                                  ORDER BY cat.nome, ite.nome";

                  //Executa a query
                  $lista_item = mysql_query($filtra_item);

                  //Percorre o array
                  while ($dados_item = mysql_fetch_array($lista_item))
                  {

                    //Efetua o switch do campo de unidade de medida
                    switch ($dados_item[unidade])
                    {

                      case "PC": $texto_unidade = "PC - Peça"; break;
                      case "UN": $texto_unidade = "UN - Unidade"; break;
                      case "GR": $texto_unidade = "GR - Grama"; break;
                      case "KG": $texto_unidade = "KG - Kilo"; break;
                      case "LT": $texto_unidade = "LT - Litro"; break;
                      case "PT": $texto_unidade = "PT - Pacote"; break;
                      case "VD": $texto_unidade = "VD - Vidro"; break;
                      case "LT": $texto_unidade = "LT - Lata"; break;
                      case "BD": $texto_unidade = "BD - Balde"; break;
                      case "CX": $texto_unidade = "CX - Caixa"; break;
                      case "GL": $texto_unidade = "GL - Galão"; break;
                      case "MT": $texto_unidade = "MT - Metro"; break;
                      case "M2": $texto_unidade = "M2 - Metro Quadrado"; break;
                      case "M3": $texto_unidade = "M3 - Metro Cúbico"; break;
                    }

                    //Define o botão de exclusão do item
                    $botão_exclui_item = "<img src='image/grid_exclui.gif' alt='Clique para remover este item do evento' width='12' height='12' border='0' onClick=\"if(confirm('Confirma a remoção deste item do evento ?')) {wdCarregarFormulario('ItemEventoExclui.php?ItemId=$dados_item[id]&EventoId=$EventoId','conteudo')}\" style='cursor: pointer'>";

                    //Define a variável do valor total do item
                    $total_item = $dados_item["quantidade"] * $dados_item["valor_venda"];

                    $total_jantar = $total_jantar + $total_item;

                    //Ajusta o total do evento
                    $total_evento = $total_evento + $total_item;

                  ?>
                  <tr>
                    <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                      <?php echo $dados_item[quantidade] ?>
                    </td>
                    <td valign="middle" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px" bgcolor="#fdfdfd" class="currentTabList">
                      <span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span>
                    </td>	
                    <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                      <?php echo $dados_item[quantidade_alocada] ?>
                    </td>
                    <td valign="middle" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px" bgcolor="#fdfdfd" class="currentTabList">
                      <span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span>
                    </td>
                    <td height="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px; padding-bottom: 2px">
                      <?php

                        echo $dados_item[nome];

                        if ($dados_item[observacoes] != '')
                        {

                          echo "<br/><span class='TextoAzul'>" . nl2br($dados_item[observacoes]) . "</span>";

                        }

                      ?>
                    </td>
                    <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                      <?php echo number_format($dados_item[valor_venda], 2, ",", ".") ?>
                    </td>
                    <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                      <?php echo number_format($total_item, 2, ",", ".") ?>
                    </td>
                    <td valign="middle" style="border-bottom: 1px dotted #aaa; padding-top: 1px">
                      <div align="center">
                        <?php

                          //verifica se o usuário pode excluir o produto
                          if ($dados_usuario["evento_produto_exclui"] == 1)
                          {

                            //Exibe o botão de excluir o item
                            echo $botão_exclui_item;

                          }

                          else
                          {

                            echo '&nbsp;';

                          }

                        ?>            	 
                      </div>
                    </td>
                  </tr>				
                  <?php
                  
                //Fecha o while dos itens
                }

              //Fecha o while das categorias
              }

              $total_jantar_formata = number_format($total_jantar, 2, ",", ".");

              echo "<tr height='24'>
                      <td colspan='6' valign='middle' align='right' bgcolor='#fdfdfd' class='oddListRowS1' >
                        <font color='#990000'><strong>Total de Itens do Jantar:</strong></font>
                      </td>
                      <td align='right' style='padding-right: 8px'>
                        <font color='#990000'><strong>$total_jantar_formata</strong></font>
                      </td>
                      <td>
                        &nbsp;
                      </td>
                    </tr>";
              
            }

          ?>
          <tr height="20">
            <td colspan="8" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 16px;">
              <span style="font-size: 16px;"><strong>SERVIÇOS:</strong></span>
            </td>
          </tr>
          <?php

            //Monta a query para capturar as categorias que existem cadastrados itens
            $sql_servico_jantar = mysql_query("SELECT 
                                              serv.id,
                                              serv.categoria_id,											
                                              serv.nome as categoria_nome,
                                              serv.valor_venda
                                              FROM servico_evento serv
                                              LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
                                              INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
                                              WHERE eve.evento_id = $EventoId	
                                              AND eve.chk_jantar = 1
                                              GROUP BY cat.nome
                                              ORDER BY cat.nome");

            //Conta o numero de compromissos que a query retornou
            $registros_servico_jantar = mysql_num_rows($sql_servico_jantar);

            if ($registros_servico_jantar == 0)
            {

              //Exibe uma linha dizendo que nao há regitros
              echo "<tr height='18'>
                      <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 16px'>
                        <font color='#990000'><strong>Não há serviços cadastrados para o JANTAR neste evento</strong></font>
                      </td>
                    </tr>";
              
            }

            else
            {

              //Percorre o array das funcoes
              while ($dados_servico_jantar = mysql_fetch_array($sql_servico_jantar))
              {

                //Fecha o php para imprimir o texto da categoria
                ?>
                <tr height="22">
                  <td colspan="8" valign="bottom" style="border-bottom: 2px dotted #aaa; padding-left: 14px">    				 	 
                    <span style="font-size: 14px"><b>
                    <?php

                      if ($dados_servico_jantar["categoria_id"] == 0)
                      {

                        echo "Sem centro de custo definido";

                      }

                      else
                      {

                        echo $dados_servico_jantar["categoria_nome"];

                      }

                    ?>
                    </b></span>
                  </td>						 
                </tr>
                <?php

                  //Monta a query de filtragem dos servicos
                  $filtra_servico = "SELECT 
                                    serv.id,
                                    serv.nome,
                                    cat.nome as categoria_nome,
                                    eve.quantidade,
                                    eve.valor_venda,
                                    eve.observacoes
                                    FROM servico_evento serv
                                    LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
                                    INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
                                    WHERE eve.evento_id = $EventoId
                                    AND eve.chk_jantar = 1
                                    AND serv.categoria_id = '$dados_servico_jantar[categoria_id]'
                                    ORDER BY cat.nome, serv.nome";

                  //Executa a query
                  $lista_servico = mysql_query($filtra_servico);

                  //Percorre o array
                  while ($dados_servico = mysql_fetch_array($lista_servico))
                  {

                    //Define a variável do valor total do servico
                    $total_servico = $dados_servico[quantidade] * $dados_servico[valor_venda];

                    //Ajusta o total do evento
                    $total_servico_jantar = $total_servico_jantar + $total_servico;

                    ?>
                    <tr valign="middle">
                      <td colspan="4" valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                        <?php echo $dados_servico[quantidade] ?>
                      </td>					 
                      <td height="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px; padding-bottom: 2px">
                        <?php

                          echo $dados_servico['nome'];

                          if ($dados_servico[observacoes] != '')
                          {

                            echo "<br/><span class='TextoAzul'>" . nl2br($dados_servico[observacoes]) . "</span>";

                          }
                        
                        ?>
                      </td>
                      <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                        <?php echo number_format($dados_servico[valor_venda], 2, ",", ".") ?>
                      </td>
                      <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                        <?php echo number_format($total_servico, 2, ",", ".") ?>
                      </td>
                      <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                        <div align="center">
                          &nbsp;            	 
                        </div>
                      </td>
                    </tr>
                    <?php

                  }

                }

                $total_servico_jantar_formata = number_format($total_servico_jantar, 2, ",", ".");

                echo "<tr height='24'>
                        <td colspan='6' valign='middle' align='right' bgcolor='#fdfdfd' class='oddListRowS1' >
                          <font color='#990000'><strong>Total de Serviços do Jantar:</strong></font>
                        </td>
                        <td align='right' style='padding-right: 8px'>
                          <font color='#990000'><strong>$total_servico_jantar_formata</strong></font>
                        </td>
                        <td>
                          &nbsp;
                        </td>
                      </tr>";
                
            }

            //*** ITENS DO BAILE ***
            //Monta a variável de total do evento
            $total_evento = 0;
            
            ?>
            <tr height="24">
              <td colspan="8" valign="middle" bgcolor="#FFFFCD" class="oddListRowS1" style="padding-left: 6px; border-top:1px solid;border-bottom: 1px solid">
                <span style="font-size: 18px; color: #33485C"><b>BAILE:</b></span>
              </td>
            </tr>
            <tr height="20">
              <td width="60" align='right' class='listViewThS1' style='padding-right: 5px'>Qt Venda</td>
              <td width="20" class='listViewThS1'>&nbsp;</td>
              <td width="60" align='right' class='listViewThS1' style='padding-right: 5px'>Qt Entrega</td>
              <td width="20" class='listViewThS1'>&nbsp;</td>
              <td width='300' class='listViewThS1'>&nbsp;&nbsp;Descrição do Produto/Serviço</td>
              <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Preço Un.</td>
              <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Valor Tot.</td>
              <td class='listViewThS1'>&nbsp;</td>  
            </tr>
            <tr height="20">
              <td colspan="8" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 16px;">
                <span style="font-size: 16px;"><strong>PRODUTOS:</strong></span>
              </td>
            </tr>
            <?php

              //Monta a query para capturar as categorias que existem cadastrados itens
              $sql_produto_baile = mysql_query("SELECT 
                                                ite.id,
                                                ite.categoria_id,											
                                                cat.nome as categoria_nome,
                                                eve.valor_venda
                                                FROM item_evento ite
                                                LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                                                INNER JOIN eventos_item eve ON eve.item_id = ite.id
                                                WHERE eve.evento_id = $EventoId	
                                                AND eve.chk_baile = 1
                                                GROUP BY cat.nome
                                                ORDER BY cat.nome");

              //Conta o numero de compromissos que a query retornou
              $registros_produto_baile = mysql_num_rows($sql_produto_baile);

              if ($registros_produto_baile == 0)
              {

                //Exibe uma linha dizendo que nao há regitros
                echo "
                    <tr height='18'>
                      <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 16px'>
                        <font color='#990000'><strong>Não há produtos cadastrados para o BAILE neste evento</strong></font>
                      </td>
                    </tr>";
                
              }
              
              else
              {

                //Percorre o array das funcoes
                while ($dados_produto_baile = mysql_fetch_array($sql_produto_baile))
                {

                  //Fecha o php para imprimir o texto da categoria
                  ?>
                  <tr height="22">
                    <td height="24" colspan="8" valign="bottom" style="border-bottom: 2px dotted #aaa; padding-left: 14px; padding-bottom: 2px">     				 	 
                      <span style="font-size: 14px"><b>
                        <?php

                          if ($dados_produto_baile["categoria_id"] == 0)
                          {

                            echo "Sem centro de custo definido";

                          }

                          else
                          {

                            echo $dados_produto_baile["categoria_nome"];

                          }

                        ?>
                      </b></span>
                    </td>						 
                  </tr>
                  <?php

                    //Monta a query de filtragem dos itens
                    $filtra_item = "SELECT 
                                    ite.id,
                                    ite.nome,
                                    ite.unidade,											
                                    cat.nome as categoria_nome,
                                    eve.quantidade_alocada,
                                    eve.quantidade,
                                    eve.valor_venda,
                                    eve.observacoes
                                    FROM item_evento ite
                                    LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                                    INNER JOIN eventos_item eve ON eve.item_id = ite.id
                                    WHERE eve.evento_id = $EventoId
                                    AND eve.chk_baile = 1
                                    AND ite.categoria_id = '$dados_produto_baile[categoria_id]'
                                    ORDER BY cat.nome, ite.nome";

                    //Executa a query
                    $lista_item = mysql_query($filtra_item);

                    //Percorre o array
                    while ($dados_item = mysql_fetch_array($lista_item))
                    {

                      //Efetua o switch do campo de unidade de medida
                      switch ($dados_item[unidade])
                      {

                        case "PC": $texto_unidade = "PC - Peça"; break;
                        case "UN": $texto_unidade = "UN - Unidade"; break;
                        case "GR": $texto_unidade = "GR - Grama"; break;
                        case "KG": $texto_unidade = "KG - Kilo"; break;
                        case "LT": $texto_unidade = "LT - Litro"; break;
                        case "PT": $texto_unidade = "PT - Pacote"; break;
                        case "VD": $texto_unidade = "VD - Vidro"; break;
                        case "LT": $texto_unidade = "LT - Lata"; break;
                        case "BD": $texto_unidade = "BD - Balde"; break;
                        case "CX": $texto_unidade = "CX - Caixa"; break;
                        case "GL": $texto_unidade = "GL - Galão"; break;
                        case "MT": $texto_unidade = "MT - Metro"; break;
                        case "M2": $texto_unidade = "M2 - Metro Quadrado"; break;
                        case "M3": $texto_unidade = "M3 - Metro Cúbico"; break;
                        
                      }

                      //Define o botão de exclusão do item
                      $botão_exclui_item = "<img src='image/grid_exclui.gif' alt='Clique para remover este item do evento' width='12' height='12' border='0' onClick=\"if(confirm('Confirma a remoção deste item do evento ?')) {wdCarregarFormulario('ItemEventoExclui.php?ItemId=$dados_item[id]&EventoId=$EventoId','conteudo')}\" style='cursor: pointer'>";

                      //Define a variável do valor total do item
                      $total_item = $dados_item["quantidade"] * $dados_item["valor_venda"];

                      $total_baile = $total_baile + $total_item;

                      //Ajusta o total do evento
                      $total_evento = $total_evento + $total_item;
                      
                      ?>
                      <tr>
                        <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                          <?php echo $dados_item[quantidade] ?>
                        </td>
                        <td valign="middle" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px" bgcolor="#fdfdfd" class="currentTabList">
                          <span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span>
                        </td>	
                        <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                          <?php echo $dados_item[quantidade_alocada] ?>
                        </td>
                        <td valign="middle" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px" bgcolor="#fdfdfd" class="currentTabList">
                          <span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span>
                        </td>
                        <td height="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px; padding-bottom: 2px">
                          <?php

                            echo $dados_item[nome];

                            if ($dados_item[observacoes] != '')
                            {

                              echo "<br/><span class='TextoAzul'>" . nl2br($dados_item[observacoes]) . "</span>";

                            }

                          ?>
                        </td>
                        <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                          <?php echo number_format($dados_item[valor_venda], 2, ",", ".") ?>
                        </td>
                        <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                          <?php echo number_format($total_item, 2, ",", ".") ?>
                        </td>
                        <td valign="middle" style="border-bottom: 1px dotted #aaa; padding-top: 1px">
                          <div align="center">
                            <?php

                              //verifica se o usuário pode excluir o produto
                              if ($dados_usuario["evento_produto_exclui"] == 1)
                              {

                                //Exibe o botão de excluir o item
                                echo $botão_exclui_item;

                              }

                              else
                              {

                                echo '&nbsp;';

                              }

                            ?>            	 
                          </div>
                        </td>
                      </tr>				
                      <?php

                    //Fecha o while dos itens
                    }

                  //Fecha o while das categorias
                  }

                  $total_baile_formata = number_format($total_baile, 2, ",", ".");

                  echo "<tr height='24'>
                          <td colspan='6' valign='middle' align='right' bgcolor='#fdfdfd' class='oddListRowS1' >
                            <font color='#990000'><strong>Total de Itens do Baile:</strong></font>
                          </td>
                          <td align='right' style='padding-right: 8px'>
                            <font color='#990000'><strong>$total_baile_formata</strong></font>
                          </td>
                          <td>
                            &nbsp;
                          </td>
                        </tr>";
                  
                }

              ?>
              <tr height="20">
                <td colspan="8" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 16px;">
                  <span style="font-size: 16px;"><strong>SERVIÇOS:</strong></span>
                </td>
              </tr>
              <?php
              
                //Monta a query para capturar as categorias que existem cadastrados itens
                $sql_servico_baile = mysql_query("SELECT 
                                                  serv.id,
                                                  serv.categoria_id,											
                                                  serv.nome as categoria_nome,
                                                  serv.valor_venda
                                                  FROM servico_evento serv
                                                  LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
                                                  INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
                                                  WHERE eve.evento_id = $EventoId	
                                                  AND eve.chk_baile = 1
                                                  GROUP BY cat.nome
                                                  ORDER BY cat.nome");

                //Conta o numero de compromissos que a query retornou
                $registros_servico_baile = mysql_num_rows($sql_servico_baile);

                if ($registros_servico_baile == 0)
                {

                  //Exibe uma linha dizendo que nao há regitros
                  echo "<tr height='18'>
                          <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 16px'>
                            <font color='#990000'><strong>Não há serviços cadastrados para o BAILE neste evento</strong></font>
                          </td>
                        </tr>";
                  
                }

                else
                {

                  //Percorre o array das funcoes
                  while ($dados_servico_baile = mysql_fetch_array($sql_servico_baile))
                  {

                    //Fecha o php para imprimir o texto da categoria
                    ?>
                    <tr height="22">
                      <td colspan="8" valign="bottom" style="border-bottom: 2px dotted #aaa; padding-left: 14px">    				 	 
                        <span style="font-size: 14px"><b>
                        <?php

                          if ($dados_servico_baile["categoria_id"] == 0)
                          {

                            echo "Sem centro de custo definido";

                          }

                          else
                          {

                            echo $dados_servico_baile["categoria_nome"];

                          }

                        ?>
                        </b></span>
                      </td>						 
                    </tr>
                    <?php

                      //Monta a query de filtragem dos servicos
                      $filtra_servico = "SELECT 
                                        serv.id,
                                        serv.nome,
                                        cat.nome as categoria_nome,
                                        eve.quantidade,
                                        eve.valor_venda,
                                        eve.observacoes
                                        FROM servico_evento serv
                                        LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
                                        INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
                                        WHERE eve.evento_id = $EventoId
                                        AND eve.chk_baile = 1
                                        AND serv.categoria_id = '$dados_servico_baile[categoria_id]'
                                        ORDER BY cat.nome, serv.nome";

                      //Executa a query
                      $lista_servico = mysql_query($filtra_servico);

                      //Percorre o array
                      while ($dados_servico = mysql_fetch_array($lista_servico))
                      {

                        //Define a variável do valor total do servico
                        $total_servico = $dados_servico[quantidade] * $dados_servico[valor_venda];

                        //Ajusta o total do evento
                        $total_servico_baile = $total_servico_baile + $total_servico;
                        
                        ?>
                        <tr valign="middle">
                          <td colspan="4" valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                            <?php echo $dados_servico[quantidade] ?>
                          </td>					 
                          <td height="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px; padding-bottom: 2px">
                            <?php

                              echo $dados_servico[nome];

                              if ($dados_servico[observacoes] != '')
                              {

                                echo "<br/><span class='TextoAzul'>" . nl2br($dados_servico[observacoes]) . "</span>";

                              }

                            ?>
                          </td>
                          <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                            <?php echo number_format($dados_servico[valor_venda], 2, ",", ".") ?>
                          </td>
                          <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                            <?php echo number_format($total_servico, 2, ",", ".") ?>
                          </td>
                          <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                            <div align="center">
                              &nbsp;            	 
                            </div>
                          </td>
                        </tr>
                        <?php

                      }

                    }

                    $total_servico_baile_formata = number_format($total_servico_baile, 2, ",", ".");

                    echo "<tr height='24'>
                            <td colspan='6' valign='middle' align='right' bgcolor='#fdfdfd' class='oddListRowS1' >
                              <font color='#990000'><strong>Total de Serviços do Baile:</strong></font>
                            </td>
                            <td align='right' style='padding-right: 8px'>
                              <font color='#990000'><strong>$total_servico_baile_formata</strong></font>
                            </td>
                            <td>
                              &nbsp;
                            </td>
                          </tr>";
                }

                //*** ITENS AVULSOS ***
                //Monta a variável de total do evento
                $total_evento = 0;
                
                ?>
                <tr height="24">
                  <td colspan="8" valign="middle" bgcolor="#FFFFCD" class="oddListRowS1" style="padding-left: 6px; border-top:1px solid;border-bottom: 1px solid">
                    <span style="font-size: 18px; color: #33485C"><b>ITENS AVULSOS:</b></span>
                  </td>
                </tr>
                <tr height="20">
                  <td width="60" align='right' class='listViewThS1' style='padding-right: 5px'>Qt Venda</td>
                  <td width="20" class='listViewThS1'>&nbsp;</td>
                  <td width="60" align='right' class='listViewThS1' style='padding-right: 5px'>Qt Entrega</td>
                  <td width="20" class='listViewThS1'>&nbsp;</td>
                  <td width='300' class='listViewThS1'>&nbsp;&nbsp;Descrição do Produto/Serviço</td>
                  <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Preço Un.</td>
                  <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Valor Tot.</td>
                  <td class='listViewThS1'>&nbsp;</td> 
                </tr>
                <tr height="20">
                  <td colspan="8" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 16px;">
                    <span style="font-size: 16px;"><strong>PRODUTOS:</strong></span>
                  </td>
                </tr>
                <?php

                  //Monta a query para capturar as categorias que existem cadastrados itens
                  $sql_produto_avulso = mysql_query("SELECT 
                                                    ite.id,
                                                    ite.categoria_id,											
                                                    cat.nome as categoria_nome,
                                                    eve.valor_venda
                                                    FROM item_evento ite
                                                    LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                                                    INNER JOIN eventos_item eve ON eve.item_id = ite.id
                                                    WHERE eve.evento_id = $EventoId	
                                                    AND eve.chk_culto = 0
                                                    AND eve.chk_jantar = 0
                                                    AND eve.chk_colacao = 0
                                                    AND eve.chk_baile = 0
                                                    GROUP BY cat.nome
                                                    ORDER BY cat.nome");

                  //Conta o numero de compromissos que a query retornou
                  $registros_produto_avulso = mysql_num_rows($sql_produto_avulso);

                  if ($registros_produto_avulso == 0)
                  {

                    //Exibe uma linha dizendo que nao há regitros
                    echo "<tr height='18'>
                            <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 16px'>
                              <font color='#990000'><strong>Não há produtos cadastrados AVULSO deste evento</strong></font>
                            </td>
                          </tr>";
                    
                  }

                  else
                  {

                    //Percorre o array das funcoes
                    while ($dados_produto_avulso = mysql_fetch_array($sql_produto_avulso))
                    {

                      //Fecha o php para imprimir o texto da categoria
                      ?>
                      <tr>
                        <td height="24" colspan="8" valign="bottom" style="border-bottom: 2px dotted #aaa; padding-left: 14px; padding-bottom: 2px">    				 	 
                          <span style="font-size: 14px"><b>
                          <?php

                            if ($dados_produto_avulso["categoria_id"] == 0)
                            {

                              echo "Sem centro de custo definido";

                            }

                            else
                            {

                              echo $dados_produto_avulso["categoria_nome"];

                            }

                          ?>
                          </b></span>
                        </td>						 
                      </tr>
                      <?php
                                      
                        //Monta a query de filtragem dos itens
                        $filtra_item = "SELECT 
                                        ite.id,
                                        ite.nome,
                                        ite.unidade,											
                                        cat.nome as categoria_nome,
                                        eve.quantidade_alocada,
                                        eve.quantidade,
                                        eve.valor_venda,
                                        eve.observacoes
                                        FROM item_evento ite
                                        LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                                        INNER JOIN eventos_item eve ON eve.item_id = ite.id
                                        WHERE eve.evento_id = $EventoId
                                        AND eve.chk_culto = 0
                                        AND eve.chk_jantar = 0
                                        AND eve.chk_colacao = 0
                                        AND eve.chk_baile = 0
                                        AND ite.categoria_id = '$dados_produto_avulso[categoria_id]'
                                        ORDER BY cat.nome, ite.nome";

                        //Executa a query
                        $lista_item = mysql_query($filtra_item);

                        //Percorre o array
                        while ($dados_item = mysql_fetch_array($lista_item))
                        {

                          //Efetua o switch do campo de unidade de medida
                          switch ($dados_item[unidade])
                          {

                            case "PC": $texto_unidade = "PC - Peça"; break;
                            case "UN": $texto_unidade = "UN - Unidade"; break;
                            case "GR": $texto_unidade = "GR - Grama"; break;
                            case "KG": $texto_unidade = "KG - Kilo"; break;
                            case "LT": $texto_unidade = "LT - Litro"; break;
                            case "PT": $texto_unidade = "PT - Pacote"; break;
                            case "VD": $texto_unidade = "VD - Vidro"; break;
                            case "LT": $texto_unidade = "LT - Lata"; break;
                            case "BD": $texto_unidade = "BD - Balde"; break;
                            case "CX": $texto_unidade = "CX - Caixa"; break;
                            case "GL": $texto_unidade = "GL - Galão"; break;
                            case "MT": $texto_unidade = "MT - Metro"; break;
                            case "M2": $texto_unidade = "M2 - Metro Quadrado"; break;
                            case "M3": $texto_unidade = "M3 - Metro Cúbico"; break;
                            
                          }

                          //Define o botão de exclusão do item
                          $botão_exclui_item = "<img src='image/grid_exclui.gif' alt='Clique para remover este item do evento' width='12' height='12' border='0' onClick=\"if(confirm('Confirma a remoção deste item do evento ?')) {wdCarregarFormulario('ItemEventoExclui.php?ItemId=$dados_item[id]&EventoId=$EventoId','conteudo')}\" style='cursor: pointer'>";

                          //Define a variável do valor total do item
                          $total_item = $dados_item["quantidade"] * $dados_item["valor_venda"];

                          $total_avulso = $total_avulso + $total_item;

                          //Ajusta o total do evento
                          $total_evento = $total_evento + $total_item;
                          
                        ?>
                        <tr>
                          <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                            <?php echo $dados_item[quantidade] ?>
                          </td>
                          <td valign="middle" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px" bgcolor="#fdfdfd" class="currentTabList">
                            <span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span>
                          </td>	
                          <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                            <?php echo $dados_item[quantidade_alocada] ?>
                          </td>
                          <td valign="middle" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px" bgcolor="#fdfdfd" class="currentTabList">
                            <span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span>
                          </td>
                          <td height="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px; padding-bottom: 2px">
                            <?php
                            
                              echo $dados_item[nome];

                              if ($dados_item[observacoes] != '')
                              {

                                echo "<br/><span class='TextoAzul'>" . nl2br($dados_item[observacoes]) . "</span>";
                                
                              }
                              
                            ?>
                          </td>
                          <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                            <?php echo number_format($dados_item[valor_venda], 2, ",", ".") ?>
                          </td>
                          <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                            <?php echo number_format($total_item, 2, ",", ".") ?>
                          </td>
                          <td valign="middle" style="border-bottom: 1px dotted #aaa; padding-top: 1px">
                            <div align="center">
                              <?php
                              
                                //verifica se o usuário pode excluir o produto
                                if ($dados_usuario["evento_produto_exclui"] == 1)
                                {

                                  //Exibe o botão de excluir o item
                                  echo $botão_exclui_item;
                                  
                                }
                                
                                else
                                {

                                  echo '&nbsp;';
                                  
                                }
                                
                              ?>            	 
                            </div>
                          </td>
                        </tr>				
                        <?php

                      //Fecha o while dos itens
                      }

                    //Fecha o while das categorias
                    }

                    $total_avulso_formata = number_format($total_avulso, 2, ",", ".");

                    echo "<tr height='24'>
                            <td colspan='6' valign='middle' align='right' bgcolor='#fdfdfd' class='oddListRowS1' >
                              <font color='#990000'><strong>Total de Itens Avulsos:</strong></font>
                            </td>
                            <td align='right' style='padding-right: 8px'>
                              <font color='#990000'><strong>$total_avulso_formata</strong></font>
                            </td>
                            <td>
                              &nbsp;
                            </td>
                          </tr>";
                    
                  }
                  
                  ?>
                  <tr height="20">
                    <td colspan="8" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 16px;">
                      <span style="font-size: 16px;"><strong>SERVIÇOS:</strong></span>
                    </td>
                  </tr>
                  <?php

                    //Monta a query para capturar as categorias que existem cadastrados itens
                    $sql_servico_avulso = mysql_query("SELECT 
                                                      serv.id,
                                                      serv.categoria_id,											
                                                      serv.nome as categoria_nome,
                                                      serv.valor_venda
                                                      FROM servico_evento serv
                                                      LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
                                                      INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
                                                      WHERE eve.evento_id = $EventoId	
                                                      AND eve.chk_culto = 0
                                                      AND eve.chk_jantar = 0
                                                      AND eve.chk_colacao = 0
                                                      AND eve.chk_baile = 0
                                                      GROUP BY cat.nome
                                                      ORDER BY cat.nome");

                    //Conta o numero de compromissos que a query retornou
                    $registros_servico_avulso = mysql_num_rows($sql_servico_avulso);

                    if ($registros_servico_avulso == 0)
                    {

                      //Exibe uma linha dizendo que nao há regitros
                      echo "<tr height='18'>
                              <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 16px'>
                                <font color='#990000'><strong>Não há serviços cadastrados AVULSO neste evento</strong></font>
                              </td>
                            </tr>";
                      
                    }

                    else
                    {

                      //Percorre o array das funcoes
                      while ($dados_servico_avulso = mysql_fetch_array($sql_servico_avulso))
                      {

                        //Fecha o php para imprimir o texto da categoria
                        ?>
                        <tr height="22">
                          <td colspan="8" valign="bottom" style="border-bottom: 2px dotted #aaa; padding-left: 14px">    				 	 
                            <span style="font-size: 14px"><b>
                            <?php

                              if ($dados_servico_avulso["categoria_id"] == 0)
                              {

                                echo "Sem centro de custo definido";

                              }

                              else
                              {

                                echo $dados_servico_avulso["categoria_nome"];

                              }

                            ?>
                            </b></span>
                          </td>						 
                        </tr>
                        <?php

                          //Monta a query de filtragem dos servicos
                          $filtra_servico = "SELECT 
                                            serv.id,
                                            serv.nome,
                                            cat.nome as categoria_nome,
                                            eve.quantidade,
                                            eve.valor_venda,
                                            eve.observacoes
                                            FROM servico_evento serv
                                            LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
                                            INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
                                            WHERE eve.evento_id = $EventoId
                                            AND eve.chk_culto = 0
                                            AND eve.chk_jantar = 0
                                            AND eve.chk_colacao = 0
                                            AND eve.chk_baile = 0
                                            AND serv.categoria_id = '$dados_servico_avulso[categoria_id]'
                                            ORDER BY cat.nome, serv.nome";

                          //Executa a query
                          $lista_servico = mysql_query($filtra_servico);

                          //Percorre o array
                          while ($dados_servico = mysql_fetch_array($lista_servico))
                          {

                            //Define a variável do valor total do servico
                            $total_servico = $dados_servico[quantidade] * $dados_servico[valor_venda];

                            //Ajusta o total do evento
                            $total_servico_avulso = $total_servico_avulso + $total_servico;
                            $total_geral_servico = $total_geral_servico + $total_servico;
                            
                            ?>
                            <tr valign="middle">
                              <td colspan="4" valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted #aaa; padding-right: 5px; padding-top: 1px">
                                <?php echo $dados_servico[quantidade] ?>
                              </td>					 
                              <td height="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-top: 1px; padding-bottom: 2px">
                                <?php

                                  echo $dados_servico[nome];

                                  if ($dados_servico[observacoes] != '')
                                  {

                                    echo "<br/><span class='TextoAzul'>" . nl2br($dados_servico[observacoes]) . "</span>";
                                    
                                  }
                                  
                                ?>
                              </td>
                              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                                <?php echo number_format($dados_servico[valor_venda], 2, ",", ".") ?>
                              </td>
                              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                                <?php echo number_format($total_servico, 2, ",", ".") ?>
                              </td>
                              <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-right: 1px dotted #aaa; border-bottom: 1px dotted #aaa; padding-right: 8px">
                                <div align="center">
                                  &nbsp;            	 
                                </div>
                              </td>
                            </tr>
                            <?php

                          }

                        }

                        $total_servico_avulso_formata = number_format($total_servico_avulso, 2, ",", ".");

                        echo "<tr height='24'>
                                <td colspan='6' valign='middle' align='right' bgcolor='#fdfdfd' class='oddListRowS1' >
                                  <font color='#990000'><strong>Total de Serviços Avulsos:</strong></font>
                                </td>
                                <td align='right' style='padding-right: 8px'>
                                  <font color='#990000'><strong>$total_servico_avulso_formata</strong></font>
                                </td>
                                <td>
                                  &nbsp;
                                </td>
                              </tr>";
                        
                    }

                    ?>
                    </table>
                    <table width="340" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                      <tr>
                        <td width="120" height="26" style="padding-left: 5px">
                          <span style="font-size: 12px"><b>PRODUTOS:</b>
                          </span>
                        </td>
                        <td width="100">
                          <span style="font-size: 12px">
                            Culto:
                          </span>
                        </td>
                        <td style="padding-right: 5px" align="right">
                          <span style="font-size: 12px">
                            <?php echo number_format($total_culto, 2, ",", "."); ?>
                          </span>
                        </td>
                      </tr>
                      <tr>
                        <td width="120" height="22">
                          <span style="font-size: 12px">
                            &nbsp;
                          </span>
                        </td>
                        <td width="100">
                          <span style="font-size: 12px">
                            Colação:
                          </span>
                        </td>
                        <td style="padding-right: 5px" align="right">
                          <span style="font-size: 12px">
                            <?php echo number_format($total_colacao, 2, ",", "."); ?>
                          </span>
                        </td>
                      </tr>
                      <tr>
                        <td width="120" height="22">
                          <span style="font-size: 12px">
                            &nbsp;
                          </span>
                        </td>
                        <td width="100">
                          <span style="font-size: 12px">
                            Jantar:
                          </span>
                        </td>
                        <td style="padding-right: 5px" align="right">
                          <span style="font-size: 12px">
                            <?php echo number_format($total_jantar, 2, ",", "."); ?>
                          </span>
                        </td>
                      </tr>
                      <tr>
                        <td width="120" height="22">
                          <span style="font-size: 12px">
                            &nbsp;
                          </span>
                        </td>
                        <td width="100">
                          <span style="font-size: 12px">
                            Baile:
                          </span>
                        </td>
                        <td style="padding-right: 5px" align="right">
                          <span style="font-size: 12px">
                            <?php echo number_format($total_baile, 2, ",", "."); ?>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td width="120" height="22">
                            <span style="font-size: 12px">
                              &nbsp;
                            </span>
                          </td>
                          <td width="100">
                            <span style="font-size: 12px">
                              Avulso:
                            </span>
                          </td>
                          <td style="padding-right: 5px" align="right">
                            <span style="font-size: 12px" >
                              <?php echo number_format($total_avulso, 2, ",", "."); ?>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="22" style="padding-left: 5px; border-bottom: 1px #aaa solid" align="right">
                            <span style="font-size: 12px" >
                              <b>TOTAL GERAL DE PRODUTOS:</b>
                            </span>
                          </td>
                          <td style="padding-right: 5px; border-bottom: 1px #aaa solid" align="right">
                            <span style="font-size: 12px"><b>
                              <?php
                              
                                $total_geral_produtos = $total_culto + $total_jantar + $total_colacao + $total_baile + $total_avulso;

                                echo number_format($total_geral_produtos, 2, ",", ".");

                              ?>
                              </b></span>
                          </td>
                        </tr>
                        <tr>
                          <td width="120" height="22" style="padding-left: 5px">
                            <span style="font-size: 12px">
                              <b>SERVIÇOS:</b>
                            </span>
                          </td>
                          <td width="100">
                            <span style="font-size: 12px">
                              Culto:
                            </span>
                          </td>
                          <td style="padding-right: 5px" align="right">
                            <span style="font-size: 12px">
                              <?php echo number_format($total_servico_culto, 2, ",", "."); ?>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td width="120" height="22">
                            <span style="font-size: 12px">
                              &nbsp;
                            </span>
                          </td>
                          <td width="100">
                            <span style="font-size: 12px">
                              Colação:
                            </span>
                          </td>
                            <td style="padding-right: 5px" align="right">
                              <span style="font-size: 12px">
                                <?php echo number_format($total_servico_colacao, 2, ",", "."); ?>
                              </span>
                            </td>
                          </tr>
                          <tr>
                          <td width="120" height="22">
                            <span style="font-size: 12px">
                              &nbsp;
                            </span>
                          </td>
                          <td width="100">
                            <span style="font-size: 12px">
                              Jantar:
                            </span>
                          </td>
                          <td style="padding-right: 5px" align="right">
                            <span style="font-size: 12px">
                                <?php echo number_format($total_servico_jantar, 2, ",", "."); ?>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td width="120" height="22">
                            <span style="font-size: 12px">
                              &nbsp;
                            </span>
                          </td>
                          <td width="100">
                            <span style="font-size: 12px">
                              Baile:
                            </span>
                          </td>
                          <td style="padding-right: 5px" align="right">
                            <span style="font-size: 12px">
                              <?php echo number_format($total_servico_baile, 2, ",", "."); ?>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td width="120" height="22">
                            <span style="font-size: 12px">
                              &nbsp;
                            </span>
                          </td>
                          <td width="100">
                            <span style="font-size: 12px">
                              Avulso:
                            </span>
                          </td>
                          <td style="padding-right: 5px" align="right">
                            <span style="font-size: 12px">
                              <?php echo number_format($total_servico_avulso, 2, ",", "."); ?>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="26" align="right">
                            <span style="font-size: 12px">
                              <b>TOTAL GERAL DE SERVIÇOS:</b>
                            </span>
                          </td>
                          <td style="padding-right: 5px" align="right">
                            <span style="font-size: 12px"><b>
                              <?php
                              
                                $total_geral_servicos = $total_servico_culto + $total_servico_jantar + $total_servico_colacao + $total_servico_baile + $total_servico_avulso;

                                echo number_format($total_geral_servicos, 2, ",", ".");

                              ?>
                            </b></span>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="22" style="border-top: 1px #aaa solid" align="right">
                            <span style="font-size: 12px; color: #990000"><b>DESCONTOS:</b></span>
                          </td>
                          <td style="padding-right: 5px; border-top: 1px #aaa solid" align="right">
                            <span style="font-size: 12px; color: #990000"><b>
                              <?php
                              
                                echo number_format($dados_evento['valor_desconto_evento'], 2, ",", ".");

                              ?>
                            </b></span>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="22" align="right">
                            <span style="font-size: 12px"><b>TOTAL GERAL DO EVENTO:</b></span>
                          </td>
                          <td style="padding-right: 5px;" align="right">
                            <span style="font-size: 12px"><b>
                              <?php
                                      
                                $total_geral_evento = ($total_geral_produtos + $total_geral_servicos) - $dados_evento['valor_desconto_evento'];
                                $total_geral_evento_totaliza = $total_geral_evento;
                                echo number_format($total_geral_evento, 2, ",", ".");
                                
                              ?>
                            </b></span>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="22" style="border-top: 1px #aaa solid" align="right">
                            <span style="font-size: 12px"><b>ALUNOS COLAÇÃO - INICIAL:</b></span>
                          </td>
                          <td style="padding-right: 5px; border-top: 1px #aaa solid" align="right">
                            <span style="font-size: 12px"><b>
                              <?php echo $dados_evento[alunos_colacao] ?>
                            </b></span>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="22" align="right">
                            <span style="font-size: 12px"><b>ALUNOS COLACAO - ATUAL:</b></span>
                          </td>
                          <td style="padding-right: 5px;" align="right">
                            <span style="font-size: 12px"><b>
                            <?php echo $total_alunos_colacao ?>
                            </b></span>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="22" align="right">
                            <span style="font-size: 12px"><b>ALUNOS BAILE - INICIAL:</b></span>
                          </td>
                          <td style="padding-right: 5px;" align="right">
                            <span style="font-size: 12px"><b>
                              <?php echo $dados_evento[alunos_baile] ?>
                            </b></span>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="22" align="right">
                            <span style="font-size: 12px"><b>ALUNOS BAILE - ATUAL:</b></span>
                          </td>
                          <td style="padding-right: 5px;" align="right">
                            <span style="font-size: 12px"><b>
                            <?php echo $total_alunos_baile ?>
                            </b></span>
                          </td>
                        </tr>
                      </table>
                    </div>
                    <?php

                  //Fecha o if de se deve exibir o módulo de produtos
                  }

                ?> 
              </td>
            </tr>
            <?php
              
              if ($usuarioNome == "Maycon" OR $usuarioNome == "Joni" OR $usuarioNome == "Josiane" OR $usuarioNome == "Zulaine")
              {  
                
                ?>
                <tr>
                  <td>
                    <br/>
                    <table class="listView" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom: 0px;">
                      <tr height="40">
                        <td width="30" valign="middle" style="padding-left: 5px"><img src="image/bt_terceiro_gd.gif"/></td>    
                        <td><span class="TituloModulo">Fechamento Financeiro</span></td>
                      </tr>
                    </table>
                  <?php

                  //Monta o sql de filtragem das contas
                  $sql = "SELECT 
                          rec.id,			
                          rec.pessoa_id,			
                          eve.nome AS evento_nome,
                          form.nome AS formando_nome,
                          form.chk_culto,
                          form.chk_colacao,
                          form.chk_jantar,
                          form.chk_baile
                          FROM contas_receber rec
                          LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
                          LEFT OUTER JOIN eventos_formando form ON form.id = rec.pessoa_id
                          WHERE rec.empresa_id = $empresaId 
                          AND rec.evento_id = $EventoId
                          AND rec.formando_id > 0
                          GROUP BY formando_nome
                          ORDER BY formando_nome			
                          ";   

                  $query = mysql_query($sql);

                  $registros = mysql_num_rows($query);


                  //Caso não encontrar contas
                  if ($registros == 0)
                  {

                    ?>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td>
                          Nenhuma conta a receber encontrada !
                        </td>
                      </tr>
                    </table>
                    <?php

                  }

                  else

                  {

                    ?>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">	
                      <tr>
                        <td>
                          <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
                            <tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">                
                              <td style="padding-left: 4px">Dados do Sacado/Evento/Formando</td>
                              <td width="36" align="center" style="border-left: #aaa 1px dotted">Part.</td>
                              <td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Vl Contrato</td>
                              <td width="36" align="center" style="border-left: #aaa 1px dotted">Parc.</td>
                              <td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Vl Parcela</td>
                              <td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Vl Recebido</td>
                              <td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">A Receber</td>
                              <td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Em Atraso</td>          
                            </tr>
                            <?php

                              //Percorre as contas
                              while ($dados = mysql_fetch_array($query))
                              {

                                $desc_participante = "&nbsp;";

                                if ($dados["chk_culto"] == 1)
                                {

                                  $desc_participante .= "<span title='Formando Participa do Culto'>M</span>&nbsp;";

                                }

                                if ($dados["chk_colacao"] == 1)
                                {

                                  $desc_participante .= "<span title='Formando Participa da Colação'>C</span>&nbsp;";

                                }

                                if ($dados["chk_jantar"] == 1)
                                {

                                  $desc_participante .= "<span title='Formando Participa do Jantar'>J</span>&nbsp;";

                                }

                                if ($dados["chk_baile"] == 1)
                                {

                                  $desc_participante .= "<span title='Formando Participa do Baile'>B</span>";

                                }	

                                $FormandoId = $dados["pessoa_id"];

                                //Pega o numero de parcelas e o valor
                                //Monta o sql de filtragem das contas
                                $sql = "SELECT 
                                        count(1) AS total_parcelas,
                                        valor_original AS valor_parcela
                                        FROM contas_receber rec
                                        WHERE empresa_id = $empresaId AND pessoa_id = $FormandoId
                                        GROUP BY pessoa_id";   

                                $query_parcela = mysql_query($sql);

                                $registros_parcela = mysql_num_rows($query_parcela);

                                //Verifica se possuem registros
                                if ($registros_parcela > 0)
                                {

                                  //Percorre as contas
                                  while ($dados_parcela = mysql_fetch_array($query_parcela))
                                  {

                                    $numero_parcelas = $dados_parcela["total_parcelas"];
                                    $valor_parcela = $dados_parcela["valor_parcela"];

                                  }

                                }

                                $hoje = date('Y-m-d', mktime());

                                $TextoSituacao = " ";

                                //Pega o numero contas atrasadas
                                //Monta o sql de filtragem das contas
                                $sql = "SELECT 
                                        SUM(valor_original) AS total_atraso								
                                        FROM contas_receber rec
                                        WHERE empresa_id = $empresaId 
                                        AND pessoa_id = $FormandoId
                                        AND situacao = 1 
                                        AND data_vencimento < '$hoje'";   


                                $query_atraso = mysql_query($sql);

                                $registros_atraso = mysql_num_rows($query_atraso);

                                //Verifica se possuem registros
                                if ($registros_atraso > 0)
                                {

                                  //Percorre as contas
                                  while ($dados_atraso = mysql_fetch_array($query_atraso))
                                  {

                                    $valor_atraso = $dados_atraso["total_atraso"];

                                    $geral_atraso = $geral_atraso + $dados_atraso["total_atraso"];

                                  }

                                }

                                //Monta o sql de filtragem das contas
                                $sql = "SELECT 
                                        rec.id,
                                        SUM(rec.valor_original) AS total_valor_original,
                                        SUM(rec.valor) AS total_valor,
                                        SUM(rec.valor_recebido) AS total_recebido,
                                        SUM(rec.valor_boleto) AS total_boleto,
                                        SUM(rec.valor_multa_juros) AS total_multa,								
                                        rec.pessoa_id								
                                        FROM contas_receber rec
                                        WHERE rec.empresa_id = $empresaId 
                                        AND rec.pessoa_id = $FormandoId
                                        GROUP BY rec.pessoa_id";   

                                $query_formando = mysql_query($sql);

                                $registros_formando = mysql_num_rows($query_formando);

                                //Verifica se possuem registros
                                if ($registros_formando > 0)
                                {

                                  //Percorre as contas
                                  while ($dados_formando = mysql_fetch_array($query_formando))
                                  {

                                    $geral_contrato = $geral_contrato + $dados_formando["total_valor_original"];
                                    $geral_recebido = $geral_recebido + $dados_formando["total_recebido"];
                                    $geral_receber = $geral_receber + ($dados_formando["total_valor"] - $dados_formando["total_recebido"]);

                                    $total_receber = $dados_formando["total_valor"] - $dados_formando["total_recebido"];

                                    ?>
                                    <tr height="22">                
                                      <td style="padding-left: 4px; border-bottom: #aaa 1px dotted"><b><?php echo $dados["formando_nome"] ?></b></td>
                                      <td align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted"><?php echo $desc_participante ?></td>
                                      <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($dados_formando["total_valor_original"],2,",",".") ?></td>
                                      <td align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted"><?php echo $numero_parcelas ?></td>
                                      <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($valor_parcela,2,",",".") ?></td>
                                      <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($dados_formando["total_recebido"],2,",",".") ?></td>
                                      <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($total_receber,2,",",".") ?></td>
                                      <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($valor_atraso,2,",",".") ?></td>								
                                    </tr>
                                    <?php

                                  }

                                }					

                              }

                            ?>
                            <tr height="22">                
                              <td colspan="2" align="right" style="padding-left: 4px; border-bottom: #aaa 1px dotted"><b>TOTAL:&nbsp;</b></td>
                              <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_contrato,2,",",".") ?></b></td>
                              <td align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted">&nbsp;</td>
                              <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted">&nbsp;</td>
                              <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_recebido,2,",",".") ?></b></td>
                              <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_receber,2,",",".") ?></b></td>
                              <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_atraso,2,",",".") ?></b></td>						
                            </tr>			
                          </table>
                        </td>
                      </tr>
                    </table>
                    <br/>
                    <table width="350" border="0" cellpadding="0" cellspacing="0" class="listView">	
                      <tr>
                        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px; border-bottom: 1px solid">
                          Valor Orçado R$: 
                        </td>
                        <td style="border-left: 1px solid; border-bottom: 1px solid; padding-right: 8px" align="right">
                          <b><?php echo number_format($dados_evento["valor_geral_evento"], 2, ",", ".") ?></b>
                        </td>
                      </tr>
                      <tr>
                        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px; border-bottom: 1px solid">
                          Valor Orçamento Atual R$: 
                        </td>
                        <td style="border-left: 1px solid; border-bottom: 1px solid; padding-right: 8px" align="right">
                          <b><?php echo number_format($total_geral_evento_totaliza, 2, ",", ".") ?></b>
                        </td>
                      </tr>
                      <tr>
                        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px;">
                          Valor em Contratos Individuais R$: 
                        </td>
                        <td style="border-left: 1px solid; border-bottom: 1px solid; padding-right: 8px" align="right">
                          <b><?php echo number_format($geral_contrato, 2, ",", ".") ?></b>
                        </td>
                      </tr>
                      <tr>
                        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px;">
                          <?php

                            $diferenca_total = $geral_contrato - $total_geral_evento_totaliza;
                            @$diferenca_percentual = ($diferenca_total / $total_geral_evento_totaliza) * 100;

                          ?>
                          Diferença R$: 
                        </td>
                        <td style="border-left: 1px solid; padding-right: 8px" align="right">
                          <span style="color: #990000"><b><?php echo number_format($diferenca_total, 2, ",", ".") . ' (' .  number_format($diferenca_percentual, 0, ",", ".") . '%)' ?></b></span>
                        </td>
                      </tr>
                    </table>
                    <?php

                  }
                  
                  ?>
                  </td>
                </tr>
                <?php
                
              }
            
            ?>
            <tr>
              <td>
                <?php

                  //verifica se o usuário pode ver os terceiros
                  if ($dados_usuario["evento_terceiro_exibe"] == 1)
                  {

                    ?>
                    <br/>
                    <?php /* EXIBE OS TERCEIROS CADASTRADOS PARA ESTE EVENTO */ ?>
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: #9e9e9e 1px solid">
                      <tr>
                        <td height="30">
                          <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td width="30" valign="middle" style="padding-left: 5px"><img src="image/bt_terceiro_gd.gif"/></td>    
                              <td width="190"><span class="TituloModulo">Terceiros do Evento</span></td>
                              <td>
                                <?php

                                  //verifica se o usuário pode incluir terceiros
                                  if ($dados_usuario["evento_terceiro_inclui"] == 1)
                                  {

                                    ?>
                                    [<a title="Clique para gerenciar os terceiros deste evento" href="#" onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Gerenciar Terceiros</a>]
                                    <?php
                                    
                                  }
                                  
                                  else
                                  {

                                    echo "&nbsp;";
                                    
                                  }
                                  
                                ?>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                    <?php

                      //verifica todos os terceiros cadastrados na base para montar o primeiro array (para comparar com os que estão inclusos no evento
                      //Monta a query de filtragem dos terceiros
                      $filtra_terceiro = "SELECT
                                          evento_id														 
                                          FROM eventos_terceiro
                                          WHERE evento_id = $EventoId";

                      //Executa a query
                      $lista_terceiro = mysql_query($filtra_terceiro);

                      //Cria um contador com o número de contar que a query retornou
                      $registros = mysql_num_rows($lista_terceiro);
                      
                    ?>
                    <div id="75">   
                      <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                        <?php

                          if ($registros > 0)
                          {

                            echo "<tr height='20'>
                                    <td width='370' class='listViewThS1'>&nbsp;&nbsp;Terceiro/Fornecedor</td>
                                    <td class='listViewThS1'>Serviço Contratado</td>
                                    <td width='70' align='right' class='listViewThS1' style='padding-right: 5px'>Custo</td>
                                    <td width='70' align='right' class='listViewThS1' style='padding-right: 5px'>Venda</td>
                                    <td class='listViewThS1'>&nbsp;</td>
                                  </tr>";
                            
                          }

                          if ($registros == 0)
                          {

                            //Exibe uma linha dizendo que nao há registros
                            echo "<tr height='24'>
                                    <td colspan='5' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' >
                                      <font color='#33485C'><b>Não há terceiros cadastrados para este evento</b></font>
                                    </td>
                                  </tr>";
                          }
                          
                          //Monta a variável de total do evento
                          $total_terceiros = 0;

                          //Monta a query de filtragem dos servicos
                          $filtra_terceiro = "SELECT
                                              ter.id,
                                              ter.fornecedor_id,
                                              ter.servico_contratado,
                                              ter.custo,
                                              ter.valor_venda,
                                              ter.observacoes,														 
                                              ter.status_contrato,
                                              forn.nome as fornecedor_nome
                                              FROM eventos_terceiro ter
                                              LEFT OUTER JOIN fornecedores forn ON forn.id = ter.fornecedor_id
                                              WHERE ter.evento_id = $EventoId
                                              ORDER by ter.status_contrato, forn.nome";

                          //Executa a query
                          $lista_terceiro = mysql_query($filtra_terceiro);

                          echo "<tr height='18' valign='middle'>
                                  <td colspan='5'>
                                    <span style='font-size: 14px; color: blue'><b>&nbsp;A Contratar:</b></span>
                                  </td>				
                                </tr>";

                          $valor_quebra = 1;

                          //Percorre o array
                          while ($dados_terceiro = mysql_fetch_array($lista_terceiro))
                          {

                            if ($dados_terceiro[status_contrato] != $valor_quebra)
                            {

                              if ($dados_terceiro[status_contrato] == 2)
                              {

                                echo "<tr height='18' valign='middle'>
                                        <td colspan='5' style='padding-top: 8px'>
                                          <span style='font-size: 14px; color: red'><b>&nbsp;Contratado:</b></span>
                                        </td>				
                                      </tr>";

                                $valor_quebra = 2;

                              }

                              else if ($dados_terceiro[status_contrato] == 3)
                              {

                                echo "<tr height='18' valign='middle'>
                                        <td colspan='5' style='padding-top: 8px'>
                                          <span style='font-size: 14px'><b>&nbsp;Cancelado:</b></span>
                                        </td>				
                                      </tr>";

                                $valor_quebra = 3;

                              }

                            }

                            //Define o botão de exclusão do terceiro
                            $botão_exclui_terceiro = "";

                            //Ajusta o total do evento
                            $total_custo_terceiros = $total_custo_terceiros + $dados_terceiro[custo];
                            $total_venda_terceiros = $total_venda_terceiros + $dados_terceiro[valor_venda];
                            
                          ?>
                          <tr valign="middle">					 
                            <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-top: 1px; padding-bottom: 2px; padding-left: 15px">
                              <?php

                                //verifica se o usuário pode alterar os terceiros
                                if ($dados_usuario["evento_terceiro_altera"] == 1)
                                {

                                  ?>
                                  <font color="#CC3300" size="2" face="Tahoma"><a title="Clique para alterar os dados deste terceiro" href="javascript: void(0);" onclick="wdCarregarFormulario('TerceiroEventoAltera.php?EventoId=<?php echo $EventoId ?>&Id=<?php echo $dados_terceiro[id] ?>&headers=1','conteudo')"><?php echo $dados_terceiro['fornecedor_nome']; ?></a></font>
                                  <?php

                                }

                                else
                                {

                                  ?>
                                  <font size="2" face="Tahoma" color="000000"><b><?php echo $dados_terceiro['fornecedor_nome']; ?></font>
                                  <?php

                                }

                              ?>
                            </td>
                            <td valign="middle" bgcolor="#fdfdfd" style="padding-top: 1px; padding-bottom: 2px">
                              <?php echo $dados_terceiro[servico_contratado] ?>
                            </td>
                            <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 8px">
                              <?php echo "R$ " . number_format($dados_terceiro[custo], 2, ",", ".") ?>
                            </td>	
                            <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 8px">
                              <?php echo "R$ " . number_format($dados_terceiro[valor_venda], 2, ",", ".") ?>
                            </td>					 
                            <td valign="middle" style="padding-right: 6px">
                              <div align="center">
                                <?php

                                  //verifica se o usuário pode excluir os terceiros
                                  if ($dados_usuario["evento_terceiro_exclui"] == 1)
                                  {

                                    ?>
                                    <img src="image/grid_exclui.gif" alt="Clique para remover este terceiro do evento" width="12" height="12" border="0" onclick="if(confirm('Confirma a remoção deste terceiro do evento ?')) {wdCarregarFormulario('TerceiroEventoExclui.php?TerceiroId=<?php echo $dados_terceiro[id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer" />
                                    <?php

                                  }

                                  else
                                  {

                                    echo "&nbsp;";

                                  }

                                ?>
                              </div>
                            </td>
                          </tr>	
                          <?php

                          //Verifica se a variável de observações contem algum valor
                          if ($dados_terceiro[observacoes] != '')
                          {
                            echo "<tr><td colspan='6' style='padding-left: 15px'><span class='TextoAzul'>" . nl2br($dados_terceiro[observacoes]) . "</span></td></tr>";
                          }

                        ?>			
                        <?php
                        
                      }
                      
                      ?>
                      </table>
                      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                        <tr>
                          <td height="26">
                            <span style="font-size: 12px">
                              <?php

                                echo "&nbsp;&nbsp;Valor total dos terceiros do evento: <b>Custo: R$ " . number_format($total_custo_terceiros, 2, ",", ".") . "&nbsp;&nbsp;Venda: R$ " . number_format($total_venda_terceiros, 2, ",", ".") . "</b>";

                              ?>
                            </span>
                          </td>
                        </tr>
                      </table>
                    </div>
                    <?php

                  //Fecha o if de se deve exibir os terceiros
                  }

                ?>
                </td>
              </tr>
              <tr>
                <td>
                  <?php

                    //verifica se o usuário pode exibir os brindes
                    if ($dados_usuario["evento_brinde_exibe"] == 1)
                    {

                      ?>
                      <br/>
                      <?php /* EXIBE OS BRINDES CADASTRADOS PARA ESTE EVENTO */ ?>
                      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: #9e9e9e 1px solid">
                        <tr>
                          <td height="30">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                              <tr>
                                <td width="30" valign="middle" style="padding-left: 5px"><img src="image/bt_brinde_gd.gif"/></td>    
                                <td width="190"><span class="TituloModulo">Brindes do Evento</span></td>
                                <td>
                                  <?php
                                  
                                    //verifica se o usuário pode incluir os brindes
                                    if ($dados_usuario["evento_brinde_inclui"] == 1)
                                    {

                                      ?>
                                      [<a title="Clique para gerenciar os brindes deste evento" href="#" onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Gerenciar Brindes</a>]
                                      <?php
                                      
                                    }

                                    else
                                    {

                                      echo "&nbsp;";

                                    }

                                  ?>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <?php

                        //verifica todos os brindes cadastrados na base para montar o primeiro array (para comparar com os que estão inclusos no evento
                        //Monta a query de filtragem dos itens
                        $filtra_item = "SELECT
                                        evento_id														 
                                        FROM eventos_brinde
                                        WHERE evento_id = $EventoId";

                        //Executa a query
                        $lista_item = mysql_query($filtra_item);

                        //Cria um contador com o número de contar que a query retornou
                        $registros = mysql_num_rows($lista_item);

                      ?>
                      <div id="74">   
                        <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                          <?php

                            if ($registros > 0)
                            {

                              echo "<tr height='20'>
                                      <td width='52' align='right' class='listViewThS1' style='padding-right: 5px'>Qtde</td>
                                      <td width='355' class='listViewThS1'>&nbsp;&nbsp;Descrição do Brinde</td>
                                      <td width='300' class='listViewThS1'>Observações</td>
                                      <td class='listViewThS1'>&nbsp;</td> 
                                    </tr>";
                              
                            }

                            if ($registros == 0)
                            { 
                              
                              //Exibe uma linha dizendo que nao há regitros
                              echo "<tr height='24'>
                                      <td colspan='5' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' >
				  	<font color='#33485C'><b>Não há brindes cadastrados para este evento</b></font>
					</td>
                                    </tr>";
                              
                            }

                            //Monta a query de filtragem dos brindes
                            $filtra_item = "SELECT 
                                            bri.brinde_id,
                                            bri.quantidade,
                                            bri.observacoes,
                                            brinde.nome as brinde_nome
                                            FROM eventos_brinde bri
                                            LEFT OUTER JOIN brindes brinde ON brinde.id = bri.brinde_id
                                            WHERE bri.evento_id = $EventoId
                                            ORDER BY brinde.nome";

                            //Executa a query
                            $lista_item = mysql_query($filtra_item);

                            //Percorre o array
                            while ($dados_item = mysql_fetch_array($lista_item))
                            {

                              //Define o botão de exclusão do item
                              $botão_exclui_item = "<img src='image/grid_exclui.gif' alt='Clique para remover este brinde do evento' width='12' height='12' border='0' onClick=\"if(confirm('Confirma a remoção deste brinde do evento ?')) {wdCarregarFormulario('BrindeEventoExclui.php?BrindeId=$dados_item[brinde_id]&EventoId=$EventoId','conteudo')}\" style='cursor: pointer'>";
                              
                              ?>
                              <tr valign="top">
                                <td valign="top" align="right" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 5px; padding-top: 1px">
                                  <?php echo $dados_item[quantidade] ?>
                                </td>				 
                                <td valign="top" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-top: 1px; padding-bottom: 2px">
                                  <?php echo $dados_item[brinde_nome] ?>
                                </td>
                                <td valign="top" bgcolor="#fdfdfd" class="currentTabList">
                                  <?php echo $dados_item[observacoes] ?>
                                </td>
                                <td valign="top" style="padding-right: 6px; padding-top: 1px">
                                  <div align="right">
                                    <?php
                                    
                                      //verifica se o usuário pode excluir os brindes
                                      if ($dados_usuario["evento_brinde_exclui"] == 1)
                                      {

                                        //Exibe o botão de excluir o brinde
                                        echo $botão_exclui_item;

                                      }

                                      else
                                      {

                                        echo "&nbsp;";

                                      }
                                    
                                    ?>            	 
                                  </div>
                                </td>
                              </tr>	
                              <?php
                              
                            //Fecha o while dos itens
                            }
                            
                          ?>
                        </table>
                      </div>
                      <?php

                    //Fecha o if de se pode exibir os brindes
                    }

                  ?>
                </td>
              </tr>
              <tr>
                <td>
                  <?php

                    //verifica se o usuário pode exibir o repertório
                    if ($dados_usuario["evento_repertorio_exibe"] == 1)
                    {

                      ?>
                      <br/>
                      <?php /* EXIBE O REPERTÓRIO CADASTRADO PARA ESTE EVENTO */ ?>
                      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: #9e9e9e 1px solid">
                        <tr>
                          <td height="30">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                              <tr>
                                <td width="30" valign="middle" style="padding-left: 5px"><img src="image/bt_repertorio_gd.gif"/></td>    
                                <td width="190"><span class="TituloModulo">Repertório do Evento</span></td>
                                <td>
                                  <?php
                                  
                                    //verifica se o usuário pode incluir o repertório
                                    if ($dados_usuario["evento_repertorio_inclui"] == 1)
                                    {

                                      ?>
                                      [<a title="Clique para gerenciar o repertório deste evento" href="#" onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Gerenciar Repertório</a>]
                                      <?php

                                    //Fecha o if do nivel de acesso
                                    }

                                    else
                                    {

                                      echo "&nbsp;";

                                    }
                                  
                                  ?>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <?php

                        //Monta um sql para pesquisar se há repertório para este evento
                        $sql_conta_rep = mysql_query("SELECT
                                                      rep.id,
                                                      rep.categoria_repertorio_id
                                                      FROM eventos_repertorio rep
                                                      WHERE rep.evento_id = $EventoId
                                                      ");

                        $registros = mysql_num_rows($sql_conta_rep);

                        //Verifica as categorias cadastradas para o evento
                        $sql_conta_categorias = mysql_query("SELECT 
                                                            rep.id, 
                                                            cat.id as categoria_id,
                                                            cat.nome as categoria_nome
                                                            FROM eventos_repertorio rep
                                                            INNER JOIN categoria_repertorio cat ON cat.id = rep.categoria_repertorio_id
                                                            WHERE rep.evento_id = $EventoId
                                                            GROUP BY rep.categoria_repertorio_id");
                        
                      ?>
                      <div id="60">   
                        <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px"> 
                          <?php
                          
                            if ($registros > 0)
                            { 
                              
                              echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
                                      <td width='400' style='padding-left: 12px'>&nbsp;Música</td>
                                      <td width='300'>Intérprete</td>
                                      <td class='listViewThS1'>&nbsp;</td>
                                    </tr>";
                              
                            }

                            if ($registros == 0)
                            { 
                              
                              //Exibe uma linha dizendo que nao há regitros
                              echo "<tr height='24'>
                                      <td colspan='5' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' >
				  	<font color='#33485C'><b>Não há repertório cadastrado para este evento</b></font>
                                      </td>
                                    </tr>";
                              
                            }

                            //Cria o array e o percorre para montar a listagem das categorias
                            while ($dados_conta_categoria = mysql_fetch_array($sql_conta_categorias))
                            {

                              //Exibe a descrição da categoria
                              ?>
                              <tr valign="middle">
                                <td colspan="7" valign="bottom" style="padding-left: 6px">    				 	 
                                  <span style="font-size: 14px"><b>
                                    <?php echo $dados_conta_categoria['categoria_nome']; ?>
                                  </b></span>
                                </td>
                              </tr>
                              <?php
                              
                                //Monta a pesquisa das musicas listadas na categoria
                                //verifica o repertório já cadastrado para este evento e exibe na tela
                                $sql_consulta = mysql_query("SELECT
                                                            rep.id,
                                                            mus.nome as musica_nome,
                                                            mus.interprete as musica_interprete
                                                            FROM eventos_repertorio rep
                                                            LEFT OUTER JOIN musicas mus ON mus.id = rep.musica_id
                                                            WHERE rep.evento_id = $EventoId AND rep.categoria_repertorio_id = $dados_conta_categoria[categoria_id]
                                                            ");


                                //Cria o array e o percorre para montar a listagem dinamicamente
                                while ($dados_consulta = mysql_fetch_array($sql_consulta))
                                {
                                  
                                  ?>		
                                  <tr valign="middle">
                                    <td height="18" valign="middle" class="oddListRowS1" bgcolor="#fdfdfd" style="padding-bottom: 1px; padding-left: 12px">
                                      &nbsp;
                                      <?php echo $dados_consulta["musica_nome"]; ?>
                                    </td>
                                    <td height="18" valign="middle" bgcolor="#fdfdfd">
                                      <?php echo $dados_consulta[musica_interprete] ?>
                                    </td>	
                                    <td valign="top" style="padding-top: 4px; padding-right:6px">
                                      <div align="right">
                                        <?php

                                          //verifica se o usuário pode excluir o repertório
                                          if ($dados_usuario["evento_repertorio_exclui"] == 1)
                                          {

                                            ?>            	
                                            <img src="image/grid_exclui.gif" alt="Clique para excluir esta música do repertório" width="12" height="12" border="0" onclick="if(confirm('Confirma a exclusão desta música do repertório ?')) {wdCarregarFormulario('RepertorioEventoExclui.php?RepertorioId=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer">
                                            <?php

                                          }
                                        
                                        ?>
                                      </div>
                                    </td>									
                                  </tr>
                                  <?php

                                //Fecha o WHILE
                                }

                              //Fecha o while das categorias
                              }

                            ?>
                          </table>
                        </div>
                        <?php
                                        
                      //Fecha o if de se pode exibir o repertório
                      }
                      
                    ?>	
                </td>
              </tr>
              <tr>
                <td>
                  <?php
                  
                    //verifica se o usuário pode exibir foto e vídeo
                    if ($dados_usuario["evento_fotovideo_exibe"] == 1)
                    {

                      ?> 
                      <br/>
                      <?php /* EXIBE O FOTO E VÍDEO CADASTRADO PARA ESTE EVENTO */ ?>
                      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: #9e9e9e 1px solid">
                        <tr>
                          <td height="30">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                              <tr>						
                                <td width="30" valign='middle' style="padding-left: 5px"><img src="image/bt_fotovideo_gd.gif"/></td>						   
                                <td width="190"><span class="TituloModulo">Foto e Vídeo do Evento</span></td>
                                <td>
                                  <?php

                                    //verifica se o usuário pode incluir foto e vídeo
                                    if ($dados_usuario["evento_fotovideo_inclui"] == 1)
                                    {

                                      ?> 
                                      [<a title="Clique para gerenciar o Foto e Vídeo deste evento" href="#" onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Gerenciar Foto e Vídeo</a>]
                                      <?php

                                    }
                                    
                                    else
                                    {

                                      echo "&nbsp;";
                                      
                                    }
                                    
                                  ?>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                      <?php

                    }

                  ?>
                </td>
              </tr>
            </table>
</td>