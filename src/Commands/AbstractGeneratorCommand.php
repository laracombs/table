<?php

namespace LaraCombs\Table\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

abstract class AbstractGeneratorCommand extends GeneratorCommand
{
    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath(Str::lower($this->type) . '.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     */
    protected function resolveStubPath(string $stub): string
    {
        return is_file($customPath = $this->laravel->basePath('laracombs/table/' . trim($stub, '/')))
            ? $customPath
            : dirname(__DIR__, 2) . '/stubs/' . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Tables\Resources';
    }
}
