<?php
namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\File;
use JournalTransporterPlugin\Repository\GalleyFile;
use JournalTransporterPlugin\Repository\Journal;
use JournalTransporterPlugin\Repository\SupplementaryFile;
use JournalTransporterPlugin\Utility\DataObject;

class Files extends ApiRoute
{
    protected Journal $journalRepository;
    protected Article $articleRepository;
    protected File $fileRepository;
    protected GalleyFile $galleyFileRepository;
    protected SupplementaryFile $supplementaryFileRepository;

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

        if (!is_null($parameters['file'])) {
            return (new \JournalTransporterPlugin\Api\Files)
                ->execute(['article' => $article->getId(), 'file' => $parameters['file']], $arguments);
        }

        $files = $this->getAllFilesForArticle($article);

        if ($arguments[ApiRoute::DEBUG_ARGUMENT]) {
            return DataObject::dataObjectToArray($files);
        }
        return array_map(
            function ($item) {
                return NestedMapper::map($item);
            },
            $files
        );
    }

    /**
     * @param $type
     * @param $article \Article
     * @return mixed
     * @throws \Exception
     */
    protected function getAllFilesForArticle(\Article $article): mixed
    {
        return $this->fileRepository->fetchByArticle($article);
    }
}