<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionH extends Model
{
    use HasFactory;
    protected $fillable = ['id','title_1','title_2','description_1','description_2','adresse','data_sectionh','format_id','page_id','code','url'];
   
}
