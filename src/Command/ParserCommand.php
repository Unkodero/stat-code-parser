<?php

namespace App\Command;

use App\FileHandler;
use App\Model\Country;
use App\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\ProgressBar;

class ParserCommand extends Command
{
    private $dirTimestamp;

    protected function configure()
    {
        $this->setName('parser');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dirTimestamp = time();

        // Getting available countries
        $output->writeln('Getting available countries...');
        $countries = App::GetAvailableCountries()->store;
        $tableRows = [];

        foreach ($countries as $index => $country) {
            /** @var $country Country */
            $tableRows[] = [
                $index,
                $country->name
            ];
        }

        // Build table and ask for needle countries
        $table = new Table($output);
        $table->setHeaders(['#', 'Name'])
            ->setRows($tableRows)
            ->render();

        $output->writeln([
            'Please enter id`s of needle countries (comma-separated)',
            'or just press Enter to parse all available countries'
        ]);

        // Ask
        $helper = $this->getHelper('question');
        $question = new Question('Countries: ');
        $countriesToParse = $helper->ask($input, $output, $question);

        // Get selected countries
        if ($countriesToParse) {
            $countriesToParse = explode(',', $countriesToParse);
        } else {
            // If no selected
            $countriesToParse = array_keys($countries);
        }

        // Countries to parse count
        $parseCount = count($countriesToParse);

        // Handle all selected countries
        for ($currentCountryIndex = 0; $currentCountryIndex < $parseCount; $currentCountryIndex++) {
            $country = App::getCountryPages($countries[$countriesToParse[$currentCountryIndex]]);
            $pages = $country->getPagesStore()->store;

            $db = new FileHandler($country, $this->dirTimestamp);

            // Start progress bar
            $pageProgress = new ProgressBar($output, count($pages));
            $pageProgress->setFormat(' <info>%current%/%max%</info> [%bar%] <info>%percent:3s%%</info> %elapsed:6s%/%estimated:-6s% <=[%country% (%current_country%\%total%)]');
            $pageProgress->setMessage($country->name, 'country');
            $pageProgress->setMessage($currentCountryIndex + 1, 'current_country');
            $pageProgress->setMessage($parseCount, 'total');

            // Each page
            foreach ($pages as $page) {
                $db->csv(App::parsePage($page));
                $pageProgress->advance();
            }

            $pageProgress->finish();

            $db->save(); // Save country data
        }
    }
}
