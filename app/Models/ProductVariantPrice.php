<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $appends = ['Variants'];
    protected $fillable = ['product_variant_one','product_variant_two','product_variant_three','price','stock','product_id'];


    public function getVariantsAttribute($value)
    {
        
        $str='';
        if(!empty($this->variant_one)){
            $variant = $this->variant_one->variant;
            $str.=$variant.'/';
        }
        if(!empty($this->variant_two)){
            $variant = $this->variant_two->variant;
            $str.=$variant;
        }
        
        if(!empty($this->variant_three)){
            $str.='/'.$this->variant_three->variant;
        }
        return $str;
    }

    public function variant_one()
    {
        return $this->belongsTo(ProductVariant::class,'product_variant_one')->withDefault();;
    }

    public function variant_two()
    {
        return $this->belongsTo(ProductVariant::class,'product_variant_two')->withDefault();;
    }

    public function variant_three()
    {
        return $this->belongsTo(ProductVariant::class,'product_variant_three')->withDefault();;
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();;
    }


}
