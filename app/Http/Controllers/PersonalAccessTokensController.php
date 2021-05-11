<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PersonalAccessTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PersonalAccessTokensController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

    public function generate_token(Request $request){
        if (Auth::check()) {
            $user = Auth::user();
            $delToken = PersonalAccessTokens::where('tokenable_id', $user->id)->delete();
            $token = $user->createToken('authToken', ['server:get'])->plainTextToken;
            if($token){
                $personalAccessTokens = PersonalAccessTokens::where('tokenable_id',$user->id)->first();
                $personalAccessTokens->plainText = $token;
                $personalAccessTokens->save();
                return $token;
            }else{
                return false;
            }
        } else {
            return redirect(route('login'));
        }
    }
}
