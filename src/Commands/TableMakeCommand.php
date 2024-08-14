<?php

namespace LaraCombs\Table\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'laracombs:table')]
class TableMakeCommand extends AbstractGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'laracombs:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new table ressource';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Table';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name): string
    {
        return str_replace(
            ['{{ resourceModel }}', '{{resourceModel}}'],
            $this->resolveModel(),
            parent::buildClass($name)
        );
    }

    /**
     * Resolve Model class with the namespace.
     */
    protected function resolveModel(): string
    {
        $name = $this->option('model');

        if (! $name) {
            return '// TODO: Implement model() method.';
        }

        $name = str_contains($name, '\\') ? $name : 'App\Models\\' . $name;

        return 'return \\' . trim($name, '\\') . '::class;';
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The resource model for the table'],
        ];
    }
}
