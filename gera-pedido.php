<?php
require 'vendor/autoload.php';

use Alura\DesignPattern\{Orcamento, Pedido};

$valorOrcamento = $argv[1];
$numeroItens = $argv[2];
$nomeCliente = $argv[3];

$orcamento = new Orcamento();
$orcamento->quantidadeItens = $numeroItens;
$orcamento->valor = $valorOrcamento;

$pedido = new Pedido();
$pedido->dataFinalizacao = new \DateTimeImmutable();
$pedido->nomeCliente = $nomeCliente;
$pedido->orcamento = $orcamento;

var_dump($pedido);
