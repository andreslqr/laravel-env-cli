<?php

namespace Andresdevr\EnvCli\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SetEnvVariable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:set
                            {variable : The variable of the .env file to change}
                            {value : The value of the variable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the env value of the file';

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $variables = $this->getEnvData();

        $variable = $this->argument('variable');
        $value = Str::of($this->argument('value'))->start("\"")->finish("\"");

        if($variables->has($variable))
        {
            $variables[$variable] = (string) $value;
            $this->setEnvData($variables);
            $this->info("Variable changed");
        }
        else
        {
            $variables->put($variable, $value);
            $this->setEnvData($variables);
            $this->info("Variable added");
        }

        return 0;
    }

    private function getEnvData() : Collection
    {
        if(File::exists(base_path('.env')))
            return Collection::make(explode("\n", file_get_contents(base_path('.env'))))->filter()->mapWithKeys(function($item) : array {
                $data = explode('=', $item);
                $key = $data[0];
                $value = isset($data[1]) ? $data[1] : "";
                return [$key => (string) empty($value) ? "" : Str::of($value)->start("\"")->finish("\"")];
            });
        else
        {
            if($this->confirm("The .env file does't exists, do you want create one?"))
            {
                $this->createEnvFile();
                return $this->getEnvData();
            }
            else
                exit;
        }
    }

    private function setEnvData(Collection $data) : void
    {
        file_put_contents(base_path('.env'), null);
        $data->each(function($value, $variable) : void {
            file_put_contents(base_path('.env'), "$variable=$value\n", FILE_APPEND);
        });
    }

    private function createEnvFile() : void
    {
        copy(base_path('.env.example'), '.env');
    }
}
