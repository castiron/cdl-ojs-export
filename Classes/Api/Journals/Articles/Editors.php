<?php
namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\EditAssignment;
use JournalTransporterPlugin\Repository\Journal;

class Editors extends ApiRoute
{
    protected Journal $journalRepository;
    protected Article $articleRepository;
    protected EditAssignment $editAssignmentRepository;

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
        $editors = $this->editAssignmentRepository->fetchByArticle($article);

        return array_map(
            function ($item) {
                return NestedMapper::map($item);
            },
            $editors->toArray()
        );
    }
}