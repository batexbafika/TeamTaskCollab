<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    //index to get all members in a project
    public function index($projectId)
{
    $project = Project::with('members')->findOrFail($projectId); //uses the member()  relationship
    return response()->json($project->members);
}

//store to add a member to a project
public function store(Request $request, $projectID)
{
    $project = Project::findOrFail($projectID);

    // Authorize against the project
    $this->authorize('create', [ProjectMember::class, $project]);

    $validated = $request->validate([
        'userID' => 'required|exists:users,userID',
        'role'   => 'required|string|max:50',
    ]);

    $membership = ProjectMember::create([
        'projectID' => $projectID,
        'userID'    => $validated['userID'],
        'role'      => $validated['role'],
        'joinedAt'  => now(),
    ]);

    return response()->json([
        'message'    => 'Member added to project successfully',
        'membership' => $membership
    ]);
}


 //remove a member from a project
  public function destroy($projectID, $membershipID)
{
    $membership = ProjectMember::where('projectID', $projectID)
        ->where('membershipID', $membershipID)
        ->firstOrFail();

    $this->authorize('delete', $membership);

    $membership->delete();

    return response()->json([
        'message' => 'Member removed successfully'
    ]);
}


//update a member's role in a project
public function update(Request $request, $projectID, $membershipID)
{
    $membership = ProjectMember::where('projectID', $projectID)
        ->where('membershipID', $membershipID)
        ->firstOrFail();

    $this->authorize('update', $membership);

    $validated = $request->validate([
        'role' => 'sometimes|required|string|max:50',
    ]);

    $membership->update($validated);

    return response()->json([
        'message'    => 'Member updated successfully',
        'membership' => $membership
    ]);
}

 
}
