<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    public function index()
    {
        $teams = Team::get()
            ->map(fn($team) => [
                'id' => $team->id,
                'name' => $team->name
            ]);

        return $teams;
    }
}
