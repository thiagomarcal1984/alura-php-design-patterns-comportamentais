<?php
require 'vendor/autoload.php';

use Alura\DesignPattern\{GerarPedido, GerarPedidoHandler, Orcamento, Pedido};

$valorOrcamento = $argv[1];
$numeroItens = $argv[2];
$nomeCliente = $argv[3];

$gerarPedido = new GerarPedido($valorOrcamento, $numeroItens, $nomeCliente);
$gerarPedidoHandler = new GerarPedidoHandler();
$gerarPedidoHandler->execute($gerarPedido);
