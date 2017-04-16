<?php

namespace ActiveLAMP\Bundle\TaxonomyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\Annotations\AnnotationRegistry;

class TaxonomyControllerTest extends WebTestCase
{
    protected function setUp() {
        $vendor_dir = isset($_ENV['VENDOR_DIR']) ? realpath(__DIR__ . '/' . $_ENV['VENDOR_DIR']) : realpath(__DIR__ . '/../../../../../../../symfony/vendor');
        AnnotationRegistry::registerFile($vendor_dir . "/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
        AnnotationRegistry::registerFile($vendor_dir . "/sensio/framework-extra-bundle/Sensio/Bundle/FrameworkExtraBundle/Configuration/Route.php");
        AnnotationRegistry::registerFile($vendor_dir . "/sensio/framework-extra-bundle/Sensio/Bundle/FrameworkExtraBundle/Configuration/Method.php");
        AnnotationRegistry::registerFile($vendor_dir . "/sensio/framework-extra-bundle/Sensio/Bundle/FrameworkExtraBundle/Configuration/Template.php");
    }

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/structure/taxonomy/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
        $crawler = $client->click($crawler->selectLink('Create New Vocabulary')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'activelamp_taxonomybundle_vocabulary[labelName]'  => 'Rating',
            'activelamp_taxonomybundle_vocabulary[name]'  => 'rating',
            'activelamp_taxonomybundle_vocabulary[description]'  => 'Test adding a rating vocabulary',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Rating")')->count(), 'Missing element td:contains("Rating")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'activelamp_taxonomybundle_vocabulary[labelName]'  => 'Foo',
            'activelamp_taxonomybundle_vocabulary[name]'  => 'foo',
            'activelamp_taxonomybundle_vocabulary[description]'  => 'Updated rating to Foo',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Foo")')->count(), 'Missing element td:contains("Foo")');

        // Check the element contains an attribute with value equals "Foo"
        $crawler = $client->click($crawler->selectLink('Edit')->link());
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }

}
