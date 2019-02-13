

<div id="payment_info" class="eachItem">
    <h2>My Payment Information</h2>
    <p>You have added below card information to your account.</p>
    <div class="updateCardWrap">
        <p>credit card information</p>
        <div class="updateCard">
            <div class="cardInfo cardNumber">
                <h4>card number</h4>
                <h3>xxxx xxxx xxxx <span id="card-last-4">{{$card['last4']}}</span></h3>
            </div>
            <div class="cardInfo expiryDate">
                <h4>expiry (mm/yy)</h4>
                <h3>{{getCardMonth($card['exp_month'])}}/{{getCardYear($card['exp_year'])}}</h3>
            </div>
            <button id="update-card-btn" class="orangeBtn">Update Card</button>
        </div>
    </div>
</div>