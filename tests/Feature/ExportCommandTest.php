<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

final class ExportCommandTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testCanRunWithMinimalArguments(): void
    {
        Artisan::call('export', ['file'=>'hotels.csv']);
        $this->assertContains('File exported successfully', Artisan::output());
    }

    public function testCanFailGracefully(): void {
        Artisan::call('export', ['file'=>'doesnotexist.csv' ]);
        $this->assertContains('Cannot find', Artisan::output());
    }

    public function testCanRunWithFormatArgument(): void {
        Artisan::call('export', [
            'file' =>'hotels.csv',
            'output_type' => 'yaml'
        ]);

        $this->assertContains('File exported successfully', Artisan::output());
    }

    public function testCanRunWithAllArguments(): void {
        Artisan::call('export', [
            'file' =>'hotels.csv',
            'output_type' => 'yaml',
            'output_file'=>'other_file_name.yml'
        ]);
        $this->assertContains('File exported successfully', Artisan::output());
    }

    public function testListsFormat(): void {
        Artisan::call('formats');
        $output = Artisan::output();

        $this->assertContains('json - JavaScript Object Notation -- a fairly standard web-friendly format for passing or storing data.', $output);
        $this->assertcontains('yaml - YAML Ain\'t Markup Language -- A superset of JSON used primarily for configuration files.', $output);
        
    }
}
