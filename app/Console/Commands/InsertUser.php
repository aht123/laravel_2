<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// app/Console/Commands/InsertUser.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class InsertUser extends Command
{
    protected $signature = 'insert:user';

    protected $description = 'Insert a user into the database';

    public function handle()
    {
        $name = $this->ask('Enter user name:');
        $email = $this->ask('Enter user email:');

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->save();

        $this->info('User inserted successfully.');
    }
}
