<?php

namespace Database\Seeders;

use App\Enum\User\UserGenderEnum;
use App\Enum\User\UserTypeEnum;
use App\Models\Location;
use App\Services\Global\RoleService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove the relationships from pivot tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        (new RoleService())->handle();

        User::query()
            ->firstOrCreate(
                [
                    'email' => 'root@perfect.com',
                ],
                [
                    'name' => 'root',
                    'password' => '123456',
                    'phone' => '5412545214',
                    'type' => UserTypeEnum::ADMIN,
                ],
            )
            ->assignRole('root');

        User::query()
            ->firstOrCreate(
                [
                    'email' => 'admin@perfect.com',
                ],
                [
                    'name' => 'admin',
                    'password' => '123456',
                    'phone' => '5412545215',
                    'type' => UserTypeEnum::ADMIN,
                ],
            )
            ->assignRole('admin');
    }
}
