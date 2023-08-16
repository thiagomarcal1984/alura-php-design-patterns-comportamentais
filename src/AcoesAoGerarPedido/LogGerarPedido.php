<?php

namespace Alura\DesignPattern\AcoesAoGerarPedido;

use Alura\DesignPattern\Pedido;

class LogGerarPedido
{
    public function executarAcao(Pedido $pedido) : void
    {
        echo "Gerando log do pedido";
    }
}
