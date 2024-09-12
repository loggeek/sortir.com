<?php

namespace App\Tests\Unit;

use App\Entity\Town;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TownTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        // Initialise correctement le kernel
        parent::setUp();
        
        // Lance le kernel de Symfony
        self::bootKernel();

        // Utilisation de static::getContainer() pour récupérer le service ValidatorInterface
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function createTown(string $name, string $zipcode): Town{
        return (new Town())
            ->setName($name)
            ->setZipcode($zipcode);
    }

    public function testTownIsValid(): void
    {
        $town = $this->createTown('Ville 1', '35520');

        // Valide l'objet $town en vérifiant qu'il respecte toutes les contraintes de validation définies dans l'entité
        $errors = $this->validator->validate($town);

        // Vérifie qu'il n'y a aucune erreur de validation (c'est-à-dire que $town est valide)
        $this->assertCount(0, $errors);
    }

    public function testTownInvalidName(): void
    {
        $town = $this->createTown('', '35520');

        $errors = $this->validator->validate($town);
        $this->assertCount(2, $errors);
    }

    public function testTownInvalidNameLength(): void
    {
        $town = $this->createTown('djdjdjdjdjdjdjdjdjdjdjdjdjdjdjdjdjdjdjdjd', '35520');

        $errors = $this->validator->validate($town);
        $this->assertCount(1, $errors);
    }

    public function testTownInvalidZipcode(): void
    {
        $town = $this->createTown('Ville 1', '');

        $errors = $this->validator->validate($town);
        $this->assertCount(2, $errors);
    }

    public function testTownInvalidZipcodeLength(): void
    {
        $town = $this->createTown('Ville 1', '355200');

        $errors = $this->validator->validate($town);
        $this->assertCount(1, $errors);
    }

    public function testTownInvalidZipcodeLength2(): void
    {
        $town = $this->createTown('Ville 1', '3552');

        $errors = $this->validator->validate($town);
        $this->assertCount(1, $errors);
    }

}
