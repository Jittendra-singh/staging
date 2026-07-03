<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Setting;
use App\Traits\HasTranslations;


class EventTicket extends Model
{
    use HasFactory, HelperTrait, HasTranslations;

    protected $translatable = ['ticket_name', 'ticket_description'];

    protected $guarded = ['id'];

    public function soldTickets(){
        return $this->hasMany(EventOrderTicket::class, 'ticket_id');
    }

    public function approvedSold(){
        return $this->soldTickets()->active();
    }
    public function event(){
        return $this->belongsTo(Event::class , 'event_id');
    }

    public function remainingTickets(){
        $approvedSoldCount = $this->approvedSold->count();
        $remainingTickets  = $this->available_quantity - $approvedSoldCount;
        return $remainingTickets;
    }

    public function ticketPrice():Attriubte
    {
        return Attribute::make(
            get : fn($value) => $this->ticket_type  == 'paid' ?  $value : 0 , 
        );
    }
    public function payablePrice(): Attribute
    {
        return Attribute::make(
            get: function () {

                // Free ticket → no payable amount
                if ($this->ticket_type !== 'paid') {
                    return 0;
                }

                // Base price (raw DB value)
                $basePrice = (float) $this->getRawOriginal('ticket_price');

                if (!$this->event || $this->event->charge_bear_by != 1) {
                    return round($basePrice, 2);
                }
                
                // Get commission % (fallback to 0)
                $commissionPercent = (float) (Setting::value('event_commission') ?? 0);

                // Commission amount
                $commissionAmount = ($basePrice * $commissionPercent) / 100;

                // Final payable price
                return round($basePrice + $commissionAmount, 2);
            }
        );
    }
    public function allPrice(): Attribute
    {
        return Attribute::make(
            get: function () {

                // Free ticket → no payable amount
                if ($this->ticket_type !== 'paid') {
                    $re = [
                        'ticket_price' => 0,
                        'ticket_base_price' => 0,
                        'ticket_platform_fee' => 0,
                        'tikcet_org_payout' => 0,
                    ];
                    return $re;
                }

                // Base price (raw DB value)
                $basePrice = (float) $this->getRawOriginal('ticket_price');
                $commissionPercent = (float) (Setting::value('event_commission') ?? 0);
                $commissionAmount = ($basePrice * $commissionPercent) / 100;
                $basePriceR = round($basePrice, 2);

                if (!$this->event || $this->event->charge_bear_by != 1) {
                    $re = [
                        'ticket_price' => $basePriceR,
                        'ticket_base_price' => $basePriceR,
                        'ticket_platform_fee' => round($commissionAmount, 2),
                        'tikcet_org_payout' => round(($basePrice-$commissionAmount), 2),
                    ];
                    return $re;
                }

                $re = [
                    'ticket_price' => round($basePrice + $commissionAmount, 2),
                    'ticket_base_price' => $basePriceR,
                    'ticket_platform_fee' => round($commissionAmount, 2),
                    'tikcet_org_payout' => $basePriceR,
                ];
                return $re;
            }
        );
    }
}
