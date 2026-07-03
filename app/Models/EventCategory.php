<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\HasTranslations;
class EventCategory extends Model
{
    use HasFactory , HelperTrait, HasTranslations;

    protected $translatable = ['name'];

    protected $guarded = ['id'];
    protected $table = 'event_categories';

    public function events(){
        return $this->hasMany(Event::class, 'event_category_id');
    }
    public function defaultBannerImg() : Attribute
    {
        return Attribute::make(
            get : fn($value) => json_decode($value ?: null),
        );
    }

    public function formattedDefaultBannerImg()
    {
        if (!$this->default_banner_img) {
            return asset('assets_front/images/event-imgs/big-2.jpg');
        }

        return asset($this->default_banner_img->path);
    }
}
