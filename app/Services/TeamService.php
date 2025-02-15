<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TeamService
{
    public function createTeam(array $data) {
        $exists = DB::table('teams')->where('name', $data['name'])->exists();

        if ($exists) {
            return [
                'error' => 'Team name already exists',
                'status' => 400
            ];
        }

        $team = DB::table('teams')->insert($data);
        
        $newTeam = DB::table('teams')->where('name', $data['name'])->first();

        return [
            'success' => 'Team created successfully',
            'team' => $newTeam,
            'status' => 201
        ];
    }

    public function getTeams() {
        return DB::table('teams')
            ->whereNull('deleted_at')
            ->select('id', 'team_name', 'created_at')
            ->get();
    }

    public function addMemberToTeam($teamId, $userId) {
        return DB::table('team_members')->insert([
            'team_id' => $teamId,
            'user_id' => $userId,
            'created_at' => now(),
        ]);
    }


    public function getById($id) {
        return DB::table('teams')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }

    public function deleteById($id) {
        return DB::table('teams')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);
    }

      public function getMembers($teamId)
    {
        return DB::table('team_members')
            ->join('users', 'team_members.user_id', '=', 'users.id')
            ->where('team_members.team_id', $teamId)
            ->select('users.id', 'users.name', 'users.email')
            ->get();
    }
}
