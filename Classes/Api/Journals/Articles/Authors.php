<?php
namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\File;
use JournalTransporterPlugin\Repository\Journal;

class Authors extends ApiRoute
{
    protected Journal $journalRepository;
    protected Article $articleRepository;
    protected File $fileRepository;

    /**
     * @param array $parameters
     * @param array $arguments
     * @return array
     * @throws \Exception
     */
    public function execute(array $parameters, array $arguments): array
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);

        return array_map(
            function ($item) {
                return NestedMapper::map($item);
            },
            $article->getAuthors()
        );
    }
}