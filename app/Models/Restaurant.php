<?php
/**
 * File name: Restaurant.php
 * Last modified: 2020.04.30 at 08:21:09
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\DB;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use Facades\App\Helpers\Helper;
use Facades\App\Models\Coupon;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Restaurant extends Model implements HasMedia
{
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }

    public $table = 'restaurants';
    


    public $fillable = [
          'name',
        'address',
        'latitude',
        'longitude',
        'phone',
        'mobile',
        'email',
        'information',
        'logo',
        'banner',
        'preparing_time',
        'min_order',
        'delivery_fee',
        'delivery_range',
        'default_tax',
        'accept_cash',
        'free_delivery',
        'has_riders',     
        'available_for_delivery',
        'food_truck',
        'admin_commission', 
        'bank_account', 
        'bank_name',
        'available_for_pickup',
        'featured',       
        'active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'image' => 'string',
        'address' => 'string',
        'latitude' => 'string',
        'banner' => 'string',
        'longitude' => 'string',
        'phone' => 'string',
        'mobile' => 'string',
        'admin_commission' =>'double',
        'delivery_fee'=>'double',
        'default_tax'=>'double',
        'delivery_range'=>'double',
        'available_for_delivery'=>'boolean',
        'has_riders'=>'boolean',
        'closed'=>'boolean',
        'information' => 'string',
        'active' =>'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $adminRules = [
        'name' => 'required',
        'description' => 'required',
        'delivery_fee' => 'nullable|numeric|min:0',
        'longitude' => 'required|numeric',
        'latitude' => 'required|numeric',
        'admin_commission' => 'required|numeric|min:0',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $managerRules = [
        'name' => 'required',
        'description' => 'required',
        'delivery_fee' => 'nullable|numeric|min:0',
        'longitude' => 'required|numeric',
        'latitude' => 'required|numeric',
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        'has_media',
        'app_close',
        'has_discount',
        'cuisine',
        'restaurant_banner',
        'open_date',
        'rate'
        
    ];

    /**
     * @param Media|null $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 200, 200)
            ->sharpen(10);

        $this->addMediaConversion('icon')
            ->fit(Manipulations::FIT_CROP, 100, 100)
            ->sharpen(10);
    }

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }
   


       /**
     * Add Media to api results
     * @return bool
     */
    public function getAppCloseAttribute()
    {
        $open_at=Helper::getKeyValue('open_at');
        $close_at=Helper::getKeyValue('close_at');
              // Check if store is available that day
        $now = Carbon::now();
        $day = $now->format('N');
        // Check store hours
        $app_open = Carbon::createFromFormat('H:i', $open_at);
        $app_close = Carbon::createFromFormat('H:i', $close_at);

        if ($now >= $app_open && $now <= $app_close) {
            return false;
        }       
        
        return true;
    }



       /**
     * Add Media to api results
     * @return bool
     */
    public function getOpenDateAttribute()
    {
         return Helper::findOpenDaysTime($this->id);
    }
  
        /**
     * Add Media to api results
     * @return bool
     */
    public function getHasDiscountAttribute()
    { 
      $discount = [];
      $coupon = Coupon::where('restaurant_id',$this->id)->where('user_id',null)->where('enabled','1')->where('expires_at','>',Carbon::now())->first();
      if($coupon != null)
       
       { 
        if($coupon->discount_type == 'fixed')
            $type = 'RM'; else $type = '%';

         return $discount = [
         'code' => $coupon->code,
         'message' => $coupon->description, 
         'amount' => $coupon->discount.$type
     ];
       
       }

       else return null; 
      
    }

    /**
     * Add Media to api results
     * @return bool
     */
    public function getRestaurantBannerAttribute()
    {
         $url = Config::get('app.url');
         $hungerpark = Str::contains($url, ['hungerpark']);
         $eatstation = Str::contains($url, ['eatstation']);
        if($eatstation)
        $url = 'https://eatstation.app/uploads/storeimage/';
         else if($eatstation)
        $url = 'https://hungerpark.com/uploads/storeimage/';
        else $url = 'https://spoongate.com/uploads/storeimage/';
   
        return $this->banner ? $url.$this->banner : $url.'image_default.png';
    }
    /**
     * to generate media url in case of fallback will
     * return the file type icon
     * @param string $conversion
     * @return string url
     */
    public function getFirstMediaUrl($collectionName = 'default',$conversion = '')
    {
        $url = $this->getFirstMediaUrlTrait($collectionName);
        $array = explode('.', $url);
        $extension = strtolower(end($array));
        if (in_array($extension,config('medialibrary.extensions_has_thumb'))) {
            return asset($this->getFirstMediaUrlTrait($collectionName,$conversion));
        }else{
            return asset(config('medialibrary.icons_folder').'/'.$extension.'.png');
        }
    }

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class,setting('custom_field_models',[]));
        if (!$hasCustomField){
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields','custom_fields.id','=','custom_field_values.custom_field_id')
            ->where('custom_fields.in_table','=',true)
            ->get()->toArray();

        return convertToAssoc($array,'name');
    }

    /**
     * Add Media to api results
     * @return bool
     */
    public function getHasMediaAttribute()
    {
        return $this->hasMedia('image') ? true : false;
    }

    /**
     * Add Media to api results
     * @return bool
     */
    public function getRateAttribute()
    {
        return $this->restaurantReviews()->select(DB::raw('round(AVG(restaurant_reviews.rate),1) as rate'))->first('rate')->rate;
    }
   
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function getCuisineAttribute()
    {
        $cuisine = [];
        $cuisines = \App\Models\RestaurantCuisine::where('restaurant_id',$this->id)->get();
        $count = 0;
        foreach ($cuisines as $key => $value) {
            if($count == 3) 
             break;
            $cuisine_name = \App\Models\Cuisine::find($value->cuisine_id)->name;
            $cuisine[] = $cuisine_name;
             $count++;
        }
        $cuisine = implode(' • ',$cuisine);
        return $cuisine;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function foods()
    {
        return $this->hasMany(\App\Models\Food::class, 'restaurant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function galleries()
    {
        return $this->hasMany(\App\Models\Gallery::class, 'restaurant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function restaurantReviews()
    {
        return $this->hasMany(\App\Models\RestaurantReview::class, 'restaurant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'user_restaurants');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function drivers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'driver_restaurants');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function cuisines()
    {
        return $this->belongsToMany(\App\Models\Cuisine::class, 'restaurant_cuisines');
    }

    public function discountables()
    {
        return $this->morphMany('App\Models\Discountable', 'discountable');
    }


}
