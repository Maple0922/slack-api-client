<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class NotionController extends Controller
{
    public function roadmapCreated(Request $request)
    {
        $data = $request->all();

        // Process the data as needed
        // For example, you can log it or send a response
        \Log::info('Notion Webhook Data:', $data);

        return response()->json(['status' => 'success']);
    }
}
