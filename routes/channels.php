<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use App\Models\Admin;

// Authorize private channel for User notifications
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // In this app users are not authenticated via standard Laravel auth,
    // so we simply allow if session account_id matches the id.
    return session('account_id') == $id && session('account_role') === 'user';
});

Broadcast::channel('App.Models.Admin.{id}', function ($admin, $id) {
    return session('account_id') == $id && session('account_role') === 'admin';
});
