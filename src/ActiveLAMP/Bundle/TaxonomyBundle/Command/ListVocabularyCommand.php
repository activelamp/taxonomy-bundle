<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/29/14
 * Time: 3:28 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Command;

use ActiveLAMP\Bundle\TaxonomyBundle\Entity\Vocabulary;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class ListVocabularyCommand
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Command
 * @author Bez Hermoso <bez@activelamp.com>
 */
class ListVocabularyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('taxonomy:vocabulary:list')
            ->setAliases(array('tax:ls'))
            ->setDescription('List terms of a given vocabulary.')
            ->addArgument('vocabulary', InputArgument::OPTIONAL, 'List terms of which vocabulary?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get('al_taxonomy.taxonomy_service');

        $vocabularyName = $input->getArgument('vocabulary');

        $vocabularies = array();

        if ($vocabularyName) {
            $vocabularyNames = array_map('trim', explode(',', $vocabularyName));
            foreach ($vocabularyNames as $vocabularyName) {
                $vocabularies[] = $service->findVocabularyByName($vocabularyName);
            }
        } else {
            $vocabularies = $this->getContainer()->get('doctrine.orm.entity_manager')
                                 ->getRepository('ALTaxonomyBundle:Vocabulary')->findAll();
        }

        $output->writeln('');

        foreach ($vocabularies as $vocabulary) {
            if (!$vocabulary) {
                $output->writeln(sprintf('<fg=white;bg=red>Vocabulary of name "%s" not found.</fg=white;bg=red>', $vocabularyName));
                continue;
            }
            $terms = $service->findTermsInVocabulary($vocabulary);
            $this->dumpVocabularyAndTerms($vocabulary, $terms, $output);
        }
    }

    protected function dumpVocabularyAndTerms(Vocabulary $vocabulary, $terms, OutputInterface $output)
    {

        $lines = array();

        $lines[] = sprintf(
            '<comment>Terms in vocabulary <info>"%s"</info>:</comment>',
            $vocabulary->getName());

        if (count($terms) == 0) {
            $lines[] = 'No terms found.';
        } else {
            $lines[] = sprintf('%d terms found:', count($terms));
        }

        foreach ($terms as $term) {
            $lines[] = sprintf('    <info>%s:</info>', $term->getName());
            $lines[] = sprintf('        label: <comment>"%s"</comment>', $term->getLabelName());
            $lines[] = sprintf('        weight: <comment>%d</comment>', $term->getWeight());
        }
        $lines[] = '';

        $output->writeln($lines);
    }
} 