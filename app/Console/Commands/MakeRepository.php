<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;

class MakeRepository extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository
                            {name : Repository name}
                            {--service : Service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository';

    /**
     * @var string $namespace
     */
    private string $namespace = 'App\Repositories';

    /**
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function fire(): int
    {
        $serviceName = $this->argument('name');
        $path = $this->getPath($this->namespace . '\\' . $serviceName);
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($serviceName));

        return Command::SUCCESS;
    }

    /**
     * @return string
     */
    public function getStub(): string
    {
        return base_path('stubs/repository.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Repositories';
    }
}
