<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $users = User::when(
            $request->has('column') && $request->has('search'),
            fn (Builder $builder) => $builder->whereLike($request->input('column'), "%{$request->input('search')}%")
        )
            ->paginate(10)->withQueryString();

        return Inertia::render('Dashboard', compact('users'));
    }
}
