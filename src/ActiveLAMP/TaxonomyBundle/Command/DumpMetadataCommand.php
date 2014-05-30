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

        $metadataReader = $this->getContainer()->get('al_taxonomy.subscriber.read_metadata');

        $metadataReader->onRequest();

        $metadata = $this->getContainer()->get('al_taxonomy.metadata');

        $metadatas = $metadata->getAllEntityMetadata();

        $lines = array();

        foreach ($metadatas as $entity) {
            $lines[] = sprintf('<info>%s</info>', $entity->getReflectionClass()->getName());
            $lines[] = sprintf('Type: <comment>%s</comment>', $entity->getType() == $entity->getReflectionClass()->getName() ? '(The entity\'s FQCN)' : $entity->getType());
            foreach ($entity->getVocabularies() as $vocabulary) {
                $lines[] = sprintf('    <info>$%s</info>:', $vocabulary->getFieldName());
                $lines[] = sprintf('        vocabulary: <comment>%s</comment>', $vocabulary->getName());
                $lines[] = sprintf('        singular: <comment>%s</comment>', $vocabulary->isSingular() ? 'true' : 'false');

            }
            $lines[] = '';
        }

        $output->writeln($lines);
    }
}