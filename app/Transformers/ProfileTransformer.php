<?php

namespace App\Transformers;

use App\Entities\Profile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use League\Fractal\TransformerAbstract;

class ProfileTransformer extends TransformerAbstract
{

    /**
     * A Fractal transformer.
     * @param  $profile
     * @return array
     */
    public function transform($profile)
    {
        if (!is_null($profile)) {
            return [
                'region' => $profile->region,
                'city' => $profile->city,
                'address' => $profile->address,
                'coverage_area' => $profile->coverage_area,
                'available_from' => Carbon::parse($profile->available_from)->format('H:i'),
                'available_to' => Carbon::parse($profile->available_to)->format('H:i'),
                'photo' => $profile->photo != '' ? URL::to('public'.Storage::url($profile->photo)) : null,
                'driving_license' => $profile->driving_license != '' ? URL::to('public'.Storage::url($profile->driving_license)) : null,
                'roa_tax' => $profile->roa_tax != '' ? URL::to('public'.Storage::url($profile->roa_tax)) : null,
                'bike_photo' => $profile->bike_photo != '' ? URL::to('public'.Storage::url($profile->bike_photo)) : null,
                'ic_number' => $profile->ic_number != '' ? URL::to('public'.Storage::url($profile->ic_number)) : null,
                'bank_name' => $profile->bank_name,
                'account_number' => $profile->account_number,
                'stage' => $profile->stage,
            ];
        } else {
            return [];
        }

    }
}
