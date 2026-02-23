<?php

namespace App\Tests\Controller;

use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProfileControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $profileRepository;
    private string $path = '/profil/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->profileRepository = $this->manager->getRepository(Profile::class);

        foreach ($this->profileRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Profile index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'profile[bio]' => 'Testing',
            'profile[specialite]' => 'Testing',
            'profile[localisation]' => 'Testing',
            'profile[user]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->profileRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Profile();
        $fixture->setBio('My Title');
        $fixture->setSpecialite('My Title');
        $fixture->setLocalisation('My Title');
        $fixture->setUser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Profile');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Profile();
        $fixture->setBio('Value');
        $fixture->setSpecialite('Value');
        $fixture->setLocalisation('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'profile[bio]' => 'Something New',
            'profile[specialite]' => 'Something New',
            'profile[localisation]' => 'Something New',
            'profile[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/profil/');

        $fixture = $this->profileRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getBio());
        self::assertSame('Something New', $fixture[0]->getSpecialite());
        self::assertSame('Something New', $fixture[0]->getLocalisation());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Profile();
        $fixture->setBio('Value');
        $fixture->setSpecialite('Value');
        $fixture->setLocalisation('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/profil/');
        self::assertSame(0, $this->profileRepository->count([]));
    }
}
