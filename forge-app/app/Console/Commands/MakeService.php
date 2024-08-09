<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    protected $files;

    /**
     * Summary of __construct
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Summary of handle
     * @return bool
     */
    public function handle()
    {
        $name = $this->argument('name');
        $serviceClass = $this->qualifyClass($name);

        $path = $this->getPath($serviceClass);

        if ($this->files->exists($path)) {
            $this->error('Service class already exists!');
            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($serviceClass));

        $this->info('Service class created successfully.');

        return true;
    }

    /**
     * Summary of qualifyClass
     * @param string $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);

        return $name;
    }

    /**
     * Summary of getPath
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return base_path('app/Services') . '/' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Summary of makeDirectory
     * @param string $path
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Summary of buildClass
     * @param string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get(base_path('stubs/service.stub'));

        return str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$this->getNamespace($name), class_basename($name)],
            $stub
        );
    }

    /**
     * Summary of getNamespace
     * @param string $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return 'App\\Services' . (dirname($name) !== '.' ? '\\' . dirname($name) : '');
    }

    /**
     * Summary of rootNamespace
     * @return string
     */
    protected function rootNamespace()
    {
        return 'App\\';
    }
}
