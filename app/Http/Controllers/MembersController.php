<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    public function index()
    {
        $members = Member::with('team')
            ->get()
            ->map(fn($member) => [
                'id' => $member->notion_id,
                'name' => $member->name,
                'imageUrl' => $member->image_url,
                'team' => [
                    'id' => $member->team->id ?? null,
                    'name' => $member->team->name ?? null
                ],
                'targetPoint' => $member->target_point,
                'isValid' => $member->is_valid
            ])
            ->sortByDesc('targetPoint')
            ->values();

        return $members;
    }

    public function create(Request $request): Member
    {
        return Member::create([
            'notion_id' => $request->input('id'),
            'name' => $request->input('name'),
            'team_id' => $request->input('team.id'),
            'image_url' => $request->input('imageUrl'),
            'target_point' => $request->input('targetPoint'),
            'is_valid' => $request->input('isValid')
        ]);
    }


    public function update(Request $request, string $id)
    {
        Member::where('notion_id', $id)
            ->update([
                'name' => $request->input('name'),
                'image_url' => $request->input('imageUrl'),
                'team_id' => $request->input('team.id'),
                'target_point' => $request->input('targetPoint'),
                'is_valid' => $request->input('isValid')
            ]);
    }

    public function delete(string $id): void
    {
        Member::where('notion_id', $id)->delete();
    }
}
