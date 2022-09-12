<?php

namespace App\Http\Controllers\Api\v1\Listing;

use Auth;
use Spatie\Fractal\Fractal;
use Illuminate\Support\Facades\DB;
use App\Modules\Listing\Models\Listing;
use Spatie\Fractalistic\ArraySerializer;
use App\Modules\Account\User\Models\User;
use App\Http\Controllers\Api\ApiController;
use App\Modules\Chat\Models\ChatParticipant;
use App\Modules\Listing\Transformers\ListingTransformer;

/**
* @group Listing endpoints
*/
class ListingApiController extends ApiController
{
    // returns all listings posted by user
    public function index()
    {
        $user = Auth::user();

        $listings = Listing::with("user")
                            ->whereHas("user", function ($query) use ($user) {
                                return $query->where('user_id', '=', $user->id);
                            })
                            ->get();

        $listings = Fractal::create()
                    ->collection($listings)
                    ->transformWith(new ListingTransformer())
                    ->serializeWith(new ArraySerializer())
                    ->toArray();

        return $this->respondSuccess($listings, trans('api.generic.index.success', ['resource' => 'Messages']));
    }

    // returns all listings ordered by most recent
    public function getAllListings()
    {
        $listings = Listing::with("user")
                            ->orderBy('deprioritized')
                            ->orderBy('updated_at', 'desc')
                            ->get();

        $listings = Fractal::create()
                    ->collection($listings)
                    ->transformWith(new ListingTransformer())
                    ->serializeWith(new ArraySerializer())
                    ->toArray();

        return $this->respondSuccess($listings, trans('api.generic.index.success', ['resource' => 'Messages']));
    }
}
