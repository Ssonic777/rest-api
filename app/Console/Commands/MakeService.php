<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;

/**
 * class MakeService
 * @package App\Console\Commands
 */
class MakeService extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service
                            {name : Service name}
                            {--repository : Repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service';

    /**
     * @var string $namespace
     */
    private string $namespace = 'App\Services';

    /**
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function fire(): int
    {
        $serviceName = $this->argument('name');
        $this->fire();

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
        return base_path('stubs/service.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Services';
    }
}
