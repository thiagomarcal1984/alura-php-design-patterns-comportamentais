<?php

namespace Alura\DesignPattern\AcoesAoGerarPedido;

use Alura\DesignPattern\Pedido;

class CriarPedidoNoBanco
{
    public function executarAcao(Pedido $pedido) : void
    {
        echo "Salvando pedido no banco de dados";
    }
}
