<?php

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";
	
//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

 
//Recupera os valores vindos do formulário e armazena nas variaveis
if($_POST["Submit"])
{

	$edtTotalChk = $_POST["edtTotalChk"];
  
  //Verifica se precisa alterar alguma conta
  if ($edtTotalChk > 0)
  {


  	//Define o valor inicial para efetuar o FOR
  	for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++)
    {
  	
  		//Monta a variável com o nome dos campos
      $conta_id_original = "edtContaId" . $contador_for;
  		$boleto_id_original = "edtBoletoId" . $contador_for;
  		$vencimento_original = "edtVencimento" . $contador_for;
      $valor_original = "edtValorOriginal" . $contador_for;
      $valor_boleto = "edtValorBoleto" . $contador_for;
      $valor_multa_juros = "edtValorMultaJuros" . $contador_for;
      
      $conta_id_banco = $_POST[$conta_id_original];
  		$boleto_id_banco = $_POST[$boleto_id_original];
      $vencimento_banco = DataMySQLInserir($_POST[$vencimento_original]);
      $valor_original_banco = MoneyMySQLInserir($_POST[$valor_original]);      
      $valor_boleto_banco = MoneyMySQLInserir($_POST[$valor_boleto]);
      $valor_multa_juros_banco = MoneyMySQLInserir($_POST[$valor_multa_juros]);
      
      $valor_receber_banco = $valor_original_banco + $valor_boleto_banco + $valor_multa_juros_banco;
      			
  		
  		//Enquanto não chegar ao final do contador total de itens
  		if ($_POST[$contador_for] != 0) 
      {
  												
  			$sql_altera_conta = "UPDATE contas_receber SET
                             data_vencimento = '$vencimento_banco',
  						 						   valor_original = '$valor_original_banco',
                             valor_boleto = '$valor_boleto_banco',
                             valor_multa_juros = '$valor_multa_juros_banco',
                             valor = '$valor_receber_banco'
                             WHERE id = $conta_id_banco";																		
  			
			//echo $sql_altera_conta . "<br/>";
        
  			//Insere os registros na tabela de eventos_itens
  			mysql_query($sql_altera_conta);
        
			//Verifica se não possui um boleto associado
			if ($boleto_id_banco > 0)
			{
			  
				$data_atualizacao = date("Y-m-d", mktime());
				
				$sql_altera_boleto = "UPDATE boleto SET
									valor_boleto = '$valor_receber_banco',
									data_vencimento = '$vencimento_banco',
									reajustado = 0,
									data_atualizacao = '$data_atualizacao'
									WHERE id = $boleto_id_banco";																		
				
				echo $sql_altera_boleto . "<br/>";
			
				//Altera os parâmetros dos boletos
				mysql_query($sql_altera_boleto);
			
			}
  					 
  										
  		}
  														
  										
  	//Fecha o FOR
  	}
 
 //Fecha o if de se precisa alterar
 }
 
 else
 
 {
  
  echo "<div id='98'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Nenhuma conta foi marcada para alteração !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
  ?>
  
  <input class="button" title="Retorna ao Módulo de Contas a Receber" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Módulo de Contas a Receber" onclick="wdCarregarFormulario('ModuloContasReceber.php','conteudo')" />
 
  <?php
 }
 ?>
  
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" valign="top">
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
		    <tr>
		      <td>
			    	<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Alteração de Contas a Receber</span>			  	
					</td>
		    </tr>
		    <tr>
		      <td colspan="5">
			    	<img src="image/bt_espacohoriz.gif" width="100%" height="12">			    	
		  	  </td>
		    </tr>
		  </table>
    </td>
  </tr>
  <tr>
		<td style="PADDING-BOTTOM: 2px">
      <?php					
      	//Exibe a mensagem de inclusão com sucesso
        echo "<div id='98'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Contas a receber alteradas com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
      ?>
      <input class="button" title="Retorna ao Módulo de Contas a Receber" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Módulo de Contas a Receber" onclick="wdCarregarFormulario('ModuloContasReceber.php','conteudo')" />
    </td>
  </tr>
</table>
<?php
}

?>