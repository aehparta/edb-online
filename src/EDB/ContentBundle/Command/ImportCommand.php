<?php

namespace EDB\ContentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('edb:import')
            ->setDescription('Import file(s) into eDB by creating an article and setting the file(s) as attachment(s)')
            ->addArgument(
                'categoryId',
                InputArgument::REQUIRED,
                'Article category ID'
            )
            ->addArgument(
                'title',
                InputArgument::REQUIRED,
                'Article title'
            )
            ->addArgument(
                'uploadroot',
                InputArgument::REQUIRED,
                'Uploads root directory'
            )
            ->addOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'File attachment, you can define multiple of these'
            )
            ->addOption(
                'description',
                null,
                InputOption::VALUE_REQUIRED,
                'Article description'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Don not confirm this action, just do it'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $categoryId = $input->getArgument('categoryId');
        $title = $input->getArgument('title');
        $uploads = $input->getArgument('uploadroot');
        $files = $input->getOption('file');
        $description = $input->getOption('description');
        if (!$description)
            $description = '';

        if (count($files) < 1) {
            throw new \RuntimeException('Atleast one file must be defined using --file=..');
        }

        $output->writeln('');
        $output->writeln('<info>Creating new article</info>');
        $output->writeln('<comment> Category: '.$categoryId.'</comment>');
        $output->writeln('<comment> Title: '.$title.'</comment>');
        $output->writeln('<comment> Description: '.$description.'</comment>');
        $output->writeln('<comment> Files: </comment>');
        foreach ($files as $file) {
            $output->writeln('<comment> - '.basename($file).'</comment>');
        }
        $output->writeln('');

        if (!$input->getOption('force')) {
            $dialog = $this->getHelperSet()->get('dialog');
            if (!$dialog->askConfirmation($output, '<question>Continue (yes/no)?</question> ', false))
                return;
        }
        $output->writeln('');

        $container = $this->getApplication()->getKernel()->getContainer();
        $articles_s = $container->get('edbcontentbundle.articles');
        $attachments_s = $container->get('edbcontentbundle.attachments');

        $article = $articles_s->put(false, $title);
        if (!$article)
            throw new \RuntimeException('Unable to create article.');
        $articles_s->setCategory($article, $categoryId);
        $articles_s->setResource($article, 'description', $description);
        $articles_s->setData($article, 'portrait', '');

        foreach ($files as $file) {
            $filename = basename($file);
            $attachment = $attachments_s->create($filename, $filename, $file, true, $uploads);
            $articles_s->addAttachment($article, $attachment);
        }

        $output->writeln('<info>Article created succesfully</info>');
    }
}
