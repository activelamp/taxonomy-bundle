TaxonomyBundle [![Build Status](https://travis-ci.org/activelamp/taxonomy-bundle.png?branch=master)](https://travis-ci.org/activelamp/taxonomy-bundle)
==============

#Usage

Steps:

* Add `activelamp/taxonomy-bundle` as a dependency to your project.
* Register the bundle:

```php
   $bundles = array(
       ...
       new ActiveLAMP\TaxonomyBundle\ALTaxonomyBundle(),
   );
```

* Update your database schema (i.e., run `php app/console doctrine:schema:update --force`)
* Add the following lines to your `app/config/routing.yml` file to expose the back-end administration for taxonomies and terms:

```yml
al_taxonomy:
    resource: "@ALTaxonomyBundle/Resources/routing.yml"
    prefix : /manage-taxonomies #This can be whatever you want.
```

#Entity set-up

You have to set-up your entities before you can start using them with taxonomies. This is done by adding a few annotations to the entity class and the properties you want to use taxonomies with. The annotations you are going to be using resides in the `\ActiveLAMP\TaxonomyBundle\Annotations` namespace.

1. Declare that your entity is a termed entity by tagging it with the `@Entity` annotation.
2. Mark the properties which are going to be using vocabulary terms with the `@Vocabulary` annotation.
3. Stub the vocabulary fields when necessary (more on this in the example below.):

```php
<?php

namespace Foo\BarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ActiveLAMP\TaxonomyBundle\Annotations as Taxn;

/**
 * The identifier defaults to "id". However, since this entity uses the userId field
 * as its primary key, then we should set identifier to "userId", like below:
 * 
 * @Taxn\Entity(identifier="userId")
 */
class User
{

     /**
      * @ORM\Id
      */
     protected $userId;
    
    /**
     * This will contain an array of ActiveLAMP\TaxonomyBundle\Entity\Term objects 
     * from the "languages" vocabulary that are linked to the entity.
     *
     * @Taxn\Vocabulary(name="languages")
     */
    protected $languages;
   
    /**
     * This is a field that will contain a singular taxonomy term value instead
     * instead of an array of them. 
     * See the singular=true setting in the annotation below.
     *
     * This will contain a singular ActiveLAMP\TaxonomyBundle\Entity\Term object
     * from the "organizations" vocabulary that is linked to the entity.
     * 
     * @Taxn\Vocabulary(name="organizations", singular=true)
     */
    protected $organization;
   
    public function __construct()
    {
        /*
         * You might want to stub non-singular vocabulary fields with 
         * an instance of ArrayCollection so that you can add, remove terms 
         * on a non-managed/detached instance of this entity class.
         * 
         * i.e. 
         * $user = new User();
         * $user->getLanguages()->add($term);
         * 
         */
        $this->languages = new \Doctrine\Common\Collections\ArrayCollection();
    }
   
    public function getLanguages()
    {
        return $this->languages;
    }
   
    public function setOrganization(\ActiveLAMP\TaxonomyBundle\Entity\Term $organization)
    {
       /*
        * The taxonomy system would inject an instance of
        * \ActiveLAMP\TaxonomyBundle\Entity\SingularVocabularyField
        * into your entities during certain points in the entity's lifecycle.
        *
        * You might also want to check for this field as well and deal with terms
        * appropriately.
        *
        * Although this is NOT necessary at all, it saves the taxonomy system
        * some trips to the database in certain cases.
        *
        */
       if ($this->organization instanceof
       \ActiveLAMP\TaxonomyBundle\Entity\SingularVocabularyField) {
           $this->organization->setTerm($organization);
       } else {
           $this->organization = $organization;
       }
   }
   
   public function getOrganization()
   {
        /*
         * Nothing extra here, as SingularVocabularyField will just
         * act like a Term object. So as far as you are concerned, treat
         * this as a Term object.
         */
        return $this->organization;
   }
}
```

#The taxonomy service

The taxonomy service can be retrieved from the service container at `al_taxonomy.taxonomy_service`.

#Common operations

###Retrieving vocabularies

```php
<?php

//Via the service:
$languages = $service->findVocabularyByName("languages")

//Via the vocabulary field of a managed entity:

$user = $em->find('Foo\BarBundle\Entity\User', 1);
$languages = $user->getLanguages()->getVocabulary();

//From detached entities:

$user = new User();
$service->loadVocabularyFields($user);
$languages = $user->getLanguages()->getVocabulary();
```

###Retrieving terms

```php
<?php

//From a vocabulary object:

$languages = $service->findVocabularyByName("languages");

$french = $language->getTermByName('french');
$filipino = $language->getTermByName('filipino');

/* Will throw a \DomainException for non-existing terms. */
$klingon = $language->getTermByName('klingon'); 
```
###Looping through an entity's taxonomy terms

```php

//With a managed entity:

$user = $em->find('Foo\BarBundle\Entity\User', 1);

foreach ($user->getLanguages() as $languageTerm) {
     echo $languageTerm->getLabelName();
}

//With a detached entity:

$user = new User();

//This will yield nothing.
foreach ($user->getLanguages() as $languageTerm) {
    exit("Something you won't see.");
}

/*
 * You would have to call TaxonomyService#loadVocabularyFields before 
 * you can loop through attached terms.
 */
$service->loadVocabularyFields($user);

foreach ($user->getLanguages() as $languageTerm) {
     echo $languageTerm->getLabelName();
}

```

###Persisting taxonomies

```php

//Managed entities:
$user = $em->find('Foo\BarBundle\Entity\User', 1);
$languages = $user->getLanguages()->getVocabulary();

$user->getLanguages()->add($languages->getTermByName('french'));
$user->getLanguages()->removeElement($languages->getTermByName('english'));

$service->saveTaxonomies($user);


//How about detached entities?

$user = new User();

$user->setName("Albert Einstein");
$user->getLanguages()
    ->replace(array(
        $languages->getTermByName('english'), 
        $languages->getTermByName('german')
    ));
    
$em->persist($user);
$em->flush();
$service->saveTaxonomies($user); //No problem!

```
