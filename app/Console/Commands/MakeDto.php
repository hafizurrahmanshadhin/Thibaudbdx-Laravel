<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeDto extends Command {
    protected $signature   = 'make:dto {name}';
    protected $description = 'Create a new DTO';

    public function handle() {
        $name = $this->argument('name');
        $path = app_path("DTOs/{$name}.php");

        if (File::exists($path)) {
            $this->error("DTO {$name} already exists!");
            return;
        }

        File::ensureDirectoryExists(dirname($path));

        $content = "<?php\n\nnamespace App\DTOs\\" . str_replace('/', '\\', dirname($name)) . ";\n\nclass " . basename($name) . "\n{\n    // Define your properties and methods here\n}\n";

        File::put($path, $content);

        $this->info("DTO {$name} created successfully.");
    }
}
