<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use function Illuminate\Events\queueable;
use Illuminate\Support\Facades\Validator;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Sincronizando dados do cliente com o Stripe
    protected static function booted() {

        static::updated(queueable(function ($customer) {
            if ($customer->hasStripeId()) {
                $customer->syncStripeCustomerDetails();
            }
        }));

    }

    // Cria ou recupera um cliente stripe a partir de um usuario
    public static function findCustomer($user_id, &$user = null) {

        $user = User::find($user_id);
        return $user ? $user->createOrGetStripeCustomer() : null;

    }

    // Validacao dos dados de um usuario
    public static function validator(array $data, int $updated = null) {

        $required = $updated ? '' : 'required';

        return Validator::make($data, [
            'name'     => [$required, 'string', 'min:5', 'max:255', 'regex:/\s/'],
            'email'    => [$required, 'string', 'max:255', 'email', $updated ? "unique:users,email,\"$updated\",id" : 'unique:users,email'],
            'password' => [$required, 'string', 'min:6', 'max:30', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/', 'confirmed'],
        ], [
            'name.min'           => 'Digite o seu nome completo.',
            'name.regex'         => 'Digite seu nome e sobrenomes.',
            'email.unique'       => 'Este e-mail já foi cadastrado em nosso sistema.',
            'password.regex'     => 'A senha deve ter pelo menos uma letra e um número.',
            'password.confirmed' => 'As senhas não estão iguais.',
        ], [
            'name'     => 'Nome',
            'email'    => 'E-mail',
            'password' => 'Senha'
        ]);

    }

}
