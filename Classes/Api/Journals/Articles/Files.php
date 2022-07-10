<?php
namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObject;

class Files extends ApiRoute
{
    protected $journalRepository;
    protected $articleRepository;
    protected $fileRepository;
    protected $galleyFileRepository;
    protected $supplementaryFileRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
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
     * @param $article
     * @return mixed
     * @throws \Exception
     */
    protected function getAllFilesForArticle($article)
    {
        return $this->fileRepository->fetchByArticle($article);
    }
}