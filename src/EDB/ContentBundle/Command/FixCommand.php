<?php

namespace EDB\ContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FixCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('edb:fix')
            ->setDescription('Fix something')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $articles_s = $container->get('edbcontentbundle.articles');
        $attachments_s = $container->get('edbcontentbundle.attachments');
        $db = $container->get('doctrine.orm.entity_manager');
        
        $articles = $articles_s->getAll();
        foreach ($articles as $article) {
            //$output->writeln('<info>Article: '.$article->getTitle().'</info>');
            $as = $article->getAttachments();
            $asids = array();
            foreach ($as as $a) {
                if (!is_object($a))
                    continue;
                $asids[] = $a->getId();
            }
            if (count($asids) < 1)
                continue;
                
            $output->writeln('<comment>'.implode($asids, ', ').'</comment>');
            
            $article->setAttachments($asids);
        }
        $db->flush();
    }
}
