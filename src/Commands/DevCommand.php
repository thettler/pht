<?php

namespace Thettler\Pht\Commands;

use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemReader;
use League\Flysystem\Local\LocalFilesystemAdapter;
use React\EventLoop\Loop;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DevCommand extends Command
{
    protected array $changed = [];
    protected Filesystem $filesystem;
    protected \League\Flysystem\Local\LocalFilesystemAdapter $adapter;
    protected \React\EventLoop\LoopInterface $loop;

    protected string $src = '';
    protected string $target = '';

    public function __construct()
    {
        parent::__construct();
        $this->loop = Loop::get();
        $this->adapter = new LocalFilesystemAdapter(__DIR__ .'/../../../../..');
        $this->filesystem = new Filesystem($this->adapter);

        $this->changed = [];
    }

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('dev')
            ->setDescription('Watch and compile pht to php')
            ->addOption('src', null, InputOption::VALUE_OPTIONAL, '', 'app')
            ->addOption('target', null, InputOption::VALUE_OPTIONAL, '', '.pht');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->src = trim($input->getOption('src'), '/');
        $this->target = trim($input->getOption('target'), '/');

        $output->writeln('Started Watching for .pht files:');

        $this->loop->addPeriodicTimer(.5, function () use ($output) {
            $files = $this->getPhtFiles($this->src);

            /** @var FileAttributes $file */
            foreach ($files as $file) {
                $this->compilePhtFile($file);

                $this->changed[$file->path()] = $file->lastModified();

                $output->write('Updated: '.$file->path().PHP_EOL);
            }
        });

        $this->loop->run();

        return 0;
    }

    public function getPhtFiles($src): array
    {
        return $this->filesystem->listContents($src, FilesystemReader::LIST_DEEP)
            ->filter(fn (\League\Flysystem\StorageAttributes $attributes) => $attributes->isFile())
            ->filter(fn (FileAttributes $attributes) => str_contains($attributes->path(), '.pht'))
            ->filter(fn (
                FileAttributes $attributes
            ) => $attributes->lastModified() > ($this->changed[$attributes->path()] ?? 0))
            ->toArray();
    }

    public function compilePhtFile(FileAttributes $file)
    {
        $content = $this->filesystem->read($file->path());

        $php = (new \Thettler\Pht\PhtCompiler())
            ->compile($content);

        $this->filesystem->write($this->phtToPhpPath($file->path()), $php);
    }

    public function phtToPhpPath(string $path)
    {
        $path = str_replace($this->src, $this->target, $path);

        return rtrim($path, '.pht') . '.php';
    }
}
