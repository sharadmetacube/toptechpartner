@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Control Panel - Dashboard') }}
                    <div class="float-right">
                        {{ __('You are logged in!') }}
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    
                    <div class="section">
                        <div class="alert alert-success alert-block" style="display: none;">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong class="success-msg"></strong>
                        </div>
                        <form>
                        @csrf
                            <div class="form-group">
                                <h2 class="doc_subheading mt-5 key">Your API Access Key :-</h2>
                            </div>
                            @if($token)
                            <div class="form-group">
                                <input type="text" class="form-control" id="accessTokenVal" readonly value="{{$token->plainText}}">
                            </div>
                            @endif
                            <div class="form-group">
                                <button class="btn btn-success" id="btnToken">Regenerate Access Token</button>
                                <p class="success" id="loadMsg" style="display:none;">Loading Please wait ....</p>
                            </div> 
                        </form>
                    </div>
                </div>
            </div>
            @if($token)
            <?php 
                if($token->token_count==null): 
                    $tokenCount = 0;
                else:
                    $tokenCount = $token->token_count;
                endif;

                if($plan): $planName = $plan->name; $cancels_at =  'cancels at '.date("jS F, Y", strtotime($plan->cancels_at)); else: $planName = 'No Plan Selected'; $cancels_at =''; endif;
            ?>
            <div class="card">
                <div class="card-header"></div>
                <div class="card-body">
                    <div class="form-group">
                        <h2 class="doc_subheading mt-5">Your Plan :-</h2>
                    </div>
                    <div class="form-group">
                        <p><strong>Subscription :- </strong><span>{{$planName}}  </span><span style="color:red;">{{$cancels_at}}</span></p>
                        <p><strong>API Usage :- </strong><span>{{$tokenCount}} / {{config('global.api_token_limit')}} </span></p>
                        <progress value="{{$tokenCount}}" max="{{config('global.api_token_limit')}}"></progress>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('ajaxJsCode')
<script type="text/javascript">
    // For Regenerate Token
    $(document).ready(function() {
        $("#btnToken").click(function(e){
            e.preventDefault();
            $('#loadMsg').show();
            var _token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ route('generate.token') }}",
                type:'POST',
                data: {_token:_token},
                success: function(data) {
                    if(data!=false){
                        $('#accessTokenVal').val(data);
                        $('#loadMsg').hide();
                        location.reload();
                    }
                }
            });
        });
    });
</script>
@endsection


