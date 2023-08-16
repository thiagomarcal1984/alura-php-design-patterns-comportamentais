<?php

namespace Alura\DesignPattern;

use Alura\DesignPattern\AcoesAoGerarPedido\AcaoAposGerarPedido;

class GerarPedidoHandler
{
        /** @var AcaoAposGerarPedido[] $acoesAposGerarPedido */
        private array $acoesAposGerarPedido = [];

    public function __construct(/* PedidoRepository, MailService */)
    {
        // Repare que os parâmetros contém os objetos injetados por DI.
    }

    public function adicionarAcaoAoGerarPedido(AcaoAposGerarPedido $acao)
    {
        $this->acoesAposGerarPedido[] = $acao;
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

        // Remoção das redundâncias.
        foreach($this->acoesAposGerarPedido as $acao) {
            $acao->executarAcao($pedido);
        }
    }
}
