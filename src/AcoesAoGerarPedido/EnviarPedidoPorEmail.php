<?php

namespace Alura\DesignPattern\AcoesAoGerarPedido;

use Alura\DesignPattern\Pedido;

class EnviarPedidoPorEmail implements \SplObserver
{
    public function update(\SplSubject $subject): void
    {
        echo "Nome do cliente: " . $subject->pedido->nomeCliente . ". ";
        echo "Enviando e-mail do pedido gerado" . PHP_EOL;
    }
}
