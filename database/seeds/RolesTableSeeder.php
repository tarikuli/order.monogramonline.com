<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;

class RolesTableSeeder extends Seeder
{
    private $roles = [
        'OWNER'  => 'Store Owner',
        'SUPR'   => 'Supervisor',
        'INVM'   => 'Inventory Manager',
        'OPER'   => 'Customer Service',
        'VEND'   => 'Drop/Shipper',
        'ACCT'   => 'Accountant',
        'CSR'    => 'Call center/no update',
        'OWNERX' => 'Store owner excluding user management',
        'USERM'  => 'Only manage users',
    ];

    public function run ()
    {
        foreach ( $this->roles as $name => $display_name ) {
            $role = new Role();
            $role->name = $name;
            $role->display_name = $display_name;
            $role->description = $display_name;
            $role->save();
        }
    }
}
