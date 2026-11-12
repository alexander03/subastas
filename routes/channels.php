<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('auction.{bienId}', function (User $user, int $bienId) {
    //dd($user, $bienId);
    return true;
});

Broadcast::channel('subasta.{bienId}', function (User $user, int $bienId) {
    return true;
});