<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filterStaffName = $request->string('filterStaffName');

        $staffs = User::where('role', UserRole::Staff)
            ->when($filterStaffName, function ($query) use ($filterStaffName) {
                $query->where(function ($q) use ($filterStaffName) {
                    $q->where('name', 'like', "%$filterStaffName%")
                        ->orWhere('email', 'like', "%$filterStaffName%");
                });
            })
            ->paginate(10);

        return inertia('Admin/Staff/StaffsRecord', compact('staffs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CreateNewUser $createNewUser): RedirectResponse
    {
        $createNewUser->create($request->all());

        return redirect()->route('admin.staffs.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $staff, UpdateUserProfileInformation $updateUserProfile): RedirectResponse
    {
        $updateUserProfile->update($staff, $request->all());

        return redirect()->route('admin.staffs.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $staff): RedirectResponse
    {
        $staff->delete();

        return redirect()->route('admin.staffs.index');
    }
}
