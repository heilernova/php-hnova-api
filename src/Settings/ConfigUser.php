<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */
namespace HNova\Api\Settings;

class ConfigUser
{
    public function __construct(private object $user){}

    /**
     * Establece el nombre de usuarios
     */
    public function setUsername(string $name):void
    {
        $this->user->username = $name;
    }

    /**
     * Establece la contraseña del usaruio
     */
    public function setPassword(string $password):void
    {
        $this->user->password = password_hash($password, PASSWORD_DEFAULT, ['cost'=>4]);
    }

    /**
     * Verifica si la contraseña concuerda con la del usuario.
     */
    public function passwordVerify(string $password):bool
    {
        return password_verify($password, $this->user->password);
    }

    /**
     * Retorna el correo electronico del usuario
     */
    public function getEmail():?string
    {
        return $this->user->email;
    }

    /**
     * Estable el correo eletrónico del usuario.
     */
    public function setEmail(string $email):void
    {
        $this->user->emial = $email;
    }
}