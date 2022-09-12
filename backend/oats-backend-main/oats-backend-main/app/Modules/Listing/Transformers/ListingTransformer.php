<?php

namespace App\Modules\Listing\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use App\Modules\Listing\Models\Listing;

class ListingTransformer extends TransformerAbstract
{
    public function transform(Listing $listing)
    {
        $listingArray = [
			'listing_id'=> $listing->id,
            'user_id' => $listing->user_id,
            'username' => $listing->user->username,
            'title' => $listing->title,
            'description' => $listing->description,
            'category' => $listing->category,
            'price' => $listing->price,
            'listed_date' => Carbon::parse($listing->listed_date)->format('d M Y'),
            'deprioritized' => (bool) $listing->deprioritized,
            'created_at' => Carbon::parse($listing->created_at)->format('d M Y'),
            'updated_at' => Carbon::parse($listing->updated_at)->format('d M Y')
        ];

        return $listingArray;
    }
}
