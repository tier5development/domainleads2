<script type="text/javascript">
        var username            =   "{{$user->name}}";
        var email               =   "{{$user->email}}";
        var publicKey           =   "{{$stripeDetails->public_key}}";
        var userStoredImagePath =   "{{$user->image_path}}";

        $(window).on('popstate', function() {
            handler.close();
        });

        var handler = StripeCheckout.configure({
            key: publicKey,
            image: userStoredImagePath,
            locale: 'auto',
            token: function(token) {
                $.ajax({
                    url: "{{route('updateCardDetails')}}",
                    data: {
                        stripe_token    :   token.id,
                        _token  :   "{{csrf_token()}}"
                    },
                    type :"post",
                    beforeSend : function() {
                        $('#loader-icon').show();
                    }, success: function(resp) {
                        console.log(resp);
                        if(resp.status) {
                            $('#loader-icon').hide();
                            var last4 = response.card;
                            var exp_month   = response.card['exp_month'];
                            var exp_year    = response.card['exp_year'];
                        } else {
                            $('#loader-icon').hide();
                        }
                    }, error : function(err) {
                        $('#loader-icon').hide();
                        if(err.status == 401) {
                            window.location.replace("{{route('loginPage')}}");
                        }
                        console.log(err.status);
                    }
                });
            }
        });

        $('#update-card-btn').on('click', function(e) {
            e.preventDefault();
            handler.open({
                name        : username,
                description : 'Stripe Card Update',
                label       : 'Update Card Details',
                email       : email
            });
        });
    </script>