<?php
namespace JournalTransporterPlugin\Api\Journals\Articles\Rounds;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\Journal;
use JournalTransporterPlugin\Repository\ReviewAssignment;
use JournalTransporterPlugin\Repository\SectionEditorSubmission;
use JournalTransporterPlugin\Utility\DataObject;

class ReviewAssignments extends ApiRoute
{
    protected Journal $journalRepository;
    protected Article $articleRepository;
    protected ReviewAssignment $reviewAssignmentRepository;
    protected SectionEditorSubmission $sectionEditorSubmissionRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute(array $parameters, array $arguments): array
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $reviewAssignments = $this->reviewAssignmentRepository->fetchByArticle($article, (int)$parameters['round']);
        $reviewAssignmentId = (int)$parameters['review_assignment'];

        if ($reviewAssignmentId > 0) {
            // There doesn't seem to be a way to get review assignments by id, so we do it this way so that we're
            // sure that we're showing a review assignment associated with the article.
            foreach ($reviewAssignments as $reviewAssignment) {
                if ((int)$reviewAssignmentId !== (int)$reviewAssignment->getId()) {
                    continue;
                }
                if ($arguments[ApiRoute::DEBUG_ARGUMENT]) {
                    return DataObject::dataObjectToArray($reviewAssignment);
                }
                return NestedMapper::map($reviewAssignment);
            }
        } else {
            if ($arguments[ApiRoute::DEBUG_ARGUMENT]) {
                return array_map(
                    function ($item) {
                        return DataObject::dataObjectToArray($item);
                    },
                    array_values($reviewAssignments)
                );
            } else {
                return array_map(
                    function ($item) {
                        return NestedMapper::map($item, 'sourceRecordKey');
                    },
                    array_values($reviewAssignments)
                );
            }
        }
    }
}