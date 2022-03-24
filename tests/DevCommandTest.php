<?php

it('can run dev command', function () {
    $src = '/tests/testDump/src';
    $target = __DIR__.'/testDump/.pht';

    if (file_exists($target)) {
        if (PHP_OS_FAMILY == 'Windows') {
            exec("rd /s /q \"$target\"");
        } else {
            exec("rm -rf \"$target\"");
        }
    }

    $app = new \Symfony\Component\Console\Application('Pht');
    $app->add(new \Thettler\Pht\Commands\DevCommand());

    $tester = new \Symfony\Component\Console\Tester\CommandTester($app->find('dev'));

    $statusCode = $tester->execute(['--src' => $src,'--target' => '/tests/testDump/.pht']);

    $this->assertSame(0, $statusCode);
    $this->assertDirectoryExists(__DIR__.'/testDump/.pht');
    $this->assertFileExists(__DIR__.'/testDump/.pht/PhtTestClass.php');
    $this->assertFileEquals(__DIR__.'/fixture/VerifyPhtTestClass.php', __DIR__.'/testDump/.pht/PhtTestClass.php');
});
