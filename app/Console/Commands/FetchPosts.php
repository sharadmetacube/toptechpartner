<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Posts;

class FetchPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Posts From WP';

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
        $request_url = 'http://localhost/wordpress/wp-json/list_post/v1/list-post/';
        $credentials = array();
        $credentials = array( 'username: admin', 'password: admin' );

        $curl_handle = curl_init( );
        curl_setopt( $curl_handle, CURLOPT_URL, $request_url );
        curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT, 0 );
        curl_setopt( $curl_handle, CURLOPT_TIMEOUT, 15 );
        curl_setopt( $curl_handle, CURLOPT_HTTPHEADER, $credentials  );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, TRUE );

        $JsonResponse = curl_exec( $curl_handle );
        $http_code = curl_getinfo( $curl_handle );

        if ( 200 == $http_code[ 'http_code' ] ) {
            $postArr = json_decode($JsonResponse,true);
            foreach($postArr as $post){
                $checkPost = Posts::where('wp_post_id',$post['ID'])->first();
                if($checkPost){ }else{
                    $insertPost = Posts::create(['wp_post_id'=>$post['ID'],'post_title'=>$post['post_title'],'post_excerpt'=>$post['post_excerpt'],'post_content'=>$post['post_content'],'featured_image'=>$post['meta_value'],'categories'=>$post['Categories'],'tags'=>$post['Tags']]);
                }
            }
            $this->info('Posts has been updated successfully');
        }
        else {
            echo 'ERROR: <pre>', var_export( $JsonResponse, true ), "</pre>\n";
        }
    }
}
