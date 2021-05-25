<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Categories;

class UpdateCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Categories ';

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
        $request_url = 'http://localhost/wordpress/wp-json/list_cat/v1/categories/';
        $credentials = array();
        $credentials = array( 'username: '.config("global.wp_user_name").'', 'password: '.config("global.wp_user_pass").'' );

        $curl_handle = curl_init( );
        curl_setopt( $curl_handle, CURLOPT_URL, $request_url );
        curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT, 0 );
        curl_setopt( $curl_handle, CURLOPT_TIMEOUT, 15 );
        curl_setopt( $curl_handle, CURLOPT_HTTPHEADER, $credentials  );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, TRUE );

        $JsonResponse = curl_exec( $curl_handle );
        $http_code = curl_getinfo( $curl_handle );

        if ( 200 == $http_code[ 'http_code' ] ) {
            $catArr = json_decode($JsonResponse,true);
            foreach ($catArr as $cat) {
                $checkCat = Categories::where('slug',$cat['slug'])->first();
                if($checkCat){ }else{
                    $insertCat = Categories::create(['name'=>$cat['name'],'slug'=>$cat['slug'],'description'=>$cat['description'],'link'=>$cat['link'],'wp_id'=>$cat['id'],'parent'=>$cat['parent']]);
                }
            }
            $this->info('Categories has been updated successfully');
        }
        else {
            echo 'ERROR: <pre>', var_export( $JsonResponse, true ), "</pre>\n";
        }
    }
}
