<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/22/14
 * Time: 4:43 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Command;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class DumpMetadataCommand
 *
 * @package ActiveLAMP\TaxonomyBundle\Command
 * @author Bez Hermoso <bez@activelamp.com>
 */

class DumpMetadataCommand extends ContainerAwareCommand
{

    public function configure()
    {
        $this
            ->setName('taxonomy:metadata:dump')
            ->setDescription('Dumps taxonomy metadata information.')
            ->addArgument('em', InputArgument::OPTIONAL, 'Entity manager name.', 'default');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getArgument('em'));

        /** @var $em EntityManager */
        $em = $this->getHelper('em')->getEntityManager();

        $output->writeln('Reading metadata...');
        $output->writeln('');

        /**
         * Run Doctrine2 metadata phase
         */
        $em->getMetadataFactory()->getAllMetadata();


        $metadata = $this->getContainer()->get('al_taxonomy.metadata');

        $metadatas = $metadata->getAllEntityMetadata();

        $lines = array();

        foreach ($metadatas as $entity) {
            $lines[] = $entity->getReflectionClass()->getName() . ':';
            foreach ($entity->getVocabularies() as $vocabulary) {
                $lines[] = sprintf('    $%s => \'%s\'', $vocabulary->getFieldName(), $vocabulary->getName());
            }
            $lines[] = '';
        }

        $output->writeln($lines);
    }
}