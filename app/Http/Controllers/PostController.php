<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Models\PersonalAccessTokens;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->authToken){
            $token = $request->authToken;
            $token_arr = PersonalAccessTokens::select('tokenable_id','token_count')->where('plainText',$token)->first();
            if($token_arr==null){ 
               return 'Invalid authToken'; 
            }else{
                $user_id = $token_arr->tokenable_id;
                if($token_arr->token_count==null){
                    $token_count = 0;
                }else{
                    $token_count = $token_arr->token_count;
                }
                $updated_count = $token_count+1;
                $updateTokenCount = PersonalAccessTokens::where('tokenable_id',$user_id)->first();
                $updateTokenCount->token_count = $updated_count;
                $updateTokenCount->save();
                return Posts::all();
           }
        }else{
            return 'authToken Missing';
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Seacrh for Title in posts table
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        return Posts::where('post_title','like','%'.$name.'%')->get();
    }
}
