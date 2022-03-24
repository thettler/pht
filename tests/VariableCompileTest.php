<?php

it('can compile pht variables to php', function () {
    $phpCode = (new \Thettler\Pht\PhtCompiler())
        ->compile('$bar: string = "foo";');

    expect($phpCode)->toEqual(<<<'EOF'
/** @var string $bar */
$bar = "foo";
EOF);
});

it('can ignore white spaces at compiling pht variables to php', function () {
    $phpCode = (new \Thettler\Pht\PhtCompiler())
        ->compile('     $bar    :     string  =    "foo"
        ;');

    expect($phpCode)->toEqual(<<<'EOF'
/** @var string $bar */
$bar = "foo";
EOF);
});

it('can have statements ', function () {
    $phpCode = (new \Thettler\Pht\PhtCompiler())
        ->compile('$bar: bool = true === false;');

    expect($phpCode)->toEqual(<<<'EOF'
/** @var bool $bar */
$bar = true === false;
EOF);
})->skip();
