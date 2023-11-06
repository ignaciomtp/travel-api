<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Password;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name'] = $this->ask('Name of the new user: ');
        $user['email'] = $this->ask('Email of the new user: ');
        $user['password'] = $this->secret('Password of the user: '); // secret similar a ask, pero no muestra lo que escribimos.

        $roleName = $this->choice('Role of the new user: ', ['admin', 'editor'], 1); // El 1 es la opción por defecto

        $role = Role::where('name', $roleName)->first();    // Comprobamos que exista el rol
        if(!$role) {
            $this->error('Role not found');
            return -1;
        }

        $validator = Validator::make($user, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
        ]);

        if($validator->fails()) {
            foreach($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return -1;
        }

        DB::transaction(function() use($user, $role){
            $user['password'] = Hash::make($user['password']);
            $newUser = User::create($user);
            $newUser->roles()->attach($role->id);           
        });


        $this->info('User '.$user['email'].' created successfully');

        return 0;   // un comando artisan que devuelve 0 significa éxito. Cualquier otra cosa significa fail.
    }
}
