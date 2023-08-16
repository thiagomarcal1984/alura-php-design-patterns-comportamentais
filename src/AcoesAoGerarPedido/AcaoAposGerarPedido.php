<?php

namespace Alura\DesignPattern\AcoesAoGerarPedido;

use Alura\DesignPattern\Pedido;

interface AcaoAposGerarPedido
{
    public function executarAcao(Pedido $pedido) : void;
}
