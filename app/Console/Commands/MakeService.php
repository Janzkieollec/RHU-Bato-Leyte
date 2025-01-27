<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name');
        $directory = app_path('Services');

        // Check if the Services directory exists, if not, create it
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $path = $directory.'/'.$name.'.php';

        if (file_exists($path)) {
            $this->error('Service already exists!');
            return;
        }

        $stub = "<?php\n\nnamespace App\Services;\n\nclass $name\n{\n    // Service methods go here\n}";

        file_put_contents($path, $stub);

        $this->info("Service created successfully: $path");
    }

}