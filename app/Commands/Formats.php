<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Formats extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'formats';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Lists available export formats, with description';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $outputDirectory = app_path('Output');
        //get the output directory path

        $this->info('Reading available file types');
        //give users some immediate feedback

        foreach( glob($outputDirectory . '/*.php') as $file){
            //For this particular instance, glob() was easier than using the scandir(), though if this was going to get large changing this to work for scandir may offer
            //performance improvements
            preg_match('/(\w+)\.php$/i', $file, $matches);
            //match the file name -- shouldn't use any non-word characters such as "-" in the file names.
            $className = 'App\\Output\\'.$matches[1];
            //class name is first match from the thingy
            $classObj = new $className;
            //Create the object
            $this->info( $classObj->extension .' - '. $classObj->description );
            //output stuff to screen
        }
    }

    /**
	 * Define the command's schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule $schedule
	 *
	 * @return void
	 */
	public function schedule(Schedule $schedule): void
	{
		// $schedule->command(static::class)->everyMinute();
	}
}
