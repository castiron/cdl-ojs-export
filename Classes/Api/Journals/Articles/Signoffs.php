<?php
namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\Journal;
use JournalTransporterPlugin\Repository\Signoff;
use JournalTransporterPlugin\Utility\DataObject;

class Signoffs extends ApiRoute
{
    protected Journal $journalRepository;
    protected Article $articleRepository;
    protected Signoff $signoffRepository;

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

        $signoffs = $this->signoffRepository->fetchByArticle($article)->toArray();

        if ($arguments[ApiRoute::DEBUG_ARGUMENT]) {
            return DataObject::dataObjectToArray($signoffs);
        }
        return array_map(
            function ($item) {
                return NestedMapper::map($item);
            },
            $signoffs
        );
    }

}