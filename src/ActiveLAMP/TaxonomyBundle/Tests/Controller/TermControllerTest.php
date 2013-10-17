<?php

namespace ActiveLAMP\TaxonomyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ActiveLAMP\TaxonomyBundle\Entity\Vocabulary;
use Doctrine\Common\Annotations\AnnotationRegistry;

class TermControllerTest extends WebTestCase
{
    protected $vocabulary;
    protected $em;

    /**
     * Create a vocabulary to add terms to.
     */
    protected function setUp()
    {
        $vendor_dir = isset($_ENV['TRAVIS']) ? realpath(__DIR__ . '/../../../../../../../symfony/vendor') : realpath(__DIR__ . '/' . $_ENV['VENDOR_DIR']);
        AnnotationRegistry::registerFile($vendor_dir . "/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
        AnnotationRegistry::registerFile($vendor_dir . "/sensio/framework-extra-bundle/Sensio/Bundle/FrameworkExtraBundle/Configuration/Route.php");
        AnnotationRegistry::registerFile($vendor_dir . "/sensio/framework-extra-bundle/Sensio/Bundle/FrameworkExtraBundle/Configuration/Method.php");
        AnnotationRegistry::registerFile($vendor_dir . "/sensio/framework-extra-bundle/Sensio/Bundle/FrameworkExtraBundle/Configuration/Template.php");
        // Boot the kernel to get access to the container.
        $kernel = static::createKernel();
        $kernel->boot();

        // Setup a new vocabulary for testing.
        $entity = new Vocabulary();
        $entity->setLabelName('Testing Vocabulary');
        $entity->setName('testing_vocabulary');
        $entity->setDescription('Description for Vocabulary.' . $entity->getId());

        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->persist($entity);
        $this->em->flush();

        $this->vocabulary = $entity;
    }

    /**
     * Remove the vocabulary that was setUp() for this test.
     */
    protected function tearDown()
    {
       $this->em->remove($this->vocabulary);
       $this->em->flush();
    }

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/structure/taxonomy/' . $this->vocabulary->getId() . '/term/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
        $crawler = $client->click($crawler->selectLink('Add Term')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'activelamp_taxonomybundle_term[name]'  => 'Test Term',
            'activelamp_taxonomybundle_term[weight]' => 0
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        // Check to see that we are redirected back to the add term page
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Add Term")')->count(), 'Add Term text missing in title.');
        $this->assertGreaterThan(0, $crawler->filter('input[value="Create"]')->count(), 'Create button does not exist.');

        $crawler = $client->click($crawler->selectLink('Cancel')->link());

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test Term")')->count(), 'Missing element td:contains("Test Term")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'activelamp_taxonomybundle_term[name]'  => 'Foo',
            'activelamp_taxonomybundle_term[weight]' => 10
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }

}
