<div class="order-total-block mt-4">
    @foreach ($ticket_booking as $ticket)
        <div class="order-total-dt">
            <div class="order-text mb-1"><h6><b>{{$ticket['count']}}x</b> {{$ticket['name']}}</h6></div>
            <div class="order-number mb-1"><h6>{{config('app.currency')}}{{formatAmt($ticket['price'])}}</h6></div>
        </div>
    @endforeach
    @if($event->allow_donations==1 && !empty($event->creator->stripe_email) && !empty($event->creator->stripe_account_id))
    <div class="support-cause-box mt-3" >
        <div class="d-flex align-items-start">
            <div class="flex-grow-1">
                <h6 class="support-title mb-3">Support our cause</h6>
                <small class="support-desc">
                Would you like to make an optional donation to the Organizer to help support this cleanup?
                </p>

                <!-- Donation Options -->
                <div class="donation-options mt-2">
                    <label class="donation-radio mb-0">
                        <input type="radio" name="donation_type" value="10">
                        <span>$10</span>
                    </label>

                    <label class="donation-radio mb-0">
                        <input type="radio" name="donation_type" value="25">
                        <span>$25</span>
                    </label>

                    <label class="donation-radio mb-0">
                        <input type="radio" name="donation_type" value="50">
                        <span>$50</span>
                    </label>

                    <label class="donation-radio mb-0">
                        <input type="radio" name="donation_type" value="custom">
                        <span>Custom</span>
                    </label>
                </div>
                
                <div class="mt-2" id="donation-price" style="display: none;">
                    <input inputmode="decimal" autocomplete="off" id="donation_price_field"
                        type="number"
                        min="1"
                        step="1"
                        class="form-control form-control-sm integeronly"
                        name="donation_amount"
                        placeholder="Enter an amount of $1.00 or more">
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="order-total-dt mt-4 pt-1">
        <div class="order-text mb-3">Sub Total</div>
        <div class="order-number mb-3">{{config('app.currency')}}<span id="f-sub-total">{{ formatAmt($sub_total) }}</span></div>
    </div>
    <div class="order-total-dt mb-2">
        <div class="order-text mb-2"><h6>Fee Included</h6></div>
        <div class="order-number mb-2" ticketfee="{{ $ticket_fees }}"><h6>{{config('app.currency')}}{{ formatAmt($ticket_fees) }}</h6></div>
    </div>
    <div class="order-total-dt donation-total-div" style="display:none;">
        <div class="order-text">Donation</div>
        <div class="order-number">{{config('app.currency')}}<span id="donation-total"></span></div>
    </div>
    <div class="divider-line"></div>
    <div class="order-total-dt">
        <div class="order-text">Total</div>
        <div class="order-number ttl-clr">{{config('app.currency')}}<span id="f-grand-total">{{ formatAmt($final_total) }}</span></div>
    </div>
</div>
<style>
.support-cause-box {
  background: #f7faff;
  border: 1px solid #d6e4ff;
  border-radius: 10px;
  padding: 16px;
}

.support-icon .icon-circle {
  width: 36px;
  height: 36px;
  background: #e8f0ff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.support-title {
  font-weight: 600;
  color: #1f2937;
}

.support-desc {
  font-size: 14px;
  color: #6b7280;
}

.donation-options {
  display: flex;
  flex-wrap: wrap;
}

.donation-radio {
  margin-right: 8px;
  margin-bottom: 8px;
  cursor: pointer;
}

.donation-radio input {
  display: none;
}

.donation-radio span {
  display: inline-block;
  padding: 6px 14px;
  border: 1px solid #2f80ed;
  border-radius: 6px;
  color: #2f80ed;
  font-size: 13px;
  font-weight: 500;
}

.donation-radio input:checked + span {
  background: #2f80ed;
  color: #fff;
}

#donation_price_field::-webkit-outer-spin-button,
#donation_price_field::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

</style>