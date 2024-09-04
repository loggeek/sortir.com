<?php

namespace App\DataFixtures;

use App\Entity\Excursion;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {

        $user1 = new User();
        $excursion1 = new Excursion();
        $user2 = new User();
        $excursion2 = new Excursion();

        $user1->setUsername('clovis007');
        $user1->setName('Artman');
        $user1->setSurname('Clovis');
        $user1->setPhone('0725132967');
        $user1->setEmail('clovis.artman2024@campus-eni.fr');
        $user1->setPassword($this->hasher->hashPassword($user1, 'admin'));
        $user1->setAdmin(false);

//        $excursion1->setName('Sortie Buffalo Grill');
//        $excursion1->addParticipant($user1);


        $user2->setUsername('dominique223');
        $user2->setName('Dominique');
        $user2->setSurname('PAJOT');
        $user2->setPhone('0633084112');
        $user2->setEmail('dominique.pajot2024@campus-eni.fr');
        $user2->setPassword($this->hasher->hashPassword($user2, 'admin'));
        $user2->setAdmin(true);

//        $excursion2->setName('Sortie Patinoire LE BLIZZ');
//        $excursion2->addParticipant($user1);
//        $excursion2->addParticipant($user2);

        $manager->persist($user1);
//        $manager->persist($excursion1);
        $manager->persist($user2);
//        $manager->persist($excursion2);

        $manager->flush();
    }
}
