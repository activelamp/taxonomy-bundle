<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/30/14
 * Time: 10:18 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\Loader;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Term;
use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;
use Symfony\Component\Yaml\Yaml;


/**
 * Class TaxonomyLoader
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\Loader
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TaxonomyLoader 
{
    protected $files;

    /**
     * @var array|Vocabulary[]
     */
    protected $vocabularies = array();

    /**
     * @var array|Term[]
     */
    protected $terms = array();

    protected $loaded = false;

    public function __construct(array $files)
    {
        $this->files = $files;
    }

    protected function load()
    {
        $vocabs = array();
        foreach ($this->files as $file) {
            $vocabs = array_replace_recursive($vocabs, Yaml::parse(file_get_contents($file)));
        }

        foreach ($vocabs as $name => $vocab) {

            $vocabulary = $this->registerVocabulary($name, $vocab);

            foreach ($vocab['terms'] as $tname => $tdata) {
                $term = $this->registerTerm($tname, $tdata);
                $term->setVocabulary($vocabulary);
            }

        }

        $this->loaded = true;
    }

    protected function registerVocabulary($name, $data)
    {
        if (isset($this->vocabularies[$name])) {
            $vocab = $this->vocabularies[$name];
        } else {
            $vocab = new Vocabulary();
        }
        $this->vocabularies[$name] = $vocab;

        $vocab->setName($name);

        if (is_string($data)) {
            $vocab->setLabelName($data);
            return $vocab;
        }

        $proto = array(
            'label' => '(Update label)',
            'description' => '(Update description)',
        );

        $data = array_merge($proto, $data);
        $vocab->setLabelName($data['label']);
        $vocab->setDescription($data['description']);


        return $vocab;

    }

    protected function registerTerm($name, $data)
    {
        if (isset($this->terms[$name])) {
            $term = $this->terms[$name];
        } else {
            $term = new Term();
        }

        $this->terms[$name] = $term;

        $term->setName($name);

        if (is_string($data)) {
            $term->setLabelName($data);
            return $term;
        }

        $proto = array(
            'label' => '(Update label)',
            'weight' => 0,
        );

        $data = array_merge($proto, $data);

        $term->setLabelName($data['label']);
        $term->setWeight($data['weight']);


        return $term;
    }

    /**
     * @return Vocabulary[]
     */
    public function getVocabularies()
    {
        if ($this->loaded === false) {
            $this->load();
        }

        return $this->vocabularies;
    }

    /**
     * @return Term[]
     */
    public function getTerms()
    {
        if ($this->loaded === false) {
            $this->load();
        }

        return $this->terms;
    }
}