<?php


// app/Http/Controllers/UserController.php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User1; // Import the correct User1 class
 // Import the correct User1 class

class UserController extends Controller
{
    public function store(Request $request)
    {
        $user = new User1();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        // Redirect or return a response
    }
}
