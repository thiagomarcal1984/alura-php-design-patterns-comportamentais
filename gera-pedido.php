<?php
require 'vendor/autoload.php';

use Alura\DesignPattern\{GerarPedido, GerarPedidoHandler, Orcamento, Pedido};
use Alura\DesignPattern\AcoesAoGerarPedido\CriarPedidoNoBanco;
use Alura\DesignPattern\AcoesAoGerarPedido\EnviarPedidoPorEmail;
use Alura\DesignPattern\AcoesAoGerarPedido\LogGerarPedido;

$valorOrcamento = $argv[1];
$numeroItens = $argv[2];
$nomeCliente = $argv[3];

$gerarPedido = new GerarPedido($valorOrcamento, $numeroItens, $nomeCliente);
$gerarPedidoHandler = new GerarPedidoHandler();
$gerarPedidoHandler->attach(new CriarPedidoNoBanco());
$gerarPedidoHandler->attach(new EnviarPedidoPorEmail());
$gerarPedidoHandler->attach(new LogGerarPedido());
$gerarPedidoHandler->execute($gerarPedido);
