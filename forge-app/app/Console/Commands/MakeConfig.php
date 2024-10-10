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
                            {--keys=* : A list of keys to be added to the generated file}';

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

    protected static function generateContents(iterable $keys = []): string
    {
        $lines = [];

        $lines[] = '<' . '?' . 'p' . 'h' . 'p';
        $lines[] = '';
        $lines[] = 'return [';

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

        $lines[] = '];';

        return implode("\n", $lines) . "\n";
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
        } elseif (preg_match('/\.php$/i', $file)) {
            $this->error("The file name must not end in .php.");
            return;
        }
        $path = config_path($file . '.php');
        if (file_exists($path)) {
            $this->error("The file already exists: config/" . $file . ".php");
            return;
        }

        $keys = $this->option('keys');
        foreach ($keys as $k) {
            if (!preg_match('/^\w+$/', $k)) {
                $this->error("Only alphanumeric characters are allowed for the keys.");
                return;
            }
        }

        $ret = file_put_contents($path, self::generateContents($keys));
        if ($ret === false) {
            $this->error("Could not save file contents to: config/" . $file . ".php");
        } else {
            $this->info("File config/" . $file . ".php created successfully.");
        }
    }
}
