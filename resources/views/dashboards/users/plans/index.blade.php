@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Plans</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($plans as $plan)
                        <li class="list-group-item clearfix">
                            <div class="pull-left">
                                <h5>{{ $plan->name }}</h5>
                                <h5>$ {{ number_format($plan->cost, 2) }} monthly</h5>
                                <h5>{{ $plan->description }}</h5>
                                <?php  
                                    if($planSubscribed){
                                        if($planSubscribed->stripe_plan == $plan->stripe_plan){ 
                                            if($planSubscribed->cancels_at){
                                                $btnTxt = 'Cancels at '.date("jS F, Y", strtotime($planSubscribed->cancels_at));
                                                $route = 'Javascript:void(0)';
                                            }else{
                                                $btnTxt = 'Cancel';
                                                $route = route('plans.cancelSubscription');
                                            }
                                            
                                        ?>
                                            <a href="javascript:void(0)" style="background-color:#0280009c;" class="btn btn-outline-dark pull-right">Subscribed</a>

                                            <a href="{{$route}}" style="background-color:#ff0000ab;" class="btn btn-outline-dark pull-right">{{$btnTxt}}</a>
                                    <?php }else{
                                            if($planSubscribed->cost > $plan->cost){
                                                $btnTxt = 'Downgrade';
                                            }else{
                                                $btnTxt = 'Upgrade';
                                            }
                                    ?>
                                            <a href="{{ route('plans.show', $plan->slug) }}" class="btn btn-outline-dark pull-right">{{$btnTxt}}</a>
                                    <?php }
                                    }else{ ?>
                                        <a href="{{ route('plans.show', $plan->slug) }}" class="btn btn-outline-dark pull-right">Choose</a>
                              <?php }
                                ?>
                                
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection