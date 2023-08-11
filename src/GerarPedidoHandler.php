<?php

namespace Alura\DesignPattern;

class GerarPedidoHandler
{
    public function __construct(/* PedidoRepository, MailService */)
    {
        // Repare que os parâmetros contém os objetos injetados por DI.
    }

    public function execute(GerarPedido $gerarPedido)
    {
        $orcamento = new Orcamento();
        $orcamento->quantidadeItens = $gerarPedido->getNumeroItens();
        $orcamento->valor = $gerarPedido->getValorOrcamento();
        
        $pedido = new Pedido();
        $pedido->dataFinalizacao = new \DateTimeImmutable();
        $pedido->nomeCliente = $gerarPedido->getNomeCliente();
        $pedido->orcamento = $orcamento;

        // PedidoRepository
        echo "Cria pedido no banco de dados" . PHP_EOL;
        
        // MailService
        echo "Envia e-mail para o cliente" . PHP_EOL;
    }
}
