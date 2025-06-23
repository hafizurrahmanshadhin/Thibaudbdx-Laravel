<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeInterface extends Command {
    protected $signature   = 'make:interface {name}';
    protected $description = 'Create a new interface';

    public function handle() {
        $name = $this->argument('name');
        $path = app_path("Interfaces/{$name}.php");

        if (File::exists($path)) {
            $this->error("Interface {$name} already exists!");
            return;
        }

        File::ensureDirectoryExists(dirname($path));

        $content = "<?php\n\nnamespace App\Interfaces\\" . str_replace('/', '\\', dirname($name)) . ";\n\ninterface " . basename($name) . "\n{\n    // Define your methods here\n}\n";

        File::put($path, $content);

        $this->info("Interface {$name} created successfully.");
    }
}
