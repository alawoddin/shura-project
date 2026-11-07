<?php

/*
|--------------------------------------------------------------------------
| Permissions of the system (with Persian names)
|--------------------------------------------------------------------------
|
| This is the ONE place where permissions are listed. It is used by:
|   - database/seeders/PermissionsSeeder.php (creates the permissions and gives them to the admin roles)
|   - the permission pages (all / add / edit permission)
|   - the role-permission pages (add / all / edit roles permission)
|
| To add a new page permission: add it here, run
|   php artisan db:seed --class=PermissionsSeeder
| and protect the page's routes with ->middleware('permission:<name>').
|
*/

return [

    // group key => Persian name of the group
    'groups' => [
        'Users' => 'کاربران',
        'KeyPeople' => 'افراد کلیدی',
        'Category' => 'بخش‌ها و حساب‌ها',
        'Income' => 'درآمدها',
        'Undeposited' => 'واریز نشده',
        'Recieve' => 'دریافت فیس و قرض',
        'AccountTransactions' => 'انتقال بین حساب‌ها',
        'Financial' => 'گزارش مالی اعضا',
        'Credits' => 'کریدت‌ها (قرض‌ها)',
        'Aid' => 'کمک‌ها',
        'Expense' => 'مصارف',
        'Reports' => 'گزارشات عمومی',
        'UsersReport' => 'گزارش کاربران',
        'ActivityLog' => 'گزارش فعالیت‌ها',
        'Access' => 'مدیریت دسترسی',
    ],

    // permission name => [group key, Persian name]
    'permissions' => [
        'all.users' => ['Users', 'صفحه کاربران'],
        'all.key.people' => ['KeyPeople', 'صفحه افراد کلیدی'],
        'all.category' => ['Category', 'صفحه بخش‌ها و حساب‌ها'],
        'all.income' => ['Income', 'صفحه درآمدها'],
        'all.undeposited' => ['Undeposited', 'صفحه واریز نشده‌ها'],
        'all.recieve.payment' => ['Recieve', 'صفحه دریافت فیس و قرض'],
        'all.account.transactions' => ['AccountTransactions', 'صفحه انتقال بین حساب‌ها و دریافت پول'],
        'all.financial.report' => ['Financial', 'صفحه گزارش مالی اعضا'],
        'all.credits' => ['Credits', 'صفحه کریدت‌ها (قرض‌ها)'],
        'all.aid' => ['Aid', 'صفحه کمک‌ها'],
        'all.expense' => ['Expense', 'صفحه مصارف'],
        'all.report' => ['Reports', 'صفحه گزارشات عمومی (روزانه، ماهانه، سالانه)'],
        'all.users.report' => ['UsersReport', 'صفحه گزارش کاربران'],
        'all.activity.logs' => ['ActivityLog', 'صفحه گزارش فعالیت‌ها'],
        'manage.roles' => ['Access', 'مدیریت نقش‌ها و مجوزها'],
    ],

];
