<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

class Export extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'export {file : File relative to the storage path} {output_type=json : The default output type} {output_file? : Output file name. Defaults to [file argument].[output_type]}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Exports CSV hotel data into the desired format';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $fileName = $this->argument('file');
        //the file name
        $fileName = (!in_array( $fileName[0], ['.', '/' ])) ? './'.$fileName : $fileName;
        $this->info ('Loading file "'. $fileName . '" ...');
        //some output data
        $fullFileName = realpath( base_path() .'/'. $fileName);
        
        if(!$fullFileName) {
            $this->error('Cannot find "'.$this->argument('file').'" in "'.base_path().'"');
            return ;
        }
        $csvData = Storage::get($fileName);
        //get the CSV Data as a string
        $csv = Reader::createFromString($csvData, 'r');

        $csv->setHeaderOffset(0); //set the CSV header offset to call things based on their header name

        $stmt = (new Statement())->limit(10);
        //Create a new statement, if there were any optional limits on how many items we would take, this would be where.
        $records = $stmt->process($csv);
        //Process the CSV data based on the options in from the Statement object

        $validatedData = []; //blank array to store things in.

        foreach ($records as $record) {
            //Loop through the recoreds
            
            $stars = (int) $record['stars']; // Need this to be an integer

            if ($stars < 0 || $stars > 5) continue; //invalid star number

            if (preg_match('/[^\x00-\x7F]/', $record['name']) ) continue; //found a character outside of the ASCII range.

            if(!preg_match('/((https?):\/\/(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)/i', $record['uri']) 
                || !filter_var($record['uri'], FILTER_VALIDATE_URL) ) continue;
            //If it makes it through both of these filters, I am okay with believing that it is a valid URL.
            //Short of checking DNS records for each row, we'll have to trust these.
            
            $validatedData[] = $record;
            //If the code has gotten down to here, assume this is validated.
        }
        
        //Alright, we have data that's as valid as we can get without doing a DNS lookup for each domain. time to create the output object and start that whole process.
    
        $className = "App\\Output\\".ucwords(strtolower($this->argument('output_type')));
        //Really wish PHP had a better way to check stuff like this.
        $baseFileName = preg_replace('/\.csv$/', '', $fullFileName);
        //Get rid of the .csv
        if(!class_exists($className)) {
            //If the class name is not valid, a very pleasant error awaits
            $this->error('you have attempted to use a format that does not exist. Please run "php hotels formats" to receive a list of available formats at this time');
            return ;
        }
        
        $outObj = new $className;
        
        $filename = ($this->argument('output_file') ? 
            dirname($baseFileName).'/'.$this->argument('output_file') 
            : $baseFileName.'.'.$this->argument('output_type') );
        
        $outObj->convert($validatedData, $filename );

        if(file_exists($filename)){
            $this->notify('Success', 'File exported successfully');
            $this->info('File exported successfully');
        }else{
            $this->notify('Error', 'There was an error exporting the file to "'.$filename.'", do you have write permission?');
            $this->error('There was an error exporting the file');
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
