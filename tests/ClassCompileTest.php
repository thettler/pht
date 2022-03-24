<?php

it('can compile class to php', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('class Test{}'))
        ->toEqual(<<<'EOF'
class Test {
}
EOF);
});

it('can compile abstract class to php', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('abstract class Test{}'))
        ->toEqual(<<<'EOF'
abstract class Test {
}
EOF);
});

it('can compile extending class', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('class Test extends TestA{}'))
        ->toEqual(<<<'EOF'
class Test extends TestA {
}
EOF);
});

it('can compile implementing class', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('class Test implements TestA{}'))
        ->toEqual(<<<'EOF'
class Test implements TestA {
}
EOF);
});

it('can compile extending and implementing class', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('class Test extends TestA implements TestB{}'))
        ->toEqual(<<<'EOF'
class Test extends TestA implements TestB {
}
EOF);
});

it('can compile generic class', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile('class Test<T>{}'))
        ->toEqual(<<<'EOF'
/**
* @template T
*/
class Test {
}
EOF);
});

it('can compile methods in class', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile(<<<'EOF'
class Test {
    fn test() {

    }

    pub fn publicTest() {

    }

    private fn privateTest() {

    }

    static fn staticTest() {

    }

    pub static fn publicStaticTest() {

    }

    private static fn privateStaticTest() {

    }
}
EOF))
        ->toEqual(<<<'EOF'
class Test {
protected function test(): void{

    }

    public function publicTest(): void{

    }

    private function privateTest(): void{

    }

    protected static function staticTest(): void{

    }

    public static function publicStaticTest(): void{

    }

    private static function privateStaticTest(): void{

    }
}
EOF);
});

it('can compile attributes in class', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile(<<<'EOF'
class Test {
    $test: classString;

    pub $publicTest: classString = 'Class';

    private $privateTest: classString;

    static $staticTest: classString;

    pub static $publicStaticTest: classString = 'Class';

    private static $privateStaticTest: classString;

    readonly $readonlyTest: classString;

    pub readonly $publicReadonlyTest: classString = 'Class';

    private readonly $privateReadonlyTest: classString;
}
EOF))
        ->toEqual(<<<'EOF'
class Test {
/** @var class-string */
protected  string $test ;

    /** @var class-string */
public  string $publicTest = 'Class';

    /** @var class-string */
private  string $privateTest ;

    /** @var class-string */
protected static string $staticTest ;

    /** @var class-string */
public static string $publicStaticTest = 'Class';

    /** @var class-string */
private static string $privateStaticTest ;

    /** @var class-string */
protected readonly string $readonlyTest ;

    /** @var class-string */
public readonly string $publicReadonlyTest = 'Class';

    /** @var class-string */
private readonly string $privateReadonlyTest ;
}
EOF);
});

it('can compile variables in class', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile(<<<'EOF'
class Test {
    fn __construct() {
        $foo: string = 'bar';
    }
}
EOF))
        ->toEqual(<<<'EOF'
class Test {
public function __construct(){
/** @var string $foo */
$foo = 'bar';
    }
}
EOF);
});

it('can use generics inside of class', function () {
    expect((new \Thettler\Pht\PhtCompiler())
        ->compile(<<<'EOF'
class Test<T, A> {
    pub $attribute: T;

    fn test(T $var): A {
        $foo: T = 'bar';
    }
}
EOF))
        ->toEqual(<<<'EOF'
/**
* @template T
* @template A
*/
class Test {
/** @var T */
public  mixed $attribute ;

    /**
* @param T $var
* @return A
*/
protected function test(mixed $var): mixed{
/** @var T $foo */
$foo = 'bar';
    }
}
EOF);
});
