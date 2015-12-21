<?php
###########
## Módulo principal do projeto Work Eventos
## Criado: 16/04/2007 - Maycon Edinger
## Alterado: 11/09/2007 - Maycon Edinger
## Alterações: 
## 23/04/2007 - Incluido o modulo de pesquisas
## 07/05/2007 - Implementado os novos níveis de segurança de acesso aos menus do sistema
## 29/07/2007 - Implementado nivel de segurança 5 para módulos financeiros
## 11/09/2007 - Renomeado o menu CONTAS para FINANCEIRO e incluso o módulo de cheques
###########

//Processa as diretivas de segurança
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//pesquisa as diretivas do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE usuario_id = $usuarioId";													  													  
							  
//Executa a query
$resultado_usuario = mysql_query($sql_usuario);

//Monta o array dos campos
$dados_usuario = mysql_fetch_array($resultado_usuario);

$usuarioRelatorio = $usuarioNome . " " . $usuarioSobrenome;

//Inclui o arquivo para manipulação de datas
//include "./include/ManipulaDatas.php";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Expires" content="3" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />

<title>work | eventos - Sistema de Gestão de Eventos</title>

<script type="text/javascript" src="include/workFuncoes.js"></script>
<script type="text/javascript" src="include/wd_ajax.js"></script>
<script type="text/javascript" src="include/jquery.1.8.min.js"></script>
<script type="text/javascript" src="include/jquery-ui.1.8.min.js"></script>
<script type="text/javascript" src="include/jquery.mask.min.js"></script>
<script type="text/javascript" src="menu/stmenu.js"></script>
<link type="text/css" rel="stylesheet" href="include/workStyle.css">

<script type="text/javascript">

  var LargTela = screen.width;
  var AltTela = screen.height;
  if (LargTela < 1024) 
  {
    alert("Para uma maior compatibilidade com o Work | Eventos, seu monitor deve estar configurado para a resolução de 1024X768.\n\nAtualmente seu monitor está configurado com a resolução de " + LargTela + "X" + AltTela + ", o que ocasionará que o sistema seja exibido com uma formatação inadequada.\n\nEm caso de dúvidas, contate nosso departamento de suporte."); 
  }

  var cm=null;
  var cs=null;
  document.onclick = new Function("Menu(null)"); new Function("SubMenu(null)")


  function processa_juro_especial()
  {

    var pegaData = prompt("Informe a data para IGNORAR boletos gerados:","Use o formato DD/MM/AAAA")

    if (pegaData)
    {

      wdCarregarFormulario("ModuloProcessaMultaEspecial.php?DataIgnora=" + pegaData,"conteudo");

    }

  }


</script>

<script language="JavaScript">
	
  var url = document.URL;

  var consoli = url.lastIndexOf('/consoli/');
  var keventos = url.lastIndexOf('/keventos/');

  if(consoli > 0)
  {
    document.title = "work | eventos - Sistema de Gestão de Eventos - [EMPRESA: Consoli Eventos]";	   
  } 

  else if(keventos > 0)
  {

    document.title = "work | eventos - Sistema de Gestão de Eventos - [EMPRESA: K Eventos]";	

  } 
</script>

</head>

<body>


<div style="position:fixed; width:1002px; height:12px; left: auto; bottom: 11px;"> 
  <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">
    <tr height="20">
      <td class="listViewPaginationTdS1" style="background-color: #DDDDDD;">
        <div id="barra"><span style="color: #990000;"><b>Aguarde... carregando informações</b></span></div>
      </td>
    </tr>
  </table>
</div> 

<?php

  //Recupera dos dados do ultimo evento trabalhado
  $sql_back = "SELECT 
                usu.evento_id,
                eve.id,
                eve.nome as evento_nome
                FROM usuarios usu
                LEFT OUTER JOIN eventos eve ON usu.evento_id = eve.id
                WHERE usu.usuario_id = $usuarioId";													  													  

  //Executa a query
  $resultado_back = mysql_query($sql_back);

  //Monta o array dos campos
  $dados = mysql_fetch_array($resultado_back);    
     
  
?>					
<script>
  document.getElementById('barra').innerHTML = "<table width='100%' border='0' cellpadding='0' cellspacing='0'><tr><td><span style='color: #000000;'>Último evento: </span><a title='Clique para exibir os detalhes deste evento' href='#' onclick='wdCarregarFormulario(\"EventoExibe.php?EventoId=<?php echo $dados[evento_id] ?>&headers=1\",\"conteudo\",0,0,\"1\")'><span class='style1'><?php echo $dados[evento_id] . " - " . $dados[evento_nome] ?></span></a></td><td width='250' align='right' style='padding-top: 1px'><img src='./image/bt_data_evento.gif' title='Clique para gerenciar as datas deste evento' onclick='wdCarregarFormulario(\"DataEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_participante.gif' title='Clique para gerenciar os participantes deste evento' onclick='wdCarregarFormulario(\"ParticipanteEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_endereco.gif' title='Clique para gerenciar os endereços deste evento' onclick='wdCarregarFormulario(\"EnderecoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_item.gif' title='Clique para gerenciar os produtos deste evento' onclick='wdCarregarFormulario(\"ItemEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_terceiro.gif' title='Clique para gerenciar os terceiros deste evento' onclick='wdCarregarFormulario(\"TerceiroEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_brinde.gif' title='Clique para gerenciar os brindes deste evento' onclick='wdCarregarFormulario(\"BrindeEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_repertorio.gif' title='Clique para gerenciar o repertório musical deste evento' onclick='wdCarregarFormulario(\"RepertorioEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_formando.gif' title='Clique para gerenciar os formandos deste evento' onclick='wdCarregarFormulario(\"FormandoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_fotovideo.gif' title='Clique para gerenciar o foto e vídeo deste evento' onclick='wdCarregarFormulario(\"FotoVideoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_documentos.gif' title='Clique para gerenciar os documentos deste evento' onclick='wdCarregarFormulario(\"DocumentosEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;&nbsp;</td><td width='220' align='right' style='border-left: #999999 1px solid'><form name='pesquisa' action='#'>Pesquisar:&nbsp;<input name='ChavePesquisa' type='text' id='ChavePesquisa' style='padding-left: 3px; width: 130px; color: #6666CC; font-weight: bold' maxlength='35' onKeyPress='if ((window.event ? event.keyCode : event.which) == 13) { return wdSubmitPesquisaEnter(); }' />&nbsp;<input class='button' title='Efetua a pesquisa com base no texto informado' name='btnPesquisa' type='button' id='btnPesquisa' value='Ok' style='width:22px' onClick='wdSubmitPesquisa()' /></form></td></tr></table>";
</script>	

<table cellspacing="0" cellpadding="0" width="100%" border="0">
  <tr>
    <td>
      <table cellspacing="0" cellpadding="0" width="900" border="0" >
        <tr>
          <td height="70" valign="middle" background="image/topo_sistema_2011.jpg">          
            <div align="right" id="ultimo_evento"></div>
          </td>
        </tr>
        </tr>
        <tr height="20">
          <td class="subTabBar">            
						<script type="text/javascript">
						  <!--
						  stm_bm(["menu45ba",960,"./menu","blank.gif",0,"","",0,0,250,0,1000,1,0,0,"","100%",0,0,1,2,"default","hand","",1,25],this);
						  stm_bp("p0",[0,4,0,0,3,4,0,9,100,"",-2,"",-2,50,2,3,"#999999","transparent","bluefireback.gif",1,0,0,"#A9CFDB #93C0CE #155E8C"]);
						  stm_ai("p0i0",[0,"Compromissos","","",-1,-1,0,"#","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#B5BED6",1,"","bluefireback_41.gif",3,3,0,0,"#FFFFF7","#000000","#FFFFFF","#FFFFFF","9pt Verdana","9pt Verdana",0,0,"","bluefireback_40.gif","","bluefireback_40.43.gif",6,6,24],75,0);
						  stm_bpx("p1","p0",[1,4,0,0,3,4,0,0,100,"",-2,"",-2,50,2,3,"#999999","#f0f0f0","",0]);
						  <?php

						  if ($dados_usuario["menu_compromisso"] == 1)
						  {

							  ?>
							  stm_aix("p1i0","p0i0",[0,"Consultar Agenda","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloCompromissos.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  if ($dados_usuario["menu_compromisso"] == 1)
						  {

							  ?>
							  stm_aix("p1i1","p0i0",[0,"Compromissos por Data","","",-1,-1,0,"javascript:wdCarregarFormulario(\'CompromissoConsulta.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  ?>
						  stm_ep();
						  stm_aix("p0i1","p0i0",[0,"Orçamentos","","",-1,-1,0,"#","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#FFFFF7"],85,0);
						  stm_bpx("p2","p1",[]);
						  <?php

						  if ($dados_usuario["menu_orcamento"] == 1)
						  {

							  ?>
							  stm_aix("p2i0","p0i0",[0,"Consultar Orçamentos","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloOrcamentos.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  ?>
						  stm_ep();
						  stm_aix("p0i2","p0i1",[0,"Eventos"],95,0);
						  stm_bpx("p3","p1",[]);
						  <?php

						  if ($dados_usuario["menu_evento"] == 1)
						  {

							  ?>
							  stm_aix("p3i0","p0i0",[0,"Consultar Eventos","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloEventos.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  if ($dados_usuario["menu_locacao"] == 1)
						  {

							  ?>
							  stm_aix("p4i0","p0i0",[0,"Consultar Locações","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloLocacoes.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  ?>
						  stm_ep();
						  stm_aix("p0i3","p0i1",[0,"Foto e Vídeo"],95,0);
						  stm_bpx("p4","p1",[]);
						  <?php

						  //verifica a exibição
						  if ($dados_usuario["evento_fotovideo_exibe"] == 1)
						  {

							  ?>
							  stm_aix("p4i0","p0i0",[0,"Gerenciar FV do Evento","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloFotoVideo.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p4i0","p0i0",[0,"Alocar Fornecedores","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloFotoVideoFornecedores.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_bpx("p10","p1",[1,2]);
							  stm_ep();
							  <?php

						  }

						  ?>
						  stm_ep();
						  stm_aix("p0i4","p0i1",[0,"Pessoas"],85,0);
						  stm_bpx("p5","p1",[]);
						  stm_aix("p9i1","p0i0",[0,"Clientes","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_bpx("p11","p1",[1,2]);
						  <?php

						  if ($dados_usuario["menu_cliente"] == 1)
						  {

							  ?>
							  stm_aix("p11i0","p0i0",[0,"Consultar Clientes","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloClientes.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);						
							  <?php
							  }

						  ?>
						  stm_ep();
						  stm_aix("p9i1","p0i0",[0,"Fornecedores","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_bpx("p11","p1",[1,2]);
						  <?php

						  if ($dados_usuario["menu_fornecedor"] == 1)
						  {

							  ?>
							  stm_aix("p11i0","p0i0",[0,"Consultar Fornecedores","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloFornecedores.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);						
							  <?php

						  }

						  ?>
						  stm_ep();
						  stm_aix("p9i1","p0i0",[0,"Colaboradores","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_bpx("p11","p1",[1,2]);
						  <?php

						  if ($dados_usuario["menu_colaborador"] == 1)
						  {

							  ?>
							  stm_aix("p11i0","p0i0",[0,"Consultar Colaboradores","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloColaboradores.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);						
							  <?php

						  }

						  ?>
						  stm_ep();

						  stm_ep();
						  stm_aix("p0i5","p0i1",[0,"Suprimentos"],85,0);
						  stm_bpx("p6","p1",[]);
						  stm_aix("p14i0","p0i0",[0,"Nova Ordem de Compra","","",-1,-1,0,"javascript:wdCarregarFormulario(\'OrdemCompraCadastra.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_aix("p14i0","p0i0",[0,"Consultar Ordem de Compra","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloOrdemCompra.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_aix("p14i0","p0i0",[0,"Consultar Produtos","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ItemConsulta.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);

						  stm_aix("p9i0","p0i0",[0,"Movimentação dos Produtos","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],100,0);
						  stm_bpx("p10","p1",[1,2]);
						  stm_aix("p10i0","p0i0",[0,"Entrada Manual","","",-1,-1,0,"javascript:wdCarregarFormulario(\'MovimentoEntradaCadastra.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_aix("p10i0","p0i0",[0,"Saída Manual","","",-1,-1,0,"javascript:wdCarregarFormulario(\'MovimentoSaidaCadastra.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_ep();

						  stm_aix("p9i0","p0i0",[0,"Relatórios","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],100,0);
						  stm_bpx("p10","p1",[1,2]);
						  stm_aix("p10i0","p0i0",[0,"Relação Geral de Produtos","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ProdutosRelatorio.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_aix("p10i0","p0i0",[0,"Relação de Movimentação","","",-1,-1,0,"javascript:wdCarregarFormulario(\'MovimentacaoProdutosRelatorio.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_aix("p10i0","p0i0",[0,"Produtos em Ponto de Compra","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/ProdutoEventoPontoCompraRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_aix("p10i0","p0i0",[0,"Alocação de Produtos em Eventos","","",-1,-1,0,"javascript:wdCarregarFormulario(\'EventoRelatorioConsumoProduto.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
						  stm_ep();

						  stm_ep();

						  stm_aix("p0i7","p0i1",[0,"Financeiro","","",-1,-1,0,""],85,0);
						  stm_bpx("p8","p1",[]);
						  <?php

						  //verifica se o usuário pode ver este menu
						  if ($dados_usuario["menu_financeiro"] == 1 AND $dados_usuario["nova_conta_pagar"] == 1)
						  {

							  ?>
							  stm_aix("p8i0","p0i0",[0,"Contas a Pagar","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloContasPagar.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  if ($dados_usuario["menu_financeiro"] == 1)
						  {

							  ?>
							  stm_aix("p8i1"," p0i0",[0,"Contas a Receber","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloContasReceber.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  //verifica se o usuário pode ver este menu
						  if ($usuarioNome == "Joni" OR $usuarioNome == "Gerri" OR $usuarioNome == "Maycon" OR $usuarioNome == "Josiane")
						  {

							  ?>
							  stm_aix("p9i0","p0i0",[0,"Planejamento Orçamentário","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],200,0);
							  stm_bpx("p10","p1",[1,2]);
							  stm_aix("p10i0","p0i0",[0,"Cadastrar Orçamento","","",-1,-1,0,"javascript:wdCarregarFormulario(\'OrcamentarioCadastra.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p10i0","p0i0",[0,"Consultar Orçamento","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloOrcamentario.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);							
							  stm_aix("p10i0","p0i0",[0,"Relatório","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloOrcamentario.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p10i0","p0i0",[0,"Demonstrativo de Resultado","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloDRE.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_ep();
							  <?php

						  }

						  if ($dados_usuario["menu_financeiro"] == 1)
						  {

							  ?>
							  stm_aix("p8i2","p0i0",[0,"Fluxo de Caixa","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloFluxoCaixa.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  //verifica se o usuário pode ver este menu
						  if ($dados_usuario["menu_financeiro"] == 1)
						  {

							  ?>
							  stm_aix("p8i3","p0i0",[0,"Boletos","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloBoletos.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  //verifica se o usuário pode ver este menu
						  if ($dados_usuario["menu_financeiro"] == 1)
						  {

							  ?>
							  stm_aix("p8i4","p0i0",[0,"Processar Retorno","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloRetorno.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  //verifica se o usuário pode ver este menu
						  if ($dados_usuario["menu_financeiro"] == 1)
						  {

							  ?>
							  //stm_aix("p8i5","p0i0",[0,"Emitir Recibos","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloRecibos.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  //verifica se o usuário pode ver este menu
						  if ($dados_usuario["menu_financeiro"] == 1)
						  {

							  ?>
							  stm_aix("p9i0","p0i0",[0,"Cheques","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],200,0);
							  stm_bpx("p10","p1",[1,2]);
							  stm_aix("p10i0","p0i0",[0,"Cheques de Terceiro","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloCheques.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p10i0","p0i0",[0,"Cheques da Empresa","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloChequesEmpresa.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p10i0","p0i0",[0,"Compensação de Cheques","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloCompensaCheque.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_ep();
							  <?php

						  }

						  //verifica se o usuário pode ver este menu
						  if ($usuarioNome == "Joni" OR $usuarioNome == "Gerri" OR $usuarioNome == "Maycon" OR $usuarioNome == "Rozelita Evaristo")
						  {

							  ?>
							  stm_aix("p8i7","p0i0",[0,"Relação de Vales por Período","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ValeRelatorioPeriodo.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  //verifica se o usuário pode ver este menu
						  if ($usuarioNome == "Joni" OR $usuarioNome == "Gerri" OR $usuarioNome == "Maycon" OR $usuarioNome == "Cleris")
						  {

							  ?>
							  stm_aix("p8i7","p0i0",[0,"Alteracao de Sacado do Boleto","","",-1,-1,0,"javascript:wdCarregarFormulario(\'BoletoAlteraSacado.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  if ($dados_usuario["relatorio_financeiro"] == 1)
						  {

						   	?>
							  stm_aix("p9i0","p0i0",[0,"Relatórios do Financeiro","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],200,0);
							  stm_bpx("p10","p1",[1,2]);
							  stm_aix("p10i0","p0i0",[0,"Financeiro dos Formandos","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloFinanceiroFormandos.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p10i0","p0i0",[0,"Retornos por Data","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloRetornoData.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p10i0","p0i0",[0,"Financeiro Eventos (Auditoria)","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloFinanceiroAuditoria.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p10i0","p0i0",[0,"Registro Resumido/Consolidado","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloFinanceiroCaixa.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p10i0","p0i0",[0,"Registro por Conta-Caixa","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloFinanceiroContaCaixa.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p10i0","p0i0",[0,"Fotos/Contas a Pagar (Auditoria)","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloConsolidaFinanceiro.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p10i0","p0i0",[0,"Análise Gerencial por Evento","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloFinanceiroGerencialEvento.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_ep();
						  	<?php

						  }

						  ?>			
						  stm_ep();
						  stm_aix("p0i8","p0i7",[0,"Relatórios"],95,0);
						  stm_bpx("p9","p1",[1,4,0,0,3,4,0,9]);            
						  <?php

						  if ($dados_usuario["relatorio_cadastros"] == 1)
						  {

							  ?>
							  stm_aix("p9i1","p0i0",[0,"Cadastros","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_bpx("p11","p1",[1,2]);
							  stm_aix("p11i0","p0i0",[0,"Centro de Custo de Conta","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/GrupoContaRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i1","p0i0",[0,"Conta-Caixa","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/SubGrupoContaRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i2","p0i0",[0,"Bancos","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/BancoRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i3","p0i0",[0,"Centro de Custo de Produtos","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/CategoriaItemRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i4","p0i0",[0,"Centro de Custo de Serviços","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/CategoriaServicoRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i6","p0i0",[0,"Produtos do Foto e Vídeo","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/CategoriaFotoVideoRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i7","p0i0",[0,"Materiais (Composição)","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/MaterialRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i9","p0i0",[0,"Brindes","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/BrindeRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i10","p0i0",[0,"Momentos de Repertório","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/CategoriaRepertorioRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i11","p0i0",[0,"Tipo de Local","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/LocalEventoRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i12","p0i0",[0,"Músicas","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/MusicaRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i13","p0i0",[0,"Funções","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/FuncaoRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&usarBanco=<?php echo $nomeBanco ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p11i14","p0i0",[0,"Cidades","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/CidadeRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_ep();
							  <?php

						  }

						  if ($dados_usuario["relatorio_eventos"] == 1)
				     	{

							  ?> 
							  stm_aix("p9i2","p0i0",[0,"Eventos","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_bpx("p12","p1",[1,2]);
							  stm_aix("p12i0","p0i0",[0,"Eventos por Data","","",-1,-1,0,"javascript:wdCarregarFormulario(\'EventoRelatorioData.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p12i1","p0i0",[0,"Eventos por Posição Financeira","","",-1,-1,0,"javascript:wdCarregarFormulario(\'EventoRelatorioFinanceiro.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p12i1","p0i0",[0,"Formandos/Evento e Curso","","",-1,-1,0,"javascript:wdCarregarFormulario(\'EventoRelatorioFormandoCurso.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p12i1","p0i0",[0,"Formandos/Evento e Curso C/ Res.","","",-1,-1,0,"javascript:wdCarregarFormulario(\'EventoRelatorioFormandoCursoComRescisao.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p12i1","p0i0",[0,"Atividades em Eventos","","",-1,-1,0,"javascript:wdCarregarFormulario(\'EventoRelatorioAtividade.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_ep();
							  <?php

						  }

						  if ($dados_usuario["relatorio_rh"] == 1)
						  {

							  ?>
							  stm_aix("p9i3","p0i0",[0,"Recursos Humanos","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_bpx("p13","p1",[1,2]);
							  stm_aix("p13i0","p0i0",[0,"Todos os Colaboradores","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/ColaboradorRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p13i1","p0i0",[0,"Somente Freelances","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/FreelanceRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p13i2","p0i0",[0,"Somente Funcionários","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/FuncionarioRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p13i3","p0i0",[0,"Somente Ex-Funcionários","","",-1,-1,0,"javascript:abreJanela(\'./relatorios/ExFuncionarioRelatorioPDF.php?UsuarioNome=<?php echo $usuarioRelatorio ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_ep();
							  <?php

					    }

						  ?>
						  <?php

						  if ($dados_usuario["evento_fotovideo_exibe"] == 1)
						  {

							  ?>
							  stm_aix("p9i0","p0i0",[0,"Foto e Vídeo","","",-1,-1,0,"","_self","","","","",0,0,0,"0604arroldw.gif","0604arroldw.gif",9,7,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_bpx("p10","p1",[1,2]);
							  stm_aix("p10i0","p0i0",[0,"Controle Envio Foto e Vídeo","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloControleFotoVideo.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);            
							  stm_aix("p3i1","p0i0",[0,"Pesquisa Pedidos do Foto e Vídeo","","",-1,-1,0,"sistema.php?ModuloNome=ModuloPedidoFotoVideo","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p3i1","p0i0",[0,"Compras do Foto e Vídeo","","",-1,-1,0,"javascript:wdCarregarFormulario(\'ModuloComprasFotoVideo.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_aix("p3i1","p0i0",[0,"Formandos sem Compra por Cidade","","",-1,-1,0,"javascript:wdCarregarFormulario(\'FotoVideoSemCompraCidade.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  stm_ep();
							  <?php

						  }

						  ?>
						  stm_ep();
						  stm_aix("p0i9","p0i7",[0,"Configurar"],95,0);
						  stm_bpx("p14","p1",[]);

						  stm_aix("p14i0","p0i0",[0,"Alterar Minha Senha","","",-1,-1,0,"javascript:wdCarregarFormulario(\'UsuarioPreferencia.php\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);

						  <?php

						  if ($dados_usuario["ativa_ger_usuario"] == 1)
						  {

							  ?>
							  stm_aix("p14i1","p0i0",[0,"Gerenciar Usuários","","",-1,-1,0,"javascript:wdCarregarFormulario(\'UsuarioGerencia.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php
						  }

					   	if ($usuarioNome == "Maycon" OR $usuarioNome == "Cleris"  OR $usuarioNome == "Juliana" OR $usuarioNome == "Janaina")
					  	{

							  ?>
							  stm_aix("p14i2","p0i0",[0,"Gerar Arquivo Atualização Online","","",-1,-1,0,"WorkAtualizaArquivo.php?Usuario=<?php echo $usuarioNome ?>","_blank","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);																		
							  <?php

						  }

						  if ($usuarioNome == "Maycon" OR $usuarioNome == "Cleris" OR $usuarioNome == "Vanderlea"  OR $usuarioNome == "Eduarda" OR $usuarioNome == "Thais"  OR $usuarioNome == "Rafael"  OR $usuarioNome == "Lucimeire"  OR $usuarioNome == "Janaína"  OR $usuarioNome == "Karina")
						  {

							  ?>
							  stm_aix("p14i3","p0i0",[0,"Atualização Online FORMANDO","","",-1,-1,0,"javascript:wdCarregarFormulario(\'WorkAtualizaFormando.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php
						  
						  }

						  if ($usuarioNome == "Maycon" OR $usuarioNome == "Cleris" OR $usuarioNome == "Vanderlea"  OR $usuarioNome == "Eduarda" OR $usuarioNome == "Thais" OR $usuarioNome == "Rafael"  OR $usuarioNome == "Lucimeire"  OR $usuarioNome == "Janaína"  OR $usuarioNome == "Karina")
						  {

							  ?>
							  stm_aix("p14i3","p0i0",[0,"Atualização Online EVENTO","","",-1,-1,0,"javascript:wdCarregarFormulario(\'WorkAtualizaEvento.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  if ($usuarioNome == "Maycon" OR $usuarioNome == "Juliana")
						  {

							  ?>
							  stm_aix("p14i4","p0i0",[0,"Estatísticas de Produtividade","","",-1,-1,0,"javascript:wdCarregarFormulario(\'WorkEstatistica.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  if ($dados_usuario["ativa_preferencias"] == 1)
						  {

							  ?>
							  stm_aix("p14i5","p0i0",[0,"Preferências do Sistema","","",-1,-1,0,"javascript:wdCarregarFormulario(\'PreferenciasSistema.php?headers=1\',\'conteudo\')","_self","","","","",0,0,0,"","",0,0,0,0,1,"#00CCFF",1,"#C8C8C8",1,"","bluefireback3-2.gif",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0,"","bluefireback%203%20-1.gif","","bluefireback%203%20-3.gif",4,4],130,0);
							  <?php

						  }

						  ?>
						  stm_ep();
						  stm_aix("p0i10","p0i1",[0,"Sair","","",-1,-1,0,"logout.php","_self","","","","",0,0,0,"","",0,0],75,0);
						  stm_ep();
						  stm_em();
						  
						  //-->
						</script>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table width="1002" border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
    <td width="10">	</td>	    
    <td colspan="3" height="26" valign="top" class="calSharedUser">
      <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
        <tr valign="middle">
          <td width="692" height="20" style="padding-top: 3px;">
            Olá <span class="style1"><?php echo $usuarioNome ?></span>, bem-vindo ao <strong>work | eventos</strong>.
            
						<?php
							//Caso o usuário for específico para processar as contas a receber...
							if ($usuarioNome == "Maycon" OR $usuarioNome == "Janaina" OR $usuarioNome == "Cleris" OR $usuarioNome == "Thais")
							{

								//Busca a data da última atualização de juros e multa
								$sql_multa = "SELECT data_atualizacao_multa FROM parametros_sistema";													  													  
														  
								//Executa a query
								$resultado_multa = mysql_query($sql_multa);

								//Monta o array dos campos
								$dados_multa = mysql_fetch_array($resultado_multa);


								$data_processa = $dados_multa['data_atualizacao_multa'];

								$hoje = date('Y-m-d', mktime());

								if ($dados_multa['data_atualizacao_multa'] < $hoje)
								{
										
									$data_hoje = date('d/m/Y', mktime());

									echo "<div id='atualiza' style='padding-bottom: 4px'>Ainda não foram processados os juros e multas para a data de hoje. &nbsp;&nbsp;<input name='processa' type='buton' class='button' title='Processa a multa e juros para esta data' value='Processar Multa e Juros' onclick=\"wdCarregarFormulario('ModuloProcessaMulta.php?DataProcessa=$data_processa','conteudo')\" style='width: 140px; text-align: center; cursor:pointer' />&nbsp;&nbsp;<input name='processa' type='buton' class='button' title='Processa a multa e juros para esta data' value='Processamento Especial' onclick=\"processa_juro_especial();\" style='width: 140px; text-align: center; cursor:pointer' /></div>";

								}
							} 
						?>
					</td>
					<td width="302" align="right"><span class="style1" style="font-size: 12px"><?php echo $empresaNome ?></span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top"></td>
		<td width="180" valign="top">
			<table width="180" border="0" align="left" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<?php 						
							include "ModuloFavoritos.php"; 
						?>
					</td>
				</tr>
				<tr>
					<td>
						<br/>	

						<table width="180" border="0" align="left" cellpadding="0" cellspacing="0">
							<tr>
								<td class="TituloModulo">
									<img src="image/lat_cadastro.gif"> Calendário <br />
									<img src="image/bt_espacohoriz.gif" width="100%" height="12">							</td>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
					 	<div id="calendario">	     			
							<?php
								include "ModuloCalendario.php";
							?>
			 			</div>
					</td>
				</tr>
			</table>			 				
		</td>
		<td width="12" valign="top" background="image/bt_espacovert.gif">
			<img src="image/bt_brancovert.gif" width="11" height="26" />	
		</td>    
		<td width="800" align="left" valign="top">
			<div id="conteudo">
				<?php
					
					//Caso for especificado um nome de módulo para exibir
					if($_GET["ModuloNome"])
					{ 
						
						//Pega o nome do módulo passado pela URL
						$CapturaNome = $_GET["ModuloNome"]; 
						//Gera uma variavel com o nome e a extensao PHP para abrir o arquivo
						$MontaNome = $CapturaNome . ".php"; 
						//Inclui o módulo na visualização
						include $MontaNome;   
					
						//Se não for especificado um nome padrão, carregará os módulos principais do sistema
					} 
					else 
					{                 
						/*
						?>
						
						<table width='100%' border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width='440'>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Ho Ho Ho !</span>
					</td>
				</tr>
				<tr>
					<td colspan='5'>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">		
					</td>
				</tr>
			</table>
 	 	</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
				<tr>
					<td height="22" align="middle" width="45" valign="middle" bgcolor="#FFFFCD" style="border: #D3BE96 solid 1px; padding-top: 1px; padding-bottom: 0px;padding-right: 0px; border-right: 0px">
						<img src="image/bt_natal.gif" width="28" height="40" border="0" />
					</td>
					<td height="22" width="820" valign="middle" align="left" bgcolor="#FFFFCD" style="border: #D3BE96 solid 1px; padding-left: 0px; border-left: 0px; padding-bottom: 4px; padding-top: 4px">
						Queremos renovação e buscamos os grandes milagres da vida a cada instante. Todo ano é hora de renascer, de florescer, de viver de novo. <br/>Aproveite este ano que está chegando para realizar todos os seus sonhos !<br/><b>FELIZ NATAL E UM PRÓSPERO ANO NOVO !</b></span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<img src="image/fundo_frame.png" width="800" height="12" />
					</td>
				</tr>									
			</table>
		</td>
	</tr>
</table>

*/
?>
<?php

						//Exibe os compromissos do dia
						include "CompromissoLista.php"; 
						//Põe um espaço pra não ficar feio
						echo "<br>";
						//Inclui o módulo para listagem dos eventos
						include "EventosLista.php";
						//Põe um espaço pra não ficar feio
						echo "<br>";
						//Inclui o módulo para listagem das atividades
						include "AtividadesLista.php";
						//Põe um espaço pra não ficar feio
						echo "<br>";
						//Inclui o módulo para listagem das locações em aberto
						include "LocacaoLista.php";
						//Põe um espaço pra não ficar feio
						echo "<br>";
						//Inclui o módulo para listagem dos últimos recados
						include "RecadoLista.php"; 
						//Põe um espaço pra não ficar feio
			 			echo "<br>"; 
						//Inclui o módulo para exibição das datas comemorativas
						include "DataComemorativaLista.php"; 
						//Põe um espaço pra não ficar feio
						echo "<br>"; 
						//Inclui o módulo para exibição dos aniversarios
						include "DataAniversarioLista.php"; 
        
						echo "<br>";
						
						//Exibe o número de formandos cadastrados
						include "TotalFormandosLista.php";
					}

				?>	
			</div>     	
		</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td valign="top" background="image/bt_espacovert.gif">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td background="image/bt_espacohoriz.gif"></td>
    <td valign="top"><div align="center"><img src="image/bt_espaco_t.gif" width="9" height="12" /></div></td>
    <td background="image/bt_espacohoriz.gif">
      <div align="center"></div>
    </td>
  </tr>		
  <tr>
    <td>&nbsp;</td>
    <td colspan="3">						
      <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding-top: 10px; padding-bottom: 30px;">											    
            © 2007 : 2014 - Todos os direitos reservados - Desenvolvido por Work Labs Tecnologia e Sistemas Ltda - www.worklabs.com.br
          </td>
          <td width="100" align="right" valign="top">
            <a href="http://www.worklabs.com.br" target="_blank"><img src="image/worklabs_2.png" height="27" width="147" border="0" /></a>
          </td>
        </tr>	
      </table>
    </td>
  </tr>
</table>

</body>

</html>
