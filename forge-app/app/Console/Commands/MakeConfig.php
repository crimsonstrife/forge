<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:config
                            {file : The config file to be created, without the .php extension}
                            {--keys=* : A list of keys to be added to the generated file}
                            {--force : Overwrite the config file if it already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new config file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate the contents of the config file.
     *
     * @param iterable $keys
     * @return string
     */
    protected static function generateContents(iterable $keys = []): string
    {
        $lines = [];

        // Get the stub file contents
        $contents = file_get_contents(app_path('Console/Commands/stubs/make-config.stub'));

        if (count($keys) == 0) {
            $keys = ['key1', 'key2'];
        }

        $lines[] = "\t";
        foreach ($keys as $i => $key) {
            $lines[] = "\t" . '/' . '*';
            $lines[] = "\t" . '|--------------------------------------------------------------------------';
            $lines[] = "\t" . '| Name of this config option';
            $lines[] = "\t" . '|--------------------------------------------------------------------------';
            $lines[] = "\t" . '|';
            $lines[] = "\t" . '| Explanation of what this config option does, where it is expected';
            $lines[] = "\t" . '| to be used, as well as any quirks or details worth noting.';
            $lines[] = "\t" . '|';
            $lines[] = "\t" . '*' . '/';
            $lines[] = "\t";
            if ($i === 0) {
                $lines[] = "\t" . "'$key' => [";
                $lines[] = "\t\t" . "'subkey1' => 'sample value',";
                $lines[] = "\t\t" . "'subkey2' => 'sample value',";
                $lines[] = "\t" . "],";
            } else {
                $lines[] = "\t" . "'$key' => 'sample value',";
            }
            $lines[] = "\t";
        }

        // Replace the placeholder in the contents with the generated lines
        $contents = str_replace('{{lines}}', implode("\n", $lines), $contents);

        return $contents;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = $this->argument('file');
        if (!preg_match('/^\w[\w\-]+$/', $file)) {
            $this->error("Only alphanumeric characters, _ and - are allowed for the file name.");
            return;
        } else if (preg_match('/\.php$/i', $file)) {
            $this->error("The file name must not end in .php.");
            return;
        }
        $path = config_path($file . '.php');
        if (file_exists($path) && !$this->option('force')) {
            $this->error("The file already exists: config/" . $file . ".php, use the --force option to overwrite it.");
            return;
        }

        $keys = $this->option('keys');
        foreach ($keys as $k) {
            if (!preg_match('/^\w+$/', $k)) {
                $this->error("Only alphanumeric characters are allowed for the keys.");
                return;
            }
        }

        // Check if the config file already exists, and if the user wants to overwrite it
        if (file_exists($path) && $this->option('force')) {
            $this->info("The config file already exists. Overwriting it...");
        }

        $ret = file_put_contents($path, self::generateContents($keys));
        if ($ret === false) {
            $this->error("Could not save file contents to: config/" . $file . ".php");
        } else {
            $this->info("File config/" . $file . ".php created successfully.");
        }
    }
}
