<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Excursion;
use App\Entity\Location;
use App\Entity\Town;
use App\Entity\User;
use App\Enum\ExcursionStatus;
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
        $user1->setUsername('Vince');
        $user1->setName('Bond');
        $user1->setSurname('Vince');
        $user1->setPhone('0725132967');
        $user1->setEmail('vince@campus-eni.fr');
        $user1->setPassword($this->hasher->hashPassword($user1, '111111'));
        $user1->setAdmin(false);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setCampus($campus1);
        $user2->setUsername('Logan');
        $user2->setName('Pajot');
        $user2->setSurname('Dominique');
        $user2->setPhone('0633084112');
        $user2->setEmail('dominique.pajot2024@campus-eni.fr');
        $user2->setPassword($this->hasher->hashPassword($user2, '222222'));
        $user2->setAdmin(true);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setCampus($campus1);
        $user3->setUsername('Andrick');
        $user3->setName('Hanks');
        $user3->setSurname('Tom');
        $user3->setPhone('0633084112');
        $user3->setEmail('dominique.pajot2024@campus-eni.fr');
        $user3->setPassword($this->hasher->hashPassword($user3, '333333'));
        $user3->setAdmin(true);
        $manager->persist($user3);

        // Sortie
        $excursion1 = new Excursion();
        $excursion1->setName('Sortie Buffalo Grill');
        $excursion1->setOrganizer($user1);
        $excursion1->setCampus($campus1);
        $excursion1->setLocation($lieu1);
        $excursion1->setDate(new \DateTime(202409151900));
        $excursion1->setDeadline(new \DateTime(20240914));
        $excursion1->setNbSeats(8);
        $excursion1->setDuration(120);
        $excursion1->setDescription('Une bonne entrecote');
        $excursion1->setStatus(ExcursionStatus::Created);
        $manager->persist($excursion1);

        $excursion2 = new Excursion();
        $excursion2->setName('Sortie piscine');
        $excursion2->setOrganizer($user1);
        $excursion2->setCampus($campus1);
        $excursion2->setLocation($lieu2);
        $excursion2->setDate(new \DateTime(202409151900));
        $excursion2->setDeadline(new \DateTime(20240914));
        $excursion2->setNbSeats(8);
        $excursion2->setDuration(120);
        $excursion2->setDescription('Un peu de natation');
        $excursion2->setStatus(ExcursionStatus::Open);
        $excursion2->addParticipant($user2);
        $excursion2->addParticipant($user3);
        $manager->persist($excursion2);

        $excursion3 = new Excursion();
        $excursion3->setName('Patinoire');
        $excursion3->setOrganizer($user1);
        $excursion3->setCampus($campus1);
        $excursion3->setLocation($lieu1);
        $excursion3->setDate(new \DateTime(202409151900));
        $excursion3->setDeadline(new \DateTime(20240914));
        $excursion3->setNbSeats(8);
        $excursion3->setDuration(120);
        $excursion3->setDescription('Un peu de glisse');
        $excursion3->setStatus(ExcursionStatus::Ongoing);
        $excursion3->addParticipant($user2);
        $excursion3->addParticipant($user3);
        $manager->persist($excursion3);

        $excursion4 = new Excursion();
        $excursion4->setName('Karaoké');
        $excursion4->setOrganizer($user1);
        $excursion4->setCampus($campus2);
        $excursion4->setLocation($lieu2);
        $excursion4->setDate(new \DateTime(202409151900));
        $excursion4->setDeadline(new \DateTime(20240914));
        $excursion4->setNbSeats(8);
        $excursion4->setDuration(120);
        $excursion4->setDescription('Un peu de chant');
        $excursion4->setStatus(ExcursionStatus::Closed);
        $excursion4->addParticipant($user2);
        $excursion4->addParticipant($user3);
        $manager->persist($excursion4);

        $excursion5 = new Excursion();
        $excursion5->setName('Plage');
        $excursion5->setOrganizer($user3);
        $excursion5->setCampus($campus1);
        $excursion5->setLocation($lieu2);
        $excursion5->setDate(new \DateTime(202409151900));
        $excursion5->setDeadline(new \DateTime(20240914));
        $excursion5->setNbSeats(8);
        $excursion5->setDuration(120);
        $excursion5->setDescription('Un peu de bronzage');
        $excursion5->setStatus(ExcursionStatus::Finished);
        $excursion5->addParticipant($user2);
        $excursion5->addParticipant($user1);
        $manager->persist($excursion5);


        $manager->flush();
    }
}
