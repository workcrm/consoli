<?php 
###########
## Módulo para geração de estatísticas de uso do Work.
## Criado: 21/07/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
## 
###########

header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

$hoje = date("Y-m-d", mktime());
 

?>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Estatísticas de Produtividade:</span></td>
        </tr>
        <tr>
			    <td>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12" />
				  </td>
        </tr>        
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <strong>Contas a <span style="color: #990000;">RECEBER</span> cadastradas nos últimos <span style="color: #990000;">5</span> dias de uso do Work Eventos:</strong><br/>
      <table width="200" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">      
        <tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">
		      <td width="80" align="center">&nbsp;Data</td>
 		      <td align="center">Contas Cadastradas</td>
        </tr>      


<?php

//Consulta as contas que devem ser processadas na parte das multas
//Cria a SQL
$consulta_contas = "SELECT 
                    data, 
                    count(1) as total_contas 
                    FROM contas_receber 
                    GROUP BY data 
                    ORDER BY data DESC 
                    limit 0,5";
                  
//Executa a query
$listagem_contas = mysql_query($consulta_contas);

$numero_registros_contas = mysql_num_rows($listagem_contas);

//Monta e percorre o array com os dados da consulta
while ($dados_contas = mysql_fetch_array($listagem_contas))
{

  $data = DataMySQLRetornar($dados_contas[data]);
  
  echo " 
        <tr valign='middle' align='center'>
          <td height='18' valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style='padding-bottom: 1px'>
            &nbsp;$data
  				</td>
          <td align='center'><strong>
            $dados_contas[total_contas]
            <strong>
          </td>
        </tr>"; 

}

?>

      </table>
    </td>
  </tr>

  <tr>
    <td>
    <br/>
      <strong><span style="color: #990000;">BOLETOS</span> emitidos nos últimos <span style="color: #990000;">5</span> dias de uso do Work Eventos:</strong><br/>
      <table width="200" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">      
        <tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">
		      <td width="80" align="center">&nbsp;Data</td>
 		      <td align="center">Boletos Emitidos</td>
        </tr>      


<?php

//Consulta as contas que devem ser processadas na parte das multas
//Cria a SQL
$consulta_contas = "SELECT 
                    data_documento, 
                    count(1) as total_boletos 
                    FROM boleto 
                    GROUP BY data_documento 
                    ORDER BY data_documento DESC 
                    limit 0,5";
                  
//Executa a query
$listagem_contas = mysql_query($consulta_contas);

$numero_registros_contas = mysql_num_rows($listagem_contas);

//Monta e percorre o array com os dados da consulta
while ($dados_contas = mysql_fetch_array($listagem_contas))
{

  $data = DataMySQLRetornar($dados_contas[data_documento]);
  
  echo " 
        <tr valign='middle' align='center'>
          <td height='18' valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style='padding-bottom: 1px'>
            &nbsp;$data
  				</td>
          <td align='center'><strong>
            $dados_contas[total_boletos]
            <strong>
          </td>
        </tr>"; 

}

?>

      </table>
    </td>
  </tr>

  <tr>
    <td>
    <br/>
      <strong><span style="color: #990000;">FORMANDOS</span> cadastrados nos últimos <span style="color: #990000;">5</span> dias de uso do Work Eventos:</strong><br/>
      <table width="200" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">      
        <tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">
		      <td width="80" align="center">&nbsp;Data</td>
 		      <td align="center">Formandos</td>
        </tr>      


<?php

//Consulta as contas que devem ser processadas na parte das multas
//Cria a SQL
$consulta_contas = "SELECT date( cadastro_timestamp ) AS data_documento,
                    count( 1 ) AS total_formandos
                    FROM eventos_formando
                    GROUP BY date( cadastro_timestamp ) 
                    ORDER BY date( cadastro_timestamp ) DESC 
                    LIMIT 0 , 5";
                  
//Executa a query
$listagem_contas = mysql_query($consulta_contas);

$numero_registros_contas = mysql_num_rows($listagem_contas);

//Monta e percorre o array com os dados da consulta
while ($dados_contas = mysql_fetch_array($listagem_contas))
{

  $data = DataMySQLRetornar($dados_contas[data_documento]);
  
  echo " 
        <tr valign='middle' align='center'>
          <td height='18' valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style='padding-bottom: 1px'>
            &nbsp;$data
  				</td>
          <td align='center'><strong>
            $dados_contas[total_formandos]
            <strong>
          </td>
        </tr>"; 

}

?>

      </table>
    </td>
  </tr>
  
  <tr>
    <td>
      <br/>
      <strong>Contas a <span style="color: #990000;">PAGAR</span> cadastradas nos últimos <span style="color: #990000;">5</span> dias de uso do Work Eventos:</strong><br/>
      <table width="200" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">      
        <tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">
		      <td width="80" align="center">&nbsp;Data</td>
 		      <td align="center">Contas Cadastradas</td>
        </tr>      


<?php

//Consulta as contas que devem ser processadas na parte das multas
//Cria a SQL
$consulta_contas = "SELECT 
                    data, 
                    count(1) as total_contas 
                    FROM contas_pagar 
                    GROUP BY data 
                    ORDER BY data DESC 
                    limit 0,5";
                  
//Executa a query
$listagem_contas = mysql_query($consulta_contas);

$numero_registros_contas = mysql_num_rows($listagem_contas);

//Monta e percorre o array com os dados da consulta
while ($dados_contas = mysql_fetch_array($listagem_contas))
{

  $data = DataMySQLRetornar($dados_contas[data]);
  
  echo " 
        <tr valign='middle' align='center'>
          <td height='18' valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style='padding-bottom: 1px'>
            &nbsp;$data
  				</td>
          <td align='center'><strong>
            $dados_contas[total_contas]
            <strong>
          </td>
        </tr>"; 

}

?>

      </table>
    </td>
  </tr>  
</table>