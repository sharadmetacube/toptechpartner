@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Login Activity
                </div>
                <div class="panel-body">
                    @if(count($loginActivities) > 0)
                    <table class="table table-responsive table-bordered">
                        <thead>
                            <tr>
                                <th>User Agent</th>
                                <th>IP Address</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loginActivities as $loginActivity)
                                <tr>
                                    <td>{!! $loginActivity->user_agent !!}</td>
                                    <td>{!! $loginActivity->ip_address !!}</td>
                                    <td>{!! $loginActivity->created_at !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="paginate">
                        {!! $loginActivities->render() !!}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection