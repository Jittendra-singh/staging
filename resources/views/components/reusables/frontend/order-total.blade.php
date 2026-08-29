<div class="order-total-block mt-4">
    @foreach ($ticket_booking as $ticket)
        <div class="order-total-dt">
            <div class="order-text mb-0"><h6 class="mb-1"><b>{{$ticket['count']}}x</b> {{$ticket['name']}}</h6></div>
            <div class="order-number mb-0">
                @if($ticket['price'] > 0)
                <h6 class="mb-1">{{config('app.currency')}}{{formatAmt($ticket['price'] * $ticket['count'])}}</h6>
                @else
                <h6 class="mb-1">Free</h6>
                @endif
            </div>
        </div>
        @if($ticket['price'] > 0)
        @if($event->charge_bear_by==1)
        <div class="order-total-dt">
            <div class="order-text mb-0 ms-3"><small style="font-weight: 100;font-size: 13px;">Base Ticket Price ({{$ticket['count']}}x {{config('app.currency')}}{{formatAmt($ticket['base_price'])}})</small></div>
            <div class="order-number mb-0"><small style="font-weight: 100;font-size: 13px;">{{config('app.currency')}}{{formatAmt($ticket['base_price'] * $ticket['count'])}}</small></div>
        </div>
        @endif
        <!-- <div class="order-total-dt mb-3">
            <div class="order-text mb-1"><small style="font-weight: 100;font-size: 13px;">Platform Fee ({{$setting->event_commission}}%)</small></div>
            <div class="order-number mb-1"><small style="font-weight: 100;font-size: 13px;">{{config('app.currency')}}{{formatAmt(($ticket['base_price']*$setting->event_commission)/100)}}</small></div>
        </div> -->
        <div class="order-total-dt mb-3">
            <div class="order-text mb-0 ms-3">
                <small style="font-weight:100;font-size:13px;">
                    Processing Fee 
                    <i class="fa-solid fa-circle-info ms-1 text-muted" 
                       title="Platform Fee ({{ $setting->event_commission }}%): {{ config('app.currency') }}{{ formatAmt($ticket['platform_fee'] * $ticket['count']) }}&#10;Payment Fee ({{ $processing_fee_per }}%): {{ config('app.currency') }}{{ formatAmt($ticket['processing_fee'] * $ticket['count']) }}"
                       style="cursor: help;"></i>
                    {{ $buyer_pays_fees ? '' : 'Included' }}
                </small>
            </div>
            <div class="order-number mb-0">
                <small style="font-weight:100;font-size:13px;">
                    {{ config('app.currency') }}{{ formatAmt(($ticket['platform_fee'] + $ticket['processing_fee']) * $ticket['count']) }}
                </small>
            </div>
        </div>
        @else
        <div class="order-total-dt mb-3"></div>
        @endif
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
                        step="0.01"
                        class="form-control form-control-sm decimalonly"
                        name="donation_amount"
                        placeholder="Enter an amount of $1.00 or more">
                </div>
                <!-- <div class="order-total-dt donation-processing-row" style="display:none;">
                    <div class="order-text">
                        Processing Fee on Donation
                        ({{ $processing_fee_per }}%)
                    </div>
                    <div class="order-number">
                        {{ config('app.currency') }}<span id="donation-processing-fee"></span>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
    @endif
    <div class="order-total-dt mt-4 pt-1 d-none">
        <div class="order-text mb-3">Sub Total</div>
        <div class="order-number mb-3">{{config('app.currency')}}<span id="f-sub-total">{{ formatAmt($sub_total) }}</span></div>
    </div>

    <div class="order-total-dt mb-2 d-none">
        <div class="order-text mb-2">
            <h6>
                Platform Fee ({{ $setting->event_commission }}%)
                {{ $buyer_pays_fees ? '' : 'Included' }}
            </h6>
        </div>
        <div class="order-number mb-2">
            <h6>{{ config('app.currency') }}{{ formatAmt($platform_fee_total) }}</h6>
        </div>
    </div>
    @if((float) $processing_fee_per > 0)
        <div class="order-total-dt mb-2 d-none">
            <div class="order-text mb-2">
                <h6>
                    Processing Fee ({{ $processing_fee_per }}%)
                    {{ $buyer_pays_fees ? '' : 'Included' }}
                </h6>
            </div>
            <div class="order-number mb-2">
                <h6>{{ config('app.currency') }}{{ formatAmt($processing_fee_total) }}</h6>
            </div>
        </div>
    @endif


    <div class="order-total-dt donation-total-div" style="display:none;">
        <div class="order-text">Donation</div>
        <div class="order-number">{{config('app.currency')}}<span id="donation-total"></span></div>
    </div>
    <div class="order-total-dt mb-2 donation-processing-row" style="display:none;">
        <div class="order-text mb-2">
            <h6>
                Processing Fee on Donation ({{ $processing_fee_per }}%)
            </h6>
        </div>
        <div class="order-number mb-2">
            <h6>{{ config('app.currency') }}<span id="donation-processing-fee"></span></h6>
        </div>
    </div>

    <div class="divider-line"></div>
    <div class="order-total-dt">
        <div class="order-text">Total</div>
        <div class="order-number ttl-clr">{{config('app.currency')}}<span id="f-grand-total">{{ formatAmt($final_total) }}</span></div>
    </div>
    <!-- <div class="order-total-dt">
        <div class="order-text mb-1"><small style="font-weight: 100;" class="m-0">Processing fees are estimated (~3% + $0.30) and may vary based on payment method and card type. The final amount is determined by Stripe.</small></div>
    </div> -->
    <!-- <div class="order-total-dt">
        <div class="order-text mb-1">
            <small style="font-weight:100;" class="m-0">
                Processing fee is calculated at {{ $processing_fee_per }}%.
            </small>
        </div>
    </div> -->

    <input
        type="hidden"
        id="checkout-fee-config"
        data-processing-percent="{{ $processing_fee_per }}"
        data-buyer-pays="{{ $buyer_pays_fees ? 1 : 0 }}"
    >
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
