<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Validator;

class UserController extends Controller
{
    public function __construct()
    {
        // Je protège la vue aux personnes connectées
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::paginate(5);


        return view('users.index')->with('users', $users);
    }

    public function create(Request $request)
    {
        // Je vais faire comme d'habitude mes contrôles
        // Mon stockage puis le renvois de la data vers la vue

        $values = $request->all();
        $rules  = [
            'email'     => 'email|required',
            'name'  => 'string|nullable',
            'password' => 'string'
        ];
        $messages = [
            'email.email' => 'Vous avez rentré un mauvais e-mail.',
            'email.required' => 'Vous devez rentré un e-mail.',
            'name.string' => 'Il y a une erreur avec votre nom.',
        ];

        $validator = Validator::make($values, $rules, $messages);

        // Je déclare une var $json vide
        $json = '';
        if($validator->fails()){
            // au lieu de retourner avec une session
            // on retourne avec un tableau

           $json = $validator->errors()->all();
           $json[] .= 'Errors';
           return json_encode($json);
        }

        $user = new User();
        $user->name = $values['name'];
        $user->email = $values['email'];
        $user->password = Hash::make($values['name']);

        $user->save();

        $json = [
            // Je récupère l'id qui vient d'être entré
            'id' => $user->id,
            'name' => $values['name'],
            'email' => $values['email'],
        ];
        return $json;
    }
}
