<?php

namespace App\Providers;

use App\Domain\Auth\JWTService;
use App\Infrastructure\Auth\FirebaseJWTService;
use App\Repositories\Interfaces\LivroRepositoryInterface;
use App\Repositories\Interfaces\NpcRepositoryInterface;
use App\Repositories\Interfaces\PersonagemRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\LivroRepository;
use App\Repositories\Interfaces\CampanhaRepositoryInterface;
use App\Repositories\CampanhaRepository;
use App\Repositories\Interfaces\AtributoRepositoryInterface;
use App\Repositories\AtributoRepository;
use App\Repositories\Interfaces\ClasseRepositoryInterface;
use App\Repositories\ClasseRepository;
use App\Repositories\Interfaces\HabilidadeRepositoryInterface;
use App\Repositories\HabilidadeRepository;
use App\Repositories\Interfaces\OrigemRepositoryInterface;
use App\Repositories\OrigemRepository;
use App\Repositories\NpcRepository;
use App\Repositories\PersonagemRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(JWTService::class, function ($app) {
            return new FirebaseJWTService(
                config('auth.jwt.secret', env('JWT_SECRET')),  // chave
                'HS256',                                       // algoritmo
                3600                                          // ttl em segundos (1 hora)
            );
        });
        // $this->app->bind(LivroRepositoryInterface::class, LivroRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CampanhaRepositoryInterface::class, CampanhaRepository::class);
        $this->app->bind(AtributoRepositoryInterface::class, AtributoRepository::class);
        $this->app->bind(ClasseRepositoryInterface::class, ClasseRepository::class);
        $this->app->bind(HabilidadeRepositoryInterface::class, HabilidadeRepository::class);
        $this->app->bind(PersonagemRepositoryInterface::class, PersonagemRepository::class);
        $this->app->bind(NpcRepositoryInterface::class, NpcRepository::class);
        $this->app->bind(OrigemRepositoryInterface::class, OrigemRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
