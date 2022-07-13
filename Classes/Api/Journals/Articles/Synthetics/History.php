<?php
namespace JournalTransporterPlugin\Api\Journals\Articles\Synthetics;

use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\Journal;

class History extends ApiRoute
{
    protected Journal $journalRepository;
    protected Article $articleRepository;

    /**
     * @param array $args
     * @return array
     * @throws \Exception
     */
    public function execute(array $args): array
    {
        $journal = $this->journalRepository->fetchOneById($args['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($args['article'], $journal);

        $history = new \JournalTransporterPlugin\Builder\History($article);
        return $history->toArray();
    }
}