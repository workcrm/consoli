<?php
###########
## Módulo para listagem de datas comemorativas no menu principal
## Criado: 02/10/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) {
	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";
}

//Processa a contagem inicial do total de recados do usuario
$sql = mysql_query("SELECT id,nome, descricao_data FROM clientes WHERE DAY(data_comemorativa) = DAY(CURDATE()) AND MONTH(data_comemorativa) = MONTH(CURDATE())");

$registros = mysql_num_rows($sql); 

?>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
  <td>

	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width='440'><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Datas Comemorativas</span></td>
	  </tr>
	</table>


  </td>
  </tr>
  <tr>
    <td>
      <table id="4" width='100%' align='left' border='0' cellspacing='0' cellpadding='0' class="listView">
        <tr height="12">
	    	  <td height="12" colspan='4' class="listViewPaginationTdS1">
	      	  <table width='100%'  border='0' cellspacing='0' cellpadding='0'>
	      	  	<tr>
	      	  		<td width="40">
	      	  			<img src="image/bt_data_gd.gif" />
	      	  		<td>
	      	  		<td>	      	  		
									<?php 
										
										if ($registros == 0) {
										
											$mensagem_regs = "Nenhuma ";
										
										} else {
											
											$mensagem_regs = "<span style='color: #990000'>$registros</span> ";} 
									
									?>
									<span style="font-size: 12px; color: #444444"><b><?php echo $mensagem_regs ?>data comemorativa para o dia de hoje</b></span>
		  	  			</td>
		  	  		</tr>
		  	  	</table>
	    	  </td>
  			</tr>

  		<?php
        //Caso não tenha compromissos então não exibe a linha de cabeçalho.
    	if ($registros > 0) { 
      	echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          <td width='26'>&nbsp;</td>
	      	<td>Cliente</td>         
	      	<td width='390'>Descrição</td>
        </tr>
    ";}

	//Cria o array e o percorre para montar a listagem dinamicamente
    while ($dados = mysql_fetch_array($sql)){
    
?>
      <tr valign='middle'>
        <td height="15" width='26' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style="padding-bottom: 1px">
          <img src="image/bt_aniversario.gif" />
				</td>
        <td>
          <span style="font-size: 12px; color: #CC3300">
						<a title="Clique para exibir este cliente" href="#" onclick="wdCarregarFormulario('ClienteExibe.php?ClienteId=<?php echo $dados[id] ?>','conteudo')"><?php echo $dados['nome']; ?></a>
					</span>        
				</td>
        <td align='left' bgcolor='#fdfdfd'>
          <?php echo $dados[descricao_data] ?>
				</td>						
  	  </tr>

  <?php
  //Fecha o WHILE
  }
  ?>
  </table>

</td>
</tr>
</table>
