<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $list = [
            [
                'id' => 1,
                'name' => 'Пользователь',
                'slug' => 'user'
            ],
            [
                'id' => 2,
                'name' => 'Администратор',
                'slug' => 'admin'
            ]
        ];

        foreach ($list as $item) {
            Role::query()->firstOrCreate([
                'id' => $item['id']
            ], $item);
        }
    }
}
