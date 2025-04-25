<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    public function index()
    {
        $members = Member::get()
            ->map(fn($member) => [
                'id' => $member->notion_id,
                'name' => $member->name,
                'imageUrl' => $member->image_url,
                'targetPoint' => $member->target_point,
                'isValid' => $member->is_valid
            ]);

        return $members;
    }

    public function create(Request $request): Member
    {
        return Member::create([
            'notion_id' => $request->input('id'),
            'name' => $request->input('name'),
            'image_url' => $request->input('imageUrl'),
            'target_point' => $request->input('targetPoint'),
            'is_valid' => $request->input('isValid')
        ]);
    }


    public function update(Request $request, int $id): Member
    {
        $member = Member::where('notion_id', $id)->firstOrFail();

        $member->update([
            'notion_id' => $request->input('id'),
            'name' => $request->input('name'),
            'image_url' => $request->input('imageUrl'),
            'target_point' => $request->input('targetPoint'),
            'is_valid' => $request->input('isValid')
        ]);

        return $member;
    }
}
