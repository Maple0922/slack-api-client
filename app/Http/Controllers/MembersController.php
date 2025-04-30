<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
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
                'kpis' => $member->kpis
                    ->map(fn($kpi) => [
                        'id' => $kpi->id,
                        'content' => $kpi->content
                    ]),
                'isValid' => $member->is_valid
            ])
            ->sortByDesc('targetPoint')
            ->values();

        return $members;
    }

    public function create(Request $request): Member
    {
        $member = Member::create([
            'notion_id' => $request->input('id'),
            'name' => $request->input('name'),
            'team_id' => $request->input('team.id'),
            'image_url' => $request->input('imageUrl'),
            'target_point' => $request->input('targetPoint'),
            'is_valid' => $request->input('isValid')
        ]);

        collect($request->input('kpis'))
            ->each(fn($kpi) =>
            Kpi::create([
                'member_notion_id' => $member->notion_id,
                'content' => $kpi['content']
            ]));

        return $member;
    }


    public function update(Request $request, string $id)
    {
        collect($request->input('kpis'))
            ->each(fn($kpi) =>
            Kpi::updateOrCreate(
                ['id' => $kpi['id']],
                [
                    'member_notion_id' => $id,
                    'content' => $kpi['content']
                ]
            ));

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
