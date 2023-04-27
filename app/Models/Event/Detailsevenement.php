<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Module\Segment;

class Detailsevenement extends Model
{
    use HasFactory;
    protected $fillable=['segment_id','dysfonctionnement_id','evenement_id','volume_prevu','volume_realise'];



    public function segment()
    {
        return $this->belongsTo(Segment::class);
       
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
       
    }
    public function dysfonctionnement()
    {
        return $this->belongsTo(Dysfonctionnement::class);
       
    }

}
