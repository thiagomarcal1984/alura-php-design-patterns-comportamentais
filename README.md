# Padrões comportamentais (Gang of Four)
- [x] Chain of Responsibility
- [x] Command
- [ ] Interpreter
- [ ] Iterator
- [ ] Mediator
- [ ] Memento
- [x] Observer
- [x] State
- [x] Strategy
- [x] Template Method
- [ ] Visitor

Fonte: https://pt.wikipedia.org/wiki/Padr%C3%A3o_de_projeto_de_software

# Strategy

## Iniciando o sistema
Para iniciar o projeto, simplesmente criamos o arquivo `composer.json` com as informações de autoload da PSR-4. Se houver dúvidas, use o comando `composer init` para criar o `composer.json` e depois adapte no que for necessário:

```php
// composer.json
{
    "autoload": {
        "psr-4": {
            "Alura\\DesignPatterns\\": "src/"
        }
    }
}
```
Depois de criar o arquivo `composer.json`, execute o comando `composer dump-autoload` para gerar os arquivos PHP da pasta `vendor`.

## Aplicando impostos
Os problemas: 
1. o método `calcula` na classe `CalculadoraDeImpostos` pode crescer muito se mais tipos de imposto forem criados.
2. O método `calcula` precisa funcionar, mesmo que seja fornecido um tipo de imposto inexistente.

```php
<?php

namespace Alura\DesignPattern;

class CalculadoraDeImpostos
{
    public function calcula(Orcamento $orcamento, string $nomeImposto) : float
    {
        switch ($nomeImposto) {
            case 'ICMS':
                // Retorna 10% do valor do orçamento.
                return $orcamento->valor * 0.1;
                break;
            
            case 'ISS':
                return $orcamento->valor * 0.06;
                break;
        }
    }
}

```
## Removendo switch-case com Strategy
A solução é criar uma interface que é implementada por cada tipo de imposto:

```php
// Impostos\Imposto.php
<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Orcamento;

interface Imposto
{
    public function calcula(Orcamento $orcamento) : float;
}
```
```php
// Impostos\Icms.php
<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Orcamento;

class Icms implements Imposto
{
    public function calcula(Orcamento $orcamento) : float
    {
        // Retorna 10% do valor do orçamento.
        return $orcamento->valor * 0.1;
    }
}
```
```php
// Impostos\Iss.php
<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Orcamento;

class Iss implements Imposto
{
    public function calcula(Orcamento $orcamento) : float
    {
        // Retorna 6% do valor do orçamento.
        return $orcamento->valor * 0.06;
    }
}
```
```php
// CalculadoraDeImpostos.php
<?php

namespace Alura\DesignPattern;

use Alura\DesignPattern\Impostos\Imposto;

class CalculadoraDeImpostos
{
    public function calcula(Orcamento $orcamento, Imposto $imposto) : float
    {
        return $imposto->calcula($orcamento);
    }
}
```
> Perceba que o método `calcula` ficou muito mais enxuto.

O padrão Strategy usa 3 tipos de classe: 
1. O Contexto (que usa uma interface de Estratégia);
2. A interface de Estratégia; e
3. A(s) implementação(ões) concreta(s) da interface.

O Contexto não precisa conhecer os vários tipos de implementações possíveis, nem testar com `switch` ou `if` qual o comportamento a ser usado. O Contexto usa a interface de Estratégia, que é implementada por classes concretas.

Leitura complementar sobre o padrão Strategy: https://refactoring.guru/design-patterns/strategy 

# Chain of Responsibility

## Criando a calculadora de descontos
```php
<?php

namespace Alura\DesignPattern;

class CalculadoraDeDescontos
{
    public function calculaDescontos(Orcamento $orcamento) : float
    {
        if ($orcamento->quantidadeItens > 5) {
            return $orcamento->valor * 0.1;
        }

        return 0;
    }
}
```
## Aplicando mais de um desconto
```php
<?php

namespace Alura\DesignPattern;

class CalculadoraDeDescontos
{
    public function calculaDescontos(Orcamento $orcamento) : float
    {
        if ($orcamento->quantidadeItens > 5) {
            return $orcamento->valor * 0.1;
        }

        if ($orcamento->valor > 500) {
            return $orcamento->valor * 0.05;
        }

        return 0;
    }
}
```
Problemas encontrados:
1. O código acima não aplica os dois descontos caso o valor seja maior que 500 e a quantidade seja maior que 5.
2. A ordem para a aplicação dos descontos importa. Então, é complexo aplicar vários if/switch/strategy para estabelecer o desconto que será aplicado.

## Strategy resolve
Os percentuais de desconto poderiam ser delegados para uma implementação do padrão Strategy, mas a ordem e sequência dos descontos ainda precisa ser testada. Logo o padrão Strategy não soluciona o problema da calculadora de descontos.

## Criando a Chain of Responsibility
```php
// Descontos\Desconto.php
<?php

namespace Alura\DesignPattern\Descontos;

use Alura\DesignPattern\Orcamento;

abstract class Desconto
{
    protected ?Desconto $proximoDesconto;

    public function __construct(?Desconto $proximoDesconto)
    {
        $this->proximoDesconto = $proximoDesconto;
    }

    abstract function calculaDesconto(Orcamento $orcamento) : float;
}
```
> Note que a propriedade `$proximoDesconto` está protegida (para que as subclasses consigam enxergá-la). Note também que `$proximoDesconto` é nullable (veja as marcas de interrogação).
```php
// Descontos\DescontoMaisDe5Itens.php
<?php

namespace Alura\DesignPattern\Descontos;

use Alura\DesignPattern\Orcamento;

class DescontoMaisDe5Itens extends Desconto
{
    public function calculaDesconto(Orcamento $orcamento) : float
    {
        $desconto = 0;
        if ($orcamento->quantidadeItens > 5) {
            $desconto = $orcamento->valor * 0.1;
        }
        return $desconto + $this->proximoDesconto->calculaDesconto($orcamento);
    }
}

```
```php
// Descontos\DescontoMaisDe500Reais.php
<?php

namespace Alura\DesignPattern\Descontos;

use Alura\DesignPattern\Orcamento;

class DescontoMaisDe500Reais extends Desconto
{
    public function calculaDesconto(Orcamento $orcamento) : float
    {
        $desconto = 0;
        if ($orcamento->valor > 500) {
            $desconto = $orcamento->valor * 0.05;
        }
        return $desconto + $this->proximoDesconto->calculaDesconto($orcamento);
    }
}

```
```php
// Descontos\SemDesconto.php
<?php

namespace Alura\DesignPattern\Descontos;

use Alura\DesignPattern\Orcamento;

class SemDesconto extends Desconto
{
    public function __construct(){
        parent::__construct(null);
    }

    public function calculaDesconto(Orcamento $orcamento) : float
    {
        return 0;
    }
}
```
> Note que a classe `SemDesconto` finaliza a cadeia, porque ela recebe nulo no seu construtor.
```php
// CalculadoraDeDescontos.php
<?php

namespace Alura\DesignPattern;

use Alura\DesignPattern\Descontos\DescontoMaisDe500Reais;
use Alura\DesignPattern\Descontos\DescontoMaisDe5Itens;
use Alura\DesignPattern\Descontos\SemDesconto;

class CalculadoraDeDescontos
{
    public function calculaDescontos(Orcamento $orcamento) : float
    {
        $desconto5itens = new DescontoMaisDe5Itens(
            new DescontoMaisDe500Reais(
                new SemDesconto()
            )
        );

        $desconto = $desconto5itens->calculaDesconto($orcamento);

        return $desconto;
    }
}
```
> Note que a `CalculadoraDeDescontos` determina qual é a ordem de aplicação do desconto (primeiro os objetos de desconto mais internos retornam valores, depois os descontos mais externos são aplicados).

## Explicando o padrão
Fonte: https://en.wikipedia.org/wiki/Chain-of-responsibility_pattern

Há 3 tipos de classes:
1. Sender: requisita do manipulador o retorno da cadeia de receptores;
2. Manipulador (Handler): é uma superclasse dos receptores concretos, que tem um método abstrato para chamar o próximo receptor da cadeia;
3. Receptor: executa sua operação e chama o próximo receptor para continuar a atender a requisição.

Leitura complementar sobre o padrão Chain of Responsibility: https://refactoring.guru/design-patterns/chain-of-responsibility

# Template Method

## Cálculos condicionais de impostos
São propostas duas classes de impostos fictícios IKCV e ICPP:
```php
<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Impostos\Imposto;
use Alura\DesignPattern\Orcamento;

class Icpp implements Imposto
{
    public function calcula(Orcamento $orcamento): float
    {
        if ($orcamento->valor > 500) {
            return $orcamento->valor * 0.03;
        }
        return $orcamento->valor * 0.02;
    }
}
```
```php
<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Impostos\Imposto;
use Alura\DesignPattern\Orcamento;

class Ikcv implements Imposto 
{
    public function calcula(Orcamento $orcamento): float
    {
        if ($orcamento->valor > 300 && $orcamento->quantidadeItens > 3) {
            return $orcamento->valor * 0.04;
        }
        return $orcamento->valor * 0.025;
    }
}
```
Perceba que a estrutura condicional nas duas classes é semelhante. Na próxima aula veremos como abstrair isso e evitar a duplicação da estrutura do código.

## Extraindo a lógica para métodos privados
O método `calcula` das classes do IKCV e do ICPP pode ser estruturado como um método de template (template method): uma classe abstrata define um algoritmo genérico que vai referenciar métodos abstratos a serem implementados pelas suas subclasses.

A super classe `ImpostoCom2Aliquotas` vai ter o método de template `calcula`:
```php
<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Orcamento;

abstract class ImpostoCom2Aliquotas implements Imposto
{
    public function calcula(Orcamento $orcamento): float
    {
        if ($this->deveAplicarTaxaMaxima($orcamento)) {
            return $this->calculaTaxaMaxima($orcamento);
        }
        return $this->calculaTaxaMinima($orcamento);
    }

    abstract protected function deveAplicarTaxaMaxima(Orcamento $orcamento) : bool;
    abstract protected function calculaTaxaMaxima(Orcamento $orcamento) : float;
    abstract protected function calculaTaxaMinima(Orcamento $orcamento) : float;
}
```
E as subclasses do IKCV e do ICPP não precisam declarar o método calcula, apenas estender a superclasse `ImpostoCom2Aliquotas`:
```php
// ICPP
<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Orcamento;

class Icpp extends ImpostoCom2Aliquotas
{
    protected function deveAplicarTaxaMaxima(Orcamento $orcamento): bool
    {
        return $orcamento->valor > 500;
    }
    
    protected function calculaTaxaMaxima(Orcamento $orcamento): float
    {
        return $orcamento->valor * 0.03;
    }

    protected function calculaTaxaMinima(Orcamento $orcamento): float
    {
        return $orcamento->valor * 0.02;
    }
}
```

```php
// IKCV
<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Orcamento;

class Ikcv extends ImpostoCom2Aliquotas
{
    protected function deveAplicarTaxaMaxima(Orcamento $orcamento): bool
    {
        return $orcamento->valor > 300 && $orcamento->quantidadeItens > 3;
    }

    protected function calculaTaxaMaxima(Orcamento $orcamento): float
    {
        return $orcamento->valor * 0.04;
    }

    protected function calculaTaxaMinima(Orcamento $orcamento): float
    {
        return $orcamento->valor * 0.025;
    }
}
```
## Falando sobre o padrão
Perceba que a superclasse definiu os métodos abstratos como protected, de maneira que as subclasses possam conhecer os métodos abstratos, mas que as demais classes não possam conhecer esses mesmos métodos.

Leitura complementar sobre o padrão Template Method: https://refactoring.guru/design-patterns/template-method

# State

## Adicionando estados ao orçamento
A lógica da função `calculaDescontoExtra` da classe Orçamento começa a ficar muito complexa, pois conhecer o conteúdo da string `estadoAtual` aumenta muito o acomplamento:
```php
<?php

namespace Alura\DesignPattern;

class Orcamento
{
    public int $quantidadeItens;
    public float $valor;
    public string $estadoAtual;

    public function aplicaDescontoExtra()
    {
        $this->valor -= $this->calculaDescontoExtra();
    }

    public function calculaDescontoExtra() : float
    {
        if ($this->estadoAtual == 'EM APROVACAO') {
            return $this->valor * 0.05;
        }
        
        if ($this->estadoAtual == 'APROVADO') {
            return $this->valor * 0.02;
        }

        throw new \DomainException(
            'Orçamentos reprovados e finalizados não podem receber descontos.'
        ); 
    }
}
```

## Valores em string

Ao começar a tratar sobre regras dos estados, foi implementada a verificação do estado atual de um orçamento, utilizando strings.

Há dois problemas nesta abordagem:

1. Strings não possuem comportamento, então precisamos adicionar ifs para realizar o cálculo de desconto extra. Como strings são um tipo primitivo, não poderíamos delegar o cálculo do desconto extra para o valor do `$estadoAtual`. Precisamos adicionar vários ifs na classe de orçamento para isso.
2. É muito fácil digitar o nome de um estado errado e por serem simples strings, a IDE não nos ajudaria. Ter valores com significado no domínio apenas como string é um problema pois, a qualquer momento, podemos digitar o texto errado e isso pode causar uma grande dor de cabeça. Não é um problema fácil de debugar e a IDE não nos ajuda neste caso.

## Extraindo classes de estado
1. Criaremos a superclasse `EstadoOrcamento`, que vai encapsular o estado e conter as operações padrão de mudança de estado.
2. A classe `Orcamento` vai controlar a mudança de estado ao chamar cada método da classe `EstadoOrcamento`.
3. As subclasses de `EstadoOrcamento` vão aplicar os descontos necessários e redefinir as operações padrão definidas na superclasse.

```php
// EstadoOrcamento
<?php

namespace Alura\DesignPattern\EstadosOrcamento;

use Alura\DesignPattern\Orcamento;

abstract class EstadoOrcamento
{
    /**
     * @throws \DomainException
     */
    abstract public function calculaDescontoExtra(Orcamento $orcamento) : float;

    /**
     * @throws \DomainException
     */
    public function aprova(Orcamento $orcamento) 
    {
        throw new \DomainException('Este orçamento não pode ser aprovado.');
    }
    /**
     * @throws \DomainException
     */
    public function reprova(Orcamento $orcamento) 
    {
        throw new \DomainException('Este orçamento não pode ser reprovado.');
    }
    /**
     * @throws \DomainException
     */
    public function finaliza(Orcamento $orcamento) 
    {
        throw new \DomainException('Este orçamento não pode ser finalizado.');
    }
}
```
```php
// Orcamento
<?php

namespace Alura\DesignPattern;

use Alura\DesignPattern\EstadosOrcamento\EstadoOrcamento;
use Alura\DesignPattern\EstadosOrcamento\EmAprovacao;

class Orcamento
{
    public int $quantidadeItens;
    public float $valor;
    public EstadoOrcamento $estadoAtual;
    
    public function __construct()
    {
        $this->estadoAtual = new EmAprovacao();
    }

    public function aplicaDescontoExtra()
    {
        $this->valor -= $this->estadoAtual->calculaDescontoExtra($this);
    }

    public function aprova()
    {
        $this->estadoAtual->aprova($this);
    }

    public function reprova()
    {
        $this->estadoAtual->reprova($this);
    }

    public function finaliza()
    {
        $this->estadoAtual->finaliza($this);
    }
}
```
```php
// Aprovado
<?php

namespace Alura\DesignPattern\EstadosOrcamento;

use Alura\DesignPattern\Orcamento;

class Aprovado extends EstadoOrcamento
{
    public function calculaDescontoExtra(Orcamento $orcamento) : float
    {
        return $orcamento->valor * 0.02;
    }

    public function finaliza(Orcamento $orcamento)
    {
        $orcamento->estadoAtual = new Finalizado();
    }
}
```
```php
// Reprovado
<?php

namespace Alura\DesignPattern\EstadosOrcamento;

use Alura\DesignPattern\Orcamento;

class Reprovado extends EstadoOrcamento
{
    public function calculaDescontoExtra(Orcamento $orcamento) : float
    {
        throw new \DomainException('Um orçamento reprovado não pode receber desconto.'); 
    }

    public function finaliza(Orcamento $orcamento)
    {
        $orcamento->estadoAtual = new Finalizado();
    }
}
```

```php
// EmAprovacao
<?php

namespace Alura\DesignPattern\EstadosOrcamento;

use Alura\DesignPattern\Orcamento;

class EmAprovacao extends EstadoOrcamento
{
    public function calculaDescontoExtra(Orcamento $orcamento) : float
    {
        return $orcamento->valor * 0.05;
    }

    public function aprova(Orcamento $orcamento)
    {
        $orcamento->estadoAtual = new Aprovado();
    }
    
    public function reprova(Orcamento $orcamento)
    {
        $orcamento->estadoAtual = new Reprovado();
    }
}
```

```php
// Finalizado
<?php

namespace Alura\DesignPattern\EstadosOrcamento;

use Alura\DesignPattern\Orcamento;

class Finalizado extends EstadoOrcamento
{
    public function calculaDescontoExtra(Orcamento $orcamento) : float
    {
        throw new \DomainException('Um orçamento finalizado não pode receber desconto.'); 
    }
}
```

## Princípio de Substituição de Liskov
Superclasses de estado podem conter métodos que apenas lançam exceção, o que pode ferir o princípio de substituição de Liskov.

Em resumo, o princípio de Substituição de Liskov diz que, uma vez que uma assinatura de método for definida numa superclasse, suas subclasses DEVEM se comportar conforme proposto, e não lançar exceções.

Leitura complementar sobre o padrão State: https://refactoring.guru/design-patterns/state

# Command

## Gerando um pedido
Criamos uma classe `Pedido`:

```php
<?php

namespace Alura\DesignPattern;

class Pedido
{
    public string $nomeCliente;
    public \DateTimeInterface $dataFinalizacao;
    public Orcamento $orcamento;
}
```

E criamos uma CLI para preencher um pedido: 
```php
<?php
require 'vendor/autoload.php';

use Alura\DesignPattern\{Orcamento, Pedido};

// $argv contém os valores fornecidos após o nome do arquivo .php:
// php gera-pedido.php 1200.5 7 'Teste'
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
```
Mas e se precisássemos de fazer o preenchimento em uma página Web? A tendência seria copiar/colar o código e adaptar. Porém isso induz a duplicação de código.

## Criando um Command
A interface `Command`:
```php
<?php

namespace Alura\DesignPattern;

interface Command
{
    public function execute();
}
```
Uma implementação de `Command` (classe `GerarPedido`):
```php
<?php

namespace Alura\DesignPattern;

class GerarPedido implements Command
{
    private float $valorOrcamento;
    private int $numeroItens;
    private string $nomeCliente;
    
    public function __construct(
        float $valorOrcamento,
        int $numeroItens,
        string $nomeCliente
    )
    {
        $this->valorOrcamento = $valorOrcamento;
        $this->numeroItens = $numeroItens;
        $this->nomeCliente = $nomeCliente;
    }

    public function execute() 
    {
        $orcamento = new Orcamento();
        $orcamento->quantidadeItens = $this->numeroItens;
        $orcamento->valor = $this->valorOrcamento;
        
        $pedido = new Pedido();
        $pedido->dataFinalizacao = new \DateTimeImmutable();
        $pedido->nomeCliente = $this->nomeCliente;
        $pedido->orcamento = $orcamento;

        var_dump($pedido);
    }
}
```
A invocação de `GerarPedido` no script `gera-pedido.php`
```php
<?php
require 'vendor/autoload.php';

use Alura\DesignPattern\{GerarPedido, Orcamento, Pedido};

$valorOrcamento = $argv[1];
$numeroItens = $argv[2];
$nomeCliente = $argv[3];

$gerarPedido = new GerarPedido($valorOrcamento, $numeroItens, $nomeCliente);
$gerarPedido->execute();
```
Um detalhe: os dados que representam o comando e o método que usa esses dados estão declarados na mesma classe de comando (`GerarPedido`). Isso é conveniente?

## Command Handlers
Com a lógica atual para desenvolvimento de software, é interessante separar a representação dos dados da classe que utiliza esses dados para reduzir o acoplamento. Assim, usamos um `Command` para conter a representação e um `CommandHandler` para operar sobre essa representação.

```php
// GerarPedido (alterada)
<?php

namespace Alura\DesignPattern;

class GerarPedido
{
    private float $valorOrcamento;
    private int $numeroItens;
    private string $nomeCliente;
    
    public function __construct(
        float $valorOrcamento,
        int $numeroItens,
        string $nomeCliente
    )
    {
        $this->valorOrcamento = $valorOrcamento;
        $this->numeroItens = $numeroItens;
        $this->nomeCliente = $nomeCliente;
    }
    
    // Getters
    public function getValorOrcamento():  float
    {
        return $this->valorOrcamento;
    } 
    public function getNumeroItens():  int
    {
        return $this->numeroItens;
    } 
    public function getNomeCliente():  string
    {
        return $this->nomeCliente;
    } 
}
```

```php
// GerarPedidoHandler
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
```

```php
// gera-pedido.php (alterado)
<?php
require 'vendor/autoload.php';

use Alura\DesignPattern\{GerarPedido, GerarPedidoHandler, Orcamento, Pedido};

$valorOrcamento = $argv[1];
$numeroItens = $argv[2];
$nomeCliente = $argv[3];

$gerarPedido = new GerarPedido($valorOrcamento, $numeroItens, $nomeCliente);
$gerarPedidoHandler = new GerarPedidoHandler();
$gerarPedidoHandler->execute($gerarPedido);
```

Leitura complementar sobre o padrão Command: https://refactoring.guru/design-patterns/command

# Observer

## Ações ao gerar um pedido
Perceba a redundância na execução do método `executarAcao` em cada classe no código abaixo:
```php
<?php

namespace Alura\DesignPattern;

use Alura\DesignPattern\AcoesAoGerarPedido\CriarPedidoNoBanco;
use Alura\DesignPattern\AcoesAoGerarPedido\EnviarPedidoPorEmail;
use Alura\DesignPattern\AcoesAoGerarPedido\LogGerarPedido;

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

        $pedidoRepository = new CriarPedidoNoBanco();
        $logGerarPedido = new LogGerarPedido();
        $enviarPedidoPorEmail = new EnviarPedidoPorEmail();
        
        // Perceba a redundância na execução do método `executarAcao` em cada classe.
        $pedidoRepository->executarAcao($pedido);
        $logGerarPedido->executarAcao($pedido);
        $enviarPedidoPorEmail->executarAcao($pedido);
    }
}
```
## Adicionando ações como observers
Neste padrão é importante o conceito de Subject (sujeito) e de Observers (observadores do sujeito). Certas ações no sujeito disparam uma ação em cada observador "cadastrado" nesse sujeito.

A interface do observador:
```php
<?php

namespace Alura\DesignPattern\AcoesAoGerarPedido;

use Alura\DesignPattern\Pedido;

interface AcaoAposGerarPedido
{
    public function executarAcao(Pedido $pedido) : void;
}
```
Código do sujeito `GerarPedidoHandler`:
```php
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
```
Código que invoca o sujeito (`gera-pedido.php`): 
```php
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
$gerarPedidoHandler->adicionarAcaoAoGerarPedido(new CriarPedidoNoBanco());
$gerarPedidoHandler->adicionarAcaoAoGerarPedido(new EnviarPedidoPorEmail());
$gerarPedidoHandler->adicionarAcaoAoGerarPedido(new LogGerarPedido());
$gerarPedidoHandler->execute($gerarPedido);
```
> Note que as classes `CriarPedidoNoBanco`, `EnviarPedidoPorEmail` e `LogGerarPedido` implementam a interface observer chamada `AcaoAposGerarPedido`.

## Observers no PHP
O PHP tem a interface `SplSubject`, que representa o sujeito que será observado (ele força a implementação dos métodos `attach`, `detach` e `notify`).

O PHP também tem a interface `SplObserver`, que representa os obseravadores (ele força a implementação do método `update`).

Código do subject: 
```php
<?php

namespace Alura\DesignPattern;

use Alura\DesignPattern\AcoesAoGerarPedido\AcaoAposGerarPedido;
use SplObserver;

class GerarPedidoHandler implements \SplSubject
{
    /** @var \SplObserver[] $acoesAposGerarPedido */
    private array $acoesAposGerarPedido = [];

    public Pedido $pedido;

    public function __construct(/* PedidoRepository, MailService */)
    {
        // Repare que os parâmetros contém os objetos injetados por DI.
    }

    public function attach(SplObserver $observer): void
    {
        $this->acoesAposGerarPedido[] = $observer;
    }
    
    public function detach(SplObserver $observer): void
    {
        // Remover um observer da lista de observers.
    }

    public function notify(): void
    {
        foreach($this->acoesAposGerarPedido as $acao) {
            $acao->update($this);
        }
    }
    
    public function execute(GerarPedido $gerarPedido)
    {
        $orcamento = new Orcamento();
        $orcamento->quantidadeItens = $gerarPedido->getNumeroItens();
        $orcamento->valor = $gerarPedido->getValorOrcamento();
        
        $this->pedido = new Pedido();
        $this->pedido->nomeCliente = "Teste";
        $this->notify();
    }
}
```
Código do observer `EnviarPedidoPorEmail`:
```php
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
```
Código que invoca o subject (`gera-pedido.php`): 
```php
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
```

Leitura complementar sobre o padrão Observer: https://refactoring.guru/design-patterns/observer

Já para conhecer melhor as interfaces do próprio PHP: https://www.php.net/manual/pt_BR/class.splobserver.php.

# Iterator
## Visualizando uma lista de orçamentos
Uma boa prática é criar coleções que forcem a tipagem. No PHP isso é feito criando uma classe de coleção.

O código abaixo ainda não implementou essa classe, e quebra durante a execução:

```php
// lista-orcamentos.php
<?php

require_once 'vendor/autoload.php';

use Alura\DesignPattern\Orcamento;

$listaOrcamentos = [];

$orcamento1 = new Orcamento();
$orcamento1->quantidadeItens = 7;
$orcamento1->aprova();
$orcamento1->valor = 1500.75;

$orcamento2 = new Orcamento();
$orcamento2->quantidadeItens = 3;
$orcamento2->reprova();
$orcamento2->valor = 150;

$orcamento3 = new Orcamento();
$orcamento3->quantidadeItens = 5;
$orcamento3->aprova();
$orcamento3->finaliza();
$orcamento3->valor = 1350;

$listaOrcamentos = [
    $orcamento1, 
    $orcamento2,
    $orcamento3,
    'Esta string quebra a iteração na lista.'
];

foreach ($listaOrcamentos as $orcamento) {
    echo  "Valor: " . $orcamento->valor . PHP_EOL;
    echo "Estado: " . get_class($orcamento->estadoAtual) . PHP_EOL;
    echo  "Qtde. Itens: " . $orcamento->quantidadeItens . PHP_EOL;
    echo PHP_EOL;
}
```
## Representando uma coleção de orçamentos
Criação da classe de lista de orçamentos:
```php
<?php

namespace Alura\DesignPattern;

class ListaDeOrcamentos
{
    /** @var Orcamento[] */
    private array $orcamentos;

    public function __construct()
    {
        $this->orcamentos = [];
    }

    public function addOrcamento(Orcamento $orcamento)
    {
        $this->orcamentos[] = $orcamento;
    }

    public function orcamentos() : array
    {
        return $this->orcamentos;
    }
}
```
Uso da classe no arquivo `lista-orcamentos.php`:
```php
<?php

require_once 'vendor/autoload.php';

use Alura\DesignPattern\ListaDeOrcamentos;
use Alura\DesignPattern\Orcamento;

$listaOrcamentos = [];

$orcamento1 = new Orcamento();
$orcamento1->quantidadeItens = 7;
$orcamento1->aprova();
$orcamento1->valor = 1500.75;

$orcamento2 = new Orcamento();
$orcamento2->quantidadeItens = 3;
$orcamento2->reprova();
$orcamento2->valor = 150;

$orcamento3 = new Orcamento();
$orcamento3->quantidadeItens = 5;
$orcamento3->aprova();
$orcamento3->finaliza();
$orcamento3->valor = 1350;

$listaOrcamentos = new ListaDeOrcamentos();

$listaOrcamentos->addOrcamento($orcamento1);
$listaOrcamentos->addOrcamento($orcamento2);
$listaOrcamentos->addOrcamento($orcamento3);

foreach ($listaOrcamentos->orcamentos() as $orcamento) {
    echo  "Valor: " . $orcamento->valor . PHP_EOL;
    echo "Estado: " . get_class($orcamento->estadoAtual) . PHP_EOL;
    echo  "Qtde. Itens: " . $orcamento->quantidadeItens . PHP_EOL;
    echo PHP_EOL;
}
```
> O problema dessa abordagem é o acesso direto ao array dentro do `foreach`. A ideia seria obter um orçamento na medida em que iteramos o array.
