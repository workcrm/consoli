<?php 
###########
## Módulo para Montagem do Menu Lateral de Favoritos
## Criado: 18/05/2007 - Maycon Edinger
## Alterado: 21/11/2007 - Maycon Edinger
## Alterações: 
###########
?>

<table width="180" border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="2" class="TituloModulo">
      <img src="image/lat_cadastro.gif" /> Favoritos    	
    </td>
  </tr>
  <tr>
    <td colspan="2" valign="top">
      <img src="image/bt_espacohoriz.gif" width="100%" height="12">
    </td>
  </tr>  
  <tr>
    <td width="22">
      <img src="./image/bt_home.gif" border="0" />
    </td>
    <td width="158" height="18">
      <a href="#" onclick="wdCarregarFormulario('MeuPortal.php','conteudo')">Meu Portal </a>
    </td>
  </tr>
  <?php
  
  //verifica se o usuário pode ver este menu
  if ($dados_usuario['novo_compromisso'] == 1)
  {
    
    ?>
    <tr>
      <td>
        <img src="./image/bt_agenda2.gif" border="0" />
      </td>
      <td height="18">
        <a href="sistema.php?ModuloNome=CompromissoCadastra">Novo Compromisso</a>
      </td>
    </tr>
    <?php

  }

//verifica se o usuário pode ver este menu
if ($dados_usuario['novo_evento'] == 1)
{
  ?>	

    <tr>
      <td>
        <img src="./image/bt_pendencia.gif" border="0" />
      </td>
      <td height="18">
        <a href="#" onclick="wdCarregarFormulario('EventoCadastra.php?headers=1','conteudo')">Novo Evento</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario['nova_locacao'] == 1)
{
  ?>	  

    <tr>
      <td>
        <img src="./image/bt_locacao.gif" border="0" />
      </td>
      <td height="18">
        <a href="#" onclick="wdCarregarFormulario('LocacaoCadastra.php?headers=1', 'conteudo')">Nova Locação</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario['novo_cliente'] == 1)
{
  ?>

    <tr>
      <td>
        <img src="./image/bt_contas.gif" border="0" />
      </td>
      <td height="18">
        <a href="#" onclick="wdCarregarFormulario('ClienteCadastra.php?headers=1', 'conteudo')">Novo Cliente</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario['novo_fornecedor'] == 1)
{
  ?>

    <tr>
      <td>
        <img src="./image/bt_fornecedor.gif" border="0" />
      </td>
      <td height="18">
        <a href="#" onclick="wdCarregarFormulario('FornecedorCadastra.php?headers=1', 'conteudo')">Novo Fornecedor</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario['novo_colaborador'] == 1)
{
  ?>

    <tr>
      <td>
        <img src="./image/bt_colaborador.gif" border="0" />
      </td>
      <td height="18">
        <a href="#" onclick="wdCarregarFormulario('ColaboradorCadastra.php?headers=1', 'conteudo')">Novo Colaborador</a>
      </td>
    </tr>

    <?php
  }

  //verifica se o usuário pode ver este menu
  if ($dados_usuario['nova_conta_pagar'] == 1)
  {
    ?>	

    <tr>
      <td>
        <img src="./image/bt_pagar.gif" border="0" />
      </td>
      <td height="18">
        <a href="#" onclick="wdCarregarFormulario('ContaPagarCadastra.php?headers=1', 'conteudo')">Nova Conta a Pagar</a>
      </td>
    </tr>

    <?php
  }

  //verifica se o usuário pode ver este menu
  if ($dados_usuario['nova_conta_receber'] == 1)
  {
    ?>			

    <tr>
      <td>
        <img src="./image/bt_receber.gif" border="0" />
      </td>
      <td height="18" valign="middle">
        <a href="#" onclick="wdCarregarFormulario('ContaReceberCadastra.php?headers=1', 'conteudo')">Nova Conta a Receber</a>
      </td>
    </tr>

    <?php
  }

  //verifica se o usuário pode ver este menu
  if ($dados_usuario['novo_cheque_recebido'] == 1)
  {
    ?>	

    <tr>
      <td>
        <img src="./image/bt_cheque.gif" border="0" />
      </td>
      <td height="18" valign="middle">
        <a href="#" onclick="wdCarregarFormulario('ChequeCadastra.php?headers=1', 'conteudo')">Novo Cheque de Terceiro</a>
      </td>
    </tr>

    <?php
  }

  //verifica se o usuário pode ver este menu
  if ($dados_usuario['novo_lancamento_caixa'] == 1)
  {
    ?>	

    <tr>
      <td>
        <img src="./image/bt_caixa.gif" border="0" />
      </td>
      <td height="18" valign="middle">
        <a href="#" onclick="wdCarregarFormulario('CaixaCadastra.php?headers=1', 'conteudo')">Novo Lançamento no Caixa</a>
      </td>
    </tr>

    <?php
  }

  //verifica se o usuário pode ver este menu
  if ($dados_usuario['nova_ordem_compra'] == 1 OR $usuarioNome == 'Maycon' OR $usuarioNome == 'Janaina')
  {
    ?>	  

    <tr>
      <td>
        <img src="./image/bt_ordem_compra.jpg" border="0" />
      </td>
      <td height="18">
        <a href="#" onclick="wdCarregarFormulario('OrdemCompraCadastra.php?headers=1', 'conteudo')">Nova Ordem de Compra</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario['novo_vale'] == 1)
{
  ?>	

    <tr>
      <td>
        <img src="./image/bt_vale.gif" border="0" />
      </td>
      <td height="18" valign="middle">
        <a href="#" onclick="wdCarregarFormulario('ValeCadastra.php?headers=1', 'conteudo')">Novo Vale ao Colaborador</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario['novo_recado'] == 1)
{
  ?>

    <tr>
      <td>
        <img src="./image/bt_recado.gif" border="0" />
      </td>
      <td height="18">
        <a href="sistema.php?ModuloNome=RecadoCadastra">Novo Recado</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario['evento_fotovideo'] == 1)
{
  ?>

    <tr>
      <td>
        <img src="./image/bt_colar.png" border="0" />
      </td>
      <td height="18">
        <a href="sistema.php?ModuloNome=PedidoFotoVideoCadastra">Novo Pedido Foto e Vídeo</a>
      </td>
    </tr>

  <?php
}
?>	
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>

  <tr>
    <td colspan="2"><span class="TituloModulo"><img src="image/lat_cadastro.gif" /> Cadastros <br />
        <img src="image/bt_espacohoriz.gif" width="100%" height="12" /></span>
    </td>
  </tr>   	
  <tr>
    <td colspan="2">
      <b>Financeiro</b>
    </td>
  </tr>

<?php
//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_centro_custo_conta"] == 1)
{
  ?>

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=GrupoContaCadastra">Centro de Custo Contas</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_conta_caixa"] == 1)
{
  ?>

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=SubgrupoContaCadastra">Conta-Caixa</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_banco"] == 1)
{
  ?>

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=BancoCadastra">Bancos</a>
      </td>
    </tr>

    <?php
  }

  //verifica se o usuário pode ver este menu
  if ($dados_usuario["cad_conta_corrente"] == 1)
  {
    ?>

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=ContaCorrenteCadastra">Conta Corrente</a>
      </td>
    </tr>

    <?php
  }
  ?>  

  <tr>
    <td colspan="2">
      <b>Estoque</b>
    </td>
  </tr>

<?php
//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_centro_custo_produto"] == 1)
{
  ?>

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=CategoriaItemCadastra">Centro de Custo Produtos</a>
      </td>
    </tr>

  <?php
}

/*
//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_centro_custo_servico"] == 1)
{
 
  ?>
  <tr>
    <td>
      <div align="center">
        <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
      </div>
    </td>
    <td>
      <a href="sistema.php?ModuloNome=CategoriaServicoCadastra">Centro de Custo Serviços</a>
    </td>
  </tr>
  <?php
 
} 
*/

//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_produto"] == 1)
{
  
  ?>
  <tr>
    <td>
      <div align="center">
        <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
      </div>
    </td>
    <td>
      <a href="sistema.php?ModuloNome=ItemCadastra">Produtos do Evento</a>
    </td>
  </tr>
  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_produto_fotovideo"] == 1)
{
  ?>

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=CategoriaFotoVideoCadastra">Produtos do Foto/Vídeo</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_material"] == 1)
{
  ?>  

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=MaterialCadastra">Materiais (Composição)</a>
      </td>
    </tr>

  <?php
}

/*
//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_servico"] == 1)
{
  
  ?>
  <tr>
    <td>
      <div align="center">
        <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
      </div>
    </td>
    <td>
      <a href="sistema.php?ModuloNome=ServicoCadastra">Serviços</a>
    </td>
  </tr>
  <?php
  
}
*/

//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_brinde"] == 1)
{
  
  ?>
  <tr>
    <td>
      <div align="center">
        <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
      </div>
    </td>
    <td>
      <a href="sistema.php?ModuloNome=BrindeCadastra">Brindes</a>
    </td>
  </tr>
  <tr>
    <td>
      <div align="center">
        <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
      </div>
    </td>
    <td>
      <a href="sistema.php?ModuloNome=TiposMovimentacaoCadastra">Tipos de Movimentação</a>
    </td>
  </tr>
  <?php
    
  }
  
  ?>
  <tr>
    <td colspan="2"><b>Eventos</b></td>
  </tr>

  <?php
  //verifica se o usuário pode ver este menu
  if ($dados_usuario["cad_repertorio"] == 1)
  {
    ?>
    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=CategoriaRepertorioCadastra">Momentos de Repertório</a>
      </td>
    </tr>

    <?php
  }

  //verifica se o usuário pode ver este menu
  if ($dados_usuario["cad_tipo_local"] == 1)
  {
    ?>

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=LocalEventoCadastra">Tipos de Local</a>
      </td>
    </tr>

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_musica"] == 1)
{
  ?>  

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=MusicaCadastra">Músicas</a>
      </td>
    </tr>  

    <?php
  }
  ?>

  <tr>
    <td colspan="2"><b>Recursos Humanos</b></td>
  </tr>

<?php
//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_funcao"] == 1)
{
  ?>       

    <tr>
      <td><div align="center"><img src="image/lat_cadastro.gif" width="6" height="10" border="0" /></div></td>
      <td><a href="sistema.php?ModuloNome=FuncaoCadastra">Funções</a></td>
    </tr>

    <?php
  }
  ?>

  <tr>
    <td colspan="2"><b>Sistema</b></td>
  </tr>
  <tr>
    <td>
      <div align="center"><img src="image/lat_cadastro.gif" width="6" height="10" border="0" /></div>
    </td>
    <td>
      <a href="sistema.php?ModuloNome=RegiaoCadastra">Regiões</a>
    </td>
  </tr>
  <tr>
    <td>
      <div align="center"><img src="image/lat_cadastro.gif" width="6" height="10" border="0" /></div>
    </td>
    <td>
      <a href="sistema.php?ModuloNome=DepartamentoCadastra">Departamentos</a>
    </td>
  </tr>	

  <?php
  //verifica se o usuário pode ver este menu
  if ($dados_usuario["cad_cidade"] == 1)
  {
    ?>

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=CidadeCadastra">Cidades</a>
      </td>
    </tr>

    <?php
  }

  //verifica se o usuário pode ver este menu
  if ($dados_usuario["cad_cursos"] == 1)
  {
    ?>

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=CursoCadastra">Cursos</a>
      </td>
    </tr>	

  <?php
}

//verifica se o usuário pode ver este menu
if ($dados_usuario["cad_tipo_doc"] == 1)
{
  ?>

    <tr>
      <td>
        <div align="center">
          <img src="image/lat_cadastro.gif" width="6" height="10" border="0" />
        </div>
      </td>
      <td>
        <a href="sistema.php?ModuloNome=TipoDocumentoCadastra">Tipos de Documento</a>
      </td>
    </tr>

  <?php
}
?>
</table>
