<?php

it('can compile functions to php', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test(){}'))
        ->toEqual(<<<'EOF'
function test(): void{

}
EOF);
});

it('can compile function with parameters to php', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test(string $foo){}'))
        ->toEqual(<<<'EOF'
/**
* @param string $foo
*/
function test(string $foo): void{

}
EOF);
});

it('can compile function with parameters with non php type', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test(classString $foo){}'))
        ->toEqual(<<<'EOF'
/**
* @param class-string $foo
*/
function test(string $foo): void{

}
EOF);
});

it('can compile function with typed array', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test(string[] $foo){}'))
        ->toEqual(<<<'EOF'
/**
* @param string[] $foo
*/
function test(array $foo): void{

}
EOF);
});

it('can compile function with typed array stan type', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test(classString[] $foo){}'))
        ->toEqual(<<<'EOF'
/**
* @param class-string[] $foo
*/
function test(array $foo): void{

}
EOF);
});

it('can compile function with generic parameter type', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test(classString<stdClass> $foo){}'))
        ->toEqual(<<<'EOF'
/**
* @param class-string<stdClass> $foo
*/
function test(string $foo): void{

}
EOF);
});

it('can compile function with multiple parameters', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test(string[] $foo, string $bar){}'))
        ->toEqual(<<<'EOF'
/**
* @param string[] $foo
* @param string $bar
*/
function test(array $foo, string $bar): void{

}
EOF);
});

it('can compile function return value', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test(): string{}'))
        ->toEqual(<<<'EOF'
/**
* @return string
*/
function test(): string{

}
EOF);
});

it('can compile function return value with non php type', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test(): classString{}'))
        ->toEqual(<<<'EOF'
/**
* @return class-string
*/
function test(): string{

}
EOF);
});

it('can compile function with generic type', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test<T>(T $foo): T{}'))
        ->toEqual(<<<'EOF'
/**
* @template T
* @param T $foo
* @return T
*/
function test(mixed $foo): mixed{

}
EOF);
});

it('can compile generics in function scope', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('fn test<T>(){ $foo: T = "bar";}'))
        ->toEqual(<<<'EOF'
/**
* @template T
*/
function test(): void{
/** @var T $foo */
$foo = "bar";
}
EOF);
});
