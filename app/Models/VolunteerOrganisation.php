<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
class VolunteerOrganisation extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];
    protected $table = 'volunteer_organisation';
    public function volunteer(){
        return $this->belongsTo(Volunteer::class, 'volunteer_id' , 'id');
    }
    public function organisationLogo() : Attribute{
        return Attribute::make(
            get : fn($value) => json_decode($value ? : null ) , 
        );
    }
}
