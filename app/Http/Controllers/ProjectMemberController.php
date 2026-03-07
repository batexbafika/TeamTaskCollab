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
    // Validate incoming request
    $validated = $request->validate([
        'userID'   => 'required|exists:users,id',
        'role'     => 'required|string|max:50',
        'joinedAt' => 'nullable|date',
    ]);

    // Create a new membership record
    $membership = ProjectMember::create([
        'projectID' => $projectID,
        'userID'    => $validated['userID'],
        'role'      => $validated['role'],
        'joinedAt'  => $validated['joinedAt'] ?? now(),
    ]);

    return response()->json([
        'message'    => 'Member added successfully',
        'membership' => $membership
    ]);
}

 //remove a member from a project
   public function destroy($projectID, $membershipID)
{
    // Find the membership record by project + membershipID
    $membership = ProjectMember::where('projectID', $projectID)->where('membershipID', $membershipID)->firstOrFail();

    $membership->delete();

    return response()->json([
        'message' => 'Member removed successfully'
    ]);
}

//update a member's role in a project
public function update(Request $request, $projectID, $membershipID)
{
    // Validate incoming request 'role'
    $validated = $request->validate([
        'role'     => 'sometimes|required|string|max:50',
    ]);

    // Find the membership record
    $membership = ProjectMember::where('projectID', $projectID)->where('membershipID', $membershipID)->firstOrFail();

    // Update with validated data
    $membership->update($validated);

    return response()->json([
        'message'    => 'Member updated successfully',
        'membership' => $membership
    ]);
}
 
}
