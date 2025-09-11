<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Sarpras Wikrama',
            'username' => 'sarpraswikrama',
            'role' => 'superadmin',
            'password' => bcrypt('sarpraswikrama'),
        ]);

        // PPLG Users

        User::create([
            'name' => 'Juliana Mansur, M. Kom.',
            'username' => 'kaprogpplg',
            'role' => 'admin',
            'password' => bcrypt('wajibngulik'),
            'major_id' => 1
        ]);

        User::create([
            'name' => 'Lab PPLG',
            'username' => 'labpplg',
            'role' => 'user',
            'password' => bcrypt('wajibngulik'),
            'major_id' => 1
        ]);

        // DKV Users

        User::create([
            'name' => 'Kaprog DKV',
            'username' => 'kaprogdkv',
            'role' => 'admin',
            'password' => bcrypt('kreatifinovatif'),
            'major_id' => 2
        ]);

        User::create([
            'name' => 'Lab DKV',
            'username' => 'labdkv',
            'role' => 'user',
            'password' => bcrypt('kreatifinovatif'),
            'major_id' => 2
        ]);

        // TJKT Users

        User::create([
            'name' => 'Kaprog TJKT',
            'username' => 'kaprogtjkt',
            'role' => 'admin',
            'password' => bcrypt('pahlawanjaringan'),
            'major_id' => 3
        ]);

        User::create([
            'name' => 'Lab TJKT',
            'username' => 'labtjkt',
            'role' => 'user',
            'password' => bcrypt('pahlawanjaringan'),
            'major_id' => 3
        ]);

        // MPLB Users

        User::create([
            'name' => 'Kaprog MPLB',
            'username' => 'kaprogmplb',
            'role' => 'admin',
            'password' => bcrypt('beraniberprestasi'),
            'major_id' => 4
        ]);

        User::create([
            'name' => 'Lab MPLB',
            'username' => 'labmplb',
            'role' => 'user',
            'password' => bcrypt('beraniberprestasi'),
            'major_id' => 4
        ]);

        // KLN Users

        User::create([
            'name' => 'Kaprog KLN',
            'username' => 'kaprogkln',
            'role' => 'admin',
            'password' => bcrypt('yeschef'),
            'major_id' => 5
        ]);

        User::create([
            'name' => 'Lab KLN',
            'username' => 'labkln',
            'role' => 'user',
            'password' => bcrypt('yeschef'),
            'major_id' => 5
        ]);

        // HTL Users

        User::create([
            'name' => 'Kaprog HTL',
            'username' => 'kaproghtl',
            'role' => 'admin',
            'password' => bcrypt('handsonmindson'),
            'major_id' => 6
        ]);

        User::create([
            'name' => 'Lab HTL',
            'username' => 'labhtl',
            'role' => 'user',
            'password' => bcrypt('handsonmindson'),
            'major_id' => 6
        ]);

        // PMN Users

        User::create([
            'name' => 'Kaprog PMN',
            'username' => 'kaprogpmn',
            'role' => 'admin',
            'password' => bcrypt('dapetincuan'),
            'major_id' => 7
        ]);

        User::create([
            'name' => 'Lab PMN',
            'username' => 'labpmn',
            'role' => 'user',
            'password' => bcrypt('dapetincuan'),
            'major_id' => 7
        ]);
    }
}
