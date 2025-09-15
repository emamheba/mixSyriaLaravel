<?php

namespace Modules\Membership\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipType extends Model
{
    use HasFactory;

    protected $fillable = ['type','validity'];
    protected $casts = ['status'=>'integer'];
    public static function all_types()
    {
        return self::select(['id','type','validity'])->get();
    }

    public function memberships()
    {
        return $this->HasMany(Membership::class,'membership_type_id','id');
    }
}
