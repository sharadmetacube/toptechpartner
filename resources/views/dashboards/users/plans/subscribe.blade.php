@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <form action="{{route('plans.processSubscription')}}" method="POST" id="subscribe-form">
                <div class="form-group">
                    <div class="row">
                        @foreach($plans as $plan)            
                        <div class="col-md-4">
                            <div class="subscription-option">
                                <input type="radio" id="plan-silver" name="plan" value='{{$plan->id}}'>
                                <label for="plan-silver">
                                    <span class="plan-price">{{$plan->currency}}{{$plan->amount/100}}<small> /{{$plan->interval}}</small></span>
                                    <span class="plan-name">{{$plan->product->name}}</span>
                                </label>
                            </div>
                        </div>
                        @endforeach        
                    </div>
                </div>
                <input id="card-holder-name" type="text"><label for="card-holder-name">Card Holder Name</label>    @csrf
                <div class="form-row">
                    <label for="card-element">Credit or debit card</label>
                    <div id="card-element" class="form-control">        </div>
                    <!-- Used to display form errors. -->
                    <div id="card-errors" role="alert"></div>
                </div>
                <div class="stripe-errors"></div>
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                    @endforeach
                </div>
                @endif    
                <div class="form-group text-center">
                    <button id="card-button" data-secret="{{ $intent->client_secret }}" class="btn btn-lg btn-success btn-block">SUBMIT</button>
                </div>
            </form>
        @section('scripts')
            <script src="https://js.stripe.com/v3/"></script>
            <script type="text/javascript">
                var stripe = Stripe('{{ env("STRIPE_KEY") }}', { locale: 'en' });
                var elements = stripe.elements();
                var style = {
                    base: {
                        color: '#32325d',
                        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                        fontSmoothing: 'antialiased',
                        fontSize: '16px',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    },
                    invalid: {
                        color: '#fa755a',
                        iconColor: '#fa755a'
                    }
                };
                var card = elements.create('card', {
                    hidePostalCode: true,
                    style: style
                });
                card.mount('#card-element');
                card.addEventListener('change', function(event) {
                    var displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
                const cardHolderName = document.getElementById('card-holder-name');
                const cardButton = document.getElementById('card-button');
                const clientSecret = cardButton.dataset.secret;
                cardButton.addEventListener('click', async (e) => {
                    console.log("attempting");
                    const {
                        setupIntent,
                        error
                    } = await stripe.confirmCardSetup(
                        clientSecret, {
                        payment_method: {
                            card: card,
                            billing_details: {
                            name: cardHolderName.value
                            }
                        }
                        }
                    );
                    if (error) {
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = error.message;
                    } else {
                        console.log(setupIntent);
                        paymentMethodHandler(setupIntent.payment_method);
                    }
                });

                function paymentMethodHandler(payment_method) {
                    var form = document.getElementById('subscribe-form');
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'payment_method');
                    hiddenInput.setAttribute('value', payment_method);
                    form.appendChild(hiddenInput);
                    form.submit(); 
                } 
            </script>
        @endsection
        </div>
    </div>
</div>
@endsection