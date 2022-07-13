<?php
namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\EditAssignment;
use JournalTransporterPlugin\Repository\Journal;
use JournalTransporterPlugin\Repository\SectionEditorSubmission;
use JournalTransporterPlugin\Utility\DataObject;
use JournalTransporterPlugin\Utility\Date;
use JournalTransporterPlugin\Utility\SourceRecordKey;
use JournalTransporterPlugin\Exception\UnknownDatabaseAccessObjectException;

class Rounds extends ApiRoute
{
    protected Journal $journalRepository;
    protected Article $articleRepository;
    protected SectionEditorSubmission $sectionEditorSubmissionRepository;
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
        $numberOfRounds = $this->sectionEditorSubmissionRepository->fetchNumberOfRoundsByArticle($article);
        $round = (int)$parameters['round'];

        if ($round) {
            if ($round < 1 || $round > $numberOfRounds) {
                throw new UnknownDatabaseAccessObjectException("Round $round doesn't exist");
            }
            return $this->getRound($article, $round);
        } else {
            return $this->getRounds($article, $numberOfRounds);
        }
    }

    /**
     * @param \Article $article
     * @param int $numberOfRounds
     * @return array
     */
    protected function getRounds(Article $article, int $numberOfRounds): array
    {
        $out = [];
        for ($i = 1; $i <= $numberOfRounds; $i++) {
            $out[] = (object)['source_record_key' => SourceRecordKey::round($article->getId(), $i)];
        }
        return $out;
    }

    /**
     * @param \Article$article
     * @param int $round
     *
     * @psalm-return object{source_record_key:string, round:mixed, date:string}
     * @psalm-param int<1, max> $round
     */
    protected function getRound(\Article $article, int $round): object
    {
        // These are ordered ASC by date
        $assignments = $this->editAssignmentRepository->fetchByArticle($article)->toArray();
        $dateUnderway = null;
        if (count($assignments)) {
            $dateUnderway = $assignments[0]->getDateUnderway();
        }

        return (object)[
            'source_record_key' => SourceRecordKey::round($article->getId(), $round),
            'round' => $round,
            'date' => Date::formatDateString($dateUnderway ?: $article->getLastModified())
        ];
    }
}