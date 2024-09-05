<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Excursion;
use App\Entity\Location;
use App\Entity\Town;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // Campus
        $campus1 = new Campus();
        $campus1->setName('Rennes');
        $manager->persist($campus1);

        $campus2 = new Campus();
        $campus2->setName('Saint-Herblain');
        $manager->persist($campus2);

        // Ville
        $ville1 = new Town();
        $ville1->setName("Triffouillis-lès-Oies");
        $ville1->setZipcode("95959");
        $manager->persist($ville1);

        $ville2 = new Town();
        $ville2->setName("Perpète-les-Choux");
        $ville2->setZipcode("12345");
        $manager->persist($ville2);

        // Lieu
        $lieu1 = new Location();
        $lieu1->setName("Machin Truc");
        $lieu1->setAddress("420 rue des Champignons");
        $lieu1->setTown($ville1);
        $manager->persist($lieu1);

        $lieu2 = new Location();
        $lieu2->setName("Carabistouilles");
        $lieu2->setAddress("69 lieu-dit les Cornus");
        $lieu2->setTown($ville2);
        $manager->persist($lieu2);

        // Utilisateur
        $user1 = new User();
        $user1->setCampus($campus1);
        $user1->setUsername('clovis007');
        $user1->setName('Artman');
        $user1->setSurname('Clovis');
        $user1->setPhone('0725132967');
        $user1->setEmail('clovis.artman2024@campus-eni.fr');
        $user1->setPassword($this->hasher->hashPassword($user1, 'admin'));
        $user1->setAdmin(false);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setCampus($campus1);
        $user2->setUsername('dominique223');
        $user2->setName('Pajot');
        $user2->setSurname('Dominique');
        $user2->setPhone('0633084112');
        $user2->setEmail('dominique.pajot2024@campus-eni.fr');
        $user2->setPassword($this->hasher->hashPassword($user2, 'admin'));
        $user2->setAdmin(true);
        $manager->persist($user2);

        // Sortie
//        $excursion1 = new Excursion();
//        $excursion1->setName('Sortie Buffalo Grill');
//        $excursion1->addParticipant($user1);
//        $manager->persist($excursion1);

//        $excursion2 = new Excursion();
//        $excursion2->setName('Sortie Patinoire LE BLIZZ');
//        $excursion2->addParticipant($user1);
//        $excursion2->addParticipant($user2);
//        $manager->persist($excursion2);

        $manager->flush();
    }
}
