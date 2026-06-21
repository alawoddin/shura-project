<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    public function familyMembers()
    {
        return $this->hasMany(FamilyMembers::class, 'user_id');
    }

    public function familyMember()
    {
        return $this->familyMembers();
    }

    public function receivePayments()
    {
        return $this->hasMany(ReceivePayment::class);
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function aids()
    {
        return $this->hasMany(Aids::class);
    }

    public function financialReports()
    {
        return $this->hasMany(MemberFinancialReport::class, 'member_id');
    }

     public static function getpermissionGroups(){
        $permission_groups = DB::table('permissions')->select('group_name')->groupBy('group_name')->get();
        return $permission_groups;
    }
    // End Method 

     public static function getpermissionByGroupName($group_name){
        $permissions = DB::table('permissions')
                        ->select('name','id')
                        ->where('group_name',$group_name)
                        ->get();
                        return $permissions;

    }
      // End Method 

         public static function roleHasPermissions($role, $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {
                return false;
            }
        }

        return $permissions->isNotEmpty();
    }

    public function canManageAccess(): bool
    {
        if ($this->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        if ($this->can('manage.roles')) {
            return true;
        }

        return $this->role === 'admin' && $this->roles()->count() === 0;
    }
      // End Method
      





    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
