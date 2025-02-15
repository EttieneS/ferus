<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller {
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        return response()->json($this->teamService->getTeams());
    }

    public function create(Request $request)
    {
        $request->validate([
            'team_name' => 'required|string|max:255',
        ]);

        $teamId = $this->teamService->createTeam($request->all());

        return response()->json(['message' => 'Team created successfully!', 'team_id' => $teamId], 201);
    }

    public function show($id)
    {
        return response()->json($this->teamService->getById($id));
    }

    public function destroy($id)
    {
        $this->teamService->deleteById($id);

        return response()->json(['message' => 'Team deleted successfully!'], 200);
    }

    public function addMember(Request $request, $teamId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $this->teamService->addMemberToTeam($teamId, $request->user_id);

        return response()->json(['message' => 'Member added successfully!']);
    }

    public function getMembers($teamId)
    {
        return response()->json(['members' => $this->teamService->getMembers($teamId)]);
    }
}
