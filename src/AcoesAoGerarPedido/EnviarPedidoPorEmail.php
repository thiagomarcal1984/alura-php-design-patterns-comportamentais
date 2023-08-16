<?php

namespace Alura\DesignPattern\AcoesAoGerarPedido;

use Alura\DesignPattern\Pedido;

class EnviarPedidoPorEmail
{
    public function executarAcao(Pedido $pedido) : void
    {
        echo "Enviando e-mail do pedido gerado";
    }
}
