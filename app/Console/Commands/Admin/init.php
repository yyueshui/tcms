<?php

namespace App\Console\Commands\Admin;

use Illuminate\Console\Command;
use TCG\Voyager\Traits\Seedable;

class init extends Command
{
	use Seedable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化数据库数据';

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
     * @return mixed
     */
    public function handle()
    {
	    $this->seed('CategoriesTableSeeder');
	    $this->seed('UsersTableSeeder');
	    $this->seed('PostsTableSeeder');
	    $this->seed('PagesTableSeeder');
	    $this->seed('SettingsTableSeeder');
	    $this->seed('TranslationsTableSeeder');
	    $this->info('All seeds has done !');
    }
}
