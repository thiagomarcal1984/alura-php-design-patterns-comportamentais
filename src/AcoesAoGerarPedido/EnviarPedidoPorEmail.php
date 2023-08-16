<?php

namespace Alura\DesignPattern\AcoesAoGerarPedido;

use Alura\DesignPattern\Pedido;

class EnviarPedidoPorEmail implements AcaoAposGerarPedido
{
    public function executarAcao(Pedido $pedido) : void
    {
        echo "Enviando e-mail do pedido gerado" . PHP_EOL;
    }
}
