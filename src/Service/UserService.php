<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserService
{

    public function updateAvatarFile(FormInterface $form, User|null $user, $directory): User
    {

        $avatarFile = $form->get('avatar')->getData();
        if ($avatarFile) {

            $fileName = 'avatarUser' . $user->getId() . '.webp';

            try {
                $avatarFile->move(
                    $directory,
                    $fileName
                );
            } catch (FileException $e) {

            }
            $user->setAvatar($fileName);
        }
        return $user;
    }

}
