<?php
namespace JournalTransporterPlugin\Api\Journals\Articles\Files;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\File;
use JournalTransporterPlugin\Repository\Journal;

class Revisions extends ApiRoute
{
    protected Journal $journalRepository;
    protected Article $articleRepository;
    protected File $fileRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments): array
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $file = $this->fileRepository->fetchById($parameters['file']);
        $revisions = $this->fileRepository->fetchRevisionsByFile($file);
        return array_map(
            function ($item) {
                return NestedMapper::map($item);
            },
            $revisions
        );
    }

}