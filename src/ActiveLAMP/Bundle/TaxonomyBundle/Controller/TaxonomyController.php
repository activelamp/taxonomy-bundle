<?php

namespace ActiveLAMP\Bundle\TaxonomyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;
use ActiveLAMP\Bundle\TaxonomyBundle\Form\VocabularyType;

/**
 * OptionsList controller.
 */
class TaxonomyController extends Controller
{

    /**
     * Lists all OptionsList entities.
     *
     * @Route("/", name="al_taxonomy_list_vocabularies")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ALTaxonomyBundle:Vocabulary')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new OptionsList entity.
     *
     * @Route("/", name="al_taxonomy_create_vocabulary")
     * @Method("POST")
     * @Template("ALTaxonomyBundle:Vocabulary:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Vocabulary();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('al_taxonomy_list_vocabularies'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a OptionsList entity.
    *
    * @param \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Vocabulary $entity)
    {
        $form = $this->createForm(new VocabularyType(), $entity, array(
            'action' => $this->generateUrl('al_taxonomy_create_vocabulary'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new OptionsList entity.
     *
     * @Route("/new", name="al_taxonomy_new_vocabulary")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Vocabulary();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a OptionsList entity.
     *
     * @Route("/{id}", name="al_taxonomy_show_vocabulary")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ALTaxonomyBundle:Vocabulary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vocabulary entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'terms'       => $this->getTerms($id),
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing OptionsList entity.
     *
     * @Route("/{id}/edit", name="al_taxonomy_edit_vocabulary")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ALTaxonomyBundle:Vocabulary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vocabulary entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a OptionsList entity.
    *
    * @param Vocabulary $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Vocabulary $entity)
    {
        $form = $this->createForm(new VocabularyType(), $entity, array(
            'action' => $this->generateUrl('al_taxonomy_update_vocabulary', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing OptionsList entity.
     *
     * @Route("/{id}", name="al_taxonomy_update_vocabulary")
     * @Method("PUT")
     * @Template("ALTaxonomyBundle:Vocabulary:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ALTaxonomyBundle:Vocabulary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vocabulary entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('al_taxonomy_list_vocabularies'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a OptionsList entity.
     *
     * @Route("/{id}", name="al_taxonomy_delete_vocabulary")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ALTaxonomyBundle:Vocabulary')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Vocabulary entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('al_taxonomy_delete_vocabulary'));
    }

    /**
     * Creates a form to delete a OptionsList entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('al_taxonomy_delete_vocabulary', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete', 'attr' => array('class' => 'btn btn-default')))
            ->getForm()
        ;
    }

    private function getTerms($vocabulary_id)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('ALTaxonomyBundle:Term')->findBy(array('vocabulary' => $this->getVocabulary($vocabulary_id)));
    }

    /**
     * Gets a vocabulary giving a vocabulary id.
     *
     * @param $vocabulary_id
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary
     */
    private function getVocabulary($vocabulary_id)
    {
        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('ALTaxonomyBundle:Vocabulary')->find($vocabulary_id);
    }
}
