<?php
namespace Finetune\Finetune\Repositories\User;

/**
 * Interface UserInterface
 * @package Repositories\User
 */
interface UserInterface
{
    public function all($notSuper = false, $site = null);

    public function find($id, $notSuper = false, $site = null);

    public function create($user, $notSuper = false, $site = null);

    public function update($id, $user, $notSuper = false, $site = null);

    public function delete($id, $notSuper = false, $site = null);

    public function updatePassword($id, $input);
}