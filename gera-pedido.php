<?php
require 'vendor/autoload.php';

use Alura\DesignPattern\{GerarPedido, Orcamento, Pedido};

$valorOrcamento = $argv[1];
$numeroItens = $argv[2];
$nomeCliente = $argv[3];

$gerarPedido = new GerarPedido($valorOrcamento, $numeroItens, $nomeCliente);
$gerarPedido->execute();
