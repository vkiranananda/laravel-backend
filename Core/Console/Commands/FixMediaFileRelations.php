<?php

namespace Backend\Root\Core\Console\Commands;

use Illuminate\Console\Command;

use \Backend\Root\MediaFile\Models\MediaFile;
use \Backend\Root\MediaFile\Models\MediaFileRelation;


class FixMediaFileRelations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backend:fix_media_file_relations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Переходим на новую модель связей файлов с моделями';

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
        MediaFile::where('type', 2)
        	->where('imageable_type', '<>', '')
        	->chunk(100, function($files) {

		    foreach ($files as $file) {

		    	MediaFileRelation::create([
	        		'file_id' => $file['id'],
	        		'post_id' => $file['imageable_id'],
	        		'post_type' => $file['imageable_type'],
	        	]);

		    	$file['imageable_type'] = '';
		    	$file->save();
		    }
		});
    }
}
