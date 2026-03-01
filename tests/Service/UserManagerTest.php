<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase
{
    private UserManager $userManager;

    protected function setUp(): void
    {
        $this->userManager = new UserManager();
    }

    public function testValiderUtilisateurSucces(): void
    {
        $user = new User();
        $user->setEmail('test@firmetna.com');
        $user->setPassword('SecureP@ssw0rd!');

        $this->assertTrue($this->userManager->validerUtilisateur($user));
    }

    public function testEmailObligatoire(): void
    {
        $user = new User();
        $user->setEmail(''); // Vide (Règle 4 violée)
        $user->setPassword('SecureP@ssw0rd!');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("L'email est un champ obligatoire et doit être rempli.");

        $this->userManager->validerUtilisateur($user);
    }

    public function testMotDePasseDoitContenir8Caracteres(): void
    {
        $user = new User();
        $user->setEmail('test@firmetna.com');
        $user->setPassword('1234567'); // Trop court, 7 caractères (Règle 9 violée)

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le mot de passe doit contenir au moins 8 caractères.");

        $this->userManager->validerUtilisateur($user);
    }
}
