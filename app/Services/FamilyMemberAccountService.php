<?php

namespace App\Services;

use App\Models\FamilyMembers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FamilyMemberAccountService
{
    public function createAccountFromFamilyMember(FamilyMembers $familyMember): User
    {
        if ($familyMember->linked_user_id) {
            return User::findOrFail($familyMember->linked_user_id);
        }

        $parentUser = User::findOrFail($familyMember->user_id);
        $uniqueSuffix = $familyMember->id.'_'.time();

        $user = User::create([
            'name' => $familyMember->name,
            'gender' => $familyMember->gender ?? 'male',
            'birth_date' => $familyMember->birth_date,
            'education_level' => $familyMember->qualification,
            'marital_status' => 'married',
            'ethnic_branch_id' => $parentUser->ethnic_branch_id,
            'representative_id' => $parentUser->representative_id,
            'member_type' => 'normal',
            'status' => 'active',
            'role' => 'user',
            'monthly_fee' => 0,
            'register_date' => now()->toDateString(),
            'email' => 'family_'.$uniqueSuffix.'@shura.local',
            'password' => Hash::make(Str::random(12)),
        ]);

        $familyMember->update([
            'linked_user_id' => $user->id,
            'marital_status' => 'married',
        ]);

        return $user;
    }
}
