<?php

namespace Techenby\TransistorFm\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\User;
use Statamic\Http\Controllers\CP\CpController;

class EpisodesController extends CpController
{
    public function index(Request $request)
    {
        abort_unless(User::current()->can('view transistor-fm episodes'), 403);

        if (! $request->ajax()) {
            return view('transistor-fm::episodes');
        }

        return Episode::all();
    }
}
