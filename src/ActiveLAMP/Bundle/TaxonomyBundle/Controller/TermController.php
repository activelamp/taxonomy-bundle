<?php

namespace ActiveLAMP\Bundle\TaxonomyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use ActiveLAMP\Bundle\TaxonomyBundle\Form\TermType;

/**
 * Term controller.
 *
 * @Route("/{vocabulary_id}/term")
 */
class TermController extends Controller
{

    /**
     * Lists all Term entities.
     *
     * @Route("/", name="al_taxonomy_list_terms")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($vocabulary_id)
    {
        $vocabulary = $this->getVocabulary($vocabulary_id);
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ALTaxonomyBundle:Term')->findBy(array('vocabulary' => $vocabulary));

        return array(
            'vocabulary' => $vocabulary,
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Term entity.
     *
     * @Route("/", name="al_taxonomy_create_term")
     * @Method("POST")
     * @Template("ALTaxonomyBundle:Term:new.html.twig")
     */
    public function createAction(Request $request, $vocabulary_id)
    {
        $vocabulary = $this->getVocabulary($vocabulary_id);
        $entity = new Term();
        $form = $this->createCreateForm($entity, $vocabulary_id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setVocabulary($vocabulary);

            // @var \Doctrine\Common\Persistence\ObjectManager
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('al_taxonomy_new_term', array('vocabulary_id' => $vocabulary_id)));
        }

        return array(
            'vocabulary' => $vocabulary,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Term entity.
     *
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term $entity The entity
     *
     * @param $vocabulary_id
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Term $entity, $vocabulary_id)
    {
        $form = $this->createForm(new TermType(), $entity, array(
            'action' => $this->generateUrl('al_taxonomy_create_term', array('vocabulary_id' => $vocabulary_id)),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Term entity.
     *
     * @Route("/new", name="al_taxonomy_new_term")
     * @Method("GET")
     * @Template()
     */
    public function newAction($vocabulary_id)
    {
        $entity = new Term();
        $form   = $this->createCreateForm($entity, $vocabulary_id);

        return array(
            'vocabulary' => $this->getVocabulary($vocabulary_id),
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Term entity.
     *
     * @Route("/{id}", name="al_taxonomy_show_term")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id, $vocabulary_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ALTaxonomyBundle:Term')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Term entity.');
        }

        $deleteForm = $this->createDeleteForm($id, $vocabulary_id);

        return array(
            'vocabulary_id' => $vocabulary_id,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Term entity.
     *
     * @Route("/{id}/edit", name="al_taxonomy_edit_term")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id, $vocabulary_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ALTaxonomyBundle:Term')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Term entity.');
        }

        $editForm = $this->createEditForm($entity, $vocabulary_id);
        $deleteForm = $this->createDeleteForm($id, $vocabulary_id);

        return array(
            'vocabulary_id' => $vocabulary_id,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Term entity.
    *
    * @param Term $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Term $entity, $vocabulary_id)
    {
        $form = $this->createForm(new TermType(), $entity, array(
            'action' => $this->generateUrl('al_taxonomy_update_term', array('id' => $entity->getId(), 'vocabulary_id' => $vocabulary_id)),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Term entity.
     *
     * @Route("/{id}", name="al_taxonomy_update_term")
     * @Method("PUT")
     * @Template("ALTaxonomyBundle:Term:edit.html.twig")
     */
    public function updateAction(Request $request, $id, $vocabulary_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ALTaxonomyBundle:Term')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Term entity.');
        }

        $deleteForm = $this->createDeleteForm($id, $vocabulary_id);
        $editForm = $this->createEditForm($entity, $vocabulary_id);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('al_taxonomy_list_terms', array('vocabulary_id' => $vocabulary_id)));
        }

        return array(
            'vocabulary_id' => $vocabulary_id,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Term entity.
     *
     * @Route("/{id}", name="al_taxonomy_delete_term")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id, $vocabulary_id)
    {
        $form = $this->createDeleteForm($id, $vocabulary_id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ALTaxonomyBundle:Term')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Term entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('al_taxonomy_list_terms', array('vocabulary_id' => $vocabulary_id)));
    }

    /**
     * Creates a form to delete a Term entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, $vocabulary_id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('al_taxonomy_delete_term', array('id' => $id, 'vocabulary_id' => $vocabulary_id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete', 'attr' => array('class' => 'btn btn-default')))
            ->getForm()
        ;
    }

    /**
     * Gets a vocabulary giving a vocabulary id.
     *
     * @param $vocabulary_id
     * @return \ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary
     */
    private function getVocabulary($vocabulary_id) {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('ALTaxonomyBundle:Vocabulary')->find($vocabulary_id);
    }
}
