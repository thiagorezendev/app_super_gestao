# Anotações Curso Laravel

## Criar projeto

`composer create-project laravel/laravel nome-do-projeto`

* Lembrar de liberar as extensões necessárias no php.ini para que seja possível baixar as extensões da fonte! Não fazer isso pode fazer o projeto pesar muito.

## Artisan Console

Artisan é um console de linha de comando usado para auxiliar na criação do projeto, ele pode ser usado para criar e executar Migrations, Models, Views e Controllers, além de ser usado para muitas outras coisas como rodar o servidor da aplicação.

### Comando Uteis

`php artisan serve` -> Sobe a aplicação para o server.

`php artisan make:controller CoisaController` -> Cria um controller vazio.

`php artisan make:view site.contato` -> Cria uma view vazia.

`php artisan make:model Coisa` -> Cria uma classe Model para coisa.

`php artisan make:model Coisa -m` -> Cria uma classe Model para coisa e uma migration para a criação da tabela no banco de dados já com as regras de plural.

`php artisan make:migration create_coisa_table` -> Cria uma migration já com up() e down() e um Schema::create com id() e timestamps(), o nome deve ter o nome da tabela e mais ou menos a alteração que se vai fazer.

`php artisan migrate` -> roda as migrations que ainda não foram rodadas.

`php artisan migrate:rollback` -> faz o down da ultima migrate.

`php artisan migrate:status` -> mostra informações sobre as migrations.

`php artisan migrate:reset` -> faz o rollback de todas as migrations.

`php artisan migrate:refresh` -> faz o rollback das migrations e depois executa o comando migrate novamente.

`php artisan migrate:fresh` -> faz o drop das migrations e depois executa o comando migrate novamente.

(há alterações para todos esses comandos)

### Regras que o Laravel usa para criação do nome das Migrations e posteriormente seu uso no banco de dados

Se o nome do modelo é, por exemplo "ClienteFrequente" (geralmente se cria modelos com nomes no singular) o nome da tabela criada no banco de dados com a migration será cliente_frequentes, o Camel case é quebrando em _ e é adicionado um s no final, mas nem sempre esse forma é a mais correta pois o plural pode ficar errado, para consertar isso se troca manualmente o nome da migration e da tabela e especifica a tabela a ser usada pelo Model. Outro exemplo: o model "Fornecedor" seria criado como "fornecedors" na migration, mas pode se mudar para "fornecedores" e especificar no model.

## Estrutura de pastas

**Pasta App:** Lógica da aplicação, onde ficam os controllers, models, requests, etc.

**Pasta Bootstrap e Config:** Configurações e métodos para rodar o framework.

**Pasta Database:** Onde ficam as migrations, seeders e tudo relacionado ao banco de dados.

**Pasta Public:** Contém o index.php que vai rodar quando sua aplicação iniciar, além dos assets, como imagens, css e scripts js.

**Pasta Resources:** Contém as views e o css e js cru.

**Pasta Routes:** Contém as rotas da aplicação.

**Pasta Storage:** Contém os logs e classes do framework.

**Pasta Tests:** Contém os testes automatizados.

**Pasta Vendor:** Contém todas as dependências do framework.

## Routes

As routes são uma forma simples disponibilizada pelo Laravel para controlar o fluxo de navegação da sua aplicação, elas recebem um método que é um verbo http e realizam uma função de callback, retornam uma view ou chamam um método do controler, mas o ideal é que a rota chame um controller correspondente e esse controller decida o que fazer com a requisição feita pelo usuário através das rotas.

Verbos http mais comuns: 
- get
- post
- put
- patch
- delete
- options

Exemplos:

```
Route::get('/', [PrincipalController::class,'index'])->name('site.home');
```

Nessa rota é usado o verbo http get, e é acessada quando site não tem nada a mais na url além de seu endereço, ou seja é a rota para a "raiz", da aplicação. Nesse caso, quando o usuário acessar a raiz do site, a rota chamara o PrincipalController, mais especificamente no método index() e realizará suas instruções. Além disso, é possível dar um nome para a rota, nesse caso, "site.home", permitindo que ela seja chamada em uma view, por exemplo, de forma mais fácil, usando `route('site.home')`.

Há duas formas de redirecionar um rota para outra, sendo a primeira:

```
Route::redirect('/home', '/');
```

e a segunda: 

```
Route::get('/home', function () {
    return redirect()->route('site.home');
}); 
```

Nos dois casos, ao acessar o site /home será automaticamente redirecionado para a rota /.

Se existir um prefixo comum entre as rotas é possível fazer um grupo, exemplo:

```
Route::group(["prefix"=> "app"], function () {
    Route::get("/clientes", function() {echo'Clientes';})->name('app.clientes');
    Route::get("/fornecedores", [FornecedorController::class, 'index'])->name('app.fornecedores');
    Route::get("/produtos", function() {echo'Produtos';})->name('app.produtos');
});
```

Nesse caso todas essas rotas serão app/alguma-coisa, exemplo: app/clientes.

É possível passar parâmetros para as rotas, para serem usados na função, no controller ou nas views chamadas, sendo que pelo método get os parâmetros ficam visíveis na url e no método post não (há outras diferenças entre os dois métodos, mas não vem muito ao caso).

Exemplo:

```
Route::get('/{id}', [CoisaController::class, 'show'])->name('app.show');
```

nesse caso, o parâmetro id, está sendo passado para o Controller na função `show($id)` por meio do método get.

É possível também, passar parâmetros opcionais usando uma ? antes do nome do parâmetro como em:

```
Route::get('/user/{name?}', function (?string $name = null) {
    return $name;
});
```

Lembre-se de colocar um valor padrão para o parâmetro para que seja passado um valor caso não seja passado o parâmetro em si. Além disso, use só um ou poucos parâmetros opcionais pois pode gerar confusão na ordem e os parâmetros irem para o lugar errado no caso de serem passados vários parâmetros.

``` 
Route::post('/{id}', [CoisaController::class, 'update'])->name('app.update');
```

Parâmetro passado com o método post, não será passado pela url.

### Expressões regulares para os parâmetros

```
Route::get('/user/{id}/{name}', function (string $id, string $name) {
    // ...
})->where(['id' => '[0-9]+', 'name' => '[a-z]+']);
```

Nessa rota, ele só vai achar o caminho se o id for numérico, aceitando mais de um número e o nome for alfabético e minusculo. 


### Rota de contingência


**Obs:** no PHP, o operador (->) é conhecido informalmente como seta, o manual chama ele de T_OBJECT_OPERATOR serve para acessar propriedades ou métodos de um objeto, para membros estáticos(aqueles que pertencem/compartilhados a classe) utiliza-se o :: Paamayim Nekudotayim.

Outras linguagens como java e C# utilizam ponto no lugar(.) no lugar de (->).

## Controllers

Controllers são a camada de aplicação para onde os requests feitos pelo usuário são encaminhados para serem resolvidos, é ele que deve chamar as views, criar, ler, atualizar e deletar os dados do banco de dados, etc.

### Chamando uma view passando parâmetros

Há duas formas, passando um array associativo ou o método `compact()`, exemplo:

```
$coisas = Coisa::all();
return view('coisas.list', compact('coisas'));
```



## Views

Parte Visual da aplicação onde o usuário vai interagir e fazer suas requisições. 

Blade: motor de renderização de views do Laravel, ele permite que você reutilize código de forma muito mais fácil, além de permitir criar layouts e usar o php em conjunto com o HTML sem muita gambiarra e com a sintaxe muito mais limpa.

### Sintaxe Blade

No blade para usar php usa-se o operador @ seguido da função que deseja-se utilizar, por exemplo, para usar um bloco php puro, usa-se no inicio `@php` e `@endphp` no final, além disso, para usar a tag php só para o echo (`<?= 'texto' ?>`) usa-se `{{ 'texto' }}`.

#### Diretivas do Blade:

**Obs:** sim, é possível usar tags php entre as diretivas.

**@if/@else** -> Equivalente ao if, else do php, exemplo:

```
@if (count($records) === 1)
    I have one record!
@elseif (count($records) > 1)
    I have multiple records!
@else
    I don't have any records!
@endif
```

**@unless** -> Equivalente ao if com o operador de negação != do php, exemplo:

```
@unless (Auth::check())
    You are not signed in.
@endunless
```

**@isset** -> equivalente ao isset do php, mas como se fosse um if isset exemplo:

```
@isset($records)
    // $records is defined and is not null...
@endisset
```

**@empty** -> equivalente ao do php, mas como se fosse um if empty exemplo:

```
@empty($records)
    // $records is "empty"...
@endempty
```

**@switch/case** -> equivalente ao Switch/Case do php, exemplo:

```
@switch($i)
    @case(1)
        First case...
        @break
 
    @case(2)
        Second case...
        @break
 
    @default
        Default case...
@endswitch
```

**@for** -> equivalente ao loop for do php, exemplo:

```
@for ($i = 0; $i < 10; $i++)
    The current value is {{ $i }}
@endfor
```

**@while** -> equivalente ao loop while do php, exemplo:

```
@while (true)
    <p>I'm looping forever.</p>
@endwhile
```

**@foreach** -> equivalente ao loop foreach do php, exemplo:

```
@foreach ($users as $user)
    <p>This is user {{ $user->id }}</p>
@endforeach
```

**@forelse** -> equivalente ao loop foreach do php mas com a possibilidade de desviar o loop para uma diretiva empty caso o array passado como parâmetro estiver vazio, exemplo:

```
@forelse ($users as $user)
    <li>{{ $user->name }}</li>
@empty
    <p>No users</p>
@endforelse
```

Pode ser de interesse: variável $loop.

### Subviews

Com o Blade é possível reutilizar códigos comuns entre as views de forma extremamente simples e elegante, usando subviews criando layouts, partials, templates e components.

**@include** -> equivalente ao include do php, forma mais simples de reutilizar código php, todo o corpo do template é inserido no local da view onde foi incluído, ou seja, é usado para criar views "parciais" exemplo:

```
@include('layout.partials.menu');
```

**@extends** -> forma sofisticada de criar Templates no Blade, uma view que estende um template pode ter partes do seu código inseridas dentro de um layout, para isso, na view de template, deve ser usar a diretiva `@yield('nome-da-section')` onde se deseja injetar código de outra view. Já na view que o layout vai estender, deve-se ter a diretiva `@extends('nome-do-layout')` e o código a ser injetado deve estar entre as diretivas `@section('nome-da-section')` e `@endsection`, também é possível passar só um texto simples usando `@section('nome-da-section', 'texto'), exemplo: 

```
{{-- No layout: --}}

<title>Super Gestão - @yield('titulo')</title>

<body>

    @yield('main');

</body>

{{-- Na view a ser entendida --}}

@extends('layout.head')
@section('titulo', 'Home')

@section('main')

    {{-- Todo o conteúdo que se deseja passar --}}

@endsection

{{-- *Lembrando que toda que a view vista é 
a view de layout acrescida das sections* --}}
```

**@component** -> forma mais sofisticada de incluir "partials" chamados de "components" quando se precisa inserir mais conteúdo ou passar parâmetros, que podem ser passadas por uma array ou por meio do método compact, as variáveis passadas serão acessíveis no corpo do componente, para usar, bastar chamar a diretiva `@component('nome-do-componente', compact('variavel'))` e `@endcomponent` sendo que dentro da diretiva pode-se colocar conteúdo que será passado como uma variável chamada `$slot` e pode ser colocada onde se desejar, exemplo:

```
@component('layout.components.form', ['style' => 'borda-branca texto-branco'])
    <p> Responderemos o mais rápido possível! </p>
@endcomponent
```
**Obs:**
- Tudo colocado na pasta public pode ser acessado nas views como `asset('Nome do asset')`.
- Para colocar um valor default em uma variável caso ela não tenha sido iniciada usa-se ??.
- Para enviar formulários via post tem que se usar a diretiva @csrf dentro do corpo do form.

## Models

Models são representações, objetos, da tabela do banco de dados dentro do código no sistema, neles devem conter todos os dados que a tabela correspondente pode receber, já que o que vai ser inserido na tabela será um objeto do tipo do model, além de possíveis regras de negócio da empresa que está desenvolvendo a aplicação.

## Migrations

Forma simples de criar tabelas no banco de dados por meio do Laravel e manter elas sempre atualizadas em todos os ambientes de desenvolvimento, sem precisar ficar exportando e mexendo com código MySQL.

Para mexer com o Banco de Dados, a primeira coisa que se deve fazer é mexer nas configurações e colocar o banco de dados e as informações corretas no arquivo .env, exemplo:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

**Estrutura de uma migration:** Método `up()`: código executado quando o comando `artisan migrate` é rodado. Método `down()`: código executado quando o comando `artisan migrate:rollback` é rodado.

**Criando Tabelas:** é usado o método estático create da classe Schema, exemplo:

```
Schema::create('coisas', function (Blueprint $table) {
    $table->id();
    $table->string('nome', 50);
    $table->text('descricao')->nullable();
    $table->integer('peso')->nullable();
    $table->timestamps();
});
```

Observe que foram utilizados o modificador `nullable()` em alguns campos da tabela, isso significa que aquele campo em específico pode ser deixado em branco, também é possível passar um valor padrão com o modificador `default()`.

**Alterando Tabelas:** é usado o método estático `table()` da classe Schema, exemplo:

```
// Para adicionar campos
Schema::table('coisas', function (Blueprint $table) {
    $table->double('preco')->after('nome'); // adiciona depois do nome ao invés de no final
    $table->integer('estoque')->after('preco')->nullable();
});

// Para excluir campos:

Schema::table('coisas', function (Blueprint $table) {
    $table->dropColumn(['preco','estoque']);
});
```

**Adicionando Chaves Estrangeiras:** um campo do mesmo tipo e mesmo tamanho é colocado na tabela com o objetivo de referenciar a chave primaria (no caso, id) de outra tabela, posteriormente é usado o método `foreign()` para fazer a referência, exemplo:

```
$table->foreign('coisa_id')->references('id')->on('coisas');
$table->unique('coisas_id'); // caso seja um relacionamento 1 para 1, caso não seja, basta omitir
```

**Excluindo a tabela:** é usado o método dropIfExists(), exemplo:

```
public function down(): void {
    Schema::dropIfExists('coisas');
}
```

## Eloquent ORM

Mapeamento Objeto Relacional nativo do Laravel, ligação entre o paradigma de orientação a objetos e o banco de dados relacional, com ele é possível, inserir, ler, atualizar e deletar dados do banco de dados com muito mais facilidade, já que as funções já estão prontas.

### Inserindo dados no Banco de Dados

**Método save():** Passando um objeto de um model que tem uma migration correspondente (nome da migration precisa está de acordo com as regras ou identificado dentro do Model para dar certo) ele consegue salvar os dados contidos nesse objeto na tabela correspondente.

**Mudando o nome da tabela relacionada ao model:** caso haja necessidade, basta adicionar essa linha no Model correspondente:

```
protected $table = 'nome_correto_da_tabela';
```

**Método create():** Método estático da classe Model que recebe um array associativo como parâmetro, com os dados que estão definidos como fillable no Model, para inserir um registro do Model na tabela correspondente no banco de dados. Exemplo:

```
// No Model:

protected $fillable = ['nome', 'descricao', 'peso', 'preco', 'estoque'];

// Create:

Coisa::create([
    'nome' => 'coisa',
    'descricao' => 'coisa bem louca',
    'peso' => 'levemente pesado',
    'preco' => 13.90,
    'estoque' => 10
]);
```

### Selecionando dados do Banco de Dados

**Método all():** Método estático da classe Model que retorna uma coleção de objetos do tipo do model com todos os registros da tabela correspondente no banco de dados.

**Obs:** Coleções podem ser convertidas para array usando o método `toArray()`

**Método find():** Método estático da classe Model que recebe uma ou mais chaves primárias dos registros a serem buscados e retorna um objeto (caso seja buscado só uma chave primária) ou uma coleção de objetos do tipo do model com os registros encontrados com a chave primária passada como parâmetro na tabela correspondente no banco de dados.

**Método where():** Método para montagem de queries para realizar pesquisas mais complexas no banco de dados, podendo usar operadores lógicos. O método recebe 3 parâmetros, o nome da coluna a ser comparada, o operador e o valor que se deseja comparar. Ao ser usado, retorna um builder, para que seja possível continuar a elaboração da query, ao usar o método `get()` se obtém a coleção com o resultado.

**Métodos whereIn() e whereNotIn:** Método para montagem de queries, o método recebe dois parâmetros, uma coluna do banco de dados e um array com a quantidade de parâmetros que forem necessários, os métodos retornam os dados contidos na determinada coluna que contém, ou não, os valores passados como parâmetros.

**Métodos whereBetween() e whereNotBetween:** Método para montagem de queries, o método recebe dois parâmetros, uma coluna do banco de dados e um array com dois valores, os métodos retornam os dados contidos na determinada coluna que estão, ou não, entre os valores passados como parâmetros.

**Métodos whereNull() e whereNotNull:** Método para montagem de queries, o método recebe como parâmetro uma coluna do banco de dados, os métodos retorna os dados contidos na determinada coluna que estão, ou não, com o valor null.

**Métodos whereDate(), whereTime(), whereYear(), whereMonth() e whereDay():** Método para montagem de queries, o método recebe dois parâmetros, uma coluna do banco de dados do tipo, date, time ou timestamp e uma data, hora, ano, mes ou dia, os métodos retornam os dados contidos na determinada coluna que tem a determinada data, hora, ano, mes ou dia passado como parâmetro.

**Métodos whereColumn():** Método para montagem de queries, funciona da mesma forma que o where, mas com a diferença que compara o valor de duas colunas, o método recebe como parâmetro duas colunas do banco de dados e um operador, o método retorna os dados da tabela onde as duas colunas passadas como parâmetro tiverem o valor comparado correto.

**Obs:** 
- Usar um where seguido do outro na hora que fazer uma consulta realizada uma consulta com dois wheres ligados por um and, se desejar utilizar uma consulta com um or, há a query `orWhere()`, todas os outros métodos where seguem a mesma lógica (em tudo, sendo só atalhos) tendo, um orWhere correspondente.
- É possível fazer grupos de comparações e junta-los utilizando uma função de callback dentro de um where que recebe uma variável query como parâmetro e faz os grupos dentro de cada função.
- É possível ordenar o resultado de uma query usando o método `orderBy("nome-da-coluna", "asc ou desc")`.

### Atualizando dados do Banco de Dados

**Método save():** Ao recuperar um registro do Banco de Dados e alterá-lo é possível salva-lo novamente, atualizado, utilizando o método `save()`.

**Método update():** Funciona de forma parecida com o create, recebendo um array associativo com os dados contidos na variável fillable do Model, é usado a partir do resultado de uma query, ela tendo retornado um ou mais registros, atualizando todos os registros com os dados passados no array.

### Deletando dados do Banco de Dados

**Método delete() e destroy():** O método `delete()` funciona de forma parecida com o método update, sendo chamado a partir de um registro ou coleção e deletando os determinados dados do banco de dados. Já o método destroy recebe ids como parâmetro e apaga seus registros do banco.

### SoftDeletes

Para fins históricos, as vezes é necessário manter os dados no banco de dados mesmo depois de apagados, para isso, pode-se usar o soft deletes, se adiciona um campo chamado deleted_at, que é inicialmente null e quando deletado, o registro, ao invés de apagado é só atualizado seu campo deleted_at para o dia e a hora do momento, sendo que o Framework só considera o dado caso seu deleted_at seja null, assim é possível recuperar o dado posteriormente, caso necessário.

**Para Implementar:** Deve-se declarar no model que se está usando os SoftDeletes e criar um campo no banco de dados.

**Obs:** 
- `forceDelete()` apaga definitivamente do banco.
- `withTrashed()` retorna todos os registros inclusive os apagados com SoftDeletes.
- `onlyTrashed()` retorna só os registros apagados.
- `restore()` restaura um item deletado.

### Collections

Array de objetos retornada como resultado de algumas queries, pode ser manipulada de várias formas.

**Métodos first() e last():** retornam, respectivamente, o primeiro e o ultimo objeto da coleção.

**Métodos toArray() e toJson():** retornam, respectivamente, a coleção em forma de array e em forma de json.

**Método pluck():** retorna uma coleção, com os valores de uma chave passada com parâmetro, também pode receber uma segunda chave como parâmetro, cujos valores serão as chaves da coleção gerada.

### Tinker

Console interativo do Laravel onde se tem acesso as classes do projeto, pode ser usado para testar se está tudo certo com as classes e com o banco de dados antes de se fazer um view para realizar as operações do CRUD.

## Formulários

Recuperando dados utilizando o objeto $request. Através do controller, podemos acessar os dados enviados pelo formulário utilizando o método `$request->input('nome_do_campo')`. Também é possível validar os dados recebidos utilizando o método `$request->validate()`, que aceita um array de regras de validação.

**Método input():** Recupera o valor de um campo específico do formulário.

**Método validate():** Valida os dados do formulário de acordo com as regras especificadas.

**Exemplo de uso:**

```php
public function store(Request $request)
{
    $validatedData = $request->validate([
        'nome' => 'required|max:255',
        'email' => 'required|email',
        'mensagem' => 'required',
    ]);

    // Processa os dados validados
}



