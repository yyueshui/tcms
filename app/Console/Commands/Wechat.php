<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class Wechat extends Command
{
	use \App\Traits\Wechat;

	private $functionConfig = [
		'start', 'stop',
	];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wechat:serve {function} {--session} {sessionName?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '微信机器人';

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
	    $command = $this->argument('function');
	    //$sessionName = $this->argument('sessionName');
		if(!in_array($command, $this->functionConfig)) {
			throw new CommandNotFoundException('Command not exists');
		}
		//$list = $this->replyGoods('找火火兔', $image);
		//var_dump($image);
		//echo $list;
		$this->$command();
    }
}
