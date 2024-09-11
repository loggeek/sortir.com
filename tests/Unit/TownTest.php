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
        parent::setUp();  // Initialise correctement le kernel
        self::bootKernel(); // Lance le kernel de Symfony

        // Utilisation de static::getContainer() pour récupérer le service ValidatorInterface
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function getEntity(): Town{
        return (new Town())
            ->setName('Ville 1')
            ->setZipcode('35520');
    }

    public function testTownIsValid(): void
    {
        $town = $this->getEntity();
        $town->setName('Ville 1');

        $errors = $this->validator->validate($town);

        $this->assertCount(0, $errors);
    }

    public function testTownIInvalidName(): void
    {
        $town = $this->getEntity();
        $town->setName('');

        $errors = $this->validator->validate($town);

        $this->assertCount(2, $errors);
    }

    public function testTownIInvalidNameLength(): void
    {
        $town = $this->getEntity();
        $town->setName('djdjdjdjdjdjdjdjdjdjdjdjdjdjdjdjdjdjdjdjd');

        $errors = $this->validator->validate($town);

        $this->assertCount(1, $errors);
    }

    public function testTownInvalidZipcode(): void
    {
        $town = $this->getEntity();
        $town->setZipcode('');

        $errors = $this->validator->validate($town);

        $this->assertCount(2, $errors);
    }

    public function testTownInvalidZipcodeLength(): void
    {
        $town = $this->getEntity();
        $town->setZipcode('355201');

        $errors = $this->validator->validate($town);

        $this->assertCount(1, $errors);
    }

    public function testTownInvalidZipcodeLength2(): void
    {
        $town = $this->getEntity();
        $town->setZipcode('3552');

        $errors = $this->validator->validate($town);

        $this->assertCount(1, $errors);
    }

}
