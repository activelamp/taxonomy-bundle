<?php
/**
 * @file 
 * 
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Tests\Form\Type;

use ActiveLAMP\Bundle\TaxonomyBundle\Form\TermType;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use Symfony\Component\Form\Test\TypeTestCase;

class TermTypeTest extends TypeTestCase {

    /**
     * @dataProvider getValidTestData
     */
    public function testSubmitValidData($data)
    {
        list($form, $term) = $this->getForm($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($term, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($data) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @dataProvider getInvalidTestData
     */
    public function testSubmitInvalidData($data) {
        list($form, $term) = $this->getForm($data);
        $this->assertTrue($form->isSynchronized());
        $this->assertNotEquals($term, $form->getData());
    }

    /**
     * Setup the form and data.
     *
     * @param $data
     * @return array
     */
    private function getForm($data) {
        $type = new TermType();
        $form = $this->factory->create($type);

        $term = new Term();
        if (!empty($data)) {
            $term->setName($data['name']);
            $term->setWeight($data['weight']);
        }

        $form->submit($data);
        return array($form, $term);
    }

    public function getValidTestData()
    {
        return array(
            array(
                'data' => array(
                    'name' => 'Testing',
                    'weight' => '0',
                ),
            ),
            array(
                'data' => array(),
            ),
            array(
                'data' => array(
                    'name' => null,
                    'weight' => null,
                ),
            ),
        );
    }

    public function getInvalidTestData()
    {
        return array(
            array(
                'data' => array(
                    'name' => 'Testing',
                    'weight' => 'test',
                ),
            ),
        );
    }

}