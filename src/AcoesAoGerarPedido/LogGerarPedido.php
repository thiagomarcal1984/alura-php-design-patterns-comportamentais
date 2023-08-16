<?php

namespace Alura\DesignPattern\AcoesAoGerarPedido;

use Alura\DesignPattern\Pedido;

class LogGerarPedido implements \SplObserver
{
    public function update(\SplSubject $subject): void
    {
        echo "Gerando log do pedido" . PHP_EOL;
    }
}
