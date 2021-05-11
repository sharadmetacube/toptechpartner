@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                    <div class="section">
                        <div class="alert alert-success alert-block" style="display: none;">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong class="success-msg"></strong>
                        </div>
                        <form>
                            <div class="form-group">
                                @csrf
                                <button class="btn btn-success" id="btnToken">Regenerate Access Token</button>
                                <p class="success" id="loadMsg" style="display:none;">Loading Please wait ....</p>
                            </div> 
                            @if($token)
                            <div class="form-group">
                                <input type="text" class="form-control" id="accessTokenVal" readonly value="{{$token}}">
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('ajaxJsCode')
<script type="text/javascript">
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
                    }
                }
            });
        });
    });
</script>
@endsection


