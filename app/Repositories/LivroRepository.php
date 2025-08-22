<?php
namespace App\Repositories;

use App\Models\Livro;
use App\Repositories\Interfaces\LivroRepositoryInterface;
use DB;

class LivroRepository implements LivroRepositoryInterface
{
    public function buscarLivrosDestaque(): array
    {
        // return Livro::where('destaque', true)->get()->toArray();
        $users = DB::table('users')->get()->toArray();
        return $users;
        // return [
        //     [
        //         'nome' => 'O ladrão de raios',
        //         'destaque' => true
        //     ]
        // ];
    }
}
